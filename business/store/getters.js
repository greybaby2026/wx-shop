export default {
	// 公共配置
	appConfig: state => state.app.config || {},
	// 用户信息
	shopInfo: state => state.app.shopInfo || {},
	// token
	token: state => state.app.token,
	// 客户端
	client: state => state.app.client,
	// 是否登录
	isLogin: state => !!state.app.token
};
