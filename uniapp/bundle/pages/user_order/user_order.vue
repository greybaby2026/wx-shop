<template>
    <view class="order-list" :class="themeName">
        <!-- Tabs -->
        <!-- #ifndef H5 -->
        <navbar title="订单列表"></navbar>
        <!-- #endif -->
        <u-tabs
            :list="tabsList"
            :is-scroll="false"
            :height="80"
            :active-color="themeColor"
            :bar-style="{ top: '100%' }"
            :current="tabsIndex"
            @change="changeCurrentTab"
        />

        <!-- Order Lists -->
        <view class="order-item" v-if="isLogin">
            <mescroll-uni
                ref="mescrollRef"
                :fixed="false"
                :up="{
                    auto: false,
                    noMoreSize: 3,
                    empty: {
                        icon: '/static/images/empty/order.png',
                        tip: '暂无订单~',
                        fixed: true
                    }
                }"
                :down="{ auto: false }"
                @init="mescrollInit"
                @down="downCallback"
                @up="upCallback"
            >
                <block v-for="(tabItem, tabIndex) in tabsList" :key="tabItem['sign']">
                    <view class="order-contain" v-if="currentTab['sign'] === tabItem['sign']">
                        <view
                            class="order"
                            v-for="orderItem in tabsList[tabIndex].list"
                            :key="orderItem.id"
                        >
                            <!-- Order Header -->
                            <view class="order-header">
                                <!-- S 订单Tag -->
                                <template v-if="orderItem.delivery_type === 2">
                                    <view class="order-tag order-tag--green">自提</view>
                                </template>
                                <template v-if="orderItem.delivery_type === 4">
                                    <view class="order-tag order-tag--blue">虚拟</view>
                                </template>

                                <!-- E 订单Tag -->

                                <view class="order-sn">订单编号:{{ orderItem.sn }}</view>
                                <template
                                    v-if="
                                        orderItem.delivery_type === 1 ||
                                        orderItem.delivery_type === 4
                                    "
                                >
                                    <view
                                        :class="[
                                            'order-status',
                                            `order-status--${
                                                orderStatus[orderItem.order_status]['style']
                                            }`
                                        ]"
                                        >{{
                                            orderItem.order_status == 0 && orderItem.pay_way == 5
                                                ? '线下支付'
                                                : orderStatus[orderItem.order_status]['name']
                                        }}
                                    </view>
                                </template>
                                <template v-else-if="orderItem.delivery_type === 2">
                                    <view
                                        :class="[
                                            'order-status',
                                            `order-status--${
                                                orderSelffetchStatus[orderItem.order_status][
                                                    'style'
                                                ]
                                            }`
                                        ]"
                                        >{{
                                            orderItem.order_status == 0 && orderItem.pay_way == 5
                                                ? '线下支付'
                                                : orderSelffetchStatus[orderItem.order_status][
                                                      'name'
                                                  ]
                                        }}</view
                                    >
                                </template>
                            </view>

                            <!-- Order Main -->
                            <view class="order-main" @tap="goOrderDetail(orderItem.id)">
                                <goods-card
                                    v-for="(goodsItem, goodsIndex) in orderItem.order_goods"
                                    :key="goodsIndex"
                                    shape="rectangle"
                                    :name="goodsItem.goods_name"
                                    :image="goodsItem.goods_image"
                                    :contain-style="{ 'border-radius': 0, height: '230rpx' }"
                                    :image-style="{ width: '180rpx', height: '180rpx' }"
                                >
                                    <view slot="name" class="flex">
                                        <template
                                            v-if="[1, 2, 3, 5].includes(orderItem.order_type)"
                                        >
                                            <view
                                                style="width: 80rpx; text-align: center"
                                                :class="[
                                                    'order-tag',
                                                    `order-tag--${
                                                        orderType[orderItem.order_type]['style']
                                                    }`
                                                ]"
                                                >{{ orderType[orderItem.order_type]['name'] }}
                                            </view>
                                            <text class="goods-name line" style="width: 100%">
                                                {{ goodsItem.goods_name }}
                                            </text>
                                        </template>
                                        <view class="goods-name line" v-else>
                                            {{ goodsItem.goods_name }}
                                        </view>
                                    </view>
                                    <view class="flex-1" slot="default">
                                        <text class="muted xs m-t-10 skuline">{{
                                            goodsItem.spec_value_str
                                        }}</text>
                                        <view class="m-t-10 flex row-between">
                                            <price
                                                v-if="
                                                    orderItem.order_type == 0 ||
                                                    orderItem.order_type == 4
                                                "
                                                :content="goodsItem.original_price"
                                                mainSize="32rpx"
                                                minorSize="24rpx"
                                            />
                                            <price
                                                v-else
                                                :content="goodsItem.goods_price"
                                                mainSize="32rpx"
                                                minorSize="24rpx"
                                            />
                                            <text class="lighter sm"
                                                >x{{ goodsItem.goods_num }}</text
                                            >
                                        </view>
                                    </view>
                                </goods-card>

                                <view
                                    class="m-20 p-20"
                                    style="background-color: #f6f6f6; border-radius: 5px"
                                    v-if="orderstatusText(orderItem)"
                                    ><span class="m-r-24">{{ orderstatusTitle(orderItem) }}</span
                                    ><span class="muted">
                                        <u-count-down
                                        v-if="orderItem.cancel_unpaid_orders_time"
                            :timestamp="getCountDownSegment(orderItem.cancel_unpaid_orders_time)"
                            :show-days="false"
                            :show-hours="getCountDownSegment(orderItem.cancel_unpaid_orders_time) >= 3600 ? true : false"
                            :font-size="26"
                            :separator-size="26"
                            color="#999"
                            separator-color="#999"
                            bg-color="unset"
                            separator="zh"
                            @end="showCountDown = false"
                        />{{ orderstatusText(orderItem) }} </span>
                                </view>

                                <view class="order-desc">
                                    <text>共{{ orderItem.total_num }}件商品,应付款:</text>
                                    <price class="normal lg" :content="orderItem.order_amount" />
                                </view>
                            </view>

                            <!-- Order Footer -->
                            <view class="order-footer" v-if="orderItem.buttons.length">
                                <block
                                    v-for="buttonItem in orderItem.buttons"
                                    :key="buttonItem.event"
                                >
                                    <view
                                        :class="[
                                            'order-button',
                                            `order-button--${buttonItem.style}`
                                        ]"
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
                </block>
            </mescroll-uni>
        </view>
        <button class="login" v-else @click="handleLogin">立即登录</button>
    </view>
</template>

<script>
import { apiOrderList } from '@/api/order'
import OrderMixin from '@/mixins/order'
import { PaymentStatusEnum } from '@/utils/enum'
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins'
import { mapGetters } from 'vuex'
import Cache from '@/utils/cache'

export default {
    name: 'OrderList',
    mixins: [OrderMixin, MescrollMixin],

    data() {
        return {
            // Tabs列表
            tabsList: [
                {
                    sign: 'all',
                    name: '全部',
                    list: []
                },
                {
                    sign: 'pay',
                    name: '待付款',
                    list: []
                },
                {
                    sign: 'delivery',
                    name: '待发货',
                    list: []
                },
                {
                    sign: 'take',
                    name: '待收货',
                    list: []
                },
                {
                    sign: 'finish',
                    name: '已完成',
                    list: []
                },
                {
                    sign: 'close',
                    name: '已关闭',
                    list: []
                }
            ],
            tabsIndex: '', // Tabs索引

            // 订单状态
            orderStatus: {
                0: {
                    name: '待付款',
                    style: 'primary'
                },
                1: {
                    name: '待发货',
                    style: 'primary'
                },
                2: {
                    name: '待收货',
                    style: 'primary'
                },
                3: {
                    name: '已完成',
                    style: 'primary'
                },
                4: {
                    name: '已关闭',
                    style: 'muted'
                }
            },

            // 订单状态
            orderSelffetchStatus: {
                0: {
                    name: '待付款',
                    style: 'primary'
                },
                1: {
                    name: '待取货',
                    style: 'primary'
                },
                2: {
                    name: '待收货',
                    style: 'primary'
                },
                3: {
                    name: '已完成',
                    style: 'primary'
                },
                4: {
                    name: '已关闭',
                    style: 'muted'
                }
            },

            // 订单类型
            orderType: {
                1: {
                    name: '拼团',
                    style: 'pink'
                },
                2: {
                    name: '秒杀',
                    style: 'red'
                },
                3: {
                    name: '砍价',
                    style: 'blue'
                },
                5: {
                    name: '预售',
                    style: 'purple'
                }
            }
        }
    },

    computed: {
        // 当前Tab项
        currentTab() {
            return this.tabsList[this.tabsIndex] || {}
        },

        // 映射Tabs项
        mapTabsItem() {
            return (sign) => this.tabsList.find((item) => item['sign'] === sign)
        }
    },

    methods: {
          // 获取倒计时段
          getCountDownSegment(endTimestamp) {
            const startTimestamp = new Date().getTime() / 1000
            if (endTimestamp - startTimestamp <= 0) this.showCountDown = false
            return endTimestamp - startTimestamp
        },
        ...mapGetters(['isLogin']),
        orderstatusTitle(orderItem) {
            switch (orderItem.order_status) {
                case 0:
                    if (orderItem.pay_way == 5) {
                        return '线下付款'
                    } else {
                        return '待支付'
                    }
                case 1:
                    if (orderItem.order_type == 1) {
                        return '拼团中'
                    } else if (orderItem.delivery_type == 2) {
                        return '待取货'
                    } else {
                        return '待发货'
                    }
                case 2:
                    return '待收货'
                default:
                    return ''
            }
        },
        orderstatusText(orderItem) {
            switch (orderItem.order_status) {
                case 0:
                    if (orderItem.pay_way == 5) {
                        return '如已付款，请通知商家【确认收款】'
                    } else {
                        return '自动关闭'
                    }
                case 1:
                    if (orderItem.order_type == 1 && orderItem.is_team_success == 0) {
                        return '拼团成功后发货'
                    } else if (orderItem.delivery_type == 2) {
                        return '请前往指定门店取货'
                    } else if (orderItem.order_type == 5) {
                        return orderItem.presell.order_send_text
                    } else {
                        return '商品准备中'
                    }
                case 2:
                    return '待确认收货'
                default:
                    return ''
            }
        },
        handleLogin() {
            Cache.set('back_url', '/bundle/pages/user_order/user_order')
            this.$Router.replace({
                path: '/pages/login/login'
            })
        },
        // 更改当前Tab页
        changeCurrentTab(index) {
            // if (!this.isLogin) {
            //     console.log(123456)
            //     return
            // }
            this.tabsIndex = index
            this.refreshOrderData()
        },

        // 刷新订单数据 @Mixins【/src/mixins/order.js】内调用，请勿删除
        refreshOrderData() {
            this.$nextTick(() => {
                this.mescroll.resetUpScroll()
            })
        },

        // 上拉加载更多
        upCallback({ num, size }) {
            const sign = this.currentTab.sign
            apiOrderList({
                type: sign,
                page_no: num,
                page_size: size
            })
                .then(({ lists, page_size, count }) => {
                    const tab = this.mapTabsItem(sign)
                    lists.forEach(
                        (item) => (item['buttons'] = this.getOrderStatusButtons(item.btn) ?? [])
                    )
                    if (num === 1) tab.list = []
                    tab.list = [...tab.list, ...lists]
                    this.mescroll.endSuccess(lists.length, count)
                })
                .catch((err) => {
                    this.mescroll.endErr()
                })
        },

        // 去订单详情
        goOrderDetail(id) {
            this.$Router.push({
                path: '/bundle/pages/order_detail/order_detail',
                query: { order_id: id }
            })
        }
    },

    onLoad() {
        const options = this.$Route.query
        const mode = options.mode ?? 'all'
        const tabIndex = this.tabsList.findIndex((item) => item['sign'] === mode)
        if (!this.isLogin) {
            return
        }
        this.changeCurrentTab(tabIndex)
        setTimeout(() => {}, 0.5 * 1000)

        // 监听全局duringPayment事件
        // uni.$on('duringPayment', ({ result }) => {
        //     if (result === PaymentStatusEnum['SUCCESS']) {
        //         this.$Router.back()
        //         setTimeout(() => {
        //             this.$toast({ title: '支付成功' })
        //         }, 0.5 * 1000)
        //         this.refreshOrderData()
        //     }
        // })
    },
    onUnload() {
        // uni.$off('duringPayment')
    }
}
</script>

<style lang="scss">
page {
    height: 100%;
    padding: 0;
}
.order-list {
    height: 100%;
    display: flex;
    flex-direction: column;
    .order-item {
        flex: 1;
        min-height: 0;
    }
}
.order-contain {
    padding: 0 20rpx;
}
.login {
    margin: 400rpx auto;
    padding: 10rpx 20rpx;
    color: white;
    width: 200rpx;
    @include background_color();
}

.order {
    padding-left: 20rpx;
    margin-top: 20rpx;
    border-radius: 5px;
    background-color: #ffffff;

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
        .order-desc {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 20rpx;
            font-size: $-font-size-xs;
            color: $-color-muted;
        }
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
}
.order-tag {
    padding: 4rpx 10rpx;
    margin-right: 10rpx;
    border-radius: 2px;
    font-size: $-font-size-xxs;
    color: #ffffff;

    &--pink {
        background: linear-gradient(#ff369a 0%, #ff2c3c 100%);
    }

    &--red {
        background: linear-gradient(#f95f2f 0%, #ff2c3c 100%);
    }

    &--blue {
        background: linear-gradient(#fa6aa9 0%, #fc00bb 100%);
    }

    &--green {
        background: linear-gradient(#0cc21e 0%, #06c160 100%);
    }
    &--purple {
        background: linear-gradient(#a411d1 0%, #a411d1 100%);
    }
}
.goods-name {
    font-size: $-font-size-nr;
    color: #333333;
    overflow: hidden;
}
</style>
