<template>
    <div class="area-select">
        <el-cascader :style="{ width }" v-model="areaValue" :options="options"></el-cascader>
    </div>
</template>

<script lang="ts">
import area from '@/utils/area'
import { Component, Prop, Vue } from 'vue-property-decorator'

@Component
export default class AreaSelect extends Vue {
    /** S Props **/
    @Prop() province!: number // 省份ID
    @Prop() city!: number // 城市ID
    @Prop() district!: number // 区域ID
    // 表单宽度
    @Prop({ default: '380px' }) width!: string
    /** E Props **/

    /** S Data **/
    options: Array<object> = area // 地区列表
    /** S Data **/

    /** S Computed **/
    // 更新绑定数据
    get areaValue(): Array<number> {
        return [this.province, this.city, this.district]
    }

    set areaValue(value) {
        this.$emit('update:province', value[0])
        this.$emit('update:city', value[1])
        this.$emit('update:district', value[2])
    }
    /** E Computed **/
}
</script>
