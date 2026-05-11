<template>
    <view :class="themeName">
        <view :class="['goods-team', { 'bg--primary': hasGoods }]">
            <mescroll-uni
                ref="mescroll"
                :up="{
                    noMoreSize: 5,
                    empty: {
                        icon: '/static/images/empty/shop.png',
                        tip: '暂无拼团商品~',
                        fixed: true
                    },
                    textColor: optionTextColor
                }"
                :down="{
                    textColor: optionTextColor
                }"
                @init="mescrollInit"
                @down="downCallback"
                @up="upCallback"
            >
                <!-- Banner -->
                <view :class="['banner', { 'bg--primary': !hasGoods }]">
                    <image class="title-image" src="/bundle/static/images/goods_team_banner.png" />
                </view>

                <!-- Goods List -->
                <view class="goods-contain">
                    <view class="goods">
                        <goods-card
                            v-for="(goods, index) in goodsList"
                            :key="index"
                            shape="rectangle"
                            :name="goods.name"
                            :image="goods.image"
                            :contain-style="{ 'border-raius': 0, height: '250rpx' }"
                            :image-style="{ width: '210rpx', height: '210rpx' }"
                        >
                            <view @click="goGoodsDetail(goods.id)">
                                <!-- 拼团信息 -->
                                <view class="flex">
                                    <goods-team-icon :count="goods.people_num" />
                                    <text class="m-l-20 xs muted"
                                        >已拼{{
                                            Number(goods.activity_sales) +
                                            Number(goods.virtual_sales_num)
                                        }}件</text
                                    >
                                </view>
                                <!-- 价格信息 -->
                                <view class="m-t-10 flex row-between">
                                    <view>
                                        <price
                                            :content="goods.min_team_price"
                                            mainSize="34rpx"
                                            minorSize="24rpx"
                                            :color="themeColor"
                                        />
                                        <price
                                            class="m-l-20 muted xs"
                                            :content="goods.max_price"
                                            :line-through="true"
                                        />
                                    </view>
                                    <view class="goods-button">去拼团</view>
                                </view>
                            </view>
                        </goods-card>
                    </view>
                </view>
            </mescroll-uni>

            <view class="fixed-contain" @click="goOrderTeamList">
                <text>我的</text>
                <text>拼团</text>
            </view>
        </view>
    </view>
</template>

<script>
import { apiGoodsTeamList } from '@/api/goods'
import { mapGetters, mapState } from 'vuex'

import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins'

export default {
    name: 'GoodsTeam',
    mixins: [MescrollMixin],

    data() {
        return {
            goodsList: [] // 商家列表
        }
    },

    computed: {
        ...mapGetters(['userInfo']),

        // 是否有拼团商品
        hasGoods() {
            return !!this.goodsList.length
        },

        // Scorll配置字体颜色
        optionTextColor() {
            return this.hasGoods ? '#FFFFFF' : '#808080'
        }
    },
    onShareAppMessage() {
        const { code } = this.userInfo

        const share = {
            path: `bundle/pages/goods_team/goods_team?invite_code=${code}`
        }
        return share
    },
    methods: {
        // 初始化Mescroll
        mescrollInit(mescroll) {
            this.mescroll = mescroll
            this.downCallback()
        },

        // 下拉刷新
        downCallback() {
            this.mescroll.resetUpScroll()
        },

        // 上拉加载更多
        upCallback(page) {
            apiGoodsTeamList({
                page_no: page.num,
                page_size: page.size
            })
                .then(({ lists, page_size, count }) => {
                    if (page.num === 1) this.goodsList = []
                    this.goodsList = [...this.goodsList, ...lists]
                    this.mescroll.endSuccess(lists.length, count)
                })
                .catch((err) => {
                    this.mescroll.endBySize()
                })
        },

        // 去商品详情
        goGoodsDetail(id) {
            this.$Router.push({
                path: '/bundle/pages/goods_team_detail/goods_team_detail',
                query: { id }
            })
        },

        // 去拼团订单列表
        goOrderTeamList() {
            this.$Router.push({
                path: '/bundle/pages/goods_team_history/goods_team_history'
            })
        }
    }
}
</script>

<style lang="scss">
.goods-team {
    display: flex;
    flex-direction: column;
    height: 100vh;
    max-height: 100vh;
    overflow: hidden;

    .banner {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100rpx;
        width: 100%;
        margin-bottom: 20rpx;
        .title-image {
            width: 440rpx;
            height: 48rpx;
        }
    }

    .goods-contain {
        padding: 0 20rpx;
    }

    .fixed-contain {
        position: fixed;
        right: 20rpx;
        bottom: 240rpx;
        z-index: 9;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 110rpx;
        height: 110rpx;
        border-radius: 50%;
        font-size: $-font-size-sm;
        font-weight: 500;
        border: $-solid-border;
        background-color: #ffffff;
    }
}

.bg--primary {
    @include background_linear();
}

.goods {
    margin-bottom: 20rpx;

    &-button {
        padding: 10rpx 35rpx;
        border-radius: 60px;
        font-size: $-font-size-nr;
        @include background_color();
        color: #ffffff;
    }
}
</style>
