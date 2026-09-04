const path = require('path')
const fs = require('fs')

/**
 * 手动解析 .env 文件，不依赖 dotenv 模块
 * .env 格式: KEY='VALUE' 或 KEY=VALUE
 */
function parseEnvFile(filePath) {
    const content = fs.readFileSync(filePath, 'utf-8')
    const result = {}
    content.split('\n').forEach(line => {
        line = line.trim()
        // 跳过空行和注释
        if (!line || line.startsWith('#')) return
        const match = line.match(/^([A-Za-z_][A-Za-z0-9_]*)\s*=\s*(.*)$/)
        if (match) {
            let value = match[2].trim()
            // 去除引号
            if ((value.startsWith("'") && value.endsWith("'")) ||
                (value.startsWith('"') && value.endsWith('"'))) {
                value = value.slice(1, -1)
            }
            result[match[1]] = value
        }
    })
    return result
}

module.exports = {
    getEnvsByDot() {
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
        const envPath = __dirname + '/.env.' + env
        if (fs.existsSync(envPath)) {
            const parsed = parseEnvFile(envPath)
            Object.keys(parsed).forEach((key) => {
                if (prefixRE.test(key)) {
                    dotEnvs[key] = parsed[key]
                }
            })
            return dotEnvs
        } else {
            throw '请参考官方文档在.env文件下配置请求域名，缺少文件: ' + envPath
        }
    }
}
