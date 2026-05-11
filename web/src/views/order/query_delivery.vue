<template>
    <div>
        <header>
            <div class="ls-card">
                <el-page-header @back="$router.go(-1)" content="物流详情" />
            </div>
        </header>

        <div class="ls-card m-t-24">
            <div class="flex row-lef col-centert">
                <div class="nr weight-500 xl">发货批次</div>
                <div class="flex m-l-40">
                    <span
                        v-for="item in orderData.delivery_time"
                        :key="item.id"
                        class="m-l-30"
                        :class="{ active: selectId == item.id }"
                        @click="handleSelect(item.id)"
                        style="cursor: pointer"
                        >{{ item.create_time }}</span
                    >
                </div>
            </div>
            <div class="m-t-15 flex row-between">
                <div class="flex">
                    <div>订单编号：{{ select_Info.order_sn }}</div>
                    <div class="m-l-60">支付时间：{{ select_Info.pay_time }}</div>
                </div>

                <div>
                    <el-popover placement="bottom-start" width="400" trigger="hover">
                        <div slot="reference" style="cursor: pointer" class="active">查看发货/退货信息</div>
                        <template>
                            <div class="lg weight-500">退货信息</div>
                            <div class="m-t-5">
                                退货地址：{{ select_Info.return_address_info.addresss }},{{
                                    select_Info.return_address_info.contact
                                }},{{ select_Info.return_address_info.mobile }}
                            </div>
                            <div class="lg weight-500 m-t-15">发货信息</div>
                            <div class="m-t-5">
                                发货地址：{{ select_Info.delivery_address_info.addresss }},{{
                                    select_Info.delivery_address_info.contact
                                }},{{ select_Info.delivery_address_info.mobile }}
                            </div>
                        </template>
                    </el-popover>
                </div>
            </div>
            <div class="m-t-15 flex">
                <div>
                    收货信息：{{ select_Info.receipt_address_info.addresss }},{{
                        select_Info.receipt_address_info.contact
                    }},{{ select_Info.receipt_address_info.mobile }}
                </div>
            </div>
            <div class="m-t-15 flex">
                <div>我的备注：{{ select_Info.remark || '-' }}</div>
            </div>
            <div class="flex row-lef col-centert m-t-24" v-show="select_parcel.invoice_no">
                <div class="nr weight-500 xl">包裹信息</div>
            </div>
            <div class="flex row-lef col-centert m-t-15">
                <div
                    style="width: 220px; height: 75px; cursor: pointer"
                    class="cards m-l-5"
                    :class="{ active_card: selectCardId == i.id }"
                    v-for="(i, index) in select_Info.parcel_info"
                    :key="i.id"
                    @click="handleSelectCard(i.id)"
                    v-show="select_parcel.invoice_no"
                >
                    <div class="flex row-between">
                        <span class="lg">包裹{{ index + 1 }}</span>
                        <span class="xxs lighter">共{{ deliveryNum(i) }}件</span>
                    </div>
                    <div class="flex row-between lighter">
                        <span>物流单号： {{ i.invoice_no }}</span>
                    </div>
                </div>
            </div>
            <div class="m-t-24 flex col-top">
                <div class="left">
                    <div class="flex row-lef col-centert">
                        <div class="nr weight-500 xl">包裹明细</div>
                    </div>
                    <div class="m-t-15 flex" v-if="select_parcel.invoice_no">
                        <div>物流单号：{{ select_parcel.invoice_no }}</div>
                        <ls-dialog class="inline m-l-24" :title="'修改快递单号'" width="25vw" @confirm="handleComfirm">
                            <div>
                                <el-alert
                                    class="xxl"
                                    title="频繁修改订单快递单号不利于准确追踪物流，请尽量避免。"
                                    type="info"
                                    :closable="false"
                                    show-icon
                                >
                                </el-alert>
                                <div class="m-t-24">物流单号</div>
                                <el-input class="m-t-10" placeholder="请输入快递单号" v-model="expressForm.invoice_no">
                                    <template slot="prepend">
                                        <div>
                                            <el-select
                                                style="width: 120px"
                                                v-model="expressForm.express_id"
                                                placeholder="请选择"
                                            >
                                                <el-option
                                                    :label="item.name"
                                                    :value="item.id"
                                                    v-for="(item, index) in express"
                                                    :key="index"
                                                ></el-option>
                                            </el-select>
                                        </div>
                                    </template>
                                </el-input>
                            </div>
                            <div class="m-l-24 active" style="cursor: pointer" slot="trigger">修改单号</div>
                        </ls-dialog>
                    </div>
                    <div class="m-t-15 flex" v-if="select_parcel.express_name">
                        <div>物流公司：{{ select_parcel.express_name }}</div>
                    </div>
                    <div class="m-t-15 flex">
                        <div>发货时间：{{ select_parcel.create_time }}</div>
                    </div>
                    <div class="m-t-15 flex">
                        <div>发货方式：{{ select_parcel.send_type_desc }}</div>
                    </div>
                    <div class="m-t-15 flex">
                        <div style="align-self: flex-start">包裹中的商品：</div>
                        <div>
                            <div
                                v-for="item in select_parcel.order_goods_info"
                                :key="item.goods_image"
                                class="flex m-b-5"
                            >
                                <el-image
                                    :src="item.goods_image"
                                    style="width: 68px; height: 68px; border-radius: 7px"
                                    class="flex-none"
                                ></el-image>
                                <div class="flex-col row-around m-l-5" style="height: 68px">
                                    <div class="flex">
                                        <div class="line-1" style="width: 300px">
                                            {{ item.goods_name }}
                                        </div>
                                        <span class="m-l-5"> X{{ item.delivery_num }} </span>
                                    </div>
                                    <div>¥{{ item.goods_price }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="right" v-if="select_parcel.send_type == 1">
                    <div class="flex row-lef col-centert">
                        <div class="nr weight-500 xl">物流动态</div>
                    </div>
                    <div class="m-t-15">
                        <el-timeline>
                            <el-timeline-item
                                v-for="(activity, index) in select_parcel.logistics_info.traces"
                                :key="index"
                                :timestamp="
                                    activity == '暂无物流信息'
                                        ? '暂无物流信息'
                                        : select_parcel.logistics_info.express_field == 'code100'
                                        ? activity[0]
                                        : activity[2]
                                "
                                :color="index == 0 ? 'rgba(33, 137, 255, 1)' : ''"
                            >
                                {{ activity == '暂无物流信息' ? '暂无物流信息' : activity[1] }}
                            </el-timeline-item>
                        </el-timeline>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import AreaSelect from '@/components/area-select.vue'
import OrderLogistics from '@/components/order/order-logistics.vue'
import { apiOrderDeliveryInfo, apiOrderLogistics, apiOrderDelivery, apiOrderChangeDelivery } from '@/api/order/order'

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
    form: any = { send_type: '' }
    expressForm: any = {
        express_id: '', //	是(配送方式为1时必选)	int	订单id
        invoice_no: ''
    }
    // 订单数据
    orderData: any = {
        order_goods: []
    }
    selectId: any = ''
    selectCardId: any = ''
    select_Info: any = {}
    select_parcel: any = {}
    express: any = {}
    // E Data

    // 获取物流查询
    getOrderLogistics() {
        apiOrderLogistics({ id: this.id }).then(res => {
            this.orderData = res
            this.selectId = res.delivery_time[0].id
            this.selectCardId = res.delivery_info[0].parcel_info[0].id
            this.select_Info = this.orderData.delivery_info.find((i: any) => {
                return this.selectId == i.delivery_time_id
            })
            this.select_parcel = this.orderData.delivery_info[0].parcel_info.find((i: any) => {
                return this.selectCardId == i.id
            })
        })
    }
    deliveryNum(item: any) {
        let sum = 0
        console.log(item)
        item.order_goods_info.map((i: any) => [(sum += Number(i.delivery_num))])
        return sum
    }
    handleSelect(id: number) {
        this.selectId = id
        this.select_Info = this.orderData.delivery_info.find((i: any) => {
            return this.selectId == i.delivery_time_id
        })
        console.log(this.select_Info)
        this.select_parcel = this.select_Info.parcel_info[0]
        // this.select_parcel = this.select_Info[0]
        this.selectCardId = this.select_parcel.id
    }
    handleSelectCard(id: number) {
        this.selectCardId = id
        this.select_parcel = this.select_Info.parcel_info.find((i: any) => {
            return this.selectCardId == i.id
        })
    }
    // 获取订单详情
    getOrderDetail() {
        apiOrderDeliveryInfo({ id: this.id }).then(res => {
            this.express = res.express
        })
    }
    handleComfirm() {
        apiOrderChangeDelivery({
            id: this.select_parcel.id,
            ...this.expressForm
        }).then(() => {
            this.getOrderLogistics()
            this.getOrderDetail()
        })
    }
    // E Methods

    created() {
        this.id = this.$route.query.id
        this.id && this.getOrderLogistics()
        this.getOrderDetail()
    }
}
</script>

<style lang="scss" scoped>
::v-deep .el-form .el-form-item {
    margin-bottom: 12px !important;
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
.active {
    color: #4073fa;
}
.cards {
    border: 2px solid rgba(247, 247, 247, 1);
    border-radius: 7px;
    padding: 10px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.active_card {
    border: 2px solid rgba(33, 137, 255, 1);
}
.left {
    width: 50%;
}
.right {
    width: 50%;
}
</style>
