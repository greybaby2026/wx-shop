<template>
    <view :class="themeName">
        <view class="balance-transfer">
            <!-- 对方账户ID或者手机号码 -->
            <view class="contain bg-white">
                <input
                    style="height: 100rpx"
                    v-model="userSn"
                    type="text"
                    placeholder="请输入对方会员ID/手机号码进行转账"
                />
            </view>

            <!-- 转账输入金额 -->
            <view class="contain transfer-money bg-white m-t-20">
                <view class="muted xs">转账金额</view>
                <view class="input flex row-center">
                    <view class="m-b-5" style="font-size: 23px; align-self: flex-end">¥</view>
                    <input v-model="money" type="digit" placeholder="0.00" />
                    <view class="column" style="flex: none">
                        <view
                            class="xs primary"
                            style="text-align: right"
                            @click="money = walletData.user_money"
                            >全部</view
                        >
                        <view class="xs m-t-10" style="color: #bbbbbb"
                            >钱包余额￥{{ walletData.user_money }}</view
                        >
                    </view>
                </view>
                <view class="btn lg white flex row-center br60" @click="transferBtn">转账</view>
                <navigator
                    url="/bundle/pages/transfer_record/transfer_record"
                    hover-class="none"
                    class="m-t-20 nr lighter flex row-center"
                >
                    转账记录</navigator
                >
            </view>

            <!-- 最近转账数据 -->
            <view class="user contain bg-white m-t-20" v-if="transferList.length">
                <view class="lg bold">最近转账</view>
                <view class="list">
                    <view
                        class="item flex"
                        v-for="(item, index) in transferList"
                        :key="index"
                        @click="userSn = item.sn"
                    >
                        <u-image
                            class="m-r-30"
                            width="68rpx"
                            height="68rpx"
                            border-radius="50%"
                            :src="item.avatar"
                        ></u-image>
                        <view>
                            <view class="md">{{ item.nickname }}</view>
                            <view class="xs muted">会员ID:{{ item.sn }}</view>
                        </view>
                    </view>
                </view>
            </view>

            <!-- 弹窗确认转账 -->
            <u-modal
                :value="showTransferInfo"
                show-cancel-button
                title="确认转账给"
                :confirm-color="themeColor"
                async-close
                confirm-text="确认转账"
                @confirm="showInputPwd"
                @cancel="showTransferInfo = false"
            >
                <view class="slot-content flex row-center" style="padding: 40rpx 80rpx">
                    <view class="transfer-user flex">
                        <u-image
                            class="m-r-30"
                            width="68rpx"
                            height="68rpx"
                            border-radius="50%"
                            :src="transferInfo.avatar"
                        >
                        </u-image>
                        <view>
                            <view class="md">{{ transferInfo.nickname }}</view>
                            <view class="xs muted mt10">会员ID:{{ transferInfo.sn }}</view>
                        </view>
                    </view>
                </view>
            </u-modal>

            <!-- 交易密码组件 -->
            <set-pay-pwd ref="setPayPwd" @confirm="transferFun"></set-pay-pwd>
        </view>
    </view>
</template>

<script>
import {
    hasPayPassword,
    transfer,
    getTransferRecent,
    setPassword,
    userTransferInfo,
    apiWalletData
} from '@/api/user'
import { trottle } from '@/utils/tools'
export default {
    data() {
        return {
            showTransferInfo: false, //是否显示转账对方的用户信息
            pwd: '', //密码
            comfirmPwd: '', //确认密码
            userSn: '', //用户编号/手机号
            money: '', //转账的金额
            transferInfo: {}, //转账对方的用户信息
            transferList: [],
            walletData: {
                user_money: '0.00', //总资产
                total_recharge_amount: '0.00', //累计充值
                total_order_amount: '0.00' //累计消费
            }
        }
    },

    onShow() {
        setTimeout(() => {
            this.$refs.setPayPwd && this.$refs.setPayPwd.hasPayWord()
        })
    },
    onLoad() {
        this.transferFun = trottle(this.transferFun)
        this.getTransferRecentFun() //最近的转账会员记录
        this.apiWalletDataFun() //获取钱包余额数据
    },
    methods: {
        // 获取钱包余额数据
        apiWalletDataFun() {
            apiWalletData().then((res) => {
                this.walletData = res
            })
        },
        // 显示输入交易密码弹窗
        showInputPwd() {
            this.showTransferInfo = false
            this.$refs.setPayPwd.showInputPwd()
        },
        // 最近的转账会员记录
        getTransferRecentFun() {
            getTransferRecent().then((res) => {
                this.transferList = res
            })
        },
        // 获取对方账户信息
        transferBtn() {
            if (!this.userSn)
                return this.$toast({
                    title: '请输入对方会员ID/手机号码'
                })
            if (!this.money)
                return this.$toast({
                    title: '请输入转账金额'
                })
            // 校验会员信息
            userTransferInfo({
                transfer_in: this.userSn
            }).then((res) => {
                this.transferInfo = res
                this.showTransferInfo = true
            })
        },
        // 确认转账
        transferFun(payPwd) {
            let data = {
                transfer_in: this.userSn,
                money: this.money,
                pay_password: payPwd
            }
            transfer({ ...data }).then((res) => {
                this.$toast({
                    title: '转账成功'
                })
                this.money = ''
                this.showInputPwd()
                this.apiWalletDataFun()
                this.getTransferRecentFun()
            })
        }
    }
}
</script>

<style lang="scss">
.balance-transfer {
    padding: 20rpx 30rpx;

    .contain {
        border-radius: 20rpx;
        padding: 0 24rpx;
    }

    .transfer-money {
        padding: 30rpx 45rpx;

        .input {
            border-bottom: $-solid-border;
            margin-top: 30rpx;

            input {
                width: 100%;
                height: 94rpx;
                text-align: left;
                font-size: 66rpx;
                margin-left: 30rpx;
            }
        }

        .btn {
            @include background_linear(180deg, 50%, 100%);
            line-height: 84rpx;
            margin-top: 80rpx;
        }
    }

    .user {
        padding: 22rpx 25rpx;

        .list .item {
            padding: 26rpx 0;

            &:not(:last-of-type) {
                border-bottom: $-solid-border;
            }
        }
    }
}

/* 弹窗 */
.balance-transfer .set-word input {
    border: var(--border);
    padding: 16rpx 20rpx;
    font-size: 28rpx;
    border-radius: 6rpx;
    margin-bottom: 20rpx;
}

.balance-transfer .phone {
    padding: 20rpx 0;
    font-size: 42rpx;
}
</style>
