<template>
    <div class="ls-add-admin">
        <div class="ls-card ls-coupon-edit__form m-t-10">
            <div class="nr weight-500 m-b-20">结算设置</div>
            <el-form ref="list" :model="list" label-width="120px" size="small">
                <el-form-item label="佣金计算方式" prop="send_total_type">
                    <el-radio v-model="list.cal_method" :label="typeof list.cal_method == 'number' ? 1 : '1'"
                        >商品实际支付金额</el-radio
                    >
                    <span class="desc">根据商品实际给付金额和佣金规格计算佣金</span>
                </el-form-item>

                <el-form-item label="结算时机" prop="send_total_type">
                    <el-radio
                        v-model="list.settlement_timing"
                        :label="typeof list.settlement_timing == 'number' ? 1 : '1'"
                        >订单完成后</el-radio
                    >
                    <el-input class="ls-input" v-model="list.settlement_time"></el-input>天

                    <span class="desc">预估佣金结算后无法进行回收，请谨慎设置结算天数</span>
                </el-form-item>
            </el-form>
        </div>

        <!-- 底部保存或取消 -->
        <div class="bg-white ls-fixed-footer row-center flex">
            <div class="row-center flex">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="onSubmit('form')">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import { apiDistributionDetails, apiDistributionSet } from '@/api/distribution/distribution'

@Component({
    components: {}
})
export default class DistributionResultSet extends Vue {
    /** S Data **/

    list: any = {}

    /** E Data **/

    /** S Methods **/

    onSubmit() {
        apiDistributionSet({ ...this.list })
            .then(() => {
                this.detail()
                this.$message.success('修改成功!')
            })
            .catch(() => {
                this.$message.error('数据获取失败!')
            })
    }

    // 详情
    detail() {
        apiDistributionDetails({})
            .then(res => {
                this.list = res
            })
            .catch(() => {
                this.$message.error('数据获取失败!')
            })
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.detail()
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.ls-add-admin {
    padding-bottom: 80px;

    .ls-input {
        width: 380px;
    }

    .desc {
        display: block;
        color: #999;
        font-size: 12px;
    }
}
</style>
