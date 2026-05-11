<template>
    <ls-dialog
        ref="dialog"
        width="800px"
        top="20vh"
        :title="`设置${extend.name}价格`"
        @close="handleClose"
        @open="initPrice"
        @confirm="handleConfirm"
    >
        <el-button slot="trigger" size="small" :disabled="disabled">{{ `设置${extend.name}价格` }}</el-button>
        <div class="spec-table">
            <div class="m-b-20">
                <popover-input
                    class="m-r-10"
                    ref="popoverInput"
                    v-for="(item, index) in extend.price"
                    :key="index"
                    @confirm="batchSetting($event, item.key)"
                    :disabled="disabled"
                >
                    <el-button size="small" :disabled="disabled">批量设置{{ item.title }}</el-button>
                </popover-input>
            </div>
            <u-table
                :data="value"
                use-virtual
                size="mini"
                height="400"
                :row-height="50"
                tooltip-effect="dark"
                :border="false"
                @selection-change="handleSelectionChange"
            >
                <u-table-column type="selection" width="60"> </u-table-column>
                <u-table-column label="规格名" prop="spec_value_str"> </u-table-column>

                <u-table-column label="原价">
                    <template slot-scope="scope"> ￥{{ scope.row.sell_price }} </template>
                </u-table-column>
                <u-table-column label="现有库存" prop="stock"> </u-table-column>
                <u-table-column v-for="(item, index) in extend.price" :key="index" :label="item.title">
                    <template slot-scope="scope">
                        <el-input
                            class="m-r-10 m-t-5"
                            :key="index"
                            style="width: 100px"
                            type="number"
                            v-model="price[scope.$index][item.key]"
                            :disabled="disabled"
                        >
                        </el-input>
                    </template>
                </u-table-column>
            </u-table>
        </div>
    </ls-dialog>
</template>

<script lang="ts">
import { Component, Inject, Prop, Vue, Watch } from 'vue-property-decorator'
import PopoverInput from '@/components/popover-input.vue'
import LsDialog from '@/components/ls-dialog.vue'
@Component({
    components: {
        PopoverInput,
        LsDialog
    }
})
export default class Table extends Vue {
    $refs!: { popoverInput: PopoverInput[]; dialog: LsDialog }
    @Prop({ default: false }) disabled!: boolean //是否禁用
    @Prop({ default: () => [] }) value!: any[]
    @Prop({ default: () => [] }) extend!: any
    price: any[] = []
    multipleSelection = []

    @Watch('value', { immediate: true })
    valueChange(val: any) {
        this.initPrice()
    }

    initPrice() {
        this.price = this.value.map(item => {
            const obj: any = {}
            this.extend.price.forEach((kitem: any) => {
                obj[kitem.key] = item[kitem.key] || ''
            })
            return obj
        })
    }

    handleClose() {
        this.price = this.price.map(() => ({}))
        this.$refs.popoverInput.forEach(item => {
            item.close()
        })
    }

    handleConfirm() {
        this.value.forEach((item, index) => {
            this.extend.price.forEach((kitem: any) => {
                this.$set(item, kitem.key, this.price[index][kitem.key])
            })
        })
    }

    batchSetting(value: string, fields: string) {
        this.value.forEach((item, index) => {
            this.multipleSelection.forEach((iten: any) => {
                if (iten === item) {
                    this.$set(iten, fields, value)
                    this.price[index][fields] = value
                }
            })
        })
    }

    handleSelectionChange(val: any) {
        this.multipleSelection = val
    }
}
</script>

<style scoped lang="scss"></style>
