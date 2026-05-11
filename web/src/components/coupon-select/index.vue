<template>
    <div class="select">
        <c-dialog v-model="selectData" :limit="limit" :disabled="disabled">
            <slot name="trigger"></slot>
        </c-dialog>
        <div class="m-t-20">
            <div class="select-list">
                <draggable v-model="selectData" animation="300">
                    <div class="select-item ls-del-wrap" v-for="(item, index) in selectData" :key="index">
                        <div class="flex m-b-16">
                            <div class="lighter label">名称</div>
                            <div>{{ item.name }}</div>
                        </div>
                        <div class="flex">
                            <div class="lighter label">门槛</div>
                            <div>{{ item.discount_content }}</div>
                        </div>
                        <i @click="handleDelete(index)" class="el-icon-close ls-icon-del"></i>
                    </div>
                </draggable>
            </div>
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
    @Prop({ default: () => [] }) value!: any
    @Prop({ default: false }) disabled!: boolean
    @Prop({ default: 10 }) limit!: number
    get selectData() {
        return this.value
    }
    set selectData(val) {
        this.$emit('input', val)
        this.$emit('change', val)
    }

    handleDelete(index: number) {
        this.selectData.splice(index, 1)
    }
}
</script>

<style scoped lang="scss">
.select {
    .select-list {
        .select-item {
            background: #f9f9f9;
            padding: 16px;
            margin-bottom: 10px;
            cursor: move;
            .label {
                width: 50px;
            }
        }
    }
}
</style>
