<template>
    <view class="flex col-center bg-white box" style="height: 160rpx" :class="themeName">
        <view class="abs-box" :style="{ height: showheight }">
            <view style="height: 100%" class=".over">
                <view
                    v-show="show"
                    style="height: 70%; border-radius: 0 0 20rpx 20rpx; background-color: white"
                    class="p-20"
                >
                    <view style="height: 100%">
                        <view style="height: 15%" class="flex col-center row-between">
                            <view style="font-weight: 600">全部分类</view>
                            <view
                                style="height: 130rpx"
                                class="flex row-center col-center in-vertical-text"
                                @click="handleShow"
                            >
                                <view style="font-weight: 600"> 收 </view>
                                <view class="m-t-10" style="font-weight: 600">起</view>
                                <u-icon name="arrow-up m-t-5"></u-icon>
                            </view>
                        </view>
                        <scroll-view style="height: 85%" scroll-y="true" :show-scrollbar="false">
                            <view class="flex col-center m-t-20" style="flex-wrap: wrap">
                                <view
                                    v-for="(item, index) in categoryLists"
                                    :key="item.id"
                                    class=""
                                >
                                    <view class="in-item-card">
                                        <u-image
                                            mode="aspectFit"
                                            width="80rpx"
                                            height="80rpx"
                                            shape="circle"
                                            :src="item.image"
                                            :class="{ active: activeIndex == index }"
                                            @click="InchangeActive(index)"
                                        ></u-image>
                                        <view class="m-t-5 item-text" style="font-size: 24rpx">
                                            {{ item.name.slice(0, 4) }}
                                        </view>
                                    </view>
                                </view>
                            </view>
                        </scroll-view>
                    </view>
                </view>
                <view style="height: 30%" class="" @click="handleShow"></view>
            </view>
        </view>
        <scroll-view
            style="height: 160rpx; width: 700rpx"
            scroll-x="true"
            scroll-with-animation="true"
            :scroll-left="scrollLeft"
        >
            <view class="flex col-center" style="height: 160rpx">
                <view
                    v-for="(item, index) in categoryLists"
                    :key="item.id"
                    class=""
                    @click="changeActive(index)"
                >
                    <view class="item-card">
                        <u-image
                            mode="aspectFit"
                            width="70rpx"
                            height="70rpx"
                            shape="circle"
                            :src="item.image"
                            :class="{ active: activeIndex == index }"
                        ></u-image>
                        <view class="item-text m-t-5" style="font-size: 24rpx">
                            {{ item.name.slice(0, 4) }}
                        </view>
                    </view>
                </view>
            </view>
        </scroll-view>

        <view
            class="flex row-center col-center vertical-text"
            @click="handleShow"
            style="font-weight: 600"
        >
            <view> 展 </view>
            <view class="m-t-10">开</view>
            <u-icon name="arrow-down m-t-5"></u-icon>
        </view>
    </view>
</template>

<script type="text/javascript">
export default {
    props: {
        categoryLists: {
            type: Array,
            default: () => []
        },
        height: {
            type: Number,
            default: 0
        },
        tabbarHeight: {
            type: Number,
            default: 0
        },
        selectIndex: {
            type: Number,
            default: 0
        }
    },
    emit: ['change'],

    data() {
        return {
            show: 0,
            showheight: 0,
            scrollLeft: 0,
            activeIndex: 0
        }
    },
    watch: {
        selectIndex: {
            handler(newVal) {
                this.activeIndex = newVal
            },
            immediate: true
        }
    },
    methods: {
        InchangeActive(index) {
            this.scrollLeft = index * 110 - 10 + 'rpx'
            this.handleShow()
            this.activeIndex = index
            this.$emit('change', index)
            // this.parent.selectIndex = index
        },
        changeActive(index) {
            this.scrollLeft = index * 110 - 10 + 'rpx'
            this.activeIndex = index
            this.$emit('change', index)
        },
        handleShow() {
            this.show = this.show == 1 ? 0 : 1
            this.showheight = this.show ? this.height + this.tabbarHeight + 'px' : 0
        }
    },
    computed: {},
    created() {}
}
</script>
<style lang="scss" scoped>
.item-card {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    // padding: 10rpx;
    width: 110rpx;
    height: 140rpx;
}
.in-item-card {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 10rpx;
    width: 142rpx;
    height: 150rpx;
}
.item-text {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.in-vertical-text {
    writing-mode: vertical-rl;
    text-orientation: upright;
    height: 160rpx;
    width: 50rpx;
    font-size: 24rpx;
    // box-shadow: -2px 0 6px #0000000f;
    text-align: center;
}
.vertical-text {
    writing-mode: vertical-rl;
    text-orientation: upright;
    height: 160rpx;
    width: 50rpx;
    font-size: 24rpx;
    box-shadow: -2px 0 6px #0000000f;
    text-align: center;
}
.box {
    position: relative;
}
.abs-box {
    position: absolute;
    width: 100%;
    height: 0rpx;
    top: 0;
    z-index: 9999;
    // transition: height 0.2s ease; /* 设置过渡动画 */
}
.over {
    background-color: rgba(0, 0, 0, 0.5); /* 半透明黑色 */
}
.active {
    border: 2px solid;
    @include border_color();

    border-radius: 50%;
}
</style>
