// 订单Mixins

import {
    apiCancelIntegralOrder,
    apiDelIntegralOrder,
    apiConfirmIntegralOrder,
    getwechatSyncCheck,
    getwxReceiveDetail
} from '@/api/integral_mall'
import { apiOrderConfirm } from '@/api/order'
import { compareWeChatVersion } from '@/utils/tools'
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
                }
            }
        }
    },

    methods: {
        // 获取订单状态按钮组
        getOrderStatusButtons(buttonStatus) {
            let buttons = []

            // 删除
            buttonStatus.del_btn && buttons.push(this.ButtonsMap['delete'])
            // 取消
            buttonStatus.cancel_btn && buttons.push(this.ButtonsMap['close'])
            // 物流
            buttonStatus.delivery_btn && buttons.push(this.ButtonsMap['express'])
            // 收货
            buttonStatus.confirm_btn && buttons.push(this.ButtonsMap['succeed'])
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
                case 'express':
                    return this.handleExpress(orderID)
                case 'close':
                    return this.handleClose(orderID)
                case 'delete':
                    return this.handleDelete(orderID)
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
                    from: 'integral',
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
                    success({ extraData }) {
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
            })
        },
        //查询是否收货成功
        querycomfirmReceive(id) {
            return new Promise((resolve, reject) => {
                getwechatSyncCheck({ id })
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
                        res = await getwechatSyncCheck({ id: orderID })
                    } catch (error) {
                        uni.showModal({
                            title: '温馨提示',
                            content: '是否确认收货?',
                            confirmColor: this.themeColor,
                            success: ({ confirm }) => {
                                if (!confirm) return
                                // uni.showLoading()
                                // 取消订单
                                apiOrderConfirm({
                                    id: orderID
                                })
                                    .then((data) => {
                                        this.$toast({ title: '收货成功' })
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
                        const { transaction_id } = await getwxReceiveDetail({
                            order_id: orderID
                        })
                        await this.comfirmReceive(transaction_id)
                        await this.querycomfirmReceive(orderID)
                        await apiConfirmIntegralOrder({ id: orderID })
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
                        success: ({ confirm }) => {
                            if (!confirm) return
                            // uni.showLoading()
                            // 取消订单
                            apiConfirmIntegralOrder({
                                id: orderID
                            })
                                .then((data) => {
                                    this.$toast({ title: '收货成功' })
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
                    success: ({ confirm }) => {
                        if (!confirm) return
                        // uni.showLoading()
                        // 取消订单
                        apiConfirmIntegralOrder({
                            id: orderID
                        })
                            .then((data) => {
                                this.$toast({ title: '收货成功' })
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
                success: ({ confirm }) => {
                    if (!confirm) return
                    // uni.showLoading()
                    // 取消订单
                    apiConfirmIntegralOrder({
                        id: orderID
                    })
                        .then((data) => {
                            this.$toast({ title: '收货成功' })
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

        // 处理：查看物流
        handleExpress(orderID) {
            this.$Router.push({
                path: '/bundle/pages/exchange_logistics/exchange_logistics',
                query: { order_id: orderID, type: 'integral' }
            })
        },

        // 处理：关闭订单
        handleClose(orderID) {
            uni.showModal({
                title: '温馨提示',
                content: '是否取消订单?',
                confirmColor: this.themeColor,
                success: ({ confirm }) => {
                    if (!confirm) return
                    // uni.showLoading()
                    // 取消订单
                    apiCancelIntegralOrder({
                        id: orderID
                    })
                        .then((data) => {
                            this.$toast({ title: '取消成功' })
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
                success: ({ confirm }) => {
                    if (!confirm) return
                    // uni.showLoading()
                    // 取消订单
                    apiDelIntegralOrder({
                        id: orderID
                    })
                        .then((data) => {
                            this.$toast({ title: '删除成功' })
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
    }
}

export default OrderMixin
