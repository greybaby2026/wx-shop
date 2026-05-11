<template>
    <view>
        <view class="sort" style="font-size: 24rpx">
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
                <sort-icon class="m-l-10" :status="search.sale==''?'':search.sale=='asc'?'desc':'asc'" :active-color="themeColor" />
            </view>
        </view>
        <thirdlyTabs v-if="thirdly" :scrollheight="scrollHeight"></thirdlyTabs>
        <mescroll-uni
            v-if="scrollHeight"
            :height="scrollHeight"
            :fixed="false"
            ref="mescrollRef"
            @init="mescrollInit"
            @down="downCallback"
            @up="upCallback"
            :down="downOption"
            :up="upOption"
        >
            <view v-if="banner">
                <w-banner :content="banner.content" :styles="banner.styles" />
            </view>

            <view :class="['goods-container', `goods-container-${goodsStyle}`]">
                <view
                    class="goods-item"
                    @tap="goGoodsDetail(item.id)"
                    v-for="item in goodsList"
                    :key="item.id"
                >
                    <goods-card
                        :shape="goodsStyle"
                        :name="item.name"
                        :image="item.image"
                        :price="item.sell_price"
                        :minPrice="item.lineation_price"
                    />
                </view>
            </view>
        </mescroll-uni>
    </view>
</template>

<script type="text/javascript">
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins.js'
import { apiGoodsLists } from '@/api/goods'
import category from './category.js'
import thirdlyTabs from './thirdly-tabs.vue'
export default {
    mixins: [category, MescrollMixin],
    props: {
        goodsStyle: {
            type: String,
            default: 'rectangle'
        },
        scrollHeight: {
            type: [Number, String],
            default: 0
        },
        thirdly: {
            type: Boolean,
            default: false
        }
    },
    components: { thirdlyTabs },
    data() {
        return {
            downOption: {
                auto: false // 不自动加载 (mixin已处理第一个tab触发downCallback)
            },
            search: {
                price: '', // 价格排序: desc-降序; asc-升序;
                sale: '', // 销量排序: desc-降序; asc-升序;
                brand_id: '' //品牌
            },
            upOption: {
                auto: false, // 不自动加载
                noMoreSize: 1, //如果列表已无数据,可设置列表的总数量要大于半页才显示无更多数据;避免列表数据过少(比如只有一条数据),显示无更多数据会不好看; 默认5
                empty: {
                    icon: '/static/images/empty/shop.png',
                    tip: '暂无商品~', // 提示
                    fixed: true
                },
                toTop: {
                    bottom: 300
                }
            },
            category: {},
            goodsList: []
        }
    },
    methods: {
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
        // 上拉加载更多
        upCallback(page) {
            const pageNum = page.num
            const pageSize = page.size

            apiGoodsLists({
                ...this.search,
                page_no: pageNum,
                page_size: pageSize,
                category_id: this.category.id
            })
                .then(({ lists, page_size, count }) => {
                    // 如果是第一页需手动置空列表
                    if (page.num == 1) this.goodsList = []
                    // 重置列表数据
                    this.goodsList = this.goodsList.concat(lists)
                    this.mescroll.endBySize(lists.length, count)
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
        }
    },
    computed: {},
    watch: {
        selectIndex: {
            handler(val) {
                this.category = this.parent.lists[val] || {}
                this.goodsList = []
                this.mescroll.resetUpScroll()
            }
        },
        leftIndex: {
            handler(val) {
                this.category = this.leftlists[val] || {}
                this.goodsList = []
                this.mescroll.resetUpScroll()
            }
        },
        thirdlyIndex: {
            handler(val) {
                if (this.thirdlyLists[val]) {
                    this.category = this.thirdlyLists[val] || {}
                    this.goodsList = []
                    this.mescroll.resetUpScroll()
                }
            }
        }
    },
    created() {}
}
</script>

<style lang="scss" scoped>
.goods-container {
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
        margin-top: 20rpx;
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
</style>
