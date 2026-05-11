<template>
    <div class="indicator" :style="{ bottom: `${bottom}px` }">
        <div
            v-if="type == 1 || type == 2"
            class="indicator-content"
            :class="{
                fillet: type == 1,
                circle: type == 2
            }"
            :style="{
                'text-align': align
            }"
        >
            <span
                class="indicator-item active"
                :style="{
                    'background-color': color
                }"
            ></span>
            <span class="indicator-item"></span>
            <span class="indicator-item"></span>
        </div>
        <div
            v-if="type == 3"
            :style="{
                'text-align': align
            }"
        >
            <span class="indicator-number">1/5</span>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
@Component
export default class StyleChose extends Vue {
    /** S props **/

    @Prop({ default: 1 }) type!: number
    @Prop({ default: 'left' }) align!: string
    @Prop({ default: '#FF2C3C' }) color!: string
    @Prop({ default: 10 }) bottom!: number
    /** E props **/

    /** S computed **/

    /** E computed **/
}
</script>

<style lang="scss" scoped>
.indicator {
    position: absolute;
    bottom: 10px;
    z-index: 99;
    width: 100%;
    padding: 0 20px;
    box-sizing: border-box;
    .indicator-item {
        display: inline-block;
        background: rgba(0, 0, 0, 0.3);
        &:not(:last-of-type) {
            margin-right: 5px;
        }
    }

    .fillet {
        .indicator-item {
            width: 12px;
            height: 2px;
        }
    }
    .circle {
        .indicator-item {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }
    }
    .indicator-number {
        display: inline-block;
        min-width: 18px;
        height: 18px;
        padding: 0 6px;
        color: #fff;
        line-height: 18px;
        background: rgba(0, 0, 0, 0.3);
        border-radius: 18px;
        font-size: 12px;
    }
}
</style>
