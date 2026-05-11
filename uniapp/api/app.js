import request from '@/utils/request'
import wechath5 from '@/utils/wechath5'
import store from 'store'

// 账号登录
export const apiAccountLogin = (data) =>
    request.post('login/account', { ...data, terminal: store.getters.client })

// 登录发送验证码
export const apiLoginCaptcha = (data) => request.post('login/captcha', data)

// 手机号注册
export const apiAccountRegister = (data) =>
    request.post('register/register', {
        ...data,
        register_source: store.getters.client
    })

// 注册发送验证码
export const apiRegisterCaptcha = (data) => request.post('register/captcha', data)

// 重置登录密码
export const apiResetPassword = (data) => request.post('user/resetPassword', data)

// 发送验证码 - 重置登录密码
export const apiResetPasswordCaptcha = (data) => request.post('user/resetPasswordCaptcha', data)

// 微信小程授权登录
export const apiAuthLogin = (params) => request.post('login/authLogin', params)

// 微信小程序静默登录
export const apiSilentLogin = (params) => request.post('login/silentLogin', params)

//更新小程序头像昵称
export const apiUpdateUser = (params, token) => {
    return request.post('login/updateUser', params, { headers: { token } })
}

// 头条小程序授权登录
export const apiToutiaoAuthLogin = (params) => request.post('login/toutiaoAuthLogin', params)

// 头条小程序静默登录
export const apiToutiaoSilentLogin = (params) => request.post('login/toutiaoSilentLogin', params)

// 公众号登录
export const apiOALogin = (params) => request.post('login/oaLogin', params)

// app登录
export const apiAppWxLogin = (params) =>
    request.post('login/uninAppLogin', {
        ...params,
        terminal: store.getters.client
    })

// 向微信请求code的链接
export const apiCodeUrlGet = () =>
    request.get('login/codeUrl', {
        params: {
            url: location.href
        },
        headers: { 1: 1 }
    })

//微信sdk配置
export function apiJsConfig() {
    return request.get('wechat/jsConfig', {
        params: {
            url: wechath5.signLink()
        }
    })
}

// 公共配置
export const apiConfig = (params) => request.get('config/getconfig', { params })

// 支付方式
export const apiPayway = (params) =>
    request.get('pay/payway', {
        params: { ...params, scene: store.getters.client }
    })

// 预支付
export const apiPrepay = (params, token) =>
    request.post(
        'pay/prepay',
        {
            ...params,
            order_source: store.getters.client
        },
        { headers: { token } }
    )

// 支付结果查询
export const apiPayStatus = (params) => request.get('pay/payStatus', { params })

// 微信消息订阅
export const apiSubscribe = (data) => request.post('subscribe/lists', data)

// 访客记录
export const apiVisit = () => request.post('index/visit', { terminal: store.getters.client })

// 获取服务协议
export const apiPolicyAgreement = (params) => request.get('config/getPolicyAgreement', { params })

// 获取微信小程序码
export const apiMnpQrCode = (params) => request.get('share/getMnpQrCode', { params })

// 获取头条小程序码
export const apiTtQrCode = (params) => request.get('share/getTouTiaoQrcode', { params })

// 支付结果
export const apiPayResult = (params) => request.get('pay/payResult', { params })

export const apiGoodsPosterConfig = (params) => request.get('/poster/getGoodsConfig', { params })

// 分销海报
export const apiInvitePosterConfig = (params) =>
    request.get('poster/getDistributionConfig', { params })
//检测手机号
export const apiCheckMobile = (data) => request.post('login/check_mobile_user', data)

export const apiserviceConfig = (data) => request.post('config/getKefuConfig', data)
export const apishareConfig = (data) => request.post('config/getShareConfig', data)
