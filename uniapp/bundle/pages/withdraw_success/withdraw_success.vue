<template>
    <view :class="themeName">
        <view class="withdraw-status">
            <view class="withdraw-content bg-white">
                <view class="flex row-center">
                    <!-- 待提现时 -->
                    <template v-if="detail.status == 1">
                        <u-icon name="clock-fill" size="120" :color="themeColor"></u-icon>
                    </template>
                    <!-- 提现中 -->
                    <template v-if="detail.status == 2">
                        <u-icon name="clock-fill" size="120" :color="themeColor"></u-icon>
                    </template>
                    <!-- 提现成功 -->
                    <template v-if="detail.status == 3">
                        <u-icon
                            name="checkmark-circle-fill"
                            size="120"
                            :color="themeColor"
                        ></u-icon>
                    </template>
                    <!-- 提现失败 -->
                    <template v-if="detail.status == 4">
                        <u-icon name="close-circle-fill" size="120" :color="themeColor"></u-icon>
                    </template>
                </view>

                <view class="flex row-center xl bold m-t-30">
                    {{ detail.status_desc }}
                </view>
                <view class="flex row-center m-t-30">
                    <price
                        :content="detail.money"
                        color="#FF2C3C"
                        mainSize="56rpx"
                        minorSize="38rpx"
                    ></price>
                </view>
                <view class="normal nr m-t-60 p-b-60" style="border-bottom: 1rpx solid #e5e5e5">
                    <view class="flex row">
                        <text class="withdraw-label">提现单号</text>
                        <text>{{ detail.sn }}</text>
                    </view>
                    <view class="flex row m-t-24">
                        <text class="withdraw-label">申请时间</text>
                        <text>{{ detail.create_time }}</text>
                    </view>
                    <view class="flex row m-t-24">
                        <text class="withdraw-label">提现至</text>
                        <text>{{ detail.type_desc }}</text>
                    </view>
                    <view class="flex row m-t-24">
                        <text class="withdraw-label">服务费</text>
                        <text>{{ detail.handling_fee }}</text>
                    </view>
                    <view class="flex row m-t-24">
                        <text class="withdraw-label">实际到账</text>
                        <text>{{ detail.left_money }}</text>
                    </view>
                </view>

                <!-- 银行卡提现 -->
                <view
                    class="normal nr m-t-60 p-b-60"
                    style="border-bottom: 1rpx solid #e5e5e5"
                    v-if="detail.type == 3"
                >
                    <view class="flex row">
                        <text class="withdraw-label">银行卡账号</text>
                        <text>{{ detail.account }}</text>
                    </view>
                    <view class="flex row m-t-24">
                        <text class="withdraw-label">持卡人姓名</text>
                        <text>{{ detail.real_name }}</text>
                    </view>
                    <view class="flex row m-t-24">
                        <text class="withdraw-label">提现银行</text>
                        <text>{{ detail.bank }}</text>
                    </view>
                    <view class="flex row m-t-24">
                        <text class="withdraw-label">银行支行</text>
                        <text>{{ detail.subbank }}</text>
                    </view>
                    <view class="flex row m-t-24">
                        <text class="withdraw-label">备注说明</text>
                        <text>{{ detail.apply_remark }}</text>
                    </view>
                </view>

                <!-- 支付宝提现 -->
                <view
                    class="normal nr m-t-60 p-b-60"
                    style="border-bottom: 1rpx solid #e5e5e5"
                    v-if="detail.type == 5"
                >
                    <view class="flex row m-t-24">
                        <text class="withdraw-label">支付宝账号</text>
                        <text>{{ detail.account }}</text>
                    </view>
                    <view class="flex row m-t-24">
                        <text class="withdraw-label">真实姓名</text>
                        <text>{{ detail.real_name }}</text>
                    </view>
                    <view class="flex row m-t-24 col-top">
                        <text class="withdraw-label">支付宝收款码</text>
                        <u-image
                            height="160"
                            width="160"
                            :src="detail.money_qr_code"
                            @click="showImage([detail.money_qr_code])"
                        >
                        </u-image>
                    </view>
                    <view class="flex row m-t-24">
                        <text class="withdraw-label">备注说明</text>
                        <text>{{ detail.apply_remark }}</text>
                    </view>
                </view>

                <!-- 微信收款码提现 -->
                <view
                    class="normal nr m-t-60 p-b-60"
                    style="border-bottom: 1rpx solid #e5e5e5"
                    v-if="detail.type == 4"
                >
                    <view class="flex row m-t-24">
                        <text class="withdraw-label">微信账号</text>
                        <text>{{ detail.account }}</text>
                    </view>
                    <view class="flex row m-t-24">
                        <text class="withdraw-label">真实姓名</text>
                        <text>{{ detail.real_name }}</text>
                    </view>
                    <view class="flex row m-t-24 col-top">
                        <text class="withdraw-label">微信收款码</text>
                        <u-image
                            height="160"
                            width="160"
                            :src="detail.money_qr_code"
                            @click="showImage([detail.money_qr_code])"
                        >
                        </u-image>
                    </view>
                    <view class="flex row m-t-24">
                        <text class="withdraw-label">备注说明</text>
                        <text>{{ detail.apply_remark }}</text>
                    </view>
                </view>

                <view
                    class="normal nr m-t-60 p-b-60"
                    style="border-bottom: 1rpx solid #e5e5e5"
                    v-if="detail.status == 3 || detail.status == 4"
                >
                    <view class="flex row col-top">
                        <text class="withdraw-label">转账凭证</text>
                        <u-image
                            height="160"
                            width="160"
                            :src="detail.transfer_voucher"
                            v-if="detail.transfer_voucher"
                            @click="showImage([detail.transfer_voucher])"
                        >
                        </u-image>
                    </view>
                    <view class="flex row m-t-24 col-top">
                        <text class="withdraw-label">转账说明</text>
                        <text>{{ detail.transfer_remark }}</text>
                    </view>
                </view>

                <template v-if="detail.status == 1">
                    <template v-if="!type">
                        <view @click="toRecord" class="br60 btn record-btn flex row-center"
                            >查看历史提现记录</view
                        >
                        <view @click="toHome" class="br60 btn home-btn flex row-center m-t-30"
                            >返回首页</view
                        >
                    </template>
                </template>
            </view>
            <view class="muted xs flex row-center m-t-30" v-if="detail.type != 2"
                >* 审核成功后约72小时内到账，请留意账户明细</view
            >

            <view
                class="br60 btn record-btn flex row-center"
                v-if="detail.wechat_change_wait_receive"
                @click="handleclick"
                >立即收款</view
            >
        </view>
    </view>
</template>

<script>
import { apiWithdrawDetail, withdrawReceive } from '@/api/user.js'
import Wechath5 from '@/utils/wechath5'

export default {
    data() {
        return {
            id: '',
            type: false, //true为从记录进入，false为默认
            detail: {}
        }
    },

    methods: {
        async handleclick() {
            // #ifdef MP-WEIXIN
            if (wx.canIUse('requestMerchantTransfer')) {
                wx.requestMerchantTransfer({
                    mchId: this.detail.pay_config.mch_id,
                    appId: wx.getAccountInfoSync().miniProgram.appId,
                    package: this.detail.pay_desc.package_info,
                    success: (res) => {
                        // res.err_msg将在页面展示成功后返回应用时返回ok，并不代表付款成功
                        console.log('success:', res)
                        withdrawReceive({ id: this.id }).then((res) => {
                            this.getWithdrawDetailDataFun()
                        })
                    },
                    fail: (res) => {
                        console.log('fail:', res)
                    }
                })
            } else {
                wx.showModal({
                    content: '你的微信版本过低，请更新至最新版本。',
                    showCancel: false
                })
            }

            // #endif
            // #ifdef H5
            var ua = navigator.userAgent.toLowerCase()
            if (ua.match(/MicroMessenger/i) == 'micromessenger') {
                //这是微信环境
                try {
                    await Wechath5.reviceTransfer(
                        this.detail.pay_config.mch_id,
                        this.detail.pay_config.app_id,
                        this.detail.pay_desc.package_info
                    )
                    withdrawReceive({ id: this.id }).then((res) => {
                        this.getWithdrawDetailDataFun()
                    })
                } catch (error) {
                    console.log(error)
                }
            } else {
                //这是非微信环境
                return this.$toast({
                    title: '请在微信环境内打开'
                })
            }
            // #endif
            // #ifdef APP-PLUS
            return this.$toast({
                title: '请在微信环境内打开'
            })
            // #endif
        },
        getWithdrawDetailDataFun() {
            apiWithdrawDetail({
                id: this.id
            }).then((res) => {
                this.detail = res
                if (res.wechat_change_wait_receive) {
                    setTimeout(() => {
                        this.handleclick()
                    }, 800)
                }
            })
        },

        toRecord() {
            uni.redirectTo({
                url: '/bundle/pages/withdraw_record/withdraw_record'
            })
        },

        toHome() {
            uni.reLaunch({
                url: '/pages/index/index'
            })
        },

        // 预览图片
        showImage(list) {
            // 预览图片
            uni.previewImage({
                urls: list,
                current: 1
            })
        }
    },

    onLoad() {
        const options = this.$Route.query
        this.id = options.id
        this.type = options.type
        this.getWithdrawDetailDataFun()
    }
}
</script>

<style lang="scss">
.withdraw-status {
    padding: 24rpx;

    .withdraw-content {
        width: 100%;
        padding: 30rpx;
        border-radius: 20rpx;
    }

    .btn {
        height: 84rpx;
        font-size: 32rpx;
    }

    .record-btn {
        color: #ffffff;
        margin-top: 60rpx;
        @include background_color();
    }

    .home-btn {
        border-width: 2rpx;
        border-style: solid;
        @include border_color();
        @include font_color();
    }

    .withdraw-label {
        width: 180rpx;
        text-align: left;
    }
}
</style>
