<template>
    <ls-dialog
        class="goods-select"
        title="选择商品"
        width="900px"
        top="20vh"
        ref="dialog"
        @confirm="handleConfirm"
        :disabled="disabled"
    >
        <div class="goods-select__trigger" slot="trigger">
            <slot name="trigger">
                <el-button :disabled="disabled" size="mini" type="primary">选择商品</el-button>
            </slot>
        </div>
        <div class="p-l-20 p-r-20">
            <detail
                v-model="goods"
                :goods="value"
                :limit="limit"
                :type="type"
                :params="params"
                :show-virtual-goods="showVirtualGoods"
            />
        </div>
    </ls-dialog>
</template>

<script lang="ts">
import { Component, Inject, Prop, Vue, Watch } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import Detail from './detail.vue'
@Component({
    components: {
        LsDialog,
        Detail
    }
})
export default class Dialog extends Vue {
    @Prop({ default: () => [] }) value!: any[] | object
    @Prop({ default: 'multiple' }) type!: 'multiple' | 'single'
    @Prop({ default: false }) disabled!: boolean
    @Prop({ default: 50 }) limit!: number
    @Prop({ default: false }) isSpec!: boolean
    @Prop({ default: () => {} }) params!: Record<any, any>
    @Prop({ default: false }) showVirtualGoods?: boolean
    visible = false
    goods = []
    @Watch('value', { immediate: true })
    valueChange(val: any) {
        this.goods = JSON.parse(JSON.stringify(val))
    }

    handleConfirm() {
        this.$emit('input', this.goods)
    }
}
</script>

<style scoped lang="scss"></style>
