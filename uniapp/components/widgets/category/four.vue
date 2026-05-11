<template>
    <view style="width: 100%" class="style-four">
        <left-aside />
        <view class="right-warp">
            <scroll-view style="height: 100%" scroll-y="true" scroll-with-animation="true">
                <view class="">
                    <goods v-if="height" :scroll-height="$px2rpx(height) - 80" class="goods-wrap" />
                </view>
            </scroll-view>
        </view>
        <!-- <scroll-view style="height: 100%" scroll-y="true" scroll-with-animation="true">
			<img-list :lists="lists" />
		</scroll-view> -->
    </view>
</template>

<script type="text/javascript">
import leftAside from './left-aside.vue'
import goods from './goods.vue'

import imgList from './img-list.vue'
import category from './category.js'
export default {
    mixins: [category],
    components: {
        imgList,
        leftAside,
        goods
    },
    props: {},
    data() {
        return {}
    },
    methods: {},
    computed: {},
    created() {}
}
</script>
<style lang="scss" scoped>
.style-four {
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
    }
}
</style>
