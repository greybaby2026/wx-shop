<template>
    <widget-root :styles="styles">
        <view
            class="container"
            :style="{
                'border-radius': `${$px2rpx(styles.border_radius_top)}rpx ${$px2rpx(
                    styles.border_radius_top
                )}rpx ${$px2rpx(styles.border_radius_bottom)}rpx ${$px2rpx(
                    styles.border_radius_bottom
                )}rpx`
            }"
        >
            <u-parse :html="content.data" :show-with-animation="true"></u-parse>
        </view>
    </widget-root>
</template>
<script>
import { pxTorpx } from '@/utils/tools'
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
            current: 0
        }
    },
    methods: {},
    computed: {},
    watch: {}
}
</script>
<style lang="scss">
.container {
    background-color: white;
    padding: 20rpx;
}
</style>
