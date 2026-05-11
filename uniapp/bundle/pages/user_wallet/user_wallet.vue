<template>
    <view :class="themeName">
        <view class="wallet bg-white">
            <!-- 钱包内容 -->
            <view class="wallet-content">
                <view class="flex row-center">
                    <view class="white">
                        <view class="flex row-center xs m-b-10">总资产(元)</view>
                        <view class="money">
                            {{walletData.total_amount}}
                        </view>
                    </view>
                </view>
                <view class="flex row-around white">
                    <view>
                        <text class="xs m-b-10">可用余额(元)</text>
                        <view class="xxl flex row-center">
                            {{walletData.user_money}}
                        </view>
                    </view>
                    <view>
                        <text class="xs m-b-10">可提现金额(元）</text>
                        <view class="xxl flex row-center">
                            {{walletData.user_earnings}}
                        </view>
                    </view>
                </view>
            </view>

            <!-- 提现及充值入口按钮 -->
            <view class="flex row-between m-t-40">
                <view @click="goPage('/bundle/pages/user_withdraw/user_withdraw')"
                    class="recharge-btn flex row-center white sm" >
                    提现
                </view>
                <template v-if="walletData.recharge_open">
                    <view @click="goPage('/bundle/pages/user_recharge/user_recharge')"
                        class="recharge-btn flex row-center normal sm" style="background: #f4f4f4;">
                        充值
                    </view>
                </template>
            </view>
        </view>

        <!-- 记录 -->
        <view class="fun normal">
            <view @click="goPage('/bundle/pages/balance_transfer/balance_transfer')">
                <image src="../../static/images/icon_yezz.png" mode=""></image>
                <view class="sm">
                    余额转账
                </view>
            </view>
            <view @click="goPage('/bundle/pages/user_bill/user_bill')">
                <image src="../../static/images/icon_zhmx.png" mode=""></image>
                <view class="sm">
                    账户明细
                </view>
            </view>
           <!-- <view @click="goPage('/bundle/pages/transfer_record/transfer_record')">
                <image src="@/static/images/icon_zzjl.png" mode=""></image>
                <view class="sm">
                    转账记录
                </view>
            </view> -->
           <!-- <view @click="goPage('/bundle/pages/user_recharge_record/user_recharge_record')">
                <image src="@/static/images/icon_czjl.png" mode=""></image>
                <view class="sm">
                    充值记录
                </view>
            </view> -->
        </view>
        <notification-popup />
    </view>
</template>

<script>
    import {
        apiWalletData
    } from "@/api/user.js";
    import notificationPopup from '@/components/notification-popup/notification-popup.vue'
    export default {
        components: { notificationPopup },
        data() {
            return {
                walletData: {
                    user_money: '0.00', //总资产
                    total_recharge_amount: '0.00', //累计充值
                    total_order_amount: '0.00' //累计消费
                }
            }
        },

        methods: {

            // 获取钱包数据
            getWalletData() {
                apiWalletData().then(res => {
                    this.walletData = res;
                })
            },

            goPage(url) {
                uni.navigateTo({
                    url: url
                })
            }
        },

        onShow() {
            this.getWalletData();
        }
    }
</script>

<style lang="scss">
    .wallet {
        margin: 30rpx;
        padding: 20rpx;
        border-radius: 20rpx;

        // 钱包信息
        .wallet-content {
            width: 100%;
            height: 320rpx;
            border-radius: 20rpx;
            padding: 50rpx 20rpx 50rpx 30rpx;
            @include background_linear(90deg, 50%, 100%);

            .money {
                font-size: 68rpx;
                font-weight: normal;
            }
        }

        .recharge-btn {
            width: 300rpx;
            height: 84rpx;
            border-radius: 14rpx;
            @include background_color();
        }
    }
    
    // 钱包功能
    .fun {
        padding: 0 0 0 24rpx;
        >view {
            width: 220rpx;
            height: 160rpx;
            text-align: center;
            border-radius: 14rpx;
            display: inline-block;
            padding-top: 25rpx;
            margin-right: 20rpx;
            margin-bottom: 20rpx;
            background-color: #FFFFFF;
        }
    
        image {
            width: 52rpx;
            height: 52rpx;
        }
    }
</style>
