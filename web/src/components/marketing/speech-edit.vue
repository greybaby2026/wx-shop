<template>
    <div class="speech-edit">
        <ls-dialog
            :async="true"
            width="740px"
            top="20vh"
            :title="value.id ? '编辑客服话术' : '新客服话术'"
            confirmButtonText="保存"
            ref="lsDialog"
            @cancel="closeDialog"
            @confirm="handleSave"
        >
            <div>
                <el-form ref="form" :model="value" label-width="120px" size="small">
                    <el-form-item
                        label="话术标题"
                        required
                        prop="title"
                        :rules="[
                            {
                                required: true,
                                message: '请输入话术标题',
                                trigger: ['blur', 'change']
                            }
                        ]"
                    >
                        <el-input style="width: 380px" v-model="value.title" placeholder="请输入话术标题"></el-input>
                    </el-form-item>
                    <el-form-item
                        label="话术内容"
                        required
                        prop="content"
                        :rules="[
                            {
                                required: true,
                                message: '请输入话术内容',
                                trigger: ['blur', 'change']
                            }
                        ]"
                    >
                        <el-input
                            style="width: 380px"
                            type="textarea"
                            v-model="value.content"
                            placeholder="多行输入"
                            :rows="10"
                        ></el-input>
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
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
@Component({
    components: {
        LsDialog
    }
})
export default class SpeechEdit extends Vue {
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
                this.$emit('save', this.value)
            } else {
                return false
            }
        })
    }
}
</script>
