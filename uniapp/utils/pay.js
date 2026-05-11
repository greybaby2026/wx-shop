import { router } from '../router'
import wechath5 from './wechath5'
import { handleClientEvent } from './tools'
import Cache from './cache'

// 微信支付
export const wxpay = async (options) => {
    await handleClientEvent({
        // 微信小程序
        MP_WEIXIN: () => {
            return new Promise((resolve, reject) => {
                uni.requestPayment({
                    provider: 'wxpay',
                    timeStamp: options.timeStamp,
                    // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
                    nonceStr: options.nonceStr,
                    // 支付签名随机串，不长于 32 位
                    package: options.package,
                    // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=***）
                    signType: options.signType,
                    // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
                    paySign: options.paySign,

                    success: (res) => resolve(res),
                    cancel: (res) => reject(res),
                    fail: (res) => reject(res)
                })
            })
        },

        // 微信公众号
        OA_WEIXIN: () => {
            return wechath5.wxPay(options)
        },

        // H5
        H5: () => {
            return new Promise((resolve, reject) => {
                window.open(options, '_self')
                resolve()
            })
        },

        // APP
        IOS: () => {
            return new Promise((resolve, reject) => {
                uni.requestPayment({
                    provider: 'wxpay',
                    orderInfo: options,
                    success: (res) => resolve(res),
                    cancel: (res) => reject(res),
                    fail: (res) => reject(res)
                })
            })
        },
        ANDROID: () => {
            return new Promise((resolve, reject) => {
                uni.requestPayment({
                    provider: 'wxpay',
                    orderInfo: options,
                    success: (res) => resolve(res),
                    cancel: (res) => reject(res),
                    fail: (res) => reject(res)
                })
            })
        }
    })
}

// 支付宝支付
export const alipay = async (options, params, token) => {
    await handleClientEvent({
        // 微信小程序
        MP_WEIXIN: () => {
            return new Promise((resolve, reject) => {
                uni.$emit('Alipay')
                // resolve()
            })
        },
        // 微信公众号
        OA_WEIXIN: () => {
            return new Promise((resolve, reject) => {
                const container = document.createElement('div')
                container.style.display = 'none'
                container.innerHTML = options
                document.body.appendChild(container)
                const form = container.querySelector('form')
                if (form) {
                    form.submit()
                    setTimeout(() => {
                        resolve()
                    }, 2000)
                } else {
                    reject('未找到表单')
                }
            })
        },
        // H5
        H5: () => {
            return new Promise((resolve, reject) => {
                const container = document.createElement('div')
                container.style.display = 'none'
                container.innerHTML = options
                document.body.appendChild(container)
                const form = container.querySelector('form')
                if (form) {
                    form.submit()
                    setTimeout(() => {
                        resolve()
                    }, 2000)
                } else {
                    reject('未找到表单')
                }
            })
        },

        // APP
        IOS: () => {
            return new Promise((resolve, reject) => {
                uni.requestPayment({
                    provider: 'alipay',
                    orderInfo: options,
                    success: (res) => resolve('success'),
                    cancel: (res) => reject('fail'),
                    fail: (res) => reject('fail')
                })
            })
        },
        ANDROID: () => {
            console.log(options)
            return new Promise((resolve, reject) => {
                uni.requestPayment({
                    provider: 'alipay',
                    orderInfo: options,
                    success: (res) => resolve('success'),
                    cancel: (res) => reject('fail'),
                    fail: (res) => reject('fail')
                })
            })
        }
    })
}
export const ttpay = async (options) => {
    return new Promise((resolve, reject) => {
        tt.pay({
            orderInfo: options,
            service: 5,
            success: (res) => {
                if (res.code == 0) {
                    resolve(res)
                } else {
                    reject(res)
                }
            },
            cancel: (res) => reject(res),
            fail: (res) => reject(res)
        })
    })
}
