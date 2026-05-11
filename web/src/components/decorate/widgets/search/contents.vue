<template>
    <widget-root :styles="styles">
        <div class="search">
            <div
                class="search-content"
                :style="{
                    'background-color': styles.bg_color,
                    'text-align': styles.text_align,
                    'border-radius': `${styles.border_radius}px`
                }"
            >
                <i class="el-icon-search" :style="{ color: styles.icon_color }"></i>
                <span class="search-text" :style="{ color: styles.color }">{{ content.text }}</span>
            </div>
        </div>
    </widget-root>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import WidgetRoot from '@/components/decorate/widget-root.vue'
@Component({
    components: {
        WidgetRoot
    }
})
export default class Contents extends Vue {
    @Prop() content!: object | any[]
    @Prop() styles!: object | any[]
}
</script>

<style lang="scss" scoped>
.search {
    box-sizing: border-box;
    width: 100%;
    .search-content {
        height: 32px;
        line-height: 32px;
        padding: 0 12px;
        .search-text {
            margin-left: 4px;
            background-color: transparent;
        }
    }
}
</style>
