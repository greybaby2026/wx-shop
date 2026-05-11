<template>
    <view>
        <mescroll-body ref="mescrollRef" @init="mescrollInit" @down="downCallback" @up="upCallback"
            :up="{ use: false }">
            <view class="index">
                <view class="index-wrap">
                    <!-- Header -->
                    <view class="index-header flex row-between">
                        <view class="md" style="font-weight: 400">{{
                            pagesData.shop_name || "-"
                        }}</view>
                        <view class="xs">{{ time }}</view>
                    </view>

                    <!-- Section -->
                    <!-- <view style="height: 130vh"></view> -->
                    <view class="index-section">
                        <!-- Section today Data -->

                        <view class="today-data">
                            <view class="title">今日数据</view>
                            <view class="turnover">
                                <view>
                                    <view class="item-data">
                                        <view>营业额</view>
                                        <view class="primary">{{ totleAmount }}</view>
                                    </view>
                                </view>
                            </view>
                            <view class="flex">
                                <block v-for="(item, index) in todayObj" :key="index">
                                    <view class="item-data">
                                        <view>{{ item.name }}</view>
                                        <view>{{ item.val }}</view>
                                    </view>
                                </block>
                            </view>
                        </view>

                        <view class="today-data m-t-30">
                            <view class="title">待办事项</view>
                            <view class="flex">
                                <block v-for="(item, index) in orderObj" :key="index">
                                    <view class="item-data">
                                        <view>{{ item.name }}</view>
                                        <view :class="{ 'red': index == 3 }">{{ item.val }}</view>
                                    </view>
                                </block>
                            </view>
                        </view>

                        <!-- Section turnover echarts Data -->
                        <view class="e-data m-t-30 p-20">
                            <view class="title">营业额趋势图</view>

                            <view class="e-content m-t-20">
                                <charts ids="canvasColumn" width="100%" height="544rpx" :chartData="turnoverData">
                                </charts>
                            </view>
                        </view>

                        <!-- Section visit echarts Data -->
                        <view class="e-data m-t-30 p-b-60 p-20">
                            <view class="title">访问量趋势图</view>

                            <view class="e-content m-t-20">
                                <charts ids="canvasColumn2" width="100%" height="544rpx" :chartData="visitData">
                                </charts>
                            </view>
                        </view>
                    </view>
                </view>
            </view>
        </mescroll-body>
    </view>
</template>
<script>
import { apiIndex } from "@/api/store";

import { apiVisit } from "@/api/app";
import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins";
export default {
    mixins: [MescrollMixin],
    data() {
        return {
            pagesData: [],
            loading: true,

            time: "",
            totleAmount: '123',
            todayObj: [
                {
                    name: "成交订单",
                    val: "0.00",
                },
                {
                    name: "新增用户",
                    val: "0",
                },
                {
                    name: "访客数",
                    val: "0",
                },
            ],
            orderObj: [
                {
                    name: "待发货订单",
                    val: "0.00",
                },
                {
                    name: "待审核售后",
                    val: "0",
                },
                {
                    name: "待审核评价",
                    val: "0",
                },
                {
                    name: "售罄商品",
                    val: "0",
                },
            ],

            turnoverData: {},

            visitData: {},
        };
    },

    methods: {
        async downCallback() {
            await this.getPageInfo();
            this.mescroll.endSuccess(0, false);
        },

        async getPageInfo() {
            const res = await apiIndex();
            this.totleAmount = res.today.today_order_amount
            this.todayObj[0].val = res.today.today_order_num;
            this.todayObj[1].val = res.today.today_new_user;
            this.todayObj[2].val = res.today.today_visitor;

            this.orderObj[0].val = res.pending.wait_shipped;
            this.orderObj[1].val = res.pending.wait_audit;
            this.orderObj[2].val = res.pending.wait_process;
            this.orderObj[3].val = res.pending.no_stock_goods;
            const turnover = {
                categories: res.business5.date,
                series: res.business5.list
            };
            const visit = {
                categories: res.visitor5.date,
                series: res.visitor5.list
            };
            this.turnoverData = turnover;
            this.visitData = visit;
            this.pagesData = res;
        },

        showTime() {
            var d = new Date();
            var year = d.getFullYear();
            var month = d.getMonth() + 1; //0~11
            var date = d.getDate();

            var hour =
                Number(d.getHours()) <= 9
                    ? "0" + Number(d.getHours())
                    : Number(d.getHours());
            var min =
                Number(d.getMinutes()) <= 9
                    ? "0" + Number(d.getMinutes())
                    : Number(d.getMinutes());
            var sec =
                Number(d.getSeconds()) <= 9
                    ? "0" + Number(d.getSeconds())
                    : Number(d.getSeconds());

            var str =
                year + "-" + month + "-" + date + "  " + hour + ":" + min + ":" + sec;
            this.time = str;
        },
    },
    onLoad() {
        setInterval(this.showTime, 1000);
    },
};
</script>

<style lang="scss" scoped>
.index {
    // overflow: hidden;
    // margin-bottom: 100rpx;

    &-wrap {

        // 头部
        .index-header {
            color: $-color-white;
            padding: 30rpx 24rpx;
            background-repeat: no-repeat;
            background-size: 100% 100rpx;
            background-color: #3868f9;
            // padding-bottom: 200rpx;
        }

        .index-section {
            width: 100%;
            // position: absolute;
            // top: 100rpx;

            // 今日数据卡片
            .today-data {
                padding: 30rpx 30rpx 40rpx 30rpx;
                border-radius: 14rpx;
                background-color: $-color-white;

                .item-data {
                    width: 220rpx;

                    view:first-child {
                        color: $-color-muted;
                        font-size: 24rpx;
                    }

                    view:last-child {
                        // color: $-color-normal;
                        font-size: 44rpx;
                        font-weight: 500;
                        margin-top: 6rpx;
                        height: 50px;
                        word-wrap: break-word;
                        word-break: break-all;
                    }
                }

                .item-data:first-child {
                    width: 260rpx !important;
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

            .title {
                display: flex;
                align-items: center;
                height: 100%;
                font-size: 32rpx;
                font-weight: 500;
                margin-bottom: 38rpx;

                &:before {
                    display: block;
                    width: 5rpx;
                    height: 38rpx;
                    background-color: #3868f9;
                    margin-right: 10rpx;
                    content: '';
                }
            }
        }
    }
}
</style>
