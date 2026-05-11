<!-- 财务概况 -->
<template>
    <div class="data-profile">
        <div class="ls-card">
            <div class="card-title">经营概况</div>
            <div class="card-content m-t-24">
                <el-row :gutter="20">
                    <el-col :span="6">
                        <el-tooltip content="所有订单实付金额总和（包含已退款金额）" placement="top">
                            <div class="inline">
                                <div class="lighter m-b-8">累计营业额（元）</div>
                                <div class="font-size-24">
                                    {{ ProfileData.order_sum }}
                                </div>
                            </div>
                        </el-tooltip>
                    </el-col>
                    <el-col :span="6">
                        <el-tooltip content="所有订单成交订单总数（包含已退款订单）" placement="top">
                            <div class="inline">
                                <div class="lighter m-b-8">累计成交订单数（笔）</div>
                                <div class="font-size-24">
                                    {{ ProfileData.order_num }}
                                </div>
                            </div>
                        </el-tooltip>
                    </el-col>
                    <el-col :span="6">
                        <el-tooltip content="包含取消订单金额（已支付但是取消订单）和售后退款金额" placement="top">
                            <div class="inline">
                                <div class="lighter m-b-8">累计售后退款金额（元）</div>
                                <div class="font-size-24">
                                    {{ ProfileData.after_sale_sum }}
                                </div>
                            </div>
                        </el-tooltip>
                    </el-col>
                </el-row>
            </div>
        </div>
        <div class="ls-card m-t-16">
            <div class="card-title">用户概况</div>
            <div class="card-content m-t-24">
                <el-row :gutter="20">
                    <el-col :span="6">
                        <el-tooltip content="所有用户的资产总和" placement="top">
                            <div class="inline">
                                <div class="lighter m-b-8">用户总资产（元）</div>
                                <div class="font-size-24">
                                    {{ ProfileData.user_total_assets }}
                                </div>
                            </div>
                        </el-tooltip>
                    </el-col>
                    <el-col :span="6">
                        <el-tooltip content="所有用户的可用余额（不包含可提现金额）" placement="top">
                            <div class="inline">
                                <div class="lighter m-b-8">用户可用余额（元）</div>
                                <div class="font-size-24">
                                    {{ ProfileData.user_money_sum }}
                                </div>
                            </div>
                        </el-tooltip>
                    </el-col>
                    <el-col :span="6">
                        <el-tooltip content="所有用户的可提现金额（不包含余额）" placement="top">
                            <div class="inline">
                                <div class="lighter m-b-8">用户可提现金额（元）</div>
                                <div class="font-size-24">
                                    {{ ProfileData.user_earnings_sum }}
                                </div>
                            </div>
                        </el-tooltip>
                    </el-col>
                </el-row>
            </div>
        </div>
        <div class="ls-card m-t-16">
            <div class="card-title">分销概况</div>
            <div class="card-content m-t-24">
                <el-row :gutter="20">
                    <el-col :span="6">
                        <div class="inline">
                            <div class="lighter m-b-8">今日入账佣金（元）</div>
                            <div class="font-size-24">
                                {{ ProfileData.distribution_data.today_rebated_commission }}
                            </div>
                        </div>
                    </el-col>
                    <el-col :span="6">
                        <div class="inline">
                            <div class="lighter m-b-8">待结算佣金（元）</div>
                            <div class="font-size-24">
                                {{ ProfileData.distribution_data.cumulative_unrefunded_commission }}
                            </div>
                        </div>
                    </el-col>
                    <el-col :span="6">
                        <div class="inline">
                            <div class="lighter m-b-8">累计已入账佣金（元）</div>
                            <div class="font-size-24">
                                {{ ProfileData.distribution_data.cumulative_rebated_commission }}
                            </div>
                        </div>
                    </el-col>
                    <el-col :span="6">
                        <div class="inline">
                            <div class="lighter m-b-8">今日新增待结算佣金（元）</div>
                            <div class="font-size-24">
                                {{ ProfileData.distribution_data.today_unrefunded_commission }}
                            </div>
                        </div>
                    </el-col>
                </el-row>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiFinanceDataCenter } from '@/api/finance/finance'

@Component
export default class FinanceProfile extends Vue {
    /** S Data **/
    ProfileData = {
        distribution_data: {}
    }
    /** E Data **/

    /** S Methods **/
    // 获取财务概况
    financeDataCenter() {
        apiFinanceDataCenter()
            .then(res => {
                this.ProfileData = res
            })
            .catch(res => {})
    }
    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.financeDataCenter()
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.ls-card {
    .card-title {
        font-size: 14px;
        font-weight: 500;
    }
}
</style>
