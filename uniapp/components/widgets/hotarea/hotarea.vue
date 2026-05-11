<template>
    <widget-root :styles="styles">
        <view class="area-contanier">
            <u-image mode="widthFix" :src="$getImageUri(content.data.imgurl)" alt="" />
            <view
                v-for="(item, index) in content.data.areaLists"
                class="areahot"
                :style="{
                    height: item.areaHeight * 2 + 'rpx',
                    width: item.areaWidth * 2 + 'rpx',
                    top: item.areaY * 2 + 'rpx',
                    left: item.areaX * 2 + 'rpx'
                }"
                @click="handleClick(item.url)"
            >
            </view>
        </view>
    </widget-root>
</template>
<script>
import { pxTorpx } from '@/utils/tools'
import { navigateTo } from '@/utils/tools'

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
        handleClick(url) {
            navigateTo(url)
        }
    },
    computed: {},
    watch: {}
}
</script>
<style lang="scss">
.area-contanier {
    position: relative;
    .areahot {
        background-color: skyblue;
        position: absolute;
        opacity: 0;
    }
}
</style>
