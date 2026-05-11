<template>
    <div class="widget-fixed">
        <widget-package :index="index" :operate="operate">
            <contents :content="content" :styles="styles" />
        </widget-package>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import Contents from './contents.vue'
import WidgetPackage from '@/components/decorate/widget-package.vue'
@Component({
    components: {
        Contents,
        WidgetPackage
    }
})
export default class Widget extends Vue {
    @Prop() content!: object | any[]
    @Prop() styles!: object | any[]
    @Prop() index!: number
    @Prop() operate!: string[]
}
</script>
<style lang="scss" scoped>
.widget-fixed {
    position: absolute;
    top: 640px;
    right: 0;
    transform: translateY(-50%);
    z-index: 999;
}
</style>
