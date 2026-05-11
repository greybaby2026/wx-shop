<template>
    <div>
        <header>
            <div class="ls-card">
                <el-page-header @back="$router.go(-1)" content="售后详情" />
            </div>

            <!-- 头部 -->
            <div class="flex m-t-24">
                <!-- 订单信息 -->
                <div class="ls-card flex flex-wrap col-stretch">
                    <div style="width: 100%">
                        <div class="nr weight-500 title">售后信息</div>

                        <div class="flex col-top p-20">
                            <el-form ref="form" :model="orderData" label-width="120px" size="small">
                                <el-form-item label="售后状态">
                                    <div>
                                        {{ orderData.after_sale.status_desc }}
                                    </div>

                                    <el-image
                                        style="width: 50px; height: 50px"
                                        fit="fit"
                                        v-if="orderData.after_sale.refund_image"
                                        :src="orderData.after_sale.refund_image"
                                    >
                                    </el-image>
                                </el-form-item>

                                <el-form-item label="售后单号">
                                    {{ orderData.after_sale.sn || '' }}
                                </el-form-item>
                                <el-form-item label="售后类型">
                                    {{ orderData.after_sale.refund_type_desc }}
                                </el-form-item>
                                <el-form-item label="售后数量">
                                    {{ orderData.goods_info.order_goods_sum.sum_goods_num || 0 }}
                                </el-form-item>

                                <el-form-item label="申请时间">
                                    {{ orderData.after_sale.create_time }}
                                </el-form-item>
                                <el-form-item label="退款凭证">
                                    <div
                                        class="inline m-r-10"
                                        v-for="(item, index) in orderData.after_sale.voucher"
                                        :key="index"
                                    >
                                        <el-image
                                            @click="reviews(item)"
                                            style="width: 50px; height: 50px"
                                            :src="item"
                                            fit="fit"
                                        ></el-image>
                                    </div>
                                </el-form-item>
                            </el-form>

                            <el-form
                                ref="form"
                                style="margin-left: 15vw"
                                :model="orderData"
                                label-width="120px"
                                size="small"
                            >
                                <el-form-item label="订单编号">
                                    <span class="order pointer" @click="toOrder(orderData.after_sale.order_id)">
                                        {{ orderData.order_info.sn || '' }}
                                    </span>
                                </el-form-item>

                                <el-form-item label="售后原因">
                                    {{ orderData.after_sale.refund_reason }}
                                </el-form-item>
                                <el-form-item label="售后方式">
                                    {{ orderData.after_sale.refund_method_desc }}
                                </el-form-item>

                                <el-form-item label="退款金额">
                                    ¥{{ orderData.goods_info.order_goods_sum.sum_refund_amount }}
                                </el-form-item>

                                <el-form-item label="发货状态" v-if="orderData.order_info.delivery_type !== 2">
                                    {{ orderData.order_info.express_status_desc }}
                                </el-form-item>
                                <el-form-item label="核销状态" v-if="orderData.order_info.delivery_type == 2">
                                    {{ orderData.order_info.verification_status == 0 ? '待核销' : '已核销' }}
                                </el-form-item>

                                <el-form-item label="退款说明" style="padding-right: 20px; word-break: break-all">
                                    {{ orderData.after_sale.refund_remark || '-' }}
                                </el-form-item>
                            </el-form>
                        </div>
                    </div>

                    <div class="flex col-bottom" style="width: 100%">
                        <div class="border-top flex col-bottom row-left p-t-24" style="width: 100%">
                            <!-- 同意售后 -->
                            <ls-dialog
                                class="inline m-l-24"
                                v-if="orderData.btns.agree_btn"
                                title="同意售后"
                                width="20vw"
                                @confirm="afterSaleAgree"
                            >
                                <el-button slot="trigger" size="small" type="primary">同意售后</el-button>

                                <div>提示：售后同意</div>
                            </ls-dialog>

                            <!-- 拒绝售后 -->
                            <ls-dialog
                                class="inline m-l-24"
                                v-if="orderData.btns.refuse_btn"
                                title="拒绝售后"
                                width="20vw"
                                @confirm="afterSaleRefuse"
                            >
                                <el-button slot="trigger" size="small">拒绝售后</el-button>

                                <div>提示：请填写拒绝售后的原因</div>
                                <div class="flex m-t-12">
                                    <span class="inline m-b-24" style="width: 70px">拒绝原因</span>
                                    <el-input
                                        class="m-t-10"
                                        type="textarea"
                                        :rows="2"
                                        placeholder="请输入拒绝原因"
                                        v-model="reason"
                                    >
                                    </el-input>
                                </div>
                            </ls-dialog>

                            <!-- 确认收货 -->
                            <ls-dialog
                                class="inline m-l-24"
                                v-if="orderData.btns.confirm_goods_btn"
                                title="确认收货"
                                width="20vw"
                                @confirm="afterSaleConfirmGoods"
                            >
                                <el-button slot="trigger" size="small" type="primary">确认收货</el-button>

                                <div>提示：确认收货</div>
                            </ls-dialog>

                            <!-- 拒绝收货 -->
                            <ls-dialog
                                class="inline m-l-24"
                                v-if="orderData.btns.refuse_goods_btn"
                                title="拒绝收货"
                                width="20vw"
                                @confirm="afterSaleRefuseGoods"
                            >
                                <el-button slot="trigger" size="small">拒绝收货</el-button>

                                <div>提示：请填写拒绝收货的原因</div>
                                <div class="flex m-t-12">
                                    <span class="inline m-b-24" style="width: 70px">拒绝原因</span>
                                    <el-input
                                        class="m-t-10"
                                        type="textarea"
                                        :rows="2"
                                        placeholder="请输入拒绝原因"
                                        v-model="refuseGoods"
                                    >
                                    </el-input>
                                </div>
                            </ls-dialog>

                            <!-- 确认退款 -->
                            <ls-dialog
                                class="inline m-l-24"
                                v-if="orderData.btns.change_btn"
                                title="确认退款"
                                width="20vw"
                                @confirm="afterSaleConfirmRefund"
                            >
                                <el-button slot="trigger" size="small" type="primary">确认退款</el-button>

                                <div class="m-t-12">
                                    退款方式：
                                    <el-radio v-model="refund_way" label="1">原路退回</el-radio>
                                    <el-radio v-model="refund_way" label="2">退回余额</el-radio>
                                </div>
                                <div class="m-t-12">
                                    退款金额：
                                    <span style="color: red"
                                        >¥{{ orderData.goods_info.order_goods_sum.sum_refund_amount }}</span
                                    >
                                </div>
                            </ls-dialog>

                            <!-- 同意退款 -->
                            <ls-dialog
                                class="inline m-l-24"
                                v-if="orderData.btns.agree_refund_btn"
                                title="同意退款"
                                width="20vw"
                                @confirm="afterSaleAgreeRefund"
                            >
                                <el-button slot="trigger" size="small" type="primary">同意退款</el-button>

                                <div>提示：该笔订单通过微信支付付款，同意售后申请后，退款将自动原路退回会员账户。</div>
                                <div class="m-t-12">
                                    退货方式：
                                    {{ orderData.after_sale.refund_method_desc }}
                                </div>
                                <div class="m-t-12">
                                    退款金额：
                                    <span style="color: red"
                                        >¥{{ orderData.goods_info.order_goods_sum.sum_refund_amount }}</span
                                    >
                                </div>
                            </ls-dialog>

                            <!-- 拒绝退款 -->
                            <ls-dialog
                                class="inline m-l-24"
                                v-if="orderData.btns.refuse_refund"
                                title="拒绝退款"
                                width="20vw"
                                @confirm="afterSaleRefuseRefund"
                            >
                                <el-button slot="trigger" size="small">拒绝退款</el-button>

                                <div>提示：请与会员协商后确认拒绝申请，会员可再次发起退款。</div>
                                <div class="m-t-12">
                                    退货方式：
                                    {{ orderData.after_sale.refund_method_desc }}
                                </div>
                                <div class="m-t-12">
                                    退款金额：
                                    <span style="color: red"
                                        >¥{{ orderData.goods_info.order_goods_sum.sum_refund_amount }}</span
                                    >
                                </div>

                                <div class="flex m-t-12">
                                    <span class="inline m-b-24" style="width: 70px">拒绝原因</span>
                                    <el-input
                                        class="m-t-10"
                                        type="textarea"
                                        :rows="2"
                                        placeholder="请输入拒绝原因"
                                        v-model="reason"
                                    >
                                    </el-input>
                                </div>
                            </ls-dialog>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div
            class="ls-card m-t-24 flex flex-wrap col-stretch"
            style="height: auto"
            v-if="orderData.after_sale.admin_remark"
        >
            <div style="width: 100%">
                <div class="nr weight-500 m-b-20 title">拒绝原因</div>
                <el-form ref="form" :model="orderData" label-width="120px" class="flex" size="small">
                    <el-form-item label="拒绝原因">
                        <div class="order pointer">
                            {{ orderData.after_sale.admin_remark }}
                        </div>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <section>
            <!-- 用户及收货信息 -->
            <div class="ls-card m-t-24 flex flex-wrap col-stretch" style="height: auto">
                <div style="width: 100%">
                    <div class="nr weight-500 m-b-20 title">买家信息</div>
                    <el-form ref="form" :model="orderData" label-width="120px" class="flex" size="small">
                        <el-form-item label="买家昵称">
                            <div class="order pointer" @click="toUserDetail()">
                                {{ orderData.return_goods_info.user_nickname }}（{{
                                    orderData.return_goods_info.user_sn
                                }}）
                            </div>
                        </el-form-item>
                    </el-form>
                </div>
            </div>

            <!-- 商品信息 -->
            <div class="ls-card m-t-24">
                <div class="nr weight-500 m-b-20 title">商品信息</div>

                <el-table
                    :data="orderData.goods_info.order_goods"
                    ref="paneTable"
                    style="width: 100%"
                    size="mini"
                    :summary-method="getSummaries"
                >
                    <el-table-column label="商品信息" min-width="240">
                        <template slot-scope="scope">
                            <div class="flex m-t-10">
                                <el-image
                                    :src="scope.row.goods_image"
                                    style="width: 58px; height: 58px"
                                    class="flex-none"
                                >
                                </el-image>
                                <div class="m-l-8 flex-1">
                                    <div class="line-2">
                                        {{ scope.row.goods_name }}
                                    </div>
                                    <div class="line-2 muted" v-if="scope.row.supplier_name">
                                        供应商：{{ scope.row.supplier_name }}
                                    </div>
                                    <div class="xs muted">
                                        {{ scope.row.spec_value_str }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="original_price" label="商品价格" min-width="120">
                        <template slot-scope="scope">
                            <span>¥{{ scope.row.original_price }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="goods_num" label="购买数量" min-width="80"></el-table-column>
                    <el-table-column label="商品总额" prop="total_amount" min-width="120">
                        <template slot-scope="scope">
                            <span>¥{{ scope.row.total_amount }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column label="优惠金额" prop="total_pay_price" min-width="120">
                        <template slot-scope="scope">¥{{ scope.row.total_discount }}</template>
                    </el-table-column>
                    <el-table-column label="订单调整" prop="total_pay_price" min-width="120">
                        <template slot-scope="scope">¥{{ scope.row.change_price }}</template>
                    </el-table-column>
                    <el-table-column label="实付金额" prop="total_pay_price" min-width="120">
                        <template slot-scope="scope">¥{{ scope.row.total_pay_price }}</template>
                    </el-table-column>
                    <el-table-column label="操作" min-width="120">
                        <template slot-scope="scope">
                            <el-button @click="toOrder(orderData.after_sale.order_id)" type="text" size="small"
                                >查看订单</el-button
                            >
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </section>

        <footer class="col-top">
            <!-- 售后金额 -->

            <div class="ls-card m-t-24" style="height: auto" v-if="orderData.after_sale.express_name">
                <div style="width: 100%">
                    <div class="nr weight-500 m-b-20 title">退货信息</div>
                    <el-form ref="form" :model="orderData" label-width="120px" size="small">
                        <el-form-item label="快递公司">
                            {{ orderData.after_sale.express_name }}
                        </el-form-item>
                        <el-form-item label="快递单号">
                            {{ orderData.after_sale.invoice_no }}
                        </el-form-item>
                        <!-- <el-form-item label="收货人">
                            {{orderData.contact}}
                        </el-form-item> -->
                        <el-form-item label="退货时间">
                            {{ orderData.after_sale.express_time }}
                        </el-form-item>
                        <!-- <el-form-item label="收货地址">
                            {{orderData.delivery_address}}
                        </el-form-item>
                        <el-form-item label="配送状态">
                            {{orderData.express_status_desc}}
                        </el-form-item>
                        <el-form-item label="配送方式">
                            {{orderData.delivery_type_desc}}
                        </el-form-item>
                        <el-form-item label="配送时间">
                            {{orderData.express_time}}
                        </el-form-item> -->
                    </el-form>
                </div>
            </div>

            <!-- 售后日志 -->
            <div class="ls-card m-t-24">
                <div class="nr weight-500 m-b-20 title">售后日志</div>

                <el-table :data="orderData.after_sale_log" ref="paneTable" style="width: 100%" size="mini">
                    <el-table-column label="操作人" prop="operator_name" width="155"></el-table-column>
                    <el-table-column prop="content" label="操作事件" min-width="220"></el-table-column>
                    <el-table-column prop="create_time" label="操作时间" min-width="180"></el-table-column>
                </el-table>
            </div>
        </footer>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import AreaSelect from '@/components/area-select.vue'
import OrderLogistics from '@/components/order/order-logistics.vue'
import { throttle, debounce } from '@/utils/util'

import {
    apiAfterSaleDetail,
    apiAfterSaleAgree,
    apiAfterSaleRefuse,
    apiAfterSaleConfirmGoods,
    apiAfterSaleRefuseGoods,
    apiAfterSaleAgreeRefund,
    apiAfterSaleRefuseRefund,
    apiAfterSaleConfirmRefund
} from '@/api/order/order'
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
        after_sale: {
            id: 2,
            order_id: 1,
            user_id: 1,
            sn: '20210809184530062266134',
            refund_type: 2,
            refund_type_desc: '商品售后',
            refund_method: 1,
            refund_method_desc: '仅退款',
            status: 1,
            status_desc: '售后中',
            refund_reason: '7天无理由',
            refund_remark: '',
            refund_image: '',
            create_time: '2021-08-09 18:45:30',
            express_name: '',
            invoice_no: '',
            express_remark: '',
            express_image: ''
        },
        return_goods_info: {
            user_sn: '100001',
            user_nickname: '测试用户',
            user_mobile: '13800138000'
        },
        order_info: {
            order_status: 3,
            order_status_desc: '已完成',
            sn: 'SN0001',
            order_type: 0,
            order_type_desc: '普通订单',
            order_terminal: 1,
            order_terminal_desc: '微信小程序',
            create_time: '2021-08-05 16:35:31',
            pay_status: 1,
            pay_status_desc: '已支付',
            pay_way: 1,
            pay_way_desc: '微信支付',
            pay_time: '——',
            confirm_take_time: '——'
        },
        goods_info: {
            order_goods: [
                {
                    item_image: '',
                    spec_value_str: '黑色,L码',
                    goods_name: '小米10青春版',
                    goods_image: '',
                    goods_price: '20.00',
                    goods_num: 1,
                    total_price: null,
                    discount_price: null,
                    total_pay_price: '80.00',
                    refund_amount: '50.00'
                }
            ],
            order_goods_sum: {
                sum_refund_amount: 50,
                sum_total_pay_price: 80,
                sum_discount_price: 0,
                sum_total_price: 0,
                sum_goods_num: 1
            }
        },
        after_sale_log: [
            {
                operator_role: 3,
                operator_id: 1,
                content: '卖家已确认退款，售后退款中',
                create_time: '2021-08-09 20:29:14',
                operator_name: 'ljj'
            },
            {
                operator_role: 3,
                operator_id: 1,
                content: '卖家已同意,等待退款',
                create_time: '2021-08-09 20:24:57',
                operator_name: 'ljj'
            },
            {
                operator_role: 2,
                operator_id: 1,
                content: '买家发起商品售后,等待卖家同意',
                create_time: '2021-08-09 18:45:30',
                operator_name: '测试用户'
            }
        ],
        btns: {
            agree_btn: false,
            refuse_btn: false,
            refuse_goods_btn: false,
            confirm_goods_btn: false,
            agree_refund_btn: true,
            refuse_refund: true,
            change_btn: false
        }
    }

    // 拒绝原因
    reason: String = ''

    refuseGoods: String = ''
    // 退款的金额
    refund_total_amount: String = ''

    // 退款方式
    refund_way: String = '1'

    // E Data

    // S Methods

    toUserDetail() {
        this.$router.push({
            path: '/user/user_details',
            query: {
                id: this.orderData.after_sale.user_id
            }
        })
    }

    // 打开售后凭证图片
    reviews(item: string) {
        window.open(item)
    }

    // 获取订单详情
    getOrderDetail() {
        apiAfterSaleDetail({ id: this.id }).then(res => {
            this.orderData = res
            this.refund_total_amount = res.goods_info.order_goods_sum.sum_refund_amount
        })
    }

    // 同意售后
    afterSaleAgree() {
        apiAfterSaleAgree({ id: this.id }).then(res => {
            this.getOrderDetail()
        })
    }

    // 拒绝售后
    afterSaleRefuse() {
        apiAfterSaleRefuse({ id: this.id, admin_remark: this.reason }).then(res => {
            this.getOrderDetail()
        })
    }

    // 确认收货
    afterSaleConfirmGoods() {
        apiAfterSaleConfirmGoods({ id: this.id }).then(res => {
            this.getOrderDetail()
        })
    }

    // 拒绝收货
    afterSaleRefuseGoods() {
        apiAfterSaleRefuseGoods({ id: this.id, admin_remark: this.refuseGoods }).then(res => {
            this.getOrderDetail()
        })
    }

    // 同意退款
    afterSaleAgreeRefund() {
        apiAfterSaleAgreeRefund({ id: this.id }).then(res => {
            this.getOrderDetail()
        })
    }

    // 拒绝退款
    afterSaleRefuseRefund() {
        apiAfterSaleRefuseRefund({ id: this.id }).then(res => {
            this.getOrderDetail()
        })
    }

    // 确认退款
    afterSaleConfirmRefund() {
        apiAfterSaleConfirmRefund({
            id: this.id,
            refund_way: this.refund_way,
            refund_total_amount: this.refund_total_amount
        }).then(res => {
            this.getOrderDetail()
        })
    }

    // 去订单详情
    toOrder(id: any) {
        this.$router.push({
            path: '/order/order_detail',
            query: { id }
        })
    }

    // 商品信息的底部结算信息
    getSummaries(param: any) {
        const { columns, data } = param
        const sums: any = []
        columns.forEach((column: any, index: any) => {
            if (index === 0) {
                sums[0] = '总价'
                return
            }
            const values = data.map((item: any) => Number(item[column.property]))
            if (!values.every((value: any) => isNaN(value))) {
                if (index == 1) {
                    return
                }
                sums[index] = values.reduce((prev: any, curr: any) => {
                    const value = Number(curr)
                    if (!isNaN(value)) {
                        return prev + curr
                    }
                    return prev
                }, 0)
                if (index !== 2) {
                    sums[index] = '¥' + sums[index]
                }
            }
        })

        return sums
    }

    // E Methods

    created() {
        this.id = this.$route.query.id
        this.id && this.getOrderDetail()
        this.afterSaleConfirmRefund = debounce(this.afterSaleConfirmRefund, 1000)
        this.afterSaleConfirmRefund = throttle(this.afterSaleConfirmRefund, 1000)
    }
}
</script>

<style lang="scss" scoped>
::v-deep .el-form .el-form-item {
    margin-bottom: 0px !important;
}

::v-deep .el-table__footer-wrapper tbody td {
    background: #fff !important;
}

::v-deep .el-table__footer-wrapper td {
    // border: 0;
}

::v-deep .el-table--border::after,
.el-table::before {
    position: static;
}

.order:hover {
    color: $--color-primary;
}

.title {
    width: 100%;
    padding-bottom: 20px;
    border-bottom: 1px solid #f2f2f2;
}
</style>
