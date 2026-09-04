<template>
    <view class="verification-detail" :class="themeName">
        <view class="order-contain">
            <view class="order">
                <!-- Order Header -->
                <view class="order-header">
                    <view class="order-sn">联系人: {{ orderInfo.contact }}</view>
                    <view :class="['order-status', 'order-status--primary']">{{
                        orderInfo.verification_status_desc
                    }}</view>
                </view>

                <!-- Order Main -->
                <view class="order-main">
                    <goods-card
                        v-for="(goodsItem, goodsIndex) in orderInfo.order_goods"
                        :key="goodsIndex"
                        shape="rectangle"
                        :name="goodsItem.goods_name"
                        :image="goodsItem.goods_image"
                        :contain-style="{ 'border-radius': 0, height: '230rpx' }"
                        :image-style="{ width: '180rpx', height: '180rpx' }"
                    >
                        <view class="m-t-10 order-main__spec">
                            <text class="muted xs skuline">{{ goodsItem.spec_value_str }}</text>
                            <text class="lighter sm">x{{ goodsItem.goods_num }}</text>
                        </view>
                        <view style="margin-left: auto; color: red">
                            <text v-if="goodsItem.after_sale.status == 1">售后中</text>
                            <text v-if="goodsItem.after_sale.status == 2">售后成功</text>
                            <text v-if="goodsItem.after_sale.status == 3">售后失败</text>
                        </view>
                    </goods-card>
                </view>
            </view>

            <view class="operation operation--primary" @click="showVerificationModal = true"
                >已提货</view
            >
            <view class="operation operation--normal" @click="goVerificationList"
                >返回核销列表</view
            >
        </view>

        <!-- 二次确认核销 -->
        <u-modal
            ref="uModalInput"
            v-model="showVerificationModal"
            show-cancel-button
            :confirm-color="themeColor"
            confirm-text="确定"
            title="确认核销"
            content="是否确认核销？"
            @confirm="handleVerificationConfirm"
        />

        <!-- 页面状态 -->
        <page-status :status="pageStatus">
            <view slot="error" class="flex-col column-center">
                <u-empty
                    text="订单异常"
                    src="/static/images/empty/order.png"
                    :icon-size="280"
                ></u-empty>
                <view class="operation operation--primary" @click="goVerificationList"
                    >返回核销列表</view
                >
            </view>
        </page-status>
    </view>
</template>

<script>
import { apiVerificationOrderDetail, apiVerificationOrderConfirm } from '@/api/order'
import { PageStatusEnum } from '@/utils/enum'

export default {
    name: 'VerificationDetail',

    data() {
        return {
            code: '', // 核销码
            orderInfo: {}, // 订单信息
            pageStatus: PageStatusEnum['LOADING'],
            showVerificationModal: false // 显示(核销)：是|否
        }
    },

    methods: {
        // 初始化订单数据
        initOrderData() {
            return new Promise((resolve, reject) => {
                apiVerificationOrderDetail({
                    pickup_code: this.code,
                    confirm: 1
                })
                    .then((data) => {
                        this.orderInfo = data
                        resolve(data)
                    })
                    .catch((err) => {
                        reject(err.message)
                    })
            })
        },

        // 确认核销订单
        handleVerificationConfirm() {
            apiVerificationOrderConfirm({
                id: this.orderInfo.id
            }).then((data) => {
                // this.initOrderData()
                setTimeout(() => {
                    this.$Router.back()
                }, 0.5 * 1000)
            })
        },

        // 返回核销列表
        goVerificationList() {
            this.$Router.back()
        }
    },

    async onLoad() {
        const options = this.$Route.query

        try {
            if (!options.code) throw new Error('订单异常')
            this.code = options.code
            await this.initOrderData()
            console.log('HELLOW')
            this.pageStatus = PageStatusEnum['NORMAL']
        } catch (err) {
            console.log(err)
            // setTimeout(() => {
            // 	this.$Router.back()
            // }, 0.5 * 1000)
            this.pageStatus = PageStatusEnum['ERROR']
        }
    }
}
</script>

<style lang="scss" scoped>
.verification-detail {
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
        border-bottom: $-dashed-border;

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
        &__spec {
            display: flex;
            justify-content: space-between;
            height: 100%;
        }
    }
}

.operation {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 80rpx;
    margin-top: 20rpx;
    border-radius: 60px;

    &--primary {
        @include background_color(); color: #ffffff;
    }

    &--normal {
        background-color: #ffffff;
        color: $-color-normal;
    }
}
</style>
