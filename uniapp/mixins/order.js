// 订单Mixins
import {
	apiOrderClose,
	apiOrderConfirm,
	apiOrderDelete,
	getwechatSyncCheck,
	getwxReceiveDetail
} from '@/api/order'
import {
	compareWeChatVersion
} from '@/utils/tools'
import store from '@/store'

const OrderMixin = {
	data() {
		return {
			// 订单按钮组
			ButtonsMap: {
				payment: {
					event: 'payment',
					name: '立即付款',
					style: 'primary'
				},
				succeed: {
					event: 'succeed',
					name: '确认收货',
					style: 'normal'
				},
				evaluate: {
					event: 'evaluate',
					name: '去评价',
					style: 'normal'
				},
				content: {
					event: 'content',
					name: '查看内容',
					style: 'muted'
				},
				express: {
					event: 'express',
					name: '查看物流',
					style: 'muted'
				},
				close: {
					event: 'close',
					name: '取消订单',
					style: 'muted'
				},
				delete: {
					event: 'delete',
					name: '删除订单',
					style: 'muted'
				},
				pickup: {
					event: 'pickup',
					name: '查看提货码',
					style: 'normal'
				}
			},
			isDetail: false
		}
	},
	onLoad(options) {
		if (options != null && options.hasOwnProperty('order_id')) {
			this.isDetail = true;
			console.log(this.isDetail)
		}
	},


	methods: {
		// 获取订单状态按钮组
		getOrderStatusButtons(buttonStatus) {
			let buttons = []

			// 删除
			buttonStatus.delete_btn && buttons.push(this.ButtonsMap['delete'])
			// 取消
			buttonStatus.cancel_btn && buttons.push(this.ButtonsMap['close'])

			// 查看内容
			buttonStatus.content_btn && buttons.push(this.ButtonsMap['content'])
			// 物流
			buttonStatus.delivery_btn && buttons.push(this.ButtonsMap['express'])
			// 评论
			buttonStatus.comment_btn && buttons.push(this.ButtonsMap['evaluate'])
			// 收货
			buttonStatus.confirm_btn && buttons.push(this.ButtonsMap['succeed'])
			// 提货码
			buttonStatus.pickup_btn && buttons.push(this.ButtonsMap['pickup'])
			// 支付
			buttonStatus.pay_btn && buttons.push(this.ButtonsMap['payment'])

			return buttons
		},

		// 点击订单按钮映射处理方法
		onOrderButtons(event, orderID, pay_way) {
			switch (event) {
				case 'payment':
					return this.handlePayment(orderID)
				case 'succeed':
					return this.handleSucceed(orderID, pay_way)
				case 'evaluate':
					return this.handleEvaluate(orderID)
				case 'express':
					return this.handleExpress(orderID)
				case 'close':
					return this.handleClose(orderID)
				case 'delete':
					return this.handleDelete(orderID)
				case 'pickup':
					return this.handlePickup(orderID)
				case 'content':
					return this.handleContent(orderID)
			}
		},

		// 刷新订单数据
		refreshOrderData() {
			throw new Error('未初始化刷新方法')
		},

		// 处理：支付
		handlePayment(orderID) {
			this.$Router.push({
				path: `/bundle/pages/payment/payment`,
				query: {
					from: 'order',
					order_id: orderID
				}
			})
		},
		// 小程序确认收货
		comfirmReceive(transaction_id) {
			return new Promise((resolve, reject) => {
				// #ifdef MP-WEIXIN
				wx.openBusinessView({
					businessType: 'weappOrderConfirm',
					extraData: {
						transaction_id
					},
					success({
						extraData
					}) {
						if (extraData.status == 'success') {
							resolve('确认收货')
						} else {
							resolve('取消收货')
						}
					},
					fail(err) {
						reject(err)
					}
				})
				// #endif
				// #ifndef MP-WEIXIN
				// 非微信端不存在「微信原生确认收货」能力：必须让 Promise settle，
				// 否则调用方 await 永久挂起（原实现整个 Promise 体被条件编译排除，Promise 永不 settle）
				resolve('当前端不支持微信原生确认收货')
				// #endif
			})
		},
		//查询是否收货成功
		querycomfirmReceive(id) {
			return new Promise((resolve, reject) => {
				getwechatSyncCheck({
						id
					})
					.then((data) => {
						if (data.order.order_state === 4) {
							resolve('已确认收货')
						} else {
							reject('未确认收货')
						}
					})
					.catch((err) => {
						reject(err)
					})
			})
		},

		// 处理：确认收货
		async handleSucceed(orderID, pay_way) {
			console.log(pay_way)
			// #ifdef MP-WEIXIN
			if (store.state.app.config.mini_express_send_sync) {
				let res = {}
				if (pay_way === 2) {
					try {
						res = await getwechatSyncCheck({
							id: orderID
						})
					} catch (error) {
						uni.showModal({
							title: '温馨提示',
							content: '是否确认收货?',
							confirmColor: this.themeColor,
							success: ({
								confirm
							}) => {
								if (!confirm) return
								// uni.showLoading()
								// 取消订单
								apiOrderConfirm({
										id: orderID
									})
									.then((data) => {
										this.$toast({
											title: '收货成功'
										})
										this.refreshOrderData()
									})
									.catch((err) => {
										console.log(err)
									})
									.finally(() => {
										// uni.hideLoading()
									})
							}
						})
						return
					}
				}
				// #ifdef MP-WEIXIN
				if (
					compareWeChatVersion('2.6.0') === 1 &&
					wx.openBusinessView &&
					pay_way === 2 &&
					res.order.order_state !== 1
				) {
					try {
						const {
							transaction_id
						} = await getwxReceiveDetail({
							order_id: orderID
						})
						await this.comfirmReceive(transaction_id)
						await this.querycomfirmReceive(orderID)
						await apiOrderConfirm({
							id: orderID
						})
					} catch (error) {
						console.log(error)
					}
					this.refreshOrderData()
				} else {
				// #endif
					uni.showModal({
						title: '温馨提示',
						content: '是否确认收货?',
						confirmColor: this.themeColor,
						success: ({
							confirm
						}) => {
							if (!confirm) return
							// uni.showLoading()
							// 取消订单
							apiOrderConfirm({
									id: orderID
								})
								.then((data) => {
									this.$toast({
										title: '收货成功'
									})
									this.refreshOrderData()
								})
								.catch((err) => {
									console.log(err)
								})
								.finally(() => {
									// uni.hideLoading()
								})
						}
					})
				}
			} else {
				uni.showModal({
					title: '温馨提示',
					content: '是否确认收货?',
					confirmColor: this.themeColor,
					success: ({
						confirm
					}) => {
						if (!confirm) return
						// uni.showLoading()
						// 取消订单
						apiOrderConfirm({
								id: orderID
							})
							.then((data) => {
								this.$toast({
									title: '收货成功'
								})
								this.refreshOrderData()
							})
							.catch((err) => {
								console.log(err)
							})
							.finally(() => {
								// uni.hideLoading()
							})
					}
				})
			}

			// #endif
			// #ifndef MP-WEIXIN
			uni.showModal({
				title: '温馨提示',
				content: '是否确认收货?',
				confirmColor: this.themeColor,
				success: ({
					confirm
				}) => {
					if (!confirm) return
					// uni.showLoading()
					// 取消订单
					apiOrderConfirm({
							id: orderID
						})
						.then((data) => {
							this.$toast({
								title: '收货成功'
							})
							this.refreshOrderData()
						})
						.catch((err) => {
							console.log(err)
						})
						.finally(() => {
							// uni.hideLoading()
						})
				}
			})
			// #endif
		},
		handleContent(orderID) {
			this.$Router.push({
				path: `/bundle/pages/order_detail/order_detail`,
				query: {
					order_id: orderID
				}
			})
		},
		// 处理：去评价
		handleEvaluate(orderID) {
			this.$Router.push({
				path: '/bundle/pages/goods_comment/goods_comment'
			})
		},

		// 处理：查看物流
		handleExpress(orderID) {
			this.$Router.push({
				path: '/bundle/pages/order_logistics/order_logistics',
				query: {
					order_id: orderID
				}
			})
		},

		// 处理：关闭订单
		handleClose(orderID) {
			uni.showModal({
				title: '温馨提示',
				content: '是否取消订单?',
				confirmColor: this.themeColor,
				success: ({
					confirm
				}) => {
					if (!confirm) return
					// uni.showLoading()
					// 取消订单
					apiOrderClose({
							id: orderID
						})
						.then((data) => {
							this.$toast({
								title: '取消成功'
							})
							this.refreshOrderData()
						})
						.catch((err) => {
							console.log(err)
						})
						.finally(() => {
							// uni.hideLoading()
						})
				}
			})
		},

		// 处理：删除订单
		handleDelete(orderID) {
			uni.showModal({
				title: '温馨提示',
				content: '是否删除订单?',
				confirmColor: this.themeColor,
				success: ({
					confirm
				}) => {
					if (!confirm) return
					// uni.showLoading()
					// 取消订单
					apiOrderDelete({
							id: orderID
						})
						.then((data) => {
							this.$toast({
								title: '删除成功'
							})
							if (this.isDetail) {
								uni.navigateBack({
									delta: 1,
									success: function() {
										// 返回后通知列表页刷新数据。
										// ⚠️ 原实现是 `prevPage.onLoad()` —— 无参手动重跑上一页的 onLoad：
										//    ① 破坏「onLoad 只执行一次」语义，且上一页会拿到 undefined 的 options
										//       （本项目 mixins/order.js:63 恰好有 options != null 守卫才没出事，
										//        其它页面若直接读 options.xxx 将抛 TypeError）
										//    ② 上一页的初始化（请求、埋点、状态重置）会被重复执行一遍
										// 改为事件通知，由全局 mixin（mixins/app.js）转发到页面的 refreshOrderData()
										uni.$emit('orderListRefresh')
									}
								});

							} else {
								this.refreshOrderData()
							}

						})
						.catch((err) => {
							console.log(err)
						})
						.finally(() => {
							// uni.hideLoading()
						})
				}
			})
		},

		// 处理：查看提货码
		handlePickup(orderID) {
			this.$Router.push({
				path: `/bundle/pages/order_detail/order_detail`,
				query: {
					order_id: orderID
				}
			})
		}
	}
}

export default OrderMixin