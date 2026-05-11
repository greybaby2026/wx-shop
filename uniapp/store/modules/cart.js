import theme from '@/uni.scss'
import {
	THEME_CONFIG
} from '@/config/cachekey';
import Cache from '@/utils/cache'
import {
	apiCartNum
} from '@/api/store'
const state = {
	cartNum: 0
};

const mutations = {
	setCartNum(state, data) {
		state.cartNum = data
	},
};

const actions = {
	getCartNum({
		state,
		commit,
		getters
	}) {
		// 未登录
		if(!getters.isLogin) {
			return
		}
		apiCartNum().then(res => {
			commit('setCartNum', res.num)
		}).catch(() => {
			commit('setCartNum', 0)
		})
	}

};

export default {
	state,
	mutations,
	actions
};
