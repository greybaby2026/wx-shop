const fs = require('fs')
const path = require('path')

// 手动解析 .env 文件
function parseEnvFile(filePath) {
    const content = fs.readFileSync(filePath, 'utf-8')
    const result = {}
    content.split('\n').forEach(line => {
        line = line.trim()
        if (!line || line.startsWith('#')) return
        const match = line.match(/^([A-Za-z_][A-Za-z0-9_]*)\s*=\s*(.*)$/)
        if (match) {
            let value = match[2].trim()
            if ((value.startsWith("'") && value.endsWith("'")) ||
                (value.startsWith('"') && value.endsWith('"'))) {
                value = value.slice(1, -1)
            }
            result[match[1]] = value
        }
    })
    return result
}

// 获取环境变量
function getEnvsByDot() {
    const prefixRE = /^VUE_APP_/
    let dotEnvs = {}
    let env = 'local'
    if (process.env.NODE_ENV === 'development') {
        env = 'development'
    } else if (process.env.NODE_ENV === 'production') {
        env = 'production'
    } else {
        env = process.env.NODE_ENV
    }
    const envPath = path.resolve(__dirname, '.env.' + env)
    if (fs.existsSync(envPath)) {
        const parsed = parseEnvFile(envPath)
        Object.keys(parsed).forEach((key) => {
            if (prefixRE.test(key)) {
                dotEnvs[key] = parsed[key]
            }
        })
    }
    return dotEnvs
}

// 尝试加载 uni-read-pages（可能因缺少 node_modules 而失败）
let webpack = null
let TransformPages = null
try {
    TransformPages = require('./js_sdk/hhyang-uni-read-pages/uni-read-pages@1.0.5')
    const tp = new TransformPages()
    webpack = tp.webpack
} catch (e) {
    // HBuilderX 内置 webpack，尝试直接获取
    try {
        webpack = require(require('path').resolve(process.cwd(), 'node_modules/webpack'))
    } catch (e2) {
        console.warn('[vue.config.js] 无法加载 webpack，ROUTES 注入将跳过')
    }
}

const envConfig = getEnvsByDot()

const webpackConfig = {
    chainWebpack: (config) => {
        config.plugin('define').tap((args) => {
            Object.keys(envConfig).forEach((key) => {
                let val = envConfig[key]
                if (typeof val == 'string') {
                    val = '"' + val + '"'
                }
                // 仍然注入 process.env，供已有代码使用
                args[0]['process.env'][key] = val

                // 额外注入编译期常量，修复小程序端没有 process.env 的问题
                if (key === 'VUE_APP_BASE_API') {
                    args[0]['__VUE_APP_BASE_API__'] = val
                }
            })
            return args
        })
    }
}

// 如果 webpack 和 TransformPages 可用，添加 ROUTES 插件
if (webpack && TransformPages) {
    webpackConfig.configureWebpack = {
        plugins: [
            new webpack.DefinePlugin({
                ROUTES: webpack.DefinePlugin.runtimeValue(() => {
                    const tfPages = new TransformPages({
                        includes: ['path', 'name', 'aliasPath', 'animation', 'meta']
                    })
                    return JSON.stringify(tfPages.routes)
                }, true)
            })
        ]
    }
}

module.exports = webpackConfig
