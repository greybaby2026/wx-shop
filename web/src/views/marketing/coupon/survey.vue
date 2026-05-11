<template>
    <div class="ls-coupon">
        <div class="ls-card">
            <div class="title">优惠券概览</div>
            <div class="content">
                <el-row :gutter="20">
                    <el-col :span="6" class="statistics-col">
                        <div class="lighter">累计已领取优惠券/张</div>
                        <div class="m-t-8 font-size-30">
                            {{ statisticsData.receive_num }}
                        </div>
                    </el-col>
                    <el-col :span="6" class="statistics-col">
                        <div class="lighter">今日领取优惠券/张</div>
                        <div class="m-t-8 font-size-30">
                            {{ statisticsData.today_receive_num }}
                        </div>
                    </el-col>
                    <el-col :span="6" class="statistics-col">
                        <div class="lighter">累计已使用优惠券/张</div>
                        <div class="m-t-8 font-size-30">
                            {{ statisticsData.use_num }}
                        </div>
                    </el-col>
                    <el-col :span="6" class="statistics-col">
                        <div class="lighter">今日使用优惠券/张</div>
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
                    <div class="title">优惠券领取排行榜</div>
                    <div class="content">
                        <el-table :data="receiveTableData" size="mini">
                            <el-table-column type="index" label="排行" width="120" />
                            <el-table-column prop="name" label="优惠券名称" min-width="250" show-overflow-tooltip />
                            <el-table-column prop="num" label="已领取" />
                        </el-table>
                    </div>
                </div>
            </el-col>

            <el-col :span="12">
                <div class="ls-card">
                    <div class="title">优惠券使用排行榜</div>
                    <div class="content">
                        <el-table :data="useTableData" size="mini">
                            <el-table-column prop="date" label="排行" min-width="70" />
                            <el-table-column prop="name" label="优惠券名称" min-width="250" show-overflow-tooltip />
                            <el-table-column prop="num" label="已使用" />
                        </el-table>
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>
</template>

<script>
import { Component, Vue } from 'vue-property-decorator'
import { apiCouponSurvey } from '@/api/marketing/coupon'

@Component
export default class CouponSurvey extends Vue {
    /** S Data **/
    statisticsData = {} // 统计数据
    receiveTableData = [] // 优惠券领取排行榜列表
    useTableData = [] // 优惠券使用排行榜列表
    /** E Data **/

    /** S Life Cycle **/
    created() {
        apiCouponSurvey()
            .then(res => {
                this.statisticsData = res.base
                this.useTableData = res.sort_use
                this.receiveTableData = res.sort_receive
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
