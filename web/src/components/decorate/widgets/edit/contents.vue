<template>
    <widget-root :styles="styles">
        <div
            class="preview flex-none"
            :style="{
                'border-radius': `${styles.border_radius_top}px ${styles.border_radius_top}px ${styles.border_radius_bottom}px ${styles.border_radius_bottom}px`
            }"
        >
            <div class="flex-1" style="min-height: 0">
                <div class="p-l-10 p-r-10" v-html="content.data"></div>
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
export default class SearchContents extends Vue {
    @Prop() content!: object | any[]
    @Prop() styles!: object | any
}
</script>

<style lang="scss" scoped>
.preview {
    // width: 360px;
    box-sizing: content-box;
    flex: none;
    // height: 712px;
    // border: 10px solid #f5f8ff;
    // border-radius: 10px;
    min-height: 50px;
    background-color: white;
    display: flex;
    flex-direction: column;
    ::v-deep img {
        max-width: 100%;
        vertical-align: top;
        display: inline;
    }
}
.graphic {
    .graphic-list {
        display: flex;
        .graphic-item {
            overflow: hidden;
            flex: none;
            width: 140px;
            background-color: #fff;
            &:not(:last-of-type) {
                margin-right: 10px;
            }
            .info {
                width: 100%;
                text-align: center;
                font-size: 15px;
                line-height: 1;
                padding: 10px 8px;
                .title {
                    font-weight: bold;
                    font-size: 16px;
                }
                .subtitle {
                    margin-top: 10px;
                    font-size: 12px;
                }
            }
        }
    }
}
</style>
