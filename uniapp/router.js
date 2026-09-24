import {
    RouterMount,
    createRouter,
    runtimeQuit
} from './js_sdk/hhyang-uni-simple-router/uni-simple-router'
import { BACK_URL } from './config/cachekey'
import store from './store'
import { getCode } from './utils/login'
import Cache from './utils/cache'
import wechath5 from './utils/wechath5'
import { isWeixinClient } from './utils/tools'
const scrollInfo = {}
const whiteList = ['register', 'login', 'forget_pwd']
const router = createRouter({
    platform: process.env.VUE_APP_PLATFORM,
    APP: {
        animation: {}
    },
    routerErrorEach: ({ type, msg }) => {
        router.$lockStatus = false
        // #ifdef APP-PLUS
        if (type === 3) {
            runtimeQuit()
        }
        // #endif
    },
    debugger: false,
    routes: [
        ...ROUTES,
        {
            path: '*',
            // 未匹配路径统一落到 404 页。
            // 说明：原先 redirect 到 { name: '404' } 永远不生效 —— uni-read-pages 生成的
            // route.name 取自 pages.json 各页面的 name 字段，而全库无任何页面声明 name，
            // 因此未匹配路径既进不了 404 页也回不去首页，表现为「导航失败/白屏」。
            // 改为按「路径」重定向，不再依赖 name。
            redirect: () => {
                return {
                    path: '/pages/404/404'
                }
            }
        }
    ]
})

router.beforeEach((to, from, next) => {
    const index = whiteList.findIndex((item) => from.path.includes(item))

    if (index == -1 && !store.getters.token) {
        //保存登录前的路径
        Cache.set(BACK_URL, from.fullPath)
    }
    if (
        to.meta.auth &&
        !store.getters.token &&
        to.path !== '/bundle/pages/business_suspended/business_suspended'
    ) {
        next('/pages/login/login')
        return
    } else {
        next()
    }
})
router.afterEach((to, from, next) => {
    // #ifdef H5
    // 添加定时器防止拿到的域名是上一个域名
    setTimeout(async () => {
        if (isWeixinClient()) {
            if (to.path.includes('cancel_result')) return
            // jssdk配置
            await wechath5.config()
            // 分享配置
            if (to.path.includes('goods_detail')) return
            store.dispatch('setWxShare')
        }
    })

    // #endif
})

export { router, RouterMount }
