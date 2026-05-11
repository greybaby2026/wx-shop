<template>
    <widget-root :styles="styles">
        <div class="banner">
            <div
                class="banner-content"
                :style="{
                    'padding-top': `${(340 / 750) * 100}%`,
                    'border-radius': `${styles.border_radius}px`
                }"
            >
                <div class="banner-image">
                    <el-image style="width: 100%; height: 100%" :src="getImage" fit="cover">
                        <div slot="error" class="image-error muted flex row-center">
                            <i class="el-icon-picture font-size-40"></i>
                        </div>
                    </el-image>
                </div>
                <indicator
                    :type="styles.indicator_style"
                    :align="styles.indicator_align"
                    :color="styles.indicator_color"
                />
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

    get getImage() {
        const { data } = this.content
        if (Array.isArray(data)) {
            return data[0] ? this.$getImageUri(data[0].url) : ''
        }
        return ''
    }
}
</script>

<style lang="scss" scoped>
.banner-content {
    position: relative;
    height: 0;
    box-sizing: border-box;
    overflow: hidden;

    .banner-image {
        position: absolute;
        top: 0;
        width: 100%;
        height: 100%;
    }
}
</style>
