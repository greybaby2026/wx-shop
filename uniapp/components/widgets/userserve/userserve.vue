<template>
    <widget-root :styles="styles">
        <view
            class="user-serve"
            :style="{
                'background-color': styles.bg_color,
                'border-radius': `${$px2rpx(styles.border_radius_top)}rpx ${$px2rpx(
                    styles.border_radius_top
                )}rpx ${$px2rpx(styles.border_radius_bottom)}rpx ${$px2rpx(
                    styles.border_radius_bottom
                )}rpx`
            }"
        >
            <view class="serve-title">
                <view class="bold">{{ content.text }}</view>
            </view>
            <view class="serve-nav flex flex-wrap">
                <view
                    class="nav-item flex-col col-center"
                    v-for="(item, index) in content.data"
                    :key="index"
                    @tap.stop="handleClick(item.link)"
                >
                    <template>
                        <u-image width="52rpx" height="52rpx" :src="$getImageUri(item.url)" />
                        <view class="xs m-t-12">{{ item.name }}</view>
                    </template>
                </view>
            </view>
        </view>
    </widget-root>
</template>

<script type="text/javascript">
import ServiceMixin from '@/mixins/service'
import { mapGetters, mapState } from 'vuex'
import { navigateTo } from '@/utils/tools'

export default {
    mixins: [ServiceMixin],
    ...mapGetters(['isLogin', 'ServiceConfig']),

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
    methods: {
        handleClick(link) {
            navigateTo(link)
        }
    },
    computed: {}
}
</script>

<style lang="scss" scoped>
.user-serve {
    .serve-title {
        border-bottom: $-solid-border;
        padding: 24rpx 30rpx;
    }

    .serve-nav {
        padding: 26rpx 0 0;

        .nav-item {
            width: 25%;
            margin-bottom: 30rpx;
        }
    }
}
.button {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    line-height: normal;
}
</style>
