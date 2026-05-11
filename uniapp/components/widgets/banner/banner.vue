<template>
    <widget-root :styles="styles" v-if="content.data.length">
        <view
            class="banner"
            :style="{
                'border-radius': `${$px2rpx(styles.border_radius)}rpx`,
                'padding-top': `${(340 / 750) * 100}%`
            }"
        >
            <swiper class="swiper" @change="swiperChange" :autoplay="true">
                <swiper-item
                    v-for="(item, index) in content.data"
                    :key="index"
                    @tap="handleClick(item.link)"
                >
                    <u-image
                        mode="aspectFill"
                        width="100%"
                        height="100%"
                        :border-radius="$px2rpx(styles.border_radius)"
                        :src="$getImageUri(item.url)"
                    >
                    </u-image>
                </swiper-item>
            </swiper>
            <indicator
                :current="current"
                :length="content.data.length"
                :type="styles.indicator_style"
                :align="styles.indicator_align"
                :color="styles.indicator_color"
            />
        </view>
    </widget-root>
</template>

<script type="text/javascript">
import { navigateTo } from '@/utils/tools'

import { getRect } from '@/utils/tools'
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
    methods: {
        swiperChange({ detail }) {
            this.current = detail.current
        },
        handleClick(link) {
            console.log(link)
            navigateTo(link)
        }
    },
    computed: {},
    watch: {}
}
</script>

<style lang="scss" scoped>
.banner {
    height: 0;
    box-sizing: border-box;
    overflow: hidden;
    position: relative;
    transform: translateY(0);

    .swiper {
        position: absolute;
        top: 0;
        width: 100%;
        height: 100%;
    }
}
</style>
