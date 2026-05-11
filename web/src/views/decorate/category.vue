<template>
    <div class="decorate-category flex">
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
export default class DecorateCategory extends Vue {
    /** S data **/
    pagesInfo = {
        name: '商品分类',
        type: 2,
        common: {
            title: '商品分类',
            background_type: '0',
            bg_color: '#F5F5F5',
            background_image: ''
        }
    }
    pageData = [
        {
            title: '搜索框',
            name: 'search',
            show: 1,
            operate: ['hidden'],
            content: {
                text: '请输入关键字搜索'
            },
            styles: {
                text_align: 'left',
                border_radius: 40,
                icon_color: '#999999',
                text_color: '#999999',
                padding_top: 12,
                padding_horizontal: 15,
                padding_bottom: 12,
                root_bg_color: 'rgba(255, 255, 255, 1)',
                bg_color: '#F5F5F5',
                color: '#999999'
            }
        },
        {
            title: '商品分类',
            name: 'category',
            show: 1,
            operate: ['hidden'],
            content: {
                data: [],
                style: 1
            },
            styles: {
                border_radius: 0
            }
        }
    ]
    /** E data **/

    /** S computed **/

    /** E computed **/

    /** S methods **/

    getPages() {
        this.$store.dispatch('getPages', { type: 2 })
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
.decorate-category {
    height: calc(100vh - #{$--header-height});
}
</style>
