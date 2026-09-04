<template>
    <el-dialog
        :title="isEdit ? '编辑知识' : '添加知识'"
        :visible.sync="visible"
        width="600px"
        :before-close="handleClose"
        :close-on-click-modal="false"
    >
        <el-form ref="formRef" :model="form" :rules="rules" label-width="100px" size="small">
            <el-form-item label="知识标题" prop="title">
                <el-input v-model="form.title" placeholder="请输入知识标题/问题" maxlength="200" show-word-limit />
            </el-form-item>
            <el-form-item label="知识内容" prop="content">
                <el-input
                    v-model="form.content"
                    type="textarea"
                    :rows="8"
                    placeholder="请输入知识内容/答案"
                    maxlength="2000"
                    show-word-limit
                />
            </el-form-item>
            <el-form-item label="分类" prop="category">
                <el-select v-model="form.category" placeholder="请选择分类" style="width: 100%">
                    <el-option
                        v-for="item in categoryOptions"
                        :key="item.value"
                        :label="item.label"
                        :value="item.value"
                    />
                </el-select>
            </el-form-item>
            <el-form-item label="状态" prop="status">
                <el-switch
                    v-model="form.status"
                    :active-value="1"
                    :inactive-value="0"
                    active-text="启用"
                    inactive-text="待审核"
                />
            </el-form-item>
        </el-form>
        <div slot="footer">
            <el-button size="small" @click="handleClose">取 消</el-button>
            <el-button size="small" type="primary" @click="handleSubmit">确 定</el-button>
        </div>
    </el-dialog>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiAiBotKnowledgeAdd, apiAiBotKnowledgeEdit, apiAiBotKnowledgeDetail } from '@/api/application/kefu_ai_bot'

@Component
export default class KnowledgeEdit extends Vue {
    visible = false
    isEdit = false
    editId = 0
    form: any = {
        title: '',
        content: '',
        category: 'general',
        status: 0
    }
    rules = {
        title: [{ required: true, message: '请输入知识标题', trigger: 'blur' }],
        content: [{ required: true, message: '请输入知识内容', trigger: 'blur' }],
        category: [{ required: true, message: '请选择分类', trigger: 'change' }]
    }
    categoryOptions = [
        { label: '商品', value: 'product' },
        { label: '订单', value: 'order' },
        { label: '售后', value: 'after_sale' },
        { label: '分销', value: 'distribution' },
        { label: '物流', value: 'delivery' },
        { label: '通用', value: 'general' }
    ]

    open(id?: number) {
        this.visible = true
        this.resetForm()
        if (id) {
            this.isEdit = true
            this.editId = id
            this.getDetail(id)
        } else {
            this.isEdit = false
            this.editId = 0
        }
    }

    resetForm() {
        this.form = {
            title: '',
            content: '',
            category: 'general',
            status: 0
        }
        this.$nextTick(() => {
            const refs = this.$refs.formRef as HTMLFormElement
            refs && refs.clearValidate()
        })
    }

    getDetail(id: number) {
        apiAiBotKnowledgeDetail({ id }).then((res: any) => {
            this.form = {
                title: res.title || '',
                content: res.content || '',
                category: res.category || 'general',
                status: res.status ?? 0
            }
        })
    }

    handleSubmit() {
        const refs = this.$refs.formRef as HTMLFormElement
        refs.validate((valid: boolean) => {
            if (!valid) return
            const api = this.isEdit ? apiAiBotKnowledgeEdit : apiAiBotKnowledgeAdd
            const params = this.isEdit ? { id: this.editId, ...this.form } : { ...this.form }
            api(params).then(() => {
                this.$message.success(this.isEdit ? '编辑成功' : '添加成功')
                this.visible = false
                this.$emit('refresh')
            })
        })
    }

    handleClose() {
        this.visible = false
    }
}
</script>

<style lang="scss" scoped></style>
