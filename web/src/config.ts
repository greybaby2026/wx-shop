// 开发环境域名

const host_development = process.env.VUE_APP_BASE_API ? process.env.VUE_APP_BASE_API : ''
export default {
    // 版本
    version: '3.6.5',
    baseURL: host_development,
    tencentMapKey: 'FWEBZ-WHSHV-IRFPO-UNMRL-5EUWV-BFBFW'
}
