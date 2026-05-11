<template>
    <div class="add-supplier-category">
        <ls-dialog
            :async="true"
            width="740px"
            top="30vh"
            :title="value.id ? '编辑供应商分类' : '新增供应商分类'"
            confirmButtonText="保存"
            ref="lsDialog"
            @cancel="closeDialog"
            @confirm="handleSave"
        >
            <div>
                <el-form ref="form" :model="value" label-width="120px" size="small">
                    <!-- <el-form-item label="分类编号" required prop="name">
                                <el-input
                                    class="ls-input"
                                    v-model="form.name"
                                    placeholder="请输入分类编号"
                                ></el-input>
                            </el-form-item> -->
                    <el-form-item
                        label="分类名称"
                        required
                        prop="name"
                        :rules="[
                            {
                                required: true,
                                message: '请输入分类名称',
                                trigger: ['blur', 'change']
                            }
                        ]"
                    >
                        <el-input style="width: 380px" v-model="value.name" placeholder="请输入分类名称"></el-input>
                    </el-form-item>
                    <el-form-item label="排序">
                        <el-input style="width: 220px" v-model="value.sort" placeholder=""></el-input>
                        <div class="xs muted">排序值必须为整数；数值越小，越靠前</div>
                    </el-form-item>
                </el-form>
            </div>
        </ls-dialog>
    </div>
</template>

<script lang="ts">
import { apiSupplierCategoryAdd, apiSupplierCategoryEdit } from '@/api/goods'
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
@Component({
    components: {
        LsDialog
    }
})
export default class AddSupplierCategory extends Vue {
    $refs!: { lsDialog: any; form: any }
    @Prop() value: any

    @Watch('value', { deep: true })
    valueChange() {
        this.$nextTick(() => {
            this.$refs.form.clearValidate()
        })
    }
    closeDialog() {
        this.$refs.lsDialog.close()
    }
    openDialog() {
        this.$refs.lsDialog.open()
    }

    handleSave() {
        this.$refs.form.validate((valid: boolean, object: any) => {
            if (valid) {
                const api = this.value.id ? apiSupplierCategoryEdit(this.value) : apiSupplierCategoryAdd(this.value)
                api.then(() => {
                    this.closeDialog()
                    this.$emit('refresh')
                })
            } else {
                return false
            }
        })
    }
}
</script>
