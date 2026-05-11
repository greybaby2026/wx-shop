import { apiNoticeUnread, apiNoticeRead } from '@/api/store'

const notification = {
    state: {
        unreadList: [],
        unreadCount: 0,
        showPopup: false,
        currentNotice: null,
        lastCheckTime: 0,
        initialCheck: false,
    },
    mutations: {
        SET_UNREAD_LIST(state, list) {
            state.unreadList = list
        },
        SET_UNREAD_COUNT(state, count) {
            state.unreadCount = count
        },
        SET_SHOW_POPUP(state, show) {
            state.showPopup = show
        },
        SET_CURRENT_NOTICE(state, notice) {
            state.currentNotice = notice
        },
        SET_LAST_CHECK_TIME(state, time) {
            state.lastCheckTime = time
        },
        SET_INITIAL_CHECK(state, val) {
            state.initialCheck = val
        },
    },
    actions: {
        checkUnread({ commit, state, rootState }) {
            if (!rootState.app || !rootState.app.token) return Promise.resolve()
            const now = Date.now()
            if (now - state.lastCheckTime < 30000) return Promise.resolve()
            commit('SET_LAST_CHECK_TIME', now)
            return apiNoticeUnread().then(res => {
                if (!res || typeof res !== 'object') return
                commit('SET_UNREAD_LIST', res.lists || [])
                commit('SET_UNREAD_COUNT', res.count || 0)
                if (res.count > 0 && !state.showPopup) {
                    const delay = state.initialCheck ? 5000 : 1500
                    commit('SET_INITIAL_CHECK', true)
                    setTimeout(() => {
                        if (!state.showPopup) {
                            const notice = (res.lists && res.lists[0]) || null
                            if (notice) {
                                commit('SET_CURRENT_NOTICE', notice)
                                commit('SET_SHOW_POPUP', true)
                            }
                        }
                    }, delay)
                }
            }).catch(() => {})
        },
        markAsRead({ commit, state }, noticeId) {
            return apiNoticeRead({ id: noticeId || '' }).then(() => {
                commit('SET_SHOW_POPUP', false)
                commit('SET_CURRENT_NOTICE', null)
                if (noticeId) {
                    const newList = state.unreadList.filter(n => n.id !== noticeId)
                    commit('SET_UNREAD_LIST', newList)
                    commit('SET_UNREAD_COUNT', newList.length)
                } else {
                    commit('SET_UNREAD_LIST', [])
                    commit('SET_UNREAD_COUNT', 0)
                }
            }).catch(() => {})
        },
        hideNotificationPopup({ commit }) {
            commit('SET_SHOW_POPUP', false)
        },
    },
}

export default notification
