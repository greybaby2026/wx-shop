import { apiLogin, apiKefuDetail } from '@/api/app'

// State
const state = {
    config: {},
}

// Mutations
const mutations = {
    setConfig(state, data) {
        state.config = data
    }
}

// Actions
const actions = {
    
}

const user = {
    state,
    mutations,
    actions,
}

export default user
