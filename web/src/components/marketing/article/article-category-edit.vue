<!-- 用户详情·钱包调整 -->
<template>
    <div>
        <div class="ls-dialog__trigger" @click="onTrigger">
            <!-- 触发弹窗 -->
            <slot name="trigger"></slot>
        </div>
        <el-dialog
            coustom-class="ls-dialog__content"
            :title="title"
            :visible="visible"
            :width="width"
            :top="top"
            :modal-append-to-body="false"
            center
            :before-close="close"
            :close-on-click-modal="false"
            @close="close"
        >
            <!-- 弹窗主要内容-->
            <div class="">
                <el-form :rules="formRules" ref="formRef" :model="form" label-width="120px" size="small">
                    <el-form-item label="分类名称" prop="name">
                        <el-input v-model="form.name" placeholder="请输入分类名称"></el-input>
                    </el-form-item>
                    <el-form-item label="排序">
                        <el-input v-model.number="form.sort" placeholder="请输入排序值"></el-input>
                        <div class="muted xs">默认为0，数值越大越排在前面</div>
                    </el-form-item>
                    <el-form-item label="分类状态" prop="is_show">
                        <el-radio-group v-model="form.is_show">
                            <el-radio :label="1">启用</el-radio>
                            <el-radio :label="0">停用</el-radio>
                        </el-radio-group>
                    </el-form-item>
                </el-form>
            </div>

            <!-- 底部弹窗页脚 -->
            <div slot="footer" class="dialog-footer">
                <el-button size="small" @click="close">取消</el-button>
                <el-button size="small" @click="onSubmit" type="primary">确认</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import { apiUserSearchList, apiUserSetUserLabel } from '@/api/user/user'
import { apiArticleCategoryDetail, apiArticleCategoryEdit, apiArticleCategoryAdd } from '@/api/application/article'
@Component({
    components: {}
})
export default class ArticleCategoryEdit extends Vue {
    @Prop() cid?: number
    @Prop({
        default: ''
    })
    title!: string //弹窗标题
    @Prop({
        default: '660px'
    })
    width!: string | number //弹窗的宽度
    @Prop({
        default: '20vh'
    })
    top!: string | number //弹窗的距离顶部位置
    /** S Data **/
    visible = false
    $refs!: {
        formRef: any
    }
    form = {
        name: '', // 分类名称
        is_show: 1, // 是否显示 0-不显示 1-显示
        sort: 0 // 排序值，须大于或等于0
    }

    // 表单验证
    formRules = {
        name: [
            {
                required: true,
                message: '请输入分类名称',
                trigger: 'blur'
            }
        ],
        is_show: [
            {
                required: true,
                message: '请选择分类状态',
                trigger: 'change'
            }
        ]
    }
    /** E Data **/

    // 分类详情
    getCategoryDetail() {
        apiArticleCategoryDetail({
            id: this.cid
        })
            .then((res: any) => {
                this.form = res
            })
            .catch((err: any) => {})
    }

    // 新增分类
    onCategoryAdd() {
        apiArticleCategoryAdd(this.form)
            .then(() => {
                this.$emit('refresh')
                this.close()
            })
            .catch((err: any) => {})
    }

    // 编辑分类
    onCategoryEdit() {
        apiArticleCategoryEdit({
            ...this.form,
            id: this.cid
        })
            .then(() => {
                this.$emit('refresh')
                this.close()
            })
            .catch((err: any) => {})
    }

    // 提交
    onSubmit() {
        this.$refs.formRef.validate((valid: any) => {
            if (!valid) {
                return
            }
            if (this.cid) {
                this.onCategoryEdit()
            } else {
                this.onCategoryAdd()
            }
        })
    }

    // 弹窗打开触发
    onTrigger() {
        this.visible = true

        if (this.cid) {
            this.getCategoryDetail()
        }
    }

    // 关闭弹窗
    close() {
        this.visible = false

        this.form = {
            name: '', // 分类名称
            is_show: 1, // 是否显示 0-不显示 1-显示
            sort: 0 // 排序值，须大于或等于0
        }
    }
    /** E Methods **/

    /** S Life Cycle **/
    /** E Life Cycle **/
}
</script>

<style scoped lang="scss"></style>
