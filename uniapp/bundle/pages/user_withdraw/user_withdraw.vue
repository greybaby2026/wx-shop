<template>
    <view :class="themeName">
        <view class="withdraw">
            <view class="withdraw-header m-b-24">
                <template v-if="withdrawData.type != undefined">
                    <u-tabs
                        :list="withdrawData.type"
                        :active-color="themeColor"
                        :is-scroll="true"
                        :current="current"
                        @change="change"
                    ></u-tabs>
                </template>
            </view>

            <!-- 微信 -->
            <template v-if="withdrawData.type[current] != undefined">
                <template v-if="withdrawData.type[current].value == 4">
                    <view class="withdraw-other m-b-24">
                        <u-field
                            label-width="160"
                            v-model="withdraw.account"
                            label="微信账号"
                            placeholder="请输入微信账号"
                        >
                        </u-field>

                        <u-field
                            label-width="160"
                            v-model="withdraw.real_name"
                            label="真实姓名"
                            placeholder="请输入真实姓名"
                        >
                        </u-field>

                        <u-field
                            label-width="160"
                            v-model="withdraw.apply_remark"
                            label="备注"
                            placeholder="(选填)"
                        >
                        </u-field>

                        <view class="m-t-20">
                            <uploader tips="微信收款码" v-model="withdraw.money_qr_code"></uploader>
                        </view>
                    </view>
                </template>
            </template>

            <!-- 支付宝 -->
            <template v-if="withdrawData.type[current] != undefined">
                <template v-if="withdrawData.type[current].value == 5">
                    <view class="withdraw-other m-b-24">
                        <u-field
                            label-width="160"
                            v-model="withdraw.account"
                            label="支付宝账号"
                            placeholder="请输入支付宝账号"
                        >
                        </u-field>

                        <u-field
                            label-width="160"
                            v-model="withdraw.real_name"
                            label="真实姓名"
                            placeholder="请输入真实姓名"
                        >
                        </u-field>

                        <u-field
                            label-width="160"
                            v-model="withdraw.apply_remark"
                            label="备注"
                            placeholder="(选填)"
                        >
                        </u-field>

                        <view class="m-t-20">
                            <uploader
                                tips="支付宝收款码"
                                v-model="withdraw.money_qr_code"
                            ></uploader>
                        </view>
                    </view>
                </template>
            </template>

            <!-- 银行卡 -->

            <template v-if="withdrawData.type[current] != undefined">
                <template v-if="withdrawData.type[current].value == 3">
                    <view class="withdraw-other m-b-24">
                        <u-field
                            label-width="160"
                            v-model="withdraw.account"
                            label="银行卡账号"
                            placeholder="请输入银行卡账号"
                        >
                        </u-field>

                        <u-field
                            label-width="160"
                            v-model="withdraw.real_name"
                            label="持卡人姓名"
                            placeholder="请输入持卡人姓名"
                        >
                        </u-field>

                        <u-field
                            label-width="160"
                            v-model="withdraw.bank"
                            label="提现银行"
                            placeholder="请输入银行名称"
                        >
                        </u-field>

                        <u-field
                            label-width="160"
                            v-model="withdraw.subbank"
                            label="银行支行"
                            placeholder="如: 金湾银行"
                        >
                        </u-field>

                        <u-field
                            label-width="160"
                            v-model="withdraw.apply_remark"
                            label="备注"
                            placeholder="(选填)"
                        >
                        </u-field>
                    </view>
                </template>
            </template>

            <view class="withdraw-content">
                <view class="input flex row-between">
                    <view class="flex flex-1">
                        <text>¥</text>
                        <input type="digit" v-model="withdraw.money" placeholder="0.00" />
                    </view>

                    <view class="xs">
                        <text
                            class="all-withdraw flex row-right"
                            @click="withdraw.money = withdrawData.able_withdraw"
                            >全部提现</text
                        >
                        <view class="muted m-t-10"
                            >可提现余额￥{{ withdrawData.able_withdraw }}</view
                        >
                    </view>
                </view>

                <view class="m-t-30 xs muted">
                    提示： 提现需扣除手续费{{ withdrawData.percentage }}%
                </view>
            </view>

            <view class="withdraw-btn flex row-center br60 lg white" @click="withdrawFun('')">
                确认提现
            </view>

            <view
                class="m-t-30 muted sm flex row-center"
                @click="goPage('/bundle/pages/withdraw_record/withdraw_record')"
            >
                提现记录
            </view>
        </view>
    </view>
</template>

<script>
import { apiWithdrawGetConfig, apiWithdrawApply } from '@/api/user.js'
import { prepay } from '@/api/app.js'
import { apiSubscribe } from '@/api/app'

export default {
    data() {
        return {
            list: [],
            current: 0,

            withdrawData: {
                able_withdraw: 2, //可提现
                min_withdraw: '', //最小提现金额
                max_withdraw: '', //最大提现金额
                percentage: '', //提现手续百分比
                type: [] //提现类型
            },

            withdraw: {
                type: [], //提现类型
                money: '', //提现余额
                account: '', //收款账户
                real_name: '', //真实姓名
                apply_remark: '', //备注
                money_qr_code: '', //收款码
                bank: '', // 提现银行
                subbank: '' //支行银行卡
            }
        }
    },

    methods: {
        change(event) {
            this.current = event
        },

        // 获取提现基础数据
        getWithdrawData() {
            apiWithdrawGetConfig().then((res) => {
                this.withdrawData = res
            })
        },
        // 处理：微信订阅消息
        handleSubscribeMP_WEIXIN() {
            return new Promise((resolve, reject) => {
                apiSubscribe({
                    scene_id: [111]
                })
                    .then((data) => {
                        if (!data.length) reject('暂无可订阅信息')
                        uni.requestSubscribeMessage({
                            tmplIds: data,
                            success(res) {
                                resolve(res)
                            },
                            fail(err) {
                                reject('订阅失败')
                            }
                        })
                    })
                    .catch(() => reject('订阅获取失败'))
            })
        },
        // 提现
        async withdrawFun() {
            if (this.withdraw.money == '')
                return this.$toast({
                    title: '请输入提现金额'
                })

            // #ifdef MP-WEIXIN
            if (this.withdrawData.type[this.current].value == '2') {
                try {
                    await this.handleSubscribeMP_WEIXIN()
                } catch (error) {
                    console.log(error)
                }
            }
            // #endif
            const params = {
                type: this.withdrawData.type[this.current].value, //提现类型
                money: this.withdraw.money, //提现余额
                account: this.withdraw.account, //收款账户
                real_name: this.withdraw.real_name, //真实姓名
                apply_remark: this.withdraw.apply_remark, //备注
                money_qr_code: this.withdraw.money_qr_code.toString(), //收款码
                bank: this.withdraw.bank, // 提现银行
                subbank: this.withdraw.subbank //支行银行卡
            }
            apiWithdrawApply({
                ...params
            }).then((res) => {
                this.withdraw.money = ''
                this.$Router.push({
                    path: '/bundle/pages/withdraw_success/withdraw_success',
                    query: {
                        id: res.id
                    }
                })
            })
        },

        goPage(url) {
            uni.navigateTo({
                url: url
            })
        }
    },

    onShow() {
        this.getWithdrawData()
    }
}
</script>

<style lang="scss">
.withdraw {
    padding: 30rpx;

    .withdraw-header {
        padding: 10rpx;
        border-radius: 20rpx;
        background-color: #ffffff;
    }

    .withdraw-other {
        padding: 24rpx;
        border-radius: 20rpx;
        background-color: #ffffff;
    }

    .withdraw-content {
        width: 100%;
        height: 350rpx;
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

        .all-withdraw {
            @include font_color();
        }
    }

    .withdraw-btn {
        width: 100%;
        height: 84rpx;
        margin-top: 60rpx;
        @include background_color();
    }
}
</style>
