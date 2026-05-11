<template>
    <div class="ls-home">
        <el-row :gutter="16" type="flex" class="ls-home__top col-stretch">
            <el-col :span="6">
                <div class="ls-card ls-top__store">
                    <div class="title weight-500">商城信息</div>
                    <div class="content">
                        <div class="flex">
                            <el-image
                                fit="scale-down"
                                style="width: 58px; height: 58px; border-radius: 50%"
                                :src="indexData.shop_info.logo"
                            >
                            </el-image>
                            <div class="m-l-20">
                                <div class="store-name sm flex weight-600">
                                    <span>{{ indexData.shop_info.name }}</span>
                                </div>
                                <div class="store-status m-t-14 flex">
                                    <span class="label">版本号：</span>
                                    <span>{{ $store.getters.config.version }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="store-time m-t-14 flex">
                            <!-- <span class="label">
                                
                                <el-popover placement="right-start" width="300" trigger="hover">
                                    <div class="flex">
                                        <el-image src=""
                                            style="width: 300px; height: 300px;border-radius: 20px;">
                                        </el-image>
                                    </div>
                                    <el-link type="primary" slot="reference">商城推广</el-link>
                                </el-popover>
                            </span> -->
                        </div>
                        <div class="flex m-l-40" v-if="$store.getters.config.document_status">
                            <el-tag type="success" class="m-r-8" effect="dark" size="small">
                                <a href="https://www.likeshop.cn/" target="_blank">官网 </a>
                            </el-tag>
                            <el-tag type="danger" class="m-l-4 m-r-4" effect="dark" size="small">
                                <a href="https://www.likeshop.cn/doc" target="_blank"> 开发文档 </a>
                            </el-tag>
                        </div>
                    </div>
                </div>
            </el-col>
            <el-col :span="18">
                <div class="ls-card ls-top__data">
                    <div class="title">今日数据</div>
                    <div class="content">
                        <el-row :gutter="20">
                            <el-col :span="6">
                                <div class="lighter">营业额（元）</div>
                                <div class="m-t-8 font-size-30">
                                    {{ indexData.today.today_order_amount }}
                                </div>
                            </el-col>
                            <el-col :span="6">
                                <div class="lighter">成交订单（笔）</div>
                                <div class="m-t-8 font-size-30">
                                    {{ indexData.today.today_order_num }}
                                </div>
                            </el-col>
                            <el-col :span="6">
                                <div class="lighter">访客数（人）</div>
                                <div class="m-t-8 font-size-30">
                                    {{ indexData.today.today_visitor }}
                                </div>
                            </el-col>
                            <el-col :span="6">
                                <div class="lighter">新增用户（人）</div>
                                <div class="m-t-8 font-size-30">
                                    {{ indexData.today.today_new_user }}
                                </div>
                            </el-col>
                        </el-row>
                    </div>
                </div>
            </el-col>
        </el-row>
        <div class="ls-card ls-home_todo m-t-16">
            <div class="title">待办事项</div>
            <div class="content">
                <el-row :gutter="20">
                    <el-col :span="6">
                        <div @click="goPage('order/order')" class="pointer">
                            <div class="lighter">待发货订单</div>
                            <div class="m-t-8 font-size-30">
                                {{ indexData.pending.wait_shipped }}
                            </div>
                        </div>
                    </el-col>
                    <el-col :span="6">
                        <div @click="goPage('/order/after_sales')" class="pointer">
                            <div class="lighter">待审核售后申请</div>
                            <div class="m-t-8 font-size-30">
                                {{ indexData.pending.wait_audit }}
                            </div>
                        </div>
                    </el-col>
                    <el-col :span="6">
                        <div @click="goPage('/goods/evaluation')" class="pointer">
                            <div class="lighter">待审核商品评价</div>
                            <div class="m-t-8 font-size-30">
                                {{ indexData.pending.wait_process }}
                            </div>
                        </div>
                    </el-col>
                    <el-col :span="6">
                        <div @click="goPage('/goods/lists')" class="pointer">
                            <div class="lighter">售罄商品</div>
                            <div class="m-t-8 font-size-30">
                                {{ indexData.pending.no_stock_goods }}
                            </div>
                        </div>
                    </el-col>
                </el-row>
            </div>
        </div>
        <el-row :gutter="16" class="ls-home__chart m-t-16">
            <el-col :span="12">
                <div class="ls-card ls-chart--turnover">
                    <div class="title">近15天营业额（元）</div>
                    <div class="content">
                        <e-chart class="chart" :option="business" />
                    </div>
                </div>
            </el-col>
            <el-col :span="12">
                <div class="ls-card ls-chart--visitors">
                    <div class="title">近15天访客数（人）</div>
                    <div class="content">
                        <e-chart class="chart" :option="visitor" />
                    </div>
                </div>
            </el-col>
        </el-row>
        <el-row :gutter="16" class="ls-home__rank m-t-16">
            <!-- 商品销量排行 -->
            <el-col :span="12">
                <div class="ls-card">
                    <div class="title">商品销量排行</div>
                    <div class="content">
                        <el-table :data="pagerTopGoods.lists" size="mini">
                            <el-table-column label="排名" min-width="70">
                                <template slot-scope="scope">
                                    <div
                                        class="icon"
                                        style="background: #f86056; color: #fff"
                                        v-if="scope.$index + (pagerTopGoods._page - 1) * pagerTopGoods._size == 0"
                                    >
                                        {{ scope.$index + 1 + (pagerTopGoods._page - 1) * pagerTopGoods._size }}
                                    </div>
                                    <div
                                        class="icon"
                                        style="background: #fc8d2e; color: #fff"
                                        v-if="scope.$index + (pagerTopGoods._page - 1) * pagerTopGoods._size == 1"
                                    >
                                        {{ scope.$index + 1 + (pagerTopGoods._page - 1) * pagerTopGoods._size }}
                                    </div>
                                    <div
                                        class="icon"
                                        style="background: #fcbc2e; color: #fff"
                                        v-if="scope.$index + (pagerTopGoods._page - 1) * pagerTopGoods._size == 2"
                                    >
                                        {{ scope.$index + 1 + (pagerTopGoods._page - 1) * pagerTopGoods._size }}
                                    </div>
                                    <div
                                        class="icon"
                                        v-if="scope.$index + (pagerTopGoods._page - 1) * pagerTopGoods._size >= 3"
                                    >
                                        {{ scope.$index + 1 + (pagerTopGoods._page - 1) * pagerTopGoods._size }}
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column label="商品信息" min-width="300" show-overflow-tooltip>
                                <template slot-scope="scope">
                                    <div class="flex">
                                        <el-image
                                            :src="scope.row.image"
                                            style="width: 34px; height: 34px"
                                            class="flex-none"
                                        >
                                        </el-image>
                                        <div class="m-l-10 line-1">
                                            {{ scope.row.name }}
                                        </div>
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column prop="total_num" label="销量"> </el-table-column>
                            <el-table-column prop="total_pay_price" label="销售额">
                                <template slot-scope="scope"> ¥{{ scope.row.total_pay_price }} </template>
                            </el-table-column>
                        </el-table>
                    </div>
                    <div class="flex row-right m-t-16 row-right">
                        <ls-pagination v-model="pagerTopGoods" @change="getTopGoods" />
                    </div>
                </div>
            </el-col>
            <!-- 用户购买能力排行 -->
            <el-col :span="12">
                <div class="ls-card">
                    <div class="title">用户购买能力排行</div>
                    <div class="content">
                        <el-table :data="pagerTopUser.lists" size="mini">
                            <el-table-column label="排名" min-width="70">
                                <template slot-scope="scope">
                                    <div
                                        class="icon"
                                        style="background: #f86056; color: #fff"
                                        v-if="scope.$index + (pagerTopUser._page - 1) * pagerTopUser._size == 0"
                                    >
                                        {{ scope.$index + 1 + (pagerTopUser._page - 1) * pagerTopUser._size }}
                                    </div>
                                    <div
                                        class="icon"
                                        style="background: #fc8d2e; color: #fff"
                                        v-if="scope.$index + (pagerTopUser._page - 1) * pagerTopUser._size == 1"
                                    >
                                        {{ scope.$index + 1 + (pagerTopUser._page - 1) * pagerTopUser._size }}
                                    </div>
                                    <div
                                        class="icon"
                                        style="background: #fcbc2e; color: #fff"
                                        v-if="scope.$index + (pagerTopUser._page - 1) * pagerTopUser._size == 2"
                                    >
                                        {{ scope.$index + 1 + (pagerTopUser._page - 1) * pagerTopUser._size }}
                                    </div>
                                    <div
                                        class="icon"
                                        v-if="scope.$index + (pagerTopUser._page - 1) * pagerTopUser._size >= 3"
                                    >
                                        {{ scope.$index + 1 + (pagerTopUser._page - 1) * pagerTopUser._size }}
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column label="用户信息" min-width="300" show-overflow-tooltip>
                                <template slot-scope="scope">
                                    <div class="flex">
                                        <el-image
                                            :src="scope.row.avatar"
                                            style="width: 34px; height: 34px"
                                            class="flex-none"
                                        >
                                        </el-image>
                                        <div class="m-l-10 line-1">
                                            {{ scope.row.nickname }}
                                        </div>
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column prop="total_num" label="成交单数"> </el-table-column>
                            <el-table-column prop="total_order_amount" label="消费金额">
                                <template slot-scope="scope"> ¥{{ scope.row.total_order_amount }} </template>
                            </el-table-column>
                        </el-table>
                    </div>
                    <div class="flex row-right m-t-16 row-right">
                        <ls-pagination v-model="pagerTopUser" @change="getTopUser" />
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import * as echarts from 'echarts/core'
import { BarChart, LineChart } from 'echarts/charts'
import { apiWorkbenchIndex, apiWorkbenchTopUser, apiWorkbenchTopGoods } from '@/api/home'
import LsPagination from '@/components/ls-pagination.vue'
import { GridComponent, TitleComponent, LegendComponent, PolarComponent } from 'echarts/components'
// 注意，新的接口中默认不再包含 Canvas 渲染器，需要显示引入，如果需要使用 SVG 渲染模式则使用 SVGRenderer
import { CanvasRenderer } from 'echarts/renderers'
import { RequestPaging } from '@/utils/util'

echarts.use([BarChart, GridComponent, CanvasRenderer, TitleComponent, LegendComponent, PolarComponent, LineChart])
@Component({
    components: {
        LsPagination
    }
})
export default class Home extends Vue {
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
            data: ['营业额', '商品数量', '订单数量']
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
        },
        series: [
            {
                name: '营业额',
                type: 'line',
                stack: '总量',
                data: []
            },
            {
                name: '商品数量',
                type: 'line',
                stack: '总量',
                data: []
            },
            {
                name: '订单数量',
                type: 'line',
                stack: '总量',
                data: []
            }
        ]
    }

    indexData: any = {
        shop_info: {
            logo: ''
        },
        today: {
            today_order_num: '',
            today_order_amount: '',
            today_new_user: '',
            today_visitor: ''
        },
        pending: {
            wait_shipped: '',
            wait_audit: '',
            wait_reply: '',
            no_stock_goods: ''
        }
    }

    pagerTopGoods: RequestPaging = new RequestPaging()
    pagerTopUser: RequestPaging = new RequestPaging()

    getWorkbenchIndexData() {
        // 清空echarts 数据
        this.business.xAxis.data = []
        this.business.legend.data = []
        // this.business.series = [];

        apiWorkbenchIndex({}).then(res => {
            this.indexData = res
            res.business15.list[0].data = res.business15.list[0].data.reverse()
            res.visitor15.list[0].data = res.visitor15.list[0].data.reverse()

            this.business.xAxis.data = res.business15.date.reverse()
            this.visitor.xAxis.data = res.visitor15.date.reverse()

            res.business15.list.forEach((item: any, index: number) => {
                this.business.series[index].data = item.data
                this.business.series[index].name = item.name
                this.business.legend.data[index] = item.name
            })

            res.visitor15.list.forEach((item: any, index: number) => {
                this.visitor.series[index].data = item.data
                this.visitor.series[index].name = item.name
            })
        })
    }

    goPage(url: string) {
        this.$router.push({
            path: url
        })
    }

    // 商品top50
    getTopGoods(): void {
        this.pagerTopGoods
            .request({
                callback: apiWorkbenchTopGoods
            })
            .then((res: any) => {})
    }

    // 用户top50
    getTopUser(): void {
        this.pagerTopUser
            .request({
                callback: apiWorkbenchTopUser
            })
            .then((res: any) => {})
    }

    created() {
        this.getWorkbenchIndexData()
        this.getTopGoods()
        this.getTopUser()
    }
}
</script>
<style lang="scss" scoped>
.ls-card {
    .title {
        font-size: 14px;
        font-weight: bold;
        padding-bottom: 20px;
        border-bottom: 1px solid $--background-color-base;
    }
    .content {
        margin-top: 20px;
        a {
            text-decoration: none;
            color: #fff;
        }
    }
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
.ls-home {
    .ls-home__top {
        .ls-top__store {
            .store-time {
                // padding: 3px 6px;
                display: inline-block;
                margin-left: 80px;
                background: #ebf1ff;
            }
            .label {
                flex: none;
                color: $--color-text-secondary;
            }
        }
        .ls-top__data {
            height: 100%;
            box-sizing: border-box;
        }
    }
    .ls-home_todo {
        margin-right: 0;
    }
    .ls-home__chart {
        .ls-chart--turnover,
        .ls-chart--visitors {
            height: 460px;
            // min-width: 800px;
            .chart {
                // width: 900px !important;
                height: 400px;
            }
        }
    }
}
</style>
