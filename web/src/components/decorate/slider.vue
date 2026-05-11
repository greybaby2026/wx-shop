<template>
    <div class="slider flex">
        <el-slider class="flex-1 m-r-10" v-model="size" :min="min" :max="max"></el-slider>

        <el-input
            v-if="setOpacity"
            style="width: 100px"
            v-model="size"
            :min="min"
            :max="max"
            oninput="value = (value.trim() === '') ? '0' : value.replace(/[^0-9]/g, '')"
        >
            <template slot="append">%</template>
        </el-input>
        <el-input-number
            v-else
            style="width: 90px"
            v-model="size"
            controls-position="right"
            :min="min"
            :max="max"
        ></el-input-number>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
@Component
export default class StyleChose extends Vue {
    /** S props **/

    @Prop() value!: number | string
    @Prop({ default: 0 }) min!: number
    @Prop({ default: 40 }) max!: number
    @Prop({ default: false }) setOpacity!: boolean
    /** E props **/

    /** S computed **/

    get size() {
        if (this.setOpacity) {
            return Number(this.value)
        }
        return this.value
    }

    set size(val) {
        if (this.setOpacity) {
            this.$emit('input', Number(val))
            return
        }
        this.$emit('input', val)
    }

    /** E computed **/
}
</script>

<style lang="scss" scoped></style>
