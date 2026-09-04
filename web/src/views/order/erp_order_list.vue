<template>
    <div class="erp-order-list">
        <el-card class="search-card">
            <el-form :model="searchForm" label-width="80px" size="small" inline>
                <el-form-item label="订单编号">
                    <el-input v-model="searchForm.sn" placeholder="请输入订单编号" clearable style="width:200px" />
                </el-form-item>
                <el-form-item label="用户信息">
                    <el-input v-model="searchForm.user_info" placeholder="用户编号/昵称/手机号" clearable style="width:200px" />
                </el-form-item>
                <el-form-item label="下单时间">
                    <el-date-picker v-model="searchForm.create_time" type="daterange" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期" value-format="yyyy-MM-dd" style="width:280px" />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" size="small" @click="onSearch">查询</el-button>
                    <el-button size="small" @click="onReset">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>

        <el-card class="table-card m-t-16">
            <el-table :data="list" v-loading="loading" stripe border style="width:100%" size="small">
                <el-table-column prop="sn" label="订单编号" min-width="180" />
                <el-table-column prop="user_sn" label="用户编号" min-width="120" />
                <el-table-column prop="nickname" label="用户昵称" min-width="120" />
                <el-table-column prop="order_amount" label="订单金额" min-width="100" />
                <el-table-column prop="deduct_amount" label="活动抵扣" min-width="100">
                    <template slot-scope="scope">
                        <span v-if="scope.row.deduct_amount > 0" style="color:#e6a23c">-{{ scope.row.deduct_amount }}</span>
                        <span v-else>0</span>
                    </template>
                </el-table-column>
                <el-table-column label="实付金额" min-width="100">
                    <template slot-scope="scope">{{ (scope.row.order_amount - (scope.row.deduct_amount || 0)).toFixed(2) }}</template>
                </el-table-column>
                <el-table-column prop="pay_status_desc" label="支付状态" min-width="80" />
                <el-table-column prop="create_time" label="下单时间" min-width="160" />
                <el-table-column prop="user_remark" label="备注" min-width="200" show-overflow-tooltip />
                <el-table-column label="操作" min-width="80" fixed="right">
                    <template slot-scope="scope">
                        <el-button type="text" size="small" @click="goDetail(scope.row.id)">详情</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex row-between m-t-16">
                <div />
                <el-pagination
                    @size-change="onSizeChange"
                    @current-change="onPageChange"
                    :current-page="page"
                    :page-sizes="[10,20,50,100]"
                    :page-size="pageSize"
                    layout="total,sizes,prev,pager,next"
                    :total="total"
                />
            </div>
        </el-card>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiErpOrderList } from '@/api/order/order'

@Component
export default class ErpOrderList extends Vue {
    loading = false
    list: any[] = []
    page = 1
    pageSize = 20
    total = 0
    searchForm: any = { sn: '', user_info: '', create_time: null }

    mounted() { this.getList() }

    onSearch() { this.page = 1; this.getList() }
    onReset() { this.searchForm = { sn: '', user_info: '', create_time: null }; this.page = 1; this.getList() }
    onSizeChange(size: number) { this.pageSize = size; this.page = 1; this.getList() }
    onPageChange(page: number) { this.page = page; this.getList() }

    getList() {
        this.loading = true
        const params: any = { page_no: this.page, page_size: this.pageSize }
        if (this.searchForm.sn) params.sn = this.searchForm.sn
        if (this.searchForm.user_info) params.user_info = this.searchForm.user_info
        if (this.searchForm.create_time && this.searchForm.create_time.length === 2) {
            params.start_time = this.searchForm.create_time[0]
            params.end_time = this.searchForm.create_time[1]
        }
        apiErpOrderList(params).then((res: any) => {
            this.list = Array.isArray(res.lists) ? res.lists : []
            this.total = res.count || 0
        }).catch(() => { this.list = []; this.total = 0 }).finally(() => { this.loading = false })
    }

    goDetail(id: number) { this.$router.push({ path: '/order/erp_order_detail', query: { id } }) }
}
</script>

<style lang="scss" scoped>
.erp-order-list { .search-card { margin-bottom: 0; } .table-card { margin-top: 16px; } }
</style>
