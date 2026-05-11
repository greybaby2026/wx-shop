<template>
    <div class="ls-coupon">
        <!-- 签到数据 -->
        <div class="ls-card">
            <div class="title">签到数据</div>
            <div class="content">
                <el-row :gutter="20">
                    <el-col :span="6" class="statistics-col">
                        <div class="lighter">累计签到次数/次</div>
                        <div class="m-t-8 font-size-30">
                            {{ sign_data.total_sign }}
                        </div>
                    </el-col>
                    <el-col :span="6" class="statistics-col">
                        <div class="lighter">累计奖励积分/积分</div>
                        <div class="m-t-8 font-size-30">
                            {{ sign_data.total_integral }}
                        </div>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div class="ls-card m-t-16">
            <div id="main" ref="main" :style="{ width: '100%', height: '300px' }"></div>
        </div>

        <!-- 用户签到排行榜 -->
        <el-row :gutter="16" class="m-t-16">
            <el-col :span="12">
                <div class="ls-card">
                    <div class="title">用户签到排行榜</div>
                    <div class="content">
                        <el-table :data="top_data.top_num" size="mini">
                            <el-table-column type="index" label="排行" min-width="70">
                                <template slot-scope="scope">
                                    <div class="icon" style="background: #f86056; color: #fff" v-if="scope.$index == 0">
                                        {{ scope.$index + 1 }}
                                    </div>
                                    <div class="icon" style="background: #fc8d2e; color: #fff" v-if="scope.$index == 1">
                                        {{ scope.$index + 1 }}
                                    </div>
                                    <div class="icon" style="background: #fcbc2e; color: #fff" v-if="scope.$index == 2">
                                        {{ scope.$index + 1 }}
                                    </div>
                                    <div class="icon" v-if="scope.$index >= 3">
                                        {{ scope.$index + 1 }}
                                    </div>
                                </template>
                            </el-table-column>
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
                            <el-table-column prop="num" label="累计签到次数" />
                        </el-table>
                    </div>
                </div>
            </el-col>

            <!-- 用户奖励排行榜 -->
            <el-col :span="12">
                <div class="ls-card">
                    <div class="title">用户奖励排行榜</div>
                    <div class="content">
                        <el-table :data="top_data.top_award" size="mini">
                            <el-table-column type="index" label="排行" min-width="70">
                                <template slot-scope="scope">
                                    <div class="icon" style="background: #f86056; color: #fff" v-if="scope.$index == 0">
                                        {{ scope.$index + 1 }}
                                    </div>
                                    <div class="icon" style="background: #fc8d2e; color: #fff" v-if="scope.$index == 1">
                                        {{ scope.$index + 1 }}
                                    </div>
                                    <div class="icon" style="background: #fcbc2e; color: #fff" v-if="scope.$index == 2">
                                        {{ scope.$index + 1 }}
                                    </div>
                                    <div class="icon" v-if="scope.$index >= 3">
                                        {{ scope.$index + 1 }}
                                    </div>
                                </template>
                            </el-table-column>
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
                            <el-table-column prop="amount" label="累计奖励积分" />
                        </el-table>
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>
</template>

<script>
import { Component, Vue } from 'vue-property-decorator'
import { apiCalendarData } from '@/api/marketing/calendar'
import * as echarts from 'echarts'

@Component
export default class CalendarSurvey extends Vue {
    /** S Data **/
    sign_data = {} // 统计数据
    top_data = {}

    // echarts数据
    option = {
        xAxis: {
            type: 'category',
            data: ['2021-01-01', '2021-02-01', '2021-03-01', '2021-04-01', '2021-05-01', '2021-06-01', '2021-07-01']
        },
        yAxis: {
            type: 'value'
        },
        legend: {
            data: ['人数']
        },
        itemStyle: {
            // 点的颜色。
            color: 'red'
        },
        tooltip: {
            trigger: 'axis'
        },
        series: [
            {
                name: '人数',
                data: [0, 0, 0, 0, 0, 0, 0],
                type: 'line',
                smooth: true
            }
        ]
    }

    /** E Data **/

    /** S Life Cycle **/
    mounted() {
        // 获取展示echarts的标签并设置echarts数据
        const chartDom = document.getElementById('main')
        const myChart = echarts.init(chartDom)
        myChart.setOption(this.option)

        // 获取签到预览的数据
        apiCalendarData()
            .then(res => {
                this.top_data = res.top_data

                this.sign_data = res.sign_data
                this.topUser = res.top_user
                this.topRule = res.top_rule

                // 清空echarts 数据
                this.option.xAxis.data = []
                this.option.series[0].data = []

                // 写入从后台拿来的数据
                res.recent_data.forEach((item, index) => {
                    this.option.xAxis.data.push(item.date)
                    this.option.series[0].data.push(item.num)

                    if (index + 1 == res.recent_data.length) {
                        myChart.setOption(this.option, true)
                    }
                })
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

    .icon {
        width: 25px;
        height: 25px;
        color: #333;
        border-radius: 2px;
        background: #f5f5f5;
        font-family: 'PingFang SC';
        font-weight: normal;
        font-size: 12px;
        line-height: 25px;
        text-align: center;
    }
}
</style>
