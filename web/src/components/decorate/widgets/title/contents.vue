<template>
    <widget-root :styles="styles">
        <div
            class="title"
            :style="{
                'background-color': styles.bg_color,
                'border-radius': `${styles.border_radius_top}px ${styles.border_radius_top}px ${styles.border_radius_bottom}px ${styles.border_radius_bottom}px`
            }"
        >
            <div class="title-style1 flex-col col-center" v-if="content.style == 1">
                <div
                    class="title-content"
                    :style="{
                        color: styles.title_color,
                        'font-size': `${styles.title_font_size}px`
                    }"
                >
                    {{ content.title }}
                </div>
                <div
                    v-if="!content.hidden_subtitle"
                    class="title-subtitle"
                    :style="{
                        color: styles.subtitle_color,
                        'font-size': `${styles.subtitle_font_size}px`
                    }"
                >
                    {{ content.subtitle }}
                </div>
            </div>
            <div class="title-style2 flex" v-if="content.style == 2">
                <div
                    class="title-content m-r-10"
                    :style="{
                        color: styles.title_color,
                        'font-size': `${styles.title_font_size}px`
                    }"
                >
                    {{ content.title }}
                </div>
                <div class="flex-1">
                    <div
                        v-if="!content.hidden_subtitle"
                        class="title-subtitleline-1"
                        :style="{
                            color: styles.subtitle_color,
                            'font-size': `${styles.subtitle_font_size}px`
                        }"
                    >
                        {{ content.subtitle }}
                    </div>
                </div>
                <div
                    v-if="content.show_more"
                    class="title-more"
                    :style="{
                        color: styles.more_color
                    }"
                >
                    {{ content.more_title }}<i class="el-icon-arrow-right"></i>
                </div>
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
.title {
    padding: 12px 15px;
    .title-content {
        font-size: 16px;
        font-weight: 500;
    }
    .title-subtitle {
        font-size: 12px;
    }
    .title-style1 {
        text-align: center;
        .title-content {
            display: inline-flex;
            align-items: center;
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
    .title-more {
        font-size: 12px;
    }
}
</style>
