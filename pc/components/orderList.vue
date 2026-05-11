<template>
    <div class="order-list">
        <div class="item m-b-16" v-for="item in list" :key="item.id">
            <div class="item-hd flex">
                <div class="flex-1 lighter sm">
                    下单时间：{{ item.create_time }}
                </div>
                <div class="flex-1 lighter sm">订单编号：{{ item.sn }}</div>
                <div class="flex-1 lighter sm">
                    订单类型：{{ item.order_type_desc }}
                </div>
                <div
                    :class="['status sm', { primary: item.order_status == 0 }]"
                >
                    {{
                        item.pay_way == 5 && item.order_status == 0
                            ? "线下支付"
                            : item.order_status_desc
                    }}
                </div>
            </div>
            <div class="item-con flex flex-1">
                <!-- 商品栏 -->
                <div class="goods">
                    <nuxt-link
                        class="goods-item flex"
                        :to="`/goods_details?id=${goods.goods_id}`"
                        v-for="(goods, index) in item.order_goods"
                        :key="index"
                    >
                        <el-image
                            class="goods-img"
                            :src="goods.goods_image"
                            alt=""
                        />
                        <div class="goods-info flex-1">
                            <div class="goods-name m-b-8 flex row-between">
                                <div class="line1" style="width: 350px">
                                    <el-tag
                                        size="mini"
                                        effect="plain"
                                        v-if="goods.is_seckill"
                                        >秒杀</el-tag
                                    >
                                    {{ goods.goods_name }}
                                </div>
                                <div class="num">x{{ goods.goods_num }}</div>
                            </div>
                            <div class="sm muted m-b-8">
                                {{ goods.spec_value_str }}
                            </div>
                            <div class="primary">
                                <price-formate
                                    v-if="
                                        item.order_type == 0 ||
                                        item.order_type == 4
                                    "
                                    :price="goods.original_price"
                                />
                                <price-formate
                                    v-else
                                    :price="goods.goods_price"
                                />
                            </div>
                        </div>
                    </nuxt-link>
                </div>
                <!-- 总数量和总金额 -->
                <div
                    class="pay-price flex flex-col flex-1 col-center row-center"
                    style="padding-left: 30px"
                >
                    <div class="muted">共{{ item.total_num }}件商品</div>
                    <div class="muted m-t-8 flex">
                        应付金额：
                        <span class="primary">
                            <price-formate
                                :price="item.order_amount"
                                :subscript-size="16"
                                :first-size="16"
                                :second-size="16"
                            />
                        </span>
                    </div>
                </div>
                <!-- 操作栏 -->
                <div
                    class="operate flex-col col-center sm row-center"
                    style="flex: 1"
                >
                    <nuxt-link
                        :to="`/payment?id=${item.id}`"
                        class="btn m-b-16 bg-primary flex row-center white sm"
                        v-if="item.btn.pay_btn"
                    >
                        <span class="m-r-8">去付款</span>
                        <count-down
                            v-if="getCancelTime(item.order_cancel_time) > 0"
                            :time="getCancelTime(item.order_cancel_time)"
                            format="hh:mm:ss"
                            @finish="$emit('refresh')"
                        />
                    </nuxt-link>
                    <div
                        class="btn m-b-10 primary flex row-center sm plain"
                        v-if="item.btn.confirm_btn"
                        @click="handleOrder(2, item.id)"
                    >
                        确认收货
                    </div>
                    <div
                        class="m-b-10 muted flex row-center sm"
                        v-if="item.btn.logistics_btn"
                        @click="showDeliverDialog(item.id)"
                    >
                        物流查询
                    </div>
                    <div
                        class="m-b-10 muted flex row-center sm"
                        v-if="item.btn.cancel_btn"
                        @click="handleOrder(0, item.id)"
                    >
                        取消订单
                    </div>
                    <div
                        class="m-b-10 muted flex row-center sm"
                        v-if="item.btn.delete_btn"
                        @click="handleOrder(1, item.id)"
                    >
                        删除订单
                    </div>
                    <nuxt-link
                        class="lighter"
                        :to="`/user/order/detail?id=${item.id}`"
                    >
                        <span>{{
                            item.order_type == 4 && item.btn.content_btn
                                ? "查看内容"
                                : "查看详情"
                        }}</span>
                        <!-- <i class="el-icon-arrow-right"></i> -->
                    </nuxt-link>
                </div>
            </div>
            <div
                class="lighter sm muted m-t-5"
                style="text-align: right"
                v-if="item.pay_way == 5 && item.order_status == 0"
            >
                如已付款，请通知商家【确认收款】
            </div>
        </div>
        <deliver-search v-model="showDeliver" :aid="aid" />
    </div>
</template>

<script>
import CountDown from "~/components/countDown";
export default {
    components: {
        CountDown,
    },
    props: {
        list: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            showDeliver: false,
            aid: -1,
        };
    },
    created() {
        console.log(this.list);
    },
    methods: {
        handleOrder(type, id) {
            this.type = type;
            this.orderId = id;
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
            const { type, orderId } = this;
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
            let { code, data, msg } = await this.$post(url, { id: orderId });
            // if (code == 1) {
            //   this.$message({
            //     message: msg,
            //     type: "success",
            //   });
            // }
            this.$emit("refresh");
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
        showDeliverDialog(id) {
            console.log("showDeliverDialog");
            this.aid = id;
            this.showDeliver = true;
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
    },
};
</script>

<style lang="scss" scoped>
.order-list {
    padding: 0 10px;
    .item {
        margin-bottom: 20px;
        .item-hd {
            height: 40px;
            border: 1px solid #e5e5e5;
            background: #f2f2f2;
            padding: 0 20px;
            .status {
                width: 100px;
                text-align: right;
            }
        }
        .item-con {
            box-shadow: 0px 3px 4px rgba(0, 0, 0, 0.08);
            align-items: stretch;
            .goods {
                padding: 17px 0;
                width: 560px;
                .goods-item {
                    padding: 10px 20px;
                    .goods-img {
                        flex: none;
                        margin-right: 10px;
                        width: 72px;
                        height: 72px;
                    }
                    .goods-name {
                        width: 100%;
                        .num {
                            padding: 0 42px;
                        }
                    }
                }
            }
            .pay-price {
                width: 200px;
                border-left: 1px solid #e5e5e5;
                border-right: 1px solid #e5e5e5;
            }
            .operate {
                width: 185px;
                & > div {
                    cursor: pointer;
                }
                .btn {
                    width: 120px;
                    height: 32px;
                    border-radius: 2px;
                    &.plain {
                        border: 1px solid $--color-primary;
                    }
                }
            }
        }
    }
}
</style>
