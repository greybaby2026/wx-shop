<template>
    <view class="goods bg-white">

        <!-- Stction -->
        <view class="goods-wrap flex" @click="toDetail(data.id)">
            <view class="image">
                <u-image :src="data.image" width="160" height="160"></u-image>
            </view>

            <view class="m-l-16 line-1">
                <!-- 商品名称 -->
                <view class="goods-name line-1 m-t-10">{{ data.name }}</view>
                <!-- 商品价格 -->
                <view class="goods-price primary m-t-10">{{ data.price }}</view>
                <!-- 商品库存销量 -->
                <view class="muted flex row-between xs m-t-10">
                    <view>总库存: {{ data.total_stock }}</view>
                    <view>总销量: {{ data.sales_num }}</view>
                </view>
            </view>
        </view>

        <!-- Footer -->
        <view class="goods-footer flex row-right">
            <slot></slot>
        </view>
    </view>
</template>

<script>

/**
 * @description 商品管理卡片
 * 
 * @example <goods-card :data="goods" />
 */

export default {
    name: 'GoodsCard',

    props: {
        data: {
            type: Object,
            default: () => { },
        },
    },

    methods: {
        toDetail(id) {
            this.$Router.push({
                path: '/pages/goods_detail/goods_detail',
                query: {
                    id
                }
            })
        }
    }
}
</script>

<style lang="scss" scoped>
.goods {
    width: 100%;
    padding: 20rpx;
    margin-top: 20rpx;

    &-wrap {
        width: 100%;

        .goods-name {
            color: #101010;
            font-size: $-font-size-nr;
        }

        .goods-price {
            color: #FF0000;
            font-size: $-font-size-nr;
        }

        >view {
            width: 100%;
        }

        .image {
            flex: 0;
        }
    }

    &-footer {}
}
</style>
