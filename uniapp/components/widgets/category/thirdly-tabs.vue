<template>
    <view
        :class="themeName"
        class="sort box"
        style="font-size: 24rpx"
        v-if="thirdlyLists.length && leftIndex !== 0"
    >
        <view class="abs-box over" :style="{ height: showheight + 'rpx' }" @click="handleShow">
            <view class="dowm-box" :style="{ padding: !!showheight ? '15rpx' : 0 }">
                <view
                    v-show="!!showheight"
                    v-for="(item, index) in thirdlyLists"
                    :key="item.id"
                    @click="handleClick(index)"
                >
                    <view class="item-card" :class="{ active: thirdlyIndex == index }">
                        {{ item.name.slice(0, 4) }}
                    </view>
                </view>
            </view>
            <!-- <view style="height: 30%" class="" @click="handleShow"></view> -->
        </view>
        <scroll-view
            style="height: 80rpx; width: 520rpx"
            scroll-x="true"
            scroll-with-animation="true"
            :scroll-left="scrollLeft"
        >
            <view class="flex col-center p-l-10 p-r-10" style="height: 80rpx">
                <view
                    v-for="(item, index) in thirdlyLists"
                    :key="item.id"
                    @click="handleClick(index)"
                >
                    <view class="item-card" :class="{ active: thirdlyIndex == index }">
                        {{ item.name.slice(0, 4) }}
                    </view>
                </view>
            </view>
        </scroll-view>
        <view
            style="width: 50rpx; height: 80rpx"
            class="flex col-center row-center"
            @click="handleShow"
        >
            <u-icon name="arrow-down"></u-icon>
        </view>
    </view>
</template>

<script type="text/javascript">
import category from './category.js'
export default {
    mixins: [category],
    props: {
        goodsStyle: {
            type: String,
            default: 'rectangle'
        },
        scrollheight: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {
            show: 0,
            showheight: 0,
            scrollLeft: 0
        }
    },
    methods: {
        handleClick(index) {
            this.scrollLeft = index * 140 + 'rpx'

            this.parent.thirdlyIndex = index
        },
        handleShow() {
            console.log(this.scrollheight)

            this.showheight = this.showheight == 0 ? this.scrollheight : 0
        }
    },
    computed: {},
    watch: {},
    created() {}
}
</script>

<style lang="scss" scoped>
.sort {
    display: flex;
    align-items: center;
    height: 80rpx;
    background-color: #ffffff;
    .item-card {
        width: 128rpx;
        height: 40rpx;
        border-radius: 20rpx;
        background: #f7f8f9;
        text-align: center;
        font-size: 22rpx;
        margin-right: 10rpx;
        line-height: 40rpx;
    }
}

.box {
    position: relative;
}
.abs-box {
    position: absolute;
    width: 100%;
    height: 0rpx;
    top: 80rpx;
    z-index: 99;
    // transition: height 0.2s ease; /* 设置过渡动画 */
}
.dowm-box {
    padding: 15rpx;
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    background-color: white;
    border-radius: 0 0 15rpx 15rpx;
    .item-card {
        width: 163rpx;
        height: 52rpx;
        border-radius: 5rpx;
        background: #f7f8f9;
        text-align: center;
        font-size: 22rpx;
        margin-right: 10rpx;
        line-height: 52rpx;
        margin-bottom: 10rpx;
    }
}
.over {
    background-color: rgba(0, 0, 0, 0.5); /* 半透明黑色 */
}
.active {
    // @include background_color();

    // background-color: #e8f2ff;
    @include font_color();
}
</style>
