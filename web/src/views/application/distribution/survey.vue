<template>
    <div class="ls-coupon">
        <div class="ls-card">
            <div class="title">佣金概览</div>
            <div class="content">
                <el-row :gutter="20">
                    <el-col :span="6" class="statistics-col">
                        <div class="lighter">累计已入账佣金</div>
                        <div class="m-t-8 font-size-30">
                            {{ earningsData.cumulative_rebated_commission }}
                        </div>
                    </el-col>
                    <el-col :span="6" class="statistics-col">
                        <div class="lighter">今日入账佣金</div>
                        <div class="m-t-8 font-size-30">
                            {{ earningsData.today_rebated_commission }}
                        </div>
                    </el-col>
                    <el-col :span="6" class="statistics-col">
                        <div class="lighter">待结算佣金</div>
                        <div class="m-t-8 font-size-30">
                            {{ earningsData.cumulative_unrefunded_commission }}
                        </div>
                    </el-col>
                    <el-col :span="6" class="statistics-col">
                        <div class="lighter">今日新增待结算佣金</div>
                        <div class="m-t-8 font-size-30">
                            {{ earningsData.today_unrefunded_commission }}
                        </div>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div class="ls-card m-t-16">
            <div class="title">分销概览</div>
            <div class="content">
                <el-row :gutter="20">
                    <el-col :span="6" class="statistics-col">
                        <div class="lighter">分销商</div>
                        <div class="m-t-8 font-size-30">
                            {{ distributionData.distribution_members }}
                        </div>
                    </el-col>
                    <el-col :span="6" class="statistics-col">
                        <div class="lighter">分销商占比</div>
                        <div class="m-t-8 font-size-30">
                            {{ distributionData.distribution_member_percentage.toFixed(2) }}%
                        </div>
                    </el-col>
                </el-row>
            </div>
        </div>

        <el-row :gutter="16" class="m-t-16">
            <el-col :span="12">
                <div class="ls-card">
                    <div class="title">分销商收入排行榜</div>
                    <div class="content">
                        <el-table :data="pagerTopMemberEarnings.lists" size="mini">
                            <el-table-column type="index" label="排行" min-width="70">
                                <template slot-scope="scope">
                                    <div
                                        class="icon"
                                        style="background: #f86056; color: #fff"
                                        v-if="
                                            scope.$index +
                                                (pagerTopMemberEarnings._page - 1) * pagerTopMemberEarnings._size ==
                                            0
                                        "
                                    >
                                        {{
                                            scope.$index +
                                            1 +
                                            (pagerTopMemberEarnings._page - 1) * pagerTopMemberEarnings._size
                                        }}
                                    </div>
                                    <div
                                        class="icon"
                                        style="background: #fc8d2e; color: #fff"
                                        v-if="
                                            scope.$index +
                                                (pagerTopMemberEarnings._page - 1) * pagerTopMemberEarnings._size ==
                                            1
                                        "
                                    >
                                        {{
                                            scope.$index +
                                            1 +
                                            (pagerTopMemberEarnings._page - 1) * pagerTopMemberEarnings._size
                                        }}
                                    </div>
                                    <div
                                        class="icon"
                                        style="background: #fcbc2e; color: #fff"
                                        v-if="
                                            scope.$index +
                                                (pagerTopMemberEarnings._page - 1) * pagerTopMemberEarnings._size ==
                                            2
                                        "
                                    >
                                        {{
                                            scope.$index +
                                            1 +
                                            (pagerTopMemberEarnings._page - 1) * pagerTopMemberEarnings._size
                                        }}
                                    </div>
                                    <div
                                        class="icon"
                                        v-if="
                                            scope.$index +
                                                (pagerTopMemberEarnings._page - 1) * pagerTopMemberEarnings._size >=
                                            3
                                        "
                                    >
                                        {{
                                            scope.$index +
                                            1 +
                                            (pagerTopMemberEarnings._page - 1) * pagerTopMemberEarnings._size
                                        }}
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column label="用户信息" min-width="120" show-overflow-tooltip>
                                <template slot-scope="scope">
                                    <div class="flex">
                                        <el-image :src="scope.row.avatar" style="width: 34px; height: 34px"> </el-image>
                                        <span>{{ scope.row.nickname }}</span>
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column prop="sum_earnings" label="已入账佣金" />
                        </el-table>
                    </div>
                    <div class="flex row-right m-t-16 row-right">
                        <ls-pagination v-model="pagerTopMemberEarnings" @change="getTopEarnings" />
                    </div>
                </div>
            </el-col>

            <el-col :span="12">
                <div class="ls-card">
                    <div class="title">分销商上下级人数排行</div>
                    <div class="content">
                        <el-table :data="pagerTopMemberFans.lists" size="mini">
                            <el-table-column type="index" label="排行" min-width="70">
                                <template slot-scope="scope">
                                    <div
                                        class="icon"
                                        style="background: #f86056; color: #fff"
                                        v-if="
                                            scope.$index + (pagerTopMemberFans._page - 1) * pagerTopMemberFans._size ==
                                            0
                                        "
                                    >
                                        {{
                                            scope.$index + 1 + (pagerTopMemberFans._page - 1) * pagerTopMemberFans._size
                                        }}
                                    </div>
                                    <div
                                        class="icon"
                                        style="background: #fc8d2e; color: #fff"
                                        v-if="
                                            scope.$index + (pagerTopMemberFans._page - 1) * pagerTopMemberFans._size ==
                                            1
                                        "
                                    >
                                        {{
                                            scope.$index + 1 + (pagerTopMemberFans._page - 1) * pagerTopMemberFans._size
                                        }}
                                    </div>
                                    <div
                                        class="icon"
                                        style="background: #fcbc2e; color: #fff"
                                        v-if="
                                            scope.$index + (pagerTopMemberFans._page - 1) * pagerTopMemberFans._size ==
                                            2
                                        "
                                    >
                                        {{
                                            scope.$index + 1 + (pagerTopMemberFans._page - 1) * pagerTopMemberFans._size
                                        }}
                                    </div>
                                    <div
                                        class="icon"
                                        v-if="
                                            scope.$index + (pagerTopMemberFans._page - 1) * pagerTopMemberFans._size >=
                                            3
                                        "
                                    >
                                        {{
                                            scope.$index + 1 + (pagerTopMemberFans._page - 1) * pagerTopMemberFans._size
                                        }}
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column label="用户信息" min-width="120" show-overflow-tooltip>
                                <template slot-scope="scope">
                                    <div class="flex">
                                        <el-image :src="scope.row.avatar" style="width: 34px; height: 34px"> </el-image>
                                        <span class="m-l-10">{{ scope.row.nickname }}</span>
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column prop="fans" label="下级人数" />
                        </el-table>
                    </div>
                    <div class="flex row-right m-t-16 row-right">
                        <ls-pagination v-model="pagerTopMemberFans" @change="getTopFans" />
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiDistributionData, apiTopMemberEarnings, apiTopMemberFans } from '@/api/distribution/distribution'
import { RequestPaging } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'
@Component({
    components: {
        LsPagination
    }
})
export default class CouponSurvey extends Vue {
    /** S Data **/
    earningsData = {} // 佣金概览数据统计
    //分销概览谁统计
    distributionData = {
        distribution_members: 0, // 分销会员数量
        distribution_member_percentage: 0 // 分销会员占比
    }
    topMemberData = [] // 分销会员排行榜列表
    topGoodsData = [] // 分销商品排行榜列表

    pagerTopMemberEarnings: RequestPaging = new RequestPaging()
    pagerTopMemberFans: RequestPaging = new RequestPaging()
    /** E Data **/

    // 分销商收入排行榜top50
    getTopEarnings(): void {
        this.pagerTopMemberEarnings
            .request({
                callback: apiTopMemberEarnings
            })
            .then((res: any) => {})
    }

    // 分销商下级人数排行榜top50
    getTopFans(): void {
        this.pagerTopMemberFans
            .request({
                callback: apiTopMemberFans
            })
            .then((res: any) => {})
    }

    /** S Life Cycle **/
    created() {
        this.getTopEarnings()
        this.getTopFans()

        apiDistributionData()
            .then(res => {
                this.earningsData = res.earnings_data
                this.distributionData = res.distribution_data
                this.topGoodsData = res.top_member_earings
                this.topMemberData = res.top_member_fans
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
        font-size: 16px;
        font-weight: 500;
    }

    .content {
        margin-top: 20px;
    }

    .statistics-col {
        text-align: center;
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
</style>
