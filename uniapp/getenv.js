const path = require('path')
const fs = require('fs')

const resolvePath = (dir) => {
    return path.resolve(path.resolve(process.cwd(), 'node_modules'), dir)
}
const dotenv = require(resolvePath('dotenv'))
module.exports = {
    getEnvsByDot() {
        const prefixRE = /^VUE_APP_/
        let dotEnvs = {}
        //再加载专属环境变量
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
            const dotEnvsConfig = dotenv.config({ path: envPath })
            if (!dotEnvsConfig.error) {
                Object.keys(dotEnvsConfig.parsed).forEach((key) => {
                    if (prefixRE.test(key)) {
                        dotEnvs[key] = dotEnvsConfig.parsed[key]
                    }
                })
            }
            return dotEnvs
        } else {
            throw '请参考官方文档在.env文件下配置请求域名'
        }
    }
}
