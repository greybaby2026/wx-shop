<template>
    <view class="style-eight">
        <left-aside />
        <view class="right-warp">
            <scroll-view style="height: 100%" :scroll-y="true">
                <view>
                    <w-banner v-if="banner" :content="banner.content" :styles="banner.styles" />
                </view>
                <template v-for="(item, index) in lists">
                    <view class="item-one" v-if="selectIndex == index" :key="index">
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
                                <c-list v-if="sitem.sons" :lists="sitem.sons" />
                            </view>
                        </view>
                    </view>
                </template>
            </scroll-view>
        </view>
    </view>
</template>

<script type="text/javascript">
import leftAside from './left-aside.vue'
import cList from './list.vue'
import category from './category.js'
export default {
    mixins: [category],
    components: {
        leftAside,
        cList
    },
    props: {},
    data() {
        return {}
    },
    methods: {}
}
</script>
<style lang="scss" scoped>
.style-eight {
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
