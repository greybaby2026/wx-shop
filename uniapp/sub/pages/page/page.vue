<template>
    <view class="page" :style="[pageStyle]">
        <view v-for="(item, index) in pagesData" :key="index">
            <lazy-component>
                <template v-if="item.name == 'search' && item.show">
                    <w-search :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'navigation' && item.show">
                    <w-navigation :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'banner' && item.show">
                    <w-banner :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'title' && item.show">
                    <w-title :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'rubik' && item.show">
                    <w-rubik :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'notice' && item.show">
                    <w-notice :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'blank' && item.show">
                    <w-blank :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'separate' && item.show">
                    <w-separate :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'goodsgroup' && item.show">
                    <w-goodsgroup :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'tabs' && item.show">
                    <w-tabs :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'presell' && item.show">
                    <w-presell :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'hotarea' && item.show">
                    <w-hotarea :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'coupon' && item.show">
                    <w-coupon
                        :content="item.content"
                        :styles="item.styles"
                        @refresh="getPageInfo"
                    />
                </template>
                <template v-else-if="item.name == 'seckill' && item.show">
                    <w-seckill :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'spellgroup' && item.show">
                    <w-spellgroup :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'video' && item.show">
                    <w-video :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'graphic' && item.show">
                    <w-graphic :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'hotarea' && item.show">
                    <w-hotarea :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'edit' && item.show">
                    <w-editor :content="item.content" :styles="item.styles" />
                </template>
                <!-- #ifdef MP-WEIXIN -->
                <template v-else-if="item.name == 'mnplive' && item.show">
                    <w-mnplive :content="item.content" :styles="item.styles" />
                </template>
                <!-- #endif -->
                <view slot="skeleton" style="height: 10vh"></view>
            </lazy-component>
        </view>
        <tabbar />
    </view>
</template>
<script>
import { apiMiniPage } from '@/api/store'
export default {
    data() {
        return {
            styles: {},
            pagesData: []
        }
    },
    onLoad() {
        this.getPageInfo()
    },
    methods: {
        getPageInfo() {
            const { id } = this.$Route.query
            apiMiniPage({
                id
            }).then((res) => {
                const {
                    common: { title },
                    common,
                    content
                } = res

                uni.setNavigationBarTitle({
                    title
                })
                this.styles = common
                this.tempData = content.map((item, index) => {
                    return {
                        index,
                        value: item
                    }
                })
                this.splitSetData()
            })
        },
        splitSetData() {
            const item = this.tempData.shift()
            if (!item) return
            this.$set(this.pagesData, item.index, Object.freeze(item.value))
            setTimeout(() => {
                this.splitSetData()
            }, 100)
        }
    },
    computed: {
        pageStyle() {
            const { background_color, background_image, background_type } = this.styles
            if (background_type == 0) return
            const style =
                background_type == 1
                    ? {
                          'background-color': background_color
                      }
                    : {
                          'background-image': `url(${background_image})`
                      }
            return style
        }
    }
}
</script>

<style lang="scss">
.page {
    background-repeat: no-repeat;
    background-size: 100% auto;
    overflow: hidden;
    min-height: calc(100vh - env(safe-area-inset-bottom));
}
</style>
