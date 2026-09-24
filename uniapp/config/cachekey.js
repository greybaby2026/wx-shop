module.exports = {
	USER_INFO: 'user_info', // 用户信息
	TOKEN: 'token', // Token
	BACK_URL: 'back_url', // Back URL
	CONFIG: 'config', // 配置
	INVITE_CODE: 'invite_code', // Invite Code
	PENDING_STORE_ID: 'pending_store_id', // 待绑定门店(扫码未登录暂存)
	THEME_CONFIG: 'theme_config',
	// 开屏广告「只展示一次」相关（原先散落为 App.vue / open-advertisement.vue 中的字符串字面量，
	// 极易被后续改动写成不一致的键名而静默失效）
	OPENIMAGE_ENABLE: 'OPENIMAGE_ENABLE', // 本次冷启动是否允许展示
	OPENIMAGE: 'OPENIMAGE', // 上次展示的图片（show_config=1 时用于判断图片是否变化）
	OPENIMAGE_NUMBER: 'OPENIMAGE_NUMBER', // 剩余展示次数（show_config=2 时使用）
	// 抵扣活动展示信息（缓存供冷启动时先用缓存渲染主题，避免等接口）
	ACTIVITY_DEDUCT_INFO: 'ACTIVITY_DEDUCT_INFO',
}
