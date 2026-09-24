import { apiConfig } from '@/api/app'
import { apiUserCentre, apiDistributionCode , apiUserBindStore } from '@/api/user'
import { CONFIG, USER_INFO, TOKEN, INVITE_CODE , PENDING_STORE_ID, ACTIVITY_DEDUCT_INFO } from '@/config/cachekey'
import wechath5 from '@/utils/wechath5'
import Cache from '@/utils/cache'
import { router } from '@/router'
import { getClient, toast } from '@/utils/tools'
import { apiserviceConfig, apishareConfig } from '@/api/app'

// 强制绑定门店引导的时间节流（避免同一时间重复跳转「选择门店」页）
let lastBindStoreRedirect = 0

const state = {
    config: Cache.get(CONFIG) || {
        app_pop_agreement: 1
    },
    userInfo: {},
    token: Cache.get(TOKEN) || null,
    client: getClient() || null,
    serviceConfig: {},
    shareConfig: []
}

const mutations = {
    setshareConfig(state, data) {
        state.shareConfig = data
    },
    setserviceConfig(state, data) {
        state.serviceConfig = data
    },
    setConfig(state, data) {
        state.config = data
        Cache.set(CONFIG, data)
    },
    login(state, data) {
        state.token = data.token
        Cache.set(TOKEN, data.token)
        // 登录完成绑定邀请码
        const code = Cache.get(INVITE_CODE)
        if (code) {
            apiDistributionCode({
                code,
                hide: 1
            }).finally(() => {
                Cache.remove(INVITE_CODE)
            })
        }
        // 登录完成绑定门店(首绑定终身)
        const pendingStore = Cache.get(PENDING_STORE_ID)
        if (pendingStore) {
            apiUserBindStore({ store_id: pendingStore, hide: 1 })
                .then((res) => {
                    // 补绑成功给出明确反馈,避免用户对归属门店无感
                    if (res && res.is_new && res.store_name) {
                        setTimeout(() => {
                            toast({ title: `已加入${res.store_name}` })
                        }, 300)
                    }
                })
                .catch(() => {})
                .finally(() => {
                    Cache.remove(PENDING_STORE_ID)
                })
        }
    },
    logout(state) {
        state.token = ''
        state.userInfo = {}
        Cache.remove(TOKEN)
    },
    setUserInfo(state, data) {
        state.userInfo = data
    }
}

const actions = {
    /**
     * 登出（含 token 过期被强制登出）：
     * 除清理 token / 用户信息外，**必须一并复位「派生态」全局状态** ——
     * 否则 tabbar 的购物车角标与未读消息红点会保留上一账号的数字，换号登录后还会短暂显示他人数据。
     * 注意：只清用户态数据，公共配置（CONFIG / THEME_CONFIG）不清理，避免影响其它功能。
     */
    logout({ commit }) {
        // 1) 本模块：token / userInfo / TOKEN 缓存
        commit('logout')
        // 2) 购物车角标（cart.js 的 getCartNum 在未登录时直接 return，不会把 cartNum 归零）
        commit('setCartNum', 0, { root: true })
        // 3) 未读消息与通知弹窗状态
        commit('SET_UNREAD_LIST', [], { root: true })
        commit('SET_UNREAD_COUNT', 0, { root: true })
        commit('SET_SHOW_POPUP', false, { root: true })
        commit('SET_CURRENT_NOTICE', null, { root: true })
        // 4) 用户态的抵扣活动展示缓存（下次冷启动由接口重新拉取）
        Cache.remove(ACTIVITY_DEDUCT_INFO)
    },
    getsetshareConfig({ state, commit }) {
        return apishareConfig()
            .then((res) => {
                commit('setshareConfig', res)
                return Promise.resolve(res)
            })
            .catch(() => {
                return Promise.reject()
            })
    },
    getsetserviceConfig({ state, commit }) {
        return apiserviceConfig()
            .then((res) => {
                commit('setserviceConfig', res)
                return Promise.resolve(res)
            })
            .catch(() => {
                return Promise.reject()
            })
    },
    getConfig({ state, commit }) {
        return apiConfig()
            .then((res) => {
                commit('setConfig', res)
                return Promise.resolve(res)
            })
            .catch(() => {
                return Promise.reject()
            })
    },
    getUser({ state, commit }) {
        return new Promise((resolve, reject) => {
            apiUserCentre()
                .then((res) => {
                    commit('setUserInfo', res)
                    // 强制绑定门店（后台开关开启时）：登录后若未绑定门店，统一引导进入「选择门店」页
                    // 说明：开关默认关闭，需等「选择门店」页随客户端发版上线后再开启
                    if (
                        Number(res.force_bind_store || 0) === 1 &&
                        !res.bind_store_id &&
                        Date.now() - lastBindStoreRedirect > 3000
                    ) {
                        lastBindStoreRedirect = Date.now()
                        const pages = getCurrentPages()
                        const current = pages.length ? pages[pages.length - 1].route : ''
                        if (current !== 'bundle/pages/store_bind/store_bind') {
                            uni.navigateTo({ url: '/bundle/pages/store_bind/store_bind' })
                        }
                    }
                    resolve(res)
                })
                .catch(() => {
                    reject()
                })
        })
    },
    setWxShare({ state }, opt) {
        // #ifdef H5
        const { share_image, share_intro, share_title } = state.config
        const inviteCode = state.userInfo.code
        const href = window.location.href
        const sym = href.includes('?') ? '&' : '?'
        const option = {
            shareTitle: share_title,
            shareLink: inviteCode ? `${href}${sym}invite_code=${inviteCode}` : href,
            shareImage: share_image,
            shareDesc: share_intro
        }

        wechath5.share(Object.assign(option, opt))
        // #endif
    }
}

export default {
    state,
    mutations,
    actions
}
