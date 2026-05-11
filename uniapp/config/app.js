/** S 是否H5端 **/
// #ifdef H5
const IS_H5 = true
// #endif

// #ifndef H5
const IS_H5 = false
// #endif
/** E 是否H5端 **/

/** S API BaseURL **/
const baseURLMap = {
    // 开发环境
    development: process.env.VUE_APP_BASE_API,
    // 生产环境
    production: IS_H5
        ? location.origin
        : process.env.VUE_APP_BASE_API
        ? process.env.VUE_APP_BASE_API
        : '[baseUrl]'
}

const baseURL = baseURLMap[process.env.NODE_ENV]

/** E API BaseURL **/

module.exports = {
    version: '3.6.5', // 版本号
    baseURL, // API Base URL
    basePath: '/mobile'
}
