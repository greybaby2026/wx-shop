<template>
    <view>
        <scroll-view scroll-y="true" class="scroll-Y" style="height: calc(100vh - 170rpx)">
            <view class="p-10 item"> 订单编号：{{ express.sn }} </view>
            <view class="bg-white">
                <view v-for="item in orderGoods" :key="item.id" class="flex m-b-30">
                    <u-checkbox
                        @change="checkboxChange(item)"
                        v-model="item.checked"
                        v-if="type != 4"
                        :disabled="item.after_sale_status == 1"
                        style="flex-shrink: 0"
                    ></u-checkbox>

                    <view style="height: 150rpx; width: 150rpx; flex-shrink: 0" class="m-l-30">
                        <u-image :src="item.goods_image" width="150rpx" height="150rpx"></u-image>
                    </view>

                    <view class="m-l-20 flex-col row-around" style="height: 150rpx; width: 100%">
                        <view class="line">
                            {{ item.goods_name }}
                        </view>

                        <view class="flex row-right muted" v-if="item.after_sale_status == 1">
                            <span class="sm" style="color: red"> 售后中 </span>
                        </view>
                        <view class="flex row-between muted" v-else>
                            <span class="sm">
                                {{ item.spec_value_str }}
                            </span>
                            <span class="sm"> x{{ item.surplus_delivery_num }} </span>
                        </view>
                    </view>
                </view>
            </view>
            <view class="item" v-if="express.contact">
                {{ express.contact }},{{ express.mobile }},{{ express.delivery_address }}
            </view>
            <view class="bg-white flex row-right muted">
                <!-- <router-link :to="'/pages/address_edit/address_edit?id=' + form.id"> -->
                <view @click="handleAddress"><u-icon name="edit-pen"></u-icon>修改地址</view>
                <!-- </router-link> -->
                <view
                    class="m-l-20"
                    @click="
                        $copy(`${express.contact},${express.mobile},${express.delivery_address}`)
                    "
                    ><u-icon name="file-text"></u-icon>复制地址</view
                >
            </view>
            <template v-if="type != 4">
                <view class="bg-white">
                    <view class="card flex" @click="show = true">
                        <view class="m-r-30 bold">发货方式</view>
                        <view class="flex" v-if="send_type == ''" style="flex: 1">
                            <view class="muted"> 请选择 </view>
                            <u-icon name="arrow-down" style="margin-left: auto"></u-icon>
                        </view>
                        <view class="flex row-between" v-else style="flex: 1">
                            {{ send_type == 1 ? '快递发货' : '无需物流' }}
                            <u-icon name="arrow-down"></u-icon>
                        </view>
                    </view>
                </view>
                <template v-if="send_type == 1">
                    <view v-for="(item, index) in parcel">
                        <template>
                            <view style="padding: 0 30rpx" class="flex row-between">
                                <span> 包裹{{ index + 1 }} </span>

                                <u-icon
                                    name="trash"
                                    style="margin-left: auto"
                                    @click="handleDel(index)"
                                    v-if="parcel.length > 1"
                                ></u-icon>
                            </view>
                            <view class="bg-white">
                                <view class="card flex">
                                    <view class="m-r-30 bold">物流单号</view>
                                    <view class="flex" style="flex: 1">
                                        <input
                                            v-model="item.invoice_no"
                                            placeholder="请输入物流单号"
                                        />
                                    </view>
                                </view>
                            </view>
                            <view class="bg-white" style="padding: 0 30rpx 30rpx 30rpx">
                                <view class="card flex" @click="handleSelect(index)">
                                    <view class="m-r-30 bold">物流公司</view>
                                    <view class="flex" style="flex: 1">
                                        <view class="muted" v-if="item.express_id == ''">
                                            请选择物流公司
                                        </view>

                                        <view class="" v-else>
                                            {{ getexpressName(item.express_id) }}
                                        </view>

                                        <u-icon
                                            name="arrow-down"
                                            style="margin-left: auto"
                                        ></u-icon>
                                    </view>
                                    <u-select
                                        v-model="showLists"
                                        @confirm="confirmLists($event, index)"
                                        :list="express.express"
                                        label-name="name"
                                        value-name="id"
                                        mode="single-column"
                                    ></u-select>
                                </view>
                            </view>
                        </template>
                    </view>

                    <view>
                        <button class="btn" @click="handleAdd">添加包裹</button>
                    </view>
                </template>
                <view class="tip xxs" v-if="send_type == 2">
                    <u-icon name="info-circle" class="m-r-10"></u-icon>
                    如果该物品无需物流运送，可直接点击下方确认并发货</view
                >

                <view class="bg-white" style="padding: 50rpx 30rpx 30rpx 30rpx">
                    <view
                        class="card flex col-top"
                        @click="toAddress(delivery_address.id, 'delivery')"
                    >
                        <view class="m-r-30 bold">发货地址</view>
                        <view class="" style="flex: 1">
                            <view>
                                {{ delivery_address.contact }},{{ delivery_address.mobile }}
                            </view>
                            <view class="m-t-10 m-b-10">
                                {{ delivery_address.province }}{{ delivery_address.city
                                }}{{ delivery_address.district }}
                            </view>
                            <view>
                                {{ delivery_address.address }}
                            </view>
                        </view>
                        <view style="align-self: center">
                            <u-icon name="arrow-right" style="margin-left: auto"></u-icon>
                        </view>
                    </view>
                </view>
                <view class="bg-white" style="padding: 0rpx 30rpx 30rpx 30rpx">
                    <view class="card flex col-top" @click="toAddress(return_address.id, 'return')">
                        <view class="m-r-30 bold">退货地址</view>
                        <view class="" style="flex: 1">
                            <view> {{ return_address.contact }},{{ return_address.mobile }} </view>
                            <view class="m-t-10 m-b-10">
                                {{ return_address.province }}{{ return_address.city
                                }}{{ return_address.district }}
                            </view>
                            <view>
                                {{ return_address.address }}
                            </view>
                        </view>
                        <view style="align-self: center">
                            <u-icon name="arrow-right" style="margin-left: auto"></u-icon>
                        </view>
                    </view>
                </view>
                <view class="bg-white" style="padding: 0rpx 30rpx 30rpx 30rpx">
                    <view class="card flex col-top">
                        <view class="m-r-30 bold" style="width: 100rpx">备注</view>
                        <view class="" style="flex: 1">
                            <input v-model="remark" placeholder="发货备注，仅自己可见" />
                        </view>
                    </view>
                </view>
            </template>
            <template v-else>
                <view class="bg-white" style="padding: 0rpx 30rpx 30rpx 30rpx">
                    <view class="card flex col-top">
                        <view class="m-r-30" style="flex-shrink: 0">发货内容</view>
                        <view class="" style="flex: 1">
                            <u-input
                                :custom-style="{ padding: 0 }"
                                v-model="delivery_content"
                                type="textarea"
                                height="500"
                            />
                        </view>
                    </view>
                </view>
                <view class="bg-white" style="padding: 0rpx 30rpx 30rpx 30rpx">
                    <view class="card flex col-top">
                        <view class="m-r-30" style="width: 120rpx">备注</view>
                        <view class="" style="flex: 1">
                            <input v-model="remark" placeholder="发货备注，仅自己可见" />
                        </view>
                    </view>
                </view>
            </template>

            <u-select
                v-model="show"
                @confirm="confirm"
                :list="[
                    {
                        value: '1',
                        label: '快递发货'
                    },
                    {
                        value: '2',
                        label: '无需快递'
                    }
                ]"
                mode="single-column"
            ></u-select>
        </scroll-view>
        <button class="btn-delivery" @click="onSubmit" v-if="!show && !showLists">发货</button>
    </view>
</template>

<script>
import { apiOrderDelivery, apiOrderExpress } from '@/api/order'
import { trottle } from '@/utils/tools'
export default {
    data() {
        return {
            form: {
                id: 0,
                invoice_no: '',
                send_type: 1,
                shipping_id: 0,
                express_id: '', //物流公司ID
                delivery_content: '' //虚拟订单内容
            },

            delivery_content: '',

            type: '', //订单类型
            show: false,
            showLists: false,
            express: [],
            curData: {},
            orderGoods: [],
            send_type: 1,
            remark: '',
            expressIndex: 0,
            parcel: [
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
            ],
            return_address: {},
            delivery_address: {}
        }
    },

    onLoad() {
        try {
            const id = this.$Route.query.id
            this.type = this.$Route.query.type
            console.log(id, this.type)
            if (id) {
                this.form.id = id
                this.getExpressFunc(this.form.id)
            }
        } catch (e) {
            //TODO handle the exception
        }
    },
    onShow() {
        this.onSubmit = trottle(this.onSubmit, 3000)
    },
    computed: {
        selected_good() {
            return this.orderGoods.filter((i) => {
                return i.checked == true
            })
        }
    },

    methods: {
        handleSelect(val) {
            this.showLists = true
            this.expressIndex = val
        },
        handleAddress() {
            uni.$once('editAddress', () => {
                this.getExpressFunc(this.form.id)
            })
            this.$Router.push({
                path: '/pages/address_edit/address_edit',
                query: {
                    id: this.form.id
                }
            })
        },
        toAddress(id, type) {
            uni.$once('address', (row, type) => {
                if (type == 'delivery') {
                    this.delivery_address = row
                } else {
                    this.return_address = row
                }
            })
            this.$Router.push({
                path: '/bundle/pages/delivery_address/delivery_address',
                query: {
                    id,
                    type
                }
            })
        },
        getexpressName(id) {
            const { name } = this.express.express.find((i) => {
                return i.id == id
            })
            return name
        },
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
        },
        handleDel(val) {
            this.parcel = this.parcel.filter((i, index) => {
                return index != val
            })
        },

        checkboxChange(item) {
            item.checked = !item.checked
        },
        async onSubmit() {
            this.parcel.map((i) => {
                for (let num = 1; num < this.selected_good.length; num++) {
                    i.order_goods_info.push({
                        order_goods_id: '',
                        delivery_num: ''
                    })
                }
                this.selected_good.map((item, index) => {
                    i.order_goods_info[index].order_goods_id = item.id
                    i.order_goods_info[index].delivery_num = item.surplus_delivery_num
                })
            })

            let params = {}
            if (this.type != 4) {
                params = {
                    id: this.form.id,
                    delivery_address_id: this.delivery_address.id,
                    return_address_id: this.return_address.id,
                    order_goods_ids: this.selected_good.map((i) => {
                        return i.id
                    }),
                    send_type: this.send_type,
                    parcel: this.parcel,
                    remark: this.remark
                }
            } else {
                params = {
                    id: this.form.id,
                    delivery_content_type: 0,
                    delivery_content: this.delivery_content,
                    remark: this.remark
                }
            }
            await apiOrderDelivery({ ...params })
            setTimeout(() => {
                this.$Router.back()
            }, 1000)
        },

        confirm(event) {
            this.send_type = event[0].value
        },
        confirmLists(e, index) {
            console.log(index, e)
            this.parcel[this.expressIndex].express_id = e[0].value
        },

        async getExpressFunc(id) {
            const res = await apiOrderExpress({ id })
            this.express = res
            this.orderGoods = res.order_goods
            this.return_address = res.company_address.return_address
            this.delivery_address = res.company_address.delivery_address
            this.orderGoods.map((i) => {
                if (i.after_sale_status == 1) {
                    this.$set(i, 'checked', false)
                } else {
                    this.$set(i, 'checked', true)
                }
            })
        }
    }
}
</script>

<style lang="scss">
page {
    background-color: white;
}
.bg-white {
    background-color: $-color-white;
    padding: 30rpx;
}
.item {
    padding: 30rpx;
    /* #ifndef APP-NVUE */
    display: flex;
    /* #endif */
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    background-color: $-color-white;

    // > view:first-child {
    //     width: 180rpx;
    //     color: $-color-black;
    //     font-size: $-font-size-nr;
    //     font-weight: 500;
    // }

    // > view:last-child {
    //     flex: 1;
    //     text-align: right;

    //     textarea {
    //         width: 560rpx;
    //         height: 300rpx;
    //     }
    // }
}

.btn {
    width: 690rpx;
    height: 88rpx;
    margin: 0 auto;
    z-index: 99;

    line-height: 88rpx;
    background-color: #ecf1ff;
}
.card {
    background-color: #f7f7f7;
    padding: 30rpx;
    border-radius: 10px;
}
.container {
}
.btn-delivery {
    background-color: $-color-primary;
    height: 90rpx;
    margin: 0 auto;
    line-height: 90rpx;
    color: white;
    position: absolute;
    bottom: 50rpx;
    left: 20rpx;
    right: 20rpx;
    z-index: 9;
}
.tip {
    margin: 30rpx 30rpx 0 30rpx;
    padding: 10rpx 30rpx;
    background-color: #f5f8ff;
    border-radius: 5px;
}
</style>
