<!-- 流量分析 -->
<template>
    <div class="user">
        <div class="ls-card m-t-16">
            <el-table
                :data="goodsTopData"
                size="mini"
                :default-sort="{
                    prop: 'visit num amount',
                    order: 'descending'
                }"
            >
                <el-table-column label="商品信息" min-width="300" show-overflow-tooltip>
                    <template slot-scope="scope">
                        <div class="flex">
                            <el-image :src="scope.row.image" style="width: 34px; height: 34px" class="flex-none">
                            </el-image>
                            <div class="m-l-10 line-1">
                                {{ scope.row.name }}
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="visit" sortable label="浏览量"> </el-table-column>
                <el-table-column prop="num" sortable label="销量"> </el-table-column>
                <el-table-column prop="amount" sortable label="销售额">
                    <template slot-scope="scope"> ¥{{ scope.row.amount }} </template>
                </el-table-column>
            </el-table>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiGoodsTop } from '@/api/data/data'

@Component
export default class User extends Vue {
    /** S Data **/
    goodsTopData = []

    /** E Data **/

    /** S Methods **/

    onReset() {
        this.getDataCenterVisit()
    }

    // 获取数据
    getDataCenterVisit() {
        apiGoodsTop({}).then(res => {
            this.goodsTopData = res
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
.user {
}
</style>
