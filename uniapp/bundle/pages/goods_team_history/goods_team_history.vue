<template>
    <view class="order-list" :class="themeName">
        <!-- Tabs -->
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
        <block v-for="(tabItem, tabIndex) in tabsList" :key="tabItem['sign']">
            <mescroll-contain
                v-if="currentTab['sign'] === tabItem['sign']"
                top="80rpx"
                :ref="'scroll' + '-' + tabItem['sign']"
                :up-option="{
                    noMoreSize: 3,
                    empty: {
                        icon: '/static/images/empty/order.png',
                        tip: '暂无订单~',
                        fixed: true
                    }
                }"
                @upcallback="upCallback($event, tabItem['sign'])"
            >
                <view class="order-contain">
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
                            <!-- E 订单Tag -->

                            <!-- 拼团中 -->
                            <template v-if="orderItem.status === 0">
                                <view class="order-sn">
                                    <text class="m-r-10">距离结束</text>
                                    <count-down :time-end="orderItem.invalid_time" />
                                </view>
                            </template>
                            <!-- 拼团成功 -->
                            <template v-if="orderItem.status === 1">
                                <view class="order-sn">{{ orderItem.team_end_time }}</view>
                            </template>
                            <!-- 拼团失败 -->
                            <template v-if="orderItem.status === 2">
                                <view class="order-sn">{{ orderItem.team_end_time }}</view>
                            </template>

                            <!-- 拼团状态 -->
                            <view
                                :class="[
                                    'order-status',
                                    `order-status--${orderStatus[orderItem.status]['style']}`
                                ]"
                                >{{ orderStatus[orderItem.status]['name'] }}</view
                            >
                        </view>

                        <!-- Order Main -->
                        <view class="order-main" @tap="goOrderDetail(orderItem.order_id)">
                            <!-- Leader -->
                            <template v-if="orderItem.identity == 1">
                                <view class="leader-icon">团长</view>
                            </template>
                            <!-- Goods -->
                            <goods-card
                                shape="rectangle"
                                :image="orderItem.image"
                                :contain-style="{ 'border-raius': 0, height: '230rpx' }"
                                :image-style="{ width: '180rpx', height: '180rpx' }"
                            >
                                <view slot="name">
                                    <u-tag
                                        class="m-r-10"
                                        :text="orderItem.people_num + '人团'"
                                        mode="plain"
                                        size="mini"
                                        :color="themeColor"
                                    />
                                    <text>{{ orderItem.name }}</text>
                                </view>
                                <view class="flex-1 flex-col" slot="default">
                                    <text class="muted xs m-t-20 skuline">{{
                                        orderItem.spec_value_str
                                    }}</text>
                                    <view class="m-t-10 flex row-between">
                                        <price
                                            :content="orderItem.sell_price"
                                            mainSize="32rpx"
                                            minorSize="24rpx"
                                        />
                                        <text class="lighter sm">x{{ orderItem.count }}</text>
                                    </view>
                                </view>
                            </goods-card>
                            <view class="order-desc">
                                <text>共{{ orderItem.count }}件商品,应付款:</text>
                                <price class="normal lg" :content="orderItem.order_amount" />
                            </view>
                        </view>

                        <!-- Order Footer -->
                        <view
                            class="order-footer"
                            v-if="!orderItem.pay_status && orderItem.status === 0"
                        >
                            <view
                                class="operation operation--primary"
                                @click="handlePayment(orderItem.order_id)"
                                >去付款</view
                            >
                        </view>
                    </view>
                </view>
            </mescroll-contain>
        </block>
    </view>
</template>

<script>
import { apiGoodsTeamHistory } from '@/api/goods'
import { PaymentStatusEnum } from '@/utils/enum'

const TabsFormEnum = {
    NORMAL: '', // 全部
    DOING: 0, // 拼团中
    SUCCESS: 1, // 拼团成功
    FAIL: 2 // 拼团失败
}

export default {
    name: 'GoodsTeamHistory',

    data() {
        return {
            // Tabs列表
            tabsList: [
                {
                    sign: 'NORMAL',
                    name: '全部',
                    list: []
                },
                {
                    sign: 'DOING',
                    name: '拼团中',
                    list: []
                },
                {
                    sign: 'SUCCESS',
                    name: '拼团成功',
                    list: []
                },
                {
                    sign: 'FAIL',
                    name: '拼团失败',
                    list: []
                }
            ],
            tabsIndex: 0, // Tabs索引

            // 订单状态
            orderStatus: {
                0: {
                    name: '拼团中',
                    style: 'primary'
                },
                1: {
                    name: '拼团成功',
                    style: 'primary'
                },
                2: {
                    name: '拼团失败',
                    style: 'muted'
                }
            }
        }
    },

    computed: {
        // 当前Tab项
        currentTab() {
            return this.tabsList[this.tabsIndex]
        },

        // 映射Tabs项
        mapTabsItem() {
            return (sign) => this.tabsList.find((item) => item['sign'] === sign)
        }
    },

    methods: {
        // 更改当前Tab页
        changeCurrentTab(index) {
            this.tabsIndex = index
        },

        // 上拉加载更多
        upCallback({ num, size }, sign) {
            const ref = this.$refs['scroll' + '-' + sign][0]

            apiGoodsTeamHistory({
                status: TabsFormEnum[sign],
                page_no: num,
                page_size: size
            })
                .then(({ lists, page_size, count }) => {
                    const tab = this.mapTabsItem(sign)
                    if (num === 1) tab.list = []
                    tab.list = [...tab.list, ...lists]
                    ref.endSuccess(lists.length, count)
                })
                .catch((err) => {
                    ref.endBySize()
                })
        },

        // 去订单详情
        goOrderDetail(id) {
            this.$Router.push({
                path: '/pages/order_detail/order_detail',
                query: { order_id: id }
            })
        },

        // 处理：支付
        handlePayment(orderID) {
            this.$Router.push({
                path: `/pages/payment/payment`,
                query: {
                    from: 'order',
                    order_id: orderID
                }
            })
        }
    },

    onLoad() {
        // 监听全局duringPayment事件
        // uni.$on("duringPayment", ({ result }) => {
        //   if (result === PaymentStatusEnum["SUCCESS"]) {
        //     this.$Router.back();
        //     setTimeout(() => {
        //       this.$toast({ title: "支付成功" });
        //       const sign = this.currentTab["sign"];
        //       const ref = this.$refs["scroll" + "-" + sign][0];
        //       ref.downCallback();
        //     }, 0.5 * 1000);
        //   }
        // });
    },

    onUnload() {
        // uni.$off('duringPayment')s
    }
}
</script>

<style lang="scss" scoped>
.order-contain {
    padding: 0 20rpx;
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
        position: relative;

        .leader-icon {
            position: absolute;
            top: 20rpx;
            left: -20rpx;
            z-index: 9;
            padding: 4rpx 20rpx;
            border-radius: 0 60px 60px 0;
            font-size: $-font-size-xs;
            @include background_linear();
            color: #ffffff;
        }

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

        .operation {
            box-sizing: border-box;
            padding: 10rpx 30rpx;
            border-radius: 60px;
            font-size: $-font-size-nr;

            &--primary {
                @include background_color(); color: #ffffff;
            }
        }
    }
}
</style>
