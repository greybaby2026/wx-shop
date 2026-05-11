/**
 * Socket
 */

import {objectToQuery} from '@/utils/util'

class Socket {
    constructor({ws, params, ...event}) {
        this.ws = ws + `?${objectToQuery(params)}`
        this.serve = null
        this.event = event
        this.reconnectLock = true
		this.reconnectTimeout = null
		this.reconnectNums = 0
		// 心跳
		this.timeout = 10000
		this.clientTimeout = null
		this.serverTimeout = null
        this.init()
    }

    init() {
        if (this.ws) {
            this.serve = new WebSocket(this.ws)
            /* 钩子方法 */
            this.serve.onopen = this.onOpen.bind(this)
            this.serve.onerror = this.onError.bind(this)
            this.serve.onmessage = this.onMessage.bind(this)
            this.serve.onclose = this.onClose.bind(this)
        }
    }

    onOpen() {
        console.log('Open')
        this.start()
        this.event.open && this.event.open()
    }

    onError(e) {
        console.log('Error')
        this.event.error && this.event.error(e)
    }

    onMessage(data) {
        console.log('Message')
        this.reset()
        this.event.message && this.event.message(data)
    }

    onClose() {
        console.log('Close')
        this.reconnect()
        this.event.close && this.event.close()
    }


    send(data) {
        this.serve.send(JSON.stringify(data))
    }

    reset() {
		this.reconnectNums = 0
		this.start()
	}


    reconnect() {
		if (!this.reconnectLock) {
			return
		}
		console.log(this.reconnectNums)
		// 重连次数过多，断开不重连
		if (this.reconnectNums >= 5) {
			return
		}

		this.reconnectNums++
		this.reconnectLock = false
		// 延迟重连请求过多
		clearTimeout(this.reconnectTimeout)
		this.reconnectTimeout = setTimeout(() => {
			this.init()
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
				this.serve.close()
			}, this.timeout)
		}, this.timeout)
	}
    close() {
		this.reconnectLock = false
		clearTimeout(this.clientTimeout)
		clearTimeout(this.serverTimeout)
		this.serve.close && this.serve.close()
	}
}

export default Socket
