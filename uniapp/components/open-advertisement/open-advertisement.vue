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

export default {
    props: {
        value: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            showOpen: false,
            content: {}
        }
    },
    computed: {
        // content() {
        //     const { screen } = this.$store.getters
        //     return screen.content || {}
        // }
    },
    methods: {
        goPage(link) {
            this.$Router.replaceAll({
                path: link.path,
                query: link.params
            })
            this.showOpen = false
        }
    },
    created() {
        apiDecorateConfig().then((res) => {
            this.content = res.screen.content
            if (!Number(this.content.enable)) {
                return
            }
            console.log(Cache.get('OPENIMAGE_ENABLE'))
            if (!Cache.get('OPENIMAGE_ENABLE')) {
                return
            }
            console.log(this.content.image)
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
        })
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
