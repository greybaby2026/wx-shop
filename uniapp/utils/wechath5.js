// #ifdef H5
import weixin from '@/js_sdk/jweixin-module'
import { isAndroid } from './tools'
import { apiJsConfig, apiCodeUrlGet, apiOALogin } from '@/api/app'
import store from '../store'
import Cache from './cache'
class Wechath5 {
    //获取微信配置url
    signLink() {
        if (typeof window.entryUrl === 'undefined' || window.entryUrl === '') {
            window.entryUrl = location.href.split('#')[0]
        }
        return isAndroid() ? location.href.split('#')[0] : window.entryUrl
    }
    //微信sdk配置
    config() {
        return new Promise((resolve, reject) => {
            apiJsConfig()
                .then((res) => {
                    weixin.config({
                        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                        appId: res.appId, // 必填，公众号的唯一标识
                        timestamp: res.timestamp, // 必填，生成签名的时间戳
                        nonceStr: res.nonceStr, // 必填，生成签名的随机串
                        signature: res.signature, // 必填，签名
                        jsApiList: res.jsApiList // 必填，需要使用的JS接口列表
                    })
                    resolve()
                })
                .catch((err) => {
                    // apiJsConfig 失败时必须 settle：否则 Promise 永不 resolve →
                    // 下游 weixin.ready() 的回调永不执行 → 分享/支付静默失效且极难排查
                    reject(err || '微信 JSSDK 配置失败')
                })
        })
    }

    //获取微信登录url
    getWxUrl() {
        apiCodeUrlGet().then((res) => {
            location.href = res.url
        })
    }

    //微信授权
    authLogin(code) {
        return new Promise((resolve, reject) => {
            apiOALogin({
                code
            }).then((res) => {
                store.commit('login', res)
                resolve(res)
            })
        })
    }

    //微信分享
    share(option) {
        weixin.ready(() => {
            const { shareTitle, shareLink, shareImage, shareDesc } = option
            // 发送给好友（旧）
            weixin.onMenuShareAppMessage({
                title: shareTitle, // 分享标题
                link: shareLink, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: shareImage, // 分享图标
                success: function (res) {
                    // 设置成功
                }
            })
            // 分享到朋友圈（旧）
            weixin.onMenuShareTimeline({
                title: shareTitle, // 分享标题
                link: shareLink, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: shareImage, // 分享图标
                success: function (res) {
                    // 设置成功
                }
            })
            weixin.updateTimelineShareData({
                title: shareTitle, // 分享标题
                link: shareLink, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: shareImage, // 分享图标
                success: function (res) {
                    // 设置成功
                }
            })
            // 发送给好友
            weixin.updateAppMessageShareData({
                title: shareTitle, // 分享标题
                link: shareLink, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: shareImage, // 分享图标
                desc: shareDesc,
                success: function (res) {
                    // 设置成功
                }
            })
            // 发送到tx微博
            weixin.onMenuShareWeibo({
                title: shareTitle, // 分享标题
                link: shareLink, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: shareImage, // 分享图标
                desc: shareDesc,
                success: function (res) {
                    // 设置成功
                }
            })
        })
    }
    wxPay(opt) {
        return new Promise((reslove, reject) => {
            weixin.ready(() => {
                weixin.chooseWXPay({
                    timestamp: opt.timeStamp, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
                    nonceStr: opt.nonceStr, // 支付签名随机串，不长于 32 位
                    package: opt.package, // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=***）
                    signType: opt.signType, // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
                    paySign: opt.paySign, // 支付签名
                    success: (res) => {
                        reslove()
                    },
                    // reject 必须带上原因：否则调用方无法区分「用户取消」与「支付失败」，也无法提示具体原因
                    cancel: (res) => {
                        reject(res || '已取消支付')
                    },
                    fail: (res) => {
                        reject(res || '支付失败')
                    }
                })
            })
        })
    }

    getWxAddress() {
        return new Promise((reslove, reject) => {
            weixin.ready(() => {
                weixin.openAddress({
                    success: (res) => {
                        reslove(res)
                    },
                    // 用户取消/拒绝授权是高频正常操作：必须 reject 让 Promise settle，
                    // 否则调用方 await 永久挂起 → 点「获取微信地址」后按钮像坏了（无任何反馈）
                    cancel: (res) => reject(res || '已取消获取微信地址'),
                    fail: (res) => reject(res || '获取微信地址失败')
                })
            })
        })
    }
    reviceTransfer(mchId, appId, packageInfo) {
        return new Promise((reslove, reject) => {
            weixin.ready(() => {
                weixin.checkJsApi({
                    jsApiList: ['requestMerchantTransfer'],
                    // checkJsApi 失败（JSSDK 未就绪等）同样必须 settle，否则调用方永久挂起
                    fail: (res) => reject(res || '微信 JSSDK 校验失败'),
                    success: function (res) {
                        if (res.checkResult['requestMerchantTransfer']) {
                            WeixinJSBridge.invoke(
                                'requestMerchantTransfer',
                                {
                                    mchId,
                                    appId,
                                    package: packageInfo
                                },
                                function (res) {
                                    if (res.err_msg === 'requestMerchantTransfer:ok') {
                                        reslove(res.err_msg)
                                    } else {
                                        reject(res.err_msg)
                                    }
                                }
                            )
                        } else {
                            // 版本过低也必须 settle：alert 改为 uni.showToast 并 reject
                            // （跨端体验一致，且避免调用方 await 永久挂起）
                            uni.showToast({
                                title: '你的微信版本过低，请更新至最新版本',
                                icon: 'none'
                            })
                            reject('微信版本过低')
                        }
                    }
                })
            })
        })
    }
}

export default new Wechath5()
// #endif
