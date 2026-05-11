<template>
    <view class="order-detail p-l-20 p-r-20" v-show="orderInfo.id">
        <view class="header">
            <view class="item">
                <view class="white lg m-b-10">{{ orderInfo.order_status_desc }}</view>
            </view>
        </view>

        <view class="address-wrap flex contain bg-white row-between">
            <image
                class="m-r-20"
                style="width: 40rpx; height: 40rpx"
                src="/static/images/icon_address.png"
            ></image>
            <view class="address flex-1">
                <view v-if="orderInfo.delivery_type == 2">
                    <view class="name md bold">{{ orderInfo.selffetch_shop.name }}</view>
                    <view class="phone md m-t-10">{{
                        orderInfo.selffetch_shop.detailed_address
                    }}</view>
                    <view class="area sm m-t-10 lighter"
                        >营业时间：{{ orderInfo.selffetch_shop.business_start_time }} -
                        {{ orderInfo.selffetch_shop.business_end_time }}</view
                    >
                </view>
                <view v-else>
                    <text class="name md bold m-r-10">{{ orderInfo.address.contact }}</text>
                    <text class="phone md">{{ orderInfo.address.mobile }}</text>
                    <view class="area sm m-t-10 lighter">{{ orderInfo.delivery_address }}</view>
                </view>
            </view>
            <view
                class="copy"
                @click="copy(orderInfo.delivery_address)"
                v-if="orderInfo.delivery_type != 2"
            >
                复制
            </view>
        </view>

        <view class="m-t-20 bg-white contain">
            <view class="p-t-20 p-b-20 p-l-20 flex"
                >买家昵称：{{ orderInfo.user.nickname }} （<text class="sm"
                    >编号:{{ orderInfo.user.sn }}</text
                >）
            </view>
        </view>
        <view
            class="bg-white m-t-20 contain"
            style="padding: 20rpx 20rpx 40rpx 20rpx"
            v-if="orderInfo.delivery_type == 2"
        >
            <view class="black md bold"> 提货信息 </view>
            <view class="sm">
                <view class="p-t-20"
                    >提货人：{{ orderInfo.address.contact }} ，{{ orderInfo.address.mobile }}</view
                >
            </view>
            <view class="sm">
                <view class="p-t-20">核销码：{{ orderInfo.pickup_code }}</view>
            </view>
            <view class="sm">
                <view class="p-t-20"
                    >核销状态：{{ orderInfo.verification_status == 0 ? '待核销' : '已核销' }}</view
                >
            </view>
            <view class="sm">
                <view class="p-t-20">提货时间：{{ orderInfo.verification_time || '-' }}</view>
            </view>
        </view>
        <view class="m-t-20 bg-white contain">
            <!-- Header -->
            <view class="order-header flex row-between">
                <view class="black md bold"> 商品信息 </view>
            </view>
            <view class="order-goods">
                <block v-for="(item, index) in orderInfo.order_goods">
                    <view class="goods flex m-b-20" @click="toDetail(orderInfo.id)">
                        <view class="image">
                            <u-image
                                :src="item.goods_image"
                                width="160rpx"
                                height="160rpx"
                            ></u-image>
                        </view>

                        <view class="m-l-16 line-2">
                            <!-- 订单名称 -->
                            <view class="m-b-10 flex row-between col-top">
                                <view class="order-name line">
                                    {{ item.goods_name }}
                                </view>
                                <view class="text-right" style="height: 100%">
                                    <view
                                        >¥{{
                                            orderInfo.order_type == 0 || orderInfo.order_type == 4
                                                ? item.original_price
                                                : item.goods_price
                                        }}</view
                                    >
                                </view>
                            </view>
                            <!-- 商品规格 -->
                            <view class="order-str m-t-10 flex row-between"
                                ><span>
                                    {{ item.spec_value_str }}
                                </span>
                                <span style="width: 50rpx">x{{ item.goods_num }}</span>
                            </view>
                        </view>
                    </view>
                </block>
            </view>
            <!--     <view class="p-l-20 p-r-20 nr flex row-between">
                            <view class="lighter">需付款：
                            </view>
                            <view style="color: #FF0000;font-size: 32rpx;font-weight: 500;">¥{{ orderInfo.order_amount }}
                            </view>
                        </view>
     -->
            <view class="order-price">
                <view class="price-item flex row-between">
                    <view>商品总价：</view>
                    <view
                        >¥{{
                            orderInfo.order_type == 0 || orderInfo.order_type == 4
                                ? orderInfo.total_original_price
                                : orderInfo.goods_price
                        }}</view
                    >
                </view>
                <view class="price-item flex row-between">
                    <view>运费：</view>
                    <view>¥{{ orderInfo.express_price }}</view>
                </view>
                <view class="price-item flex row-between" v-if="orderInfo.discount_amount > 0">
                    <view>优惠券：</view>
                    <view style="color: #ff0000">-¥{{ orderInfo.discount_amount }}</view>
                </view>
                <view class="price-item flex row-between" v-if="orderInfo.member_amount > 0">
                    <view>会员折扣：</view>
                    <view style="color: #ff0000">-¥{{ orderInfo.member_amount }}</view>
                </view>
                <view class="price-item flex row-between" v-if="orderInfo.change_price > 0">
                    <view>商品改价：</view>
                    <view style="color: #ff0000">-¥{{ orderInfo.change_price }}</view>
                </view>
                <view class="price-item flex row-right">
                    <view class="muted sm">实付款：</view>
                    <view style="color: #ff0000">¥{{ orderInfo.order_amount }}</view>
                </view>
            </view>
        </view>

        <view
            class="bg-white m-t-20 contain"
            style="padding: 20rpx 20rpx 40rpx 20rpx"
            v-if="orderInfo.delivery_content"
        >
            <view class="black md bold"> 发货内容 </view>
            <view class="normal sm m-t-20">
                <view>
                    {{ orderInfo.delivery_content || '-' }}
                </view>
                <view class="flex row-right m-t-30" @click="copy(orderInfo.delivery_content)">
                    <view class="copy-btn">复制</view>
                </view>
            </view>
        </view>

        <view
            class="bg-white m-t-20 contain"
            style="padding: 20rpx 20rpx 40rpx 20rpx"
            v-if="orderInfo.user_remark"
        >
            <view class="black md bold"> 买家留言 </view>
            <view class="normal sm m-t-20" style="word-wrap: break-word">
                {{ orderInfo.user_remark }}
            </view>
        </view>

        <!-- 发票信息 -->
        <!-- <view class="bg-white m-t-20 contain" style="padding: 20rpx 20rpx 50rpx 20rpx;" v-if="orderInfo.invoice != null">
                <view class="black md bold">
                    发票信息
                </view>
                <view class="normal sm m-t-20">
                    发票类型： {{ orderInfo.invoice.type == 0 ? '普通发票' : '专用发票' }}
                </view>
                <view class="normal sm m-t-20">
                    抬头名称： {{ orderInfo.invoice.name }}
                </view>
                <view class="normal sm m-t-20">
                    抬头类型： {{ orderInfo.invoice.header_type == 0 ? '个人' : '企业' }}
                </view>
                <view class="normal sm m-t-20" v-if="orderInfo.invoice.duty_number">
                    企业税号： {{ orderInfo.invoice.duty_number }}
                </view>
                <view class="normal sm m-t-20">
                    电子邮箱： {{ orderInfo.invoice.email }}
                </view>
                <view class="normal sm m-t-20" v-if="orderInfo.invoice.address">
                    企业地址： {{ orderInfo.invoice.address }}
                </view>
                <view class="normal sm m-t-20" v-if="orderInfo.invoice.mobile">
                    企业电话： {{ orderInfo.invoice.mobile }}
                </view>
                <view class="normal sm m-t-20" v-if="orderInfo.invoice.bank">
                    开户银行： {{ orderInfo.invoice.bank }}
                </view>
                <view class="normal sm m-t-20" v-if="orderInfo.invoice.bank_account">
                    银行账号： {{ orderInfo.invoice.bank_account }}
                </view>
            </view> -->

        <view class="bg-white m-t-20 contain" style="padding: 20rpx 20rpx 40rpx 20rpx">
            <view class="black md bold"> 订单信息 </view>
            <view class="normal sm m-t-20"> 订单编号： {{ orderInfo.sn }} </view>
            <view class="normal sm m-t-20">
                订单类型： {{ orderInfo.order_type_desc }}({{ orderInfo.order_terminal_desc }})
            </view>
            <view class="normal sm m-t-20"> 配送方式： {{ orderInfo.delivery_type_desc }} </view>
            <view class="normal sm m-t-20"> 支付方式： {{ orderInfo.pay_way_desc || '——' }} </view>
            <view class="normal sm m-t-20"> 下单时间： {{ orderInfo.create_time }} </view>
            <view class="normal sm m-t-20"> 支付时间： {{ orderInfo.pay_time }} </view>
            <view class="normal sm m-t-20"> 完成时间： {{ orderInfo.confirm_take_time }} </view>
        </view>

        <view class="footer bg-white flex row-right">
            <template v-if="orderInfo.businesse_btn.editaddress_btn">
                <router-link :to="'/pages/address_edit/address_edit?id=' + orderInfo.id">
                    <button class="btn hollow br60 flex row-center normal">修改地址</button>
                </router-link>
            </template>
            <template v-if="orderInfo.businesse_btn.cancel_btn">
                <button
                    class="btn hollow br60 flex row-center normal"
                    @click="openFunc(orderInfo, 'close')"
                >
                    取消订单
                </button>
            </template>
            <template v-if="orderInfo.businesse_btn.deliver_btn">
                <router-link
                    :to="
                        '/bundle/pages/deliver_goods/deliver_goods?id=' +
                        orderInfo.id +
                        '&type=' +
                        orderInfo.order_type
                    "
                >
                    <button class="btn solid br60 flex row-center normal">去发货</button>
                </router-link>
            </template>
            <template v-if="orderInfo.businesse_btn.confirm_btn">
                <button
                    class="btn solid br60 flex row-center normal"
                    @click="openFunc(orderInfo, 'confirm')"
                >
                    确认收货
                </button>
            </template>
            <template v-if="orderInfo.businesse_btn.confirm_pay_btn">
                <button
                    class="btn solid br60 flex row-center normal"
                    @click="openFunc(orderInfo, 'confirmpay')"
                >
                    确认付款
                </button>
            </template>
            <template>
                <button
                    class="btn hollow br60 flex row-center normal"
                    @click="toTraces(orderInfo.id)"
                    v-if="orderInfo.businesse_btn.logistics_btn"
                >
                    查看物流
                </button>
            </template>
            <template v-if="orderInfo.businesse_btn.verification_btn">
                <!-- <router-link :to="'/pages/verification_order/verification_order'"> -->
                <button
                    @click="openFunc(orderInfo, 'verification')"
                    class="btn solid br60 flex row-center normal"
                >
                    提货核销
                </button>
                <!-- </router-link> -->
            </template>
        </view>

        <modal height="200rpx" v-model="close" @confirm="orderSetting">
            <view class="black nr flex row-center" style="height: 200rpx"> 确认取消该订单吗? </view>
        </modal>

        <modal height="200rpx" v-model="del" @confirm="orderSetting">
            <view class="black nr flex row-center" style="height: 200rpx"> 确认删除该订单吗? </view>
        </modal>

        <modal height="200rpx" v-model="confirm" @confirm="orderSetting">
            <view class="black nr flex row-center" style="height: 200rpx">
                确认用户已收到货？请谨慎处理！
            </view>
        </modal>
        <modal height="200rpx" v-model="verification" @confirm="orderSetting">
            <view class="black nr" style="height: 200rpx"> 确认核销该订单？ </view>
        </modal>
        <modal height="100rpx" v-model="verificationDialog" @confirm="orderSetting(1)">
            <view class="black nr" style="height: 200rpx">
                {{ verifyTips }}
            </view>
        </modal>
        <modal height="100rpx" v-model="confirmpay" @confirm="orderSetting">
            <view class="black nr" style="height: 200rpx"> 确认用户已付款？请谨慎处理！ </view>
        </modal>
        <u-modal :cancelShow="false" title="查看物流" v-model="recycle">
            <view class="black nr p-40" style="height: 200rpx">
                <view>快递公司： {{ logistics.shipping_name || '-' }}</view>
                <view class="m-t-20"
                    >物流单号： {{ logistics.invoice_no || '-' }}
                    <text
                        v-if="logistics.invoice_no"
                        class="copy"
                        @click="copy(logistics.invoice_no)"
                        >复制</text
                    ></view
                >
            </view>
        </u-modal>

        <u-toast ref="uToast" />
    </view>
</template>

<script>
import {
    apiOrderDetail,
    apiOrderClose,
    apiOrderConfirm,
    apiOrderLogistics,
    apiOrderDelete,
    apiVerificationOrderConfirm,
    apiOrderConfirmpay
} from '@/api/order'
import { copy } from '@/utils/tools.js'

export default {
    name: 'orderDetail',

    data() {
        return {
            orderInfo: {
                user: {
                    nickname: ''
                },
                address: {},
                businesse_btn: {}
            }, // 商品信息

            action: '',
            verifyTips: '',

            close: false,
            del: false,
            editAddressStatus: false, //编辑地址弹框状态
            confirm: false,
            recycle: false,
            confirmpay: false,

            // 物流
            logistics: {
                shipping_name: '-',
                invoice_no: ''
            },
            verification: false,
            verificationDialog: false
        }
    },

    methods: {
        toTraces(id) {
            this.$Router.push({
                path: '/bundle/pages/orderTraces/orderTraces',
                query: { id }
            })
        },
        // 初始化商品详情数据
        initOrderDetail() {
            return new Promise((resolve, reject) => {
                apiOrderDetail({
                    id: this.order_id
                })
                    .then((data) => {
                        this.orderInfo = data
                    })
                    .catch((err) => {
                        reject(err)
                    })
            })
        },

        copy(content) {
            copy(content)
        },

        async openFunc(item, action) {
            if (action == 'recycle') {
                const res = await apiOrderLogistics({ id: item.id })
                this.logistics.shipping_name = res.order.express_name
                this.logistics.invoice_no = res.order.invoice_no
            }

            this.curData = item
            this.action = action
            this[action] = true
        },

        //修改地址
        async editAddress() {
            this.editAddressStatus = true
        },

        // 操作订单
        async orderSetting(isConfirm = 0) {
            let id = this.curData.id,
                action = this.action

            if (action == 'close') {
                await apiOrderClose({ id })
            } else if (action == 'del') {
                await apiOrderDelete({ id })
            } else if (action == 'confirm') {
                await apiOrderConfirm({ id })
            } else if (action == 'confirmpay') {
                await apiOrderConfirmpay({
                    id
                })
            } else if (action == 'verification') {
                const res = await apiVerificationOrderConfirm({ id, confirm: isConfirm })
                if (res.code === 10) {
                    this.verifyTips = res.msg
                    this.verificationDialog = true
                    return
                }
            }

            this.initOrderDetail()
        }
    },

    async onLoad() {},

    async onShow() {
        const options = this.$Route.query
        this.order_id = options.id
        await this.initOrderDetail()
    }
}
</script>

<style lang="scss" scoped>
.order-detail {
    padding-bottom: 150rpx;
    background: linear-gradient(to bottom, $-color-primary 260rpx, transparent 0);

    .header {
        padding: 24rpx 40rpx;
    }

    .contain {
        border-radius: 10rpx;
    }

    .address-wrap {
        height: 164rpx;
        padding: 0 24rpx;

        .copy {
            margin-left: 20rpx;
            // text-decoration: line-through;
        }
    }

    .order-header {
        padding: 20rpx;

        .order-status {
            color: $-color-primary;
        }
    }

    .order-goods {
        padding: 20rpx;
        border-top: $-solid-border;
        border-bottom: $-solid-border;
    }

    .goods {
        width: 100%;

        .order-name {
            flex: 1;
            min-width: 0;
            color: $-color-black;
            font-size: $-font-size-nr;
        }

        .order-str {
            color: $-color-muted;
            font-size: $-font-size-sm;
        }

        > view {
            width: 100%;
        }

        .image {
            flex: 0;
        }
    }

    .order-price {
        padding-top: 20rpx;

        .price-item {
            padding: 0 20rpx 24rpx;
        }
    }

    .footer {
        position: fixed;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 120rpx;
        padding: 18rpx 0;

        .btn {
            width: 168rpx;
            height: 64rpx;
            margin-right: 20rpx;
        }

        .hollow {
            color: $-color-lighter;
            border: 1px solid #dbdbdb;
        }

        .solid {
            color: $-color-white;
            background-color: $-color-primary;
        }
    }
}

.copy {
    font-size: $-font-size-xs;
    margin-left: 20rpx;
    padding: 4rpx 10rpx;
    border-radius: 4rpx;
    color: $-color-primary;
    background-color: rgba(64, 175, 250, 0.1);
}

.copy-btn {
    font-size: 24rpx;
    padding: 10rpx 30rpx;
    border-radius: 8rpx;
    color: $-color-primary;
    background: rgba($color: $-color-primary, $alpha: 0.1);
}
</style>
