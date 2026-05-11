<template>
    <div class="select">
        <c-dialog v-model="selectData" :disabled="disabled" :level="level">
            <slot name="trigger"></slot>
        </c-dialog>
        <div class="m-t-20">
            <el-form-item label="分类名称" v-if="value.name">
                <div class="ls-del-wrap">
                    <el-input style="width: 220px" v-model="value.name" readonly> </el-input>
                    <i @click="handleDelete" class="el-icon-close ls-icon-del"></i>
                </div>
            </el-form-item>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import CDialog from './dialog.vue'
@Component({
    components: {
        CDialog
    }
})
export default class GoodsSelect extends Vue {
    @Prop({ default: () => [] }) value!: any
    @Prop({ default: false }) disabled!: boolean
    @Prop() level!: number
    get selectData() {
        return this.value
    }
    set selectData(val) {
        this.$emit('input', val)
        this.$emit('change', val)
    }

    handleDelete() {
        this.selectData.name = ''
        this.selectData.id = ''
    }
}
</script>

<style scoped lang="scss"></style>
