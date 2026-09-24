import { apiConfig } from '@/api/app'
import { apiUserCentre, apiDistributionCode , apiUserBindStore } from '@/api/user'
import { CONFIG, USER_INFO, TOKEN, INVITE_CODE , PENDING_STORE_ID } from '@/config/cachekey'
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
