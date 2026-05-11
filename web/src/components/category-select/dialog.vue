<template>
    <ls-dialog
        class="category-select"
        title="选择分类"
        width="900px"
        top="20vh"
        ref="dialog"
        @confirm="handleConfirm"
        :disabled="disabled"
    >
        <div class="category-select__trigger" slot="trigger">
            <slot name="trigger">
                <el-button :disabled="disabled" size="mini" type="primary">选择分类</el-button>
            </slot>
        </div>
        <div class="p-l-20 p-r-20">
            <detail :level="level" ref="detail" v-model="category" />
        </div>
    </ls-dialog>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import Detail from './detail.vue'
@Component({
    components: {
        LsDialog,
        Detail
    }
})
export default class Dialog extends Vue {
    @Prop({ default: () => ({}) }) value!: any[] | object
    @Prop({ default: false }) disabled!: boolean
    @Prop() level!: number
    category = {}
    @Watch('value', { immediate: true, deep: true })
    valueChange(val: any) {
        this.category = JSON.parse(JSON.stringify(val))
    }
    handleConfirm() {
        this.$emit('input', this.category)
    }
}
</script>

<style scoped lang="scss"></style>
