<template>
    <div class="verifier-edit">
        <!-- Header -->
        <div class="ls-card">
            <el-page-header @back="$router.back()" :content="pageTitle" />
        </div>

        <!-- Form -->
        <div class="ls-card m-t-16">
            <el-form :rules="rules" ref="form" :model="form" label-width="120px" size="small">
                <!-- 核销员名称 -->
                <el-form-item label="核销员姓名" prop="name">
                    <el-input
                        class="ls-input"
                        v-model="form.name"
                        maxlength="12"
                        show-word-limit
                        placeholder="请输入核销员名称"
                    />
                </el-form-item>
                <!-- 用户名称 -->
                <el-form-item label="关联用户" prop="user_id">
                    <el-select
                        v-model="form.user_id"
                        filterable
                        remote
                        placeholder="请输入用户编号/昵称/手机号码"
                        :remote-method="queryUserOptions"
                        :loading="userOption.loading"
                    >
                        <el-option
                            v-for="item in userOption.list"
                            :key="item.id"
                            :label="item.nickname"
                            :value="item.id"
                        >
                            <div class="flex">
                                <el-avatar :src="item.avatar" :size="20" />
                                <span class="m-l-10" style="margin-right: auto">{{ item.nickname }}</span>
                                <!-- <span class="muted">编号: {{ item.sn }}</span> -->
                            </div>
                        </el-option>
                    </el-select>
                    <div class="muted xs">选择商城用户，核销员可在商城个人中心进行订单核销</div>
                </el-form-item>
                <!-- 自提门店 -->
                <el-form-item label="关联自提门店" prop="selffetch_shop_id">
                    <el-select
                        v-model="form.selffetch_shop_id"
                        filterable
                        remote
                        placeholder="请输入门店名称"
                        :remote-method="queryShopOptions"
                        :loading="shopOption.loading"
                    >
                        <el-option
                            v-for="item in shopOption.list"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                        ></el-option>
                    </el-select>
                </el-form-item>
                <!-- 联系电话 -->
                <el-form-item label="联系电话" prop="mobile">
                    <el-input class="ls-input" v-model="form.mobile" show-word-limit placeholder="请输入联系电话" />
                </el-form-item>
                <!-- 核销员状态 -->
                <el-form-item label="核销员状态">
                    <el-radio-group v-model="form.status">
                        <el-radio :label="0">停用</el-radio>
                        <el-radio :label="1">启用</el-radio>
                    </el-radio-group>
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
import {
    apiSelffetchVerifierAdd,
    apiSelffetchVerifierDetail,
    apiSelffetchVerifierEdit,
    apiSelffetchShopList
} from '@/api/application/selffetch'
import { apiUserList } from '@/api/user/user'
import { PageMode } from '@/utils/type'

@Component
export default class SelffetchVerifierEdit extends Vue {
    /** S Data **/
    mode: string = PageMode.ADD // 当前页面【add: 添加管理员 | edit: 编辑管理员】
    identity: number | null = null // 当前编辑用户的身份ID  valid: mode = 'edit'

    // 添加管理员表单数据
    form: any = {
        name: '', // 核销员名称
        user_id: '', // 用户id
        selffetch_shop_id: '', // 自提门店id
        mobile: '', // 联系电话
        status: 1 // 核销员状态: 1-启用; 0-禁用;
    }

    // 表单校验
    rules = {
        name: [{ required: true, message: '请输入核销员名称', trigger: 'blur' }],
        user_id: [{ required: true, message: '请选择用户', trigger: 'blur' }],
        selffetch_shop_id: [{ required: true, message: '请选择自提门店', trigger: 'blur' }],
        mobile: [{ required: true, message: '请输入手机号码', trigger: 'blur' }]
    }

    // 核销员信息【详情】
    verifierInfo = {}

    // 用户选择框
    userOption: any = {
        loading: false,
        list: []
    }

    // 用户选择框
    shopOption: any = {
        loading: false,
        list: []
    }
    /** E Data **/

    /** S Computed **/
    get pageTitle() {
        switch (this.mode) {
            case PageMode.ADD:
                return '新增核销员'
            case PageMode.EDIT:
                return '编辑核销员'
        }
    }
    /** S Computed **/

    /** S Methods **/
    // 搜索用户列表
    queryUserOptions(query: string) {
        if (query === '') {
            return (this.userOption.list = [])
        }
        this.userOption.loading = true
        apiUserList({
            keyword: query
            // page_type: 1,
        })
            .then(data => {
                this.userOption.list = data.lists
            })
            .finally(() => {
                this.userOption.loading = false
            })
    }

    // 搜索自提门店列表
    queryShopOptions(query: string) {
        if (query === '') {
            return (this.shopOption.list = [])
        }
        this.shopOption.loading = true
        apiSelffetchShopList({
            name: query
            // page_type: 1,
        })
            .then(data => {
                this.shopOption.list = data.lists
            })
            .finally(() => {
                this.shopOption.loading = false
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
            switch (this.mode) {
                case PageMode.ADD:
                    return this.handleVerifierAdd()
                case PageMode.EDIT:
                    return this.handleVerifierEdit()
            }
        })
    }

    // 添加管理员
    handleVerifierAdd() {
        const form = this.form
        apiSelffetchVerifierAdd(form)
            .then(() => {
                // this.$message.success('添加成功!')
                setTimeout(() => this.$router.back(), 500)
            })
            .catch(() => {
                // this.$message.error('保存失败!')
            })
    }

    // 编辑管理员
    handleVerifierEdit() {
        const params = this.form
        const id: number = this.identity as number
        apiSelffetchVerifierEdit({ ...params, id })
            .then(() => {
                // this.$message.success('修改成功!')
                setTimeout(() => this.$router.back(), 500)
            })
            .catch(() => {
                // this.$message.error('保存失败!')
            })
    }

    // 表单初始化数据 [编辑模式] mode => edit
    initFormDataForAdminEdit() {
        apiSelffetchVerifierDetail({
            id: this.identity as number
        })
            .then(res => {
                Object.keys(res).map(key => {
                    this.$set(this.form, key, res[key])
                })
                // 初始化用户选择
                this.userOption.list.push({
                    id: res.user_id,
                    nickname: res.nickname,
                    avatar: res.avatar
                })
                // 初始化店铺选择
                this.shopOption.list.push({
                    id: res.selffetch_shop_id,
                    name: res.shop_name
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
            this.initFormDataForAdminEdit()
        }
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
