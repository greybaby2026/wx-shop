<template>
    <ls-dialog
        class="coupon-select"
        title="选择优惠券"
        width="900px"
        top="20vh"
        ref="dialog"
        @confirm="handleConfirm"
        :disabled="disabled"
    >
        <div class="coupon-select__trigger" slot="trigger">
            <slot name="trigger">
                <el-button :disabled="disabled" size="mini" type="primary">选择优惠券</el-button>
            </slot>
        </div>
        <div class="p-l-20 p-r-20">
            <detail ref="detail" :limit="limit" v-model="coupon" />
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
    @Prop({ default: () => [] }) value!: any[]
    @Prop({ default: false }) disabled!: boolean
    @Prop({ default: 10 }) limit!: number
    coupon = []
    @Watch('value', { immediate: true, deep: true })
    valueChange(val: any) {
        this.coupon = JSON.parse(JSON.stringify(val))
    }
    handleConfirm() {
        this.$emit('input', this.coupon)
    }
}
</script>

<style scoped lang="scss"></style>
