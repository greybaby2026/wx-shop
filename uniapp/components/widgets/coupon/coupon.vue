<template>
    <widget-root :styles="styles" v-if="list.length">
        <view class="coupon" :style="{ '--bgcolor': styles.root_bg_color || '#ffffff' }">
            <view class="coupon-style1" v-if="content.style == 1">
                <view class="coupon-item" v-for="(item, index) in list" :key="index">
                    <view class="coupon-con flex" :style="[itemStyle(item)]">
                        <view class="coupon-price flex-col col-center row-center flex-none">
                            <view class="font-size-40 bold line-1" v-if="item.condition_type != 3"
                                >￥<span class="font-size-56 money">{{
                                    parseFloat(item.money)
                                }}</span></view
                            >
                            <view class="font-size-40 bold line-1" v-else
                                ><span class="font-size-56 money">{{
                                    parseFloat(item.discount_ratio)
                                }}</span
                                >折</view
                            >
                            <view class="xs">{{ item.condition }}</view>
                        </view>
                        <view class="coupon-line" :style="[lineStyle(item)]"> </view>
                        <view class="coupon-info flex flex-1">
                            <view class="flex-1">
                                <view style="max-width: 260rpx" class="lg bold line-1"
                                    >{{ item.name }}
                                </view>
                                <view class="xs m-t-10">{{ item.use_type }}</view>
                            </view>
                            <view
                                class="coupon-btn xs flex-none"
                                :style="[btnStyle(item)]"
                                @tap="handleReceive(item)"
                            >
                                {{ item.is_receive ? '已领取' : '领取' }}
                            </view>
                        </view>
                    </view>
                </view>
            </view>
            <view class="coupon-style2 flex" v-if="content.style == 2">
                <view class="coupon-item flex-none" v-for="(item, index) in list" :key="index">
                    <view class="coupon-con flex" :style="[itemStyle(item)]">
                        <view class="coupon-price flex-col col-center row-center flex-none">
                            <view class="font-size-40 bold line-1" v-if="item.condition_type != 3"
                                >￥<span class="font-size-56 money">{{
                                    parseFloat(item.money)
                                }}</span></view
                            >
                            <view class="font-size-40 bold line-1" v-else
                                ><span class="font-size-56 money">{{
                                    parseFloat(item.discount_ratio)
                                }}</span
                                >折</view
                            >
                            <view class="xs">{{ item.condition }}</view>
                        </view>
                        <view class="coupon-line" :style="[lineStyle(item)]"> </view>
                        <view class="coupon-btn" @tap="handleReceive(item)">
                            {{ item.is_receive ? '已领取' : '立即领取' }}
                        </view>
                    </view>
                </view>
            </view>
            <view
                class="coupon-style3"
                v-if="content.style == 3"
                :style="[
                    content.bg_type == 2 ? { 'background-image': `url(${styles.bg_image})` } : {}
                ]"
            >
                <view class="title xxl" :style="{ color: styles.title_color }">{{
                    content.title
                }}</view>
                <view class="coupon-list flex">
                    <view class="coupon-item flex-none" v-for="(item, index) in list" :key="index">
                        <view
                            class="coupon-con flex-col col-center row-center"
                            :style="{
                                'background-color': item.is_available
                                    ? receiveStyle.bgColor
                                    : styles.bg_color
                            }"
                        >
                            <view class="coupon-price">
                                <view
                                    v-if="item.condition_type != 3"
                                    class="font-size-40 bold line-1"
                                    :style="{
                                        color: item.is_available
                                            ? receiveStyle.color
                                            : styles.money_color
                                    }"
                                    >￥<text class="font-size-56 money">{{
                                        parseFloat(item.money)
                                    }}</text></view
                                >
                                <view
                                    v-else
                                    class="font-size-40 bold line-1"
                                    :style="{
                                        color: item.is_available
                                            ? receiveStyle.color
                                            : styles.money_color
                                    }"
                                    ><text class="font-size-56 money">{{
                                        parseFloat(item.discount_ratio)
                                    }}</text
                                    >折</view
                                >
                            </view>
                            <view
                                class="xs"
                                :style="{
                                    color: item.is_available
                                        ? receiveStyle.color
                                        : styles.condition_color
                                }"
                            >
                                {{ item.condition }}</view
                            >
                            <view class="xs m-t-4" :style="{ color: styles.scene_color }"
                                >{{ item.use_type }}
                            </view>
                            <view
                                class="coupon-btn xs flex-none"
                                :style="[btnStyle(item)]"
                                @tap="handleReceive(item)"
                            >
                                {{ item.is_receive ? '已领取' : '立即领取' }}
                            </view>
                        </view>
                    </view>
                </view>
            </view>
        </view>
    </widget-root>
</template>

<script type="text/javascript">
import { apiCouponGet } from '@/api/store'
export default {
    props: {
        content: {
            type: [Object, Array]
        },
        styles: {
            type: [Object, Array]
        }
    },
    data() {
        return {
            receiveStyle: {
                color: '#999999',
                bgColor: '#f7f9fa',
                btnColor: '#FFFFFF',
                btnBgColor: '#D7D7D7'
            }
        }
    },
    methods: {
        handleReceive(item) {
            if (!this.isLogin) return this.$Router.push('/pages/login/login')
            apiCouponGet({
                id: item.id
            }).then(() => {
                this.$emit('refresh')
            })
        }
    },
    computed: {
        list() {
            return this.content.data
        },
        itemStyle() {
            const {
                styles,
                receiveStyle: { color, bgColor }
            } = this
            return (item) => {
                return item.is_available
                    ? {
                          'background-color': bgColor,
                          color: color
                      }
                    : {
                          'background-color': styles.bg_color,
                          color: styles.text_color
                      }
            }
        },
        lineStyle() {
            const {
                styles,
                receiveStyle: { color }
            } = this
            return (item) => {
                const lineColor = item.is_available ? color : styles.text_color
                return {
                    'background-image': `repeating-linear-gradient(${lineColor} 0px, ${lineColor} 8rpx, transparent 8rpx,transparent 16rpx)`
                }
            }
        },
        btnStyle() {
            const {
                styles,
                receiveStyle: { btnColor, btnBgColor }
            } = this
            return (item) => {
                return item.is_available
                    ? {
                          'background-color': btnBgColor,
                          color: btnColor
                      }
                    : {
                          'background-color': styles.btn_bg_color,
                          color: styles.btn_text_color
                      }
            }
        }
    }
}
</script>
<style lang="scss" scoped>
.coupon {
    .coupon-style1,
    .coupon-style2 {
        .coupon-item {
            overflow: hidden;
            height: 150rpx;

            &:not(:last-of-type) {
                margin-bottom: 20rpx;
            }

            .coupon-con {
                height: 100%;
                position: relative;
                border-radius: 10rpx;
                color: #ff2c3c;
                border: 1px solid currentColor;
                background: #fce7e7;

                &::before,
                &::after {
                    content: '';
                    position: absolute;
                    background-color: var(--bgcolor);
                    width: 28rpx;
                    height: 28rpx;
                    border-radius: 50%;
                    top: 50%;
                    left: 0;
                    transform: translate(-50%, -50%);
                    border: 1px solid currentColor;
                    box-sizing: border-box;
                }

                .coupon-price {
                    width: 240rpx;
                    padding: 0 20rpx;

                    .money {
                        line-height: 70rpx;
                    }
                }

                .coupon-line {
                    width: 4rpx;
                    height: 112rpx;
                    background-size: 2rpx 20rpx;
                    background-repeat: repeat-y;
                    background-position: 50%;
                }

                .coupon-info {
                    padding: 0 20rpx 0 30rpx;

                    .coupon-btn {
                        padding: 8rpx 0;
                        border-radius: 28rpx;
                        background: #ff2214;
                        color: #ffffff;
                        width: 128rpx;
                        text-align: center;
                    }
                }
            }
        }
    }

    .coupon-style2 {
        overflow-x: auto;

        .coupon-item {
            &:not(:last-of-type) {
                margin-right: 20rpx;
                margin-bottom: 0;
            }

            .coupon-con {
                &::before {
                    top: 0;
                    left: 242rpx;
                }

                &::after {
                    bottom: -28rpx;
                    left: 242rpx;
                    top: auto;
                }

                .coupon-btn {
                    width: 80rpx;
                    padding: 0 20rpx;
                    text-align: center;
                    white-space: wrap;
                    line-height: 28rpx;
                }
            }
        }
    }

    .coupon-style3 {
        background-image: url('../../../static/images/coupon_wrap_bg.png');
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        padding: 20rpx;
        border-radius: 20rpx;

        .coupon-list {
            margin-top: 28rpx;
            overflow: hidden;
            overflow-x: auto;

            .coupon-item {
                width: 190rpx;
                height: 240rpx;

                &:not(:last-of-type) {
                    margin-right: 20rpx;
                }

                .coupon-con {
                    background: #ffffff;
                    height: 100%;
                    border-radius: 10rpx;

                    .coupon-price {
                        .money {
                            line-height: 70rpx;
                        }
                    }

                    .coupon-btn {
                        padding: 2rpx 0;
                        border-radius: 28rpx;
                        background: #ff2214;
                        color: #ffffff;
                        margin-top: 16rpx;
                        width: 138rpx;
                        text-align: center;
                    }
                }
            }
        }
    }
}
</style>
