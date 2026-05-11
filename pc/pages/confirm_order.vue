<template>
    <div class="confirm-order">
        <!-- 配送方式 -->
        <div class="order-hd bg-white m-b-16">
            <div class="title">配送方式</div>
            <div class="address flex">
                <!-- 非虚拟订单 -->
                <el-radio-group v-model="delivery" @change="changeDelivery">
                    <el-radio
                        v-for="item in deliveries"
                        :key="item.id"
                        :label="item.id"
                    >
                        {{ item.name }}
                    </el-radio>
                </el-radio-group>
            </div>
            <!-- 快递配送 -->
            <div v-show="delivery === 1">
                <div class="title">收货地址</div>
                <div class="address flex row-between">
                    <div class="address-con" v-show="address.contact">
                        <div>
                            <span class="bold">{{ address.contact }}</span>
                            {{ address.mobile }}
                            <el-tag
                                size="mini"
                                type="warning"
                                effect="dark"
                                v-if="address.is_default"
                            >
                                默认
                            </el-tag>
                        </div>
                        <div class="lighter m-t-8">
                            {{ address.province }} {{ address.city }}
                            {{ address.district }}
                            {{ address.address }}
                        </div>
                        <div class="oprate primary flex">
                            <div
                                class="m-r-16"
                                @click="editAddress(address.id)"
                            >
                                修改
                            </div>
                            <div @click="showAddress = true">更换地址</div>
                        </div>
                    </div>
                    <div
                        class="address-add flex row-center"
                        @click="editAddress('')"
                    >
                        + 添加地址
                    </div>
                </div>
            </div>

            <!-- 门店自提 -->
            <div v-show="delivery === 2">
                <div class="title">自提门店</div>
                <div class="address flex row-between">
                    <div class="address-con" v-if="selffetchShopID">
                        <div>
                            <span class="bold">{{
                                checkedSelffetchShop.name
                            }}</span>

                            <span class="muted m-l-10 flex-none">
                                <i class="el-icon-position"></i>
                                {{ checkedSelffetchShop.distance }}
                            </span>
                        </div>
                        <div class="lighter m-t-8">
                            {{ checkedSelffetchShop.detailed_address }}
                        </div>

                        <div class="muted m-t-8">
                            <i class="el-icon-time"></i>
                            <span>{{
                                checkedSelffetchShop.business_start_time +
                                "-" +
                                checkedSelffetchShop.business_end_time
                            }}</span>
                        </div>

                        <div class="oprate primary row">
                            <div @click="showSelffetch = true">更换门店</div>
                        </div>
                    </div>
                    <div
                        v-else
                        class="address-add flex row-center"
                        @click="selectShop"
                    >
                        + 请选择门店
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white mb16" v-show="delivery === 2">
            <div class="title">提货人信息</div>
            <div class="contact">
                <div class="contact-item">
                    <div class="contact-item-label">提货人</div>
                    <div class="contact-item-content">
                        <div v-show="!contactNameEdit">
                            <span class="m-r-10 black">{{
                                contactNameValue
                            }}</span>
                            <span
                                class="primary pointer"
                                @click="contactNameEdit = true"
                            >
                                {{ contactNameValue ? "修改" : "添加" }}
                            </span>
                        </div>
                        <div v-show="contactNameEdit" class="flex">
                            <el-input
                                class="m-r-10"
                                v-model="contactName"
                                placeholder="请输入内容"
                                size="small"
                            />
                            <el-button
                                type="primary"
                                size="small"
                                @click="changeContactName"
                            >
                                确认
                            </el-button>
                            <el-button
                                size="small"
                                @click="closeContactNameEdit"
                            >
                                取消
                            </el-button>
                        </div>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-label">联系方式</div>
                    <div class="contact-item-content">
                        <div v-show="!contactPhoneEdit">
                            <span class="mr10 black">{{
                                contactPhoneValue
                            }}</span>
                            <span
                                class="primary pointer"
                                @click="contactPhoneEdit = true"
                            >
                                {{ contactPhoneValue ? "修改" : "添加" }}
                            </span>
                        </div>
                        <div v-show="contactPhoneEdit" class="flex">
                            <el-input
                                class="m-r-10"
                                v-model="contactPhone"
                                placeholder="请输入内容"
                                size="small"
                            />
                            <el-button
                                type="primary"
                                size="small"
                                @click="changeContactPhone"
                            >
                                确认
                            </el-button>
                            <el-button
                                size="small"
                                @click="closeContactPhoneEdit"
                            >
                                取消
                            </el-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="order-con bg-white">
            <div class="goods m-b-16">
                <div class="title">商品信息</div>
                <div class="goods-hd flex lighter">
                    <div class="info flex">商品信息</div>

                    <div class="price flex" v-if="orderInfo.order_type == 2">
                        日常价
                    </div>
                    <div class="price flex">
                        {{ orderInfo.order_type == 2 ? "秒杀价" : "单价" }}
                    </div>
                    <div class="num flex">数量</div>
                    <div class="money flex">合计</div>
                </div>
                <div class="goods-list">
                    <div
                        :class="[
                            'flex',
                            'item',
                            {
                                'item-disabled':
                                    orderInfo.order_type != 4 &&
                                    (delivery === 1
                                        ? !item.is_express
                                        : !item.is_selffetch),
                            },
                        ]"
                        v-for="(item, index) in orderInfo.goods"
                        :key="index"
                    >
                        <div class="info flex">
                            <div class="pictrue flex-none">
                                <el-image :src="item.image"></el-image>
                            </div>
                            <div>
                                <div class="name line-2">
                                    {{ item.goods_name }}
                                </div>
                                <div class="muted">
                                    <span>{{ item.spec_value_str }}</span>
                                    <template v-if="orderInfo.order_type != 4">
                                        <span
                                            v-show="
                                                delivery === 2 &&
                                                !item.is_selffetch
                                            "
                                            class="delivery-support"
                                        >
                                            该商品不支持门店自提
                                        </span>
                                        <span
                                            v-show="
                                                delivery === 1 &&
                                                !item.is_express
                                            "
                                            class="delivery-support"
                                        >
                                            该商品不支持快递配送
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div
                            class="price flex"
                            v-if="orderInfo.order_type == 2"
                        >
                            <price-formate :price="item.original_price" />
                        </div>
                        <div class="price flex">
                            <price-formate
                                v-if="orderInfo.order_type == 0 || orderInfo.order_type == 4"
                                :price="item.original_price"
                            />
                            <price-formate v-else :price="item.sell_price" />
                            <div
                                class="member-price m-l-10 flex xs"
                                v-if="
                                    !!item.member_price &&
                                    (orderInfo.order_type == 0 ||
                                        orderInfo.order_type == 4)
                                "
                            >
                                <div class="member-tag">会员价</div>
                                <price-formate
                                    :price="
                                        item.member_price || item.sell_price
                                    "
                                    :weight="400"
                                />
                            </div>
                        </div>
                        <div class="num">
                            {{ item.goods_num }}
                        </div>
                        <div class="money">
                            <price-formate
                                v-if="orderInfo.order_type == 0 || orderInfo.order_type == 4"
                                :price="item.total_original_price"
                            />
                            <price-formate v-else :price="item.sub_price" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="input" style="padding-bottom: 16px">
                <div class="title">买家留言</div>
                <div class="textarea">
                    <el-input
                        type="textarea"
                        placeholder="选填，给商家备注留言，100字以内"
                        v-model="userRemark"
                        maxlength="100"
                        :rows="3"
                        show-word-limit
                        resize="none"
                    ></el-input>
                </div>
            </div>
            <div
                class="coupons p-l-10 p-r-10"
                v-if="orderInfo.order_type == 0 || orderInfo.order_type == 4"
            >
                <el-tabs v-model="active">
                    <el-tab-pane
                        :label="`可用优惠券(${coupon.can_use.length})`"
                    >
                        <div class="coupons-con" v-if="coupon.can_use.length">
                            <coupons-list
                                :list="coupon.can_use"
                                :show-more="true"
                                :type="4"
                                @use="useCoupon"
                            />
                        </div>
                        <div v-else class="m-l-20" style="padding-bottom: 40px">
                            暂无可用优惠券
                        </div>
                    </el-tab-pane>
                    <el-tab-pane
                        :label="`不可用优惠券(${coupon.not_can_use.length})`"
                    >
                        <div
                            class="coupons-con"
                            v-if="coupon.not_can_use.length"
                        >
                            <coupons-list
                                :list="coupon.not_can_use"
                                :show-more="true"
                                :type="2"
                            />
                        </div>
                        <div v-else class="m-l-20" style="padding-bottom: 40px">
                            暂无不可用优惠券
                        </div>
                    </el-tab-pane>
                </el-tabs>
            </div>
            <div class="integral" v-if="orderInfo.integral_switch">
                <div class="title">
                    积分抵扣
                    <i class="el-icon-question" @click="showTipsDialog"></i>
                </div>
                <div class="flex check-box">
                    <el-checkbox
                        v-model="useIntegral"
                        @change="changeIntegral"
                        :disabled="
                            orderInfo.user_integral < orderInfo.integral_limit
                        "
                        >积分抵扣</el-checkbox
                    >
                    <div class="muted m-l-8">
                        共{{ orderInfo.user_integral }}积分{{
                            orderInfo.user_integral < orderInfo.integral_limit
                                ? "，满" + orderInfo.integral_limit + "可用"
                                : ""
                        }}
                    </div>
                </div>
            </div>
        </div>
        <div class="order-footer flex bg-white">
            <div>
                <div class="money flex m-b-8">
                    <div class="lighter">商品总价：</div>
                    <div>
                        ¥{{
                            orderInfo.order_type == 0 || orderInfo.order_type == 4
                                ? orderInfo.total_goods_original_price
                                : orderInfo.total_goods_price
                        }}
                    </div>
                </div>
                <div class="money flex m-b-8">
                    <div class="lighter">运费：</div>
                    <div>¥{{ orderInfo.express_price }}</div>
                </div>
                <div class="money flex m-b-8" v-if="orderInfo.discount_amount">
                    <div class="lighter">优惠券：</div>
                    <div>-¥{{ orderInfo.discount_amount }}</div>
                </div>
                <div class="money flex m-b-8" v-if="orderInfo.member_amount">
                    <div class="lighter">会员折扣：</div>
                    <div>-¥{{ orderInfo.member_amount }}</div>
                </div>
                <div class="money flex m-b-8" v-if="orderInfo.integral_amount">
                    <div class="lighter">积分抵扣：</div>
                    <div>-¥{{ orderInfo.integral_amount }}</div>
                </div>
                <div class="money flex m-t-16">
                    <div class="lighter">实付金额：</div>
                    <div class="primary" style="font-size: 20px">
                        ¥{{ orderInfo.order_amount }}
                    </div>
                </div>
                <div class="flex" style="justify-content: flex-end">
                    <el-button
                        type="primary"
                        class="btn lg"
                        @click="submitOrder"
                        :loading="loading"
                        >提交订单</el-button
                    >
                </div>
            </div>
        </div>
        <address-add
            v-model="showAddressAdd"
            :aid="editId"
            @success="orderBuy('settle')"
        />
        <address-list v-model="showAddress" @confirm="changeAddress" />

        <selffetch-shop-list
            v-model="showSelffetch"
            :list="selffetchShop"
            @load="getShopList"
            @confirm="changeSelffetch"
        />
    </div>
</template>

<script>
import { mapActions, mapState } from "vuex";
import { Message } from "element-ui";
import TMap from "@/utils/tcmap";
import SelffetchShopList from "../components/selffetchShopList.vue";

import headerMixins from "@/mixins/header";
export default {
    mixins: [headerMixins],

    components: { SelffetchShopList },

    async asyncData({ query, $post, $get }) {
        let { goods } = JSON.parse(decodeURIComponent(query.data)); // 商品信息
        let source = query.source; // 立即购买-buy_now;购物车购买-cart
        let order_type = query.order_type; // 订单类型 0-普通订单 1-拼团订单 2-秒杀订单 3-砍价订单
        let seckill_id = "";
        let cart_id = "";

        const asyncData = {};

        // 秒杀id
        if (query.seckill_id) {
            seckill_id = query.seckill_id;
        }
        asyncData.seckill_id = seckill_id;

        // 购物车id
        if (query.cart_id) {
            cart_id = query.cart_id;
        }
        asyncData.cart_id = cart_id;

        // 获取发货类型
        await $get("order/getDeliveryType")
            .then(({ code, data, msg }) => {
                if (code != 1) throw new Error(msg);
                return data;
            })
            .then((data) => {
                let deliveries = [];
                const { express, selffetch } = data
                if (express.is_express) {
                    deliveries.push({
                        id: 1,
                        sign: "express",
                        name: express.express_name,
                    });
                }

                // todo 暂时不开启上门自提
                if (selffetch.is_selffetch) {
                    deliveries.push({
                        id: 2,
                        sign: "store",
                        name: selffetch.selffetch_name,
                    });
                }
                if (order_type == 4) {
                    deliveries = [
                        {
                            id: 4,
                            sign: "virtual",
                            name: "虚拟发货",
                        },
                    ];
                }
                asyncData.deliveries = deliveries;
                asyncData.delivery = deliveries[0]["id"];
                return deliveries;
            })
            .then(async (data) => {
                // 订单清算
                await $post("order/placeOrder", {
                    source, // 立即购买-buy_now;购物车购买-cart
                    order_type, // 订单类型 0-普通订单 1-拼团订单 2-秒杀订单 3-砍价订单
                    action: "settle", // settle-订单结算详情
                    goods,
                    delivery_type: data[0]["id"],
                    seckill_id,
                    cart_id,
                })
                    .then(({ code, data, msg }) => {
                        if (code != 1) throw new Error(msg);
                        return data;
                    })
                    .then((data) => {
                        asyncData.orderInfo = data;
                        asyncData.address = data?.address;
                    })
                    .catch((err) => {
                        throw new Error(err);
                    });
            })
            .then(async () => {
                // 优惠券
                await $post("coupon/orderCoupon", {
                    goods,
                    source, // 1-来自下单 2-来自购物车
                })
                    .then(({ code, data, msg }) => {
                        if (code != 1) throw new Error(msg);
                        return data;
                    })
                    .then((data) => {
                        asyncData.coupon = data;
                    })
                    .catch((err) => {
                        throw new Error(err);
                    });
            })
            // .then(async () => {
            //     await $get("selffetch_shop/getBaiduMapKey")
            //         .then(({ code, data, msg }) => {
            //             if (code != 1) throw new Error(msg);
            //             return data.baidu_map_key;
            //         })
            //         .then(async (key) => {
            //             await getCurrentPosition()
            //                 .then(({ lat, lng }) => {
            //                     asyncData.lat = lat;
            //                     asyncData.lng = lng;
            //                 })
            //                 .catch((err) => {
            //                     throw new Error(err);
            //                 });
            //         })
            //         .catch((err) => {
            //             Message.error("定位获取失败");
            //             console.log(err);
            //         });
            // })
            .catch((err) => {
                Message.error("数据请求失败，请稍后重试");
                console.log(err);
            });

        return asyncData;
    },

    data() {
        return {
            orderInfo: {},
            address: {},
            coupon: {
                can_use: [],
                not_can_use: [],
            },
            active: 0,
            userRemark: "",
            showAddress: false,
            showAddressAdd: false,
            addressId: "",
            editId: "",
            isEdit: false,
            useIntegral: 0,

            selffetchShop: [],
            checkedSelffetchShop: {},
            showSelffetch: false,
            selffetchShopID: "",

            contactName: "",
            contactPhone: "",
            contactNameValue: "",
            contactPhoneValue: "",
            contactNameEdit: false,
            contactPhoneEdit: false,

            shopPage: 1,

            seckill_id: "", // 秒杀活动id
            cart_id: "", // 购物车id
            loading: false,
        };
    },
    computed: {
        ...mapState(["publicData"]),
    },
    methods: {
        ...mapActions(["getPublicData"]),
        async selectShop() {
            if (!this.lat) {
                const loading = this.$loading({ text: "正在获取当前位置" });
                try {
                    const location = await this.getCurrentPosition();
                    console.log(location)
                    this.lat = location?.lat;
                    this.lng = location?.lng;
                    loading.close();
                } catch (error) {
                    loading.close();
                    this.$message.error("获取定位失败");
                }
            }

            this.showSelffetch = true;
        },
        getCurrentPosition() {
            const key = this.publicData.shop?.tencent_map_key;
            return new Promise((resolve, reject) => {
                const geolocation = new qq.maps.Geolocation(key, "myapp")
                geolocation.getLocation(resolve, reject, { timeout: 4000 });
            });
        },
        useCoupon(id) {
            this.couponId = id;
            this.orderBuy();
        },
        changeDelivery() {
            this.orderBuy();
            
        },
        changeContactName() {
            this.contactNameEdit = false;
            this.contactNameValue = this.contactName;
        },

        changeContactPhone() {
            this.contactPhoneEdit = false;
            this.contactPhoneValue = this.contactPhone;
        },

        closeContactNameEdit() {
            this.contactNameEdit = false;
            this.contactName = this.contactNameValue;
        },

        closeContactPhoneEdit() {
            this.contactPhoneEdit = false;
            this.contactPhone = this.contactPhoneValue;
        },

        getShopList() {
            if (!this.shopPage) return;
            this.$get("selffetch_shop/lists", {
                params: {
                    longitude: this.lng,
                    latitude: this.lat,
                    page_no: this.shopPage,
                    page_size: 10,
                },
            })
                .then(({ data }) => {
                    data.more ? (this.shopPage += 1) : (this.shopPage = 0);
                    return data;
                })
                .then(({ lists }) => {
                    this.selffetchShop = [...this.selffetchShop, ...lists];
                });
        },

        editAddress(id) {
            this.editId = id;
            this.showAddressAdd = true;
        },

        changeAddress(val) {
            this.addressId = val;
            this.orderBuy();
        },

        changeSelffetch(shop) {
            this.checkedSelffetchShop = shop;
            this.selffetchShopID = shop["id"];
            this.orderBuy();
        },

        changeIntegral() {
            this.orderBuy();
        },

        showTipsDialog() {
            const { integral_config, integral_limit } = this.orderInfo;
            this.$alert(
                `·1积分可抵扣${integral_config}元\n·单次扣减积分不能低于${integral_limit}积分`,
                {
                    confirmButtonText: "确定",
                    title: "积分使用说明",
                }
            );
        },

        submitOrder() {
            this.loading = true;
            this.orderBuy("buy");
        },

        async orderBuy(action = "settle") {
            try {
                let submitObj = {
                    action,
                    delivery_type: this.delivery,
                    goods: this.goods,
                    use_integral: Number(this.useIntegral),
                    address_id: this.addressId,
                    coupon_list_id: this.couponId,
                    order_type: this.order_type,
                    source: this.source,
                    seckill_id: this.seckill_id,
                    cart_id: this.cart_id,
                };

                if (action == "buy") {
                    submitObj.user_remark = this.userRemark;
                    submitObj.type = this.type;

                    if (submitObj.delivery_type === 2) {
                        submitObj.selffetch_shop_id = this.selffetchShopID;
                        submitObj.mobile = this.contactPhoneValue;
                        submitObj.contact = this.contactNameValue;
                        delete submitObj.address_id;
                    }
                }
                const { data, code } = await this.$post(
                    "order/placeOrder",
                    submitObj
                );
                this.loading = false;
                if (code == 1) {
                    if (action == "settle") {
                        let { address } = data;
                        this.orderInfo = data;
                        this.address = address;
                    } else if (action == "buy") {
                        this.getPublicData();
                        this.$router.replace({
                            path: "/payment",
                            query: {
                                id: data.order_id,
                            },
                        });
                    }
                }
            } catch (error) {
                this.loading = false;
            }
        },
    },

    watch: {
        contactNameValue(value) {
            this.contactName = value;
        },

        contactPhoneValue(value) {
            this.contactPhone = value;
        },
    },

    created() {
        const { goods, type } = JSON.parse(
            decodeURIComponent(this.$route.query.data)
        );

        this.goods = goods; // 商品规格
        this.type = type;

        this.source = this.$route.query.source; // 立即购买-buy_now;购物车购买-cart
        this.order_type = this.$route.query.order_type; // 订单类型 0-普通订单 1-拼团订单 2-秒杀订单 3-砍价订单

        this.contactNameValue = this.orderInfo?.address?.contact ?? "";
        this.contactPhoneValue = this.orderInfo?.address?.mobile ?? "";
        // this.initSelffetchShop();
    },
};
</script>

<style lang="scss" scoped>
.confirm-order {
    padding: 16px 0;
    .title {
        padding: 12px 20px;
        font-weight: bold;
        > i {
            cursor: pointer;
        }
    }

    .contact {
        padding: 10px 20px 22px;

        &-item {
            display: flex;
            align-items: center;
            height: 36px;

            &-label {
                width: 72px;
                color: #888888;
            }
        }
    }

    .order-hd {
        .address {
            padding: 10px 20px 22px;
            .address-con {
                position: relative;
                cursor: pointer;
                padding: 16px 20px;
                width: 800px;
                height: 100px;
                padding-right: 150px;
                border: 1px solid $--color-primary;
                border-radius: 2px;
                &:hover {
                    .oprate {
                        display: flex;
                    }
                }
                .oprate {
                    display: none;
                    position: absolute;
                    right: 20px;
                    bottom: 9px;
                }
            }
            .address-add {
                cursor: pointer;
                width: 320px;
                height: 100px;
                border: 1px dashed $--border-color-base;
            }
        }
    }

    .order-con {
        .member-price {
            background-color: #fde5b0;
            color: #63360e;
            border-radius: 4px;
            line-height: 20px;
            overflow: hidden;
            padding-right: 6px;
            .member-tag {
                color: #ffe7b2;
                background-color: #141215;
                padding: 0 6px;
                margin-right: 6px;
            }
        }
        .goods {
            border-bottom: 1px dashed $--border-color-base;
            .goods-hd {
                height: 40px;
                margin: 0 20px;
            }
            .goods-list {
                .item {
                    padding: 10px 20px;
                    &-disabled {
                        position: relative;
                        &::before {
                            z-index: 9;
                            position: absolute;
                            top: 0;
                            left: 0;
                            bottom: 0;
                            right: 0;
                            height: 100%;
                            display: block;
                            content: "";
                            background-color: rgba(
                                $color: #ffffff,
                                $alpha: 0.5
                            );
                        }
                    }
                }
            }
            .info {
                width: 500px;
                .pictrue {
                    margin-right: 10px;
                    .el-image {
                        width: 72px;
                        height: 72px;
                    }
                }
                .name {
                    margin-bottom: 10px;
                }

                .delivery-support {
                    font-size: 12px;
                    padding: 4px 15px;
                    border-radius: 60px;
                    margin-left: 20px;
                    background-color: #f4f4f4;
                    color: #666;
                }
            }
            .price {
                width: 200px;
            }
            .num {
                width: 250px;
            }
            .money {
                width: 200px;
            }
        }
        .input {
            .textarea {
                margin: 0 20px;
                width: 1000px;
            }
        }
        .integral {
            .check-box {
                padding: 0 20px 12px;
            }
        }
    }
    .order-footer {
        margin-top: 2px;
        padding: 25px 20px;
        justify-content: flex-end;
        .money {
            > div {
                text-align: right;
                &:first-of-type {
                    width: 80px;
                    margin-right: 50px;
                }
                &:last-of-type {
                    flex: 1;
                }
            }
        }
        .btn {
            width: 152px;
            height: 44px;
            margin-top: 18px;
            border-radius: 2px;
            cursor: pointer;
        }
    }
}
</style>
