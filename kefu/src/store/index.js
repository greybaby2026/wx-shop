import Vue from 'vue'
import Vuex from 'vuex'
import VuexPersistence from 'vuex-persist'
import modules from './modules'
import getters from './getters'
Vue.use(Vuex)

const vuexLocal = new VuexPersistence({
    key: 'vuexkefu', // 这里可以自定义存入localStorage的键名，默认vuex
    storage: window.localStorage,
    modules: ['user'],
})

export default new Vuex.Store({
    modules: modules,
	getters: getters,
    plugins: [vuexLocal.plugin],
})
