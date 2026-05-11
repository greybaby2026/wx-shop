<template>
    <view class="search-container" :class="themeName">
        <u-sticky offset-top="0" h5-nav-height="0">
            <!-- 搜索 -->
            <view class="search-input">
                <u-search
                    class="flex-1"
                    v-model="search.name"
                    height="72"
                    bg-color="#F4F4F4"
                    :show-action="false"
                    placeholder="请输入要搜索的商品"
                    @focus="focusSearch"
                    @search="handleSearch"
                    :focus="!searchStatus"
                />

                <view class="search-action" @tap="handleSearch(search.name)"> 搜索 </view>
            </view>
        </u-sticky>

        <!-- 搜索 -->
        <view v-show="!searchStatus" class="search-options-container">
            <view class="search-options">
                <view class="search-options-header">
                    <text>历史搜索</text>
                    <u-icon name="trash" @tap="onSearchHistoryClear" />
                </view>

                <view class="flex row-center">
                    <u-loading mode="circle" :show="goodsSearchHistoryLoading" />
                </view>
                <template v-if="!goodsSearchHistoryLoading">
                    <view v-if="goodsSearchHistory.length" class="search-options-main">
                        <view
                            class="search-options-item"
                            v-for="(item, index) in goodsSearchHistory"
                            :key="index"
                            @tap="handleSearch(item)"
                            >{{ item }}</view
                        >
                    </view>
                    <view v-else class="flex row-center muted">暂无记录</view>
                </template>
            </view>
        </view>

        <!-- 搜索结果 -->
        <view v-show="searchStatus">
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
                    <sort-icon class="m-l-10" :status="search.price==''?'':search.price=='asc'?'desc':'asc'" :active-color="themeColor" />
                </view>
                <!-- 销量 -->
                <view
                    :class="['sort-item', { 'sort-item--active': search.sale }]"
                    @tap="handSortOptions('sale')"
                >
                    <text>销量</text>
                    <sort-icon class="m-l-10"  :status="search.sale==''?'':search.sale=='asc'?'desc':'asc'" :active-color="themeColor" />
                </view>
                <!-- 商品卡片样式 -->
                <view class="search-action">
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

            <!-- 商品列表 -->
            <mescroll-body
                ref="mescrollRef"
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
            </mescroll-body>
        </view>
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
import { apiGoodsLists, apiBrandLists } from '@/api/goods'
import { apiSearchHistory, apiSearchHistoryClear } from '@/api/goods'
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins'

export default {
    name: 'Search',
    mixins: [MescrollMixin],

    data() {
        return {
            showScreen: false,
            goodsList: [], // 商品列表
            goodsSearchHistory: [], // 商品搜索记录
            goodsSearchHistoryLoading: true, // 搜索记录请求状态loading
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
            category_id: ''
        }
    },

    methods: {
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
            if (!this.searchStatus) return

            const pageNum = page.num
            const pageSize = page.size

            apiGoodsLists({
                ...this.search,
                category_id: this.category_id,
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

        // 跳转商品详情
        goGoodsDetail(id) {
            this.$Router.push({
                path: '/pages/goods_detail/goods_detail',
                query: {
                    id
                }
            })
        },

        // 聚焦搜索
        focusSearch() {
            // this.searchStatus = false;
            this.initSearchHistoryData()
        },

        // 搜索
        handleSearch(value) {
            console.log(123456)
            this.searchStatus = true
            this.$set(this.search, 'name', value)
            this.mescroll && this.mescroll.resetUpScroll()
        },
        //是否参与会员价
        handlemenber(val) {
            console.log(123456, val)

            this.search.is_member_price = val
        },
        // 清空搜索记录
        onSearchHistoryClear() {
            uni.showModal({
                title: '温馨提示',
                content: '是否清空搜索记录?',
                confirmColor: this.themeColor,
                success: ({ confirm }) => {
                    if (!confirm) return
                    apiSearchHistoryClear()
                        .then(() => {
                            this.initSearchHistoryData()
                        })
                        .catch(() => {
                            this.$toast({
                                title: '操作失败'
                            })
                        })
                }
            })
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
		

        // 初始化搜索记录数据
        initSearchHistoryData() {
            apiSearchHistory()
                .then((data) => {
                    this.goodsSearchHistory = data
                })
                .catch((err) => {
                    this.$toast({
                        title: '搜索记录获取失败'
                    })
                    throw new Error(err)
                })
                .finally(() => {
                    this.goodsSearchHistoryLoading = false
                })
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
        if (JSON.stringify(options) !== '{}') {
            !!options.id &&
                this.$set(this.search, 'category_id', options.id) &&
                this.handleSearch('')
            // !!options.name && this.handleSearch(options.name)
        }
        this.category_id = options.category_id
        // 获取历史搜素内容
        this.initSearchHistoryData()
        this.getSearchList()
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
