<template>
    <view class="style-one" style="height: 100%">
        <scroll-view scroll-y="true" class="" scroll-with-animation="true" v-if="lists.length">
            <view
                @click="handleClick(item.id)"
                class="bg-white card flex row-between"
                v-for="(item, index) in lists"
                :key="index"
            >
                <text class="name p-l-10">{{ item.name }}</text>
                <u-icon name="arrow-right"></u-icon>
            </view>
        </scroll-view>
    </view>
</template>

<script type="text/javascript">
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins.js'
import { apiGoodsLists } from '@/api/goods'

import leftAside from './left-aside.vue'
import goods from './goods.vue'
import category from './category.js'
export default {
    mixins: [MescrollMixin, category],
    components: {
        leftAside,
        goods
    },
    props: {},
    data() {
        return {}
    },
    methods: {
        handleClick(id) {
            uni.navigateTo({
                url: '/bundle/pages/category/category?id=' + id
            })
        }
    },
    computed: {},
    created() {}
}
</script>
<style lang="scss" scoped>
.style-one {
    flex: 1;
    min-width: 0;
    min-height: 0;
    /* 需给flex:1的元素加上最小高,否则内容超过会溢出容器 (如:小程序Android真机) */
    display: flex;
    width: 750rpx;
    height: 100%;
    // 右边
    .right-warp {
        flex: 1;
        min-width: 0;
        height: 100%;
    }
    .card {
        margin: 20rpx 20rpx;
        padding: 20rpx 10rpx;
        border-radius: 20rpx;
    }
}
</style>
