<template>
    <view
        :class="[
            'goods',
            { 'goods--square': shape === 'square' },
            { 'goods--rectangle': shape === 'rectangle' },
            { invalid: disableMsg }
        ]"
        :style="[containStyle]"
    >
        <!-- 商品图片 -->
        <view class="goods-images" :style="[imageStyle]">
            <u-image width="100%" height="100%" :src="image" mode="scaleToFill" />
            <!-- 活动角标（斜丝带样式） -->
            <view v-if="activityBadge" class="goods-activity-badge" :style="{ '--badge-bg': activityBadge.bgColor || '#FF2C3C' }">
                <image v-if="activityBadge.icon" :src="activityBadge.icon" class="badge-icon-img" mode="aspectFit" />
                <text class="badge-label">{{ activityBadge.text }}</text>
            </view>
            <!--  disableMsg == "商品库存不足" ? "库存不足" : "商品下架" -->
            <view class="goods-status" v-if="disableMsg">{{
                TipMsg
            }}</view>
        </view>
        <!-- 商品信息 -->
        <view class="goods-content">
            <!-- 名称 -->
            <view class="goods-name line-2">
                <slot name="name">{{ name }}</slot>
            </view>
            <!-- 价格 -->
            <view class="goods-other">
                <slot>
                    <price
                        :content="price"
                        main-size="42rpx"
                        minor-size="36rpx"
                        :color="themeColor"
                    >
                        <view slot="suffix">
                            <price
                                class="muted sm m-l-12"
                                :line-through="true"
                                :content="minPrice"
                                v-show="Number(minPrice)"
                            />
                        </view>
                    </price>
                </slot>
            </view>
        </view>
    </view>
</template>

<script>
/**
 * @description 商品卡片
 * @property {String} shape 形状：square-正方形; rectangle-长方形 (默认: square)
 * @property {String} image 商品图片
 * @property {String} name 商品名称
 * @property {String|Number} price 商品价格
 * @property {String|Number|Boolean} minPrice 商品划线价格 (默认：false)
 * @property {String} imageStyle 图片样式
 * @property {String} containStyle 卡片样式
 * @property {boolean} diable 商品失效
 * @example <goods-card shape="rectangle" name="Muze" price="100" minPrice="120" />
 */

import { mapGetters } from 'vuex'

export default {
    name: 'GoodsCard',

    props: {
        // square -正方形 | rectangle -长方形
        shape: {
            type: String,
            default: 'square'
        },
        // 商品图片
        image: {
            type: String,
            default: ''
        },
        // 商品名称
        name: {
            type: String,
            default: ''
        },
        // 价格
        price: {
            type: [String, Number],
            default: 0
        },
        // 划线价
        minPrice: {
            type: [String, Number, Boolean],
            default: false
        },

        // 卡片样式
        containStyle: {
            type: Object,
            default: () => ({})
        },

        // 图片样式
        imageStyle: {
            type: Object,
            default: () => ({})
        },
        disableMsg: {
            type: String,
            default: ''
        }
    },
    computed: {
        ...mapGetters(['deductInfo']),
        TipMsg() {
            switch (this.disableMsg) {
                case '商品库存不足':
                    return '库存不足'
                case '商品不能购买':
                    return '商品下架'
                case '超过购买限制':
                    return '不可购买'
                default:
                    return ''
            }
        },
        // 活动角标：从store读取活动信息
        activityBadge() {
            const info = this.deductInfo
            if (!info || !info.active) return null
            const theme = info.theme_config || {}
            return {
                text: theme.badge_text || info.activity_name || '',
                icon: theme.badge_icon || '',
                bgColor: theme.primary_color || '#FF2C3C'
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.goods {
    display: flex;
    border-radius: 7px;
    background-color: #ffffff;
    overflow: hidden;
    position: relative;

    &--square {
        flex-direction: column;
        width: 347rpx;
        height: 510rpx;

        .goods-images {
            width: 345rpx;
            height: 345rpx;
        }

        .goods-content {
            padding: 14rpx;
        }
    }

    &--rectangle {
        flex-direction: row;
        align-items: center;
        height: 250rpx;
        padding: 20rpx;

        .goods-images {
            box-sizing: border-box;
            width: calc(250rpx - 2 * 20rpx);
            height: calc(250rpx - 2 * 20rpx);
            border-radius: 7px;
            overflow: hidden;
            position: relative;
            .goods-status {
                position: absolute;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                color: white;
                font-size: 24rpx;
                text-align: center;
            }
        }

        .goods-content {
            margin-left: 20rpx;
        }
    }

    &-images {
        position: relative;
        overflow: hidden;
    }

    &-activity-badge {
        position: absolute;
        top: 16rpx;
        left: -28rpx;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4rpx 32rpx;
        background-color: var(--badge-bg, #FF2C3C);
        color: #ffffff;
        font-size: 18rpx;
        font-weight: 600;
        line-height: 1.3;
        transform: rotate(-45deg);
        transform-origin: center center;
        box-shadow: 0 2rpx 6rpx rgba(0, 0, 0, 0.2);

        .badge-icon-img {
            width: 20rpx;
            height: 20rpx;
            margin-right: 4rpx;
        }

        .badge-label {
            white-space: nowrap;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }
    }

    &-content {
        box-sizing: border-box;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;

        .goods-other {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }
    }
}

.goods-name {
    font-size: $-font-size-nr;
    color: #333333;
    overflow: hidden;
}
.invalid {
    opacity: 0.7;
}
</style>
