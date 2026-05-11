<template>
    <div class="consumption-reward">
        <div class="ls-card">
            <el-alert title="温馨提示：设置会员下单的积分奖励" type="info" show-icon :closable="false" />
        </div>
        <div class="ls-card m-t-16" v-loading="loading">
            <el-form :model="formDate" :rules="formRules" ref="formRef" label-width="100px" size="small">
                <el-form-item label="消费赠送积分" required>
                    <el-radio-group v-model="formDate.open_award">
                        <el-radio :label="1">开启</el-radio>
                        <el-radio :label="0">关闭</el-radio>
                    </el-radio-group>
                    <div class="muted">状态开启才生效，默认为关闭</div>
                </el-form-item>
                <el-form-item label="赠送积分事件" prop="award_event">
                    <el-select v-model="formDate.award_event" placeholder="请选择赠送积分事件">
                        <el-option label="订单付款" :value="1"></el-option>
                        <el-option label="订单发货" :value="2"></el-option>
                        <el-option label="订单完成" :value="3"></el-option>
                        <el-option label="订单超过售后期" :value="4"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="赠送积分比例" prop="award_ratio">
                    <el-input placeholder="请输入赠送积分比例" type="number" v-model="formDate.award_ratio">
                        <template slot="append">%</template>
                    </el-input>
                    <div class="muted">按订单实付金额赠送，比率必须为整数，例：当设置为100%时，100元=100积分</div>
                </el-form-item>
            </el-form>
        </div>
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="setConfig">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { apiAwardIntegralGet, apiAwardIntegralSet } from '@/api/marketing'
import { Component, Vue } from 'vue-property-decorator'

@Component
export default class ConsumptionReward extends Vue {
    $refs!: any
    loading = false
    formDate = {
        open_award: 0,
        award_event: '',
        award_ratio: ''
    }

    formRules = {
        award_event: [{ required: true, message: '请选择赠送积分事件', trigger: 'blur' }],
        award_ratio: [{ required: true, message: '请输入赠送积分比例', trigger: 'blur' }]
    }

    // 设置
    setConfig() {
        this.$refs.formRef.validate((valid: boolean) => {
            if (valid) {
                apiAwardIntegralSet(this.formDate)
            } else {
                return false
            }
        })
    }

    // 获取设置
    getConfig() {
        this.loading = true
        apiAwardIntegralGet()
            .then(res => {
                this.formDate = res
            })
            .finally(() => {
                this.loading = false
            })
    }

    created() {
        this.getConfig()
    }
}
</script>
