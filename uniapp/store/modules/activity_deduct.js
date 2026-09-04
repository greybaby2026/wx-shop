import Cache from '@/utils/cache'
import { apiGetDeductDisplayInfo } from '@/api/activity_deduct'

const state = {
    deductInfo: {}
}

const mutations = {
    setDeductInfo(state, data) {
        state.deductInfo = data
        Cache.set('ACTIVITY_DEDUCT_INFO', data)
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
