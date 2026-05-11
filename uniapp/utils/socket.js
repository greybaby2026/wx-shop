// 
import {
	paramsToStr
} from './tools'
class Socket {
	constructor(wssUrl, data) {
		this.connected = false
		this.error = false
		this.url = `${wssUrl}${paramsToStr(data)}`
		this.socketTask = {}
		this.reconnectLock = true
		this.reconnectTimeout = null
		this.reconnectNums = 0
		// 心跳
		this.timeout = 10000
		this.clientTimeout = null
		this.serverTimeout = null
	}
	// 允许的订阅
	events = {
		connect: null,
		close: null,
		message: null,
		error: null,
		open: null
	}

	// 添加订阅
	addEvent(type, callback) {
		this.events[type] = callback
	}

	// 触发订阅
	dispatch(type, data) {
		const fun = this.events[type]
		fun && fun(data)
	}

	connect() {
		// 已经连接则无需重复连接
		if (this.connected) return
		this.dispatch('connect')
		this.socketTask = uni.connectSocket({
			url: this.url,
			complete: () => {}
		})
		this.socketTask.onOpen(this.onOpen.bind(this))
		this.socketTask.onError(this.onError.bind(this));
		this.socketTask.onMessage(this.onMessage.bind(this))
		this.socketTask.onClose(this.onClose.bind(this));
	}
	close() {
		this.reconnectLock = false
		clearTimeout(this.clientTimeout)
		clearTimeout(this.serverTimeout)
		
		this.socketTask.close && this.socketTask.close()
		
	}
	reconnect() {
		if (!this.reconnectLock) {
			return
		}
		// 重连次数过多，断开不重连
		if (this.reconnectNums >= 5) {
			return
		}

		this.reconnectNums++
		this.reconnectLock = false
		// 延迟重连请求过多
		clearTimeout(this.reconnectTimeout)
		this.reconnectTimeout = setTimeout(() => {
			this.connect()
			this.reconnectLock = true
		}, 4000)
	}
	start() {
		clearTimeout(this.clientTimeout)
		clearTimeout(this.serverTimeout)
		this.clientTimeout = setTimeout(() => {
			this.send({
				event: 'ping'
			})
			this.serverTimeout = setTimeout(() => {
				this.socketTask.close()
			}, this.timeout)
		}, this.timeout)
	}

	reset() {
		this.reconnectNums = 0
		this.start()
	}

	send(data) {
		// 如果socket已连接则发送消息
		if (!this.connected) {
			return
		}
		let datas = JSON.stringify(data)
		// console.log('发送信息:' + datas)
		this.socketTask.send({
			data: datas,
		})
	}
	onOpen() {
		this.connected = true

		// 开启心跳
		this.start()

		// console.log('连接成功')

		this.dispatch('open')
	}
	onError(res) {
		this.error = true
		this.connected = false
		this.dispatch('error')
		// console.log('连接错误', res)
	}
	onMessage({data}) {
		this.dispatch('message', JSON.parse(data))
		// console.log('收到信息:', data)
		// 重置心跳
		this.reset()
	}
	onClose(res) {
		this.dispatch('close')
		// console.log('连接已关闭', res)
		this.connected = false
		this.reconnect()
	}

}
export default Socket
