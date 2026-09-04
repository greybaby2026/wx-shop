<template>
    <div>
        <header>
            <div class="ls-card">
                <el-page-header @back="$router.go(-1)" content="ERP订单详情" />
            </div>
            <div class="flex m-t-24">
                <div class="ls-card flex flex-wrap col-stretch" style="min-height:300px">
                    <div style="width:100%">
                        <div class="nr weight-500 m-b-20 title">订单信息</div>
                        <div class="flex col-top">
                            <el-form ref="form" :model="orderData" label-width="100px" size="small">
                                <el-form-item label="订单状态">{{ orderData.order_status_desc }}</el-form-item>
                                <el-form-item label="订单编号">{{ orderData.sn }}</el-form-item>
                                <el-form-item label="订单类型">{{ orderData.order_type_desc }}</el-form-item>
                                <el-form-item label="下单时间">{{ orderData.create_time }}</el-form-item>
                                <el-form-item label="支付方式">{{ orderData.pay_way_desc }}</el-form-item>
                            </el-form>
                            <el-form ref="form" style="margin-left:10vw" :model="orderData" label-width="100px" size="small">
                                <el-form-item label="支付状态">{{ orderData.pay_status_desc }}</el-form-item>
                                <el-form-item label="支付时间">{{ orderData.pay_time }}</el-form-item>
                                <el-form-item label="订单来源">{{ orderData.order_terminal_desc }}</el-form-item>
                                <el-form-item label="用户备注">{{ orderData.user_remark }}</el-form-item>
                            </el-form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <section>
            <div class="ls-card m-t-24 flex flex-wrap col-stretch">
                <div style="width:100%">
                    <div class="nr weight-500 m-b-20 title">用户信息</div>
                    <el-form ref="form" :model="orderData" size="small" label-width="100px">
                        <el-form-item label="用户昵称">{{ orderData.nickname }} [{{ orderData.user_sn }}]</el-form-item>
                        <el-form-item label="联系电话">{{ orderData.mobile }}</el-form-item>
                    </el-form>
                </div>
            </div>
            <div class="ls-card m-t-24">
                <div class="nr weight-500 m-b-20 title">商品信息</div>
                <el-table :data="orderData.order_goods" :header-cell-style="{background:'#f5f8ff',border:'none',color:'#666',height:'60px'}" style="width:100%" size="mini">
                    <el-table-column label="商品信息" min-width="240">
                        <template slot-scope="scope">
                            <div class="flex m-t-10">
                                <el-image :src="scope.row.goods_image" style="width:78px;height:78px" class="flex-none" />
                                <div style="width:100%" class="flex-col row-around m-l-10">
                                    <div class="line-2">{{ scope.row.goods_name }}</div>
                                    <div class="flex row-between"><text class="muted xs">{{ scope.row.spec_value_str }}</text></div>
                                </div>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="goods_price" label="单价" min-width="100" />
                    <el-table-column prop="goods_num" label="数量" min-width="80" />
                    <el-table-column prop="total_amount" label="小计" min-width="120" />
                </el-table>
            </div>
            <div class="ls-card m-t-24">
                <div class="nr weight-500 m-b-20 title">费用信息</div>
                <el-form ref="form" :model="orderData" label-width="120px" size="small" inline>
                    <el-form-item label="商品金额">￥{{ orderData.order_amount }}</el-form-item>
                    <el-form-item v-if="orderData.discount_amount > 0" label="优惠券">-￥{{ orderData.discount_amount }}</el-form-item>
                    <el-form-item v-if="orderData.deduct_amount > 0" label="活动余额抵扣" style="color:#e6a23c">-￥{{ orderData.deduct_amount }}</el-form-item>
                    <el-form-item label="实付款" style="font-weight:bold;font-size:16px;color:#e6a23c">￥{{ ((orderData.order_amount || 0) - (orderData.deduct_amount || 0)).toFixed(2) }}</el-form-item>
                </el-form>
            </div>
        </section>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiErpOrderDetail } from '@/api/order/order'

@Component
export default class ErpOrderDetail extends Vue {
    id: any = 0
    orderData: any = { order_goods: [] }

    created() { this.id = this.$route.query.id; this.id && this.getDetail() }

    getDetail() {
        apiErpOrderDetail({ id: this.id }).then((res: any) => {
            this.orderData = res
        }).catch(() => { this.$message.error('获取订单详情失败') })
    }
}
</script>

<style lang="scss" scoped>
::v-deep .el-form .el-form-item { margin-bottom: 12px !important; }
.title { font-size: 16px; }
.line-2 { overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
</style>
