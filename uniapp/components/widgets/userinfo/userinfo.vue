<template>
    <widget-root :styles="styles" :class="themeName">
        <view class="user-info flex-col" :style="[infoStyle]">
            <!-- #ifndef  H5 -->
            <u-sticky offset-top="0" h5-nav-height="0" bg-color="transparent">
                <u-navbar
                    :is-back="false"
                    :title="title"
                    :title-bold="true"
                    :is-fixed="false"
                    :border-bottom="false"
                    :background="{ background: 'rgba(256,256, 256,' + percent + ')' }"
                    :title-color="percent > 0.5 ? '#000' : '#fff'"
                ></u-navbar>
            </u-sticky>
            <!-- #endif -->
            <view class="user-top flex-1">
                <view class="flex" v-if="isLogin">
                    <router-link to="/bundle/pages/user_profile/user_profile">
                        <image class="icon-avatar" :src="userInfo.avatar" />
                    </router-link>
                    <view class="m-l-30 flex-1">
                        <view class="xxl white" v-if="userInfo.nickname">
                            {{ userInfo.nickname }}
                        </view>
                        <view
                            class="flex user-id m-t-10"
                            v-if="userInfo.sn && content.show_user_sn"
                        >
                            <view class="xs m-r-20 white">会员ID: {{ userInfo.sn }}</view>
                            <view class="xs copy-btn m-l-5" @tap.stop="$copy(userInfo.sn)"
                                >复制</view
                            >
                        </view>
                    </view>
                </view>
                <router-link v-else to="/pages/login/login">
                    <view class="flex white">
                        <image
                            class="icon-avatar"
                            :src="
                                content.avatar_type == 2 && content.avatar
                                    ? $getImageUri(content.avatar)
                                    : require('@/static/images/icon_portrait.png')
                            "
                            mode=""
                        />
                        <view class="m-l-20 flex-1">
                            <view class="xxl"> 点击登录</view>
                            <view class="xs">登录体验更多功能</view>
                        </view>
                    </view>
                </router-link>
            </view>
            <view class="assets-nav white flex">
                <router-link
                    class="flex-1"
                    to="/bundle/pages/user_wallet/user_wallet"
                    v-if="showAssets(1)"
                >
                    <view class="flex-col col-center">
                        <view class="number">{{ userInfo.user_money || 0 }}</view>
                        <view>余额</view>
                    </view>
                </router-link>

                <router-link
                    class="flex-1"
                    to="/bundle/pages/user_sign/user_sign"
                    v-if="showAssets(2)"
                >
                    <view class="flex-col col-center">
                        <view class="number">{{ userInfo.user_integral || 0 }}</view>
                        <view>积分</view>
                    </view>
                </router-link>
                <router-link class="flex-1" to="/bundle/pages/coupon/coupon" v-if="showAssets(3)">
                    <view class="flex-col col-center">
                        <view class="number">{{ userInfo.coupon || 0 }}</view>
                        <view>优惠券</view>
                    </view>
                </router-link>
                <router-link
                    class="flex-1"
                    to="/bundle/pages/goods_collects/goods_collects"
                    v-if="showAssets(4)"
                >
                    <view class="flex-col col-center">
                        <view class="number">{{ userInfo.collect || 0 }}</view>
                        <view>收藏</view>
                    </view>
                </router-link>
            </view>
            <router-link
                class="flex-1"
                to="/bundle/pages/user_vip/user_vip"
                v-if="content.show_member"
            >
                <view class="user-grade flex row-between">
                    <view class="sm">
                        <span class="lg m-r-12 weight-500" style="font-style: italic"
                            >V <span class="xxs">{{ userInfo.level_rank || '' }}</span></span
                        >{{ isLogin ? userInfo.level_name || '' : '登录查看会员权益' }}
                    </view>
                    <view class="xs">了解会员 <u-icon name="arrow-right"></u-icon> </view>
                </view>
            </router-link>
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
        },
        percent: {
            type: Number,
            default: 0
        },
        title: {
            type: String,
            default: '个人中心'
        }
    },
    data() {
        return {}
    },
    methods: {},
    computed: {
        showAssets() {
            return (type) => {
                return this.content.assets.includes(type)
            }
        },
        infoStyle() {
            const { background_image, background_type } = this.content
            if (!background_image || background_type == 1) {
                return {}
            }
            return {
                'background-image': `url(${this.$getImageUri(this.content.background_image)})`
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.user-info {
    @include background_image(90deg, 0%, 100%, '../../../static/images/bg_user.png');
    background-size: 100% auto;
    background-repeat: no-repeat;
    box-sizing: border-box;

    .user-top {
        padding: 0 30rpx;
        // #ifdef  H5
        padding-top: 30rpx;

        // #endif
        .icon-avatar {
            width: 110rpx;
            height: 110rpx;
            border-radius: 50%;
            border: 1px solid #f8f8f8;
        }

        .user-id {
            display: inline-flex;
            border: 1px solid #fff;
            border-radius: 100rpx;
            padding-left: 20rpx;

            .copy-btn {
                padding: 3rpx 20rpx;
                border-radius: 20rpx;
                background: #fff;
            }
        }
    }

    .assets-nav {
        padding: 30rpx 0 20rpx;

        .number {
            font-size: 32rpx;
        }
    }

    .user-grade {
        color: #ffe0a1;
        height: 80rpx;
        background: url('../../../static/images/bg_user_grade.png');
        background-size: 100%;
        padding: 0 20rpx;
        margin: 0 20rpx;
    }
}
</style>
