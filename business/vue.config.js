const TransformPages = require('./js_sdk/hhyang-uni-read-pages/uni-read-pages@1.0.5')
const { webpack } = new TransformPages()
const { getEnvsByDot } = require('./getenv.js')
module.exports = {
    configureWebpack: {
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
    },
    chainWebpack: (config) => {
        config.plugin('define').tap((args) => {
            // console.log(args)
            const config = getEnvsByDot()
            // // 将自定义的环境变量塞入配置中
            // //这里有个格式转化时的字符串的坑
            // //'"http://127.0.0.1:8088/"' //这种没问题
            // //'http://127.0.0.1:8088/' //这种就报错了！
            Object.keys(config).forEach((key) => {
                if (typeof config[key] == 'string') {
                    config[key] = '"' + config[key] + '"'
                }
                args[0]['process.env'][key] = config[key]
            })
            //args[0]['process.env'] = Object.assign(args[0]['process.env'],config)
            return args
        })
    }
}
