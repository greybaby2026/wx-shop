<template>
    <div class="order-pane">
        <div class="m-b-24">
            <!-- <ls-dialog class="inline" :content="`确定打印小票吗?请谨慎操作`" @confirm="couponDel('',0)">
                <el-button slot="trigger" size="small">小票打印</el-button>
            </ls-dialog> -->

            <ls-dialog class="inline m-l-24" title="商家备注" @confirm="postOrderRemarks()">
                <el-button slot="trigger" size="mini">商家备注</el-button>
                <div>
                    <span class="m-b-10">商家备注</span>
                    <el-input class="m-t-10" type="textarea" :rows="5" placeholder="请输入内容" v-model="remarks">
                    </el-input>
                </div>
            </ls-dialog>
        </div>

        <div class="pane-table">
            <el-table
                :data="value"
                ref="paneTable"
                style="width: 100%"
                size="mini"
                v-loading="pager.loading"
                @selection-change="selectionChange"
            >
                <!-- 选择 -->
                <el-table-column fixed="left" type="selection" width="55"></el-table-column>
                <!-- 订单编号 -->
                <el-table-column label="订单编号" prop="sn" width="180"></el-table-column>
                <!-- 订单类型描述 -->
                <el-table-column label="订单类型" prop="order_type_desc" width="180"></el-table-column>
                <!-- 卖家的信息 -->
                <el-table-column label="用户信息" prop="nickname" min-width="120">
                    <template slot-scope="scope">
                        <el-popover placement="top" width="200" trigger="hover">
                            <div class="flex">
                                <span class="flex-none m-r-20">头像：</span>
                                <el-image :src="scope.row.avatar" style="width: 40px; height: 40px; border-radius: 50%">
                                </el-image>
                            </div>
                            <div class="flex m-t-20 col-top">
                                <span class="flex-none m-r-20">昵称：</span>
                                <span>{{ scope.row.nickname }}</span>
                            </div>
                            <div class="flex m-t-20 col-top">
                                <span class="flex-none m-r-20">编号：</span>
                                <span>{{ scope.row.user_sn }}</span>
                            </div>
                            <div slot="reference" class="pointer" @click="toUser(scope.row.user_id)">
                                {{ scope.row.nickname }}
                            </div>
                        </el-popover>
                    </template>
                </el-table-column>

                <!-- 商品的图片 -->
                <el-table-column label="商品图片" min-width="110">
                    <template slot-scope="scope">
                        <div v-for="(item, index) in scope.row.order_goods" :key="index">
                            <div class="m-t-6" style="height: 65px" v-if="index < 3">
                                <el-image :src="item.goods_image" style="width: 58px; height: 58px" class="flex-none">
                                </el-image>
                            </div>
                        </div>
                        <div
                            class="muted m-t-5 flex"
                            v-if="scope.row.order_goods.length > 2"
                            @click="toOrder(scope.row.id)"
                        >
                            共{{ scope.row.order_goods.reduce((pre, cur) => pre + cur.goods_num, 0) }}件商品
                            <i class="el-icon-arrow-right"></i>
                        </div>
                    </template>
                </el-table-column>

                <!-- 商品的信息 -->
                <el-table-column label="商品信息" min-width="260">
                    <template slot-scope="scope">
                        <div
                            :style="{
                                'margin-bottom': scope.row.order_goods.length > 2 ? '15px' : ''
                            }"
                        >
                            <div v-for="(item, index) in scope.row.order_goods" :key="index">
                                <div class="m-t-10 goods" @click="toOrder(scope.row.id)" v-if="index < 3">
                                    <div>
                                        <div class="flex row-between normal p-r-24 line-1" style="line-height: 12px">
                                            <span class="line-1 name">{{ item.goods_name }}</span>
                                        </div>
                                        <div class="xs lighter flex line-1 p-r-24">规格：{{ item.spec_value_str }}</div>
                                        <div class="xs muted flex p-r-24 line-1">
                                            价格：<span
                                                class="normal m-r-10"
                                                v-if="scope.row.order_type == 0 || scope.row.order_type == 4"
                                                >¥{{ item.original_price }}</span
                                            >
                                            <span
                                                class="normal m-r-10"
                                                v-if="
                                                    scope.row.order_type == 1 ||
                                                    scope.row.order_type == 2 ||
                                                    scope.row.order_type == 3
                                                "
                                                >¥{{ item.goods_price }}</span
                                            >
                                            数量：<span class="normal">{{ item.goods_num }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <!-- 实际付款金额 -->
                <el-table-column label="实付金额" min-width="120">
                    <template slot-scope="scope"> ¥{{ scope.row.order_amount }} </template>
                </el-table-column>

                <!-- 收货人 -->
                <el-table-column prop="contact" label="收货人" min-width="120">
                    <template slot-scope="scope">
                        <el-popover
                            placement="top"
                            width="300"
                            trigger="hover"
                            v-if="scope.row.mobile || scope.row.delivery_address"
                        >
                            <div>收件人：{{ scope.row.contact }}</div>
                            <div class="m-t-10">手机号：{{ scope.row.mobile }}</div>
                            <div class="m-t-10">
                                地&nbsp;&nbsp;&nbsp;址：
                                {{ scope.row.delivery_address }}
                            </div>
                            <el-tag size="medium" slot="reference">{{ scope.row.contact }}</el-tag>
                        </el-popover>
                        <el-tag v-if="!scope.row.contact" size="medium" slot="reference">{{ '-' }}</el-tag>
                    </template>
                </el-table-column>

                <!-- 配送方式 -->
                <el-table-column label="配送方式" prop="order_status_desc" min-width="120">
                    <template slot-scope="scope">
                        <span>{{ scope.row.delivery_type_desc }}</span>
                    </template>
                </el-table-column>

                <!-- 支付状态 -->
                <el-table-column label="支付状态" prop="pay_status_desc" min-width="120">
                    <template slot-scope="scope">
                        <span style="color: #ff2214" v-if="scope.row.pay_status_desc == '未支付'">{{
                            scope.row.pay_status_desc
                        }}</span>
                        <span v-else>{{ scope.row.pay_status_desc }}</span>
                    </template>
                </el-table-column>

                <!-- 订单的状态 -->
                <el-table-column label="订单状态" prop="order_status_desc" min-width="120">
                    <template slot-scope="scope">
                        <span>{{ scope.row.order_status_desc }}</span>
                    </template>
                </el-table-column>

                <!-- 操作 -->
                <el-table-column fixed="right" label="操作" min-width="250">
                    <template slot-scope="scope">
                        <div class="flex">
                            <!-- 详情 -->
                            <el-button
                                @click="toOrder(scope.row.id)"
                                v-if="scope.row.admin_order_btn.detail_btn"
                                type="text"
                                size="small"
                                >订单详情</el-button
                            >

                            <!-- 物流查询 -->
                            <!-- <order-logistics
                                class="m-l-12"
                                v-if="scope.row.admin_order_btn.logistics_btn"
                                :flag="false"
                                :id="scope.row.id"
                            > -->

                            <el-button
                                slot="trigger"
                                type="text"
                                size="small"
                                v-if="scope.row.admin_order_btn.logistics_btn"
                                @click="toqueryDelivery(scope.row.id)"
                                >物流查询</el-button
                            >
                            <!-- </order-logistics> -->

                            <!-- 订单发货 -->
                            <!-- <order-logistics
                                class="m-l-12"
                                @update="getOrderLists"
                                :flag="true"
                                :id="scope.row.id"
                                v-if="scope.row.admin_order_btn.deliver_btn"
                                :orderType="scope.row.order_type"
                                
                            > -->
                            <el-button
                                slot="trigger"
                                type="text"
                                size="small"
                                @click="toDelivery(scope.row.id)"
                                v-if="scope.row.admin_order_btn.deliver_btn"
                                >发货</el-button
                            >
                            <!-- </order-logistics> -->

                            <!-- 确认收货 -->
                            <ls-dialog
                                class="inline m-l-12"
                                title="确认收货"
                                v-if="scope.row.admin_order_btn.confirm_btn"
                                :content="`确定收货订单：${scope.row.sn}`"
                                @confirm="orderConfirm(scope.row.id)"
                            >
                                <el-button slot="trigger" type="text" size="small">确认收货</el-button>
                            </ls-dialog>
                            <!-- 确认付款 -->
                            <ls-dialog
                                class="inline m-l-12"
                                title="确认付款"
                                v-if="scope.row.admin_order_btn.confirm_pay_btn"
                                :content="`确定付款订单：${scope.row.sn}`"
                                @confirm="orderConfirmpay(scope.row.id)"
                            >
                                <el-button slot="trigger" type="text" size="small">确认付款</el-button>
                            </ls-dialog>
                            <!-- 提货核销 & 核销提示 -->
                            <ls-dialog
                                title="提货核销"
                                class="inline m-l-10"
                                v-if="scope.row.admin_order_btn.verification_btn"
                                :content="`确定核销订单(${scope.row.sn})吗?请谨慎操作`"
                                @confirm="onSelffetchOrderVerification(scope.row.id)"
                            >
                                <el-button type="text" size="small" slot="trigger">提货核销</el-button>
                            </ls-dialog>

                            <ls-dialog
                                ref="verifyDialogRef"
                                title="温馨提示"
                                v-if="scope.row.admin_order_btn.verification_btn"
                                :content="verifyTips"
                                @confirm="onSelffetchOrderVerification(scope.row.id, 1)"
                            />

                            <el-dropdown class="m-l-12">
                                <el-button type="text" size="small"> 更多<i class="el-icon-arrow-down" /> </el-button>

                                <el-dropdown-menu slot="dropdown">
                                    <!-- 小票打印 v-if="scope.row.admin_order_btn.print_btn"-->
                                    <el-dropdown-item>
                                        <ls-dialog
                                            title="小票打印"
                                            class="inline"
                                            :content="`确定打印小票吗?请谨慎操作`"
                                            @confirm="onPrintOrderFunc(scope.row)"
                                        >
                                            <el-button slot="trigger" class="order_btn" type="text" size="small"
                                                >小票打印</el-button
                                            >
                                        </ls-dialog>
                                    </el-dropdown-item>

                                    <!-- 商家备注 -->
                                    <el-dropdown-item v-if="scope.row.admin_order_btn.remark_btn">
                                        <ls-dialog
                                            class="inline"
                                            title="商家备注"
                                            @confirm="postOrderRemarks([scope.row.id])"
                                        >
                                            <el-button slot="trigger" class="order_btn" type="text" size="small">
                                                商家备注
                                            </el-button>
                                            <div>
                                                <span class="m-b-10"> 商家备注</span>
                                                <el-input
                                                    class="m-t-10"
                                                    type="textarea"
                                                    :rows="5"
                                                    placeholder="请输入内容"
                                                    v-model="remarks"
                                                >
                                                </el-input>
                                            </div>
                                        </ls-dialog>
                                    </el-dropdown-item>

                                    <!-- 取消订单 -->
                                    <el-dropdown-item v-if="scope.row.admin_order_btn.cancel_btn">
                                        <ls-dialog
                                            class="inline"
                                            title="取消订单"
                                            :content="`确定取消订单(${scope.row.sn})吗?请谨慎操作`"
                                            @confirm="orderCancel(scope.row.id)"
                                        >
                                            <el-button slot="trigger" type="text" class="order_btn" size="small">
                                                取消订单
                                            </el-button>
                                        </ls-dialog>
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                        </div>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <div class="pane-footer m-t-16 flex row-between">
            <div class="btns flex">
                <div class="m-r-16">
                    <el-checkbox :value="selectIds.length == value.length" @change="selectAll" :disabled="!value.length"
                        >当页全选</el-checkbox
                    >
                </div>

                <!-- <ls-dialog class="inline m-l-24 " :content="`确定打印小票吗?请谨慎操作`" @confirm="couponDel('',0)">
                    <el-button slot="trigger" size="small">小票打印</el-button>
                </ls-dialog> -->

                <ls-dialog class="inline m-l-24" title="商家备注" @confirm="postOrderRemarks()">
                    <el-button slot="trigger" size="small">商家备注</el-button>
                    <div>
                        <span class="m-b-10">商家备注</span>
                        <el-input class="m-t-10" type="textarea" :rows="5" placeholder="请输入内容" v-model="remarks">
                        </el-input>
                    </div>
                </ls-dialog>
            </div>
            <ls-pagination v-model="pager" @change="$emit('refresh')" />
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import { apiOrderPrint } from '@/api/application/print'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import OrderLogistics from '@/components/order/order-logistics.vue'
import { apiOrderRemarks, apiOrderCancel, apiOrderConfirm, apiOrderConfirmpay } from '@/api/order/order'
import { apiSelffetchVerification } from '@/api/application/selffetch'
@Component({
    components: {
        LsDialog,
        LsPagination,
        OrderLogistics
    }
})
export default class OrderPane extends Vue {
    $refs!: { paneTable: any; verifyDialogRef: any }
    @Prop() value: any
    @Prop() pager!: any

    selectIds: any = []

    remarks = '' //商家备注

    verifyTips = ''

    // 获取订单信息
    getOrderLists() {
        ;(this.$parent as any).getOrderLists()
    }

    // 选择某条数据
    selectionChange(val: any[]) {
        this.selectIds = val.map(item => item.id)
    }

    // 全选
    selectAll() {
        ;(this.$refs.paneTable as any).toggleAllSelection()
    }

    // 去订单详情
    toOrder(id: any) {
        this.$router.push({
            path: '/order/order_detail',
            query: { id }
        })
    }
    toDelivery(id: any) {
        this.$router.push({
            path: '/order/order_delivery',
            query: { id }
        })
    }
    toqueryDelivery(id: any) {
        this.$router.push({
            path: '/order/query_delivery',
            query: { id }
        })
    }
    toUser(id: any) {
        this.$router.push({
            path: '/user/user_details',
            query: { id: id }
        })
    }

    // 取消订单
    orderCancel(id: Number) {
        apiOrderCancel({ id: id }).then(res => {
            this.getOrderLists()
        })
    }

    // 确认收货
    orderConfirm(id: Number) {
        apiOrderConfirm({ id: id }).then(res => {
            this.getOrderLists()
        })
    }
    orderConfirmpay(id: Number) {
        apiOrderConfirmpay({ id: id }).then(res => {
            this.getOrderLists()
        })
    }

    async onPrintOrderFunc(row: any) {
        await apiOrderPrint({ id: row.id })
    }

    // 商家备注
    postOrderRemarks(id: any) {
        id = Array.isArray(id) == true ? id : this.selectIds
        apiOrderRemarks({
            id: id,
            order_remarks: this.remarks
        }).then(res => {
            this.remarks = ''
            this.getOrderLists()
        })
    }

    // 订单核销
    onSelffetchOrderVerification(id: number, isConfirm = 0) {
        apiSelffetchVerification({
            id,
            confirm: isConfirm
        }).then(res => {
            if (res.code === 10) {
                this.verifyTips = res.msg
                this.$nextTick(() => {
                    this.$refs.verifyDialogRef?.open()
                })
                return
            }
            this.getOrderLists()
        })
    }
}
</script>

<style scoped lang="scss">
.goods:hover {
    // height: 100%;
    cursor: pointer;
    .name {
        color: $--color-primary;
    }
}
.goods:nth-child(n + 1) {
    margin-top: 5px;
}
.goods {
    height: 65px;
}
.order_btn {
    color: #101010;
}
.order_btn:hover {
    color: $--color-primary;
}
</style>
