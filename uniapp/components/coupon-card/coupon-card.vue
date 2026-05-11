<template>
    <view :class="themeName">
        <view class="coupon-card" @tap="onCardSelf">
            <!-- Aside -->
            <view class="coupon-card__aside">
                <price
                    :content="money"
                    :prec="2"
                    mainSize="60rpx"
                    color="#FFFFFF"
                    v-if="condition_type != 3"
                />
                <price
                    :content="discount_ratio"
                    :prec="2"
                    mainSize="60rpx"
                    color="#FFFFFF"
                    prefix=" "
                    v-if="condition_type == 3"
                >
                    <template #suffix>折</template>
                </price>
                <text class="sm m-t-20">{{ condition }}</text>
            </view>

            <!-- Main -->
            <view class="coupon-card__main">
                <view class="coupon-card__main-section">
                    <text class="md bold m-b-24">{{ name }}</text>
                    <text class="xxs lighter m-b-20">{{ time }}</text>
                    <div>
                        <text class="xxs lighter">{{ scene }}</text>
                        <!-- <text class="xxs lighter" v-if="Number(discount_max_money)"
                            >（最大优惠{{ discount_max_money }}元）</text
                        > -->
                    </div>
                    <div class="xxs lighter" v-if="Number(discount_max_money)">
                        (最大优惠{{ discount_max_money }}元）
                    </div>
                </view>

                <template v-if="mode === 'radio'">
                    <u-checkbox
                        class="u-checkbox"
                        v-model="checked"
                        shape="circle"
                        :name="id"
                        :disabled="false"
                        :active-color="themeColor"
                    />
                </template>

                <template v-if="mode === 'get'">
                    <image
                        class="pendant pendant--receive"
                        v-if="gotten"
                        src="/static/images/coupon_receive.png"
                    />
                </template>
                <template v-if="button">
                    <view
                        :class="[
                            'pendant',
                            'pendant-button',
                            button.disable
                                ? 'pendant-button--disable'
                                : `pendant-button--${button.theme}`
                        ]"
                        @tap="onButton"
                        >{{ button.name }}</view
                    >
                </template>
            </view>
        </view>
        <view class="instructions bg-white" v-if="desc.content != ''">
            <view class="flex row-between" @click="showTips = !showTips" v-if="desc.content != ''">
                <view class="xs normal">{{ desc.title }}</view>
                <u-icon :name="showTips == true ? 'arrow-up' : 'arrow-down'" size="24"></u-icon>
            </view>
            <view class="xs muted m-t-20" v-show="showTips == true">
                <text v-for="item in desc.content" :key="item">{{ item }}</text>
            </view>
        </view>
    </view>
</template>

<script>
/**
 * @description 优惠券卡片
 * @property {String} mode 卡片模式[normal|get|radio] (默认值: normal)
 *   注：radio模式下需要配合u-radio-group标签
 *
 */

export default {
    name: 'CouponCard',

    props: {
        // 卡片模式
        mode: {
            type: String,
            default: 'normal'
        },

        // 优惠券ID
        couponId: {
            type: Number | String,
            default: ''
        },

        // 名称
        name: {
            type: String,
            default: ''
        },

        // 金额
        money: {
            type: String | Number,
            default: 0
        },

        // 使用条件
        condition: {
            type: String,
            default: ''
        },

        // 使用时间
        time: {
            type: String,
            required: true
        },

        // 使用场景
        scene: {
            type: String,
            default: ''
        },

        // 按钮名称
        button: {
            type: Object | Boolean,
            // default: {
            // 	name: '领取',
            // 	theme: 'primary',
            // }
            default: false
        },

        // 是否已得到【get模式】
        gotten: {
            type: Boolean,
            default: false
        },

        // 是否选中【radio模式】
        checked: {
            type: Boolean,
            default: false
        },

        // 使用说明
        desc: {
            type: Object | Boolean,
            default: false
        },
        condition_type: {
            type: Number,
            default: 1
        },
        discount_ratio: {
            type: String,
            default: 0
        },
        discount_max_money: {
            type: String,
            default: 0
        }
    },

    data() {
        return {
            showTips: false
        }
    },

    computed: {
        // 优惠券卡片ID
        id() {
            return this.couponId
        }
    },

    methods: {
        // 领取优惠券
        onButton() {
            if (!this.isLogin) {
                this.$Router.push('/pages/login/login')
                return
            }
            // if (this.button.disable) return
            this.$emit('button', this.couponId)
        },

        // 点击卡片
        onCardSelf() {
            if (this.mode !== 'radio') return
            this.$emit('change', !this.checked)
            this.$emit('update:checked', !this.checked)
        }
    }
}
</script>

<style lang="scss" scoped>
.coupon-card {
    display: flex;
    width: 100%;
    height: 200rpx;

    &__aside {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 200rpx;
        @include background_linear(260deg);
        color: #ffffff;
    }

    &__main {
        position: relative;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-left: 20rpx;
        background-color: #ffffff;

        &-section {
            display: flex;
            flex-direction: column;
        }

        .u-checkbox {
            position: relative;
            &::after {
                position: absolute;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;
                display: block;
                content: '';
                width: 100%;
                height: 100%;
            }
        }

        .pendant {
            position: absolute;
            &-button {
                right: 20rpx;
                bottom: 20rpx;
                padding: 8rpx 40rpx;
                border-radius: 60px;
                font-size: $-font-size-nr;

                &--primary {
                    @include background_color();
                    color: #ffffff;
                }

                &--normal {
                    border-width: 1px;
                    border-style: solid;
                    @include border_color();
                    @include font_color();
                }

                &--muted {
                    border: $-solid-border;
                    color: $-color-muted;
                }

                &--disable {
                    background-color: #f4f4f4;
                    color: $-color-lighter;
                }
            }

            &--receive {
                height: 78rpx;
                width: 100rpx;
                top: 0;
                right: 20rpx;
            }
        }
    }
}

.instructions {
    padding: 14rpx 20rpx;
}
</style>
