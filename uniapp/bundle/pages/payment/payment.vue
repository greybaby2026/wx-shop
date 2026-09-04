<template>
    <view :class="themeName" :style="themeCssVars">
        <view class="payment u-skeleton">
            <!-- Header -->
            <view class="payment-header">
                <price
                    class="u-skeleton-fillet"
                    :content="amount"
                    mainSize="56rpx"
                    minorSize="40rpx"
                    color="#FFFFFF"
                />
                <view v-if="deduct_amount > 0" class="payment-header-deduct">
                    <text>活动余额抵扣 -¥{{ deduct_amount }}</text>
                </view>
                <template v-if="timeout > 0">
                    <view class="payment-count-down">
                        <text>支付剩余时间</text>
                        <u-count-down
                            :timestamp="timeout"
                            :show-days="false"
                            :show-hours="false"
                            :font-size="22"
                        />
                    </view>
                </template>
            </view>

            <!-- Main -->
            <view class="payment-main">
                <!-- 抵扣明细 -->
                <view v-if="deduct_amount > 0" class="deduct-info u-skeleton-fillet">
                    <view class="deduct-info-item">
                        <text class="deduct-info-label">订单金额</text>
                        <text class="deduct-info-value">¥{{ order_amount }}</text>
                    </view>
                    <view class="deduct-info-item deduct-info-item--highlight">
                        <text class="deduct-info-label">活动余额抵扣</text>
                        <text class="deduct-info-value deduct-info-value--deduct">-¥{{ deduct_amount }}</text>
                    </view>
                    <view class="deduct-info-item deduct-info-item--total">
                        <text class="deduct-info-label">实际支付</text>
                        <text class="deduct-info-value deduct-info-value--total">¥{{ amount }}</text>
                    </view>
                </view>
                <view class="payway-container u-skeleton-fillet">
                    <!-- Payway -->
                    <u-radio-group v-model="payway" style="width: 100%">
                        <view class="payway">
                            <view
                                class="payway-item"
                                v-for="(item, index) in filteredPaywayList"
                                :key="item.id"
                                @click="changePayway(item.pay_way)"
                            >
                                <u-image
                                    :src="item.icon"
                                    width="48"
                                    height="48"
                                    mode="scaleToFill"
                                />
                                <view class="payway-item-content">
                                    <text class="payway-item-content-name">{{ item.name }}</text>
                                    <text class="payway-item-content-tips">{{ item.extra }}</text>
                                </view>
                                <u-radio
                                    shape="circle"
                                    :name="item.pay_way"
                                    :active-color="themeColor"
                                />
                            </view>
                        </view>
                    </u-radio-group>
                    <template v-if="!paywayList.length">
                        <view class="payway-empty">暂无支付方式</view>
                    </template>
                </view>
            </view>

            <!-- Footer -->
            <view class="payment-footer" v-if="paywayList.length">
                <view
                    :class="[
                        'payment-submit',
                        'u-skeleton-fillet',
                        { 'payment-submit--disabled': loadingPay }
                    ]"
                    @tap="handlePrepay('')"
                >
                    <u-loading mode="circle" :show="loadingPay" />
                    <text v-show="!loadingPay">{{ deduct_amount > 0 ? '组合支付' : '立即支付' }}</text>
                </view>
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
            <u-popup
                v-model="Alipayshow"
                mode="bottom"
                height="600rpx"
                safe-area-inset-bottom
                border-radius="20"
                closeable
                @close="handleclose"
            >
                <view class="Alipay">
                    <view class="m-t-50">
                        <price
                            class="u-skeleton-fillet"
                            :content="amount"
                            mainSize="56rpx"
                            minorSize="40rpx"
                        />
                    </view>
                    <view class="flex row-between m-t-50" style="width: 100%">
                        <text class="bold">支付方式</text>
                        <text class="bold">支付宝</text>
                    </view>
                    <view class="p-20 m-t-50 m-b-50" style="width: 100%; background-color: #f7f7f7"
                        >请复制链接,粘贴至浏览器并支付</view
                    >

                    <button class="btn" @click="$copy(key)">复制链接</button>
                </view>
            </u-popup>
        </view>

        <u-skeleton :loading="loadingSkeleton" :animation="true" bgColor="#FFFFFF" />
    </view>
</template>

<script>
/**
 * @description 支付页面
 * @query {String} from 订单来源: order-商品订单; recharge-充值订单;
 * @query {Number} order_id	订单ID
 */
import { mapGetters } from 'vuex'
import { apiPrepay, apiPayway, apiPayStatus } from '@/api/app'
import { wxpay, alipay, ttpay } from '@/utils/pay'
import { PaymentStatusEnum, PayWayEnum, PageStatusEnum } from '@/utils/enum'
import { getClient } from '@/utils/tools'
import store from '@/store'
import { ClientEnum } from '@/utils/enum'
import Wechath5 from '@/utils/wechath5'

export default {
    name: 'Payment',

    data() {
        return {
            from: '', // 订单来源
            order_id: '', // 订单ID
            order_amount: 0, // 订单原始金额
            deduct_amount: 0, // 活动余额抵扣金额
            amount: 0, // 实际支付金额（order_amount - deduct_amount）
            timeout: 0, // 倒计时间戳
            payway: '', // 支付方式
            paywayList: [], // 支付方式列表
            Alipayshow: false,
            pageStatus: PageStatusEnum['NORMAL'],
            pageErrorMsg: '',
            loadingSkeleton: true, // 骨架屏Loading
            loadingPay: false, // 支付处理中Loading
            key: ''
        }
    },
    computed: {
        // 无需过滤余额支付，活动余额自动抵扣后用户可选择任意方式支付剩余金额
        filteredPaywayList() {
            return this.paywayList
        }
    },
    methods: {
        // 更改支付方式
        changePayway(value) {
            this.$set(this, 'payway', value)
        },

        // 初始化页面数据
        initPageData() {
            // 获取支付方式
            return new Promise((resolve, reject) => {
                apiPayway({
                    from: this.from,
                    order_id: this.order_id
                })
                    .then((data) => {
                        this.order_amount = data.order_amount
                        this.deduct_amount = data.deduct_amount || 0
                        this.amount = Math.max(0, this.order_amount - this.deduct_amount)
                        this.paywayList = data.lists
                        this.payway = this.paywayList[0]?.pay_way
                        // 倒计时
                        const startTimestamp = new Date().getTime() / 1000
                        const endTimestamp = data.cancel_time * 1
                        this.timeout = endTimestamp - startTimestamp
                        resolve(data)
                    })
                    .catch((errMsg) => reject(errMsg))
            })
        },
        //支付宝弹窗关闭
        handleclose() {
            this.handlePayResult()
            this.loadingPay = false
        },
        // 预支付处理
        async handlePrepay(code) {
            if (this.userInfo.is_auth === 0 && this.payway == PayWayEnum['WECHAT'] && !code) {
                switch (getClient()) {
                    case ClientEnum['MP_WEIXIN']:
                        const res = await uni.login()
                        code = res[1].code
                        break
                    case ClientEnum['OA_WEIXIN']:
                        return Wechath5.getWxUrl()
                }
            }

            // TODO: 安全优化 - 应使用一次性支付令牌替代用户Token，避免Token泄露
            this.key = `${store.getters.appConfig.domain}/mobile/bundle/pages/toAlipay/toAlipay?id=${this.order_id}&from=${this.from}&pay_way=${this.payway}#token=${store.getters.token}`
            // 阻止重复操作
            if (this.loadingPay) return
            this.loadingPay = true
            // 处理：支付
            apiPrepay({
                from: this.from,
                order_id: this.order_id,
                pay_way: this.payway,
                code
            })
                .then(async ({ config, pay_way }) => {
                    switch (+pay_way) {
                        case PayWayEnum['WALLET']:
                            await this.handleWalletPay()
                            break
                        case 5:
                            await this.handleDownLinePay()
                            break
                        case PayWayEnum['WECHAT']:
                            await this.handleWechatPay(config)
                            break
                        case PayWayEnum['ALIPAY']:
                            uni.$on('Alipay', () => {
                                this.Alipayshow = true
                            })
                            await this.handleAlipayPay(
                                config,
                                {
                                    from: this.from,
                                    order_id: this.order_id,
                                    pay_way: pay_way
                                },
                                store.getters.token
                            )
                            break

                        default:
                            throw '支付异常'
                    }
                })
                .then(() => {
                    this.handlePayResult()
                })
                .catch((errMsg) => {
                    this.handlePayResult()
                    console.log('PAYMENT_ERROR_MSG:', errMsg)
                })
                .finally(() => {
                    this.loadingPay = false
                })
        },

        // 微信支付
        handleWechatPay(data) {
            return new Promise((resolve, reject) => {
                wxpay(data)
                    .then(async (res) => {
                        resolve(res)
                    })
                    .catch((errMsg) => reject(errMsg))
            })
        },

        // 支付宝支付
        handleAlipayPay(data, params, token) {
            return new Promise((resolve, reject) => {
                alipay(data, params, token)
                    .then(async (res) => {
                        resolve(res)
                    })
                    .catch((errMsg) => reject(errMsg))
            })
        },

        // 钱包余额支付
        handleWalletPay() {
            return new Promise((resolve, reject) => {
                resolve('支付成功')
            })
        },
        // 线下支付
        handleDownLinePay() {
            return new Promise((resolve, reject) => {
                resolve('支付成功')
            })
        },

        // 处理结果
        handlePayResult() {
            this.$Router.replace({
                path: '/bundle/pages/payment_result/payment_result',
                query: { order_id: this.order_id, from: this.from }
            })
        }
    },

    async onLoad() {
        const options = this.$Route.query
        this.from = options.from
        this.order_id = options.order_id
        const { code } = this.$Route.query

        if (code) {
            setTimeout(() => {
                this.$set(this, 'payway', PayWayEnum['WECHAT'])
                this.handlePrepay(code)
            }, 100)
        }
        try {
            if (!this.from && !this.order_id) throw '页面参数有误'
            await this.initPageData()
            this.loadingSkeleton = false
        } catch (errMsg) {
            this.pageErrorMsg = errMsg
            this.pageStatus = PageStatusEnum['ERROR']
        }
    },

    onUnload() {
        this.$Router.push({
            path: '/bundle/pages/payment_result/payment_result',
            query: { order_id: this.order_id, from: this.from }
        })
    }
}
</script>

<style lang="scss" scoped>
.payment {
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    height: 100vh;
    max-height: 100vh;
    padding-bottom: calc(100rpx + 20rpx + constant(safe-area-inset-bottom));
    padding-bottom: calc(100rpx + 20rpx + env(safe-area-inset-bottom));

    &-header {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 300rpx;
        @include background_linear(260deg);
        color: #ffffff;

        &-deduct {
            margin-top: 10rpx;
            padding: 6rpx 20rpx;
            border-radius: 30rpx;
            background-color: rgba(255, 255, 255, 0.2);
            font-size: $-font-size-xxs;
        }
    }

    &-main {
        flex: 1;
        margin-top: -40rpx;
        padding: 0 20rpx;
        overflow: hidden;
    }

    .deduct-info {
        padding: 24rpx 20rpx;
        margin-bottom: 20rpx;
        border-radius: 7px;
        background-color: #ffffff;

        &-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10rpx 0;

            &--highlight {
                padding: 14rpx 0;
                border-top: 1px dashed #eee;
                border-bottom: 1px dashed #eee;
                margin: 6rpx 0;
            }

            &--total {
                padding-top: 14rpx;
            }
        }

        &-label {
            font-size: $-font-size-sm;
            color: $-color-muted;
        }

        &-value {
            font-size: $-font-size-sm;
            color: $-color-black;

            &--deduct {
                color: #FF2C3C;
                font-weight: 500;
            }

            &--total {
                font-size: $-font-size-nr;
                font-weight: bold;
                color: $-color-black;
            }
        }
    }

    &-footer {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        height: calc(100rpx + constant(safe-area-inset-bottom));
        height: calc(100rpx + env(safe-area-inset-bottom));
        padding: 0 20rpx constant(safe-area-inset-bottom) 20rpx;
        padding: 0 20rpx env(safe-area-inset-bottom) 20rpx;
        background-color: #ffffff;
    }

    .payway-container {
        padding: 0 20rpx;
        border-radius: 7px;
        background-color: #ffffff;

        .payway-empty {
            display: flex;
            justify-content: center;
            padding: 20rpx 0;
            font-size: $-font-size-sm;
            color: $-color-muted;
        }
    }

    .payway {
        width: 100%;

        &-item {
            width: 100%;
            display: flex;
            align-items: center;
            height: 120rpx;

            &:nth-child(n + 2) {
                border-top: $-dashed-border;
            }

            &-content {
                flex: 1;
                display: flex;
                flex-direction: column;
                margin-left: 16rpx;

                &-name {
                    font-size: $-font-size-nr;
                    color: $-color-black;
                }

                &-tips {
                    font-size: $-font-size-xxs;
                    color: $-color-muted;
                }
            }
        }
    }

    &-count-down {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 7rpx 25rpx;
        border-radius: 60px;
        margin-top: 10rpx;
        font-size: $-font-size-xxs;
        background-color: #ffffff;
        color: $-color-normal;
    }

    &-submit {
        flex: 1;
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 74rpx;
        font-size: $-font-size-nr;
        border-radius: 60px;
        @include background_linear(260deg);
        color: #ffffff;

        &--disabled::before {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100%;
            display: block;
            content: '';
            background: rgba(255, 255, 255, 0.3) !important;
        }
    }
    .Alipay {
        padding: 20rpx;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        .btn {
            @include background_color();
            border-radius: 12rpx;
            width: 100%;
            height: 80rpx;
            line-height: 80rpx;
            font-size: 28rpx;
            color: white;
        }
    }
}
</style>
