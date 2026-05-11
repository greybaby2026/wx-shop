<template>
    <div class="category">
        <div class="category-hd bg-white">
            <div class="category-wrap">
                <div class="category-con flex" v-if="categoryOne.length">
                    <div class="name muted">一级分类：</div>
                    <div class="category-list flex flex-wrap lighter">
                        <div
                            :class="[
                                'item line1',
                                { active: oneIndex == index },
                            ]"
                            v-for="(item, index) in categoryOne"
                            :key="index"
                            @click="changeData(item.id)"
                        >
                            {{ item.name }}
                        </div>
                    </div>
                </div>
                <div
                    class="category-con flex"
                    v-if="categoryTwo && categoryTwo.length"
                >
                    <div class="name muted">二级分类：</div>
                    <div class="category-list flex flex-wrap lighter">
                        <div
                            :class="['item line1', { active: twoIndex === '' }]"
                            @click="clickAllTwo"
                        >
                            全部
                        </div>
                        <div
                            :class="[
                                'item line1',
                                { active: twoIndex === index },
                            ]"
                            v-for="(item, index) in categoryTwo"
                            :key="index"
                            @click="changeData(item.id)"
                        >
                            {{ item.name }}
                        </div>
                    </div>
                </div>
                <div
                    class="category-con flex"
                    v-if="categoryThree && categoryThree.length"
                >
                    <div class="name muted">三级分类：</div>
                    <div class="category-list flex flex-wrap lighter">
                        <div
                            :class="[
                                'item line1',
                                { active: threeIndex === '' },
                            ]"
                            @click="clickAll"
                        >
                            全部
                        </div>
                        <div
                            :class="[
                                'item line1',
                                { active: threeIndex === index },
                            ]"
                            v-for="(item, index) in categoryThree"
                            :key="index"
                            @click="changeData(item.id)"
                        >
                            {{ item.name }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="sort m-b-16 flex bg-white">
                <div class="title muted">排序方式：</div>
                <div class="sort-name m-l-16 flex lighter">
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
                        <i
                            v-show="priceSort == 'asc'"
                            class="el-icon-arrow-up"
                        ></i>
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
                        <i
                            v-show="saleSort == 'asc'"
                            class="el-icon-arrow-up"
                        ></i>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="isHasGoods">
            <goods-list :list="goodsList" />
            <div
                class="pagination flex row-center"
                style="padding-bottom: 38px"
                v-if="count"
            >
                <el-pagination
                    background
                    hide-on-single-page
                    layout="prev, pager, next"
                    :total="count"
                    :current-page="page"
                    prev-text="上一页"
                    next-text="下一页"
                    :page-size="20"
                    @current-change="changePage"
                >
                </el-pagination>
            </div>
        </div>
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
    watchQuery: true,
    async asyncData({ query, $get }) {
        // 获取商品分类数据
        let { data } = await $get("goodsCategory/lists", {
            // params: { client: 2 },
        });
        console.log("goodsCategory", data);
        return {
            categoryList: data.lists,
        };
    },
    data() {
        return {
            count: 0,
            oneIndex: 0,
            twoIndex: "",
            threeIndex: "",
            categoryOne: [],
            categoryTwo: [],
            categoryThree: [],
            sortType: "",
            saleSort: "desc",
            priceSort: "desc",
            page: 1,
            goodsList: [],
            cateId: 0,
            isHasGoods: true,
        };
    },
    beforeRouteLeave(to, from, next) {
        if (to.path.indexOf("/goods_details") !== -1) {
            sessionStorage.setItem("categoryPageCache", JSON.stringify({
                page: this.page,
                sortType: this.sortType,
                saleSort: this.saleSort,
                priceSort: this.priceSort,
                oneIndex: this.oneIndex,
                twoIndex: this.twoIndex,
                threeIndex: this.threeIndex,
                cateId: this.cateId,
                id: this.$route.query.id
            }));
        } else {
            sessionStorage.removeItem("categoryPageCache");
        }
        next();
    },
    created() {
        this.changeSortType = trottle(this.changeSortType, 500, this);
    },
    methods: {
        changeData(id) {
            this.page = 1;
            const { categoryList } = this;
            this.setIndex(id);
            this.categoryOne = categoryList;
            this.categoryTwo = categoryList[this.oneIndex]
                ? categoryList[this.oneIndex].sons
                : [];

            if (this.categoryTwo) {
                this.categoryThree = this.categoryTwo[this.twoIndex]
                    ? this.categoryTwo[this.twoIndex].sons
                    : [];
            }

            this.setCateId(id);
            this.getGoods();
        },
        setCateId(id) {
            if (
                this.twoIndex == "" &&
                this.threeIndex == "" &&
                this.oneIndex !== ""
            ) {
                this.cateId = this.categoryOne[this.oneIndex].id;
            }
            if (this.threeIndex == "" && this.twoIndex !== "") {
                this.cateId = this.categoryTwo[this.twoIndex].id;
            }
            if (id) {
                this.cateId = id;
            }
        },
        setIndex(id) {
            const { categoryList } = this;
            categoryList.some((oitem, oindex) => {
                if (oitem.id === id) {
                    this.oneIndex = oindex;
                    this.twoIndex = "";
                    this.threeIndex = "";
                    return true;
                }
                return (
                    oitem.sons &&
                    oitem.sons.some((witem, windex) => {
                        if (witem.id === id) {
                            this.oneIndex = oindex;
                            this.twoIndex = windex;
                            this.threeIndex = "";
                            return true;
                        }
                        return (
                            witem.sons &&
                            witem.sons.some((titem, tindex) => {
                                if (titem.id === id) {
                                    this.oneIndex = oindex;
                                    this.twoIndex = windex;
                                    this.threeIndex = tindex;
                                    return true;
                                }
                            })
                        );
                    })
                );
            });
        },
        clickAllTwo() {
            this.twoIndex = "";
            this.threeIndex = "";
            this.changeData();
        },
        clickAll() {
            this.threeIndex = "";
            this.changeData();
        },
        changeSortType(type) {
            this.sortType = type;
            this.page = 1;
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
        // 获取商品列表
        async getGoods() {
            const { priceSort, sortType, saleSort } = this;
            let category_id = this.cateId;
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
                    category_id: category_id,
                    price: this.sortType == "price" ? this.priceSort : "",
                    sale: this.sortType == "sales_sum" ? this.saleSort : "",
                    presell: 0,
                },
            });
            this.goodsList = lists;
            if (!lists.length) {
                this.isHasGoods = false;
            } else {
                this.isHasGoods = true;
            }
            this.count = count;
        },
    },
    watch: {
        categoryList: {
            immediate: true,
            handler(value) {
                const { id } = this.$route.query;
                
                const cacheStr = sessionStorage.getItem("categoryPageCache");
                if (cacheStr) {
                    try {
                        const cache = JSON.parse(cacheStr);
                        if (cache.id == id) {
                            this.page = cache.page || 1;
                            this.sortType = cache.sortType || "";
                            this.saleSort = cache.saleSort || "desc";
                            this.priceSort = cache.priceSort || "desc";
                            this.oneIndex = cache.oneIndex;
                            this.twoIndex = cache.twoIndex;
                            this.threeIndex = cache.threeIndex;
                            this.cateId = cache.cateId;
                            
                            this.categoryOne = value;
                            this.categoryTwo = value[this.oneIndex] ? value[this.oneIndex].sons : [];
                            if (this.categoryTwo && this.twoIndex !== "") {
                                this.categoryThree = this.categoryTwo[this.twoIndex] ? this.categoryTwo[this.twoIndex].sons : [];
                            }
                            this.getGoods();
                            return;
                        }
                    } catch (e) {
                        console.error(e);
                    }
                }
                
                this.changeData(Number(id));
            },
        },
    },
};
</script>

<style lang="scss" scoped>
.category {
    padding: 16px 0;
    .category-hd {
        .category-wrap {
            padding: 0 16px;
        }
        .category-con {
            border-bottom: 1px dashed #e5e5e5;
            align-items: flex-start;
            padding-top: 16px;

            .name {
                flex: none;
            }
            .item {
                margin-bottom: 16px;
                width: 84px;
                margin-left: 14px;
                cursor: pointer;
                &.active {
                    color: $--color-primary;
                }
                &:hover {
                    color: $--color-primary;
                }
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
}
</style>
