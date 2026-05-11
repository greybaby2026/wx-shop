<template>
    <div class="order-detail bg-white">
        <div class="detail-hd flex row-between">
            <div class="lg">订单详情</div>
        </div>

        <div class="address">
            <div class="address-item">
                <div class="lighter address-item-label">收件人：</div>
                <div>{{ orderDetail.address.contact }}</div>
            </div>
            <div class="address-item">
                <div class="lighter address-item-label">联系方式：</div>
                <div>{{ orderDetail.address.mobile }}</div>
            </div>

            <template v-if="orderDetail.delivery_type === 1">
                <div class="address-item">
                    <div class="lighter address-item-label">收货地址：</div>
                    <div>{{ orderDetail.delivery_address }}</div>
                </div>
            </template>

            <template v-if="orderDetail.delivery_type === 2">
                <div class="address-item">
                    <div class="lighter address-item-label">门店地址：</div>
                    <div>
                        <div class="bold black">
                            {{ orderDetail.selffetch_shop.name }}
                        </div>
                        <div class="lighter m-t-8">
                            {{ orderDetail.selffetch_shop.detailed_address }}
                        </div>
                        <template
                            v-if="
                                orderDetail.pickup_code &&
                                orderDetail.show_pickup_code
                            "
                        >
                            <div
                                class="flex"
                                v-if="orderDetail.order_status == 1"
                            >
                                <div class="flex col-center m-t-10 flex-col">
                                    <div
                                        class="qr-container"
                                        ref="qrCodeUrl"
                                    ></div>
                                    <div class="selffetc-code m-t-10">
                                        <span>提货码:</span>
                                        <span>{{
                                            orderDetail.pickup_code
                                        }}</span>
                                        <span
                                            class="primary m-l-8 pointer"
                                            @click="onCopyQRCode"
                                        >
                                            复制
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <div class="detail-con">
            <div class="flex order-info">
                <div>
                    订单状态：
                    <span
                        :class="[
                            'status',
                            { primary: orderDetail.order_status == 0 },
                        ]"
                        v-if="orderDetail.pay_way != 5"
                    >
                        {{ orderDetail.order_status_desc }}
                    </span>
                    <span
                        :class="[
                            'status',
                            { primary: orderDetail.order_status == 0 },
                        ]"
                        v-else
                    >
                        线下付款
                    </span>
                </div>

                <div>订单编号：{{ orderDetail.sn }}</div>
                <div>订单类型：{{ orderDetail.order_type_desc }}</div>

                <div>下单时间：{{ orderDetail.create_time }}</div>
                <div>付款时间：{{ orderDetail.pay_time }}</div>

                <div>支付方式：{{ getPayWay(orderDetail.pay_way) }}</div>
                <div>买家留言：{{ orderDetail.user_remark || "-" }}</div>
            </div>
            <div class="goods">
                <div class="goods-hd lighter flex title">
                    <div class="info flex flex-1">商品信息</div>
                    <div class="num flex">数量</div>
                    <div class="total flex">商品总价</div>
                    <div class="total flex">实付金额</div>
                    <div class="total flex">操作</div>
                </div>
                <div class="goods-list">
                    <div
                        class="goods-item flex"
                        v-for="(item, index) in orderDetail.order_goods"
                        :key="index"
                    >
                        <nuxt-link
                            :to="`/goods_details?id=${item.goods_id}`"
                            class="info flex flex-1"
                        >
                            <el-image
                                class="goods-img"
                                :src="item.goods_image"
                                alt=""
                            />
                            <div class="goods-info flex-1">
                                <div class="goods-name line-2">
                                    <el-tag
                                        size="mini"
                                        effect="plain"
                                        v-if="item.is_seckill"
                                        >秒杀</el-tag
                                    >
                                    {{ item.goods_name }}
                                </div>
                                <div class="sm lighter m-t-8 m-b-8">
                                    {{ item.spec_value_str }}
                                </div>
                                <price-formate
                                    v-if="
                                        orderDetail.order_type == 0 ||
                                        orderDetail.order_type == 4
                                    "
                                    :price="item.original_price"
                                />
                                <price-formate
                                    v-else
                                    :price="item.goods_price"
                                />
                            </div>
                        </nuxt-link>
                        <div class="num flex">
                            {{ item.goods_num }}
                        </div>
                        <div class="total flex">
                            <price-formate
                                v-if="
                                    orderDetail.order_type == 0 ||
                                    orderDetail.order_type == 4
                                "
                                :price="item.total_original_price"
                            />
                            <price-formate v-else :price="item.total_price" />
                        </div>
                        <div>
                            <div class="total flex">
                                <price-formate :price="item.total_pay_price" />
                            </div>
                            <div class="muted xs m-t-5">
                                (含运费：{{ item.express_price }})
                            </div>
                            <div class="muted xs m-t-5">
                                (含优惠：{{ getDiscountPrice(item) }})
                                <el-popover placement="top" trigger="hover">
                                    <div>
                                        优惠券：-¥{{ item.coupon_discount }}
                                    </div>
                                    <div>
                                        会员折扣：-¥{{ item.member_discount }}
                                    </div>
                                    <i
                                        slot="reference"
                                        class="el-icon-question pointer"
                                    ></i>
                                    <div>
                                        积分抵扣：-¥{{ item.integral_discount }}
                                    </div>
                                </el-popover>
                            </div>
                            <div class="muted xs m-t-5">
                                (含改价：{{ item.change_price }})
                            </div>
                        </div>
                        <div class="total flex">
                            <el-button
                                plain
                                v-if="item.after_sale_btn === 1"
                                @click.stop="goPage(item.id, 'apply')"
                                size="small"
                            >
                                申请售后
                            </el-button>
                            <span v-else-if="!item.after_sale_btn"> - </span>
                            <el-button
                                plain
                                v-else
                                @click.stop="
                                    goPage(item.after_sale_id, 'details')
                                "
                                size="small"
                            >
                                {{ item.after_sale_status_desc }}
                            </el-button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="m-t-16" v-if="orderDetail.user_remark">
                <span class="lighter m-r-8">买家留言：</span>
                <span>{{ orderDetail.user_remark }}</span>
            </div>
            <div class="m-t-16" v-if="orderDetail.delivery_content">
                <span class="lighter m-r-8">发货内容：</span>
                <span>{{ orderDetail.delivery_content }}</span>
            </div>
        </div>
        <div class="detail-footer flex">
            <div>
                <div class="flex-col" style="align-items: flex-end">
                    <div class="money flex m-b-8">
                        <div class="lighter">商品总价：</div>
                        <div>
                            <price-formate
                                v-if="
                                    orderDetail.order_type == 0 ||
                                    orderDetail.order_type == 4
                                "
                                :price="orderDetail.total_original_price"
                            />
                            <price-formate
                                v-else
                                :price="orderDetail.goods_price"
                            />
                        </div>
                    </div>
                    <div class="money flex m-b-8">
                        <div class="lighter">运费金额：</div>
                        <div>
                            <price-formate :price="orderDetail.express_price" />
                        </div>
                    </div>
                    <!-- <div
                        class="money flex m-b-8"
                        v-if="orderDetail.member_amount > 0"
                    >
                        <div class="lighter">会员折扣：</div>
                        <div>
                            -
                            <price-formate :price="orderDetail.member_amount" />
                        </div>
                    </div> -->
                    <div
                        class="money flex m-b-16"
                        v-if="orderDetail.total_discount != 0"
                    >
                        <div class="lighter">优惠金额：</div>
                        <div>
                            -
                            <price-formate
                                :price="orderDetail.total_discount"
                            />
                        </div>
                    </div>
                    <div
                        class="money flex m-b-16"
                        v-if="orderDetail.change_price"
                    >
                        <div class="lighter">商品改价：</div>
                        <div>
                            -
                            <price-formate :price="orderDetail.change_price" />
                        </div>
                    </div>
                    <div class="money flex">
                        <div class="lighter">实付金额：</div>
                        <div class="primary">
                            <price-formate
                                :price="orderDetail.order_amount"
                                :subscript-size="14"
                                :first-size="28"
                                :second-size="28"
                            />
                        </div>
                    </div>
                </div>
                <div class="oprate-btn flex row-end m-t-16">
                    <div
                        class="btn plain flex row-center lighter"
                        v-if="orderDetail.btn.cancel_btn"
                        @click="handleOrder(0)"
                    >
                        取消订单
                    </div>
                    <div
                        class="btn plain flex row-center m-l-8 lighter"
                        v-if="orderDetail.btn.delivery_btn"
                        @click="showDeliverPop = true"
                    >
                        物流查询
                    </div>
                    <div
                        class="btn bg-primary flex row-center white m-l-8"
                        v-if="orderDetail.btn.confirm_btn"
                        @click="handleOrder(2)"
                    >
                        确认收货
                    </div>
                    <div
                        class="btn plain flex row-center lighter m-l-8"
                        v-if="orderDetail.btn.delete_btn"
                        @click="handleOrder(1)"
                    >
                        删除订单
                    </div>
                    <nuxt-link
                        :to="`/payment?id=${orderDetail.id}`"
                        class="btn bg-primary flex row-center white m-l-8"
                        v-if="orderDetail.btn.pay_btn"
                    >
                        <span class="m-r-8">去付款</span>
                        <count-down
                            v-if="
                                getCancelTime(orderDetail.order_cancel_time) > 0
                            "
                            :time="getCancelTime(orderDetail.order_cancel_time)"
                            format="hh:mm:ss"
                            @finish="getOrderDetail"
                        />
                    </nuxt-link>
                </div>
            </div>
        </div>
        <deliver-search v-model="showDeliverPop" :aid="id" />
    </div>
</template>

<script>
import CountDown from "~/components/countDown";
import { copyClipboard } from "@/utils/tools";
import QRCode from "qrcodejs2";

import headerMixins from "@/mixins/header";
export default {
    mixins: [headerMixins],
    layout: "user-layout",
    components: {
        CountDown,
    },
    async asyncData({ $get, query }) {
        const { data, code } = await $get("order/detail", {
            params: {
                id: query.id,
            },
        });

        if (code == 1) {
            return {
                orderDetail: data,
                id: query.id,
            };
        }
    },
    data() {
        return {
            orderDetail: {},
            showDeliverPop: false,
        };
    },

    mounted() {
        if (this.orderDetail.delivery_type === 2) {
            this.creatQrCode(this.orderDetail.pickup_code);
        }
    },

    methods: {
        creatQrCode(content) {
            if (!this.$refs.qrCodeUrl) return;
            const qrcode = new QRCode(this.$refs.qrCodeUrl, {
                text: content,
                width: 106,
                height: 106,
                colorDark: "#333333",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H,
            });
        },
        onCopyQRCode() {
            const content = this.orderDetail.pickup_code;
            copyClipboard(content)
                .then(() => {
                    this.$message.success("复制成功");
                })
                .catch((err) => {
                    this.$message.error("复制失败");
                    console.log(err);
                });
        },

        async getOrderDetail() {
            const { data, code } = await this.$get("order/detail", {
                params: {
                    id: this.id,
                },
            });
            if (code == 1) {
                this.orderDetail = data;
            }
        },
        handleOrder(type) {
            this.type = type;
            this.$confirm(this.getTipsText(type), {
                title: "温馨提示",
                center: true,
                confirmButtonText: "确定",
                cancelButtonText: "取消",
                callback: (action) => {
                    if (action == "confirm") {
                        this.postOrder();
                    }
                },
            });
        },
        async postOrder() {
            const { type, id } = this;
            let url = "";
            switch (type) {
                case 0:
                    url = "order/cancel";
                    break;
                case 1:
                    url = "order/del";
                    break;
                case 2:
                    url = "order/confirm";
                    break;
            }
            let { code, data, msg } = await this.$post(url, { id });
            if (code == 1) {
                this.$message({
                    message: msg,
                    type: "success",
                });
                if (type == 1) {
                    setTimeout(() => {
                        this.$router.go(-1);
                    }, 1500);
                } else {
                    this.getOrderDetail();
                }
            }
        },
        getTipsText(type) {
            switch (type) {
                case 0:
                    return "确认取消订单吗？";
                case 1:
                    return "确认删除订单吗?";
                case 2:
                    return "确认收货吗?";
            }
        },
        goPage(id, type) {
            switch (type) {
                case "apply":
                    this.$router.push(
                        "/user/after_sales/apply_sale?order_id=" + id
                    );
                    break;
                case "details":
                    this.$router.push(
                        "/user/after_sales/after_sale_details?afterSaleId=" + id
                    );
                    break;
            }
        },
        getDiscountPrice(item) {
            const { integral_discount, member_discount, coupon_discount } =
                item;
            return (
                integral_discount +
                member_discount +
                +coupon_discount
            ).toFixed(2);
        },
    },
    computed: {
        getOrderStatus() {
            return (status) => {
                let text = "";
                switch (status) {
                    case 0:
                        text = "待支付";
                        break;
                    case 1:
                        text = "待发货";
                        break;
                    case 2:
                        text = "待收货";
                        break;
                    case 3:
                        text = "已完成";
                        break;
                    case 4:
                        text = "订单已关闭";
                        break;
                }
                return text;
            };
        },
        getCancelTime() {
            return (time) => time - Date.now() / 1000;
        },
        getPayWay() {
            return (payway) => {
                let payWay = "";
                switch (payway) {
                    case 1:
                        payWay = "余额支付";
                        break;
                    case 2:
                        payWay = "微信支付";
                        break;
                    case 3:
                        payWay = "支付宝支付";
                        break;
                }
                return payWay;
            };
        },
    },
};
</script>

<style lang="scss" scoped>
.order-detail {
    padding: 10px 10px 20px;
    .detail-hd {
        padding: 14px 5px;
        border-bottom: 1px solid #e5e5e5;
    }
    .address {
        padding: 16px 15px;
        border-bottom: 1px solid #e5e5e5;
        > div {
            margin-bottom: 10px;
        }

        &-item {
            display: flex;

            &-label {
                width: 70px;
                text-align: justify;
                text-align-last: justify;
            }
        }
    }
    .detail-con {
        .order-info {
            padding: 15px 10px;
            line-height: 30px;
            flex-wrap: wrap;
            & > div {
                width: 33.3%;
            }
        }
        .title {
            height: 40px;
            background: #f2f2f2;
            border: 1px solid #e5e5e5;
            padding: 0 20px;
        }
        .goods {
            .goods-hd,
            .goods-list {
                padding: 10px 20px;
                border: 1px solid #e5e5e5;
                border-top-width: 0;
                .goods-item {
                    padding: 10px 0;
                    .goods-name {
                        line-height: 1.5;
                    }
                }
            }
            .info {
                .goods-img {
                    width: 72px;
                    height: 72px;
                    margin-right: 10px;
                }
            }
            .price,
            .num,
            .total,
            .real_total {
                width: 150px;
            }
        }
    }
    .detail-footer {
        padding: 25px 20px;
        justify-content: flex-end;
        .money {
            > div {
                text-align: right;
                &:first-of-type {
                    width: 80px;
                }
                &:last-of-type {
                    width: 120px;
                    display: flex;
                    justify-content: flex-end;
                }
            }
        }
        .oprate-btn {
            .btn {
                width: 152px;
                height: 44px;
                cursor: pointer;
                border-radius: 2px;
                &.plain {
                    border: 1px solid #e5e5e5;
                }
            }
        }
    }

    .selffetc-code {
        display: inline-block;
        padding: 4px 15px;
        border-radius: 60px;
        font-size: 12px;
        background-color: #f6f6f6;
        // color: $-color-black;
    }

    .qr-container {
        width: 120px;
        height: 120px;
        padding: 6px;
        border: 1px solid #e5e5e5;
        border-radius: 6px;
    }
}
</style>
