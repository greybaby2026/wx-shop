<template>
    <view :class="themeName">
        <view class="order-detail">
            <!-- Order Status -->
            <view class="order-status">
                <!-- 订单状态【快递配送】 -->
                <view class="order-status-name">{{ orderAction['name'] }}</view>
                <template v-if="orderAction['desc']">
                    <view class="order-status-desc">{{ orderAction['desc'] }}</view>
                </template>
            </view>

            <!-- Address -->
            <address-card :has-content="true" border-radius="7px">
                <template slot="header">
                    <text>{{ addressInfo.contact }}</text>
                    <text class="m-l-10">{{ addressInfo.mobile }}</text>
                </template>
                <template slot="main">
                    <text>{{ orderInfo.delivery_address }}</text>
                </template>
            </address-card>
            <!-- 商品 -->
            <view class="contain order-goods flex p-20">
                <u-image :src="goods.image" border-radius="10" width="160" height="160"></u-image>
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

            <!-- 账单 -->
            <view class="contain order-bill">
                <view class="cell order-bill__item">
                    <view class="cell-label">商品金额</view>
                    <view class="cell-content">
                        <price :content="orderInfo.order_integral" :prefix="null" suffix="积分" />
                        <price
                            v-if="orderInfo.goods_price > 0"
                            :content="orderInfo.goods_price"
                            prefix="+"
                            suffix="元"
                        />
                    </view>
                </view>
                <view class="cell order-bill__item" v-if="orderInfo.express_price">
                    <view class="cell-label">运费</view>
                    <view class="cell-content">
                        <price :content="orderInfo.express_price" />
                    </view>
                </view>
                <view class="cell order-bill__item">
                    <view class="cell-content">
                        <text class="muted m-r-10">实付款:</text>
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
            </view>
            <!-- 留言 -->
            <view v-if="orderInfo.user_remark" class="contain cell remark">
                <view class="cell-label">买家留言</view>
                <view class="cell-content muted">{{ orderInfo.user_remark }}</view>
            </view>

            <!-- 订单信息 -->
            <view class="contain order-info">
                <view class="cell order-info__item">
                    <view class="cell-label">订单编号</view>
                    <view class="cell-content">
                        <view class="flex-1">{{ orderInfo.sn }}</view>
                        <view class="button--copy" @tap="handleCopy(orderInfo.sn)">复制</view>
                    </view>
                </view>
                <view class="cell order-info__item">
                    <view class="cell-label">支付方式</view>
                    <view class="cell-content">{{ getPaywayName(orderInfo.pay_way) }}</view>
                </view>
                <view class="cell order-info__item">
                    <view class="cell-label">下单时间</view>
                    <view class="cell-content">{{ orderInfo.create_time }}</view>
                </view>
                <view v-if="orderInfo.pay_time" class="cell order-info__item">
                    <view class="cell-label">付款时间</view>
                    <view class="cell-content">{{ orderInfo.pay_time }}</view>
                </view>
                <view v-if="orderInfo.express_time" class="cell order-info__item">
                    <view class="cell-label">发货时间</view>
                    <view class="cell-content">{{ orderInfo.express_time }}</view>
                </view>
                <view v-if="orderInfo.confirm_take_time" class="cell order-info__item">
                    <view class="cell-label">成交时间</view>
                    <view class="cell-content">{{ orderInfo.confirm_take_time }}</view>
                </view>
            </view>

            <!-- Order Operational -->
            <view v-if="orderButtons.length" class="operational">
                <block v-for="buttonItem in orderButtons" :key="buttonItem.event">
                    <view
                        :class="['order-button', `order-button--${buttonItem.style}`]"
                        @tap="onOrderButtons(buttonItem['event'], orderInfo.id, orderInfo.pay_way)"
                        >{{ buttonItem.name }}</view
                    >
                </block>
            </view>

            <!-- 页面状态 -->
            <page-status :status="pageStatus">
                <template #error>
                    <u-empty
                        text="订单异常"
                        src="/static/images/empty/order.png"
                        :icon-size="280"
                    ></u-empty>
                </template>
            </page-status>
        </view>
    </view>
</template>

<script>
import { apiIntegralOrderDetail } from '@/api/integral_mall'
import OrderMixin from '@/mixins/integral_order'
import { copy } from '@/utils/tools'
import { PageStatusEnum, PaymentStatusEnum, OrderTypeEnum } from '@/utils/enum'

export default {
    name: 'OrderDetail',
    mixins: [OrderMixin],

    data() {
        return {
            // 页面状态
            pageStatus: PageStatusEnum['LOADING'],
            order_id: '', // 订单ID
            orderInfo: {}, // 订单信息
            goods: {}, // 商品列表
            addressInfo: {}, // 地址信息

            orderButtons: [], // 订单按钮组

            // 订单状态
            orderStatus: {
                0: {
                    name: '等待买家付款'
                },
                1: {
                    name: '等待商家发货',
                    desc: '您的商品正在打包中，请耐心等待…'
                },
                2: {
                    name: '待收货',
                    desc: '您的商品正在路中，请耐心等待…'
                },
                3: {
                    name: '已完成',
                    desc: '商品已签收，期待再次购买！'
                },
                4: {
                    name: '订单已关闭',
                    desc: ''
                }
            }
        }
    },

    computed: {
        OrderTypeEnum: () => OrderTypeEnum,
        orderAction() {
            return this.orderStatus[this.orderInfo.order_status] || {}
        }
    },

    methods: {
        handleCopy: copy,
        // 刷新订单数据
        refreshOrderData() {
            return apiIntegralOrderDetail({
                id: this.order_id
            })
                .then((data) => {
                    this.orderInfo = data
                    this.addressInfo = data.address
                    this.goods = data.goods
                    this.orderButtons = this.getOrderStatusButtons(data.btns) ?? []
                })
                .catch((err) => {
                    return Promise.reject(err)
                })
        },

        // 获取支付方式名称
        getPaywayName(payway) {
            switch (payway) {
                case 1:
                    return '余额支付'
                case 2:
                    return '微信支付'
                case 3:
                    return '支付宝支付'
                default:
                    return '-'
            }
        },

        // 去商品详情
        goGoodsDetail(goods_id) {
            this.$Router.push({
                path: '/pages/goods_detail/goods_detail',
                query: {
                    id: goods_id
                }
            })
        },

        // 去售后
        goAfterSale(href, id) {
            this.$Router.push({
                path: href,
                query: {
                    id
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

    async onLoad() {
        const options = this.$Route.query

        try {
            if (!options.order_id) throw new Error('订单异常')
            this.order_id = options.order_id
            await this.refreshOrderData()
            this.pageStatus = PageStatusEnum['NORMAL']
        } catch (err) {
            console.log(err)
            // setTimeout(() => {
            // 	this.$Router.back()
            // }, 0.5 * 1000)
            this.pageStatus = PageStatusEnum['ERROR']
        }

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
.order-detail {
    padding: 0 20rpx calc(100rpx + 20rpx + constant(safe-area-inset-bottom)) 20rpx;
    padding: 0 20rpx calc(100rpx + 20rpx + env(safe-area-inset-bottom)) 20rpx;

    @include background_linear(); background-size: 200vw 200rpx;
    background-repeat: no-repeat;

    .contain {
        margin-top: 20rpx;
        border-radius: 7px;
        background-color: #ffffff;
    }

    // 单元格
    .cell {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: $-font-size-nr;

        &-label {
            width: 142rpx;
        }

        &-content {
            flex: 1;
            display: flex;
            align-items: center;
        }
    }
}

.order-status {
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 136rpx;
    // @include background_linear()
    color: #ffffff;

    // &::before {
    // 	z-index: 1;
    // 	position: absolute;
    // 	left: -20rpx;
    // 	right: -20rpx;
    // 	display: block;
    // 	content: "";
    // 	width: 100vw;
    // 	height: 200rpx;
    // 	@include background_linear()
    // }

    &-name {
        font-size: $-font-size-lg;
    }

    &-desc {
        font-size: $-font-size-sm;
    }
}

.order-goods {
    &__handle {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: 0 20rpx 20rpx 20rpx;

        &-button {
            padding: 8rpx 30rpx;
            border: $-solid-border;
            border-radius: 60px;
            font-size: $-font-size-sm;
        }
    }
}

.order-bill {
    padding: 20rpx;

    &__item {
        height: 60rpx;

        .cell-content {
            justify-content: flex-end;
        }
    }
}

.remark {
    padding: 24rpx 20rpx;
    align-items: flex-start !important;
}

.order-info {
    padding: 20rpx;

    &__item {
        height: 60rpx;
    }
}

.receive {
    position: relative;

    .delivery--die {
        position: absolute;
        top: 0;
        right: 30rpx;
    }

    .receive-qr {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 460rpx;
    }

    .qr-contain {
        box-sizing: border-box;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 140px;
        height: 140px;
        border: 1px solid #cccccc;
        border-radius: 5px;

        &--die {
            position: relative;

            &::before {
                position: absolute;
                z-index: 99;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                display: block;
                content: '';
                background-color: rgba(255, 255, 255, 0.5);
            }
        }
    }

    .qr-code {
        padding: 8rpx 30rpx;
        border-radius: 60px;
        background-color: #f6f6f6;
    }

    .receive-info {
        padding-left: 20rpx;

        &-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100rpx;
            padding-right: 30rpx;

            &:nth-child(n + 2) {
                border-top: $-dashed-border;
            }
        }
    }
}

.operational {
    box-sizing: border-box;
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    height: calc(100rpx + constant(safe-area-inset-bottom));
    height: calc(100rpx + env(safe-area-inset-bottom));
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 0 20rpx constant(safe-area-inset-bottom) 20rpx;
    padding: 0 20rpx env(safe-area-inset-bottom) 20rpx;
    border-top: $-solid-border;
    background-color: #ffffff;

    .order-button {
        box-sizing: border-box;
        padding: 10rpx 30rpx;
        border-radius: 60px;
        font-size: $-font-size-nr;

        &:nth-child(n + 2) {
            margin-left: 20rpx;
        }

        &--primary {
            @include background_color();
            color: #ffffff;
        }

        &--normal {
            border-width: 1px;
            border-style: solid;
            @include border_color();
            @include font_color();
        }

        &--muted {
            border: $-solid-border;
            color: $-color-lighter;
        }
    }
}

.button--copy {
    padding: 4rpx 20rpx;
    border: $-solid-border;
    border-radius: 60px;
    font-size: $-font-size-xs;
}
</style>
