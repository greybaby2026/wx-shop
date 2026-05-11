<template>
    <view class="category-pages" :class="themeName" :style="[pageStyle]">
        <!-- #ifndef  H5 -->
        <u-sticky offset-top="0" h5-nav-height="0" bg-color="transparent">
            <u-navbar
                :is-back="false"
                title="商品分类"
                :title-bold="true"
                :is-fixed="false"
                :border-bottom="false"
                :background="{ background: 'rgba(256,256, 256, 1)' }"
                title-color="#000"
            ></u-navbar>
        </u-sticky>
        <!-- #endif -->
        <view v-for="(item, index) in pagesData" :key="index" :class="[item.name]">
            <template v-if="item.name == 'search'">
                <w-search v-show="item.show" :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'category'">
                <w-category
                    class="category"
                    v-show="item.show"
                    :content="item.content"
                    :styles="item.styles"
                    :height="height"
                    :tabbarHeight="tabbarHeight"
                />
            </template>
        </view>
        <view class="tabbar">
            <tabbar />
        </view>
    </view>
</template>

<script>
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins.js'
import { apiGetPage } from '@/api/store'
import { apiGoodsCategory, apiGoodsLists } from '@/api/goods'
import { getRect } from '@/utils/tools'
import { mapGetters, mapState } from 'vuex'
export default {
    mixins: [MescrollMixin],
    data() {
        return {
            styles: {},
            pagesData: [],
            height: 0,
            goodsList: [],
            title: '',
            tabbarHeight: 0
        }
    },
    onLoad() {
        apiGetPage({
            type: 2
        }).then((res) => {
            const {
                common: { title },
                common,
                content
            } = res
            uni.setNavigationBarTitle({
                title
            })
            this.styles = common
            console.log(content.data)
            this.pagesData = content
            this.$nextTick(() => {
                this.getHeight()
            })
        })
    },
    onShareAppMessage() {
        const { code } = this.userInfo

        const share = {
            path: `/pages/category/category?invite_code=${code}`
        }
        return share
    },
    computed: {
        ...mapGetters(['userInfo']),

        pageStyle() {
            const { background_color, background_image, background_type } = this.styles
            if (background_type == 0 || !background_image || !background_color) {
                return
            }
            const style =
                background_type == 1
                    ? {
                          'background-color': background_color
                      }
                    : {
                          'background-image': `url(${background_image})`
                      }
            return style
        }
    },
    methods: {
        async getHeight() {
            let bottom = 0
            uni.getSystemInfo({
                success(res) {
                    bottom = res.safeAreaInsets.bottom
                }
            })
            const category = await getRect('.category')
            const tabbar = await getRect('.tabbar')
            this.tabbarHeight = tabbar.height + bottom
            this.height = category.height
        },
        upCallback(page) {
            const pageNum = page.num
            const pageSize = page.size

            apiGoodsLists({
                page_no: pageNum,
                page_size: pageSize
            })
                .then(({ lists, page_size, count }) => {
                    // 如果是第一页需手动置空列表
                    if (page.num == 1) this.goodsList = []
                    // 重置列表数据
                    this.goodsList = [...this.goodsList, ...lists]
                    this.mescroll.endBySize(page_size, count)
                })
                .catch(() => {
                    this.mescroll.endErr()
                })
        }
    } // 使用mixin (在main.js注册全局组件)
}
</script>

<style lang="scss">
/*根元素需要有固定的高度*/
page {
    height: 100%;
    box-sizing: border-box;
    // 支付宝小程序,钉钉小程序需添加绝对定位,否则height:100%失效: https://opendocs.alipay.com/mini/framework/acss#%E5%B8%B8%E8%A7%81%E9%97%AE%E9%A2%98
    /* #ifdef MP-ALIPAY || MP-DINGTALK*/
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    /* #endif */
    /*需给父元素设置height:100%*/
    .category-pages {
        height: 100%;
        display: flex;
        flex-direction: column;
        background-repeat: no-repeat;
        background-size: 100% auto;

        /* 中间 */
        .category {
            flex: 1;
            min-width: 0;
            min-height: 0;
            /* 需给flex:1的元素加上最小高,否则内容超过会溢出容器 (如:小程序Android真机) */
            display: flex;
            flex-direction: column;
        }
    }
}
</style>
