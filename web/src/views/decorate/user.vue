<template>
    <div class="decorate-user flex">
        <decorate-phone :disabledDrag="true" />
        <decorate-attr :config="{ setBg: false }" />
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import DecorateWidget from '@/components/decorate/decorate-widget.vue'
import DecoratePhone from '@/components/decorate/decorate-phone.vue'
import DecorateAttr from '@/components/decorate/decorate-attr.vue'
import { apiThemePageDetail } from '@/api/shop'
@Component({
    components: {
        DecorateWidget,
        DecoratePhone,
        DecorateAttr
    }
})
export default class DecorateUser extends Vue {
    /** S data **/
    pagesInfo = {
        name: '个人中心',
        type: 3,
        common: {
            title: '个人中心',
            background_type: '0',
            bg_color: '#F5F5F5',
            background_image: ''
        }
    }
    pageData = [
        {
            title: '会员信息',
            name: 'userinfo',
            show: 1,
            operate: ['hidden'],
            content: {
                style: 1,
                background_image: '',
                background_type: 1,
                avatar_type: 1,
                avatar: '',
                assets: [2, 1, 4, 3],
                show_user_sn: 1,
                show_member: 1
            },
            styles: []
        },
        {
            title: '我的订单',
            name: 'userorder',
            show: 1,
            operate: ['hidden'],
            content: {
                text: '我的订单',
                pay_icon: 'resource/image/adminapi/theme/centre/pay.png',
                pay_name: '待付款',
                delivery_icon: 'resource/image/adminapi/theme/centre/deliver.png',
                delivery_name: '待发货',
                take_icon: 'resource/image/adminapi/theme/centre/take.png',
                take_name: '待收货',
                comment_icon: 'resource/image/adminapi/theme/centre/evaluate.png',
                comment_name: '商品评价',
                sale_icon: 'resource/image/adminapi/theme/centre/aftersale.png',
                sale_name: '售后'
            },
            styles: {
                root_bg_color: '',
                bg_color: '#FFFFFF',
                border_radius_top: 0,
                border_radius_bottom: 0,
                padding_top: 10,
                padding_horizontal: 0,
                padding_bottom: 0
            }
        },
        {
            title: '我的服务',
            name: 'userserve',
            show: 1,
            operate: ['hidden'],
            content: {
                text: '我的服务',
                data: [
                    {
                        url: 'resource/image/adminapi/theme/centre/wallet.png',
                        name: '我的钱包',
                        link: {
                            index: 13,
                            name: '我的钱包',
                            path: '/bundle/pages/user_wallet/user_wallet',
                            params: [],
                            type: 'shop'
                        }
                    },
                    {
                        url: 'resource/image/adminapi/theme/centre/distribution.png',
                        name: '分销推广',
                        link: {
                            index: 17,
                            name: '分销推广',
                            path: '/bundle/pages/user_spread/user_spread',
                            params: [],
                            type: 'shop'
                        }
                    },
                    {
                        url: 'resource/image/adminapi/theme/centre/coupon.png',
                        name: '我的优惠券',
                        link: {
                            index: 11,
                            name: '我的优惠券',
                            path: '/bundle/pages/coupon/coupon',
                            params: [],
                            type: 'shop'
                        }
                    },
                    {
                        url: 'resource/image/adminapi/theme/centre/collect.png',
                        name: '我的收藏',
                        link: {
                            index: 15,
                            name: '我的收藏',
                            path: '/bundle/pages/goods_collects/goods_collects',
                            params: [],
                            type: 'shop'
                        }
                    },
                    {
                        url: 'resource/image/adminapi/theme/centre/address.png',
                        name: '收货地址',
                        link: {
                            index: 7,
                            name: '收货地址',
                            path: '/pages/address/address',
                            params: [],
                            type: 'shop'
                        }
                    },
                    {
                        url: 'resource/image/adminapi/theme/centre/userlevel.png',
                        name: '会员等级',
                        link: {
                            index: 9,
                            name: '会员中心',
                            path: '/bundle/pages/user_vip/user_vip',
                            params: [],
                            type: 'shop'
                        }
                    },
                    {
                        url: 'resource/image/adminapi/theme/centre/service.png',
                        name: '联系客服',
                        link: {
                            index: 9,
                            name: '会员中心',
                            path: '/bundle/pages/user_vip/user_vip',
                            params: [],
                            type: 'shop'
                        }
                    },
                    {
                        url: 'resource/image/adminapi/theme/centre/notice.png',
                        name: '消息中心',
                        link: {
                            index: 14,
                            name: '消息中心',
                            path: '/bundle/pages/message_center/message_center',
                            params: [],
                            type: 'shop'
                        }
                    }
                ]
            },
            styles: {
                root_bg_color: '',
                bg_color: '#FFFFFF',
                border_radius_top: 0,
                border_radius_bottom: 0,
                padding_top: 10,
                padding_horizontal: 0,
                padding_bottom: 0
            }
        },
        {
            title: '为您推荐',
            name: 'goodsrecom',
            show: 1,
            operate: ['hidden'],
            content: {
                style: 2,
                header_title: '为您推荐',
                show_title: 1,
                show_price: 1,
                show_scribing_price: 1,
                show_btn: 1,
                btn_text: '购买',
                btn_bg_type: 1,
                data: [],
                tips: '根据系统算法，推荐用户购买商品的同分类10款商品。优先推荐高销量且排序在前的商品。 如果用户没有购买商品，则按商品销量和排序进行推荐。'
            },
            styles: {
                header_title_color: '#333333',
                header_title_size: 20,
                title_color: '#101010',
                scribing_price_color: '#999999',
                price_color: '#FF2C3C',
                btn_bg_color: '#FF2C3C',
                btn_color: '#FFFFFF',
                btn_border_radius: 30,
                btn_border_color: '',
                root_bg_color: '',
                bg_color: '#FFFFFF',
                margin: 10,
                padding_top: 10,
                padding_horizontal: 10,
                padding_bottom: 10,
                border_radius_top: 4,
                border_radius_bottom: 4,
                margin_top: 0
            }
        }
    ]

    /** E data **/

    /** S computed **/

    /** E computed **/

    /** S methods **/

    async getPages() {
        this.$store.dispatch('getPages', { type: 3 })
        // this.$store.commit('setPagesInfo', this.pagesInfo)
        // this.$store.commit('setPagesData', this.pageData)
    }

    /** E methods **/
    /** S life cycle **/
    created() {
        this.getPages()
    }
    /** E life cycle **/
}
</script>
<style lang="scss" scoped>
.decorate-user {
    height: calc(100vh - #{$--header-height});
}
</style>
