<template>
    <div class="goods-list">
        <!-- <div class="banner m-t-16">
            <ad-item :item="ad" v-if="ad.image"></ad-item>
        </div> -->
        <div class="sort m-b-16 flex bg-white">
            <div class="title">排序方式：</div>
            <div class="sort-name m-l-16 flex">
                <div
                    :class="['item', { active: sortType == '' }]"
                    @click="changeSortType('')"
                >
                    综合
                </div>
                <div
                    :class="['item', { active: sortType == 'price' }]"
                    @click="changeSortType('price')"
                >
                    价格
                    <i
                        v-show="priceSort == 'desc'"
                        class="el-icon-arrow-down"
                    ></i>
                    <i v-show="priceSort == 'asc'" class="el-icon-arrow-up"></i>
                </div>
                <div
                    :class="['item', { active: sortType == 'sales_sum' }]"
                    @click="changeSortType('sales_sum')"
                >
                    销量
                    <i
                        v-show="saleSort == 'desc'"
                        class="el-icon-arrow-down"
                    ></i>
                    <i v-show="saleSort == 'asc'" class="el-icon-arrow-up"></i>
                </div>
            </div>
        </div>
        <template v-if="goodsList.length">
            <goods-list :list="goodsList" />
            <div
                class="pagination flex row-center"
                style="padding-bottom: 38px"
                v-if="count"
            >
                <el-pagination
                    background
                    layout="prev, pager, next"
                    :total="count"
                    prev-text="上一页"
                    next-text="下一页"
                    hide-on-single-page
                    :page-size="20"
                    @current-change="changePage"
                >
                </el-pagination>
            </div>
        </template>
        <null-data
            v-else
            :img="require('~/assets/images/goods_null.png')"
            text="暂无商品~"
        ></null-data>
    </div>
</template>

<script>
import { trottle } from "~/utils/tools";
import headerMixins from "@/mixins/header";
export default {
    mixins: [headerMixins],
    components: {},
    watchQuery: true,
    async asyncData({ $get, params, query }) {
        const { type } = params;
        const { name } = query;
        const data = {
            page_size: 20,
            type,
            name,
            type,
            presell: 0,
        };
        if (type == 1) {
            (data.sort = "desc"), (data.sort_type = "sales_sum");
        }
        const {
            data: { lists, count },
        } = await $get("goods/lists", {
            params: data,
        });
        // const {
        //     data: indexData
        // } = await $get("pc/index");
        // let ad = {};
        // switch (type) {
        //     case "1":
        //         ad = indexData.host_ad;
        //         break;
        //     case "2":
        //         ad = indexData.new_ad;
        //         break;
        //     case "3":
        //         ad = indexData.best_ad;
        //         break;
        // }
        return {
            goodsList: lists,
            count,
            // ad
        };
    },
    data() {
        return {
            sortType: "",
            saleSort: "desc",
            priceSort: "desc",
            page: "",
        };
    },
    created() {
        this.changeSortType = trottle(this.changeSortType, 500, this);
    },
    methods: {
        changeSortType(type) {
            this.sortType = type;
            switch (type) {
                case "price":
                    if (this.priceSort == "asc") {
                        this.priceSort = "desc";
                    } else if (this.priceSort == "desc") {
                        this.priceSort = "asc";
                    }
                    break;
                case "sales_sum":
                    if (this.saleSort == "asc") {
                        this.saleSort = "desc";
                    } else if (this.saleSort == "desc") {
                        this.saleSort = "asc";
                    }
                    break;
                default:
            }
            this.getGoods();
        },
        changePage(current) {
            this.page = current;
            this.getGoods();
        },
        async getGoods() {
            const { name } = this.$route.query;
            const { priceSort, sortType, saleSort } = this;
            let sort = "";
            switch (sortType) {
                case "price":
                    sort = priceSort;
                    break;
                case "sales_sum":
                    sort = saleSort;
                    break;
            }
            const {
                data: { lists, count },
            } = await this.$get("goods/lists", {
                params: {
                    page_size: 20,
                    page_no: this.page,
                    price: this.sortType == "price" ? this.priceSort : "",
                    sale: this.sortType == "sales_sum" ? this.saleSort : "",
                    name,
                    presell: 0,
                },
            });
            this.goodsList = lists;
        },
    },
};
</script>

<style lang="scss">
.goods-list {
    .banner {
        img {
            width: 100%;
            display: block;
        }
    }

    .sort {
        padding: 15px 16px;

        .sort-name {
            .item {
                margin-right: 30px;
                cursor: pointer;

                &.active {
                    color: $--color-primary;
                }
            }
        }
    }
}
</style>
