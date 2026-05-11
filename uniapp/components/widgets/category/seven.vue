<template>
    <view class="style-seven">
        <left-aside />
        <view class="right-warp">
            <scroll-view style="height: 100%" :scroll-y="true">
                <view v-if="banner">
                    <w-banner :content="banner.content" :styles="banner.styles" />
                </view>
                <view v-for="(item, index) in lists" :key="index">
                    <view class="item-one" v-if="selectIndex == index">
                        <view class="two-lists" v-if="item.sons">
                            <view
                                class="item-two"
                                v-for="(sitem, sindex) in item.sons"
                                :key="sindex"
                            >
                                <router-link
                                    :to="{
                                        path: '/bundle/pages/category/category',
                                        query: {
                                            id: sitem.id
                                        }
                                    }"
                                >
                                    <view class="title flex row-between">
                                        <text class="name bold sm">{{ sitem.name }}</text>
                                        <u-icon name="arrow-right"></u-icon>
                                    </view>
                                </router-link>
                                <img-list v-if="sitem.sons" :lists="sitem.sons" :line-num="3" />
                            </view>
                        </view>
                    </view>
                </view>
            </scroll-view>
        </view>
    </view>
</template>

<script type="text/javascript">
import leftAside from './left-aside.vue'
import right from './right.vue'
import imgList from './img-list.vue'
import category from './category.js'
export default {
    components: {
        leftAside,
        right,
        imgList
    },
    mixins: [category],
    props: {},
    data() {
        return {}
    },
    watch: {
        parent(val) {
            console.log(val)
        }
    },
    methods: {}
}
</script>
<style lang="scss" scoped>
.style-seven {
    flex: 1;
    min-width: 0;
    min-height: 0;
    /* 需给flex:1的元素加上最小高,否则内容超过会溢出容器 (如:小程序Android真机) */
    display: flex;
    height: 100%;
    width: 750rpx;
    // 右边
    .right-warp {
        flex: 1;
        min-width: 0;

        .item-one {
            padding: 20rpx;
            .item-two {
                background: #fff;
                border-radius: 10rpx;
                &:not(:last-of-type) {
                    margin-bottom: 20rpx;
                }

                .title {
                    padding: 20rpx;
                }
            }
        }
    }
}
</style>
