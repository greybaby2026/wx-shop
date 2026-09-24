<template>
    <view>
        <u-popup v-model="showOpen" mode="center">
            <view class="container" @click="goPage(content.link)">
                <u-image
                    v-if="content.image"
                    :src="$getImageUri(content.image)"
                    width="100%"
                    height="100%"
                    mode="aspectFit"
                ></u-image>
            </view>
            <view class="close">
                <u-icon name="close-circle" color="white" @tap="showOpen = false" size="46" />
            </view>
        </u-popup>
    </view>
</template>
<script>
import Cache from '@/utils/cache'
import { apiDecorateConfig } from '@/api/store'
import { mapGetters } from 'vuex'

// ⚠️ 「开屏广告只展示一次」是跨文件的隐式约定，改动任意一处都要一并考虑：
//    1) App.vue 在 onLaunch / onLoad 中把缓存 OPENIMAGE_ENABLE 置为 true（一次冷启动只置一次）；
//    2) 本组件 processAd() 展示完立即把它置为 false → 同一次启动内不会再弹出；
//       具体展示策略由装修配置 show_config 决定（1 = 图片变化才展示；2 = 按 OPENIMAGE_NUMBER 次数）。
//    另：本组件显示状态由内部 showOpen 自控，不需要外部 prop
//    （原 value prop 从未被使用、调用方 pages/index/index.vue 也从未传入，已移除）。
export default {
    data() {
        return {
            showOpen: false,
            content: {}
        }
    },
    computed: {
        ...mapGetters(['screen']),
    },
    created() {
        // 优先使用 store 中已有的装修配置，避免重复请求
        const storeScreen = this.screen && this.screen.content
        if (storeScreen) {
            this.content = storeScreen
            this.processAd()
        } else {
            apiDecorateConfig().then((res) => {
                this.content = res.screen.content
                this.processAd()
            })
        }
    },
    methods: {
        goPage(link) {
            this.$Router.replaceAll({
                path: link.path,
                query: link.params
            })
            this.showOpen = false
        },
        processAd() {
            if (!Number(this.content.enable)) {
                return
            }
            if (!Cache.get('OPENIMAGE_ENABLE')) {
                return
            }
            switch (this.content.show_config) {
                case '1':
                    if (Cache.get('OPENIMAGE') !== this.content.image) {
                        this.showOpen = true
                    }
                    Cache.set('OPENIMAGE', this.content.image)
                    break
                case '2':
                    const number = Cache.get('OPENIMAGE_NUMBER')
                    if (!Cache.get('OPENIMAGE_NUMBER')) {
                        this.showOpen = true
                        Cache.set(
                            'OPENIMAGE_NUMBER',
                            this.content.show_config_number,
                            1000 * 60 * 60 * 24
                        )
                    } else if (Cache.get('OPENIMAGE_NUMBER') > 1) {
                        this.showOpen = true
                        Cache.set('OPENIMAGE_NUMBER', number - 1, 1000 * 60 * 60 * 24)
                    }
                    break
            }
            Cache.set('OPENIMAGE_ENABLE', false)
        }
    }
}
</script>
<style scoped>
.container {
    width: 558rpx;
    height: 708rpx;

    margin-bottom: 20rpx;
}
.close {
    width: 100%;
    text-align: center;
}
</style>
