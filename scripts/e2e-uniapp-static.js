#!/usr/bin/env node
/**
 * uniapp 静态端到端校验（FIX_PLAN 批次 2 交付物）
 *
 * 背景：uniapp 为 HBuilderX 工程（无 package.json / node_modules），无法在服务器侧做编译级回归，
 *       而批次 2 修复的问题（路由不可达、生命周期误放、TDZ、loading 不复位）恰好都能用「全库静态校验」
 *       提前拦下 —— 这些也正是 FIX_PLAN 各 Unit 里要求的「全库扫描脚本」。
 *
 * 用法：node scripts/e2e-uniapp-static.js [uniapp目录]   默认 ./uniapp（相对仓库根）
 * 退出码：0 = 全部通过；1 = 存在失败项（可接 CI）
 *
 * 覆盖 9 项检查：
 *   1 路由可达性        导航目标必须在 pages.json 中注册
 *   2 相对路径          小程序跳转/转发路径必须以 / 开头
 *   3 页面注册一致性    pages.json 注册的页面文件必须存在（双向核对）
 *   4 methods 误放      生命周期 / 分享钩子 / mapGetters / mapState 不得写在 methods 内
 *   5 根层级 map*       mapGetters/mapState/mapActions/mapMutations 不得展开在组件选项根层级
 *   6 TDZ               console.log 引用的标识符不得在其后才声明
 *   7 onUnload 跳转     页面销毁钩子里不得做跳转（导航劫持）
 *   8 配置可解析性      pages.json / manifest.json / androidPrivacy.json / AASA
 *   9 第三方域名残留    likeshop / yixiangonline 等功能性引用
 */
const fs = require('fs')
const path = require('path')
const os = require('os')
const { execFileSync } = require('child_process')

const REPO_ROOT = path.resolve(__dirname, '..')
const UNIAPP = path.resolve(process.argv[2] || path.join(REPO_ROOT, 'uniapp'))
const EXCLUDE_DIRS = new Set(['node_modules', 'unpackage', 'dist', '.git', 'uni_modules', 'uview-ui', 'js_sdk', 'plugin'])
const THIRD_PARTY = /(?:b2cplus\.likeshop\.cn|yixiangonline)/

let passCount = 0
const failures = []

function report(no, name, ok, detail) {
    if (ok) { passCount++; console.log(`  ✅ ${no} ${name}`) }
    else { failures.push(`${no} ${name}`); console.log(`  ❌ ${no} ${name}`) }
    if (detail) console.log(detail.replace(/^/gm, '     '))
}

function stripComments(src) {
    let out = '', state = 'code'
    for (let i = 0; i < src.length; i++) {
        const c = src[i], n = src[i + 1]
        if (state === 'code') {
            if (c === '/' && n === '/') { state = 'line'; out += '  '; i++; continue }
            if (c === '/' && n === '*') { state = 'block'; out += '  '; i++; continue }
            if (c === '<' && src.startsWith('<!--', i)) { state = 'html'; out += '    '; i += 3; continue }
            if (c === '"' || c === "'" || c === '`') { state = c; out += c; continue }
            out += c; continue
        }
        if (state === 'line') { if (c === '\n') { state = 'code'; out += '\n' } else out += ' '; continue }
        if (state === 'block') { if (c === '*' && n === '/') { state = 'code'; out += '  '; i++; continue } out += (c === '\n') ? '\n' : ' '; continue }
        if (state === 'html') { if (c === '-' && src.startsWith('-->', i)) { state = 'code'; out += '   '; i += 2; continue } out += (c === '\n') ? '\n' : ' '; continue }
        out += c
        if (c === '\\') { out += (n || ''); i++; continue }
        if (c === state) state = 'code'
    }
    return out
}

function parseJsonc(file) {
    let s = stripComments(fs.readFileSync(file, 'utf8'))
    // JSONC 里 /* */ 也被允许：stripComments 已按 JS 规则处理
    return JSON.parse(s)
}

const files = []
;(function walk(dir) {
    for (const name of fs.readdirSync(dir)) {
        const full = path.join(dir, name)
        const st = fs.statSync(full)
        if (st.isDirectory()) { if (!EXCLUDE_DIRS.has(name)) walk(full) }
        else if (/\.(vue|js)$/.test(name)) files.push(full)
    }
})(UNIAPP)

const rel = f => path.relative(UNIAPP, f)

// ---------- 1~2 路由 ----------
const pagesJsonPath = path.join(UNIAPP, 'pages.json')
const pagesJson = parseJsonc(pagesJsonPath)
const routes = new Map()
;(pagesJson.pages || []).forEach(p => routes.set('/' + p.path, p))
;(pagesJson.subPackages || []).forEach(sp => {
    ;(sp.pages || []).forEach(p => routes.set('/' + sp.root + '/' + p.path, p))
})

const NAV_PATTERNS = [
    { re: /path\s*:\s*['"`]([^'"`]+)['"`]/g, kind: 'path' },
    { re: /url\s*:\s*['"`]([^'"`]+)['"`]/g, kind: 'url' },
    { re: /\$Router\.(?:push|replace|replaceAll)\(\s*['"`]([^'"`]+)['"`]/g, kind: '$Router(str)' },
    { re: /<router-link[^>]*\bto\s*=\s*["']([^"']+)["']/g, kind: 'router-link' },
    { re: /\bgoPage\(\s*['"`]([^'"`]+)['"`]/g, kind: 'goPage' },
]
/**
 * 已知且已登记的「暂不处理」事项（**不是隐藏缺陷**：命中的条目会在下方显式列出）
 * 只有满足「已登记 + 有明确原因 + 有后续动作」才允许进白名单。
 */
const ROUTE_ALLOWLIST = [
    {
        match: 'bundle/pages/user_set/user_set.vue',
        target: '/components/uview-ui/components/u-avatar-cropper/u-avatar-cropper',
        reason: 'uView 头像裁剪页未在 pages.json 注册为页面；仅出现在 #ifndef MP-WEIXIN 分支（App/H5），'
            + '小程序端走原生 chooseAvatar 不受影响。修复需在 pages.json 注册该页面（涉及主包体积取舍），'
            + '属批次 2 范围外，已登记为待决策项（见 FIX_PLAN.md 批次 2 执行记录）。',
    },
]
const unreachable = []
const allowlisted = []
const relative = []
for (const file of files) {
    const src = stripComments(fs.readFileSync(file, 'utf8'))
    for (const { re, kind } of NAV_PATTERNS) {
        re.lastIndex = 0
        let m
        while ((m = re.exec(src))) {
            const raw = m[1]
            if (!raw || raw.startsWith('http')) continue
            const lineNo = src.slice(0, m.index).split('\n').length
            const pure = raw.split('?')[0]
            if (!pure.startsWith('/')) {
                if (/^(bundle\/)?pages\//.test(pure)) relative.push(`${rel(file)}:${lineNo} [${kind}] ${raw}`)
                continue
            }
            const hitText = () => `${rel(file)}:${lineNo} [${kind}] ${raw}`
            if (pure.includes('${')) {
                const prefix = pure.split('${')[0]
                if (prefix.length > 1 && ![...routes.keys()].some(r => r.startsWith(prefix))) {
                    const allow = ROUTE_ALLOWLIST.find(a => rel(file).endsWith(a.match))
                    ;(allow ? allowlisted : unreachable).push(hitText())
                }
                continue
            }
            if (!routes.has(pure)) {
                const allow = ROUTE_ALLOWLIST.find(a => rel(file).endsWith(a.match) && a.target === pure)
                ;(allow ? allowlisted : unreachable).push(hitText())
            }
        }
    }
}
report(1, `路由可达性（${files.length} 个文件 / ${routes.size} 条路由）`, unreachable.length === 0,
    unreachable.length ? unreachable.join('\n') : (allowlisted.length ? `已豁免 ${allowlisted.length} 处（见下）：\n` + allowlisted.map(x => x + '\n  ↑ ' + ROUTE_ALLOWLIST[0].reason).join('\n') : ''))
report(2, '相对路径（跳转/转发必须以 / 开头）', relative.length === 0, relative.join('\n'))

// ---------- 3 页面注册一致性 ----------
const missingPage = []
for (const [route] of routes) {
    const vue = path.join(UNIAPP, route.slice(1) + '.vue')
    if (!fs.existsSync(vue)) missingPage.push(`注册了但文件不存在: ${route}`)
}
const unregistered = []
;(function walkPages(dir, root) {
    if (!fs.existsSync(dir)) return
    for (const name of fs.readdirSync(dir)) {
        const full = path.join(dir, name)
        const st = fs.statSync(full)
        if (st.isDirectory()) walkPages(full, root)
        else if (name.endsWith('.vue')) {
            const route = '/' + path.relative(UNIAPP, full).replace(/\\/g, '/').replace(/\.vue$/, '')
            if (!routes.has(route)) unregistered.push(`文件存在但未注册: ${route}`)
        }
    }
})(path.join(UNIAPP, 'bundle/pages'), 'bundle')
;(function walkMain(dir) {
    for (const name of fs.readdirSync(dir)) {
        const full = path.join(dir, name)
        const st = fs.statSync(full)
        if (st.isDirectory()) { if (!EXCLUDE_DIRS.has(name)) walkMain(full) }
        else if (name.endsWith('.vue')) {
            const route = '/' + path.relative(UNIAPP, full).replace(/\\/g, '/').replace(/\.vue$/, '')
            if (!routes.has(route)) unregistered.push(`文件存在但未注册: ${route}`)
        }
    }
})(path.join(UNIAPP, 'pages'))
report(3, `页面注册一致性（${routes.size} 条注册）`, missingPage.length === 0 && unregistered.length === 0,
    [...missingPage, ...unregistered].join('\n'))

// ---------- 4~5 methods 误放 / 根层级 map* ----------
const DANGEROUS = {
    onLoad: '生命周期', onShow: '生命周期', onHide: '生命周期', onUnload: '生命周期',
    onReady: '生命周期', onPullDownRefresh: '生命周期', onReachBottom: '生命周期',
    onPageScroll: '生命周期', onTabItemTap: '生命周期', onResize: '生命周期', onBackPress: '生命周期',
    onShareAppMessage: '分享钩子', onShareTimeline: '分享钩子', onAddToFavorites: '分享钩子',
    mapGetters: 'Vue映射', mapState: 'Vue映射',
}
function scanMethodsBody(src, openIdx) {
    const keys = []
    let depth = 0, expectKey = true, state = null
    for (let i = openIdx; i < src.length; i++) {
        const c = src[i]
        if (state) { if (c === '\\') { i++; continue } if (c === state) state = null; continue }
        if (c === '"' || c === "'" || c === '`') { state = c; continue }
        if (c === '{') { depth++; if (depth === 1) expectKey = true; continue }
        if (c === '}') { depth--; if (depth === 0) return keys; continue }
        if (c === ',' && depth === 1) { expectKey = true; continue }
        if (depth === 1 && expectKey && /[A-Za-z_$]/.test(c)) {
            let j = i
            while (j < src.length && /[A-Za-z0-9_$]/.test(src[j])) j++
            const word = src.slice(i, j)
            let k = j
            while (k < src.length && /\s/.test(src[k])) k++
            if (src[k] === ':' || src[k] === '(') { keys.push({ word, pos: i }); expectKey = false; i = j - 1; continue }
            i = j - 1
        }
    }
    return keys
}
const methodHits = []
const rootHits = []
for (const file of files) {
    const src = stripComments(fs.readFileSync(file, 'utf8'))
    const re = /\bmethods\s*:\s*\{/g
    let m
    while ((m = re.exec(src))) {
        const openIdx = m.index + m[0].length - 1
        for (const k of scanMethodsBody(src, openIdx)) {
            if (DANGEROUS[k.word]) methodHits.push(`${rel(file)}:${src.slice(0, k.pos).split('\n').length} [${DANGEROUS[k.word]}] ${k.word}`)
        }
        re.lastIndex = openIdx
    }
    const open = src.indexOf('export default {')
    if (open === -1) continue
    const openIdx = src.indexOf('{', open)
    let depth = 0, state = null
    for (let i = openIdx; i < src.length; i++) {
        const c = src[i]
        if (state) { if (c === '\\') { i++; continue } if (c === state) state = null; continue }
        if (c === '"' || c === "'" || c === '`') { state = c; continue }
        if (c === '{') { depth++; continue }
        if (c === '}') { depth--; if (depth === 0) break; continue }
        if (depth === 1 && src.startsWith('...', i)) {
            const m2 = /^\.\.\.(mapGetters|mapState|mapActions|mapMutations)\s*\(/.exec(src.slice(i))
            if (m2) rootHits.push(`${rel(file)}:${src.slice(0, i).split('\n').length} ...${m2[1]}(...)`)
        }
    }
}
report(4, 'methods 内误放生命周期/分享钩子/Vue 映射', methodHits.length === 0, methodHits.join('\n'))
report(5, '组件选项根层级的 map* 展开', rootHits.length === 0, rootHits.join('\n'))

// ---------- 6 TDZ ----------
const RESERVED = new Set(['this', 'true', 'false', 'null', 'undefined', 'console', 'uni', 'wx', 'window', 'document', 'Math', 'JSON', 'String', 'Number', 'Boolean', 'Array', 'Object', 'Date', 'Promise', 'parseInt', 'parseFloat', 'isNaN', 'setTimeout', 'setInterval', 'clearTimeout', 'clearInterval', 'getCurrentPages', 'require', 'module', 'exports', 'process'])
const tdz = []
for (const file of files) {
    const src = stripComments(fs.readFileSync(file, 'utf8'))
    const re = /console\.log\(([^)]*)\)/g
    let m
    while ((m = re.exec(src))) {
        const ids = [...m[1].matchAll(/[A-Za-z_$][A-Za-z0-9_$]*/g)].map(x => x[0])
        const after = src.slice(m.index, m.index + 1200)
        for (const id of new Set(ids)) {
            if (RESERVED.has(id)) continue
            const decl = new RegExp('(?:const|let|var)\\s+' + id + '\\b').exec(after)
            if (decl && decl.index > m[0].length) {
                const between = after.slice(m[0].length, decl.index)
                if (/\n\s*(?:async\s+)?[A-Za-z_$][A-Za-z0-9_$]*\s*\([^)]*\)\s*\{/.test(between)) continue
                tdz.push(`${rel(file)}:${src.slice(0, m.index).split('\n').length} ${id}`)
            }
        }
    }
}
report(6, 'TDZ（console.log 先用后声明）', tdz.length === 0, tdz.join('\n'))

// ---------- 7 onUnload 内跳转 ----------
const unloadJump = []
for (const file of files) {
    if (!file.endsWith('.vue')) continue
    const src = stripComments(fs.readFileSync(file, 'utf8'))
    const re = /\bonUnload\s*(?:\(\s*\)|:\s*function\s*\(\s*\))\s*\{/g
    let m
    while ((m = re.exec(src))) {
        const openIdx = m.index + m[0].length - 1
        let depth = 0
        let end = src.length
        for (let i = openIdx; i < src.length; i++) {
            if (src[i] === '{') depth++
            else if (src[i] === '}') { depth--; if (depth === 0) { end = i; break } }
        }
        const body = src.slice(openIdx, end)
        if (/\$Router\.(?:push|replace|replaceAll)|uni\.(?:navigateTo|redirectTo|switchTab|reLaunch)/.test(body)) {
            unloadJump.push(`${rel(file)}:${src.slice(0, m.index).split('\n').length}`)
        }
        re.lastIndex = end
    }
}
report(7, 'onUnload 内做跳转（导航劫持）', unloadJump.length === 0, unloadJump.join('\n'))

// ---------- 8 配置可解析 ----------
const cfgErrors = []
for (const f of ['pages.json', 'manifest.json', 'androidPrivacy.json', 'apple-app-site-association']) {
    const full = path.join(UNIAPP, f)
    if (!fs.existsSync(full)) { cfgErrors.push(`缺少文件: ${f}`); continue }
    try { parseJsonc(full) } catch (e) { cfgErrors.push(`${f} 解析失败: ${e.message}`) }
}
report(8, '配置文件可解析（pages/manifest/androidPrivacy/AASA）', cfgErrors.length === 0, cfgErrors.join('\n'))

// ---------- 9 第三方域名残留 ----------
const thirdParty = []
for (const file of files) {
    const src = fs.readFileSync(file, 'utf8')
    src.split('\n').forEach((line, idx) => {
        if (THIRD_PARTY.test(line)) thirdParty.push(`${rel(file)}:${idx + 1} ${line.trim().slice(0, 100)}`)
    })
}
for (const f of ['manifest.json', 'androidPrivacy.json', 'apple-app-site-association']) {
    const full = path.join(UNIAPP, f)
    if (!fs.existsSync(full)) continue
    fs.readFileSync(full, 'utf8').split('\n').forEach((line, idx) => {
        // 注释中的「历史说明」不算残留
        if (THIRD_PARTY.test(line) && !/^\s*(?:\/\*|\*|\/\/)/.test(line)) thirdParty.push(`${f}:${idx + 1} ${line.trim().slice(0, 100)}`)
    })
}
report(9, '第三方域名功能性残留（likeshop / yixiangonline）', thirdParty.length === 0, thirdParty.join('\n'))

// ---------- 10 pages.json 关键工程配置（U18）----------
const metaProblems = []
for (const [route, page] of routes) {
    if (!page.meta || typeof page.meta.auth !== 'boolean') {
        metaProblems.push(`未显式声明 meta.auth（路由守卫会因 undefined 而不拦截）: ${route}`)
    }
}
const cfgProblems = [...metaProblems]
if (pagesJson.lazyCodeLoading !== 'requiredComponents') {
    cfgProblems.push(`lazyCodeLoading 应为 "requiredComponents"，实际=${JSON.stringify(pagesJson.lazyCodeLoading)}（未开启按需注入，冷启动全量注入组件）`)
}
if (!pagesJson.preloadRule || !Object.keys(pagesJson.preloadRule).length) {
    cfgProblems.push('缺少 preloadRule（分包预下载），进入分包页需等待下载')
}
report(10, `pages.json 关键配置（meta.auth ${routes.size} 页 / 按需注入 / 分包预下载）`, cfgProblems.length === 0, cfgProblems.join('\n'))

// ---------- 11 安全基线（U20）：Android 危险权限 / 发布脚本防护 / 签名密钥 ----------
const secProblems = []

// 11.1 Android 高危险/无关权限不得再申请
const HIGH_RISK_PERMS = ['CALL_PHONE', 'GET_ACCOUNTS', 'MOUNT_UNMOUNT_FILESYSTEMS', 'READ_LOGS', 'READ_PHONE_STATE', 'WRITE_SETTINGS', 'CHANGE_NETWORK_STATE', 'CHANGE_WIFI_STATE']
const manifestRaw = fs.readFileSync(path.join(UNIAPP, 'manifest.json'), 'utf8')
for (const perm of HIGH_RISK_PERMS) {
    // 仅匹配权限声明行（注释里的「已移除 xxx」说明不算）
    const re = new RegExp('^\\s*"<uses-permission[^"]*' + perm + '[^"]*"')
    if (manifestRaw.split('\n').some(line => re.test(line.trim()))) {
        secProblems.push(`manifest.json 仍申请高风险权限: ${perm}`)
    }
}

// 11.2 发布脚本必须具备防护（set -euo pipefail / 源目录校验 / 路径前缀断言）
for (const s of ['autoRelease.sh', 'autoMiniprogram.sh']) {
    const p = path.join(UNIAPP, s)
    if (!fs.existsSync(p)) { secProblems.push(`缺少发布脚本: ${s}`); continue }
    const code = fs.readFileSync(p, 'utf8')
    if (!/set\s+-euo\s+pipefail/.test(code)) secProblems.push(`${s} 未启用 set -euo pipefail`)
    if (!/源目录/.test(code) || !/exit 1/.test(code)) secProblems.push(`${s} 未对源目录做校验并中止`)
    if (!/server\/public\//.test(code) || !/case\s+"\$releaseAbs"/.test(code)) secProblems.push(`${s} 缺少发布目录前缀断言`)
    // 逐行判定「先删除生产目录」的危险写法；跳过 echo / 注释行（回滚提示文案里会出现 rm -rf）
    const dangerousLine = code.split('\n').find(line => {
        const t = line.trim()
        if (t.startsWith('echo') || t.startsWith('#')) return false
        return /\brm\s+-rf?\s+"?\$releasePath\b/.test(t)
    })
    if (dangerousLine) secProblems.push(`${s} 仍存在「先删除生产目录」的危险写法: ${dangerousLine.trim()}`)
}

// 11.3 签名密钥：不得入库 + 权限收紧
const gitignore = fs.existsSync(path.join(REPO_ROOT, '.gitignore')) ? fs.readFileSync(path.join(REPO_ROOT, '.gitignore'), 'utf8') : ''
const uniGitignore = fs.existsSync(path.join(UNIAPP, '.gitignore')) ? fs.readFileSync(path.join(UNIAPP, '.gitignore'), 'utf8') : ''
if (!/\*\.keystore/.test(gitignore + uniGitignore)) secProblems.push('.gitignore 未忽略 *.keystore')
const keystore = path.join(UNIAPP, 'my-release-key.keystore')
if (fs.existsSync(keystore)) {
    const mode = fs.statSync(keystore).mode & 0o777
    if (mode & 0o077) secProblems.push(`签名密钥权限过宽（同机其他用户可读）: 当前 ${mode.toString(8)}，应为 600`)
}
report(11, '安全基线（Android 权限裁剪 / 发布脚本防护 / 签名密钥）', secProblems.length === 0, secProblems.join('\n'))

// ---------- 汇总 ----------
console.log(`\n${'─'.repeat(60)}`)
if (failures.length === 0) {
    console.log(`✅ 全部通过：${passCount}/11 项检查`)
    process.exit(0)
} else {
    console.log(`❌ 失败 ${failures.length} 项：`)
    failures.forEach(f => console.log('   - ' + f))
    process.exit(1)
}
