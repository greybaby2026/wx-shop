<template>
    <view :class="themeName">
        <mescroll-body
            ref="mescrollRef"
            @init="mescrollInit"
            @up="upCallback"
            :up="upOption"
            :down="downOption"
            @down="downCallback"
        >
            <view class="goods-list">
                <view class="" v-for="(item, index) in bargainGoods" :key="index">
                    <view class="bg-white flex code-item">
                        <u-image
                            :src="item.image"
                            width="90rpx"
                            height="90rpx"
                            mode="aspectFill"
                            radius="6rpx"
                        ></u-image>
                        <view class="m-l-24 flex-col desc">
                            <view class="nr line-1 bold"
                                >{{ item.title }}
                                <text v-if="item.type == 4">
                                    {{ item.name }}
                                </text></view
                            >
                            <view class="m-t-15 flex row-between">
                                <view class="xs lighter">领取时间：{{ item.create_time }}</view>
                                <!-- <view class="xs">+100</view> -->
                            </view>
                        </view>
                    </view>
                </view>
            </view>
        </mescroll-body>
    </view>
</template>

<script>
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins.js'
import { apiLuckyDrawRecord, apiLuckyDrawWinningList } from '@/api/luck_draw.js'
export default {
    mixins: [MescrollMixin], // 使用mixin
    data() {
        return {
            bargainGoods: [], // 记录列表

            upOption: {
                textColor: '#fff',
                empty: {
                    tip: '暂无中奖记录~', // 提示
                    icon: '/static/images/empty/order.png',
                    top: '200rpx',
                    textColor: '#fff'
                }
            },
            downOption: {
                // textColor: '#fff',
            },
            id: ''
        }
    },

    methods: {
        // 上拉加载
        upCallback(page) {
            apiLuckyDrawWinningList({
                page_size: page.size,
                page_no: page.num,
                status: this.status,
                id: this.id
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
    },
    onLoad() {
        this.id = this.$Route.query.id
        // this.id = 2
    }
}
</script>

<style lang="scss" scoped>
.goods-list {
    margin: 0 20rpx;
    // margin-top: 20rpx;

    .code-item {
        margin-top: 20rpx;
        padding: 26rpx 24rpx;
    }

    .desc {
        width: 550rpx;
    }
}
</style>
