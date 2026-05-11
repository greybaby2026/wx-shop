<template>
    <view class="order-list" :class="themeName">
        <!-- Tabs -->
        <u-tabs
            :list="tabsList"
            :is-scroll="true"
            :height="80"
            :active-color="themeColor"
            :current="tabsIndex"
            @change="changeCurrentTab"
        />

        <!-- Order Lists -->
        <mescroll-contain
            ref="mescrollContain"
            top="90rpx"
            :up-option="{
                noMoreSize: 3,
                empty: {
                    icon: '/static/images/empty/order.png',
                    tip: '暂无订单~',
                    fixed: true
                }
            }"
            @upcallback="upCallback($event)"
        >
            <view class="order-contain">
                <view class="order" v-for="orderItem in orderLists" :key="orderItem.id">
                    <!-- Order Header -->
                    <view class="order-header">
                        <view class="flex-1 muted">
                            {{ orderItem.create_time }}
                        </view>
                        <view class="order-status--primary">
                            {{orderItem.pay_way==5 && orderItem.order_status==0?'线下支付':orderItem.order_status_desc }}
                        </view>
                    </view>

                    <!-- Order Main -->
                    <view class="order-main" @tap="goOrderDetail(orderItem.id)">
                        <view class="order-goods flex">
                            <u-image
                                :src="orderItem.goods.image"
                                border-radius="10"
                                width="160"
                                height="160"
                            ></u-image>
                            <view class="goods-info flex-1 m-l-20">
                                <view class="goods-name line-2">{{ orderItem.goods.name }}</view>
                                <view class="flex row-between">
                                    <view class="goods-price m-t-10">
                                        <price
                                            :content="orderItem.goods.need_integral"
                                            mainSize="36rpx"
                                            minorSize="24rpx"
                                            :color="themeColor"
                                            :prefix="null"
                                            suffix="积分"
                                        />
                                        <price
                                            v-if="orderItem.goods.exchange_way === 2"
                                            :content="orderItem.goods.need_money"
                                            mainSize="36rpx"
                                            minorSize="24rpx"
                                            :color="themeColor"
                                            prefix="+"
                                            suffix="元"
                                        />
                                    </view>
                                    <view class="lighter">×{{ orderItem.total_num }}</view>
                                </view>
                            </view>
                        </view>
						<view class="offline-payment" v-if="orderItem.pay_way==5 && orderItem.order_status!=3">
					       <text>线下付款</text>
						   <text>{{orderItem.order_status==0?'如已付款，请通知商家【确认付款】':orderItem.order_status==1?'商品准备中':orderItem.order_status==2?'待确认收货':''}}</text>
						</view>
                        <view class="all-price flex row-right">
                            <text class="muted xs"
                                >共{{ orderItem.total_num }}件商品，实付款：</text
                            >
                            <view class="">
                                <price
                                    :content="orderItem.order_integral"
                                    mainSize="36rpx"
                                    minorSize="24rpx"
                                    :prefix="null"
                                    suffix="积分"
                                />
                                <price
                                    v-if="orderItem.order_amount > 0"
                                    :content="orderItem.order_amount"
                                    mainSize="36rpx"
                                    minorSize="24rpx"
                                    prefix="+"
                                    suffix="元"
                                />
                            </view>
                        </view>
                    </view>

                    <!-- Order Footer -->
                    <view class="order-footer" v-if="orderItem.buttons.length">
                        <block v-for="buttonItem in orderItem.buttons" :key="buttonItem.event">
                            <view
                                :class="['order-button', `order-button--${buttonItem.style}`]"
                                @tap="
                                    onOrderButtons(
                                        buttonItem['event'],
                                        orderItem.id,
                                        orderItem.pay_way
                                    )
                                "
                                >{{ buttonItem.name }}</view
                            >
                        </block>
                    </view>
                </view>
            </view>
        </mescroll-contain>
    </view>
</template>

<script>
import { apiIntegralOrder } from '@/api/integral_mall'
import OrderMixin from '@/mixins/integral_order'
import { PaymentStatusEnum } from '@/utils/enum'

export default {
    name: 'OrderList',
    mixins: [OrderMixin],

    data() {
        return {
            // Tabs列表
            tabsList: [
                {
                    sign: '',
                    name: '全部'
                },
                {
                    sign: 0,
                    name: '待付款'
                },
                {
                    sign: 1,
                    name: '待发货'
                },
                {
                    sign: 2,
                    name: '待收货'
                },
                {
                    sign: 3,
                    name: '已完成'
                },
                {
                    sign: 4,
                    name: '已关闭'
                }
            ],
            tabsIndex: 0, // Tabs索引
            orderLists: []
        }
    },

    computed: {
        // 当前Tab项
        currentTab() {
            return this.tabsList[this.tabsIndex]
        }
    },

    methods: {
        // 更改当前Tab页
        changeCurrentTab(index) {
            this.tabsIndex = index
            this.refreshOrderData()
        },

        // 刷新订单数据 @Mixins【/src/mixins/order.js】内调用，请勿删除
        refreshOrderData() {
            const ref = this.$refs.mescrollContain
            ref.downCallback()
        },

        // 上拉加载更多
        upCallback({ num, size }) {
            const ref = this.$refs.mescrollContain
            const sign = this.currentTab.sign
            apiIntegralOrder({
                type: sign,
                page_no: num,
                page_size: size
            })
                .then(({ lists, page_size, count }) => {
                    lists.forEach(
                        (item) => (item['buttons'] = this.getOrderStatusButtons(item.btns) ?? [])
                    )
                    if (num === 1) this.orderLists = []
                    this.orderLists = [...this.orderLists, ...lists]
                    ref.endSuccess(lists.length, count)
                })
                .catch((err) => {
                    ref.endBySize()
                })
        },

        // 去订单详情
        goOrderDetail(id) {
            this.$Router.push({
                path: '/bundle/pages/exchange_order_details/exchange_order_details',
                query: {
                    order_id: id
                }
            })
        },
        handlePayRes({ result }) {
            if (result === PaymentStatusEnum['SUCCESS']) {
                this.$Router.back()
                setTimeout(() => {
                    this.$toast({
                        title: '支付成功'
                    })
                }, 0.5 * 1000)
                this.refreshOrderData()
            }
        }
    },

    onLoad() {
        // 监听全局duringPayment事件
        this.handlePayRes = this.handlePayRes.bind(this)
        // uni.$on("duringPayment", this.handlePayRes);
    },

    onUnload() {
        // uni.$off("duringPayment", this.handlePayRes);
    }
}
</script>

<style lang="scss" scoped>
.order-contain {
    padding: 20rpx 20rpx 0;
}

.order {
    padding-left: 20rpx;
    border-radius: 5px;
    background-color: #ffffff;

    &:not(:last-of-type) {
        margin-bottom: 20rpx;
    }

    &-header {
        display: flex;
        height: 80rpx;
        align-items: center;
        padding-right: 20rpx;
        border-bottom: $-solid-border;

        .order-sn {
            font-size: $-font-size-nr;
        }

        .order-status {
            margin-left: auto;
            font-size: $-font-size-sm;

            &--primary {
                @include font_color();
            }

            &--muted {
                color: $-color-muted;
            }
        }
    }

    &-main {
        padding: 20rpx;
    }

    &-footer {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        height: 100rpx;
        padding-right: 20rpx;
        border-top: $-solid-border;

        .order-button {
            box-sizing: border-box;
            padding: 10rpx 30rpx;
            border-radius: 60px;
            font-size: $-font-size-nr;

            &:nth-child(n + 2) {
                margin-left: 20rpx;
            }

            &--primary {
                @include background_color(); color: #ffffff;
            }

            &--normal {
                border-width: 1px;
                border-style: solid;
                @include border_color(); @include font_color();
            }

            &--muted {
                border: $-solid-border;
                color: $-color-lighter;
            }
        }
    }
}
.offline-payment{
	padding: 20rpx 30rpx;
	background-color: #F6F6F6;
	border-radius: 5rpx;	
	margin: 30rpx 0;
	>text:nth-child(1){
	
		margin-right: 20rpx;
	}
	>text:nth-child(2){
		color: #999999;
	}
}
</style>
