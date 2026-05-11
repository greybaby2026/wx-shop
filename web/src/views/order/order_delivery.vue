<template>
    <div>
        <header>
            <div class="ls-card">
                <el-page-header @back="$router.go(-1)" content="订单发货" />
            </div>
        </header>
        <div class="flex m-t-24">
            <div class="ls-card">
                <div style="width: 100%">
                    <div class="nr weight-500 m-b-20 title">1.确认订单信息</div>
                </div>
                <div>
                    <div class="flex row-between m-t-24 m-b-24">
                        <div>订单编号：{{ orderData.sn }}</div>
                        <div>支付时间：{{ orderData.pay_time }}</div>
                    </div>

                    <el-table :data="orderData.order_goods" ref="paneTable" style="width: 100%" size="mini">
                        <el-table-column fixed="left" width="55" v-if="orderData.order_type != 4">
                            <template slot="header" slot-scope="scope">
                                <el-checkbox
                                    @change="handleCheckAll"
                                    v-model="checkedAll"
                                    true-label="true"
                                    false-label="false"
                                    :disabled="disabledAll"
                                ></el-checkbox>
                            </template>
                            <template slot-scope="scope">
                                <el-checkbox
                                    @change="handleCheck($event, scope.row)"
                                    v-model="scope.row.checked"
                                    true-label="true"
                                    false-label="false"
                                    :disabled="scope.row.surplus_delivery_num == 0 || scope.row.after_sale_status == 1"
                                ></el-checkbox>
                            </template>
                        </el-table-column>
                        <el-table-column label="商品信息" prop="id">
                            <template slot-scope="scope">
                                <div class="m-t-6 flex" style="height: 65px">
                                    <el-image
                                        :src="scope.row.goods_image"
                                        style="width: 78px; height: 78px"
                                        class="flex-none"
                                    >
                                    </el-image>
                                    <div class="m-l-10">
                                        <div class="flex row-between normal p-r-24 line-1" style="line-height: 12px">
                                            <span class="line-1 name">{{ scope.row.goods_name }}</span>
                                        </div>
                                        <div class="xs lighter flex line-1 p-r-24">
                                            规格：{{ scope.row.spec_value_str }}
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column label="价格">
                            <template slot-scope="scope">
                                <div class="xs muted p-r-24 line-1">
                                    <div>
                                        价格：<span class="normal m-r-10">¥{{ scope.row.goods_price }}</span>
                                    </div>
                                    <div>
                                        数量：<span class="normal">{{ scope.row.surplus_delivery_num }}</span>
                                    </div>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column label="订单状态" prop="after_sale_status_desc"></el-table-column>
                    </el-table>
                </div>

                <div>
                    <div class="m-b-20 m-t-20 m-l-30">
                        <span class="nr m-b-20 m-t-20" style="font-size: 14px"> 买家收货信息 </span>
                        <span> {{ orderData.delivery_address }},{{ orderData.contact }},{{ orderData.mobile }} </span>
                        <!-- 修改地址 -->
                        <ls-dialog
                            class="inline m-l-24"
                            :title="'收货地址修改'"
                            width="35vw"
                            @confirm="orderAddressSet"
                        >
                            <span class="" slot="trigger">
                                <i class="el-icon-edit"></i>
                            </span>
                            <div>
                                <div>
                                    <span>订单编号</span>
                                    <span class="m-l-30">{{ orderData.sn }}</span>
                                </div>
                                <div class="m-t-30">
                                    <div class="flex">
                                        <div style="align-self: flex-start">收货信息</div>

                                        <el-form ref="address" :model="address" label-width="80px">
                                            <el-form-item label="收货人" prop="return_district">
                                                <el-input class="ls-input" v-model="address.contact" show-word-limit />
                                            </el-form-item>
                                            <el-form-item label="收货电话" prop="return_district">
                                                <el-input class="ls-input" v-model="address.mobile" show-word-limit />
                                            </el-form-item>
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
                                </div>
                            </div>
                        </ls-dialog>
                    </div>
                </div>
                <template v-if="orderData.order_type != 4">
                    <div style="width: 100%" class="flex row-left col-center">
                        <div class="nr weight-500">2.确认发货/退货信息</div>
                        <div class="xs lighter flex line-1 m-l-10">
                            请正确填写发货/退货地址，以避免因地址填写不准确导致货物无法退回等风险
                        </div>
                    </div>
                    <div style="background-color: rgba(247, 247, 247, 1)" class="p-24 m-t-20">
                        <div class="">
                            <span class="nr m-b-20 m-t-20" style="font-size: 14px"> 我的发货信息 </span>
                            <template v-if="deliveryAddress">
                                <span>
                                    {{ deliveryAddress.province }},{{ deliveryAddress.city }},{{
                                        deliveryAddress.district
                                    }},{{ deliveryAddress.address }},{{ deliveryAddress.contact }},{{
                                        deliveryAddress.mobile
                                    }}
                                </span>
                            </template>
                            <template v-else>-</template>
                            <span class="m-l-10" @click="handleAddress('delivery', deliveryAddress.id)">
                                <i class="el-icon-edit"></i>
                            </span>
                        </div>
                        <div class="m-t-20">
                            <span class="nr m-b-20 m-t-20" style="font-size: 14px"> 我的退货信息 </span>
                            <template v-if="returnAddress">
                                <span>
                                    {{ returnAddress.province }},{{ returnAddress.city }},{{
                                        returnAddress.district
                                    }},{{ returnAddress.address }},{{ returnAddress.contact }},{{
                                        returnAddress.mobile
                                    }}</span
                                >
                            </template>
                            <template v-else> - </template>
                            <span class="m-l-10">
                                <i class="el-icon-edit" @click="handleAddress('return', returnAddress.id)"></i>
                            </span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        <div class="flex m-t-24 m-b-60">
            <div class="ls-card">
                <template v-if="orderData.order_type != 4">
                    <div style="width: 100%" class="flex row-left">
                        <div class="nr weight-500">3.选择发货方式</div>
                        <div class="m-l-20">
                            <el-radio v-model="send_type" :label="1">需要物流</el-radio>
                            <el-radio v-model="send_type" :label="2">无需物流</el-radio>
                        </div>
                    </div>

                    <div class="m-l-30 m-t-30" v-if="send_type == 1">
                        <div v-for="(item, index) in parcel" :key="index" class="m-t-30 flex">
                            <div class="m-r-5 nr weight-500 m-t-10" style="align-self: flex-start">
                                包裹{{ index + 1 }}
                            </div>
                            <div>
                                <div class="flex">
                                    <el-input
                                        class="m-r-10"
                                        style="width: 520px"
                                        placeholder="请输入快递单号"
                                        v-model="item.invoice_no"
                                    >
                                        <template slot="prepend">
                                            <div>
                                                <el-select
                                                    style="width: 120px"
                                                    v-model="item.express_id"
                                                    placeholder="请选择"
                                                >
                                                    <el-option
                                                        :label="item.name"
                                                        :value="item.id"
                                                        v-for="(item, index) in orderData.express"
                                                        :key="index"
                                                    ></el-option>
                                                </el-select>
                                            </div>
                                        </template>
                                    </el-input>
                                    <el-button type="primary" @click="handleAdd">添加包裹</el-button>
                                    <el-button @click="handleDel(index)" icon="el-icon-delete"></el-button>
                                </div>

                                <div
                                    class="m-t-10 flex"
                                    v-for="(i, inde) in item.order_goods_info"
                                    :key="inde"
                                    v-show="parcel.length != 1"
                                >
                                    <el-select v-model="i.order_goods_id" placeholder="请选择" style="width: 540px">
                                        <el-option
                                            :label="Item.goods_name"
                                            :value="Item.id"
                                            v-for="(Item, index) in goodschecked"
                                            :key="index"
                                            v-show="!item.order_goods_info.map(I => I.order_goods_id).includes(Item.id)"
                                        >
                                        </el-option>
                                    </el-select>
                                    <!-- item.order_goods_info.map(I => I.order_goods_id).includes(item.id) -->
                                    <el-input style="width: 150px" class="m-l-10" v-model="i.delivery_num">
                                        <template slot="append">件</template></el-input
                                    >
                                    <i
                                        class="el-icon-circle-plus-outline m-l-10"
                                        style="font-size: 18px; cursor: pointer"
                                        @click="handleGoodadd(index)"
                                    ></i>
                                    <i
                                        class="el-icon-remove-outline m-l-10"
                                        style="font-size: 18px; cursor: pointer"
                                        @click="handleGooddel(index, inde)"
                                        v-if="item.order_goods_info.length > 1"
                                    ></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="send_type == 2">
                        <div class="xs lighter flex line-1 m-l-60 m-t-10">
                            如果该快递无需物流运送，可直接点击下方确认并发货
                        </div>
                    </div>
                </template>
                <!-- 虚拟订单 -->
                <template v-if="orderData.order_type == 4">
                    <div style="width: 100%">
                        <div class="nr weight-500">2.选择发货类型</div>
                        <div class="m-t-15">
                            <el-form ref="form" :model="form" size="small" label-width="80px">
                                <el-form-item label="发货类型">
                                    <el-radio-group v-model="form.delivery_content_type">
                                        <el-radio label="0">固定内容</el-radio>
                                        <el-radio label="1">自定义内容</el-radio>
                                    </el-radio-group>
                                </el-form-item>
                                <el-form-item label="发货内容">
                                    <el-input
                                        class="m-t-10"
                                        type="textarea"
                                        style="max-width: 520px; width: 100%"
                                        :rows="7"
                                        placeholder="请输入内容"
                                        v-model="form.delivery_content"
                                        @change="handleChange"
                                        v-if="form.delivery_content_type == 0"
                                    ></el-input>
                                    <div v-else>
                                        <el-table ref="table" size="mini" :data="form.delivery_content1">
                                            <el-table-column label="名称">
                                                <template #default="scope">
                                                    <el-input placeholder="请输入" v-model="scope.row.name"></el-input>
                                                </template>
                                            </el-table-column>
                                            <el-table-column label="内容">
                                                <template #default="scope">
                                                    <el-input
                                                        placeholder="请输入"
                                                        v-model="scope.row.content"
                                                    ></el-input>
                                                </template>
                                            </el-table-column>
                                            <el-table-column label="操作">
                                                <template #default="scope">
                                                    <el-button type="danger" @click="handleContentDel(scope.$index)"
                                                        >删除</el-button
                                                    >
                                                </template>
                                            </el-table-column>
                                        </el-table>
                                        <el-button type="text" @click="handleContentAdd">添加字段</el-button>
                                    </div>
                                </el-form-item>
                            </el-form>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        <div class="ls-release__footer bg-white ls-fixed-footer">
            <div class="btns row-center flex" style="height: 100%">
                <el-button
                    type="primary"
                    @click="orderData.order_type == 4 ? handleVirtualSave() : handleSave()"
                    style="margin-left: auto"
                    >确认并发货</el-button
                >
                <el-input
                    placeholder="发货备注，仅自己可见"
                    v-model="remark"
                    style="width: 400px; margin-left: auto; margin-right: 20px"
                ></el-input>
            </div>
        </div>

        <SelectAddress
            ref="SelectAddressRef"
            @select="handleSelect"
            v-if="SelectAddressshow"
            @close="SelectAddressshow = false"
        ></SelectAddress>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import AreaSelect from '@/components/area-select.vue'
import OrderLogistics from '@/components/order/order-logistics.vue'
import { apiOrderAddressEdit, apiOrderDeliveryInfo, apiOrderDelivery } from '@/api/order/order'
import { apiSelffetchVerification } from '@/api/application/selffetch'
import SelectAddress from '@/components/order/select-address.vue'
import { apiaddressLists } from '@/api/application/address'
import { throttle, debounce } from '@/utils/util' //节流 防抖

@Component({
    components: {
        LsDialog,
        AreaSelect,
        OrderLogistics,
        SelectAddress
    }
})
export default class OrderDetail extends Vue {
    $refs!: { verifyDialogRef: any; SelectAddressRef: any }
    // S Data

    // 订单详情ID
    id: any = 0
    // 发货表单
    form: any = {
        send_type: 1, //	是	int	配送方式:1-快递配送;2-无需快递
        express_id: '', //	是(配送方式为1时必选)	int	订单id
        invoice_no: '', //	是(配送方式为1时必选)	int	订单id
        remark: '', //	否	varchar	发货备注
        delivery_content: '', //发货内容
        delivery_content_type: '0',
        delivery_content1: [],
        pay_way: ''
    }
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
        },
        order_goods: []
    }
    checkedAll: any = false
    goodschecked: any = []
    SelectAddressshow: any = false
    deliveryAddress: any = {}
    returnAddress: any = {}
    address: any = {
        province_id: '', //必填	int	所在地区:省id
        city_id: '', //必填	int	所在地区:市id
        district_id: '', //必填	int	所在地区:区id
        address: '', //必填	varchar	详细地址
        contact: '',
        mobile: ''
    }
    remark: String = ''
    verifyTips = ''
    send_type: any = 1

    // 物流公司
    express_id: String = ''

    // 快递单号
    invoice_no: String = ''
    parcel: any = [
        {
            invoice_no: '',
            express_id: '',
            order_goods_info: [
                {
                    order_goods_id: '',
                    delivery_num: ''
                }
            ]
        }
    ]
    // E Data

    // S Methods
    handleSelect(val: any, type: any) {
        if (type == 'delivery') {
            this.deliveryAddress = val
        } else {
            this.returnAddress = val
        }
        this.SelectAddressshow = false
    }
    handleChange(val: string) {
        this.form.delivery_content = val.trim()
    }
    handleSave() {
        if (this.parcel.length == 1) {
            this.goodschecked.map((i: any, index: any) => {
                this.parcel[0].order_goods_info[index] = { order_goods_id: '', delivery_num: '' }
                this.parcel[0].order_goods_info[index].order_goods_id = i.id
                this.parcel[0].order_goods_info[index].delivery_num = i.surplus_delivery_num
            })
        }

        apiOrderDelivery({
            id: this.orderData.id,
            delivery_address_id: this.deliveryAddress.id,
            return_address_id: this.returnAddress.id,
            send_type: this.send_type,
            parcel: this.parcel,
            remark: this.remark,
            order_goods_ids: this.goodschecked.map((i: any) => {
                return i.id
            })
        }).then(res => {
            this.$router.go(-1)
        })
    }
    handleVirtualSave() {
        this.form = {
            delivery_content_type: this.form.delivery_content_type,
            delivery_content: this.form.delivery_content,
            delivery_content1: this.form.delivery_content1,
            remark: this.remark
        }

        apiOrderDelivery({
            id: this.id,
            ...this.form
        }).then(res => {
            this.$router.go(-1)
        })
    }

    handleContentDel(delval: number) {
        this.form.delivery_content1 = this.form.delivery_content1.filter((i: any, index: number) => {
            return index != delval
        })
    }
    handleContentAdd() {
        this.form.delivery_content1.push({ name: '', content: '' })
    }
    handleAdd() {
        this.parcel.push({
            invoice_no: '',
            express_id: '',
            order_goods_info: [
                {
                    order_goods_id: '',
                    delivery_num: ''
                }
            ]
        })
    }
    handleDel(index: any) {
        this.parcel = this.parcel.filter((i: any, Index: any) => {
            if (this.parcel.length > 1) {
                return index != Index
            }
            return true
        })
    }
    handleGoodadd(index: any) {
        this.parcel[index].order_goods_info.push({
            order_goods_id: '',
            delivery_num: ''
        })
    }
    handleGooddel(index: any, inde: any) {
        this.parcel[index].order_goods_info = this.parcel[index].order_goods_info.filter((i: any, index: any) => {
            return index != inde
        })
    }
    handleAddress(type: string, id: number) {
        this.SelectAddressshow = true
        this.$nextTick(() => {
            this.$refs.SelectAddressRef.openDialog(type, id)
        })
    }
    // 获取订单详情
    getOrderDetail() {
        apiOrderDeliveryInfo({ id: this.id }).then(res => {
            this.orderData = res
            this.orderData.order_goods = this.orderData.order_goods.map((i: any) => {
                return { ...i, checked: false }
            })
            this.address.mobile = this.orderData.mobile
            this.address.contact = this.orderData.contact
            this.deliveryAddress = this.orderData.company_address.delivery_address
            this.returnAddress = this.orderData.company_address.return_address
        })
    }
    handleCheck(e: any, row: any) {
        this.checkedAll = this.orderData.order_goods.every((i: any) => {
            return i.checked == 'true'
        })
        this.goodschecked = this.orderData.order_goods.filter((i: any) => {
            return i.checked == 'true'
        })
    }

    handleCheckAll(e: any) {
        this.orderData.order_goods.map((i: any) => {
            if (i.after_sale_status == 0) {
                return (i.checked = e == 'true' ? 'true' : 'false')
            }
        })
        this.goodschecked = this.orderData.order_goods.filter((i: any) => {
            return i.checked == 'true'
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
    get disabledAll() {
        return this.orderData.order_goods.every((i: any) => {
            return i.after_sale_status == 1
        })
    }

    // E Methods

    created() {
        this.id = this.$route.query.id
        this.id && this.getOrderDetail()
        this.handleVirtualSave = debounce(this.handleVirtualSave, 1000)
        this.handleSave = debounce(this.handleSave, 1000)
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
</style>
