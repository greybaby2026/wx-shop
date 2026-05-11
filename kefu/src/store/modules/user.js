/**
 * 用户
 */


import {apiLogin, apiKefuDetail} from '@/api/app'


// State
const state = {
    token: '',
    userInfo: {},
}


// Mutations
const mutations= {
    // 设置用户信息
    SET_USER_INFO: (state, data) => {
        state.userInfo = data;
    },

    SET_TOKEN: (state, token) => {
        state.token = token
    },

    // 清除用户信息
    clearUserInfo: (state) => {
        state.userInfo = {};
    }
}


// Actions
const actions = {
    login({commit}, form) {
        return new Promise((resolve, reject) => {
            apiLogin(form).then(data => {
                commit('SET_TOKEN', data.token)
                resolve(data)
            }).catch(err => {
                reject(err)
            })
        })
    },

    getUserInfo({commit}) {
        return new Promise((resolve, reject) => {
            apiKefuDetail().then(data => {
                commit('SET_USER_INFO', data)
                resolve(data)
            }).catch(err => {
                // reject(err)
            })
        })
    },

    logout({commit}) {
        return new Promise((resolve, reject) => {
            commit('SET_TOKEN', '')
            commit('clearUserInfo')
            resolve()
        })
    }

}


const user = {
    state,
    mutations,
    actions
}

export default user
