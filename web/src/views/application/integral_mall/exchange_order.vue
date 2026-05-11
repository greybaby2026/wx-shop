<template>
    <div class="exchange-order">
        <div class="ls-card" style="padding-bottom: 8px">
            <el-alert
                title="温馨提示：1.会员在商城下单的列表；2.订单状态有待付款，待发货，待收货，已完成，已关闭；不做售后流程；3.待付款订单取消后则为已关闭、待付款订单支付后则为待发货、待发货订单发货后则为待收货、待收货订单收货后则为已完成；红包类型的订单一付款就变成已完成状态。"
                type="info"
                show-icon
                :closable="false"
            ></el-alert>
            <div class="form-data m-t-16">
                <el-form inline :model="formData" label-width="80px" size="small">
                    <el-form-item label="兑换单号">
                        <el-input v-model="formData.sn" placeholder="请输入兑换单号"></el-input>
                    </el-form-item>
                    <el-form-item label="商品名称">
                        <el-input v-model="formData.goods_name" placeholder="请输入商品名称"></el-input>
                    </el-form-item>
                    <el-form-item label="兑换类型">
                        <el-select v-model="formData.exchange_type" placeholder="请选择兑换类型">
                            <el-option label="全部" value></el-option>
                            <el-option label="商品" :value="1"></el-option>
                            <el-option label="红包" :value="2"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="下单时间">
                        <date-picker :start-time.sync="formData.start_time" :end-time.sync="formData.end_time" />
                    </el-form-item>
                    <el-form-item label class="m-l-20">
                        <el-button size="small" type="primary" @click="getList(1)">查询</el-button>
                        <el-button size="small" @click="handleReset">重置</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <div class="ls-card m-t-16" style="padding-top: 0">
            <el-tabs v-model="tabIndex" @tab-click="getList(1)">
                <el-tab-pane v-for="(item, index) in tabLists" :key="index" :label="item.label" :name="`${index}`" lazy>
                    <div class="table-content m-t-16">
                        <el-table :data="pager.lists" style="width: 100%" size="mini" v-loading="pager.loading">
                            <el-table-column prop="sn" label="兑换单号" min-width="120"></el-table-column>
                            <el-table-column label="商品信息" min-width="250">
                                <template v-slot="{ row }">
                                    <div class="flex">
                                        <el-image
                                            class="flex-none"
                                            style="width: 58px; height: 58px"
                                            :src="row.goods_snap.image"
                                        ></el-image>
                                        <div class="m-l-10 flex-1">
                                            <div class="line-2" style="max-height: 44px">{{ row.goods_snap.name }}</div>
                                            <div class="flex flex-wrap">
                                                <div class="m-r-20">
                                                    <span class="muted">积分金额：</span>
                                                    {{ row.goods_snap.need_integral }}积分
                                                    <template v-if="row.exchange_way == 2">
                                                        +
                                                        {{ +row.goods_snap.need_money }}元
                                                    </template>
                                                </div>
                                                <div class="muted">数量：{{ row.total_num }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column prop="exchange_type_desc" label="兑换类型"></el-table-column>
                            <el-table-column label="会员信息">
                                <template v-slot="{ row }">
                                    <el-popover placement="top" width="200" trigger="hover">
                                        <div>
                                            <div class="flex">
                                                <span class="flex-none m-r-20">头像：</span>
                                                <el-image
                                                    :src="row.user.avatar"
                                                    style="width: 40px; height: 40px; border-radius: 50%"
                                                ></el-image>
                                            </div>
                                            <div class="flex m-t-20 col-top">
                                                <span class="flex-none m-r-20">昵称：</span>
                                                <span>{{ row.user.nickname }}</span>
                                            </div>
                                            <div class="flex m-t-20 col-top">
                                                <span class="flex-none m-r-20">编号：</span>
                                                <span>{{ row.user.sn }}</span>
                                            </div>
                                        </div>
                                        <span slot="reference">{{ row.user.nickname }}</span>
                                    </el-popover>
                                </template>
                            </el-table-column>
                            <el-table-column label="实际支付">
                                <template v-slot="{ row }">
                                    <div class="flex">
                                        {{ row.order_integral }}积分
                                        <template v-if="row.order_amount > 0">+ {{ +row.order_amount }}元</template>
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column label="收货信息">
                                <template v-slot="{ row }">
                                    <el-popover placement="top" width="300" trigger="hover">
                                        <div>收件人：{{ row.address.contact }}</div>
                                        <div class="m-t-10">手机号：{{ row.address.mobile }}</div>
                                        <div class="m-t-10">
                                            地&nbsp;&nbsp;&nbsp;址：
                                            {{ row.delivery_address }}
                                        </div>
                                        <el-tag size="medium" slot="reference">{{ row.address.contact }}</el-tag>
                                    </el-popover>
                                </template>
                            </el-table-column>
                            <el-table-column label="订单状态">
                                <template v-slot="{ row }">
                                    <div class>{{ row.order_status_desc }}</div>
                                </template>
                            </el-table-column>
                            <el-table-column fixed="right" label="操作" min-width="200">
                                <template slot-scope="scope">
                                    <div class="flex">
                                        <!-- 详情 -->
                                        <router-link
                                            :to="{
                                                path: '/integral_mall/exchange_order_detail',
                                                query: { id: scope.row.id }
                                            }"
                                            class="m-r-10"
                                        >
                                            <el-button type="text" size="small">订单详情</el-button>
                                        </router-link>

                                        <!-- 物流查询 -->
                                        <order-logistics
                                            v-if="scope.row.admin_btns.delivery_btn"
                                            :flag="false"
                                            :id="scope.row.id"
                                            class="m-r-12"
                                        >
                                            <el-button slot="trigger" type="text" size="small">物流查询</el-button>
                                        </order-logistics>

                                        <!-- 订单发货 -->
                                        <order-logistics
                                            class="m-r-12"
                                            @update="getList()"
                                            :flag="true"
                                            :id="scope.row.id"
                                            v-if="scope.row.admin_btns.to_ship_btn"
                                        >
                                            <el-button slot="trigger" type="text" size="small">发货</el-button>
                                        </order-logistics>

                                        <!-- 确认收货 -->
                                        <ls-dialog
                                            class="inline m-r-12"
                                            title="确认收货"
                                            v-if="scope.row.admin_btns.confirm_btn"
                                            :content="`确定收货订单：${scope.row.sn}`"
                                            @confirm="orderConfirm(scope.row.id)"
                                        >
                                            <el-button slot="trigger" type="text" size="small">确认收货</el-button>
                                        </ls-dialog>
                                        <!-- 订单发货 -->
                                        <ls-dialog
                                            v-if="scope.row.admin_btns.cancel_btn"
                                            class="inline"
                                            title="取消订单"
                                            :content="`确定取消订单(${scope.row.sn})吗?请谨慎操作`"
                                            @confirm="orderCancel(scope.row.id)"
                                        >
                                            <el-button slot="trigger" type="text" size="small">取消订单</el-button>
                                        </ls-dialog>
                                        <!-- 确认收货 -->
                                        <ls-dialog
                                            v-if="scope.row.admin_btns.confirm_pay_btn"
                                            class="inline m-l-10"
                                            title="确认付款"
                                            :content="`确定付款订单：(${scope.row.sn})`"
                                            @confirm="orderPay(scope.row.id)"
                                        >
                                            <el-button slot="trigger" type="text" size="small">确认付款</el-button>
                                        </ls-dialog>
                                    </div>
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>
                    <div class="flex row-right m-t-16">
                        <ls-pagination v-model="pager" @change="getList()" />
                    </div>
                </el-tab-pane>
            </el-tabs>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'

import { RequestPaging } from '@/utils/util'
import {
    apiIntegralGoodsCancel,
    apiIntegralGoodsConfirm,
    apiIntegralOrderLists,
    apiIntegralGoodsPay
} from '@/api/application/integral_mall'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import OrderLogistics from '@/components/application/order-logistics.vue'
import DatePicker from '@/components/date-picker.vue'
@Component({
    components: {
        DatePicker,
        LsPagination,
        LsDialog,
        OrderLogistics
    }
})
export default class ExchangeOrder extends Vue {
    tabIndex = 0
    // 选项卡数据
    tabLists = [
        {
            status: '',
            label: '全部'
        },
        {
            status: 0,
            label: '待付款'
        },
        {
            status: 1,
            label: '待发货'
        },
        {
            status: 2,
            label: '待收货'
        },
        {
            status: 3,
            label: '已完成'
        },
        {
            status: 4,
            label: '已取消'
        }
    ]
    // 表单数据
    formData = {
        status: '', //列表状态:0-待付款 1-待发货 2-待收货 3-已发货 4-已取消
        sn: '', // 兑换单号
        goods_name: '', // 商品名称
        exchange_type: '', // 兑换类型:1-商品 2-红包
        start_time: '', //开始时间
        end_time: '' //开始时间
    }
    // 分页
    pager = new RequestPaging()
    getList(page?: number): void {
        page && (this.pager.page = page)
        const status = this.tabLists[this.tabIndex].status
        this.pager.request({
            callback: apiIntegralOrderLists,
            params: {
                ...this.formData,
                status
            }
        })
    }
    // 重置
    handleReset() {
        Object.keys(this.formData).forEach(key => {
            this.$set(this.formData, key, '')
        })
        this.getList()
    }

    // 取消订单
    orderCancel(id: Number) {
        apiIntegralGoodsCancel({ id: id }).then(res => {
            this.getList()
        })
    }

    // 确认付款
    orderPay(id: Number) {
        apiIntegralGoodsPay({ id: id }).then(res => {
            this.getList()
        })
    }

    // 确认收货
    orderConfirm(id: Number) {
        apiIntegralGoodsConfirm({ id: id }).then(res => {
            this.getList()
        })
    }
    created() {
        this.getList()
    }
}
</script>
