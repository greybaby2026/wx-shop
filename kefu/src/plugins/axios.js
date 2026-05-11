'use strict'

import Vue from 'vue'
import store from '@/store'
import axios from 'axios'
import router from '@/router'
import configs from '@/config'
import { Message } from 'element-ui'
import { throttle } from '@/utils/util'

// 事件集
const eventResponse = {
	// 成功
	success({ show, msg, data }) {
		if (show * 1) Message({ type: 'success', message: msg });
		return data;
	},
	// 失败
	error({ show, msg }) {
		if (show * 1) Message({ type: 'error', message: msg });
		console.log("Error...");
		return Promise.reject();
	},
	// 重定向
	redirect: throttle(() => {
        store.dispatch('logout')
        router.replace('/login')
		return Promise.reject();
	}),
}



let config = {
    baseURL: `${configs.baseURL}`,
    headers: {
        'content-type': 'application/json',
    },
}

const _axios = axios.create(config)

_axios.interceptors.request.use(
    function (config) {
        config.headers['version'] = configs.version 
        // Do something before request is sent
		const token = store.getters.token;
		// header参入Token
		if (token) config.headers['token'] = token;
        return config
    },
    function (error) {
        // Do something with request error
		
        return Promise.reject(error)
    }
)

// Add a response interceptor
_axios.interceptors.response.use(
    function (response) {
        // Do something with response data
		const { code } = response.data;
		switch(code) {
			case 1: return eventResponse.success(response.data)
			case 0: return eventResponse.error(response.data)
			case -1: return eventResponse.redirect(response.data)
		}
    },
    function (error) {
        // Do something with response error
		Message({ type: 'error', message: '系统错误，请稍候再试' });
        return Promise.reject(error)
    }
)

Plugin.install = function (Vue, options) {
    Vue.axios = _axios
    window.axios = _axios
    Object.defineProperties(Vue.prototype, {
        axios: {
            get() {
                return _axios
            },
        },
        $axios: {
            get() {
                return _axios
            },
        },
    })
}

Vue.use(Plugin)

export default _axios
