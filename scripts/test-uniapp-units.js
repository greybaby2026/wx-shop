#!/usr/bin/env node
/**
 * uniapp 纯函数单元断言（FIX_PLAN 批次 3 交付物）
 *
 * 为什么单独做：uniapp 为 HBuilderX 工程、无法在服务器侧编译，
 * 但「纯函数类」缺陷（版本号比较、Promise 契约等）可以用「抽函数 + 打桩宿主依赖」
 * 的方式直接跑断言 —— 这是本环境能提供的最强验证手段。
 *
 * 用法：node scripts/test-uniapp-units.js   退出码 0 = 全部通过
 */
const fs = require('fs')
const path = require('path')

const REPO_ROOT = path.resolve(__dirname, '..')
const UNIAPP = path.join(REPO_ROOT, 'uniapp')

let pass = 0
const failed = []

function assert(name, actual, expected) {
    const ok = Object.is(actual, expected)
    if (ok) { pass++; console.log(`  ✅ ${name}  → ${actual}`) }
    else { failed.push(name); console.log(`  ❌ ${name}  期望 ${expected}，实际 ${actual}`) }
}

/** 从源文件中截取顶层函数定义（闭合花括号位于行首） */
function extractFunction(file, signature) {
    const src = fs.readFileSync(path.join(UNIAPP, file), 'utf8')
    const re = new RegExp('export function ' + signature + '\\s*\\([\\s\\S]*?\\n\\}')
    const m = re.exec(src)
    if (!m) throw new Error(`未找到函数：${signature}（${file}）`)
    return m[0].replace('export function', 'function')
}

// ============================================================
// compareWeChatVersion（FIX-U11 / P2-103）
// 宿主依赖 getBaseLibraryVersion() 在小程序端读 wx.getSystemInfoSync().SDKVersion，
// 此处替换为注入的 CURRENT，用于断言比较语义本身。
// ============================================================
console.log('\n[1] utils/tools.js · compareWeChatVersion')
let fnCode = extractFunction('utils/tools.js', 'compareWeChatVersion')
fnCode = fnCode.replace('const currentVersion = getBaseLibraryVersion()', 'const currentVersion = CURRENT')
const makeCompare = (current) =>
    new Function('CURRENT', fnCode + '\nreturn compareWeChatVersion')(current)

// 关键反例：两位数版本段（原实现字典序比较会判反）
assert('2.10.0 vs 2.6.0（原实现误判为 -1）', makeCompare('2.10.0')('2.6.0'), 1)
assert('2.9.0 vs 2.10.0（原实现误判为 1）', makeCompare('2.9.0')('2.10.0'), -1)
// 常规边界
assert('2.6.0 vs 2.6.0', makeCompare('2.6.0')('2.6.0'), 0)
assert('2.5.9 vs 2.6.0', makeCompare('2.5.9')('2.6.0'), -1)
assert('2.6 vs 2.6.0（长度不等，缺省段按 0）', makeCompare('2.6')('2.6.0'), 0)
assert('3.0.0 vs 2.6.0', makeCompare('3.0.0')('2.6.0'), 1)
assert('0.0.0 vs 2.6.0（非微信端 getBaseLibraryVersion 返回 0.0.0）', makeCompare('0.0.0')('2.6.0'), -1)
assert('2.10.0 vs 2.12.0', makeCompare('2.10.0')('2.12.0'), -1)
assert('2.12.1 vs 2.12.0', makeCompare('2.12.1')('2.12.0'), 1)

// ============================================================
// 静态回归：批次3 声明要建立的「契约」
// ============================================================
console.log('\n[2] 契约静态校验')

// 2.1 utils/ 与 mixins/ 下不得存在「无参数 reject()」（reject 无参 = 调用方拿不到原因）
const CONTRACT_DIRS = ['utils', 'mixins']
const bareReject = []
for (const dir of CONTRACT_DIRS) {
    const abs = path.join(UNIAPP, dir)
    if (!fs.existsSync(abs)) continue
    for (const name of fs.readdirSync(abs)) {
        if (!name.endsWith('.js')) continue
        const file = path.join(abs, name)
        fs.readFileSync(file, 'utf8').split('\n').forEach((line, i) => {
            // 排除注释行
            if (/^\s*(\/\/|\*|\/\*)/.test(line)) return
            if (/\breject\(\s*\)/.test(line)) bareReject.push(`${dir}/${name}:${i + 1}  ${line.trim()}`)
        })
    }
}
assert('utils/ 与 mixins/ 无「reject() 无参数」', bareReject.length, 0)
if (bareReject.length) bareReject.forEach(x => console.log('      ' + x))

// 2.2 request.js 响应拦截器必须对未知业务码兜底
const reqSrc = fs.readFileSync(path.join(UNIAPP, 'utils/request.js'), 'utf8')
assert('request.js 存在未知业务码兜底（events.fail 兜底分支）',
    /if\s*\(!handler\)[\s\S]{0,200}events\.fail\(response\.data\)/.test(reqSrc) ? 1 : 0, 1)
assert('request.js 请求拦截器 error 回调已 return Promise.reject',
    /\(error\)\s*=>\s*\{[\s\S]{0,300}?return\s+Promise\.reject\(error\)/.test(reqSrc) ? 1 : 0, 1)

// 2.3 closeShop 必须返回 Promise
assert('request.js closeShop 返回 Promise.reject',
    /closeShop\s*\(\s*\{\s*msg\s*\}\s*\)\s*\{[\s\S]*?return\s+Promise\.reject\(msg\)/.test(reqSrc) ? 1 : 0, 1)

// 2.4 「Promise 必须 settle」——U16 各处的定点断言
const read = (f) => fs.readFileSync(path.join(UNIAPP, f), 'utf8')

/**
 * 剥除注释（// 行注释、块注释、<!-- --> 模板注释），等长空格替换以保持行号。
 * 断言「某写法已不存在」时必须先剥注释 —— 否则修复说明的注释里引用了「原实现」，
 * 会被误判为未修复（这是本脚本踩过的坑）。
 */
function stripCodeComments(src) {
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

const loginSrc = read('utils/login.js')
const toolsSrc = read('utils/tools.js')
const wx5Src = read('utils/wechath5.js')
const orderSrc = read('mixins/order.js')
const orderCode = stripCodeComments(orderSrc)
const integralSrc = read('mixins/integral_order.js')

assert('login.js getUserProfile 失败分支已 reject', /fail\s*\(res\)\s*\{\s*reject\(res\)/.test(loginSrc) ? 1 : 0, 1)
assert('tools.js getRect 元素不存在时 settle（resolve(null)）',
    /boundingClientRect\(function\s*\(rect\)\s*\{[\s\S]{0,1500}?resolve\(null\)/.test(toolsSrc) ? 1 : 0, 1)
assert('wechath5.js getWxAddress 具备 cancel + fail 分支',
    /openAddress\(\{[\s\S]{0,260}?cancel:[\s\S]{0,160}?fail:/.test(wx5Src) ? 1 : 0, 1)
assert('wechath5.js reviceTransfer 版本过低分支已 reject（且不再用 alert）',
    (/uni\.showToast\(\{[\s\S]{0,400}?reject\('微信版本过低'\)/.test(wx5Src) && !/\balert\(/.test(wx5Src)) ? 1 : 0, 1)
assert('wechath5.js checkJsApi 已补 fail 分支',
    /checkJsApi\(\{[\s\S]{0,200}?fail:\s*\(res\)\s*=>\s*reject/.test(wx5Src) ? 1 : 0, 1)
assert('mixins/order.js comfirmReceive 非微信端 settle',
    /comfirmReceive\(transaction_id\)\s*\{[\s\S]*?#ifndef MP-WEIXIN[\s\S]{0,200}?resolve\(/.test(orderSrc) ? 1 : 0, 1)
assert('mixins/integral_order.js comfirmReceive 非微信端 settle',
    /comfirmReceive\(transaction_id\)\s*\{[\s\S]*?#ifndef MP-WEIXIN[\s\S]{0,200}?resolve\(/.test(integralSrc) ? 1 : 0, 1)
assert('integral_order.js 积分订单确认收货使用积分接口（P2-95）',
    /apiConfirmIntegralOrder\(\{\s*\n?\s*id: orderID\s*\n?\s*\}\)[\s\S]{0,80}/.test(integralSrc)
        && !/import\s*\{\s*apiOrderConfirm\s*\}/.test(integralSrc) ? 1 : 0, 1)

// 2.5 U14 登出清理全局状态 + 缓存键集中
const appStoreSrc = read('store/modules/app.js')
assert('app.js 存在 logout action 且复位购物车角标',
    /logout\(\{\s*commit\s*\}\)\s*\{[\s\S]{0,500}?commit\('setCartNum',\s*0/.test(appStoreSrc) ? 1 : 0, 1)
assert('app.js logout action 复位未读消息',
    /logout\(\{\s*commit\s*\}\)\s*\{[\s\S]{0,700}?commit\('SET_UNREAD_COUNT',\s*0/.test(appStoreSrc) ? 1 : 0, 1)
assert('cache.js 提供 removeByPrefix / clear',
    (/removeByPrefix\s*\(/.test(read('utils/cache.js')) && /clear\s*\(\s*\)/.test(read('utils/cache.js'))) ? 1 : 0, 1)
assert('cachekey.js 已集中 OPENIMAGE* 与 ACTIVITY_DEDUCT_INFO',
    (['OPENIMAGE_ENABLE', 'OPENIMAGE:', 'OPENIMAGE_NUMBER', 'ACTIVITY_DEDUCT_INFO'].every(k => read('config/cachekey.js').includes(k))) ? 1 : 0, 1)
assert('activity_deduct.js 从缓存恢复（补上读取侧，消除白写）',
    /deductInfo:\s*Cache\.get\(ACTIVITY_DEDUCT_INFO\)/.test(read('store/modules/activity_deduct.js')) ? 1 : 0, 1)

// 全库扫描：logout 是否仍被当作 mutation 直调（除 app.js 内部自调）；OPENIMAGE 字面量是否清零
const logoutBypass = []
const openImageLiteral = []
;(function walkAll(dir) {
    for (const name of fs.readdirSync(dir)) {
        const full = path.join(dir, name)
        const st = fs.statSync(full)
        if (st.isDirectory()) {
            if (!['node_modules', 'unpackage', 'dist', '.git', 'js_sdk', 'plugin', 'uview-ui', 'uni_modules'].includes(name)) walkAll(full)
        } else if (/\.(vue|js)$/.test(name)) {
            const relToUniapp = path.relative(UNIAPP, full).replace(/\\/g, '/')
            const src = fs.readFileSync(full, 'utf8')
            src.split('\n').forEach((line, i) => {
                if (/^\s*(\/\/|\*|\/\*)/.test(line)) return
                if (/(?:store\.)?commit\(\s*['"]logout['"]\s*\)|mapMutations\(\s*\[\s*['"]logout['"]/.test(line)
                    && relToUniapp !== 'store/modules/app.js') {
                    logoutBypass.push(`${relToUniapp}:${i + 1}`)
                }
                if (/'OPENIMAGE/.test(line) && relToUniapp !== 'config/cachekey.js') {
                    openImageLiteral.push(`${relToUniapp}:${i + 1}  ${line.trim()}`)
                }
            })
        }
    }
})(UNIAPP)
assert('无残留「logout 当 mutation 直调」（除 app.js 内部）', logoutBypass.length, 0)
if (logoutBypass.length) logoutBypass.forEach(x => console.log('      ' + x))
assert('OPENIMAGE* 字面量已清零（除 cachekey 定义）', openImageLiteral.length, 0)
if (openImageLiteral.length) openImageLiteral.forEach(x => console.log('      ' + x))

// 2.6 U12 socket 健壮性
const socketSrc = read('utils/socket.js')
assert('socket.js onMessage 对 JSON.parse 做了保护',
    /onMessage\(\{data\}\)\s*\{[\s\S]{0,300}?try\s*\{[\s\S]{0,120}?JSON\.parse\(data\)/.test(socketSrc) ? 1 : 0, 1)
assert('socket.js onMessage 解析失败分支仍重置心跳',
    /catch\s*\(e\)\s*\{[\s\S]{0,200}?this\.reset\(\)[\s\S]{0,80}?return/.test(socketSrc) ? 1 : 0, 1)
assert('socket.js 提供 offEvent（订阅移除）', /offEvent\s*\(type\)\s*\{/.test(socketSrc) ? 1 : 0, 1)
assert('socket.js 重连超限有用户可见提示',
    /reconnectNums\s*>=\s*5[\s\S]{0,200}?uni\.showToast/.test(socketSrc) ? 1 : 0, 1)
assert('socket.js serverTimeout 关闭前判空',
    /serverTimeout\s*=\s*setTimeout\([\s\S]{0,200}?this\.socketTask\s*&&\s*this\.socketTask\.close/.test(socketSrc) ? 1 : 0, 1)

// 2.7 U15 不再手动重跑页面生命周期（改为事件通知）
// 注意：必须剥除注释后再匹配 —— 修复说明的注释里会引用「原实现」的写法，
//       不剥注释会把这些说明误判为「未修复」
assert('mixins/order.js 删除后改用事件通知（不再 prevPage.onLoad）',
    (/uni\.\$emit\('orderListRefresh'\)/.test(orderCode) && !/prevPage\.onLoad\(/.test(orderCode)) ? 1 : 0, 1)
const loginUtilSrc = read('utils/login.js')
const loginUtilCode = stripCodeComments(loginUtilSrc)
assert('utils/login.js 静默登录后改用事件通知（不再重跑 onLoad/onShow）',
    (/uni\.\$emit\('loginSuccess'\)/.test(loginUtilCode)
        && !/onLoad\s*&&\s*onLoad\(/.test(loginUtilCode)
        && !/onShow\s*&&\s*onShow\(/.test(loginUtilCode)) ? 1 : 0, 1)
const appMixinSrc = read('mixins/app.js')
assert('mixins/app.js 全局 mixin 监听 orderListRefresh 与 loginSuccess',
    (/uni\.\$on\('orderListRefresh'/.test(appMixinSrc) && /uni\.\$on\('loginSuccess'/.test(appMixinSrc)) ? 1 : 0, 1)
assert('mixins/app.js onUnload 注销两个监听（避免泄漏）',
    (/uni\.\$off\('orderListRefresh'/.test(appMixinSrc) && /uni\.\$off\('loginSuccess'/.test(appMixinSrc)) ? 1 : 0, 1)

// 全库扫描（剥注释后）：不得再有「无参手动重跑页面生命周期」的写法
//   —— 无参重跑会让被重跑页面拿到 undefined 的 options（参数契约被破坏），且高频场景会重复初始化。
// 有意保留（不在本批改动范围，原因已写在代码注释里，此处显式列出而非隐藏）：
//   login.vue / bind_mobile.vue 在「登录成功 / 绑定成功」后重跑来源页 onLoad ——
//   取的是来源页自身的 options（参数契约未破），且仅一次、低频；改成纯事件通知会让
//   来源页（结算/购物车等依赖登录态取数的页面）不再刷新，故保留。
const MANUAL_LIFECYCLE_ALLOW = ['pages/login/login.vue', 'pages/bind_mobile/bind_mobile.vue']
const manualLifecycle = []
const manualLifecycleAllowed = []
;(function walkLifecycle(dir) {
    for (const name of fs.readdirSync(dir)) {
        const full = path.join(dir, name)
        const st = fs.statSync(full)
        if (st.isDirectory()) {
            if (!['node_modules', 'unpackage', 'dist', '.git', 'js_sdk', 'plugin', 'uview-ui', 'uni_modules'].includes(name)) walkLifecycle(full)
        } else if (/\.(vue|js)$/.test(name)) {
            const relToUniapp = path.relative(UNIAPP, full).replace(/\\/g, '/')
            stripCodeComments(fs.readFileSync(full, 'utf8')).split('\n').forEach((line, i) => {
                // 只判「无参」重跑：onLoad() / onShow() / prevPage.onLoad() / page.onLoad()
                if (/\bprevPage\.onLoad\(\)|\bpage\.onLoad\(\)|onLoad\s*&&\s*onLoad\(\s*\)|onShow\s*&&\s*onShow\(\s*\)/.test(line)) {
                    const hit = `${relToUniapp}:${i + 1}  ${line.trim()}`
                    ;(MANUAL_LIFECYCLE_ALLOW.includes(relToUniapp) ? manualLifecycleAllowed : manualLifecycle).push(hit)
                }
            })
        }
    }
})(UNIAPP)
assert('全库无「无参手动重跑页面生命周期」写法', manualLifecycle.length, 0)
if (manualLifecycle.length) manualLifecycle.forEach(x => console.log('      ' + x))
if (manualLifecycleAllowed.length) {
    console.log(`      （有意保留 ${manualLifecycleAllowed.length} 处，已登记）：`)
    manualLifecycleAllowed.forEach(x => console.log('        · ' + x))
}

// 2.8 全量枚举 new Promise（供人工复核：每个分支是否都 settle）——信息性输出
console.log('\n[3] utils/ 与 mixins/ 中 new Promise 清单（供人工复核 settle 分支）')
const promiseList = []
for (const dir of ['utils', 'mixins']) {
    const abs = path.join(UNIAPP, dir)
    if (!fs.existsSync(abs)) continue
    for (const name of fs.readdirSync(abs)) {
        if (!name.endsWith('.js')) continue
        const rel = `${dir}/${name}`
        read(rel).split('\n').forEach((line, i) => {
            if (/new\s+Promise\s*\(/.test(line) && !/^\s*(\/\/|\*)/.test(line)) {
                promiseList.push(`${rel}:${i + 1}`)
            }
        })
    }
}
console.log('     共 ' + promiseList.length + ' 处：' + promiseList.join('  '))

// ============================================================
console.log(`\n${'─'.repeat(60)}`)
if (failed.length === 0) {
    console.log(`✅ 全部通过：${pass} 项断言`)
    process.exit(0)
} else {
    console.log(`❌ 失败 ${failed.length} 项：`)
    failed.forEach(f => console.log('   - ' + f))
    process.exit(1)
}
