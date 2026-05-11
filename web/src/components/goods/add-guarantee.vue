<template>
    <div class="add-unit">
        <ls-dialog
            :async="true"
            width="740px"
            top="30vh"
            :title="value.id ? '编辑保障' : '新增保障'"
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
                        label="服务名称"
                        required
                        prop="name"
                        :rules="[
                            {
                                required: true,
                                message: '请输入服务名称',
                                trigger: ['blur', 'change']
                            }
                        ]"
                    >
                        <el-input style="width: 380px" v-model="value.name" placeholder="请输入服务名称"></el-input>
                    </el-form-item>
                    <el-form-item label="说明">
                        <el-input
                            style="width: 380px"
                            v-model="value.content"
                            placeholder="请输入说明"
                            type="textarea"
                            :rows="5"
                        ></el-input>
                    </el-form-item>
                </el-form>
            </div>
        </ls-dialog>
    </div>
</template>

<script lang="ts">
import { apigoodsServiceGuaranteetAdd, apigoodsServiceGuaranteetEdit } from '@/api/goods'
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
@Component({
    components: {
        LsDialog
    }
})
export default class AddGuarantee extends Vue {
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
                const api = this.value.id
                    ? apigoodsServiceGuaranteetEdit(this.value)
                    : apigoodsServiceGuaranteetAdd(this.value)
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
