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

                <!-- 门店信息 -->
                <view class="order-store">
                    <text class="muted xs store-line"
                        >核销门店：{{ orderInfo.selffetch_shop_name || '未绑定门店' }}</text
                    >
                    <text v-if="orderInfo.is_cross_store" class="muted xs store-line"
                        >归属门店：{{ orderInfo.belong_store_name || '未关联门店' }}</text
                    >
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

            <view
                class="operation operation--primary"
                :class="{ 'operation--submitting': submitting }"
                @click="openVerificationModal"
                >{{ submitting ? '核销中...' : '已提货' }}</view
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
import { apiVerificationOrderDetail, apiVerificationOrderConfirm, apiVerificationIsVerifier } from '@/api/order'
import { PageStatusEnum } from '@/utils/enum'

export default {
    name: 'VerificationDetail',

    data() {
        return {
            code: '', // 核销码
            orderInfo: {}, // 订单信息
            pageStatus: PageStatusEnum['LOADING'],
            showVerificationModal: false, // 显示(核销)：是|否
            submitting: false // 核销请求中(防重复提交)
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
                        reject(typeof err === 'string' ? err : (err && err.msg) || '')
                    })
            })
        },

        // 打开核销确认弹窗(核销请求中不可再次打开)
        openVerificationModal() {
            if (this.submitting) return
            this.showVerificationModal = true
        },

        // 确认核销订单
        handleVerificationConfirm() {
            // 防重复核销:请求未返回前直接拦截,避免重复写入核销记录与跨店结算明细
            if (this.submitting) return
            this.submitting = true
            apiVerificationOrderConfirm({
                id: this.orderInfo.id
            })
                .then((data) => {
                    // this.initOrderData()
                    setTimeout(() => {
                        this.$Router.back()
                    }, 0.5 * 1000)
                })
                .catch((err) => {
                    this.submitting = false
                    this.$toast({
                        title: typeof err === 'string' && err ? err : '核销失败，请重试'
                    })
                })
        },

        // 返回核销列表
        goVerificationList() {
            this.$Router.back()
        },

        // 非核销员拦截(服务端虽会拒绝, 但需避免普通用户看到核销界面甚至扫码)
        denyAccess() {
            this.$toast({ title: '仅门店核销员可访问' })
            setTimeout(() => {
                const pages = getCurrentPages()
                if (pages.length > 1) {
                    this.$Router.back()
                } else {
                    uni.reLaunch({ url: '/pages/index/index' })
                }
            }, 800)
        }
    },

    async onLoad() {
        const options = this.$Route.query

        try {
            // 页面门禁: 核销页仅限门店核销员访问(与核销列表页口径一致)
            const verifier = await apiVerificationIsVerifier().catch(() => null)
            if (!verifier || !verifier.is_verifier) {
                this.pageStatus = PageStatusEnum['ERROR']
                this.denyAccess()
                return
            }

            if (!options.code) throw new Error('订单异常')
            this.code = options.code
            await this.initOrderData()
            console.log('HELLOW')
            this.pageStatus = PageStatusEnum['NORMAL']
        } catch (err) {
            console.log(err)
            if (typeof err === 'string' && err) {
                this.$toast({ title: err })
            }
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

    &--submitting {
        opacity: 0.6;
    }
}
</style>
