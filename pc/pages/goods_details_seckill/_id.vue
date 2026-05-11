<!-- 秒杀商品 -->
<template>
    <div class="goods-details" v-if="goodsDetails.id">
        <div class>
            <div class="goods-info bg-white flex">
                <!-- 左侧商品图片 -->
                <div class="goods-swiper m-r-16 bg-white flex-col">
                    <el-image
                        class="current-img"
                        :preview-src-list="goodsImage.map((item) => item)"
                        :src="goodsImage[swiperIndex]"
                    ></el-image>
                    <client-only>
                        <swiper
                            class="swiper"
                            ref="mySwiper"
                            :options="swiperOptions"
                        >
                            <swiper-slide
                                :class="{
                                    'swiper-item': true,
                                    active: index === swiperIndex,
                                }"
                                v-for="(item, index) in goodsImage"
                                :key="index"
                            >
                                <div
                                    style="width: 100%; height: 100%"
                                    @mouseover="swiperIndex = index"
                                >
                                    <el-image
                                        style="width: 100%; height: 100%"
                                        :src="item"
                                    ></el-image>
                                </div>
                            </swiper-slide>
                            <div
                                class="swiper-button-prev"
                                slot="button-prev"
                            ></div>
                            <div
                                class="swiper-button-next"
                                slot="button-next"
                            ></div>
                        </swiper>
                    </client-only>
                </div>
                <!-- 右侧商品信息 -->
                <div class="info-wrap bg-white flex-1">
                    <div class="name bold m-b-16">{{ goodsDetails.name }}</div>

                    <div class="seckill flex row-between white">
                        <div class="xxl">限时秒杀</div>
                        <div class="flex">
                            <div class="white m-r-16">距离结束还有</div>
                            <count-down
                                :time="countTime"
                                :is-slot="true"
                                @change="onChangeDate"
                            >
                                <div class="flex row-center count-down xxl">
                                    <div class="item white">
                                        {{ timeData.days }}
                                    </div>
                                    <div class="white" style="margin: 0 4px">
                                        天
                                    </div>
                                    <div class="item white">
                                        {{ timeData.hours }}
                                    </div>
                                    <div class="white" style="margin: 0 4px">
                                        :
                                    </div>
                                    <div class="item white">
                                        {{ timeData.minutes }}
                                    </div>
                                    <div class="white" style="margin: 0 4px">
                                        :
                                    </div>
                                    <div class="item white">
                                        {{ timeData.seconds }}
                                    </div>
                                </div>
                            </count-down>
                        </div>
                    </div>
                    <div class="price-wrap lighter">
                        <div class="flex row-between">
                            <div class="price">
                                <div
                                    class="flex"
                                    v-if="
                                        Number(
                                            checkedGoods.sell_price ||
                                                goodsDetails.min_lineation_price
                                        )
                                    "
                                >
                                    日常价
                                    <span class="m-l-20">
                                        <price-formate
                                            :price="
                                                checkedGoods.sell_price ||
                                                goodsDetails.min_lineation_price
                                            "
                                            :weight="400"
                                        />
                                    </span>
                                </div>
                                <div class="flex m-t-10">
                                    秒杀价
                                    <div class="primary m-l-20">
                                        <price-formate
                                            :price="
                                                checkedGoods.seckill_price ||
                                                goodsDetails.min_price
                                            "
                                            :subscript-size="16"
                                            :first-size="26"
                                            :second-size="16"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="rate flex-col row-right">
                                <div class="primary" style="font-size: 20px">
                                    {{ comment.percent }}
                                </div>
                                <div class="lighter">好评率</div>
                            </div>
                        </div>
                    </div>
                    <div class="sales-click muted sm">
                        <span class="m-6"> 运费：{{ goodsDetails.free_shipping_tips || '以提交订单时为准' }} </span>
                        <span v-if="goodsDetails.is_sales_num">｜</span>
                        <span class="m-6" v-if="goodsDetails.is_sales_num"> 销量：{{ activity.sales_volume }}件 </span>
                        <span v-if="goodsDetails.is_click_num">｜</span>
                        <span class="m-6" v-if="goodsDetails.is_click_num"> 浏览量：{{ activity.browse_volume }}次 </span>
                    </div>

                    <!-- 规格 -->
                    <div class="spec-wrap m-t-20">
                        <div
                            class="spec flex m-b-16"
                            v-for="(item, index) in goodsSpec"
                            :key="index"
                        >
                            <div class="lighter spec-name">{{ item.name }}</div>
                            <div class="spec-list flex flex-wrap">
                                <div
                                    :class="[
                                        'spec-item lighter',
                                        { active: specitem.checked },
                                    ]"
                                    v-for="(specitem, sindex) in item.spec_list"
                                    :key="sindex"
                                    @click="
                                        onChoseSpecItem(item.id, specitem.id)
                                    "
                                >
                                    {{ specitem.value }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--数量  -->
                    <div class="goods-num flex">
                        <div class="num lighter">数量</div>
                        <number-box
                            v-model="goodsNum"
                            :min="1"
                            :max="activity.max_buy || checkedGoods.stock"
                        />
                        <div class="m-l-10 muted sm" v-if="goodsDetails.is_stock_show">
                            库存: {{ checkedGoods.stock }}
                        </div>
                        <div class="m-l-20 sm muted" v-if="!!activity.max_buy">
                            限购 {{activity.max_buy}} 件
                        </div>
                    </div>
                    <!-- 立即购买  加入购物车 收藏该商品 -->
                    <div class="goods-btns flex">
                        <div class="btn bg-primary white" @click="onBuyNow">
                            立即购买
                        </div>
                        <!-- <div class="btn primary addcart" @click="onAddCart" v-if="activity.type != 1">加入购物车</div> -->
                        <div
                            class="btn lighter collection flex row-center"
                            @click="onCollectionGoods"
                        >
                            <img
                                class="start-icon m-r-8"
                                :src="
                                    goodsDetails.is_collect
                                        ? require('~/assets/images/icon_star_s.png')
                                        : require('~/assets/images/icon_star.png')
                                "
                            />
                            <div>收藏该商品</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 底部 -->
            <div class="details-wrap flex m-t-16">
                <!-- 左侧 -->
                <!-- 猜你喜欢 -->
                <div class="goods-like m-l-16">
                    <div class="title flex row-center bg-white">
                        - 猜你喜欢 -
                    </div>
                    <div class="goods-list">
                        <template
                            v-for="(item, index) in goodsDetails.recommend"
                        >
                            <div
                                @click="toOther(item.id)"
                                class="item bg-white"
                                v-if="index < 5"
                                :key="index"
                            >
                                <el-image
                                    class="goods-img"
                                    :src="item.goods_snap.image"
                                ></el-image>
                                <div class="goods-name line-2">
                                    {{ item.goods_snap.name }}
                                </div>
                                <div class="price flex m-t-8">
                                    <div class="primary m-r-8">
                                        <price-formate
                                            :price="item.min_seckill_price"
                                            :first-size="16"
                                        />
                                    </div>
                                    <div class="muted sm line-through">
                                        <price-formate
                                            :price="item.goods_snap.max_price"
                                        />
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="details bg-white flex-1">
                    <el-tabs v-model="active">
                        <el-tab-pane label="商品详情">
                            <div
                                class="rich-text"
                                v-html="goodsDetails.content"
                            ></div>
                        </el-tab-pane>
                        <el-tab-pane label="商品评价">
                            <div class="evaluation">
                                <div class="evaluation-hd flex">
                                    <div class="rate flex">
                                        <div class="lighter m-r-8">好评率</div>
                                        <div
                                            class="primary"
                                            style="font-size: 30px"
                                        >
                                            {{ comment.percent }}
                                        </div>
                                    </div>
                                    <div class="score lighter flex">
                                        <span class="m-r-8">评分</span>
                                        <el-rate
                                            v-model="comment.star"
                                            disabled
                                            allow-half
                                        ></el-rate>
                                    </div>
                                </div>
                                <div class="evaluation-tab flex">
                                    <div
                                        :class="[
                                            'item',
                                            {
                                                active:
                                                    commentActive == item.id,
                                            },
                                        ]"
                                        v-for="(item, index) in comment.comment"
                                        :key="index"
                                        @click="commentActive = item.id"
                                    >
                                        {{ item.name }}({{ item.count }})
                                    </div>
                                </div>
                            </div>
                            <div>
                                <template
                                    v-for="(item, index) in comment.comment"
                                >
                                    <comment-list
                                        v-show="commentActive == item.id"
                                        :goods-id="goodsDetails.id"
                                        :type="item.id"
                                        :key="index"
                                    ></comment-list>
                                </template>
                            </div>
                        </el-tab-pane>
                    </el-tabs>
                </div>
                <div
                    class="mobile-code p-b-16"
                >
                    <div class="title flex row-center">手机端访问</div>
                  
                    <div
                        class="flex-col col-center m-t-20"
                    >
                        <vue-qr
                            style="width: 100px; height: 100px"
                            class="bicode"
                            :logoScale="20"
                            :margin="0"
                            :dotScale="1"
                            :text="mobileLink"
                        ></vue-qr>
                        <div class="lighter m-t-10">公众号H5</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import NumberBox from "~/components/public/numberBox";
import CountDown from "~/components/countDown";
import { config } from '~/config/app'
import { mapActions } from "vuex";
import { Message } from "element-ui";
import headerMixins from "@/mixins/header";
import { mapState } from "vuex";
export default {
    mixins: [headerMixins],

    components: {
        NumberBox,
        CountDown,
    },
    async asyncData({ params, $get, app, query }) {
        // 商品详情
        const { data, code, msg } = await $get("seckill/detail", {
            params: {
                id: query.id,
            },
        });
        if (code == 0) {
            Message({
                message: msg,
                type: "error",
            });
            setTimeout(() => app.router.back(), 1500);
        }

        // 请求评价
        let comment = {};
        let commentActive = 0;

        const dataC = await $get("/pc/goodsCommentCategory", {
            params: {
                goods_id: data.id,
            },
        });
        if (dataC.code == 1) {
            comment = dataC.data;
            commentActive = dataC.data.comment[0].id;
            console.log("dataC", dataC);
        }

        return {
            goodsDetails: data,
            goodsImage: data.goods_image,
            activity: data.activity,
            commentActive,
            comment,
            id: query.id,
        };
    },

    watchQuery: ["id"],
    data() {
        return {
            config,
            // 商品数据
            goodsDetails: {},
            // 商品图片
            goodsImage: [],
            activity: {},
            swiperOptions: {
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                preventClicks: true,
                slidesPerView: "auto",
            },
            active: "0",
            commentActive: 0,
            swiperIndex: 0,
            // 评价分类
            comment: {},
            // 选中的规格
            checkedGoods: {},
            // 选中数量
            goodsNum: 1,
            // 商品规格
            goodsSpec: [],
            id: "",
            timeData: {},
        };
    },
    created() {
        this.id = this.$route.query.id;
        // this.getComment(this.id);
    },
    methods: {
        ...mapActions(["getPublicData"]),
        // onClickSlide(e) {
        //   this.swiperIndex = e;
        // },

        // 去到其他商品
        toOther(id) {
            this.$router.push("/goods_details_seckill?id=" + id);
        },

        // 规格选择
        onChoseSpecItem(id, specid) {
            console.log("id", id);
            console.log("specid", specid);
            const { goodsSpec } = this;
            goodsSpec.forEach((item) => {
                if (item.spec_list && item.id == id) {
                    item.spec_list.forEach((specitem) => {
                        specitem.checked = 0;
                        if (specitem.id == specid) {
                            specitem.checked = 1;
                        }
                    });
                }
            });
            this.goodsSpec = [...goodsSpec];
        },
        // 加入购物车
        async onAddCart() {
            if (this.checkGoodsLimit()) return

            const {
                goodsNum,
                checkedGoods: { id },
            } = this;
            const { code, data, msg } = await this.$post("/cart/add", {
                item_id: id,
                goods_num: goodsNum,
            });
            if (code == 1) {
                this.getPublicData();
                this.$message({
                    message: msg,
                    type: "success",
                });
            }
            this.getGoodsDetail()
        },
        onBuyNow() {
            if (!this.token) {
                this.$message({
                    message: "请先登录",
                    type: "error",
                });
                return this.$router.push("account/login");
            }

            if (this.checkedGoods.stock <= 0) {
                return this.$message({
                    message: "商品库存不足",
                    type: "error",
                });
            }

            if (this.checkGoodsLimit()) return

            const {
                goodsNum,
                checkedGoods: { id },
            } = this;
            const goods = [
                {
                    item_id: id,
                    goods_num: goodsNum,
                    num: goodsNum,
                },
            ];
            this.$router.push({
                path: "/confirm_order",
                query: {
                    data: encodeURIComponent(
                        JSON.stringify({
                            goods,
                        })
                    ),
                    source: "buy_now", // 立即购买-buy_now;购物车购买-cart
                    order_type: 2, // 订单类型 0-普通订单 1-拼团订单 2-秒杀订单 3-砍价订单
                    seckill_id: this.goodsDetails.activity.id,
                },
            });
        },
        async getGoodsDetail() {
            const { data, code } = await this.$get("seckill/detail", {
                params: {
                    id: this.id,
                },
            });
            if (code == 1) {
                this.goodsDetails = data;
            }
        },
        // 收藏
        async onCollectionGoods() {
            if (!this.token) {
                this.$message({
                    message: "请先登录",
                    type: "error",
                });
                return this.$router.push("account/login");
            }

            const {
                goodsDetails: { is_collect },
            } = this;
            const { data, code } = await this.$post(
                "collect/handleCollectgoods",
                {
                    is_collect: is_collect == 0 ? 1 : 0,
                    goods_id: this.goodsDetails.id,
                }
            );
            if (code == 1) {
                if (is_collect == 0) {
                    this.$message({
                        message: "收藏成功",
                        type: "success",
                    });
                    this.goodsDetails.is_collect = 1;
                } else {
                    this.$message({
                        message: "取消收藏成功",
                        type: "success",
                    });
                    this.goodsDetails.is_collect = 0;
                }
            }
        },
        // 获取评价分类
        async getComment() {
            const { data, code } = await this.$get("/pc/goodsCommentCategory", {
                params: {
                    goods_id: this.goodsDetails.id,
                },
            });
            if (code == 1) {
                this.comment = data;
                this.commentActive = data.comment[0].id;
            }
        },
        onChangeDate(e) {
            let timeData = {};
            for (let prop in e) {
                if (prop !== "milliseconds")
                    timeData[prop] = ("0" + e[prop]).slice(-2);
            }
            this.timeData = timeData;
        },
        // 校验商品是否超出限购
        checkGoodsLimit() {
            if (this.goodsDetails.activity.max_buy > 0 && this.goodsNum > this.goodsDetails.activity.max_buy) {
                this.$message({
                    message: `当前商品已超出最大限购数 ${this.goodsDetails.activity.max_buy} 件`,
                    type: "error",
                });
                return true
            } else {
                return false
            }
        }
    },
    watch: {
        goodsSpec: {
            immediate: true,
            handler(value) {
                const goods_item = this.goodsDetails.spec_value_list;
                let keyArr = [];
                value.forEach((item) => {
                    if (item.spec_list) {
                        item.spec_list.forEach((specitem) => {
                            if (specitem.checked) {
                                keyArr.push(specitem.id);
                            }
                        });
                    }
                });
                if (!keyArr.length) return;
                let key = keyArr.join(",");
                let index = goods_item.findIndex((item) => {
                    return item.spec_value_ids == key;
                });
                if (index == -1) {
                    index = 0;
                }
                this.checkedGoods = goods_item[index];
                console.log(this.checkedGoods);
            },
        },
        goodsDetails: {
            immediate: true,
            handler(value) {
                if (!value.spec_value) return;
                value.spec_value.forEach((item) => {
                    item.spec_list.forEach((specitem, specindex) => {
                        if (specindex == 0) {
                            specitem.checked = 1;
                        } else {
                            specitem.checked = 0;
                        }
                    });
                });
                this.goodsSpec = [...value.spec_value];
            },
        },
    },
    computed: {
        countTime() {
            const info = this.activity;
            return info ? info.end_time - Date.now() / 1000 : 0;
        },

        ...mapState(["token", "publicData"]),
        mobileLink() {
            return `${this.publicData?.shop?.mobile_uri}/bundle/pages/seckill_detail/seckill_detail?id=${this.id}`
        }
    },
};
</script>

<style lang="scss" scoped>
.goods-details {
    padding: 16px 0 44px;

    .goods-info {
        align-items: flex-start;

        .goods-swiper {
            width: 430px;
            border-radius: 4px;

            .swiper {
                margin: 10px 0;
                padding: 0 25px;
                --swiper-navigation-size: 15px;
                --swiper-navigation-color: #888;

                .swiper-button-next,
                .swiper-button-prev {
                    top: 0;
                    width: 25px;
                    height: 100%;
                    margin-top: 0;
                    background-size: 12px 22px;
                }

                .swiper-button-prev {
                    left: 0;
                }

                .swiper-button-next {
                    right: 0;
                }

                .swiper-item {
                    cursor: pointer;
                    height: 66px;
                    width: 66px;
                    border: 2px solid transparent;

                    & ~ .swiper-item {
                        margin-left: 10px;
                    }

                    &.active {
                        border-color: $--color-primary;
                    }
                }
            }

            .current-img {
                width: 100%;
                height: 430px;
                padding: 15px;
            }
        }

        .info-wrap {
            min-height: 486px;
            border-radius: 4px;
            padding: 20px;

            .name {
                font-size: 20px;
            }

            // .price-wrap {
            //     background: url("~/assets/images/bg_price.png") no-repeat;

            //     background-size: 100%;
            //     height: 80px;
            //     padding: 0 50px 0 20px;
            //     margin-bottom: 26px;

            //     &.seckill {
            //         background-image: url("~/assets/images/bg_activity.png");

            //         .count-down {
            //             .item {
            //                 width: 30px;
            //                 height: 30px;
            //                 background: rgba(0, 0, 0, 0.5);
            //                 text-align: center;
            //                 line-height: 30px;
            //                 border-radius: 4px;
            //             }
            //         }
            //     }
            // }

            .seckill {
                background-color: $--color-primary;
                padding: 6px 10px;

                .count-down {
                    .item {
                        width: 30px;
                        height: 30px;
                        background: rgba(0, 0, 0, 0.3);
                        text-align: center;
                        line-height: 30px;
                        border-radius: 4px;
                    }
                }
            }

            .price-wrap {
                background: #f6f6f6;
                background-size: 100%;
                padding: 16px;

                .coupon-list {
                    .coupon-item {
                        color: $--color-primary;
                        border: 1px solid currentColor;
                        padding: 2px 6px;
                    }
                }

                .member-price {
                    background-color: #482406;
                    color: #fdebd5;
                    border-radius: 100px 100px 100px 0px;
                    padding: 0 6px;
                }
            }

            .sales-click {
                border-bottom: 1px dashed $--border-color-base;
                line-height: 28px;
                padding: 6px;

                & > div:first-of-type {
                    border-right: 1px solid $--border-color-base;
                }
            }

            .spec-wrap {
                .spec {
                    align-items: flex-start;

                    .spec-name {
                        margin-right: 20px;
                        margin-top: 6px;
                        flex: none;
                    }

                    .spec-item {
                        padding: 0 20px;
                        line-height: 32px;
                        border: 1px solid $--border-color-base;
                        border-radius: 2px;
                        margin-right: 10px;
                        margin-bottom: 10px;
                        cursor: pointer;

                        &.active {
                            color: $--color-primary;
                            background-color: #ffeeef;
                            border-color: currentColor;
                        }
                    }
                }
            }

            .goods-num {
                margin-bottom: 30px;

                .num {
                    margin-right: 20px;
                }
            }

            .goods-btns {
                .btn {
                    line-height: 42px;
                    border-radius: 2px;
                    margin-right: 14px;
                    text-align: center;
                    width: 120px;
                    border: 1px solid $--color-primary;
                    cursor: pointer;

                    &.addcart {
                        color: $--color-primary;
                        background-color: #ffeeef;
                        border-color: currentColor;
                    }

                    &.collection {
                        width: 146px;
                        color: #666;
                        background-color: transparent;
                        border-color: $--border-color-base;

                        .start-icon {
                            width: 18.5px;
                            height: 18px;
                        }
                    }
                }
            }
        }
    }

    .details-wrap {
        align-items: stretch;

        .details {
            padding: 15px 10px;
            overflow: hidden;

            .rich-text {
                padding: 0 10px;
                width: 100%;
                overflow: hidden;

                img {
                    width: 100%;
                }

                p {
                    margin: 0;
                }
            }

            .evaluation {
                .evaluation-hd {
                    background-color: #f2f2f2;
                    height: 80px;
                    margin: 0 10px;

                    .rate {
                        height: 60px;
                        width: 220px;
                        border-right: 1px solid #e5e5e5;
                        padding-left: 10px;
                        margin-right: 40px;
                    }
                }

                .evaluation-tab {
                    margin: 16px 20px;

                    .item {
                        border-radius: 2px;
                        cursor: pointer;
                        height: 32px;
                        padding: 6px 20px;
                        color: #666;
                        background-color: #f2f2f2;
                        margin-right: 10px;

                        &.active {
                            color: #fff;
                            background-color: $--color-primary;
                        }
                    }
                }
            }
        }
    }

    .goods-like {
        width: 204px;
        border-right: 1px solid $--border-color-base;
        .goods-list {
            .item {
                padding: 12px;
                display: block;

                .goods-img {
                    width: 180px;
                    height: 180px;
                    margin-bottom: 10px;
                }
            }
        }
    }
    .mobile-code {
        width: 200px;
        border-left: 1px solid $--border-color-base;
        background-color: #fff;
    }
    .title {
        border-bottom: 1px solid $--border-color-base;
        height: 55px;
        > img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    }
}
</style>
