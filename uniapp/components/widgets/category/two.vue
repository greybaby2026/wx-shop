<template>
    <view class="style-two bg-white" style="height: 100%">
        <!-- <u-tabs :active-color="themeColor" :current="selectIndex" :list="lists"  :bar-width="64" @change="changeActive">
		</u-tabs> -->
        <!-- <view class="content-warp">
            <goods
                v-if="height"
                :scroll-height="$px2rpx(height) - 80"
                goods-style="square"
                class="goods-wrap"
            />
        </view> -->
        <scroll-view
            scroll-y="true"
            scroll-with-animation="true"
            v-if="lists.length"
            style="height: 100%"
        >
            <view class="flex" style="flex-wrap: wrap">
                <view
                    @click="handleClick(item.id)"
                    v-for="(item, index) in lists"
                    :key="index"
                    class="card"
                >
                    <u-image
                        mode="aspectFit"
                        width="130rpx"
                        height="130rpx"
                        :src="item.image"
                    ></u-image>
                    <text class="m-t-10">{{ item.name }}</text>
                </view>
            </view>
        </scroll-view>
    </view>
</template>

<script type="text/javascript">
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins.js'
import goods from './goods.vue'
import category from './category.js'
export default {
    mixins: [MescrollMixin, category],
    components: {
        goods
    },
    props: {},
    data() {
        return {}
    },
    methods: {
        changeActive(index) {
            this.parent.selectIndex = index
        },
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
.style-two {
    flex: 1;
    min-width: 0;
    min-height: 0;
    /* 需给flex:1的元素加上最小高,否则内容超过会溢出容器 (如:小程序Android真机) */
    display: flex;
    flex-direction: column;
    width: 750rpx;
    height: 100%;
    // 右边
    .content-warp {
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
        .goods-wrap {
            flex: 1;
            min-width: 0;
            min-height: 0;
        }
    }
}
.card {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 10rpx;
    width: 187rpx;
    height: 187rpx;
}
</style>
