<template>
    <widget-root :styles="styles">
        <div class="separate">
            <div
                :style="{
                    'border-color': styles.line_color,
                    'border-style': content.separate
                }"
            ></div>
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
    @Prop() content!: any
    @Prop() styles!: any
}
</script>

<style lang="scss" scoped>
.separate {
    width: 100%;
    div {
        border-width: 1px;
    }
}
</style>
