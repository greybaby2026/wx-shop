<template>
    <view :class="themeName">
        <view class="success" v-if="passType == 1">
            <image
                src="/static/images/icon_success.png"
                class="img m-b-40"
                style="margin-top: 150rpx"
            ></image>
            <view class="m-t-40 m-b-40">注销完成</view>
            <view>你的所有信息都已清空</view>
            <button class="btn" :style="{ 'background-color': themeColor }" @click="handleComfirm">
                确定
            </button>
        </view>
        <view style="padding: 0 42rpx" v-if="passType == 0">
            <view class="fail">
                <image
                    src="/static/images/icon_fail.png"
                    class="img m-b-20"
                    style="margin-top: 50rpx"
                ></image>
                <view class="m-t-40 m-b-20 tip">抱歉，无法注销</view>
                <view>很遗憾，由于以下原因，导致账号无法注销</view>
            </view>
            <view style="margin-top: 50rpx">
                <template v-if="showReason('status')">
                    <view class="fail-body">账号冻结中</view>
                    <view class="muted"
                        >你的账号处于冻结状态，暂时无法申请注销，可联系客服进行处理。</view
                    >
                </template>
                <template v-if="showReason('order')">
                    <view class="fail-body">存在未完成订单</view>
                    <view class="muted"
                        >发现你的账号内有未处理完成的商城订单，待完成后可重新提交申请。</view
                    >
                </template>
                <template v-if="showReason('after_sale')">
                    <view class="fail-body">存在售后中订单</view>
                    <view class="muted"
                        >发现你的账号内有订单处于售后中，待商家处理完成后可重新提交申请。</view
                    >
                </template>
                <template v-if="showReason('withdraw')">
                    <view class="fail-body">存在佣金待提现申请</view>
                    <view class="muted"
                        >发现你的账号内有佣金处于待提现中，待商家处理完成后可重新提交申请。</view
                    >
                </template>
            </view>

            <button class="btn" :style="{ 'background-color': themeColor }" @click="handleBack">
                返回个人中心
            </button>
        </view>
    </view>
</template>
<script>
import { apiuserDeletecheck } from '@/api/user'
import store from '@/store'

export default {
    data() {
        return {
            passType: '',
            reason: []
        }
    },
    computed: {
        showReason() {
            return (val) => {
                console.log(this.reason)
                return this.reason.some((i) => {
                    return i == val
                })
            }
        }
    },

    methods: {
        handleReason(val) {
            for (const key in val) {
                if (val[key]['pass'] == 0) {
                    this.reason.push(key)
                }
            }
        },
        handleComfirm() {
            this.$Router.replaceAll({
                path: '/pages/index/index'
            })
        },
        handleBack() {
            this.$Router.replaceAll({
                path: '/pages/user/user'
            })
        }
    },
    async onLoad() {
        const { pass } = this.$Route.query
        this.passType = pass

        if (pass == 1) return store.commit('logout')
        const { data } = await apiuserDeletecheck()
        this.handleReason(data)
    }
}
</script>
<style lang="scss">
page {
    height: 100%;
    background-color: white;
    padding-bottom: 0;
    .img {
        width: 120rpx;
        height: 120rpx;
    }
    .success {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        font-size: 36rpx;
    }
    .btn {
        @include background_color();
        height: 80rpx;
        line-height: 80rpx;
        color: white;
        font-size: 28rpx;
        position: fixed;
        bottom: env(safe-area-inset-bottom);
        bottom: constant(safe-area-inset-bottom);
        // #ifdef APP
        bottom: 30rpx;
        // #endif
        left: 42rpx;
        right: 42rpx;
    }
    .fail {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        .tip {
            font-size: 36rpx;
        }
    }
    .fail-body {
        font-size: 32rpx;
        margin-top: 50rpx;
    }
}
</style>
