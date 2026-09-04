<template>
    <view :class="themeName">
        <view class="recharge">
            <!-- 活动主题头部 -->
            <view v-if="deductActive && deductTheme" class="recharge-activity-header" :style="{ backgroundColor: deductTheme.primary_color || '#1a3a5c' }">
                <view class="activity-badge" v-if="deductTheme.badge_text">
                    <image v-if="deductTheme.badge_icon" :src="deductTheme.badge_icon" class="badge-icon" mode="aspectFit" />
                    <text class="badge-text">{{ deductTheme.badge_text }}</text>
                </view>
                <view class="activity-slogan" v-if="deductTheme.slogan">{{ deductTheme.slogan }}</view>
                <view class="activity-ratio">充值金额{{ deductRatio }}%可用于订单抵扣</view>
            </view>

            <view class="recharge-content">
                <view class="normal md m-b-10"> 充值金额 </view>

                <view class="input flex">
                    <text>¥</text>
                    <input type="text" v-model="rechargeData.money" placeholder="0.00" />
                </view>

                <view class="m-t-25 xs muted">
                    提示： 当前余额为 <text class="tips"> ¥{{ money }}</text>
                </view>
                <view class="m-t-10 xs muted" v-if="deductActive">
                    活动余额为 <text class="tips"> ¥{{ activityMoney }}</text>，充值金额将进入活动余额，仅用于订单抵扣
                </view>
            </view>

            <view class="recharge-btn flex row-center br60 lg white" @click="recharge('')">
                立即充值
            </view>

            <view class="recommend-recharge m-t-25">
                <view class="xxl normal m-b-40"> 推荐充值 </view>

                <view
                    class="recommend-item"
                    @click="recharge(item.id)"
                    v-for="(item, index) in rechargeTemplateLists"
                    :key="index"
                >
                    <view class="xxl"> {{ item.money }}元 </view>
                    <view class="xs m-t-10" v-if="item.tips">
                        {{ item.tips }}
                    </view>
                </view>
            </view>

            <view
                class="record muted sm flex row-center"
                @click="goPage('/bundle/pages/user_recharge_record/user_recharge_record')"
            >
                充值记录
            </view>
        </view>
    </view>
</template>

<script>
import { apiWalletData, apiRechargeTemplateLists, apiRecharge } from '@/api/user.js'
import { prepay } from '@/api/app.js'
import { PaymentStatusEnum } from '@/utils/enum'
import { apiGetDeductDisplayInfo } from '@/api/activity_deduct.js'

export default {
    data() {
        return {
            money: '', //充值的金额
            deductActive: false,
            deductTheme: null,
            deductRatio: 0,
            activityMoney: '0.00',

            rechargeTemplateLists: [], //推荐充值模板

            rechargeData: {
                pay_way: 2,
                template_id: '',
                money: ''
            }
        }
    },

    onShow() {
        this.getWalletData()
        this.getRechargeTemplateLists()
        this.getDeductInfo()
    },

    onLoad() {
        // 监听全局duringPayment事件
        // uni.$on('duringPayment', ({ result }) => {
        //     if (result === PaymentStatusEnum['SUCCESS']) {
        //         this.$Router.back()
        //         this.money = ''
        //         setTimeout(() => {
        //             this.$toast({ title: '支付成功' })
        //         }, 0.5 * 1000)
        //     }
        // })
    },

    onUnload() {
        // uni.$off('duringPayment')
    },

    methods: {
        // 获取钱包数据
        getWalletData() {
            apiWalletData().then((res) => {
                this.money = res.user_money
                this.activityMoney = res.activity_money || '0.00'
            })
        },

        // 获取抵扣活动信息
        getDeductInfo() {
            apiGetDeductDisplayInfo().then((res) => {
                this.deductActive = res.active || false
                this.deductRatio = res.ratio || 0
                this.deductTheme = (res.theme_config && Object.keys(res.theme_config).length > 0) ? res.theme_config : null
            }).catch(() => {})
        },

        // 获取充值模板
        getRechargeTemplateLists() {
            apiRechargeTemplateLists().then((res) => {
                this.rechargeTemplateLists = res.lists
            })
        },

        // 充值
        recharge(id = '') {
            if (id !== '') {
                this.rechargeData.template_id = id
            }
            apiRecharge({
                ...this.rechargeData
            }).then((data) => {
                this.rechargeData.template_id = ''
                this.$Router.push({
                    path: `/bundle/pages/payment/payment`,
                    query: {
                        from: data.from,
                        order_id: data.order_id
                    }
                })
            })
        },

        goPage(url) {
            uni.navigateTo({
                url: url
            })
        }
    }
}
</script>

<style lang="scss">
.recharge {
    padding: 30rpx;

    .recharge-activity-header {
        width: 100%;
        padding: 40rpx 30rpx;
        border-radius: 20rpx;
        margin-bottom: 30rpx;
        color: #ffffff;
        position: relative;
        overflow: hidden;

        .activity-badge {
            display: flex;
            align-items: center;
            margin-bottom: 16rpx;

            .badge-icon {
                width: 40rpx;
                height: 40rpx;
                margin-right: 12rpx;
            }

            .badge-text {
                font-size: 28rpx;
                font-weight: 600;
                padding: 4rpx 16rpx;
                border-radius: 20rpx;
                background-color: rgba(255, 255, 255, 0.2);
            }
        }

        .activity-slogan {
            font-size: 36rpx;
            font-weight: bold;
            margin-bottom: 12rpx;
        }

        .activity-ratio {
            font-size: 24rpx;
            opacity: 0.85;
        }
    }

    .recharge-content {
        width: 100%;
        height: 400rpx;
        padding: 66rpx;
        border-radius: 20rpx;
        background-color: #ffffff;

        .input {
            padding: 24rpx 0;
            font-size: 46rpx;
            border-bottom: 1rpx solid #e5e5e5;

            input {
                padding-left: 30rpx;
                font-size: 66rpx;
                height: 80rpx;
            }
        }

        .tips {
            @include font_color();
        }
    }

    .recharge-btn {
        width: 100%;
        height: 84rpx;
        margin-top: 60rpx;
        @include background_color();
    }

    .recommend-item {
        width: 214rpx;
        height: 160rpx;
        padding: 30rpx;
        float: left;
        text-align: center;
        @include font_color();
        border-width: 1rpx;
        border-style: solid;
        border-radius: 10rpx;
        margin-right: 24rpx;
        margin-bottom: 24rpx;
        @include border_color();
        background-color: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        .xxl {
            font-weight: 600;
        }
    }

    .recommend-item:nth-child(3n) {
        margin-right: 0;
    }

    .record {
        width: 100%;
        left: 0;
        bottom: 80rpx;
        box-sizing: border-box;
        position: absolute;
    }
}
</style>
