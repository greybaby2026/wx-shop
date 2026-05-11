<template>
    <div class="decorate-index flex">
        <decorate-widget client="pc" />
        <decorate-phone client="pc" />
        <decorate-attr client="pc" />
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import DecorateWidget from '@/components/decorate/decorate-widget.vue'
import DecoratePhone from '@/components/decorate/decorate-phone.vue'
import DecorateAttr from '@/components/decorate/decorate-attr.vue'

@Component({
    components: {
        DecorateWidget,
        DecoratePhone,
        DecorateAttr
    }
})
export default class DecorateIndex extends Vue {
    /** S data **/
    id!: any

    pagesInfo = {
        id: 1,
        type: 1,
        name: 'PC商城首页',
        common: {}
    }

    pageData = [
        {
            title: '头部',
            name: 'header',
            show: 1,
            forbid: true,
            operate: ['hidden'],
            content: {
                data: [
                    {
                        url: '',
                        name: '首页',
                        link: {
                            index: 1,
                            name: '商城首页',
                            path: '/',
                            params: [],
                            type: 'shop'
                        }
                    },
                    {
                        url: '',
                        name: '限时秒杀',
                        link: {
                            index: 1,
                            name: '限时秒杀',
                            path: '/seckill',
                            params: [],
                            type: 'marking'
                        }
                    },
                    {
                        url: '',
                        name: '领券中心',
                        link: {
                            index: 1,
                            name: '领券中心',
                            path: '/get_coupons',
                            params: [],
                            type: 'marking'
                        }
                    },
                    {
                        url: '',
                        name: '商城公告',
                        link: {
                            index: 1,
                            name: '商城公告',
                            path: '/news_list',
                            params: [],
                            type: 'shop'
                        }
                    }
                ]
            },
            style: []
        },
        {
            title: '轮播',
            name: 'banner',
            show: 1,
            forbid: true,
            operate: ['hidden'],
            content: {
                data: [
                    {
                        url: 'resource/image/adminapi/theme/pc/banner1.png',
                        link: []
                    },
                    {
                        url: 'resource/image/adminapi/theme/pc/banner2.png',
                        link: []
                    }
                ]
            },
            style: []
        },
        {
            title: '底部',
            name: 'footer',
            show: 1,
            forbid: true,
            operate: ['hidden'],
            content: {
                data: [
                    {
                        url: 'resource/image/adminapi/theme/pc/shop.png',
                        name: '自营商城'
                    },
                    {
                        url: 'resource/image/adminapi/theme/pc/delivery.png',
                        name: '极速配送'
                    },
                    {
                        url: 'resource/image/adminapi/theme/pc/exclusive_service.png',
                        name: '专属客服'
                    },
                    {
                        url: 'resource/image/adminapi/theme/pc/after_sale.png',
                        name: '售后无忧'
                    },
                    {
                        url: 'resource/image/adminapi/theme/pc/guarantee.png',
                        name: '品质保障'
                    }
                ]
            },
            style: []
        },
        {
            title: '固定导航',
            name: 'fixed',
            operate: ['hidden'],
            show: 1,
            forbid: true,
            content: {
                style: 1,
                data: [
                    {
                        type: 'nav',
                        icon: 'resource/image/adminapi/theme/pc/cart.png',
                        select_icon: 'resource/image/adminapi/theme/pc/select_cart.png',
                        name: '购物车',
                        link: {
                            index: 1,
                            name: '购物车',
                            path: '/shop_cart',
                            params: [],
                            type: 'shop'
                        }
                    },
                    {
                        icon: 'resource/image/adminapi/theme/pc/order.png',
                        select_icon: 'resource/image/adminapi/theme/pc/select_order.png',
                        name: '优惠券',
                        type: 'nav',
                        link: {
                            index: 1,
                            name: '我的优惠券',
                            path: '/user/coupons',
                            params: [],
                            type: 'shop'
                        }
                    },
                    {
                        icon: 'resource/image/adminapi/theme/pc/collection.png',
                        select_icon: 'resource/image/adminapi/theme/pc/select_collection.png',
                        name: '我的收藏',
                        type: 'nav',
                        link: {
                            index: 1,
                            name: '我的收藏',
                            path: '/user/collection',
                            params: [],
                            type: 'shop'
                        }
                    },
                    {
                        icon: 'resource/image/adminapi/theme/pc/service.png',
                        select_icon: 'resource/image/adminapi/theme/pc/select_service.png',
                        name: '联系客服',
                        type: 'server',
                        link: []
                    }
                ]
            },
            style: []
        },
        {
            title: '广告位',
            name: 'adv',
            show: 1,
            content: {
                data: [
                    {
                        url: 'resource/image/adminapi/theme/pc/ad1.png',
                        link: []
                    },
                    {
                        url: 'resource/image/adminapi/theme/pc/ad2.png',
                        link: []
                    },
                    {
                        url: 'resource/image/adminapi/theme/pc/ad3.png',
                        link: []
                    },
                    {
                        url: 'resource/image/adminapi/theme/pc/ad4.png ',
                        link: []
                    }
                ],
                style: 4
            },
            styles: []
        },
        {
            title: '限时秒杀',
            name: 'seckill',
            show: 1,
            content: {
                data: [],
                title: '限时秒杀',
                show_more: 1,
                data_type: 1,
                num: 10
            },
            styles: []
        },
        {
            title: '商品组',
            name: 'goodsgroup',
            show: 1,
            content: {
                data: [],
                title: '新品推荐',
                subtitle: '品质好货 件件是精品',
                show_more: 1,
                adv_url: 'resource/image/adminapi/theme/pc/new_goods.png',
                adv_link: [],
                goods_type: 1,
                category: {
                    name: '手机',
                    id: 2,
                    number: 1
                },
                show_adv: 1
            },
            styles: []
        },
        {
            title: '商品组',
            name: 'goodsgroup',
            show: 1,
            content: {
                data: [],
                title: '好物优选',
                subtitle: '精挑细选 挑你喜欢',
                show_more: 1,
                adv_url: '',
                adv_link: [],
                goods_type: 2,
                category: {
                    name: '苹果',
                    id: 5,
                    number: 1
                }
            },
            styles: []
        },
        {
            title: '商品组',
            name: 'goodsgroup',
            show: 1,
            content: {
                data: [],
                title: '热销榜单',
                subtitle: '实时更新热销火爆商品',
                show_more: 1,
                adv_url: 'resource/image/adminapi/theme/pc/hot_goods.png',
                adv_link: [],
                goods_type: 2,
                category: {
                    name: '小米',
                    id: 3,
                    number: 1
                },
                show_adv: 1
            },
            styles: []
        }
    ]

    /** E data **/

    /** S computed **/

    /** E computed **/

    /** S methods **/

    async getPages() {
        // this.$store.commit('setPagesInfo', this.pagesInfo)
        // this.$store.commit('setPagesData', this.pageData)
        this.$store.commit('setClient', 'pc')

        if (this.id) {
            this.$store.dispatch('getPages', { id: this.id })
        }
    }
    /** E methods **/
    /** S life cycle **/
    created() {
        this.id = this.$route.query.id
        document.body.setAttribute('style', 'min-width: 1800px')
        this.getPages()
    }

    beforeDestroy() {
        document.body.setAttribute('style', '')
    }
    /** E life cycle **/
}
</script>
<style lang="scss" scoped>
.decorate-index {
    height: calc(100vh - #{$--header-height});
}
</style>
