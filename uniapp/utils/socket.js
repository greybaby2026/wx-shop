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

	// 移除订阅（与 addEvent 配对，页面 onUnload 时应调用，避免持有已销毁页面的回调）
	offEvent(type) {
		if (Object.prototype.hasOwnProperty.call(this.events, type)) {
			this.events[type] = null
		}
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
			// 「静默失效」对用户不可接受（客服聊天会表现为「发消息没反应」）：给出可见提示
			uni.showToast({
				title: '连接已断开，请退出页面后重试',
				icon: 'none'
			})
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
				// 判空：与 close() 内的守卫保持一致（未连接/已释放时 socketTask 可能是空对象或 undefined）
				this.socketTask && this.socketTask.close && this.socketTask.close()
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
		// 服务端消息不一定是 JSON（纯文本 pong / 心跳应答 / 异常时的 HTML 错误页）：
		// 原实现直接 JSON.parse，抛 SyntaxError 会中断本回调后续语句 ——
		// 于是 reset() 不执行（心跳不重置、reconnectNums 不归零）→
		// 紧接着 start() 的 serverTimeout 会误判超时并主动关闭连接 → 触发重连；
		// 反复几次后 reconnectNums >= 5 → 彻底放弃重连且不给用户任何提示（客服聊天是核心通道）
		let payload
		try {
			payload = JSON.parse(data)
		} catch (e) {
			// 非 JSON：按协议只重置心跳，不派发消息
			this.reset()
			return
		}
		this.dispatch('message', payload)
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
