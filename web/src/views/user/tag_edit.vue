<!-- 新增/编辑用户标签-->
<template>
    <div class="user-tag-edit">
        <!-- 导航头部 -->
        <div class="ls-card">
            <el-page-header @back="$router.go(-1)" :content="mode === 'add' ? '新增用户标签' : '编辑用户标签'" />
        </div>

        <!-- 主要内容 -->
        <el-form :rules="formRules" ref="formRef" :model="form" label-width="120px" size="small">
            <div class="ls-card m-t-16">
                <div class="card-title">标签信息</div>
                <div class="card-content m-t-24">
                    <el-form-item label="标签名称" prop="name">
                        <el-input v-model="form.name" placeholder="请输入标签名称"></el-input>
                    </el-form-item>
                    <el-form-item label="标签描述" prop="remark">
                        <el-input
                            class="ls-input-textarea"
                            v-model="form.remark"
                            placeholder="请输入标签描述"
                            type="textarea"
                            :rows="3"
                        >
                        </el-input>
                    </el-form-item>
                </div>
            </div>

            <div class="ls-card m-t-16">
                <div class="card-title">标签类型</div>
                <div class="card-content m-t-24">
                    <el-form-item label="标签类型" prop="label_type">
                        <el-radio-group class="m-r-16" v-model="form.label_type">
                            <el-radio class="m-r-16" :label="0">手动标签</el-radio>
                        </el-radio-group>
                        <div class="muted xs m-r-16">手动标签是指手动给用户打上标签</div>
                    </el-form-item>
                </div>
            </div>
        </el-form>

        <!-- 底部保存或取消 -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="onSubmit()">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { PageMode } from '@/utils/type'
import { apiUserLabelDetail, apiUserLabelAdd, apiUserLabelEdit } from '@/api/user/user'
import { LabelAdd_Req, LabelEdit_Req, LabelDetail_Req, LabelDetail_Res } from '@/api/user/user.d.ts'
@Component({
    components: {}
})
export default class TagEdit extends Vue {
    // /** S Data **/
    mode: string = PageMode.ADD // 当前页面【add: 添加用户等级 | edit: 编辑用户等级】
    identity: number | null = null // 当前编辑用户的身份ID  valid: mode = 'edit'
    form = {
        name: '', // 名称
        remark: '', // 描述
        label_type: 0 // 标签类型：0-手动标签；1-自动标签
    }

    formRules = {
        name: [
            {
                required: true,
                message: '请输入标签名称',
                trigger: 'blur'
            }
        ],
        label_type: [
            {
                required: true,
                message: '请选择标签类型',
                trigger: 'change'
            }
        ]
    }
    $refs!: {
        formRef: any
    }
    /** E Data **/

    /** S Methods **/
    // 表单提交
    onSubmit() {
        // 验证表单格式是否正确
        this.$refs.formRef.validate((valid: boolean): any => {
            if (!valid) {
                return
            }

            // 请求发送
            switch (this.mode) {
                case PageMode.ADD:
                    return this.handleUserLabelAdd()
                case PageMode.EDIT:
                    return this.handleUserLabelEdit()
            }
        })
    }

    // 新增用户等级
    handleUserLabelAdd() {
        const form = this.form as LabelAdd_Req
        apiUserLabelAdd(form)
            .then(() => {
                // this.$message.success('添加成功!')
                setTimeout(() => this.$router.go(-1), 500)
            })
            .catch(() => {
                // this.$message.error('保存失败!')
            })
    }

    // 编辑用户等级
    handleUserLabelEdit() {
        const params = this.form
        const id: number = this.identity as number
        apiUserLabelEdit({ ...params, id } as LabelEdit_Req)
            .then(() => {
                // this.$message.success('修改成功!')
                setTimeout(() => this.$router.go(-1), 500)
                //this.initFormDataForUserLabelEdit()
            })
            .catch(() => {
                // this.$message.error('保存失败!')
            })
    }
    // 表单初始化数据 [编辑模式] mode => edit
    initFormDataForUserLabelEdit() {
        apiUserLabelDetail({
            id: this.identity as number
        })
            .then((res: LabelDetail_Res) => {
                Object.keys(res).map(key => {
                    this.$set(this.form, key, res[key])
                })
            })
            .catch(() => {
                // this.$message.error('数据初始化失败，请刷新重载！')
            })
    }
    /** E Methods **/

    /** S Life Cycle **/
    created() {
        const query: any = this.$route.query

        if (query.mode) {
            this.mode = query.mode
        }

        // 编辑模式：初始化数据
        if (this.mode === PageMode.EDIT) {
            this.identity = query.id * 1
            this.initFormDataForUserLabelEdit()
        }
    }

    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.ls-card {
    .ls-input {
        width: 133px;
    }

    .ls-input-textarea {
        width: 300px;
    }

    .card-title {
        font-size: 14px;
        font-weight: 500;
    }
}

.user-tag-edit {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;

    &__header {
        flex: none;
    }
}
</style>
