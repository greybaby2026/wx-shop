<template>
    <widget-root :styles="styles">
        <div class="graphic">
            <div class="graphic-list">
                <div
                    class="graphic-item flex-col"
                    v-for="(item, index) in content.data"
                    :key="index"
                    :style="{
                        'border-radius': `${styles.border_radius_top}px ${styles.border_radius_top}px ${styles.border_radius_bottom}px ${styles.border_radius_bottom}px`
                    }"
                >
                    <el-image :src="$getImageUri(item.url)" fit="cover" style="width: 100%; height: 140px">
                        <img slot="error" class="image-error" src="@/assets/images/goods_image.png" alt="" />
                    </el-image>
                    <div
                        class="info"
                        :style="{
                            'background-color': item.bg_color
                        }"
                    >
                        <div
                            class="title line-1"
                            :style="{
                                color: item.title_color
                            }"
                        >
                            {{ item.title }}
                        </div>
                        <div
                            class="subtitle line-1"
                            :style="{
                                color: item.subtitle_color
                            }"
                        >
                            {{ item.subtitle }}
                        </div>
                    </div>
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
export default class SearchContents extends Vue {
    @Prop() content!: object | any[]
    @Prop() styles!: object | any[]
}
</script>

<style lang="scss" scoped>
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
