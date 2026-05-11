import {
	isWeixinClient,
	currentPage,
	trottle
} from './tools'
import store from '@/store'
import Cache from './cache'
import {
	BACK_URL,
	INVITE_CODE
} from '@/config/cachekey'
import wechath5 from './wechath5'
import {
	apiSilentLogin,
	apiToutiaoSilentLogin
} from '@/api/app'
import {
	router
} from '@/router'


// 获取登录凭证（code）

export function getCode() {
	return new Promise((resolve, reject) => {
		uni.login({
			success(res) {
				console.log(res)
				resolve(res.code);
			},

			fail(res) {
				reject(res);
			}

		});
	});
}
//小程序获取用户信息
export function getUserProfile() {
	return new Promise((resolve, reject) => {
		uni.getUserProfile({
			desc: '获取用户信息，完善用户资料 ',
			success: (res) => {
				resolve(res);
			},
			fail(res) {
				console.log(res)
			}

		})
	})

}

//小程序静默授权

export async function mnpLogin() {
	const {
		coerce_mobile,
		mnp_auto_wechat_auth,
		toutiao_auto_auth
	} = store.getters.appConfig

	//#ifdef  MP-WEIXIN
	// 微信关闭自动授权
	if (!mnp_auto_wechat_auth) return
	// #endif

	//#ifdef  MP-TOUTIAO
	// 头条关闭自动授权
	if (!toutiao_auto_auth) return
	// #endif

	const code = await getCode()
	//#ifdef  MP-WEIXIN 
	const loginData = await apiSilentLogin({
		code
	})
	// #endif

	//#ifdef  MP-TOUTIAO
	const loginData = await apiToutiaoSilentLogin({
		code
	})
	// #endif
	const {
		options,
		onLoad,
		onShow,
		route
	} = currentPage()
	// 需要强制绑定手机号
	if (coerce_mobile && !loginData.mobile) {
		return
	}
	if (loginData.token && !loginData.is_new_user) {
		store.commit('login', loginData)
		store.dispatch('getUser')
		store.dispatch('getCartNum')
		// 刷新页面
		onLoad && onLoad(options)
		onShow && onShow()
	}

}
export const toLogin = trottle(_toLogin, 2000)

function _toLogin() {

	//#ifdef  MP
	mnpLogin()
	// #endif
	//#ifndef MP
	const {
		currentRoute
	} = router
	if (currentRoute.meta.auth) {
		router.push('/pages/login/login')
	}
	// #endif
}

