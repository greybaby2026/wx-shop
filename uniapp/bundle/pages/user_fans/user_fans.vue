<template>
    <view class="pages" :class="themeName">
        <mescroll-body
            ref="mescrollRef"
            @init="mescrollInit"
            @up="upCallback"
            :up="upOption"
            :down="downOption"
            @down="downCallback"
        >
            <view class="user-fans">
                <view class="header">
                    <view class="bg-white p-20">
                        <u-search
                            v-model="keyword"
                            shape="round"
                            placeholder="请输入搜索关键词"
                            @search="changeTab"
                            @custom="changeTab"
                        />
                    </view>

                    <view class="top-bar flex bg-white md">
                        <view
                            class="bar-item flex"
                            :class="{ 'item-active': active == 'all' }"
                            @tap="changeTab('all')"
                        >
                            全部粉丝
                        </view>
                        <view
                            class="bar-item flex"
                            :class="{ 'item-active': active == 'first' }"
                            @tap="changeTab('first')"
                        >
                            一级粉丝</view
                        >
                        <view
                            class="bar-item flex"
                            :class="{ 'item-active': active == 'second' }"
                            @tap="changeTab('second')"
                        >
                            二级粉丝</view
                        >
                    </view>
                    <view class="sort-bar flex bg-white">
                        <view class="sort-bar-item flex row-center" @tap="sortChange(0)">
                            <view :class="sortType == 0 ? 'item-active' : ''">团队排序</view>
                            <view class="arrow-icon flex-col col-center row-center">
                                <u-icon
                                    name="arrow-up-fill"
                                    :color="fansSort == 'asc' ? themeColor : '#333333'"
                                ></u-icon>
                                <u-icon
                                    name="arrow-down-fill"
                                    :color="fansSort == 'desc' ? themeColor : '#333333'"
                                ></u-icon>
                            </view>
                        </view>
                        <view class="sort-bar-item flex row-center" @tap="sortChange(1)">
                            <view :class="sortType == 1 ? 'item-active' : ''">金额排序</view>
                            <view class="arrow-icon flex-col col-center row-center">
                                <u-icon
                                    name="arrow-up-fill"
                                    :color="moneySort == 'asc' ? themeColor : '#333333'"
                                ></u-icon>
                                <u-icon
                                    name="arrow-down-fill"
                                    :color="moneySort == 'desc' ? themeColor : '#333333'"
                                ></u-icon>
                            </view>
                        </view>
                        <view class="sort-bar-item flex row-center" @tap="sortChange(2)">
                            <view :class="sortType == 2 ? 'item-active' : ''">订单排序</view>
                            <view class="arrow-icon flex-col col-center row-center">
                                <u-icon
                                    name="arrow-up-fill"
                                    :color="orderSort == 'asc' ? themeColor : '#333333'"
                                ></u-icon>
                                <u-icon
                                    name="arrow-down-fill"
                                    :color="orderSort == 'desc' ? themeColor : '#333333'"
                                ></u-icon>
                            </view>
                        </view>
                    </view>
                </view>
                <view class="content">
                    <view class="card-box p-t-20">
                        <view
                            v-for="(item, index) in fansList"
                            :key="index"
                            class="card-item flex row-between bg-white p-20"
                        >
                            <view class="flex">
                                <u-image
                                    :src="item.avatar"
                                    border-radius="50%"
                                    width="100rpx"
                                    height="100rpx"
                                />
                                <view class="fans-info m-l-20">
                                    <view class="fans-name bold line-1">{{ item.nickname }}</view>
                                    <view class="lighter xs">
                                        <view v-if="item.mobile" class="m-r-20">{{
                                            item.mobile
                                        }}</view>
                                        <view>{{ item.create_time }}</view>
                                    </view>
                                </view>
                            </view>
                            <view class="flex-col xs flex-none m-l-20 text-right">
                                <view class="msg"
                                    ><span class="fans-num">{{ item.fans }} </span>人</view
                                >
                                <view class="m-t-5 msg"
                                    ><span>{{ item.order_num }} </span>单</view
                                >
                                <view class="m-t-5 msg"
                                    ><span>{{ item.order_amount }} </span>元</view
                                >
                            </view>
                        </view>
                    </view>
                </view>
            </view>
        </mescroll-body>
    </view>
</template>

<script>
import { SortEnum } from '@/utils/enum'
import { apiDistributionFans } from '@/api/user'
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins'
export default {
    mixins: [MescrollMixin], // 使用mixin
    data() {
        return {
            upOption: {
                empty: {
                    icon: '',
                    tip: '暂无相关数据' // 提示
                }
            },
            active: 'all',
            sortType: -1,
            keyword: '',
            fansSort: SortEnum['NONE'],
            moneySort: SortEnum['NONE'],
            orderSort: SortEnum['NONE'],
            fansList: []
        }
    },
    methods: {
        onRefresh() {
            this.fansList = []
            this.mescroll.resetUpScroll()
        },

        // 上拉加载
        upCallback(page) {
            const { fansSort, moneySort, orderSort, active, keyword } = this
            const params = {
                type: active,
                keyword,
                fans: fansSort,
                order_amount: moneySort,
                order_num: orderSort,
                page_size: page.size,
                page_no: page.num
            }
            apiDistributionFans(params)
                .then((data) => {
                    if (page.num == 1) this.fansList = []
                    const curPageData = data.lists
                    const curPageLen = curPageData.length
                    this.fansList = this.fansList.concat(curPageData)
                    this.mescroll.endSuccess(curPageLen, data.count)
                })
                .catch(() => {
                    this.mescroll.endErr()
                })
        },

        changeTab(val) {
            this.active = val
            this.onRefresh()
        },

        // 排序状态变化
        sortChange(type) {
            let { fansSort, moneySort, orderSort } = this

            this.sortType = parseInt(type)
            console.log(fansSort, this.sortType, '44444')
            switch (this.sortType) {
                case 0:
                    // 团队排序
                    if (fansSort == SortEnum['DESC'] || fansSort == SortEnum['NONE']) {
                        console.log(123456)

                        this.fansSort = SortEnum['ASC']
                        this.moneySort = SortEnum['NONE']
                        this.orderSort = SortEnum['NONE']
                    } else if (fansSort == SortEnum['ASC']) {
                        this.fansSort = SortEnum['DESC']
                        this.moneySort = SortEnum['NONE']
                        this.orderSort = SortEnum['NONE']
                    }
                    break

                case 1:
                    // 金额排序
                    if (moneySort == SortEnum['DESC'] || moneySort == SortEnum['NONE']) {
                        this.moneySort = SortEnum['ASC']
                        this.fansSort = SortEnum['NONE']
                        this.orderSort = SortEnum['NONE']
                    } else if (moneySort == SortEnum['ASC']) {
                        this.moneySort = SortEnum['DESC']
                        this.fansSort = SortEnum['NONE']
                        this.orderSort = SortEnum['NONE']
                    }

                    break
                case 2:
                    // 订单排序
                    if (orderSort == SortEnum['DESC'] || orderSort == SortEnum['NONE']) {
                        this.orderSort = SortEnum['ASC']
                        this.moneySort = SortEnum['NONE']
                        this.fansSort = SortEnum['NONE']
                    } else if (orderSort == SortEnum['ASC']) {
                        this.orderSort = SortEnum['DESC']
                        this.moneySort = SortEnum['NONE']
                        this.fansSort = SortEnum['NONE']
                    }

                    break
            }

            this.onRefresh()
        }
    }
}
</script>
<style lang="scss">
.user-fans {
    .header {
        .top-bar {
            padding: 18rpx 50rpx;
            height: 100rpx;

            .bar-item {
                flex: 1;
                padding: 0 30rpx;
                height: 58rpx;

                &:not(:last-of-type) {
                    margin-right: 54rpx;
                }
            }

            .item-active {
                color: white;
                @include background_color();
                border-radius: 100rpx;
            }
        }

        .sort-bar {
            height: 80rpx;

            .sort-bar-item {
                flex: 1;

                .arrow-icon {
                    transform: scale(0.36);
                }

                .item-active {
                    @include font_color();
                }
            }
        }
    }
}

.content {
    .card-box {
        .card-item {
            .fans-name {
                width: 450rpx;
            }

            &:not(:last-of-type) {
                border-bottom: $-solid-border;
            }
            .fans-num {
                @include font_color();
            }
        }
    }
}
</style>
