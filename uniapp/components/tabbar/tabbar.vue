<template>
    <view :class="themeName" class="tabbar" v-if="isShow">
        <view
            class="uni-tabbar flex"
            :style="{
                'background-color': styles.bg_color
            }"
        >
            <view class="uni-tabbar__border"></view>
            <view
                class="uni-tabbar__item flex-col row-center col-center"
                v-for="(item, index) in content.data"
                :key="index"
                @tap="changeTab(item.link)"
            >
                <u-badge
                    v-if="showBadge(item.link.path)"
                    :count="cartNum"
                    :offset="[10, 54]"
                    v-show="isLogin"
                />
                <view class="uni-tabbar__icon" v-if="content.style != 3">
                    <image
                        class="icon-image"
                        v-if="item.link.path != currentPath"
                        :src="$getImageUri(item.icon)"
                    >
                    </image>
                    <image class="icon-image" v-else :src="$getImageUri(item.select_icon)"></image>
                </view>
                <view
                    class="uni-tabbar__label"
                    v-if="content.style != 2"
                    :class="{ active: item.link.path == currentPath }"
                    :style="[itemStyle(item.link.path)]"
                >
                    {{ item.name }}
                </view>
            </view>
        </view>
        <view class="tabbar-placeholder"></view>
    </view>
</template>

<script type="text/javascript">
import { currentPage } from '@/utils/tools'
import { mapGetters } from 'vuex'

export default {
    props: {},
    data() {
        return {
            pagePath: '',
            basePages: [
                '/pages/index/index',
                '/pages/category/category',
                '/pages/shop_cart/shop_cart',
                '/pages/user/user'
            ]
        }
    },
    methods: {
        changeTab(link) {
            if (this.basePages.includes(link.path)) {
                uni.switchTab({
                    url: link.path
                })
            } else {
                this.$Router.replaceAll({
                    path: link.path,
                    query: link.params
                })
            }
        }
    },
    computed: {
        ...mapGetters(['isLogin']),

        content() {
            const { tabbar } = this.$store.getters
            return tabbar.content || {}
        },
        styles() {
            const { tabbar } = this.$store.getters
            return tabbar.styles || {}
        },
        itemStyle() {
            return (path) => {
                const { color_type } = this.content
                const { select_color, color } = this.styles
                if (color_type == 1) return
                if (path == this.currentPath) {
                    return {
                        color: select_color
                    }
                }
                return {
                    color
                }
            }
        },
        currentPath() {
            return '/' + currentPage().route
        },
        isShow() {
            return this.content.data
            // &&this.content.data.some((item) => item.link.path == this.currentPath)
        },
        cartNum() {
            return this.$store.state.cart.cartNum
        },
        showBadge() {
            return (path) => this.cartNum && path === '/pages/shop_cart/shop_cart'
        }
    },
    created() {
        this.$store.dispatch('getCartNum')
    }
}
</script>

<style lang="scss" scoped>
.uni-tabbar {
    position: fixed;
    bottom: 0;
    left: 0;
    z-index: 999;
    width: 100%;
    height: 100rpx;
    padding-bottom: calc(env(safe-area-inset-bottom));
    box-sizing: content-box;
    background-color: #fff;

    &__border {
        background-color: rgba(0, 0, 0, 0.33);
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 1px;
        -webkit-transform: scaleY(0.5);
        transform: scaleY(0.5);
    }

    &__item {
        flex: 1;
        height: 100%;
        position: relative;
    }

    &__icon {
        height: 48rpx;

        .icon-image {
            width: 44rpx;
            height: 44rpx;
        }
    }

    &__label {
        font-size: 24rpx;
        color: #666666;

        &.active {
            @include font_color();
        }
    }
}

.tabbar-placeholder {
    width: 100%;
    height: 100rpx;
    box-sizing: content-box;
}
</style>
