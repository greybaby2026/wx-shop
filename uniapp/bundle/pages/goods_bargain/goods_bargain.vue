<template>
    <view :class="themeName">
        <!-- <view class="header">
		</view> -->
        <view class="goods-seckill">
            <mescroll-body
                ref="mescrollRef"
                @init="mescrollInit"
                @up="upCallback"
                :up="upOption"
                :down="downOption"
                @down="downCallback"
            >
                <view class="header">
                    <u-divider
                        color="#ffffff"
                        bg-color="transpartent"
                        half-width="100"
                        border-color="#ffffff"
                    >
                        <view class="flex">
                            <image
                                class="icon-image"
                                src="/bundle/static/images/icon_bargain.png"
                            ></image>
                            <text class="xl m-l-6 bold">砍价商品</text>
                        </view>
                    </u-divider>
                </view>
                <view class="goods-list">
                    <view v-for="(item, index) in bargainGoods" :key="index">
                        <view class="goods-item flex bg-white">
                            <u-image
                                width="180rpx"
                                height="180rpx"
                                border-radius="10rpx"
                                :src="item.goods_image"
                            />
                            <view class="goods-info m-l-20">
                                <view class="goods-name line-2 m-b-10">{{ item.goods_name }}</view>
                                <view class="info-footer flex row-between m-t-5">
                                    <view class="flex-col">
                                        <price
                                            :content="item.bargain_min_price"
                                            prefix="最低可砍至￥"
                                            :color="themeColor"
                                            mainSize="34rpx"
                                            minorSize="24rpx"
                                        />
                                        <price
                                            :content="item.goods_max_price"
                                            prefix="原价￥"
                                            color="#999999"
                                            mainSize="24rpx"
                                            minorSize="24rpx"
                                        />
                                    </view>

                                    <view
                                        class="btn br60 white"
                                        @click="toBargainProgress(item.activity_id, item.goods_id)"
                                        >发起砍价</view
                                    >
                                </view>
                            </view>
                        </view>
                    </view>
                </view>
            </mescroll-body>
        </view>
        <view class="fixed-contain" @click="goOrderTeamList">
            <text>砍价</text>
            <text>记录</text>
        </view>
    </view>
</template>

<script>
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins.js'
import { apiBargainLists } from '@/api/bargain.js'
import { mapGetters, mapState } from 'vuex'

export default {
    mixins: [MescrollMixin], // 使用mixin
    data() {
        return {
            upOption: {
                textColor: '#fff',
                empty: {
                    tip: '活动暂未开始~', // 提示
                    icon: '',
                    top: '40vh',
                    textColor: '#fff'
                }
            },
            downOption: {
                textColor: '#fff'
            },
            bargainGoods: [] // 列表数据--全部
        }
    },
    computed: {
        ...mapGetters(['userInfo'])
    },
    onShareAppMessage() {
        const { code } = this.userInfo

        const share = {
            path: `/bundle/pages/goods_bargain/goods_bargain?invite_code=${code}`
        }
        return share
    },
    methods: {
        goOrderTeamList() {
            this.$Router.push({
                path: '/bundle/pages/bargain_code/bargain_code'
            })
        },
        // 跳转到砍价进度
        toBargainProgress(activity_id, goods_id) {
            uni.navigateTo({
                url:
                    '/bundle/pages/bargain_progress/bargain_progress?activity_id=' +
                    activity_id +
                    '&goods_id=' +
                    goods_id
            })
        },

        // 上拉加载
        upCallback(page) {
            apiBargainLists({
                page_size: page.size,
                page_no: page.num
            })
                .then(({ lists, count }) => {
                    if (page.num == 1) this.bargainGoods = []
                    this.bargainGoods.push(...lists)
                    this.mescroll.endSuccess(lists.length, count)
                })
                .catch(() => {
                    this.mescroll.endErr()
                })
        }
    }
}
</script>

<style lang="scss">
page {
    padding: 0;
}
.header {
    padding: 26rpx 0;
    .icon-image {
        width: 40rpx;
        height: 40rpx;
    }
}
.goods-seckill {
    @include background_linear();
    .goods-list {
        margin: 0 24rpx;
        .goods-item {
            padding: 30rpx 24rpx;
            margin-top: 20rpx;
            border-radius: 20rpx;

            .goods-info {
                flex: 1;
                width: 450rpx;

                .sale-info {
                    padding: 4rpx 16rpx;
                    background-color: #ffe9eb;
                }

                .info-footer {
                    .btn {
                        @include background_linear();
                        padding: 13rpx 45rpx;
                        font-family: 'PingFang SC';
                        font-weight: normal;
                        font-size: 28rpx;
                        line-height: 42rpx;
                    }
                }
            }
        }
    }
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
</style>
