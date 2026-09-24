import Cache from '@/utils/cache'
import { ACTIVITY_DEDUCT_INFO } from '@/config/cachekey'
import { apiGetDeductDisplayInfo } from '@/api/activity_deduct'

const state = {
    // 冷启动先用缓存渲染（原先「只写不读」，缓存等于白写，主题必须等接口返回才生效）
    deductInfo: Cache.get(ACTIVITY_DEDUCT_INFO) || {}
}

const mutations = {
    setDeductInfo(state, data) {
        state.deductInfo = data
        Cache.set(ACTIVITY_DEDUCT_INFO, data)
    }
}

const actions = {
    getDeductInfo({ commit }) {
        apiGetDeductDisplayInfo().then((res) => {
            if (res) commit('setDeductInfo', res)
        }).catch(() => {})
    }
}

export default {
    state,
    mutations,
    actions
}
