<template>
    <view class="style-three">
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

        <view class="content-warp">
            <goods
                v-if="height"
                :scroll-height="$px2rpx(height - 124)"
                goods-style="square"
                class="goods-wrap"
            />
        </view>
    </view>
</template>

<script type="text/javascript">
import cList from './list.vue'
import category from './category.js'
import goods from './goods.vue'
import tabs from './tabs.vue'
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins.js'

export default {
    mixins: [category, MescrollMixin],
    components: {
        cList,
        goods,
        tabs
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
            this.parent.selectIndex = index
        }
    },
    computed: {},
    created() {}
}
</script>
<style lang="scss" scoped>
.style-three {
    width: 100%;
}
</style>
