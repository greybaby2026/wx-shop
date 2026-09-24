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
