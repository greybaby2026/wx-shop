<template>
    <div class="select">
        <div class="flex">
            <c-dialog v-model="selectData" :disabled="disabled" :type="type">
                <slot name="trigger"></slot>
            </c-dialog>
            <div class="m-r-20">
                <span class="muted m-l-20">最多添加{{ limit }}件商品</span>
            </div>
            <div class="clear" v-if="selectData.length">
                <el-button size="small" type="text" @click="selectData = []">清空</el-button>
            </div>
        </div>

        <div class="goods-lists">
            <draggable class="flex flex-wrap" v-model="selectData" animation="300">
                <div class="goods-item ls-del-wrap" v-for="(item, index) in selectData" :key="index">
                    <el-image style="width: 100%; height: 100%" fit="cover" :src="item.image"></el-image>
                    <i @click="handleDelete(index)" class="el-icon-close ls-icon-del"></i>
                </div>
            </draggable>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import CDialog from './dialog.vue'
import Draggable from 'vuedraggable'
@Component({
    components: {
        CDialog,
        Draggable
    }
})
export default class GoodsSelect extends Vue {
    @Prop({ default: () => [] }) value!: any[]
    @Prop({ default: false }) disabled!: boolean
    @Prop() type!: string
    @Prop({ default: 10 }) limit!: number
    get selectData() {
        return this.value
    }
    set selectData(val) {
        const newVal = val.splice(0, this.limit)
        this.$emit('input', newVal)
        this.$emit('change', newVal)
    }

    handleDelete(index: number) {
        this.selectData.splice(index, 1)
    }
}
</script>

<style scoped lang="scss">
.select {
    background-color: #f9f9f9;
    padding: 15px 10px;
    .goods-lists {
        .goods-item {
            cursor: move;
            width: 64px;
            height: 64px;
            margin-right: 10px;
            margin-top: 14px;
        }
    }
}
</style>
