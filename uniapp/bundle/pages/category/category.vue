<template>
    <view class="search-container" :class="themeName">
        <!-- 搜索结果 -->
        <view class="search-header">
            <view v-if="banner.style">
                <w-banner :content="banner.content" :styles="banner.styles" />
            </view>
            <!-- 排序方式 -->
            <view class="sort">
                <!-- 综合 -->
                <view
                    :class="['sort-item', { 'sort-item--active': !search.price && !search.sale }]"
                    @tap="handSortOptions('all')"
                >
                    <text>综合</text>
                </view>
                <!-- 价格 -->
                <view
                    :class="['sort-item', { 'sort-item--active': search.price }]"
                    @tap="handSortOptions('price')"
                >
                    <text>价格</text>
                    <sort-icon class="m-l-10" :status="search.price" :active-color="themeColor" />
                </view>
                <!-- 销量 -->
                <view
                    :class="['sort-item', { 'sort-item--active': search.sale }]"
                    @tap="handSortOptions('sale')"
                >
                    <text>销量</text>
                    <sort-icon class="m-l-10" :status="search.sale" :active-color="themeColor" />
                </view>
                <!-- 商品搜索 -->
                <view class="sort-item" @click="goSearch">
                    <view>
                        <u-icon name="search" size="38" />
                    </view>
                </view>
                <!-- 商品卡片样式 -->
                <view class="sort-item">
                    <view v-show="goodsCardStyle === 'rectangle'" @tap="goodsCardStyle = 'square'">
                        <u-icon name="grid" size="38" />
                    </view>
                    <view v-show="goodsCardStyle === 'square'" @tap="goodsCardStyle = 'rectangle'">
                        <u-icon name="list-dot" size="38" />
                    </view>
                </view>
                <view
                    :class="['sort-item', { 'sort-item--active': search.sale }]"
                    @tap="showScreen = true"
                >
                    <text>筛选</text>
                </view>
            </view>
        </view>

        <!-- 商品列表 -->
        <mescroll-uni
            ref="mescrollRef"
            class="mescroll"
            :height="height"
            :up="{
                auto: true,
                noMoreSize: 10,
                empty: {
                    icon: '/static/images/empty/shop.png',
                    tip: '没有找到商品~',
                    fixed: true
                }
            }"
            @init="mescrollInit"
            @down="downCallback"
            @up="upCallback"
        >
            <view :class="['goods-container', `goods-container-${goodsCardStyle}`]">
                <goods-card
                    v-for="item in goodsList"
                    :key="item.id"
                    :shape="goodsCardStyle"
                    class="goods-item"
                    :name="item.name"
                    :image="item.image"
                    :price="item.sell_price"
                    :minPrice="item.lineation_price"
                    @click.native="goGoodsDetail(item.id)"
                />
            </view>
        </mescroll-uni>

        <u-popup
            class="screen-popup"
            v-model="showScreen"
            mode="bottom"
            :border-radius="30"
            safe-area-inset-bottom
        >
            <view style="height: 100%" class="flex-col">
                <view class="bold text-center lg p-20">筛选</view>
                <view class="screen-content">
                    <scroll-view style="height: 800rpx" scroll-y="true">
                        <view class="screen-item">
                            <view class="bold">会员价</view>
                            <view class="brand-list flex flex-wrap m-t-30">
                                <view
                                    class="brand-item m-b-20 line-1"
                                    :class="{
                                        active: item.value === search.is_member_price
                                    }"
                                    v-for="item in vipLists"
                                    :key="item"
                                    @click="handlemenber(item.value)"
                                >
                                    {{ item.name }}
                                </view>
                            </view>
                        </view>
                        <view class="screen-item">
                            <view class="bold">价格区间</view>
                            <view class="brand-list flex flex-wrap m-t-30">
                                <view class="price-item m-b-20 line-1">
                                    <u-input
                                        v-model="search.min_price"
                                        input-align="center"
                                        type="number"
                                        placeholder="最低价"
                                        :clearable="false"
                                    />
                                </view>
                                <view class="m-b-20 m-r-20 m-l-20">——</view>
                                <view class="price-item m-b-20 line-1">
                                    <u-input
                                        v-model="search.max_price"
                                        input-align="center"
                                        type="number"
                                        placeholder="最高价"
                                        :clearable="false"
                                /></view>
                            </view>
                        </view>
                        <view class="screen-item">
                            <view class="bold">品牌</view>
                            <view class="brand-list flex flex-wrap m-t-30">
                                <view
                                    class="brand-item m-b-20 line-1"
                                    :class="{
                                        active: item.id == search.brand_id
                                    }"
                                    v-for="(item, index) in brandLists"
                                    :key="index"
                                    @tap="handleScreen(item)"
                                >
                                    {{ item.name }}
                                </view>
                            </view>
                        </view>
                    </scroll-view>
                </view>
                <view class="screen-footer flex-1 flex">
                    <button hover-class="none" class="btn btn--reset" size="md" @tap="handleReset">
                        重置
                    </button>
                    <button
                        hover-class="none"
                        class="btn btn--confirm"
                        size="md"
                        @tap="handleConfirm"
                    >
                        确定
                    </button>
                </view>
            </view>
        </u-popup>
    </view>
</template>

<script>
import { apiGoodsLists, apiBrandLists, apiGoodsCategory } from '@/api/goods'
import { apiSearchHistory, apiSearchHistoryClear } from '@/api/goods'
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins'
import { apiGetPage } from '@/api/store'
import { flattenArray } from '@/utils/tools'
import { getSafeBottom } from '@/utils/tools'
export default {
    name: 'Search',
    mixins: [MescrollMixin],

    data() {
        return {
            height: 0,
            categoryId: '',
            showScreen: false,
            goodsList: [], // 商品列表
            searchStatus: false, // 搜索状态
            goodsCardStyle: 'square', // 商品卡片样式: square -正方形; rectangle -长方形
            search: {
                is_member_price: '',
                max_price: '',
                min_price: '',
                name: '', // 商品名称
                category_id: '', // 分类
                price: '', // 价格排序: desc-降序; asc-升序;
                sale: '', // 销量排序: desc-降序; asc-升序;
                brand_id: '' //品牌
            },
            // 品牌列表
            brandLists: [],
            vipLists: [
                { name: '参与', value: 1 },
                { name: '不参与', value: 0 }
            ],
            lists: [],
            flattenLists: [],
            banner: {
                content: { data: [] },
                styles: ''
            }
        }
    },

    methods: {
        getBanner() {
            apiGetPage({
                type: 2
            }).then((res) => {
                const { content } = res
                this.banner.content.data = [
                    content[1].content.data.find((item) => item.category.id == this.categoryId)
                ]
                this.banner.styles = {
                    border_radius: 3,
                    indicator_align: 'center',
                    indicator_color: '#FF2C3C',
                    indicator_style: 2,
                    padding_horizontal: 10,
                    padding_top: 10
                }
            })
        },
        setTitle() {
            let title = ''
            this.flattenLists.find((item) => {
                if (item.id == this.categoryId) {
                    title = item.name
                }
            })
            uni.setNavigationBarTitle({
                title
            })
        },
        getLists() {
            apiGoodsCategory().then((res) => {
                this.lists = res.lists
                this.flattenLists = flattenArray(res.lists, 'sons')
                this.setTitle()
            })
        },
        // 初始化Mescroll
        mescrollInit(mescroll) {
            // console.log(mescroll);
            this.mescroll = mescroll
        },

        // 下拉刷新
        downCallback() {
            this.mescroll.resetUpScroll()
        },

        // 上拉加载更多
        upCallback(page) {
            const pageNum = page.num
            const pageSize = page.size

            apiGoodsLists({
                ...this.search,
                category_id: this.categoryId,
                page_no: pageNum,
                page_size: pageSize
            })
                .then(({ lists, page_size, count }) => {
                    // 如果是第一页需手动置空列表
                    if (page.num == 1) this.goodsList = []
                    // 重置列表数据
                    this.goodsList = [...this.goodsList, ...lists]
                    // this.mescroll.endBySize(page_size, count)
                    this.mescroll.endSuccess(lists.length, count)
                })
                .catch(() => {
                    this.mescroll.endErr()
                })
        },
        goSearch() {
            this.$Router.push({
                path: '/pages/goods_search/goods_search',
                query: {
                    category_id: this.categoryId
                }
            })
        },
        // 跳转商品详情
        goGoodsDetail(id) {
            this.$Router.push({
                path: '/pages/goods_detail/goods_detail',
                query: {
                    id
                }
            })
        },

        // 搜索
        handleSearch(value) {
            this.searchStatus = true
            this.$set(this.search, 'name', value)
            this.mescroll && this.mescroll.resetUpScroll()
        },
        //是否参与会员价
        handlemenber(val) {
            this.search.is_member_price = val
        },

        // 排序方式
        handSortOptions(type) {
            switch (type) {
                case 'all':
                    this.$set(this.search, 'sale', '')
                    this.$set(this.search, 'price', '')
                    break
                case 'sale':
                    this.$set(this.search, 'price', '')
                    this.$set(this.search, 'sale', this.getSortReverse(this.search.sale))
                    break
                case 'price':
                    this.$set(this.search, 'sale', '')
                    this.$set(this.search, 'price', this.getSortReverse(this.search.price))
                    break
            }
            // 重载数据
            this.mescroll.resetUpScroll()
        },

        // 获取反向排序
        getSortReverse(sort) {
            switch (sort) {
                case 'asc':
                    return 'desc'
                case 'desc':
                    return 'asc'
                default:
                    return 'desc'
            }
        },

        //筛选条件
        getSearchList() {
            apiBrandLists({
                page_size: 999
            }).then((res) => {
                this.brandLists = res.lists
            })
        },
        // 选中条件
        handleScreen(item) {
            this.search.brand_id = item.id
        },
        // 确定
        handleConfirm() {
            this.showScreen = false
            this.mescroll.resetUpScroll()
        },
        // 重置
        handleReset() {
            this.search.brand_id = ''
            this.search.is_member_price = ''
            this.search.max_price = ''
            this.search.min_price = ''
            this.showScreen = false
            this.mescroll.resetUpScroll()
        }
    },

    onLoad() {
        const options = this.$Route.query
        this.categoryId = options.id
        this.getSearchList()
        this.getLists()
        this.getBanner()
        console.log(uni.getSystemInfoSync().windowHeight)
        // #ifndef H5
        this.height = this.$px2rpx(uni.getSystemInfoSync().windowHeight - 41 - getSafeBottom())
        // #endif
        // #ifdef H5
        this.height = this.$px2rpx(uni.getSystemInfoSync().windowHeight - 41)
        // #endif
    }
}
</script>

<style lang="scss" scoped>
.search-container {
    display: flex;
    flex-direction: column;
}

.search-input {
    display: flex;
    align-items: center;
    height: 100rpx;
    padding: 0 20rpx;
    background-color: #ffffff;

    .search-action {
        width: 100rpx;
        text-align: center;
    }
}

.search-options-container {
    box-sizing: border-box;
    height: 100vh;
    padding: 0 20rpx;
    background-color: #ffffff;

    .search-options {
        &-header {
            display: flex;
            justify-content: space-between;
            padding: 30rpx 0 24rpx 0;
            font-size: $-font-size-nr;
        }

        &-main {
            display: flex;
            flex-wrap: wrap;
        }

        &-item {
            padding: 8rpx 24rpx;
            margin: 0 16rpx 24rpx 0;
            border-radius: 60px;
            background-color: #f4f4f4;
        }
    }
}

.sort {
    display: flex;
    align-items: center;
    height: 80rpx;
    font-size: $-font-size-nr;
    background-color: #ffffff;

    &-item {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    &-item--active {
        @include font_color();
    }

    &-style {
        position: relative;
        width: 100rpx;
        display: flex;
        justify-content: center;
        align-items: center;

        &::before {
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            display: block;
            content: '';
            height: 1em;
            border-left: $-solid-border;
        }
    }
}

.goods-container {
    flex: 1;
    display: flex;
    padding: 0 20rpx;

    &-square {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    &-rectangle {
        flex-direction: column;
    }

    .goods-item {
        margin-top: 16rpx;
    }
}
.price-item {
    width: 40%;
    padding: 0 20rpx;
    border: 1px solid transparent;
    background-color: #f5f5f5;
    line-height: 60rpx;
    border-radius: 60rpx;
    text-align: center;
}
.screen-popup {
    .screen-content {
        height: 100%;

        .screen-item {
            padding: 20rpx;
        }

        .brand-list {
            border-radius: 10rpx;

            .brand-item {
                width: 30%;
                padding: 0 20rpx;
                border: 1px solid transparent;
                background-color: #f5f5f5;
                line-height: 60rpx;
                border-radius: 60rpx;
                text-align: center;
                &.active {
                    @include border_color();
                    @include background_color(0.3);
                    @include font_color();
                }
                &:not(:nth-of-type(3n)) {
                    margin-right: 20rpx;
                }
            }
        }
    }

    .screen-footer {
        padding: 20rpx;

        .btn {
            flex: 1;
            border-radius: 60rpx;

            &--reset {
                @include font_color();
                border: 1px solid currentColor;
                margin-right: 20rpx;
                z-index: 999;
            }

            &--confirm {
                @include background_color();
                color: #fff;
            }
        }
    }
}
</style>
