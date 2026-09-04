<template>
    <view :class="themeName">
        <view class="integral-settlement">
            <view class="settlement-main">
                <router-link
                    :to="{
                        path: '/bundle/pages/address/address',
                        query: {
                            type: true
                        }
                    }"
                >
                    <view class="contain">
                        <address-card
                            :action="true"
                            :has-content="JSON.stringify(address) !== '[]'"
                        >
                            <template slot="header">
                                <text>{{ address.contact }}</text>
                                <text class="m-l-10">{{ address.mobile }}</text>
                            </template>
                            <template slot="main">
                                <text>{{ address.province }}</text>
                                <text class="m-l-10">{{ address.city }}</text>
                                <text class="m-l-10">{{ address.district }}</text>
                                <text class="m-l-10">{{ address.address }}</text>
                            </template>
                        </address-card>
                    </view>
                </router-link>

                <!-- 	<router-link to="/bundle/pages/user_address/user_address?type=1">
					<view class="address flex contain">
						<image class="icon-md m-r-20" src="/static/images/icon_address.png"></image>
						<view class="flex-1 m-r-20">
							<view class="black md" v-if="!address.contact">设置收货地址</view>
							<view v-else>
								<text class="name md m-r-10">{{address.contact}}</text>
								<text class="phone md">{{address.telephone}}</text>
								<view class="area sm m-t-10 lighter">
									{{address.province}} {{address.city}} {{address.district}} {{address.address}}
								</view>
							</view>
						</view>
						<u-icon name="arrow-right"></u-icon>
					</view>
				</router-link> -->
                <view class="order-goods contain">
                    <view class="flex goods">
                        <u-image
                            :src="goods.image"
                            border-radius="10"
                            width="160"
                            height="160"
                        ></u-image>
                        <view class="goods-info flex-1 m-l-20">
                            <view class="goods-name line-2">{{ goods.name }}</view>
                            <view class="flex row-between">
                                <view class="goods-price m-t-10">
                                    <price
                                        :content="goods.need_integral"
                                        mainSize="36rpx"
                                        minorSize="24rpx"
                                        :color="themeColor"
                                        :prefix="null"
                                        suffix="积分"
                                    />
                                    <price
                                        v-if="goods.exchange_way === 2"
                                        :content="goods.need_money"
                                        mainSize="36rpx"
                                        minorSize="24rpx"
                                        :color="themeColor"
                                        prefix="+"
                                        suffix="元"
                                    />
                                </view>
                                <view class="lighter">×{{ orderInfo.total_num }}</view>
                            </view>
                        </view>
                    </view>
                    <view class="buyer-message">
                        <u-field
                            v-model="orderFrom.user_remark"
                            type="textarea"
                            :border-bottom="false"
                            :auto-height="false"
                            label="买家留言"
                            placeholder="请添加备注（150字以内）"
                            maxlength="150"
                            :field-style="{ height: '240rpx' }"
                        >
                        </u-field>
                    </view>
                </view>
                <view class="total-goods contain">
                    <view class="flex row-between">
                        <view>商品总额</view>
                        <view>
                            <price
                                :content="orderInfo.order_integral"
                                mainSize="36rpx"
                                minorSize="24rpx"
                                :color="themeColor"
                                :prefix="null"
                                suffix="积分"
                            />
                            <price
                                v-if="orderInfo.exchange_way === 2"
                                :content="orderInfo.goods_price"
                                mainSize="36rpx"
                                minorSize="24rpx"
                                :color="themeColor"
                                prefix="+"
                                suffix="元"
                            />
                        </view>
                    </view>
                    <view class="flex row-between m-t-20">
                        <view>运费</view>
                        <view>
                            <price
                                :content="orderInfo.express_price"
                                mainSize="28rpx"
                                minorSize="24rpx"
                            />
                        </view>
                    </view>
                </view>
            </view>
            <view class="settlement-footer bg-white flex row-between">
                <view class="all-price lg flex">
                    <text>合计：</text>
                    <view>
                        <price
                            :content="orderInfo.order_integral"
                            mainSize="36rpx"
                            minorSize="24rpx"
                            :color="themeColor"
                            :prefix="null"
                            suffix="积分"
                        />
                        <price
                            v-if="orderInfo.order_amount > 0"
                            :content="orderInfo.order_amount"
                            mainSize="36rpx"
                            minorSize="24rpx"
                            :color="themeColor"
                            prefix="+"
                            suffix="元"
                        />
                    </view>
                </view>
                <button class="br60 submit-btn" size="md" hover-class="none" @tap="onSubmitOrder">
                    提交订单
                </button>
            </view>
            <!-- 页面状态 -->
            <page-status :status="pageStatus">
                <template #error>
                    <u-empty
                        :text="pageErrorMsg"
                        src="/static/images/empty/order.png"
                        :icon-size="280"
                    />
                </template>
            </page-status>
        </view>
    </view>
</template>

<script>
import { apiIntegralSettlement, apiIntegralSubmitOrder } from '@/api/integral_mall'
import { PageStatusEnum, PaymentStatusEnum } from '@/utils/enum'
export default {
    data() {
        return {
            pageStatus: PageStatusEnum['LOADING'],
            pageErrorMsg: '', // 页面异常信息
            address: {},
            orderFrom: {}, // 承载页面参数
            goods: {},
            orderInfo: {}
        }
    },
    methods: {
        // 订单信息
        // 根据商品类型初始化订单数据
        getOrderInfo() {
            return new Promise((resolve, reject) => {
                apiIntegralSettlement(this.orderFrom)
                    .then((data) => {
                        const { address, goods } = data
                        this.address = address
                        this.orderInfo = data
                        this.goods = goods
                        resolve(data)
                    })
                    .catch((errMsg) => reject(errMsg))
            })
        },
        // 点击提交订单
        onSubmitOrder() {
            uni.showModal({
                title: '温馨提示',
                content: '是否确认下单?',
                confirmColor: this.themeColor,
                success: async ({ confirm }) => {
                    if (!confirm) return
                    try {
                        // 下单处理
                        this.orderBuy()
                    } catch (errMsg) {
                        if (errMsg == '自提门店不能为空') {
                            this.$toast({ title: '请选择自提门店' })
                        }
                        console.log('ORDER_ERROR_MSG:', errMsg)
                    }
                }
            })
        },
        // 提交订单
        orderBuy() {
            return new Promise((resolve, reject) => {
                this.orderFrom.address_id = this.address.id
                apiIntegralSubmitOrder(this.orderFrom)
                    .then((res) => {
                        // 监听全局duringPayment事件
                        // uni.$once("duringPayment", (data) => {
                        //   console.log(data);
                        //   this.handlePayResult(data);
                        // });
                        return res
                    })
                    .then(({ type, order_id }) => {
                        // 去支付
                        if (this.orderInfo.need_pay) {
                            this.$Router.replace({
                                path: `/bundle/pages/payment/payment`,
                                query: {
                                    from: type,
                                    order_id
                                }
                            })
                        } else {
                            this.handlePayResult({
                                result: 1,
                                order_id
                            })
                        }
                        resolve(order_id)
                    })
                    .catch((errMsg) => reject(errMsg))
            })
        },
        handlePayResult({ result, order_id }) {
            switch (result) {
                case PaymentStatusEnum['SUCCESS']:
                case PaymentStatusEnum['FAIL']:
                    this.$Router.replace({
                        path: '/bundle/pages/payment_result/payment_result',
                        query: {
                            order_id,
                            from: 'integral'
                        }
                    })
                    break
                case PaymentStatusEnum['CLOSE']:
                    this.$Router.push({
                        path: '/bundle/pages/payment_result/payment_result',
                        query: {
                            order_id,
                            from: 'integral'
                        }
                    })
                    break
            }
        }
    },
    onLoad(options) {
        // 初始化页面参数
        this.orderFrom = this.$Route.query
        uni.$on('changeAddress', (id) => {
            console.log(id)
            if (!id) return
            this.orderFrom.address_id = id
        })
    },
    onUnload() {
        uni.$off('changeAddress')
    },
    async onShow() {
        console.log(this.orderFrom)
        try {
            // 初始化页面数据
            await this.getOrderInfo()
            this.pageStatus = PageStatusEnum['NORMAL']
        } catch (errMsg) {
            this.pageErrorMsg = errMsg
            this.pageStatus = PageStatusEnum['ERROR']
        }
    }
}
</script>

<style lang="scss">
.integral-settlement {
    overflow: hidden;
    padding-bottom: calc(120rpx + env(safe-area-inset-bottom));

    .contain {
        background-color: #fff;
        border-radius: 14rpx;
        margin: 20rpx 20rpx 0;
        overflow: hidden;
    }

    .settlement-main {
        .address {
            min-height: 164rpx;
            padding: 20rpx 24rpx;
        }

        .order-goods {
            .goods {
                padding: 30rpx 24rpx;
                border-bottom: $-solid-border;

                .goods-info {
                    .goods-name {
                        line-height: 40rpx;
                        height: 80rpx;
                    }
                }
            }

            .buyer-message {
            }
        }

        .total-goods {
            padding: 20rpx 24rpx;
        }
    }

    .settlement-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 100rpx;
        padding: 0 30rpx;
        box-sizing: content-box;
        padding-bottom: env(safe-area-inset-bottom);

        .submit-btn {
            @include background_color();
            color: #fff;
        }
    }
}
</style>
