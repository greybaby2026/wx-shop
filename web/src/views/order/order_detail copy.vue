<template>
    <div>
        <header>
            <div class="ls-card">
                <el-page-header @back="$router.go(-1)" content="订单详情" />
            </div>

            <!-- 头部 -->
            <div class="flex m-t-24">
                <!-- 订单信息 -->
                <div class="ls-card flex flex-wrap col-stretch" style="min-height: 450px">
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
                                <el-form-item label="订单类型">
                                    {{ orderData.order_type_desc }}
                                </el-form-item>
                                <el-form-item label="订单来源">
                                    {{ orderData.order_terminal_desc }}
                                </el-form-item>
                                <el-form-item label="下单时间">
                                    {{ orderData.create_time }}
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
                                    {{ orderData.pay_way_desc }}
                                </el-form-item>
                                <el-form-item label="支付时间">
                                    {{ orderData.pay_time }}
                                </el-form-item>
                                <el-form-item label="完成时间">
                                    {{ orderData.confirm_take_time }}
                                </el-form-item>
                                <el-form-item label="用户备注">
                                    {{ orderData.user_remark }}
                                </el-form-item>
                            </el-form>
                        </div>
                        <el-form ref="form" :model="orderData" label-width="120px" size="small">
                            <el-form-item label="商家备注" style="word-break: break-all">
                                {{ orderData.order_remarks }}
                            </el-form-item>
                        </el-form>
                    </div>

                    <div class="flex col-bottom" style="width: 100%">
                        <div class="border-top flex col-bottom row-left p-t-24" style="width: 100%; height: 57px">
                            <!-- 取消订单 -->
                            <ls-dialog
                                title="取消订单"
                                class="inline m-l-24"
                                v-if="orderData.admin_order_btn.cancel_btn"
                                :content="`确定取消订单(${orderData.sn})吗?请谨慎操作`"
                                @confirm="orderCancel"
                            >
                                <el-button slot="trigger" size="small">取消订单</el-button>
                            </ls-dialog>

                            <!-- 提货核销 & 核销提示 -->
                            <ls-dialog
                                title="提货核销"
                                class="inline m-l-24"
                                v-if="orderData.admin_order_btn.verification_btn"
                                :content="`确定核销订单(${orderData.sn})吗?请谨慎操作`"
                                @confirm="selffetch"
                            >
                                <el-button slot="trigger" size="small" type="primary">提货核销</el-button>
                            </ls-dialog>
                            <ls-dialog
                                ref="verifyDialogRef"
                                title="温馨提示"
                                v-if="orderData.admin_order_btn.verification_btn"
                                :content="verifyTips"
                                @confirm="selffetch(1)"
                            />

                            <!-- 商家备注 -->
                            <ls-dialog
                                title="商家备注"
                                class="inline m-l-24"
                                v-if="orderData.admin_order_btn.remark_btn"
                                @confirm="postOrderRemarks"
                            >
                                <el-button slot="trigger" size="small">商家备注</el-button>
                                <div>
                                    <span class="m-b-10">商家备注</span>
                                    <el-input
                                        class="m-t-10"
                                        type="textarea"
                                        :rows="5"
                                        placeholder="请输入内容"
                                        v-model="remarks"
                                    ></el-input>
                                </div>
                            </ls-dialog>

                            <!-- 物流查询 -->
                            <!-- <order-logistics
                                class="m-l-24"
                                v-if="orderData.admin_order_btn.logistics_btn"
                                :flag="false"
                                :id="id"
                            > -->
                            <el-button
                                size="small"
                                type="primary"
                                style="margin-left: 24px"
                                v-if="orderData.admin_order_btn.logistics_btn"
                                @click="toqueryDelivery()"
                                >物流查询</el-button
                            >
                            <!-- </order-logistics> -->

                            <!-- 订单发货 -->
                            <!-- <order-logistics
                                class="m-l-24"
                                @update="getOrderDetail"
                                :flag="true"
                                :id="id"
                                v-if="orderData.admin_order_btn.deliver_btn"
                                :orderType="orderData.order_type"
                            > -->
                            <el-button
                                style="margin-left: 24px"
                                size="small"
                                v-if="orderData.admin_order_btn.deliver_btn"
                                @click="toDelivery()"
                                type="primary"
                                >发货</el-button
                            >
                            <!-- </order-logistics> -->
                            <!-- 确认付款 -->
                            <ls-dialog
                                class="inline m-l-24"
                                title="确认付款"
                                :content="`确定确认付款吗?请谨慎操作`"
                                @confirm="orderConfirmpay"
                                v-if="orderData.admin_order_btn.confirm_pay_btn"
                            >
                                <el-button slot="trigger" size="small" type="primary">确认付款</el-button>
                            </ls-dialog>
                            <!-- 取消订单 -->
                            <ls-dialog
                                class="inline m-l-24"
                                title="确认收货"
                                :content="`确定确认收货吗?请谨慎操作`"
                                @confirm="orderConfirm"
                                v-if="orderData.admin_order_btn.confirm_btn"
                            >
                                <el-button slot="trigger" size="small" type="primary">确认收货</el-button>
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
                    <el-form ref="form" :model="orderData" label-width="120px" class="flex" size="small">
                        <el-form-item label="买家昵称">
                            <div class="username pointer" @click="toUserDetail()">
                                {{ orderData.nickname }}（{{ orderData.user_sn }}）
                            </div>
                        </el-form-item>
                    </el-form>
                </div>
            </div>

            <!-- 用户及收货信息 -->
            <div class="ls-card m-t-24 flex flex-wrap col-stretch" style="height: auto">
                <div style="width: 100%">
                    <div class="nr weight-500 m-b-20 title">
                        {{ orderData.delivery_type == 2 ? '提货信息' : '用户及收货信息' }}
                    </div>

                    <div class="flex col-top">
                        <el-form ref="form" :model="orderData" label-width="120px" size="small">
                            <el-form-item label="配送方式">
                                {{ orderData.delivery_type_desc }}
                            </el-form-item>
                            <el-form-item :label="orderData.delivery_type == 2 ? '提货人' : '收货人'">{{
                                orderData.contact
                            }}</el-form-item>
                            <el-form-item label="手机号码">
                                {{ orderData.mobile }}
                            </el-form-item>
                            <el-form-item :label="orderData.delivery_type == 2 ? '提货地址' : '收货地址'">{{
                                orderData.delivery_address
                            }}</el-form-item>
                        </el-form>

                        <el-form
                            ref="form"
                            :model="orderData"
                            style="margin-left: 15vw"
                            label-width="120px"
                            size="small"
                        >
                            <el-form-item label="发货状态" v-if="orderData.delivery_type == 1">
                                {{ orderData.express_status_desc }}
                            </el-form-item>
                            <el-form-item label="物流公司" v-if="orderData.delivery_type == 1">
                                {{ orderData.express_name }}
                                <!-- <el-popover
                                    v-if="orderData.order_status > 1 && orderData.send_type == 1"
                                    placement="top"
                                    title
                                    width="300"
                                    trigger="click"
                                >
                                    <i class="el-icon-edit primary m-l-30 lg" slot="reference"></i>
                                    <div class="flex">
                                        <el-select
                                            style="width: 188px"
                                            v-model="express_id"
                                            placeholder="请输入物流公司名称"
                                            class="m-r-24"
                                        >
                                            <el-option
                                                :label="item.name"
                                                :value="item.id"
                                                v-for="(item, index) in expressList"
                                                :key="index"
                                            ></el-option>
                                        </el-select>
                                        <el-button
                                            class="m-l-24"
                                            size="small"
                                            type="primary"
                                            @click="orderChangeLogistics()"
                                            >修改物流公司</el-button
                                        >
                                    </div>
                                </el-popover> -->
                            </el-form-item>
                            <el-form-item label="快递单号" v-if="orderData.delivery_type == 1">
                                {{ orderData.invoice_no }}
                                <!-- <el-popover
                                    v-if="orderData.order_status > 1 && orderData.send_type == 1"
                                    placement="top"
                                    title
                                    width="300"
                                    trigger="hover"
                                >
                                    <i class="el-icon-edit primary m-l-30 lg" slot="reference"></i>
                                    <div class="flex">
                                        <el-input
                                            v-model="invoice_no"
                                            class="m-r-24"
                                            style="width: 188px"
                                            placeholder="请输入快递单号"
                                        ></el-input>
                                        <el-button
                                            class="m-l-24"
                                            size="small"
                                            type="primary"
                                            @click="orderChangeCourierNumber()"
                                            >修改快递单号</el-button
                                        >
                                    </div>
                                </el-popover> -->
                            </el-form-item>
                            <el-form-item label="发货时间" v-if="orderData.delivery_type == 1">{{
                                orderData.express_time
                            }}</el-form-item>

                            <el-form-item label="核销状态" v-if="orderData.delivery_type == 2">
                                {{ orderData.verification_status == 0 ? '待核销' : '已核销' }}
                            </el-form-item>
                            <el-form-item label="核销码" v-if="orderData.delivery_type == 2">{{
                                orderData.pickup_code
                            }}</el-form-item>
                            <el-form-item label="自提门店" v-if="orderData.delivery_type == 2">{{
                                orderData.shop_name
                            }}</el-form-item>

                            <el-form-item label="提货时间" v-if="orderData.delivery_type == 2">{{
                                orderData.verification_time
                            }}</el-form-item>
                        </el-form>
                    </div>
                </div>

                <div class="flex col-bottom" style="width: 100%">
                    <div class="border-top flex col-bottom row-left p-t-24" style="width: 100%; height: 57px">
                        <!-- 重新发货 -->

                        <order-logistics
                            class="m-l-24"
                            @update="getOrderDetail"
                            :flag="true"
                            :id="id"
                            :express_again="1"
                            v-if="
                                orderData.order_status_desc == '待收货' &&
                                orderData.express_again == 0 &&
                                orderData.delivery_type != 4
                            "
                        >
                            <el-button slot="trigger" size="small" type="primary">重新发货</el-button>
                        </order-logistics>
                        <!-- 修改地址 -->
                        <ls-dialog
                            class="inline m-l-24"
                            :title="'收货地址修改'"
                            width="35vw"
                            @confirm="orderAddressSet()"
                            v-if="orderData.admin_order_btn.address_btn"
                        >
                            <el-button slot="trigger" size="small">修改地址</el-button>

                            <div class="flex row-center">
                                <el-form ref="address" :model="address" label-width="80px">
                                    <el-form-item label="地区" prop="return_district">
                                        <area-select
                                            width="280px"
                                            :province.sync="address.province_id"
                                            :city.sync="address.city_id"
                                            :district.sync="address.district_id"
                                        />
                                    </el-form-item>
                                    <el-form-item label="详细地址" prop="return_address">
                                        <el-input class="ls-input" v-model="address.address" show-word-limit />
                                    </el-form-item>
                                </el-form>
                            </div>
                        </ls-dialog>
                    </div>
                </div>
            </div>
            <!-- 发货内容 -->
            <div class="ls-card m-t-24" v-if="orderData.delivery_content || orderData.delivery_content">
                <div class="nr weight-500 m-b-20 title">发货内容</div>
                <div style="word-break: break-all">{{ orderData.delivery_content }}</div>
            </div>

            <!-- 商品信息 -->
            <div class="ls-card m-t-24">
                <div class="nr weight-500 m-b-20 title">商品信息</div>

                <el-table
                    :data="orderData.order_goods"
                    :header-cell-style="{
                        background: '#f5f8ff',
                        border: 'none',
                        color: '#666666',
                        height: '60px',
                        width: '100%'
                    }"
                    ref="paneTable"
                    style="width: 100%"
                    size="mini"
                    :summary-method="getSummaries"
                >
                    <el-table-column prop="code" label="商品编码" min-width="120"> </el-table-column>
                    <el-table-column label="商品信息" min-width="240">
                        <template slot-scope="scope">
                            <div class="flex m-t-10">
                                <el-image
                                    :src="scope.row.goods_image"
                                    style="width: 58px; height: 58px"
                                    class="flex-none"
                                ></el-image>
                                <div class="m-l-8 flex-1">
                                    <div class="line-2">{{ scope.row.goods_name }}</div>
                                    <div class="line-2 muted" v-if="scope.row.supplier_name">
                                        供应商：{{ scope.row.supplier_name }}
                                    </div>
                                    <div class="xs muted">
                                        {{ scope.row.spec_value_str }}
                                        <span v-if="scope.row.goods_snap.bar_code">
                                            ({{ scope.row.goods_snap.bar_code }})
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </el-table-column>
                    <!-- 商品价格改价 -->
                    <el-table-column prop="original_price" label="商品价格" min-width="120">
                        <template slot-scope="scope">
                            <!-- 根据订单类型判队商品价格 -->
                            <span
                                v-if="
                                    orderData.order_type == 1 ||
                                    orderData.order_type == 2 ||
                                    orderData.order_type == 3 ||
                                    orderData.order_type == 5
                                "
                                >¥{{ scope.row.goods_price }}</span
                            >
                            <span v-if="orderData.order_type == 0 || orderData.order_type == 4"
                                >¥{{ scope.row.original_price }}</span
                            >
                        </template>
                    </el-table-column>
                    <el-table-column prop="goods_num" label="购买数量" min-width="80"></el-table-column>
                    <el-table-column label="商品总额" prop="total_amount" min-width="120">
                        <template slot-scope="scope">
                            <span v-if="orderData.order_type == 0 || orderData.order_type == 4"
                                >¥{{ scope.row.total_amount }}</span
                            >

                            <span
                                v-if="
                                    orderData.order_type == 1 ||
                                    orderData.order_type == 2 ||
                                    orderData.order_type == 3 ||
                                    orderData.order_type == 5
                                "
                                >¥{{ scope.row.total_price }}</span
                            >
                        </template>
                    </el-table-column>
                    <el-table-column prop="change_price" min-width="120">
                        <template slot="header" slot-scope="scope">
                            <span class="m-r-5"> 商品改价</span
                            ><el-popover placement="top" trigger="hover" v-if="orderData.admin_order_btn.price_btn">
                                <div>负数代表扣减金额</div>
                                <i slot="reference" class="el-icon-question pointer muted"></i>
                            </el-popover>
                        </template>
                        <template slot-scope="scope">
                            <span>¥{{ scope.row.change_price }}</span>
                            <el-popover
                                placement="top"
                                v-if="orderData.admin_order_btn.price_btn"
                                title
                                width="300"
                                trigger="hover"
                            >
                                <i class="el-icon-edit primary m-l-30 lg" slot="reference"></i>

                                <div class="flex">
                                    <el-input
                                        v-model="goods_price"
                                        class="m-r-24"
                                        style="width: 188px"
                                        placeholder="请输入要调整金额"
                                    ></el-input>
                                    <el-button
                                        class="m-l-24"
                                        size="small"
                                        type="primary"
                                        @click="orderChangeGoodsPrice(scope.row.id)"
                                        >修改价格</el-button
                                    >
                                </div>
                            </el-popover>
                        </template>
                    </el-table-column>
                    <el-table-column label="商品实付总额" prop="total_pay_price" min-width="160">
                        <template slot-scope="scope">
                            <div>¥{{ scope.row.total_pay_price }}</div>
                            <div class="xs muted">
                                （含运费：{{ scope.row.express_price }}）
                                <el-popover
                                    placement="top"
                                    v-if="orderData.admin_order_btn.express_btn"
                                    title
                                    width="300"
                                    trigger="hover"
                                >
                                    <i class="el-icon-edit primary lg" slot="reference"></i>

                                    <div class="flex">
                                        <el-input
                                            v-model="express_price"
                                            class="m-r-24"
                                            style="width: 188px"
                                            placeholder="请输入要调整的运费"
                                        ></el-input>
                                        <el-button
                                            class="m-l-24"
                                            size="small"
                                            type="primary"
                                            @click="orderChangeExpress(scope.row.id)"
                                            >修改运费</el-button
                                        >
                                    </div>
                                </el-popover>
                            </div>
                            <div class="xs muted">
                                （含优惠：{{ getDiscountPrice(scope.row) }}）
                                <el-popover placement="top" trigger="hover">
                                    <div>优惠券抵扣：-¥{{ scope.row.coupon_discount }}</div>
                                    <div>会员折扣：-¥{{ scope.row.member_discount }}</div>
                                    <!-- <div>积分抵扣：-¥{{ scope.row.integral_discount }}</div> -->
                                    <i slot="reference" class="el-icon-question pointer"></i>
                                </el-popover>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column label="售后状态" prop="after_sale_status_desc" min-width="100">
                        <template slot-scope="scope">
                            <div style="color: #f2a626">{{ scope.row.after_sale_status_desc }}</div>
                        </template>
                    </el-table-column>
                </el-table>

                <div class="flex col-bottom flex-col">
                    <div class="item">
                        <span class="">商品总额：</span>
                        <span>￥{{ goods_total_price }}</span>
                    </div>
                    <div class="item">
                        <span class="m-r-25">运费金额：</span>
                        <div>
                            <span>￥{{ orderData.express_price }}</span>
                        </div>
                    </div>
                    <div class="item">
                        <span>优惠金额：</span>
                        <span style="color: red">-￥{{ orderData.total_discount }}</span>
                    </div>
                    <div class="item">
                        <span>商品改价：</span>
                        <span>￥{{ orderData.change_price || '0.00' }}</span>
                    </div>
                    <div class="item">
                        <span>订单实付金额：</span>
                        <span class="xl">￥{{ orderData.order_amount }}</span>
                    </div>
                </div>
            </div>
        </section>

        <footer class="flex col-top">
            <!-- 订单日志 -->
            <div class="ls-card m-t-24">
                <div class="nr weight-500 m-b-20 title">订单日志</div>

                <el-table :data="orderData.order_log" ref="paneTable" style="width: 100%" size="mini">
                    <el-table-column label="操作人" prop="operator" width="155"></el-table-column>
                    <el-table-column prop="channel_desc" label="操作事件" min-width="220"></el-table-column>
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
import {
    apiOrderDetail,
    apiOrderChangeExpressPrice,
    apiOrderRemarks,
    apiOrderConfirm,
    apiOrderCancel,
    apiOrderChangeGoodsPrice,
    apiOrderAddressEdit,
    apiOrderChangeDelivery,
    apiOrderDeliveryInfo,
    apiOrderConfirmpay
} from '@/api/order/order'
import { apiSelffetchVerification } from '@/api/application/selffetch'
@Component({
    components: {
        LsDialog,
        AreaSelect,
        OrderLogistics
    }
})
export default class OrderDetail extends Vue {
    $refs!: { verifyDialogRef: any }
    // S Data

    // 订单详情ID
    id: any = 0

    // 订单数据
    orderData: any = {
        admin_order_btn: {
            remark_btn: 1,
            cancel_btn: 0,
            confirm_btn: 0,
            logistics_btn: 0,
            refund_btn: 0,
            address_btn: 1,
            price_btn: 1
        }
    }

    address: any = {
        province_id: '', //必填	int	所在地区:省id
        city_id: '', //必填	int	所在地区:市id
        district_id: '', //必填	int	所在地区:区id
        address: '' //必填	varchar	详细地址
    }

    // 商家备注
    remarks: String = ''
    verifyTips = ''

    // 运费更改
    express_price: String = ''

    // 商品价格
    goods_price: String = ''

    // 物流公司
    express_id: String = ''

    // 快递单号
    invoice_no: String = ''

    // 物流公司数组
    expressList: Array<any> = []
    // E Data

    // S Methods
    // 获取订单详情
    getOrderDetail() {
        apiOrderDetail({ id: this.id }).then(res => {
            this.orderData = res

            if (res.delivery_type == 1 && res.order_status > 1 && res.send_type == 1) {
                this.getOrderDeliveryInfo()
            }
        })
    }

    // 取消订单
    orderCancel() {
        apiOrderCancel({ id: this.id }).then(res => {
            this.getOrderDetail()
        })
    }

    // 提货核销
    selffetch(isConfirm = 0) {
        apiSelffetchVerification({ id: this.id, confirm: isConfirm }).then(res => {
            if (res.code === 10) {
                this.verifyTips = res.msg
                this.$nextTick(() => {
                    this.$refs.verifyDialogRef?.open()
                })
                return
            }
            this.getOrderDetail()
        })
    }

    // 订单地址修改
    orderAddressSet() {
        apiOrderAddressEdit({
            id: this.id,
            ...this.address
        }).then(res => {
            this.getOrderDetail()
        })
    }
    toqueryDelivery() {
        this.$router.push({
            path: '/order/query_delivery',
            query: { id: this.id }
        })
    }
    toDelivery() {
        this.$router.push({
            path: '/order/order_delivery',
            query: { id: this.id }
        })
    }
    // 确认收货
    orderConfirm() {
        apiOrderConfirm({ id: this.id }).then(res => {
            this.getOrderDetail()
        })
    }
    orderConfirmpay() {
        apiOrderConfirmpay({ id: this.id }).then(res => {
            this.getOrderDetail()
        })
    }

    // 商家备注
    postOrderRemarks() {
        apiOrderRemarks({
            id: [this.id],
            order_remarks: this.remarks
        }).then(res => {
            this.getOrderDetail()
        })
    }

    // 修改商品价格
    orderChangeGoodsPrice(id: Number) {
        if (this.goods_price == '') {
            return this.$message.error('请输入价格')
        }
        apiOrderChangeGoodsPrice({
            order_goods_id: id,
            change_price: this.goods_price
        }).then(res => {
            this.getOrderDetail()
            this.goods_price = ''
        })
    }

    // 修改运费
    orderChangeExpress(id: number) {
        if (this.express_price == '') {
            return this.$message.error('请输入运费！')
        }
        apiOrderChangeExpressPrice({
            order_goods_id: id,
            express_price: this.express_price
        }).then(res => {
            this.getOrderDetail()
        })
    }

    // 修改物流公司
    orderChangeLogistics() {
        if (this.express_id == '') {
            return this.$message.error('请输入物流公司名称！')
        }
        apiOrderChangeDelivery({
            id: this.id,
            express_id: this.express_id
        }).then(res => {
            this.getOrderDetail()
            this.express_id = ''
        })
    }

    // 修改快递单号
    orderChangeCourierNumber() {
        if (this.invoice_no == '') {
            return this.$message.error('请输入快递单号！')
        }
        apiOrderChangeDelivery({
            id: this.id,
            invoice_no: this.invoice_no
        }).then(res => {
            this.getOrderDetail()
            this.invoice_no = ''
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
                if (index !== 3) {
                    if (typeof sums[index] == 'number') {
                        sums[index] = sums[index].toFixed(2)
                    }
                    sums[index] = '¥' + sums[index]
                }
                if (index == 5) {
                    if (typeof sums[index] == 'number') {
                        sums[index] = sums[index].toFixed(2)
                    }
                    sums[index] = '-' + sums[index]
                }
                if (index == 6) {
                    sums[index] = '-¥' + (this.orderData.change_price || '0.00')
                }
            }
        })
        return sums
    }

    toUserDetail() {
        this.$router.push({
            path: '/user/user_details',
            query: {
                id: this.orderData.user_id
            }
        })
    }

    // 获取订单物流信息
    getOrderDeliveryInfo() {
        apiOrderDeliveryInfo({ id: this.id }).then(res => {
            this.expressList = res.express
        })
    }

    getDiscountPrice(item: any) {
        const { integral_discount, member_discount, coupon_discount } = item
        return (Number(integral_discount) + Number(member_discount) + Number(coupon_discount)).toFixed(2)
    }

    // E Methods

    created() {
        this.id = this.$route.query.id
        this.id && this.getOrderDetail()
    }
    /** S computed **/
    // 总的商品总额
    get goods_total_price() {
        let total = 0
        if (this.orderData.order_type == 0 || this.orderData.order_type == 4) {
            this.orderData.order_goods.forEach((i: any) => {
                return (total += Number(i.total_amount))
            })
        } else if (
            this.orderData.order_type == 1 ||
            this.orderData.order_type == 2 ||
            this.orderData.order_type == 3 ||
            this.orderData.order_type == 5
        ) {
            this.orderData.order_goods.forEach((i: any) => {
                return (total += Number(i.total_price))
            })
        }
        return total.toFixed(2)
    }
    /** E computed **/
}
</script>

<style lang="scss" scoped>
::v-deep .el-form .el-form-item {
    margin-bottom: 12px !important;
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

.username:hover {
    color: $--color-primary;
}

.title {
    width: 100%;
    padding-bottom: 20px;
    border-bottom: 1px solid #f2f2f2;
}

.item {
    display: flex;
    justify-content: space-between;
    padding-top: 15px;
    min-width: 180px;
}
</style>
