<template>
    <div class="decorate-user flex">
        <decorate-phone :disabledDrag="true">
            <div slot="header">
                <img style="width: 100%" src="@/assets/images/cart.png" alt="" />
            </div>
        </decorate-phone>
        <decorate-attr :config="{ setBg: false }" />
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
export default class DecorateCart extends Vue {
    /** S data **/
    pagesInfo = {
        name: '购物车',
        type: 4,
        common: {
            title: '购物车',
            background_type: '0',
            bg_color: '#F5F5F5',
            background_image: ''
        }
    }
    pageData = [
        {
            title: '为您推荐',
            name: 'goodsrecom',
            show: 1,
            operate: ['hidden'],
            content: {
                style: 1,
                header_title: '为您推荐',
                show_title: 1,
                show_price: 1,
                show_scribing_price: 1,
                show_btn: 1,
                btn_text: '购买',
                btn_bg_type: 1,
                data: [],
                tips: '根据系统算法，推荐购物车内同分类的10款商品。优先推荐高销量且排序在前的商品。'
            },
            styles: {
                header_title_color: '#333333',
                header_title_size: 18,
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
                padding_top: 0,
                padding_horizontal: 10,
                padding_bottom: 0,
                border_radius_top: 4,
                border_radius_bottom: 4
            }
        }
    ]
    /** E data **/

    /** S computed **/

    /** E computed **/

    /** S methods **/

    async getPages() {
        this.$store.dispatch('getPages', { type: 4 })
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
