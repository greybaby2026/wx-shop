<template>
    <view class="">
        <!-- <scroll-view style="height: 100%" scroll-y="true" scroll-with-animation="true">
			<view class="p-t-20 p-l-20 p-r-20">
				<c-list :lists="lists" />
			</view>
		</scroll-view> -->
        <!-- <u-tabs
            :active-color="themeColor"
            :current="selectIndex"
            :list="lists"
            :bar-width="64"
            @change="changeActive"
        >
        </u-tabs> -->
        <tabs
            :categoryLists="lists"
            :height="height"
            :tabbarHeight="tabbarHeight"
            @change="changeActive"
            :selectIndex="selectIndex"
        ></tabs>
        <view class="content-warp style-night">
            <view :style="{ height: $px2rpx(height - 83) + 'rpx' }">
                <secondAside />
            </view>

            <view class="right-warp">
                <view class="">
                    <goods
                        v-if="height"
                        :scroll-height="$px2rpx(height - 124)"
                        class="goods-wrap"
                    />
                </view>
            </view>
        </view>
    </view>
</template>

<script type="text/javascript">
import cList from './list.vue'
import category from './category.js'
import goods from './goods.vue'
import tabs from './tabs.vue'
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins.js'
import { flattenArray } from '@/utils/tools'
import secondAside from './second-aside.vue'

export default {
    mixins: [category, MescrollMixin],
    components: {
        cList,
        goods,
        tabs,
        secondAside
    },
    props: {},
    data() {
        return {
            show: 0,
            showheight: 0,
            scrollLeft: 0
        }
    },
    methods: {
        changeActive(index) {
            this.parent.leftIndex = 0
            this.parent.selectIndex = index
        }
    },
    computed: {
        categoryLists() {
            let arr = []
            this.lists.forEach((item) => {
                if (Array.isArray(item.sons)) {
                    item.sons.forEach((i) => {
                        arr.push(i)
                    })
                }
            })
            return arr
        }
    },
    created() {}
}
</script>
<style lang="scss" scoped>
.style-night {
    width: 100%;
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
