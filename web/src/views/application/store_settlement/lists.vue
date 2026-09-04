<template>
    <div class="admin">
        <div class="ls-card">
            <div class="m-t-20">
                <el-form :inline="true" :model="form" size="small">
                    <el-form-item label="账单状态">
                        <el-select v-model="form.status" placeholder="全部" style="width: 130px">
                            <el-option label="全部" value=""></el-option>
                            <el-option label="待确认" :value="0"></el-option>
                            <el-option label="已确认" :value="1"></el-option>
                            <el-option label="已付款" :value="2"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="周期范围" class="m-l-24">
                        <el-date-picker
                            v-model="dateRange"
                            type="daterange"
                            value-format="yyyy-MM-dd"
                            range-separator="至"
                            start-placeholder="开始日期"
                            end-placeholder="结束日期"
                            size="small"
                        />
                    </el-form-item>
                    <el-form-item class="m-l-24">
                        <el-button type="primary" @click="search">查询</el-button>
                        <el-button @click="resetSearch">重置</el-button>
                        <el-button type="success" @click="showGenerate = true">生成账单</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="ls-card m-t-16">
            <div class="m-t-24">
                <el-table :data="pager.lists" v-loading="pager.loading" style="width: 100%" size="mini">
                    <el-table-column prop="sn" label="账单编号" min-width="170" />
                    <el-table-column prop="period_type_desc" label="周期" width="60" />
                    <el-table-column label="周期范围" width="190">
                        <template slot-scope="scope">{{ scope.row.start_date }} ~ {{ scope.row.end_date }}</template>
                    </el-table-column>
                    <el-table-column prop="pay_store_name" label="付款门店" min-width="130" />
                    <el-table-column prop="receive_store_name" label="收款门店" min-width="130" />
                    <el-table-column prop="amount" label="货款金额" width="100">
                        <template slot-scope="scope">￥{{ scope.row.amount }}</template>
                    </el-table-column>
                    <el-table-column prop="order_count" label="订单数" width="80" />
                    <el-table-column prop="status_desc" label="状态" width="90">
                        <template slot-scope="scope">
                            <el-tag size="mini" :type="['warning', 'primary', 'success'][scope.row.status]">{{ scope.row.status_desc }}</el-tag>
                        </template>
                    </el-table-column>
                    <el-table-column prop="create_time" label="创建时间" width="160" />
                    <el-table-column label="操作" min-width="150">
                        <template slot-scope="scope">
                            <el-button type="text" size="small" @click="openDetail(scope.row)">详情</el-button>
                            <el-button v-if="scope.row.status === 0" type="text" size="small" @click="onConfirm(scope.row)">确认</el-button>
                            <el-button v-if="scope.row.status === 1" type="text" size="small" @click="onMarkPaid(scope.row)">标记付款</el-button>
                        </template>
                    </el-table-column>
                </el-table>
                <div class="m-t-24 pagination">
                    <ls-pagination v-model="pager" @change="getList" />
                </div>
            </div>
        </div>

        <!-- 生成账单弹窗 -->
        <el-dialog title="生成结算账单" :visible.sync="showGenerate" width="440px">
            <el-form label-width="80px" size="small">
                <el-form-item label="周期类型">
                    <el-radio-group v-model="genForm.period_type">
                        <el-radio :label="1">按日</el-radio>
                        <el-radio :label="2">按周</el-radio>
                        <el-radio :label="3">按月</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="选择日期">
                    <el-date-picker v-model="genForm.date" type="date" value-format="yyyy-MM-dd" placeholder="周期内任意日期" style="width: 100%" />
                </el-form-item>
                <el-form-item>
                    <span style="color: #999; font-size: 12px">将汇总周期内所有未结算的跨店核销明细，按门店组合自动生成账单；已生成过的周期会自动跳过。</span>
                </el-form-item>
            </el-form>
            <div slot="footer">
                <el-button size="small" @click="showGenerate = false">取消</el-button>
                <el-button type="primary" size="small" :loading="genLoading" @click="onGenerate">生成</el-button>
            </div>
        </el-dialog>

        <!-- 账单详情弹窗 -->
        <el-dialog title="账单详情" :visible.sync="showDetail" width="780px">
            <template v-if="detail.bill && detail.bill.id">
                <el-descriptions :column="3" size="small" border>
                    <el-descriptions-item label="账单编号">{{ detail.bill.sn }}</el-descriptions-item>
                    <el-descriptions-item label="周期">{{ detail.bill.period_type_desc }}（{{ detail.bill.start_date }} ~ {{ detail.bill.end_date }}）</el-descriptions-item>
                    <el-descriptions-item label="状态">{{ detail.bill.status_desc }}</el-descriptions-item>
                    <el-descriptions-item label="付款门店">{{ detail.bill.pay_store_name }}</el-descriptions-item>
                    <el-descriptions-item label="收款门店">{{ detail.bill.receive_store_name }}</el-descriptions-item>
                    <el-descriptions-item label="货款金额">￥{{ detail.bill.amount }}</el-descriptions-item>
                    <el-descriptions-item label="订单数">{{ detail.bill.order_count }}</el-descriptions-item>
                    <el-descriptions-item label="创建时间">{{ detail.bill.create_time }}</el-descriptions-item>
                    <el-descriptions-item label="付款时间">{{ fmtTime(detail.bill.pay_time) }}</el-descriptions-item>
                </el-descriptions>
                <el-table :data="detail.details" size="mini" class="m-t-16" border>
                    <el-table-column prop="order_sn" label="订单编号" min-width="160" />
                    <el-table-column label="商品明细" min-width="250">
                        <template slot-scope="scope">
                            <div v-for="(g, i) in scope.row.detail" :key="i" class="line-1">
                                {{ g.goods_name }} ×{{ g.goods_num }} @￥{{ g.supply_price }} = ￥{{ g.amount }}
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="amount" label="结算金额" width="100">
                        <template slot-scope="scope">￥{{ scope.row.amount }}</template>
                    </el-table-column>
                    <el-table-column label="金额来源" width="90">
                        <template slot-scope="scope">
                            <el-tag size="mini" :type="scope.row.cost_source === 3 ? 'danger' : 'info'">
                                {{ ['协议价', '成本价', '待复核'][scope.row.cost_source - 1] || '未知' }}
                            </el-tag>
                        </template>
                    </el-table-column>
                </el-table>
            </template>
            <div slot="footer">
                <el-button size="small" @click="showDetail = false">关闭</el-button>
                <template v-if="detail.bill && detail.bill.status === 0">
                    <el-button type="primary" size="small" @click="onConfirm(detail.bill); showDetail = false">确认账单</el-button>
                </template>
                <template v-if="detail.bill && detail.bill.status === 1">
                    <el-button type="success" size="small" @click="onMarkPaid(detail.bill); showDetail = false">标记付款</el-button>
                </template>
            </div>
        </el-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import {
    apiStoreSettlementLists,
    apiStoreSettlementDetail,
    apiStoreSettlementGenerate,
    apiStoreSettlementConfirm,
    apiStoreSettlementMarkPaid
} from '@/api/application/store_settlement'
import { RequestPaging } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'

@Component({
    components: {
        LsPagination
    }
})
export default class StoreSettlementLists extends Vue {
    /** S Data **/
    form = {
        status: '' as any,
        start_date: '',
        end_date: ''
    }
    dateRange: any = []
    pager: RequestPaging = new RequestPaging()

    showGenerate = false
    genLoading = false
    genForm = {
        period_type: 1,
        date: ''
    }

    showDetail = false
    detail: any = {
        bill: {},
        details: []
    }
    /** E Data **/

    /** S Methods **/
    fmtTime(t: number) {
        if (!t || t <= 0) return '-'
        const d = new Date(t * 1000)
        const p = (n: number) => String(n).padStart(2, '0')
        return `${d.getFullYear()}-${p(d.getMonth() + 1)}-${p(d.getDate())} ${p(d.getHours())}:${p(d.getMinutes())}:${p(d.getSeconds())}`
    }

    search() {
        this.pager.page = 1
        this.getList()
    }

    resetSearch() {
        this.form.status = ''
        this.dateRange = []
        this.search()
    }

    getList() {
        this.form.start_date = this.dateRange && this.dateRange.length ? this.dateRange[0] : ''
        this.form.end_date = this.dateRange && this.dateRange.length ? this.dateRange[1] : ''
        this.pager
            .request({
                callback: apiStoreSettlementLists,
                params: { ...this.form }
            })
            .catch(() => {})
    }

    onGenerate() {
        if (!this.genForm.date) {
            this.$message.error('请选择日期')
            return
        }
        this.genLoading = true
        apiStoreSettlementGenerate({ ...this.genForm })
            .then((res: any) => {
                const count = res?.count ?? 0
                this.$message.success(count > 0 ? `已生成 ${count} 张账单` : '操作成功')
                this.showGenerate = false
                this.getList()
            })
            .catch(() => {})
            .finally(() => {
                this.genLoading = false
            })
    }

    openDetail(row: any) {
        apiStoreSettlementDetail({ id: row.id }).then((res: any) => {
            this.detail = res
            this.showDetail = true
        })
    }

    onConfirm(row: any) {
        this.$confirm(`确认账单 ${row.sn}（￥${row.amount}）？确认后可标记付款。`, '提示', { type: 'warning' })
            .then(() => apiStoreSettlementConfirm({ id: row.id }))
            .then(() => {
                this.$message.success('已确认')
                this.getList()
            })
            .catch(() => {})
    }

    onMarkPaid(row: any) {
        this.$confirm(`确认已完成账单 ${row.sn} 的货款支付（￥${row.amount}，${row.pay_store_name} → ${row.receive_store_name}）？`, '提示', { type: 'warning' })
            .then(() => apiStoreSettlementMarkPaid({ id: row.id }))
            .then(() => {
                this.$message.success('操作成功')
                this.getList()
            })
            .catch(() => {})
    }
    /** E Methods **/

    /** S Life Cycle **/
    created() {
        const today = new Date()
        const p = (n: number) => String(n).padStart(2, '0')
        this.genForm.date = `${today.getFullYear()}-${p(today.getMonth() + 1)}-${p(today.getDate())}`
        this.getList()
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.pagination {
    padding-right: 5%;
    display: flex;
    justify-content: flex-end;
}
</style>
