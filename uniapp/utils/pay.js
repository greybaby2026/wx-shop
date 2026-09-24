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
            // ⚠️ 已知且**有意保留**的「Promise 永不 settle」：本次（批次3 FIX-U16）未改，原因如下 ——
            //   小程序内无法调起支付宝，这里只发 uni.$emit('Alipay') 让支付页展示
            //   「复制链接到浏览器支付」弹窗（payment.vue:262 监听 → Alipayshow=true）。
            //   而调用方 payment.vue 的支付链是「无论 resolve 还是 reject，都会
            //   .then/.catch 里调 handlePayResult() 跳支付结果页」—— 一旦在此 settle，
            //   弹窗会被立刻跳走，用户无法拿到付款链接。
            //   因此要修必须同时改 payment.vue 的跳转策略（属业务语义变更），
            //   超出批次3「只改契约与兜底」的边界，需真机 + 产品确认后另行处理。
            //   现状影响：按钮 loading 会一直转，直到用户关闭弹窗（handleclose 会复位）。
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
    // #ifdef MP-TOUTIAO
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
    // #endif
    // #ifndef MP-TOUTIAO
    return Promise.reject('非头条小程序环境')
    // #endif
}
