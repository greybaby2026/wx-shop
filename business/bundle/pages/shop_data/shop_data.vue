<template>
    <view>
        <u-tabs :list="list" :is-scroll="false" :current="current" @change="change" active-color="#3868f9"></u-tabs>

        <view class="m-t-20" style="padding: 0 20rpx;">

            <!-- 交易数据 -->
            <template v-if="current == 0">
                <view class="normal m-t-20 m-b-20 sm">最近7天</view>
                <view class="flex row-between">
                    <view class="card">
                        <view class="xs lighter">成交订单(笔)</view>
                        <view class="normal">{{tradingInfo.order_num}}</view>
                    </view>
                    <view class="card">
                        <view class="xs lighter">营业额(元)</view>
                        <view class="primary">{{tradingInfo.order_sum}}</view>
                    </view>
                </view>
                <view class="e-data m-t-30">
                    <view class="sm normal">成交趋势图</view>

                    <view class="e-content m-t-20">
                        <charts ids="canvasColumn" width="100%" height="544rpx" :chartData="dealData"></charts>
                    </view>
                </view>
                <view class="e-data m-t-30 m-b-50">
                    <view class="e-title sm">营业额趋势图</view>

                    <view class="e-content m-t-20">
                        <charts ids="canvasColumn2" width="100%" height="544rpx" :chartData="turnoverData"></charts>
                    </view>
                </view>
            </template>

            <!-- 商品数据 -->
            <template v-if="current == 1">
                <block v-for="(item, index) in goodsInfo">
                    <view class="goods m-b-20" @click="toDetail(item.id)">
                        <u-image :src="item.image" width="160rpx" height="160rpx"></u-image>
                        <view class="m-l-16 line-2 right" style="width: 100%;">
                            <!-- 订单名称 -->
                            <view class="m-b-10 flex row-between">
                                <view class="nr black line-2">
                                    {{item.name}}
                                </view>
                            </view>
                            <!-- 商品规格 -->
                            <view class=" flex row-between">
                                <view class="muted">销量 {{item.total_num}}</view>
                                <view class="muted">销售额: <text class="black">{{item.total_pay_price}}</text></view>
                            </view>
                        </view>
                    </view>
                </block>
            </template>

            <!-- 人数数据 -->
            <template v-if="current == 2">
                <view class="flex row-between">
                    <view class="card" style="width: 100%;">
                        <view class="xs lighter">进店人数(人)</view>
                        <view class="normal">{{visitInfo.count}}</view>
                    </view>
                </view>

                <view class="e-data m-t-30">
                    <view class="sm normal">进店人数趋势图</view>

                    <view class="e-content m-t-20">
                        <charts ids="canvasColumn" width="100%" height="544rpx" :chartData="visitData"></charts>
                    </view>
                </view>
            </template>
        </view>

        <u-toast ref="uToast" />
    </view>
</template>

<script>
    import {
        mapGetters,
        mapActions
    } from 'vuex'
    import {
        apiStatisticsGoodslist, //商品
        apiStatisticsTrading, //交易
        apiStatisticsVisit //访问
    } from '@/api/store'
    export default {
        data() {
            return {
                list: [{
                    name: '交易分析'
                }, {
                    name: '商品分析'
                }, {
                    name: '访问分析'
                }],

                current: 0,

                // 交易
                tradingInfo: {},
                dealData: {}, //成交趋势
                turnoverData: {}, //营业额

                goodsInfo: [],

                // 进店
                visitInfo: {},
                visitData: {},
            }
        },

        onLoad() {
            this.getDataFunc(this.current)
        },

        methods: {
            change(index) {
                this.current = index;
                console.log(this.current)
                this.getDataFunc(index);
            },
            
            toDetail(id) {
                this.$Router.push({
                    path: '/pages/goods_detail/goods_detail',
                    query: {
                        id
                    }
                })
            },

            async getDataFunc(index) {
                if (index == 0) {
                    const res = await apiStatisticsTrading()
                    this.tradingInfo = res;
                    const turnover = {
                        categories: res.date,
                        series: [{
                            "name": res.list[0].name,
                            data: res.list[0].data
                        }]
                    }
                    const deal = {
                        categories: res.date,
                        series: [{
                            "name": res.list[1].name,
                            data: res.list[1].data
                        }]
                    }
                    this.turnoverData = turnover
                    this.dealData = deal
                } else if (index == 1) {
                    const res = await apiStatisticsGoodslist()
                    this.goodsInfo = res;
                } else if (index == 2) {
                    const res = await apiStatisticsVisit()
                    this.visitInfo = res;
                    const visit = {
                        categories: res.date,
                        series: [{
                            "name": res.list[0].name,
                            data: res.list[0].data
                        }]
                    }
                    this.visitData = visit
                }
            }
        }
    }
</script>

<style lang="scss">
    .card {
        width: 346rpx;
        padding: 30rpx 36rpx;
        border-radius: 14rpx;
        background-color: $-color-white;

        view:last-child {
            font-size: 44rpx;
            font-weight: 500;
        }

        .primary {
            color: $-color-primary;
        }
    }

    // 数据图
    .e-data {
        .e-content {
            padding: 20rpx 0;
            border-radius: 14rpx;
            background-color: $-color-white;
        }
    }

    .goods {
        padding: 20rpx;
        border-radius: 14rpx;
        background-color: $-color-white;
        display: flex;
        .right{
            flex: 1;
            padding: 0 10rpx;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
    }
</style>
