<template>
    <div class="withdraw_deposit">
        <div class="ls-card">
            <!-- 提示 -->
            <el-alert
                title="温馨提示：设置钱包可提现金额的提现方式，可提现金额来源于各类佣金奖励。"
                type="info"
                :closable="false"
                show-icon
            />
        </div>
        <el-form ref="formRef" :model="form" :rules="formRules" label-width="120px" size="small">
            <!-- 店铺信息 -->
            <div class="ls-card m-t-16">
                <div class="card-title">提现设置</div>
                <div class="card-content m-t-24">
                    <el-form-item label="提现方法" prop="withdraw_way">
                        <!-- 多选框 -->
                        <el-checkbox-group v-model="form.withdraw_way">
                            <el-checkbox label="1">账户余额(默认)</el-checkbox>
                            <el-checkbox label="2">微信零钱</el-checkbox>
                            <el-checkbox label="4">微信收款码</el-checkbox>
                            <el-checkbox label="5">支付宝收款码</el-checkbox>
                            <el-checkbox label="3">银行卡</el-checkbox>
                        </el-checkbox-group>
                        <div class="muted xs">默认需要保留至少一种提现方法</div>
                    </el-form-item>
                    <el-form-item label="微信零钱接口" prop="transfer_way">
                        <el-radio-group class="m-r-16" v-model="form.transfer_way">
                            <el-radio :label="1">企业付款到零钱</el-radio>
                            <el-radio :label="2">商家转账到零钱</el-radio>
                        </el-radio-group>
                        <div class="muted xs" v-if="form.transfer_way == 1">
                            企业付款到零钱：需在微信支付中开通“企业付款到零钱”功能，仅支持微信支付接口 V2
                        </div>
                        <div class="muted xs" v-if="form.transfer_way == 2">
                            商家转账到零钱：需在微信支付中开通“商家转账到零钱”功能，支持微信支付接口 V2、V3
                        </div>
                    </el-form-item>
                    <el-form-item label="最低提现金额" prop="withdraw_min_money">
                        <el-input class="ls-input" placeholder="请输入金额" v-model="form.withdraw_min_money">
                            <template slot="append">元</template>
                        </el-input>
                        <div class="muted xs">会员提现需满足最低提现金额。才能提交提现申请。</div>
                    </el-form-item>
                    <el-form-item label="最高提现金额">
                        <el-input class="ls-input" placeholder="请输入金额" v-model="form.withdraw_max_money">
                            <template slot="append">元</template>
                        </el-input>
                        <div class="muted xs">会员提现允许的最高提现金额。</div>
                    </el-form-item>
                    <el-form-item label="提现手续费">
                        <el-input
                            class="ls-input"
                            placeholder="请输入提现手续费"
                            v-model="form.withdraw_service_charge"
                        >
                            <template slot="append">%</template>
                        </el-input>
                        <div class="muted xs">会员提现时收取的手续费占比。</div>
                    </el-form-item>
                    <el-form-item label="单笔最高提现">
                        <el-input class="ls-input" placeholder="0表示不限制" v-model="form.withdraw_max_single">
                            <template slot="append">元</template>
                        </el-input>
                        <div class="muted xs">单笔提现金额上限，0表示不限制。</div>
                    </el-form-item>
                    <el-form-item label="每日提现总额">
                        <el-input class="ls-input" placeholder="0表示不限制" v-model="form.withdraw_max_daily">
                            <template slot="append">元</template>
                        </el-input>
                        <div class="muted xs">每位用户每日提现总额上限，0表示不限制。</div>
                    </el-form-item>
                    <el-form-item label="收款确认超时">
                        <el-input class="ls-input" placeholder="默认72小时" v-model="form.withdraw_confirm_timeout">
                            <template slot="append">小时</template>
                        </el-input>
                        <div class="muted xs">商家转账到零钱模式下，用户未确认收款的超时时间，超时后自动关闭并回退金额。</div>
                    </el-form-item>
                </div>
            </div>
        </el-form>
        <!--  表单功能键  -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <!-- <el-button size="small" @click="$router.go(-1)">取消</el-button> -->
                <el-button size="small" type="primary" @click="setWithdrawDeposit()">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'vue-property-decorator'
import { apiWithdrawConfig, apiWithdrawConfigSet } from '@/api/setting/user'
@Component({
    components: {}
})
export default class WithdrawDeposit extends Vue {
    /** S Data **/
    form = {
        withdraw_way: [], // 提现方式：1-钱包余额；2-微信零钱；3-银行卡；4-微信收款码；5-支付宝收款码
        withdraw_min_money: 0, // 最低提现金额
        withdraw_max_money: 0, // 最高提现金额
        withdraw_service_charge: 0, // 提现手续费
        withdraw_max_single: 0, // 单笔最高提现
        withdraw_max_daily: 0, // 每日提现总额
        withdraw_confirm_timeout: 72, // 收款确认超时(小时)
        scene: 'withdraw', // 场景：user-用户设置；register-注册设置；withdraw-提现设置
        transfer_way: 1 // 微信零钱接口:1-企业付款到零钱;2-商家转账到零钱
    }
    // 预校验
    formRules = {
        withdraw_way: [
            {
                type: 'array',
                required: true,
                message: '默认需要保留至少一种提现方法',
                trigger: 'change'
            }
        ]
    }
    $refs!: { formRef: any }
    /** E Data **/

    // 获取用户提现设置
    getWithdrawDeposit() {
        apiWithdrawConfig()
            .then((res: any) => {
                // this.$message.success('数据请求成功!')
                this.form = res
            })
            .catch(() => {
                // this.$message.error('数据请求失败!')
            })
    }

    // 修改用户提现设置
    setWithdrawDeposit() {
        this.form.scene = 'withdraw' // 场景：user-用户设置；register-注册设置；withdraw-提现设置
        this.form.withdraw_min_money = Number(this.form.withdraw_min_money)

        this.$refs.formRef.validate((valid: any) => {
            // 预校验
            if (!valid) {
                return
            }
            apiWithdrawConfigSet(this.form)
                .then((res: any) => {
                    setTimeout(() => {
                        this.getWithdrawDeposit()
                    }, 50)
                })
                .catch(() => {
                    // this.$message.error('保存失败!')
                })
        })
    }
    /** S Life Cycle **/
    created() {
        this.getWithdrawDeposit()
    }
    /** E Life Cycle **/
}
</script>

<style scoped lang="scss">
.ls-card {
    .ls-input {
        width: 240px;
    }

    .card-title {
        font-size: 14px;
        font-weight: 500;
    }

    .login_limit-unit {
        display: inline-block;
        width: 2em;
        text-align: center;
    }
}

::v-deep .el-input {
    width: 240px;
}

.withdraw_deposit {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;
}
</style>
