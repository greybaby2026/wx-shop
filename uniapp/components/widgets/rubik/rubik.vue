<template>
    <widget-root :styles="styles">
        <view class="rubik" :style="{ margin: `-${$px2rpx(styles.margin / 2)}rpx` }">
            <view class="rubik-item1" v-if="content.style == 1">
                <view
                    class="item-image"
                    :style="{ margin: `${$px2rpx(styles.margin / 2)}rpx` }"
                    v-if="imgLists[0] && imgLists[0].url"
                    @click="handleClick(imgLists[0].link)"
                >
                    <u-image
                        mode="widthFix"
                        :show-loading="false"
                        :src="$getImageUri(imgLists[0].url)"
                    ></u-image>
                </view>
            </view>
            <view class="rubik-item2" v-if="content.style == 2">
                <view
                    class="item-image"
                    :style="{ margin: `${$px2rpx(styles.margin / 2)}rpx` }"
                    v-for="(item, index) in imgLists"
                    :key="index"
                    @click="handleClick(item.link)"
                >
                    <u-image
                        mode="widthFix"
                        :show-loading="false"
                        :src="$getImageUri(item.url)"
                        v-if="item && item.url"
                    ></u-image>
                </view>
            </view>
            <view class="rubik-item3" v-if="content.style == 3">
                <view
                    class="item-image"
                    :style="{ margin: `${$px2rpx(styles.margin / 2)}rpx` }"
                    v-for="(item, index) in imgLists"
                    :key="index"
                    @click="handleClick(item.link)"
                >
                    <u-image
                        mode="widthFix"
                        :show-loading="false"
                        :src="$getImageUri(item.url)"
                        v-if="item && item.url"
                    ></u-image>
                </view>
            </view>
            <view class="rubik-item4" v-if="content.style == 4">
                <view
                    class="item-image"
                    :style="{
                        width: '50%',
                        height: '375rpx',
                        top: 0,
                        left: 0
                    }"
                    @click="handleClick(imgLists[0].link)"
                >
                    <u-image
                        height="100%"
                        width="100%"
                        mode="aspectFill"
                        :src="$getImageUri(imgLists[0].url)"
                        v-if="imgLists[0] && imgLists[0].url"
                    ></u-image>
                </view>
                <view
                    class="item-image"
                    :style="{
                        width: '50%',
                        height: '188rpx',
                        top: 0,
                        left: '50%'
                    }"
                    @click="handleClick(imgLists[1].link)"
                >
                    <u-image
                        height="100%"
                        width="100%"
                        mode="aspectFill"
                        :src="$getImageUri(imgLists[1].url)"
                        v-if="imgLists[1] && imgLists[1].url"
                    ></u-image>
                </view>
                <view
                    class="item-image"
                    :style="{
                        width: '50%',
                        height: '188rpx',
                        top: '188rpx',
                        left: '50%'
                    }"
                    @click="handleClick(imgLists[2].link)"
                >
                    <u-image
                        height="100%"
                        width="100%"
                        mode="aspectFill"
                        :src="$getImageUri(imgLists[2].url)"
                        v-if="imgLists[2] && imgLists[2].url"
                    ></u-image>
                </view>
            </view>
            <view class="rubik-item5" v-if="content.style == 5">
                <view
                    class="item-image"
                    v-for="(item, index) in imgLists"
                    :key="index"
                    @click="handleClick(item.link)"
                >
                    <u-image
                        mode="aspectFill"
                        width="100%"
                        height="100%"
                        :src="$getImageUri(item.url)"
                        v-if="item && item.url"
                    >
                    </u-image>
                </view>
            </view>
            <view class="rubik-item6" v-if="content.style == 6">
                <view
                    class="item-image"
                    :style="{
                        width: '100%',
                        height: '188rpx',
                        top: 0,
                        left: 0
                    }"
                    @click="handleClick(imgLists[0].link)"
                >
                    <u-image
                        height="100%"
                        width="100%"
                        mode="aspectFill"
                        :src="$getImageUri(imgLists[0].url)"
                        v-if="imgLists[0] && imgLists[0].url"
                    ></u-image>
                </view>
                <view
                    class="item-image"
                    :style="{
                        width: '50%',
                        height: '188rpx',
                        top: '188rpx',
                        left: 0
                    }"
                    @click="handleClick(imgLists[1].link)"
                >
                    <u-image
                        height="100%"
                        width="100%"
                        mode="aspectFill"
                        :src="$getImageUri(imgLists[1].url)"
                        v-if="imgLists[1] && imgLists[1].url"
                    ></u-image>
                </view>
                <view
                    class="item-image"
                    :style="{
                        width: '50%',
                        height: '188rpx',
                        top: '188rpx',
                        left: '50%'
                    }"
                    @click="handleClick(imgLists[2].link)"
                >
                    <u-image
                        height="100%"
                        width="100%"
                        mode="aspectFill"
                        :src="$getImageUri(imgLists[2].url)"
                        v-if="imgLists[2] && imgLists[2].url"
                    ></u-image>
                </view>
            </view>
        </view>
    </widget-root>
</template>

<script type="text/javascript">
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
        return {}
    },
    methods: {
        handleClick(link) {
            navigateTo(link)
        }
    },
    computed: {
        imgLists() {
            return this.content.data
        }
    }
}
</script>

<style lang="scss" scoped>
.rubik {
    .rubik-item2 {
        display: flex;

        .item-image {
            flex: 1 1 auto;
            width: 50%;
        }
    }

    .rubik-item3 {
        display: flex;

        .item-image {
            flex: 1 1 auto;
            width: 33.3%;
        }
    }

    .rubik-item4,
    .rubik-item6 {
        position: relative;
        width: 100%;
        height: 375rpx;

        .item-image {
            position: absolute;
        }
    }

    .rubik-item5 {
        display: flex;
        flex-wrap: wrap;

        .item-image {
            flex: 1 1 auto;
            width: 50%;
            height: 188rpx;
        }
    }
}
</style>
