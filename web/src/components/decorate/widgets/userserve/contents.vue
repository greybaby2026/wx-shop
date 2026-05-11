<template>
    <widget-root :styles="styles">
        <div
            class="user-serve"
            :style="{
                'background-color': styles.bg_color,
                'border-radius': `${styles.border_radius_top}px ${styles.border_radius_top}px ${styles.border_radius_bottom}px ${styles.border_radius_bottom}px`
            }"
        >
            <div class="serve-title">
                <div class="weight-500">{{ content.text }}</div>
            </div>
            <div class="serve-nav flex flex-wrap">
                <template v-for="(item, index) in content.data">
                    <div v-if="!item.hidden" class="nav-item flex-col col-center" :key="index">
                        <el-image class="nav-icon" :src="$getImageUri(item.url)">
                            <div slot="error" class="image-error muted flex row-center">
                                <i class="el-icon-picture font-size-20"></i>
                            </div>
                        </el-image>
                        <div class="xs">{{ item.name }}</div>
                    </div>
                </template>
            </div>
        </div>
    </widget-root>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import Indicator from '@/components/decorate/indicator.vue'
import WidgetRoot from '@/components/decorate/widget-root.vue'
@Component({
    components: {
        Indicator,
        WidgetRoot
    }
})
export default class Contents extends Vue {
    @Prop() content!: any
    @Prop() styles!: any

    get infoStyle() {
        const { background_image, background_type } = this.content
        if (!background_image || background_type == 1) {
            return {}
        }
        return {
            'background-image': `url(${this.content.background_image})`
        }
    }
}
</script>

<style lang="scss" scoped>
.user-serve {
    .serve-title {
        border-bottom: $--border-base;
        padding: 12px 15px;
    }
    .serve-nav {
        padding: 13px 0 0;
        .nav-item {
            width: 25%;
            margin-bottom: 15px;
            .nav-icon {
                width: 26px;
                height: 26px;
                margin-bottom: 6px;
            }
        }
    }
}
</style>
