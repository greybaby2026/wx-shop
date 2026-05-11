<template>
    <widget-root :styles="styles">
        <view class="graphic">
            <view class="graphic-list">
                <view
                    class="graphic-item flex-col"
                    v-for="(item, index) in content.data"
                    :key="index"
                    :style="{
                        'border-radius': `${$px2rpx(styles.border_radius_top)}rpx ${$px2rpx(
                            styles.border_radius_top
                        )}rpx ${$px2rpx(styles.border_radius_bottom)}rpx ${$px2rpx(
                            styles.border_radius_bottom
                        )}rpx`
                    }"
                    @tap="handleClick(item.link)"
                >
                    <u-image :src="$getImageUri(item.url)" width="100%" height="280rpx" />
                    <view
                        class="info"
                        :style="{
                            'background-color': item.bg_color
                        }"
                    >
                        <view
                            class="title line-1"
                            :style="{
                                color: item.title_color
                            }"
                            >{{ item.title }}</view
                        >
                        <view
                            class="subtitle line-1"
                            :style="{
                                color: item.subtitle_color
                            }"
                            >{{ item.subtitle }}</view
                        >
                    </view>
                </view>
            </view>
        </view>
    </widget-root>
</template>

<script type="text/javascript">
import { navigateTo } from '@/utils/tools'

import { getNonDuplicateID } from '@/utils/tools'
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
    methods: {
        handleClick(link) {
            navigateTo(link)
        }
    },
    computed: {}
}
</script>

<style lang="scss" scoped>
.graphic {
    .graphic-list {
        display: flex;
        overflow-x: auto;
        .graphic-item {
            overflow: hidden;
            flex: none;
            width: 280rpx;
            background-color: #fff;

            &:not(:last-of-type) {
                margin-right: 20rpx;
            }

            .info {
                width: 100%;
                text-align: center;
                font-size: 30rpx;
                line-height: 1;
                padding: 20rpx 16rpx;

                .title {
                    font-weight: bold;
                    font-size: 32rpx;
                }

                .subtitle {
                    margin-top: 20rpx;
                    font-size: 24rpx;
                }
            }
        }
    }
}
</style>
