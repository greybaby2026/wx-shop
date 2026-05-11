<template>
    <div class="color-select flex flex-1">
        <el-color-picker v-model="color" :predefine="predefineColors"></el-color-picker>
        <el-input v-model="color" class="m-l-10 m-r-10 flex-1" type="text" readonly> </el-input>
        <el-button type="text" @click="reset">重置</el-button>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
@Component
export default class ColorSelect extends Vue {
    /** S props **/

    @Prop() value!: string
    @Prop({ default: '' }) resetColor!: string

    /** E props **/

    /** S data **/
    predefineColors = ['#FF2C3C', '#f7971e', '#fa444d', '#e0a356', '#2f80ed', '#2ec840']
    /** E data **/

    /** S computed **/

    get color() {
        return this.value
    }

    set color(val) {
        this.$emit('input', val)
    }

    /** E computed **/

    /** S watch **/

    /** E watch **/

    /** S methods **/
    reset() {
        this.color = this.resetColor
    }

    /** E methods **/
}
</script>

<style lang="scss" scoped></style>
