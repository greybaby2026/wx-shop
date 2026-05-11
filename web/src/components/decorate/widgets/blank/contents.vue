<template>
    <widget-root :styles="styles">
        <div
            class="blank"
            :style="{
                height: `${styles.height}px`,
                'background-color': styles.bg_color
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
}
</script>

<style lang="scss" scoped>
.blank {
    width: 100%;
    height: 10px;
}
</style>
