<template>
    <widget-root :styles="styles">
        <view
            class="user-order"
            :style="{
                'background-color': styles.bg_color,
                'border-radius': `${$px2rpx(styles.border_radius_top)}rpx ${$px2rpx(
                    styles.border_radius_top
                )}rpx ${$px2rpx(styles.border_radius_bottom)}rpx ${$px2rpx(
                    styles.border_radius_bottom
                )}rpx`
            }"
        >
            <view class="order-title flex row-between">
                <view class="bold">{{ content.text }}</view>
                <router-link to="/bundle/pages/user_order/user_order?mode=all">
                    <view class="sm muted">全部订单<u-icon name="arrow-right"></u-icon> </view>
                </router-link>
            </view>
            <view class="order-nav flex">
                <router-link class="nav-item" to="/bundle/pages/user_order/user_order?mode=pay">
                    <view class="flex-col col-center">
                        <view class="badge-wrap">
                            <u-image
                                width="52rpx"
                                height="52rpx"
                                :src="$getImageUri(content.pay_icon)"
                            />
                            <u-badge
                                v-if="userInfo.wait_pay"
                                type="error"
                                :count="userInfo.wait_pay"
                                :offset="[-10, -20]"
                            ></u-badge>
                        </view>
                        <view class="xs m-t-12">{{ content.pay_name }}</view>
                    </view>
                </router-link>
                <router-link
                    class="nav-item"
                    to="/bundle/pages/user_order/user_order?mode=delivery"
                >
                    <view class="flex-col col-center">
                        <view class="badge-wrap">
                            <u-image
                                width="52rpx"
                                height="52rpx"
                                :src="$getImageUri(content.delivery_icon)"
                            />
                            <u-badge
                                v-if="userInfo.wait_delivery"
                                type="error"
                                :count="userInfo.wait_delivery"
                                :offset="[-10, -20]"
                            ></u-badge>
                        </view>
                        <view class="xs m-t-12">{{ content.delivery_name }}</view>
                    </view>
                </router-link>
                <router-link class="nav-item" to="/bundle/pages/user_order/user_order?mode=take">
                    <view class="flex-col col-center">
                        <view class="badge-wrap">
                            <u-image
                                width="52rpx"
                                height="52rpx"
                                :src="$getImageUri(content.take_icon)"
                            />
                            <u-badge
                                v-if="userInfo.wait_take"
                                type="error"
                                :count="userInfo.wait_take"
                                :offset="[-10, -20]"
                            ></u-badge>
                        </view>
                        <view class="xs m-t-12">{{ content.take_name }}</view>
                    </view>
                </router-link>
                <router-link class="nav-item" to="/bundle/pages/goods_comment/goods_comment">
                    <view class="flex-col col-center">
                        <view class="badge-wrap">
                            <u-image
                                width="52rpx"
                                height="52rpx"
                                :src="$getImageUri(content.comment_icon)"
                            />
                            <u-badge
                                v-if="userInfo.wait_comment"
                                type="error"
                                :count="userInfo.wait_comment"
                                :offset="[-10, -20]"
                            ></u-badge>
                        </view>
                        <view class="xs m-t-12">{{ content.comment_name }}</view>
                    </view>
                </router-link>
                <router-link class="nav-item" to="/bundle/pages/after_sale/after_sale">
                    <view class="flex-col col-center">
                        <view class="badge-wrap">
                            <u-image
                                width="52rpx"
                                height="52rpx"
                                :src="$getImageUri(content.sale_icon)"
                            />
                            <u-badge
                                v-if="userInfo.after_sale"
                                type="error"
                                :count="userInfo.after_sale"
                                :offset="[-10, -20]"
                            ></u-badge>
                        </view>
                        <view class="xs m-t-12">{{ content.sale_name }}</view>
                    </view>
                </router-link>
            </view>
        </view>
    </widget-root>
</template>

<script type="text/javascript">
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
        return {}
    },
    methods: {},
    computed: {}
}
</script>

<style lang="scss" scoped>
.user-order {
    .order-title {
        border-bottom: $-solid-border;
        padding: 24rpx 30rpx;
    }

    .order-nav {
        padding: 26rpx 0;

        .nav-item {
            flex: 1;
            .badge-wrap {
                position: relative;
            }
        }
    }
}
</style>
