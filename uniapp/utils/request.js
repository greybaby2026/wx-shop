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
        store.commit('logout')
        return Promise.reject(msg)
    },
    closeShop({ msg }) {
        setTimeout(() => {
            uni.navigateTo({
                url: '/bundle/pages/business_suspended/business_suspended'
            })
        }, 0)
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
        Promise.reject(error)
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
        return events[APICodeEnum[code]](response.data)
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
