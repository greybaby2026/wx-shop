<template>
    <widget-root :styles="styles" :style="{ 'margin-top': `${styles.margin_top}px` }">
        <div class="title">
            <div
                class="title-content"
                :style="{
                    color: styles.header_title_color,
                    'font-size': `${styles.header_title_size}px`
                }"
            >
                {{ content.header_title }}
            </div>
        </div>
        <goods :content="content" :styles="styles" />
    </widget-root>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import WidgetRoot from '@/components/decorate/widget-root.vue'
import Goods from '../goodsgroup/goods.vue'
@Component({
    components: {
        WidgetRoot,
        Goods
    }
})
export default class Contents extends Vue {
    @Prop() content!: any
    @Prop() styles!: any

    get goods() {
        const { data, goods_type, category } = this.content
        if (goods_type == 2) {
            return [...Array(category.num).keys()].map(() => ({}))
        }
        return data.length ? data : [{}]
    }
    get btnStyle() {
        const { btn_bg_color, btn_color, btn_border_radius, btn_border_color } = this.styles
        const { btn_bg_type } = this.content
        const style = {
            'background-color': btn_bg_color,
            color: btn_color,
            'border-radius': `${btn_border_radius}px`,
            'border-color': btn_border_color
        }
        if (btn_bg_type == 2) {
            delete style['background-color']
        }
        return style
    }
}
</script>

<style lang="scss" scoped>
.title {
    padding: 15px 0;
    text-align: center;
    .title-content {
        display: inline-flex;
        align-items: center;
        font-weight: bold;
        &::before,
        &::after {
            content: '';
            display: inline-block;
            width: 49px;
            height: 1px;
            margin: 0 10px;
            background: #dcdfe6;
        }
    }
}
</style>
