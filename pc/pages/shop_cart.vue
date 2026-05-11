<template>
    <div class="shop-cart">
        <div class="cart-list bg-white">
            <div v-show="!isDataNull">
                <div class="cart-hd flex lighter">
                    <div class="check-box">
                        <el-checkbox
                            :indeterminate="isIndeterminate"
                            v-model="isSelectedAll"
                            @change="handleCheckAllChange"
                            >全选</el-checkbox
                        >
                    </div>
                    <div class="info flex row-center">商品信息</div>
                    <div class="price flex row-center">单价</div>
                    <div class="num flex row-center">数量</div>
                    <div class="money flex row-center">合计</div>
                    <div class="operate flex row-center">操作</div>
                </div>
                <div class="cart-con">
                    <div
                        class="item flex"
                        v-for="(item, index) in shopCartList"
                        :key="index"
                    >
                        <div class="check-box">
                            <el-checkbox
                                v-model="item.selected"
                                @change="onBoxClick($event, item.id)"
                                :disabled="item.cart_status"
                            >
                            </el-checkbox>
                        </div>
                        <nuxt-link
                            class="info flex"
                            :to="'/goods_details?id=' + item.goods_id"
                        >
                            <div class="pictrue flex-none">
                                <img
                                    class="img"
                                    :src="item.goods.image"
                                    alt=""
                                />
                                <div :class="{ cover: item.cart_status }"></div>
                            </div>
                            <div>
                                <div class="name line-2">
                                    {{ item.goods.name }}
                                </div>
                                <div class="muted">
                                    {{ item.goods_item.spec_value_str }}
                                </div>
                            </div>
                        </nuxt-link>
                        <div class="price flex row-center">
                            ¥{{ item.goods_item.sell_price }}
                        </div>
                        <div class="text-center">
                            <div
                                class="num flex row-center"
                                v-if="item.goods.limit_type === 1"
                            >
                                <number-box
                                    :min="1"
                                    v-model="item.goods_num"
                                    @change="
                                        changeShopCartCount($event, item.id)
                                    "
                                    @blur="blurShopCartCount($event, item.id)"
                                    async-change
                                />
                            </div>
                            <div class="num flex row-center" v-else>
                                <number-box
                                    :min="1"
                                    :max="item.goods.limit_value"
                                    v-model="item.goods_num"
                                    @change="
                                        changeShopCartCount($event, item.id)
                                    "
                                    @blur="blurShopCartCount($event, item.id)"
                                    async-change
                                />
                            </div>
                            <div
                                v-if="item.goods.limit_type === 2"
                                class="p-t-10 muted"
                            >
                                每人限购{{ item.goods.limit_value }}件
                            </div>
                            <div
                                v-if="item.goods.limit_type === 3"
                                class="p-t-10 muted"
                            >
                                每笔限购{{ item.goods.limit_value }}件
                            </div>
                        </div>
                        <div class="money flex row-center">
                            ¥{{ item.sub_price }}
                        </div>
                        <el-popconfirm
                            title="确定删除该商品吗？"
                            confirm-button-text="确定"
                            cancel-button-text="取消"
                            icon="el-icon-info"
                            icon-color="red"
                            @confirm="goodsDelete(item.id)"
                        >
                            <div
                                class="operate flex row-center delete-btn"
                                slot="reference"
                            >
                                <img src="~/assets/images/icon_cart_del.png" />
                            </div>
                        </el-popconfirm>
                    </div>
                </div>
                <div class="cart-footer flex row-between">
                    <div class="lighter flex">
                        <div class="check-box">
                            <el-checkbox
                                :indeterminate="isIndeterminate"
                                v-model="isSelectedAll"
                                @change="handleCheckAllChange"
                                >全选</el-checkbox
                            >
                        </div>
                        <div style="margin: 0 24px">
                            已选 ({{ checkoutCount }})
                        </div>
                        <el-popconfirm
                            title="确定删除选中商品吗？"
                            confirm-button-text="确定"
                            cancel-button-text="取消"
                            icon="el-icon-info"
                            icon-color="red"
                            @confirm="deleteSelectedGoods"
                        >
                            <div style="cursor: pointer" slot="reference">
                                删除选中商品
                            </div>
                        </el-popconfirm>
                    </div>
                    <div class="total flex-col">
                        <div class="flex">
                            <div class="lighter">商品总价：</div>
                            <div
                                class="primary"
                                style="font-size: 20px; margin-left: 28px"
                            >
                                ¥{{ totalAmount }}
                            </div>
                        </div>
                        <div
                            class="white bg-primary lg btn flex row-center"
                            @click="toOrderBuy"
                        >
                            去结算
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col col-center data-null" v-show="isDataNull">
                <img
                    src="@/assets/images/cart_null.png"
                    style="width: 150px; height: 150px"
                />
                <div class="muted xs m-t-8">购物车是空的～</div>
                <div class="m-t-8">
                    <el-button
                        round
                        type="primary"
                        size="medium"
                        @click="toIndex"
                        >去逛逛～</el-button
                    >
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import NumberBox from "~/components/public/numberBox";
import { mapActions } from "vuex";
import headerMixins from "@/mixins/header";
export default {
    mixins: [headerMixins],
    components: {
        NumberBox,
    },
    async asyncData({ $get, $post }) {
        let shopCartList = [];
        let totalAmount = 0;
        let totalNum = 0;
        let isIndeterminate = false;
        let checkoutCount = 0;
        let isDataNull = true;
        let res = await $get("cart/lists");
        if (res.code == 1) {
            shopCartList = res.data.lists;
            totalAmount = res.data.total_amount;
            totalNum = res.data.total_num;
            isIndeterminate = false;
            checkoutCount = 0;
            shopCartList.forEach((item, index) => {
                // 有一个选中就是true
                if (item.selected == 1 || item.selected == true) {
                    checkoutCount++;
                    item.selected = true;
                } else {
                    item.selected = false;
                }
            });
            isIndeterminate =
                checkoutCount > 0 && checkoutCount < shopCartList.length;
            if (shopCartList.length > 0) {
                isDataNull = false;
            } else {
                isDataNull = true;
            }
        }
        return {
            shopCartList,
            totalAmount,
            totalNum,
            isIndeterminate,
            checkoutCount,
            isDataNull,

            cartListsId: [], // 购物车数据的所有ID
        };
    },
    data() {
        return {
            checkAll: false,
            isIndeterminate: false,
            checkoutCount: 0,
        };
    },
    mounted() {},
    computed: {
        isSelectedAll: {
            get() {
                if (this.shopCartList.length <= 0) {
                    return false;
                }
                let index = this.shopCartList.findIndex(
                    (item) => item.selected == 0
                );

                if (index == -1) {
                    return true;
                }

                return false;
            },
        },
    },
    methods: {
        ...mapActions(["getPublicData"]),
        async getCartList() {
            let res = await this.$get("cart/lists");
            if (res.code == 1) {
                this.shopCartList = Object.assign([], res.data.lists);
                this.totalAmount = res.data.total_amount;
                this.totalNum = res.data.total_num;
                this.isIndeterminate = true;
                this.checkoutCount = 0;
                this.shopCartList.forEach((item, index) => {
                    // 有一个选中就是true
                    if (item.selected == 1 || item.selected == true) {
                        this.checkoutCount++;
                        item.selected = true;
                    } else {
                        item.selected = false;
                    }
                });
                this.isIndeterminate =
                    this.checkoutCount > 0 &&
                    this.checkoutCount < this.shopCartList.length;
                if (this.shopCartList.length > 0) {
                    this.isDataNull = false;
                } else {
                    this.isDataNull = true;
                }
            }
        },
        async handleCheckAllChange(e) {
            let res = await this.$post("cart/selected", {
                cart_id: this.shopCartList.map((item) => item.id),
                selected: e == true ? 1 : 0,
            });
            if (res.code == 1) {
                this.getCartList();
            }
        },
        onBoxClick(e, cartId) {
            this.changeSelected(cartId, e);
        },
        async changeSelected(id, selected) {
            let res = await this.$post("cart/selected", {
                cart_id: id,
                selected: selected == true ? 1 : 0,
            });
            if (res.code == 1) {
                this.getCartList();
            }
        },
        async changeShopCartCount(number, cartId) {
            // cart/change
            let res = await this.$post("cart/change", {
                cart_id: cartId,
                goods_num: number,
            });
            if (res.code == 1) {
                this.getCartList();
                this.getPublicData();
            }
        },
        // 用户input输入框输入数量
        async blurShopCartCount(event, cartId) {
            // cart/change
            let res = await this.$post("cart/change", {
                cart_id: cartId,
                goods_num: event.value,
            });
            if (res.code == 1) {
                this.getCartList();
                this.getPublicData();
            }
        },
        async goodsDelete(cartId) {
            let res = await this.$post("cart/del", {
                cart_id: cartId,
            });
            if (res.code == 1) {
                this.getPublicData();
                this.getCartList();
                this.$message({
                    message: "删除商品成功",
                    type: "success",
                });
            }
        },
        deleteSelectedGoods() {
            let selectedGoodsArr = this.shopCartList.filter((item) => {
                return item.selected == 1 || item.selected == true;
            });
            if (selectedGoodsArr.length <= 0) {
                this.$message({
                    message: "没有选择商品",
                    type: "error",
                });
                return;
            }
            let cartIdArr = selectedGoodsArr.map((item) => item.id);
            this.goodsDelete(cartIdArr);
        },
        toOrderBuy() {
            let { shopCartList } = this;
            let goods = [];
            shopCartList.forEach((item) => {
                if (item.selected) {
                    goods.push({
                        item_id: item.item_id,
                        num: item.goods_num,
                    });

                    this.cartListsId.push(item.id);
                }
            });
            if (goods.length == 0)
                return this.$message({
                    message: "您还没有选择商品哦",
                });

            this.$router.push({
                path: "/confirm_order",
                query: {
                    data: encodeURIComponent(
                        JSON.stringify({
                            goods, // 购物车购买可以不传
                        })
                    ),
                    source: "cart", // 立即购买-buy_now;购物车购买-cart
                    order_type: 0, // 订单类型 0-普通订单 1-拼团订单 2-秒杀订单 3-砍价订单
                    cart_id: this.cartListsId, // 购物车商品id数组         购物车购买必传
                },
            });
        },
        toIndex() {
            this.$router.push("/");
        },
    },
};
</script>

<style lang="scss">
.shop-cart {
    padding: 24px 0;

    .cart-list {
        min-height: 600px;

        .cart-hd {
            height: 50px;
            margin: 0 10px;
            border-bottom: 1px solid #e5e5e5;
        }

        .cart-con {
            margin: 0 10px;

            .item {
                padding: 20px 0;
                border-bottom: 1px dashed #e5e5e5;
            }
        }

        .check-box {
            padding-left: 10px;
            width: 68px;
        }

        .info {
            width: 450px;

            .pictrue {
                margin-right: 10px;
                position: relative;

                .img {
                    width: 72px;
                    height: 72px;
                }

                .cover {
                    width: 72px;
                    height: 72px;
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: rgba(0, 0, 0, 0.5);
                }
                .cover::after {
                    content: "-已失效-";
                    font-size: 12px;
                    color: #fff;
                    position: absolute;
                    top: 50%;
                    left: 15%;
                    transform: translateY(-50%);
                }
            }

            .name {
                margin-bottom: 10px;
            }
        }

        .price {
            width: 150px;
        }

        .num {
            width: 250px;
        }

        .money {
            width: 150px;
        }

        .delete-btn {
            cursor: pointer;
        }
    }

    .cart-footer {
        padding: 20px 20px 40px 10px;
        align-items: flex-start;

        .total {
            align-items: flex-end;

            .btn {
                width: 152px;
                height: 44px;
                margin-top: 18px;
                cursor: pointer;
            }
        }
    }

    .data-null {
        padding-top: 100px;
    }
}
</style>
