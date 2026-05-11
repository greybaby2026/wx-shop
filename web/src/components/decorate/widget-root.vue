<template>
    <div
        class="widget-root"
        :style="{
            padding: `${styles.padding_top}px ${styles.padding_horizontal}px ${styles.padding_bottom}px`,
            'background-color': styles.root_bg_color
        }"
    >
        <slot></slot>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
@Component
export default class WidgetRoot extends Vue {
    /** S props **/

    @Prop() styles!: any
    /** E props **/

    /** S computed **/

    /** E computed **/
}
</script>

<style lang="scss" scoped></style>
