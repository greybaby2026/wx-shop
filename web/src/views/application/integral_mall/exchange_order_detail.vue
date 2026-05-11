<template>
    <div class="p-b-20">
        <header>
            <div class="ls-card">
                <el-page-header @back="$router.go(-1)" content="订单详情" />
            </div>

            <!-- 头部 -->
            <div class="flex m-t-24">
                <!-- 订单信息 -->
                <div class="ls-card flex flex-wrap col-stretch">
                    <div style="width: 100%">
                        <div class="nr weight-500 m-b-20 title">订单信息</div>

                        <div class="flex col-top">
                            <el-form ref="form" :model="orderData" label-width="120px" size="small">
                                <el-form-item label="订单状态">
                                    {{ orderData.order_status_desc }}
                                </el-form-item>
                                <el-form-item label="订单编号">
                                    {{ orderData.sn }}
                                </el-form-item>
                                <el-form-item label="兑换类型">
                                    {{ orderData.exchange_type_desc }}
                                </el-form-item>
                                <el-form-item label="下单时间">
                                    {{ orderData.create_time }}
                                </el-form-item>
                                <el-form-item label="买家留言">
                                    {{ orderData.user_remark || '-' }}
                                </el-form-item>
                            </el-form>
                            <el-form
                                ref="form"
                                style="margin-left: 20vw"
                                :model="orderData"
                                label-width="120px"
                                size="small"
                            >
                                <el-form-item label="支付状态">
                                    {{ orderData.pay_status_desc }}
                                </el-form-item>
                                <el-form-item label="支付方式">
                                    {{ orderData.pay_way_desc || '-' }}
                                </el-form-item>
                                <el-form-item label="支付时间">
                                    {{ orderData.pay_time || '-' }}
                                </el-form-item>
                                <el-form-item label="完成时间">
                                    {{ orderData.confirm_take_time || '-' }}
                                </el-form-item>
                            </el-form>
                        </div>
                    </div>

                    <div class="flex col-bottom" style="width: 100%">
                        <div class="border-top flex col-bottom row-left p-t-24" style="width: 100%; height: 57px">
                            <!-- 取消订单 -->
                            <ls-dialog
                                title="取消订单"
                                class="inline m-l-24"
                                v-if="orderData.admin_btns.cancel_btn"
                                :content="`确定取消订单(${orderData.sn})吗?请谨慎操作`"
                                @confirm="orderCancel"
                            >
                                <el-button slot="trigger" size="small"> 取消订单 </el-button>
                            </ls-dialog>

                            <!-- 物流查询 -->
                            <order-logistics
                                class="m-l-24"
                                v-if="orderData.admin_btns.delivery_btn"
                                :flag="false"
                                :id="id"
                            >
                                <el-button slot="trigger" size="small" type="primary"> 物流查询 </el-button>
                            </order-logistics>

                            <!-- 订单发货 -->
                            <order-logistics
                                class="m-l-24"
                                @update="getOrderDetail"
                                :flag="true"
                                :id="id"
                                v-if="orderData.admin_btns.to_ship_btn"
                            >
                                <el-button slot="trigger" size="small" type="primary"> 发货 </el-button>
                            </order-logistics>

                            <!-- 取消订单 -->
                            <ls-dialog
                                class="inline m-l-24"
                                title="确认收货"
                                :content="`确定确认收货吗?请谨慎操作`"
                                @confirm="orderConfirm"
                                v-if="orderData.admin_btns.confirm_btn"
                            >
                                <el-button slot="trigger" size="small" type="primary"> 确认收货 </el-button>
                            </ls-dialog>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <section>
            <!-- 用户及收货信息 -->
            <div class="ls-card m-t-24 flex flex-wrap col-stretch" style="height: auto">
                <div style="width: 100%">
                    <div class="nr weight-500 m-b-20 title">买家信息</div>
                    <div class="flex col-top">
                        <el-form ref="form" :model="orderData" label-width="120px" size="small">
                            <el-form-item label="会员昵称">
                                <router-link
                                    :to="{
                                        path: '/user/user_details',
                                        query: {
                                            id: this.orderData.user_id
                                        }
                                    }"
                                >
                                    {{ orderData.user.nickname }}
                                </router-link>
                            </el-form-item>
                            <el-form-item label="手机号码">
                                {{ orderData.user.mobile || '-' }}
                            </el-form-item>
                            <el-form-item label="注册时间">
                                {{ orderData.user.create_time }}
                            </el-form-item>
                        </el-form>
                        <el-form
                            ref="form"
                            :model="orderData"
                            label-width="120px"
                            size="small"
                            style="margin-left: 20vw"
                        >
                            <el-form-item label="会员编号">
                                {{ orderData.user.sn }}
                            </el-form-item>
                            <el-form-item label="性别">
                                {{ orderData.user.sex }}
                            </el-form-item>
                        </el-form>
                    </div>
                </div>
            </div>
            <div class="ls-card m-t-24 flex flex-wrap col-stretch" style="height: auto">
                <div style="width: 100%">
                    <div class="nr weight-500 m-b-20 title">收货信息</div>
                    <div class="flex col-top">
                        <el-form ref="form" :model="orderData" label-width="120px" size="small">
                            <el-form-item label="收货人">
                                {{ orderData.address.contact }}
                            </el-form-item>
                            <el-form-item label="手机号">
                                {{ orderData.address.mobile }}
                            </el-form-item>
                            <el-form-item label="收货地址">
                                {{ orderData.delivery_address }}
                            </el-form-item>
                        </el-form>
                        <el-form
                            ref="form"
                            :model="orderData"
                            label-width="120px"
                            size="small"
                            style="margin-left: 20vw"
                        >
                            <el-form-item label="物流公司">
                                {{ orderData.express_name || '-' }}
                            </el-form-item>
                            <el-form-item label="快递单号">
                                {{ orderData.invoice_no || '-' }}
                            </el-form-item>
                            <el-form-item label="发货时间">
                                {{ orderData.express_time || '-' }}
                            </el-form-item>
                        </el-form>
                    </div>
                </div>
            </div>
            <!-- 商品信息 -->
            <div class="ls-card m-t-24">
                <div class="nr weight-500 m-b-20 title">商品信息</div>

                <el-table :data="getOrderGoods" ref="paneTable" style="width: 100%" size="mini">
                    <el-table-column label="商品信息">
                        <template v-slot="{ row }">
                            <div class="flex m-t-10">
                                <el-image
                                    :src="row.image"
                                    style="width: 58px; height: 58px"
                                    class="flex-none"
                                ></el-image>
                                <div class="m-l-8 flex-1">
                                    <div class="line-2">
                                        {{ row.name }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column label="市场价">
                        <template v-slot="{ row }"> ￥{{ +row.market_price }} </template>
                    </el-table-column>
                    <el-table-column label="兑换积分">
                        <template v-slot="{ row }">
                            <div class="flex">
                                {{ row.need_integral }}积分
                                <template v-if="row.exchange_way == 2"> + {{ +row.need_money }}元 </template>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column label="数量" prop="total_num"></el-table-column>
                    <el-table-column label="运费">
                        <template v-slot="{ row }"> ￥{{ row.express_money }} </template>
                    </el-table-column>
                    <el-table-column label="实付">
                        <template v-slot="{ row }">
                            <div class="flex">
                                {{ row.order_integral }}积分
                                <template v-if="row.order_amount > 0"> + {{ +row.order_amount }}元 </template>
                            </div>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </section>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import AreaSelect from '@/components/area-select.vue'
import OrderLogistics from '@/components/application/order-logistics.vue'
import {
    apiIntegralOrderDetail,
    apiIntegralGoodsConfirm,
    apiIntegralGoodsCancel
} from '@/api/application/integral_mall'
@Component({
    components: {
        LsDialog,
        AreaSelect,
        OrderLogistics
    }
})
export default class OrderDetail extends Vue {
    // S Data

    // 订单详情ID
    id: any = 0

    // 订单数据
    orderData: any = {
        address: {},
        user: {},
        admin_btns: {}
    }

    // E Data

    // S Methods
    // 获取订单详情
    getOrderDetail() {
        apiIntegralOrderDetail({ id: this.id }).then(res => {
            this.orderData = res
        })
    }

    // 取消订单
    orderCancel() {
        apiIntegralGoodsCancel({ id: this.id }).then(res => {
            this.getOrderDetail()
        })
    }

    // 确认收货
    orderConfirm() {
        apiIntegralGoodsConfirm({ id: this.id }).then(res => {
            this.getOrderDetail()
        })
    }

    // E Methods

    // 获取商品表格信息
    get getOrderGoods() {
        const { goods_snap, order_amount, total_num, order_integral } = this.orderData
        let goods: any[] = []
        if (goods_snap) {
            goods = [
                {
                    ...goods_snap,
                    order_amount,
                    total_num,
                    order_integral
                }
            ]
        }
        return goods
    }

    created() {
        this.id = this.$route.query.id
        this.id && this.getOrderDetail()
    }
}
</script>

<style lang="scss" scoped>
::v-deep .el-form .el-form-item {
    margin-bottom: 12px !important;
}

::v-deep .el-table__footer-wrapper tbody td {
    background: #fff !important;
}

::v-deep .el-table--border::after,
.el-table::before {
    position: static;
}

.username:hover {
    color: $--color-primary;
}

.title {
    width: 100%;
    padding-bottom: 20px;
    border-bottom: 1px solid #f2f2f2;
}
</style>
