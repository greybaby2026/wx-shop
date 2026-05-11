<template>
    <div class="select">
        <div class="flex">
            <g-dialog
                v-model="selectData"
                :type="type"
                :disabled="disabled"
                :limit="limit"
                :is-spec="isSpec"
                :params="params"
                :show-virtual-goods="showVirtualGoods"
            >
                <slot></slot>
            </g-dialog>
            <div class="m-r-20">
                <span class="muted m-l-20">最多添加{{ limit }}件商品</span>
            </div>
            <div class="clear" v-if="selectData.length">
                <el-button size="small" type="text" @click="selectData = []" :disabled="disabled">清空</el-button>
            </div>
        </div>
        <div class="select-content">
            <list v-if="mode == 'list'" v-model="selectData" :disabled="disabled" />
            <g-table
                v-if="mode == 'table'"
                v-model="selectData"
                :is-spec="isSpec"
                :extend="extend"
                :disabled="disabled"
            />
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import GDialog from './dialog.vue'
import List from './list.vue'
import GTable from './table.vue'
@Component({
    components: {
        GDialog,
        List,
        GTable
    }
})
export default class GoodsSelect extends Vue {
    // 数据
    @Prop({ default: () => [] }) value!: any[] | object
    // 多选/单选
    @Prop({ default: 'multiple' }) type!: 'multiple' | 'single'
    // 是否开启
    @Prop({ default: false }) disabled!: boolean
    // 最多选择数量
    @Prop({ default: 50 }) limit!: number
    // 选中的显示模式
    @Prop({ default: 'list' }) mode!: 'table' | 'list'
    // 是否开启多规格
    @Prop({ default: false }) isSpec!: boolean
    // 表格多规格情况下的扩展
    @Prop() extend!: any[]
    @Prop({ default: () => {} }) params!: Record<any, any>
    // 是否展示虚拟商品
    @Prop({ default: false }) showVirtualGoods?: boolean
    get selectData() {
        return this.value
    }
    set selectData(val) {
        this.$emit('input', val)
        this.$emit('change', val)
    }
}
</script>

<style scoped lang="scss"></style>
