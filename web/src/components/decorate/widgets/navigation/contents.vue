<template>
    <widget-root :styles="styles">
        <div
            class="navigation"
            :style="{
                'background-color': styles.bg_color,
                'border-radius': `${styles.border_radius_top}px ${styles.border_radius_top}px ${styles.border_radius_bottom}px ${styles.border_radius_bottom}px`
            }"
        >
            <div class="navigation-content flex flex-wrap p-t-16">
                <div
                    v-for="(item, index) in navList"
                    :key="index"
                    class="flex-col col-center m-b-16"
                    :style="{ width: `${100 / styles.nav_line_num}%` }"
                >
                    <el-image
                        class="m-b-8"
                        v-if="content.style == 1"
                        :src="$getImageUri(item.url)"
                        style="width: 40px; height: 40px"
                        :style="{
                            'border-radius': `${styles.img_border_radius}px`
                        }"
                    >
                        <div slot="error" class="image-error muted flex row-center">
                            <i class="el-icon-picture font-size-20"></i>
                        </div>
                    </el-image>
                    <div class="xs" :style="{ color: styles.color }">
                        {{ item.name }}
                    </div>
                </div>
            </div>

            <div class="navigation-indicator" v-if="styles.nav_style == 2">
                <indicator :type="styles.indicator_style" align="center" :color="styles.indicator_color" :bottom="6" />
            </div>
        </div>
    </widget-root>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
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
    @Prop() styles!: object | any
    get navList() {
        const {
            content: { data },
            styles: { nav_line, nav_line_num }
        } = this
        if (!Array.isArray(data)) {
            return []
        }
        return data.slice(0, nav_line * nav_line_num)
    }
}
</script>

<style lang="scss" scoped>
.navigation {
    .el-image {
        border-radius: 0;
    }
    .navigation-indicator {
        position: relative;
        height: 10px;
    }
}
</style>
