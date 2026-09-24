const Cache = {
	
	keyPrev: 'store_',
	//设置缓存(expire为缓存时效)
	set(key, value, expire) {
		let data = {
			expire: expire ? (this.time() + expire) : "",
			value
		}
		
		if (typeof data === 'object')
			data = JSON.stringify(data);
		try {
			uni.setStorageSync(this.getKey(key), data)
		} catch (e) {
			return false;
		}
	},
	get(key) {
		try {
			let data = uni.getStorageSync(this.getKey(key))
			const {value, expire} = JSON.parse(data)
			if(expire && expire < this.time()) {
				uni.removeStorageSync(this.getKey(key))
				return false;
			}else {
				return value
			}
		} catch (e) {
			return false;
		}
	},
	//获取当前时间
	time() {
		return Math.round(new Date() / 1000);
	},
	remove(key) {
		if(key) uni.removeStorageSync(this.getKey(key))
	},
	// 按前缀批量清理（登出等场景只需清理用户态缓存时使用，避免误清公共配置）
	// 例：Cache.removeByPrefix('') 清理全部本应用缓存；需按业务分区时给缓存键加统一前缀
	removeByPrefix(prefix = '') {
		try {
			const info = uni.getStorageInfoSync() || {}
			;(info.keys || []).forEach((key) => {
				if (key.indexOf(this.getKey(prefix)) === 0) uni.removeStorageSync(key)
			})
			return true
		} catch (e) {
			return false
		}
	},
	// 清空本应用全部缓存（⚠️ 含公共配置 CONFIG/THEME_CONFIG，仅用于「恢复出厂」类场景）
	clear() {
		return this.removeByPrefix('')
	},
	getKey(key) {
		return this.keyPrev + key
	}
}

export default Cache;
