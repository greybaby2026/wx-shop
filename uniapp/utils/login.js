import {
	isWeixinClient,
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
			// 拒绝授权是高频正常操作：必须 reject 让 Promise settle，
			// 否则调用方 await 永久挂起 → 登录/绑定手机号流程卡住且无任何提示
			fail(res) {
				reject(res)
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
	// 需要强制绑定手机号
	if (coerce_mobile && !loginData.mobile) {
		return
	}
	if (loginData.token && !loginData.is_new_user) {
		store.commit('login', loginData)
		store.dispatch('getUser')
		store.dispatch('getCartNum')
		// 刷新当前页数据：改用事件通知，由页面按需响应（全局 mixin mixins/app.js 已监听）。
		// ⚠️ 原实现是 `onLoad && onLoad(options); onShow && onShow()` —— 直接手动重跑当前页生命周期：
		//    ① 破坏「onLoad 只执行一次」与参数契约
		//    ② 本函数触发频率极高：任何接口返回 -1（token 失效）都会走到这里，
		//       于是每次静默登录都重复初始化当前页 → 重复发请求、重复埋点，
		//       并重置用户已做的页面状态（已加载分页、已选筛选条件）
		uni.$emit('loginSuccess')
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

