<!-- 流量分析 -->
<template>
    <div class="flow-analysis">
        <div class="ls-card ls-card-top">
            <div class="journal-search m-t-16">
                <el-form ref="formRef" inline :model="summary" label-width="80px" size="small">
                    <el-form-item label="统计时间">
                        <el-date-picker
                            v-model="month"
                            format="yyyy 年 MM 月"
                            value-format="yyyy-MM"
                            type="month"
                            placeholder="选择月"
                        >
                        </el-date-picker>
                    </el-form-item>

                    <el-button size="small" type="primary" @click="getDataCenterVisit">查询</el-button>
                    <el-button size="small" type="" @click="onReset">重置</el-button>
                </el-form>
            </div>
        </div>
        <div class="ls-card m-t-16">
            <div class="card-title">数据汇总</div>
            <div class="card-content m-t-24">
                <el-row :gutter="20">
                    <el-col :span="6" class="flex-col col-center">
                        <div class="lighter m-b-8">访问量</div>
                        <div class="font-size-30">{{ summary.visit }}</div>
                    </el-col>
                    <el-col :span="6" class="flex-col col-center">
                        <div class="lighter m-b-8">访客数</div>
                        <div class="font-size-30">{{ summary.visitor }}</div>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div class="ls-card m-t-24 ls-chart--visitors">
            <div class="card-title">访问量</div>
            <div class="content">
                <e-chart class="chart" :option="visitor" />
            </div>
        </div>

        <div class="ls-card m-t-24 ls-chart--turnover">
            <div class="card-title">访客数</div>
            <div class="content">
                <e-chart class="chart" :option="business" />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import * as echarts from 'echarts/core'
import { BarChart, LineChart, PieChart } from 'echarts/charts'
import { GridComponent, TitleComponent, LegendComponent, PolarComponent } from 'echarts/components'
import { CanvasRenderer } from 'echarts/renderers'
import { apiDataCenterVisit } from '@/api/data/data'
echarts.use([
    BarChart,
    GridComponent,
    CanvasRenderer,
    TitleComponent,
    LegendComponent,
    PolarComponent,
    LineChart,
    PieChart
])
@Component
export default class FlowAnalysis extends Vue {
    /** S Data **/
    summary = {
        visit: '',
        visitor: ''
    }

    month = new Date().getFullYear() + '-' + (new Date().getMonth() + 1)

    visitor = {
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data: ['访客']
        },
        xAxis: {
            type: 'category',
            data: ['周一', '周二', '周三', '周四', '周五', '周六', '周日'],
            splitLine: {
                show: true, // 是否显示分隔线。默认数值轴显示，类目轴不显示
                interval: '1' // 坐标轴刻度标签的显示间隔，在类目轴中有效.0显示所有
            }
        },
        yAxis: {
            type: 'value'
            // type: "category"
        },
        series: [
            {
                name: '访客',
                type: 'line',
                stack: '总量',
                data: []
            }
        ]
    }

    business = {
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data: ['浏览量']
        },
        xAxis: {
            type: 'category',
            data: ['周一', '周二', '周三', '周四', '周五', '周六', '周日'],
            splitLine: {
                show: true, // 是否显示分隔线。默认数值轴显示，类目轴不显示
                interval: '1' // 坐标轴刻度标签的显示间隔，在类目轴中有效.0显示所有
            }
        },
        yAxis: {
            type: 'value'
            // type: "category"
        },
        series: [
            {
                name: '浏览量',
                type: 'line',
                stack: '总量',
                data: []
            }
        ]
    }

    // $refs!: { formRef: any }
    /** E Data **/

    /** S Methods **/

    onReset() {
        this.month = new Date().getFullYear() + '-' + (new Date().getMonth() + 1)
        this.getDataCenterVisit()
    }

    // 获取数据
    getDataCenterVisit() {
        // 清空echarts 数据
        this.business.xAxis.data = []
        this.visitor.xAxis.data = []

        apiDataCenterVisit({ month: this.month }).then(res => {
            res.user.list[0].data = res.user.list[0].data.reverse()
            res.visit.list[0].data = res.visit.list[0].data.reverse()

            this.summary = res.summary
            this.business.xAxis.data = res.user.date.reverse()
            this.visitor.xAxis.data = res.visit.date.reverse()

            res.user.list.forEach((item: any, index: number) => {
                this.business.series[index].data = item.data
                this.business.series[index].name = item.name
                this.business.legend.data[index] = item.name
            })

            res.visit.list.forEach((item: any, index: number) => {
                this.visitor.series[index].data = item.data
                this.visitor.series[index].name = item.name
                this.visitor.legend.data[index] = item.name
            })
        })
    }
    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.getDataCenterVisit()
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.flow-analysis {
    .ls-card {
        .card-title {
            font-size: 14px;
            font-weight: 500;
        }
    }

    .ls-card-top {
        padding-bottom: 0;
    }

    .ls-chart--turnover,
    .ls-chart--visitors {
        height: 460px;
        min-width: 500px;

        .chart {
            height: 400px;
        }
    }
}
</style>
