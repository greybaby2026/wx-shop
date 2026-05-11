<template>
    <div>
        <div class="ls-card">
            <div class="title">活动数据</div>
            <div class="content">
                <el-row :gutter="20">
                    <el-col :span="4" class="statistics-col">
                        <div class="lighter">累计活动场次/场</div>
                        <div class="m-t-8 font-size-30">
                            {{ statisticsData.total_activity_num }}
                        </div>
                    </el-col>
                    <el-col :span="4" class="statistics-col">
                        <div class="lighter">累计浏览量/人次</div>
                        <div class="m-t-8 font-size-30">
                            {{ statisticsData.total_browse_volume }}
                        </div>
                    </el-col>
                    <el-col :span="4" class="statistics-col">
                        <div class="lighter">累计销售金额/元</div>
                        <div class="m-t-8 font-size-30">
                            {{ statisticsData.total_sales_amount }}
                        </div>
                    </el-col>
                    <el-col :span="4" class="statistics-col">
                        <div class="lighter">今日使用优惠券/张</div>
                        <div class="m-t-8 font-size-30">
                            {{ statisticsData.total_sales_volume }}
                        </div>
                    </el-col>
                    <el-col :span="4" class="statistics-col">
                        <div class="lighter">累计成交订单/笔</div>
                        <div class="m-t-8 font-size-30">
                            {{ statisticsData.total_closing_order }}
                        </div>
                    </el-col>
                    <el-col :span="4" class="statistics-col">
                        <div class="lighter">累计成团数/个</div>
                        <div class="m-t-8 font-size-30">
                            {{ statisticsData.today_use_num }}
                        </div>
                    </el-col>
                </el-row>
            </div>
        </div>

        <el-row :gutter="16" class="m-t-16">
            <el-col :span="12">
                <div class="ls-card">
                    <div class="title">活动浏览量排行榜</div>
                    <div class="content">
                        <el-table :data="receiveTableData" size="mini">
                            <el-table-column type="id" label="排行" width="120" />
                            <el-table-column prop="name" label="活动名称" min-width="250" show-overflow-tooltip />
                            <el-table-column prop="sales_volume" label="浏览量" />
                        </el-table>
                    </div>
                </div>
            </el-col>

            <el-col :span="12">
                <div class="ls-card">
                    <div class="title">活动销售额排行榜</div>
                    <div class="content">
                        <el-table :data="useTableData" size="mini">
                            <el-table-column prop="id" label="排行" min-width="70" />
                            <el-table-column prop="name" label="活动名称" min-width="250" show-overflow-tooltip />
                            <el-table-column prop="browse_volume" label="销售金额" />
                        </el-table>
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>
</template>

<script>
import { Component, Vue } from 'vue-property-decorator'
import { apiTeamSurvey } from '@/api/marketing/combination'

@Component
export default class TeamSurvey extends Vue {
    /** S Data **/
    statisticsData = {} // 统计数据
    receiveTableData = [] // 优惠券领取排行榜列表
    useTableData = [] // 优惠券使用排行榜列表
    /** E Data **/

    /** S Life Cycle **/
    created() {
        apiTeamSurvey()
            .then(res => {
                this.statisticsData = res.base
                this.useTableData = res.sort_browse_volume
                this.receiveTableData = res.sort_sales_volume
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
