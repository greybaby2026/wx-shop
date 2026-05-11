<template>
    <div class="ls-coupon">
        <!-- 充值数据 -->
        <div class="ls-card">
            <div class="title">充值数据</div>
            <div class="content">
                <el-row :gutter="20">
                    <el-col :span="6" class="statistics-col">
                        <div class="lighter">累计充值金额/元</div>
                        <div class="m-t-8 font-size-30">
                            {{ statisticsData.total_amount }}
                        </div>
                    </el-col>
                    <el-col :span="6" class="statistics-col">
                        <div class="lighter">累计充值人数/人次</div>
                        <div class="m-t-8 font-size-30">
                            {{ statisticsData.total_times }}
                        </div>
                    </el-col>
                </el-row>
            </div>
        </div>

        <!-- 用户充值排行榜 -->
        <el-row :gutter="16" class="m-t-16">
            <el-col :span="12">
                <div class="ls-card">
                    <div class="title">用户充值排行榜</div>
                    <div class="content">
                        <el-table :data="topUser" size="mini">
                            <el-table-column type="index" label="排行" width="120" />
                            <el-table-column label="用户昵称" min-width="120" show-overflow-tooltip>
                                <template slot-scope="scope">
                                    <div class="flex">
                                        <el-image :src="scope.row.avatar" style="width: 34px; height: 34px"></el-image>
                                        <div class="m-l-10">
                                            {{ scope.row.nickname }}
                                        </div>
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column prop="total_amount" label="累计充值金额" />
                        </el-table>
                    </div>
                </div>
            </el-col>

            <!-- 充值规则排行榜 -->
            <el-col :span="12">
                <div class="ls-card">
                    <div class="title">充值规则排行榜</div>
                    <div class="content">
                        <el-table :data="topRule" size="mini">
                            <el-table-column type="index" label="排行" width="120" />
                            <el-table-column prop="money" label="规则金额" min-width="120" show-overflow-tooltip />
                            <el-table-column prop="total_num" label="累计充值人数" />
                        </el-table>
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiRechargeData } from '@/api/application/recharge'

@Component
export default class RechargeSurvey extends Vue {
    /** S Data **/
    statisticsData = {} // 统计数据
    topRule = [] // 充值规则排行榜列表
    topUser = [] // 充值排行榜列表
    /** E Data **/

    /** S Life Cycle **/
    created() {
        apiRechargeData()
            .then(res => {
                this.statisticsData = res.recharge_data
                this.topUser = res.top_user
                this.topRule = res.top_rule
            })
            .catch(() => {
                this.$message.error('请求数据失败，请刷新重载!')
            })
    }
    /** S Life Cycle **/
}
</script>

<style lang="scss" scoped>
.ls-card {
    .title {
        font-size: 14px;
        font-weight: 500;
    }

    .content {
        margin-top: 20px;
    }

    .statistics-col {
        text-align: center;
    }
}
</style>
