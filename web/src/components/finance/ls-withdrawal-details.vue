<!-- 用户提现弹出框 -->
<template>
    <div class="ls-withdraw-pane">
        <el-form ref="valueRef" :model="value" height="900px" label-width="240px" size="small">
            <el-form-item label="用户编号">
                <div>{{ value.sn }}</div>
            </el-form-item>
            <el-form-item label="用户昵称">
                <div>{{ value.nickname }}</div>
            </el-form-item>
            <el-form-item label="手机号码">
                <div>{{ value.mobile }}</div>
            </el-form-item>
            <el-form-item label="提现金额">
                <div>￥{{ value.money }}</div>
            </el-form-item>
            <el-form-item label="提现手续费">
                <div>￥{{ value.handling_fee }}</div>
            </el-form-item>
            <el-form-item label="到账金额">
                <div>￥{{ value.left_money }}</div>
            </el-form-item>
            <el-form-item label="提现方式">
                <!-- 单选按钮 -->
                <el-radio-group class="m-r-16" v-model="value.type" disabled>
                    <el-radio :label="1">钱包余额</el-radio>
                    <el-radio :label="2">微信零钱</el-radio>
                    <el-radio :label="3">银行卡</el-radio>
                    <el-radio :label="4">微信收款码</el-radio>
                    <el-radio :label="5">支付宝收款码</el-radio>
                </el-radio-group>
            </el-form-item>
            <!-- 银行卡 -->
            <div v-if="value.type == 3">
                <el-form-item label="提现银行">
                    <div>{{ value.bank }}</div>
                </el-form-item>
                <el-form-item label="银行支行">
                    <div>{{ value.subbank }}</div>
                </el-form-item>
                <el-form-item label="开户名称">
                    <div>{{ value.real_name }}</div>
                </el-form-item>
                <el-form-item label="银行账号">
                    <div>{{ value.account }}</div>
                </el-form-item>
            </div>
            <!-- 微信收款码 -->
            <!-- 支付宝收款码 -->
            <div v-if="value.type == 4 || value.type == 5">
                <el-form-item :label="value.type == 4 ? '微信号' : '支付宝账号'">
                    <div>{{ value.account }}</div>
                </el-form-item>
                <el-form-item label="真实姓名">
                    <div>{{ value.real_name }}</div>
                </el-form-item>
                <el-form-item label="收款码">
                    <el-image
                        style="height: 58px; height: 58px"
                        :src="value.money_qr_code"
                        :preview-src-list="[value.money_qr_code]"
                    ></el-image>
                </el-form-item>
            </div>
            <el-form-item label="提现时间">
                <div>{{ value.create_time }}</div>
            </el-form-item>
            <el-form-item label="提现状态">
                <div>{{ value.status_desc }}</div>
            </el-form-item>
            <el-form-item label="转账凭证" v-if="value.transfer_voucher">
                <el-image
                    style="height: 58px; height: 58px"
                    :src="value.transfer_voucher"
                    :preview-src-list="[value.transfer_voucher]"
                ></el-image>
            </el-form-item>
            <el-form-item label="转账时间">
                <div>{{ value.transfer_time }}</div>
            </el-form-item>
            <el-form-item label="提现说明">
                <div>{{ value.transfer_remark }}</div>
            </el-form-item>
        </el-form>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
@Component({
    components: {}
})
export default class LsWithdrawalDetails extends Vue {
    $refs!: {
        valueRef: any
    }
    @Prop() value: any
}
</script>

<style scoped lang="scss">
.ls-withdraw-pane {
    height: 650px;
    overflow-x: hidden;
}
</style>
