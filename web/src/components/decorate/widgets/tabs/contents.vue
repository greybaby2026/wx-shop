<template>
    <div class="tabs" :style="{ 'background-color': styles.root_bg_color }">
        <widget-root :styles="styles">
            <div
                class="tabs-header flex"
                :style="{
                    'background-color': styles.bg_color,
                    'border-radius': `${styles.border_radius_top}px ${styles.border_radius_top}px ${styles.border_radius_bottom}px ${styles.border_radius_bottom}px`
                }"
            >
                <div
                    class="tabs-header-item"
                    v-for="(item, index) in content.data"
                    :key="index"
                    :style="[itemStyle(index)]"
                >
                    <span>{{ item.name }}</span>
                    <span
                        v-if="content.show_line == 1 && content.active == index"
                        class="tabs-line"
                        :style="{ background: styles.line_color }"
                    ></span>
                </div>
            </div>
        </widget-root>
        <div class="tabs-content">
            <div class="tabs-content-item" v-for="(item, index) in content.data" :key="index">
                <div v-if="content.active == index">
                    <goods :styles="item" :content="item" />
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import Indicator from '@/components/decorate/indicator.vue'
import WidgetRoot from '@/components/decorate/widget-root.vue'
import Goods from '../goodsgroup/contents.vue'
@Component({
    components: {
        Indicator,
        WidgetRoot,
        Goods
    }
})
export default class Contents extends Vue {
    @Prop() content!: any
    @Prop() styles!: any

    get itemStyle() {
        return (index: number) => {
            const {
                styles: { padding, color, active_color, active_bg_color },
                content: { active, has_active_bg }
            } = this
            const style: any = {
                padding: `0 ${padding}px`,
                color: color
            }
            if (active == index) {
                style['background-color'] = has_active_bg ? active_bg_color : ''
                ;(style.color = active_color), (style['font-weight'] = 500)
            }
            return style
        }
    }
}
</script>

<style lang="scss" scoped>
.tabs {
    .tabs-header {
        overflow: hidden;
        .tabs-header-item {
            padding: 0 10px;
            text-align: center;
            font-size: 15px;
            height: 44px;
            line-height: 44px;
            position: relative;
            white-space: nowrap;
            .tabs-line {
                position: absolute;
                left: 20%;
                right: 20%;
                bottom: 0;
                height: 2px;
                background: #ff2c3c;
                z-index: 1;
            }
        }
    }
}
</style>
