/** S 是否H5端 **/
// #ifdef H5
const IS_H5 = true
// #endif

// #ifndef H5
const IS_H5_NOT_USED = false
// #endif
/** E 是否H5端 **/


/** S API BaseURL **/
/** S API BaseURL **/
// 止血方案：微信小程序端 process/require 不可用且 process.env 无法注入，导致 baseURL 为空。
// 这里直接使用生产域名常量，确保接口可用。
const baseURL = 'https://jiangjunshijia.com'

if (!baseURL) {
    console.error('[config/app.js] baseURL为空，请检查配置')
}

/** E API BaseURL **/

if (!baseURL) {
    console.error('[config/app.js] baseURL为空，请检查 .env.production 或 .env.development 文件中是否配置了 VUE_APP_BASE_API')
}

/** E API BaseURL **/

module.exports = {
    version: '3.6.5', // 版本号
    baseURL, // API Base URL
    basePath: '/mobile'
}
