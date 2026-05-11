<template>
    <view class="category-content" :style="{ height: `${height}px` }">
        <one class="wrap" v-if="content.style == 1" />
        <two class="wrap" v-if="content.style == 2" />
        <three class="" v-if="content.style == 3" />
        <four class="wrap" v-if="content.style == 4" />
        <five class="wrap" v-if="content.style == 5" />
        <six class="wrap" v-if="content.style == 6" />
        <seven class="wrap" v-if="content.style == 7" />
        <eight class="wrap" v-if="content.style == 8" />
        <night class="" v-if="content.style == 9" />
        <ten class="" v-if="content.style == 10" />
    </view>
</template>

<script type="text/javascript">
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins.js'
import { apiGoodsCategory, apiGoodsLists } from '@/api/goods'
import one from './one.vue'
import two from './two.vue'
import three from './three.vue'
import four from './four.vue'
import five from './five.vue'
import six from './six.vue'
import seven from './seven.vue'
import eight from './eight.vue'
import night from './night.vue'
import ten from './ten.vue'
export default {
    name: 'category',
    mixins: [MescrollMixin],
    components: {
        one,
        two,
        three,
        four,
        five,
        six,
        seven,
        eight,
        night,
        ten
    },
    props: {
        content: {
            type: [Object, Array]
        },
        styles: {
            type: [Object, Array]
        },
        height: {
            type: Number,
            default: 0
        },
        tabbarHeight: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {
            lists: [],
            selectIndex: -1,
            leftIndex: -1,
            thirdlyIndex: -1
        }
    },
    methods: {
        getLists() {
            apiGoodsCategory().then((res) => {
                this.lists = res.lists
                this.selectIndex = 0
                if (this.content.style == 9 || this.content.style == 10) {
                    this.leftIndex = 0
                }
                if (this.content.style == 10) {
                    this.thirdlyIndex = 0
                }
            })
        }
    },
    computed: {
        banner() {
            const data = this.content.data.filter((item) => {
                const select = this.lists[this.selectIndex]
                if (select && item.category) {
                    return item.category.id == select.id
                }
                return false
            })
            return {
                content: {
                    data
                },
                styles: {
                    border_radius: 3,
                    indicator_align: 'center',
                    indicator_color: '#FF2C3C',
                    indicator_style: 2,
                    padding_horizontal: 10,
                    padding_top: 10
                }
            }
        }
    },

    created() {
        this.getLists()
    }
}
</script>
<style lang="scss" scoped>
.category-content {
    flex: 1;
    min-width: 0;
    min-height: 0;
    /* 需给flex:1的元素加上最小高,否则内容超过会溢出容器 (如:小程序Android真机) */
    display: flex;
    flex-direction: column;
    height: 100%;
    .wrap {
        min-width: 0;
        min-height: 0;
        /* 需给flex:1的元素加上最小高,否则内容超过会溢出容器 (如:小程序Android真机) */
        display: flex;
        flex: 1;
        height: 100%;
    }
}
</style>
