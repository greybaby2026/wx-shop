<template>
    <view class="order bg-white">
        <!-- Header -->
        <view class="order-header flex row-between">
            <view class="normal nr flex flex-none">
                <view v-if="data.delivery_type == 2" class="m-r-10">
                    <u-tag text="自提" size="mini" type="success" mode="dark" />
                </view>
                <view v-if="data.order_type == 4" class="m-r-10">
                    <u-tag text="虚拟" size="mini" mode="dark" />
                </view>
                <view v-if="data.order_type == 2" class="m-r-10">
                    <u-tag text="秒杀" size="mini" type="error" mode="dark" />
                </view>
                <view v-if="data.order_type == 3" class="m-r-10">
                    <u-tag text="砍价" size="mini" type="error" mode="dark" />
                </view>
                <view v-if="data.order_type == 1" class="m-r-10">
                    <u-tag text="拼团" size="mini" type="warning" mode="dark" />
                </view>
                <text class="sm"> 订单编号：{{ data.sn }}</text>
            </view>
            <view class="order-status nr m-l-20 flex-none">
                {{ data.order_status_desc }}
            </view>
        </view>

        <!-- Stction -->
        <view class="order-section" @click="toDetail(data.id)">
            <view
                v-for="(item, index) in data.order_goods"
                :key="item.id"
                class="flex col-top m-b-20"
            >
                <view class="image">
                    <u-image :src="item.goods_image" width="160rpx" height="160rpx"></u-image>
                </view>

                <view class="m-l-16 line-2">
                    <!-- 订单名称 -->
                    <view class="m-t-10">
                        <view class="order-name line-2">
                            {{ item.goods_name }}
                        </view>
                    </view>
                    <!-- 商品规格 -->
                    <view class="order-str m-t-10 flex row-between">
                        <span>
                            {{ item.spec_value_str }}
                        </span>
                        <span> x{{ item.goods_num }} </span>
                    </view>
                </view>
            </view>
            <view class="flex row-right">
                <!-- 实付款金额 -->
                <view class="muted flex sm m-t-10">
                    <view>共{{ data.total_num }}件，实付款: </view>
                    <price
                        class="header-content-price"
                        :content="data.order_amount"
                        main-size="30rpx"
                        minor-size="22rpx"
                        color="#FF4141"
                    />
                </view>
            </view>
        </view>

        <!-- Footer -->
        <view class="order-footer flex row-right" v-if="$slots.default">
            <slot></slot>
        </view>
    </view>
</template>

<script>
/**
 * @description 订单管理卡片
 *
 * @example <order-card :data="order" />
 */

export default {
    name: 'GoodsCard',

    props: {
        data: {
            type: Object,
            default: () => {}
        }
    },

    methods: {
        toDetail(id) {
            console.log(this)
            this.$Router.push({
                path: '/pages/order_detail/order_detail',
                query: {
                    id
                }
            })
        }
    }
}
</script>

<style lang="scss" scoped>
.order {
    padding: 0 20rpx;
    margin: 0 20rpx 20rpx;
    border-radius: 10rpx;

    &-header {
        padding: 22rpx 0;

        .order-status {
            color: $-color-primary;
        }
    }

    &-section {
        padding: 25rpx 0;

        border-top: $-solid-border;

        .order-name {
            width: 100%;
            color: $-color-black;
            font-size: $-font-size-nr;
        }

        .order-str {
            color: $-color-muted;
            font-size: $-font-size-sm;
        }

        > view {
            width: 100%;
        }

        .image {
            flex: 0;
        }
    }

    &-footer {
        border-top: $-solid-border;
        padding: 20rpx 0;
    }
}
</style>
