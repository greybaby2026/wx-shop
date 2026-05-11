<template>
    <div
        class="widget-root"
        :style="{
            padding: `${styles.padding_top}px ${styles.padding_horizontal}px ${styles.padding_bottom}px`,
            'background-color': styles.root_bg_color
        }"
    >
        <goods :content="content" :styles="styles" />
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import WidgetRoot from '@/components/decorate/widget-root.vue'
import Goods from './goods.vue'
@Component({
    components: {
        WidgetRoot,
        Goods
    }
})
export default class Contents extends Vue {
    @Prop() content!: any
    @Prop() styles!: any
}
</script>

<style lang="scss" scoped></style>
