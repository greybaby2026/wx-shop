<template>
    <div class="service-edit">
        <!-- Header -->
        <div class="ls-card">
            <el-page-header @back="$router.back()" :content="pageTitle" />
        </div>

        <!-- Form -->
        <div class="ls-card m-t-16" v-loading="loading">
            <el-form :rules="rules" ref="form" :model="form" label-width="120px" size="small">
                <!-- 选择管理员 -->
                <el-form-item label="管理员" prop="admin_id">
                    <el-select v-model="form.admin_id" placeholder="请选择管理员" :disabled="identity">
                        <el-option
                            v-for="item in adminOption"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                        ></el-option>
                    </el-select>
                    <div class="muted">管理员账号密码可用于登录客服工作台</div>
                </el-form-item>
                <!-- 客服头像 -->
                <el-form-item label="客服头像" prop="avatar">
                    <material-select v-model="form.avatar"></material-select>
                </el-form-item>
                <!-- 客服昵称 -->
                <el-form-item label="客服昵称" prop="nickname">
                    <el-input class="ls-input" v-model="form.nickname" placeholder="请输入客服昵称" />
                </el-form-item>
                <!-- 客服排序 -->
                <el-form-item label="客服排序" prop="sort">
                    <el-input class="ls-input" v-model="form.sort" placeholder="请输入客服排序" />
                    <div class="muted">排序值越小越靠前，默认值为1</div>
                </el-form-item>
                <el-form-item label="状态" prop="disable" required>
                    <el-switch v-model="form.disable" :active-value="0" :inactive-value="1" />
                    <div class="muted">客服账号状态，默认开启。关闭后禁止登陆客服工作台。</div>
                </el-form-item>
            </el-form>
        </div>

        <!-- Footer -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.back()">取消</el-button>
                <el-button size="small" type="primary" @click="onSubmit('form')">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiAdminList } from '@/api/setting/permissions'
import { apikefuAdd, apikefuEdit, apikefuDetail } from '@/api/application/service'
import MaterialSelect from '@/components/material-select/index.vue'
@Component({
    components: {
        MaterialSelect
    }
})
export default class ServiceEdit extends Vue {
    /** S Data **/
    loading = false
    identity: number | null = null

    // 添加客服表单数据
    form: any = {
        admin_id: '', // 管理员id
        nickname: '', // 客服昵称
        disable: 0, // 禁用状态，0-启用，1-禁用
        sort: '', // 排序
        avatar: '' // 头像路径
    }

    // 表单校验
    rules = {
        admin_id: [
            {
                required: true,
                message: '请选择管理员',
                trigger: ['blur', 'change']
            }
        ],
        nickname: [{ required: true, message: '请输入客服昵称', trigger: 'blur' }],
        avatar: [{ required: true, message: '请输入客服头像', trigger: 'blur' }]
    }

    // 用户选择框
    adminOption: any[] = []

    /** E Data **/

    /** S Computed **/
    get pageTitle() {
        if (this.identity) {
            return '编辑客服'
        }
        return '新增客服'
    }
    /** S Computed **/

    /** S Methods **/
    getAdminOption() {
        apiAdminList({
            page_type: 0
        }).then(data => {
            this.adminOption = data.lists
        })
    }

    // 点击表单提交
    onSubmit(formName: string) {
        // 验证表单格式是否正确
        const refs = this.$refs[formName] as HTMLFormElement
        refs.validate((valid: boolean): any => {
            if (!valid) {
                return
            }

            // 请求发送
            const request = this.identity ? apikefuEdit({ id: this.identity, ...this.form }) : apikefuAdd(this.form)
            request.then(data => {
                setTimeout(() => {
                    this.$router.back()
                }, 1000)
            })
        })
    }

    // 获取详情
    getDetails() {
        this.loading = true
        apikefuDetail({
            id: this.identity as number
        })
            .then(res => {
                Object.keys(res).map(key => {
                    this.$set(this.form, key, res[key])
                })
            })
            .catch(() => {
                // this.$message.error('数据初始化失败，请刷新重载！')
            })
            .finally(() => {
                this.loading = false
            })
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        const query: any = this.$route.query
        this.identity = query.id
        this.getAdminOption()
        if (!this.identity) {
            return
        }
        this.getDetails()
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.verifier-edit {
    padding-bottom: 80px;

    .ls-input {
        width: 380px;
    }
}
</style>
