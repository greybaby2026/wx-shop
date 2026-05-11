<template>
    <widget-root :styles="styles">
        <div
            class="video"
            :style="[
                {
                    'border-radius': `${styles.border_radius_top}px ${styles.border_radius_top}px ${styles.border_radius_bottom}px ${styles.border_radius_bottom}px`
                },
                videoStyle
            ]"
            :class="{
                style1: content.proportion == 1,
                style2: content.proportion == 2,
                style3: content.proportion == 3
            }"
        ></div>
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
    @Prop() content!: any
    @Prop() styles!: any

    get videoStyle() {
        const { content } = this
        return content.poster
            ? {
                  'background-image': `url(${this.$getImageUri(content.poster)})`
              }
            : {}
    }
}
</script>

<style lang="scss" scoped>
.video {
    background-repeat: no-repeat;
    background-size: cover;
    background-position: 50%;
    background-image: url(../../../../assets/images/default_video.png);
    position: relative;
    &::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url(../../../../assets/images/icon_play.png);
        background-position: 50%;
        background-repeat: no-repeat;
        background-size: 60px;
        z-index: 2;
    }
    &.style1 {
        height: 211px;
    }
    &.style2 {
        height: 281px;
    }
    &.style3 {
        height: 375px;
    }
}
</style>
