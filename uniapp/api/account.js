import request from '@/utils/request'
import store from 'store'


// 账号登录
export const apiAccountLogin = params => 
	request.post("login/account", {...params, client:store.getters.client})

// 小程序授权登录
export const apiAuthLogin = params => 
	request.post('login/authLogin', params)

// 小程序静默登录
export const apiSilentLogin = params => 
	request.post('login/silentLogin', params)

// 公众号登录
export const apiOALogin = params => 
	request.post('login/oaLogin', params)

// 向微信请求code的链接
export const apiCodeUrlGet = () => 
	request.get('login/codeUrl', {
		params: {
			url: encodeURIComponent(location.href)
		}
	})
