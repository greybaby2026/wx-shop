import axios from '@/js_sdk/uniapp-axios/dist/uni-axios'
import store from '@/store'
import { paramsToStr, currentPage, toast } from './tools'
import { APICodeEnum } from './enum'
import Cache from './cache'
import { TOKEN } from '@/config/cachekey'
import { baseURL, version } from '@/config/app'
import { toLogin } from './login'
import { router } from '@/router'

function checkParams(params) {
    if (typeof params != 'object') return params
    for (let key in params) {
        const value = params[key]
        if (value === null || value === undefined || value === '') {
            delete params[key]
        }
    }
    return params
}

const events = {
    // 成功
    success({ data }) {
        return Promise.resolve(data)
    },
    // 失败
    fail({ msg }) {
        return Promise.reject(msg)
    },
    // 重定向到登录
    redirect({ msg }) {
        // #ifdef H5
        if (store.getters.appConfig.h5_status) {
            toLogin()
        }
        // #endif
        // #ifdef MP-WEIXIN
        if (store.getters.appConfig.mnp_status) {
            toLogin()
        }
        // #endif
        // 用 dispatch 走 logout action：除清 token 外还会复位购物车角标/未读数等派生态状态
        store.dispatch('logout')
        return Promise.reject(msg)
    },
    closeShop({ msg }) {
        setTimeout(() => {
            uni.navigateTo({
                url: '/bundle/pages/business_suspended/business_suspended'
            })
        }, 0)
        // 必须返回 rejected Promise（同对象内其余 4 个事件均已如此）：
        // 原先无 return，业务层 await 得到 undefined 且不会 reject →
        // 页面继续执行「下单成功」后续逻辑（弹成功提示、跳订单详情），而实际商城已关闭 → 误导用户
        return Promise.reject(msg)
    },
    // 提示
    tips({ code, msg }) {
        return { code, msg }
    }
}

const service = axios.create({
    baseURL: baseURL + '/shopapi/',
    timeout: 10000,
    headers: {
        'content-type': 'application/json'
    }
})

// request拦截器
service.interceptors.request.use(
    (config) => {
        // 清楚空的字段
        config.data = checkParams(config.data)
        config.params = checkParams(config.params)
        // 请求头token
        config.headers.token = config.headers.token || store.getters.token
        // 接口版本号
        config.headers.version = version
        return config
    },
    (error) => {
        // Do something with request error
        console.log(error) // for debug
        // 必须 return：原先漏写 return 时该回调返回 undefined，
        // axios 会把它当作「成功后的 config」继续走完拦截器链，
        // 带着无效 config 发请求或抛出与根因无关的二次错误（同文件响应拦截器已正确 return）
        return Promise.reject(error)
    }
)

// response 拦截器
service.interceptors.response.use(
    (response) => {
        const { msg, code, data, show } = response.data
        if (show && msg && code !== 10) {
            toast({
                title: msg
            })
        }
        // 业务码兜底：APICodeEnum 只有 5 个 key（1/0/-1/1020/10），后端返回表外 code
        // （1001、-2、null…或网关注入的码）时，原实现 events[undefined](...) 会直接抛
        // TypeError: events[...] is not a function —— 调用方 .catch(err) 读不到 msg，
        // 真实原因被掩盖，且形成大面积未处理 rejection。此处统一兜底为 fail（含 msg）。
        const handler = events[APICodeEnum[code]]
        if (!handler) {
            console.warn('[request] 未知业务码，已按 fail 兜底：code=', code, ' msg=', msg)
            return events.fail(response.data)
        }
        return handler(response.data)
    },
    (error) => {
        // uni.showToast({
        //   title: "系统错误，请稍候再试",
        //   icon: "none",
        // });
        console.log('err' + error) // for debug
        return Promise.reject(error)
    }
)

export default service
