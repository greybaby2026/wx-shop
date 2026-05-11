<template>
    <div>
        <!-- 头部导航 -->
        <div class="ls-card">
            <el-page-header @back="$router.go(-1)" :content="!identity ? '新增电子面单模版' : '编辑电子面单模版'" />
        </div>

        <div class="ls-card m-t-24 m-b-60">
            <el-form
                ref="form"
                :hide-required-asterisk="false"
                :rules="rules"
                class="m-l-24"
                :model="form"
                label-width="120px"
            >
                <el-form-item label="模版名称" prop="name" label-position="right">
                    <el-input v-model="form.name" placeholder="请输入模版名称" class="ls-input"></el-input>
                </el-form-item>

                <el-form-item label="模版编号" prop="template_id" label-position="right">
                    <el-input v-model="form.template_id" placeholder="请输入模版编号" class="ls-input"></el-input>
                </el-form-item>

                <el-form-item label="快递公司" prop="express_id" label-position="right">
                    <el-select v-model="form.express_id" placeholder="请选择">
                        <el-option
                            v-for="(item, index) in expressData"
                            :key="index"
                            :label="item.name"
                            :value="item.id"
                        >
                        </el-option>
                    </el-select>
                    <span class="m-l-20 primary pointer" @click="newExpress">新建快递公司</span>
                    <span class="primary m-l-10 m-r-10">|</span>
                    <span class="primary pointer" @click="getExpressDataFunc">刷新</span>
                </el-form-item>

                <el-form-item prop="partner_id" label="电子面单客户账号" label-position="right">
                    <el-input
                        v-model="form.partner_id"
                        placeholder="请输入电子面单客户账号"
                        class="ls-input"
                    ></el-input>
                </el-form-item>

                <el-form-item label="电子面单密码" prop="partner_key" label-position="right">
                    <el-input v-model="form.partner_key" placeholder="请输入电子面单密码" class="ls-input"></el-input>
                </el-form-item>

                <el-form-item label="收件网点标示" prop="net" label-position="right">
                    <el-input v-model="form.net" placeholder="请输入收件网点标示" class="ls-input"></el-input>
                </el-form-item>

                <el-form-item label="运费支付方式" prop="pay_type" label-position="right">
                    <el-select v-model="form.pay_type" placeholder="请选择">
                        <el-option
                            v-for="(item, index) in freightPayment"
                            :key="index"
                            :label="item.value"
                            :value="item.key"
                        >
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-form>
        </div>

        <!-- 底部保存或取消 -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex m-t-15">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="onSubmit('form')">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import {
    apiFaceSheetTemplateEdit,
    apiFaceSheetTemplateDetail,
    apiFaceSheetTemplateAdd,
    apiGetFaceSheetPayment
} from '@/api/application/express'
import { apiExpressLists } from '@/api/setting/delivery'
@Component
export default class FaceSheetEdit extends Vue {
    /** S Data **/

    identity: Number = 1

    status: any = ''

    freightPayment: Array<Object> = []

    expressData: Array<Object> = []

    // 打印机设置的数据
    form: any = {
        name: '', //	是	string	模板名称
        template_id: '', //	是	string	模板ID
        express_id: '', //	是	int	快递公司
        partner_id: '', //	是	string	电子面单账号
        partner_key: '', //	是	string	电子面单密码
        net: '', //	是	string	网点
        pay_type: '' //	是	int	运费支付方式
    }

    // 表单验证
    rules: any = {
        name: [{ required: true, message: '请输入模版名称', trigger: 'blur' }],
        template_id: [{ required: true, message: '请输入模版编号', trigger: 'blur' }],
        express_id: [{ required: true, message: '请选择快递公司', trigger: 'change' }],
        net: [{ required: true, message: '请输入收件网点标示', trigger: 'blur' }],
        pay_type: [{ required: true, message: '请选择运费支付方式', trigger: 'change' }]
    }

    /** E Data **/

    /** S Methods **/

    // 获取打印机类型数据
    async getFaceSheetDetail() {
        const res = await apiFaceSheetTemplateDetail({ id: this.identity })
        this.form = res
    }

    getFreightPaymentFunc() {
        apiGetFaceSheetPayment({})
            .then((res: any) => {
                this.freightPayment = res
            })
            .catch(() => {
                this.$message.error('数据初始化失败，请刷新重载！')
            })
    }

    // 获取小票模版数据
    getExpressDataFunc() {
        apiExpressLists({})
            .then((res: any) => {
                this.expressData = res.lists
            })
            .catch(() => {
                this.$message.error('数据初始化失败，请刷新重载！')
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
            if (!this.identity) {
                this.handleFaceSheetAdd()
            } else {
                this.handleFaceSheetEdit()
            }
        })
    }

    handleFaceSheetAdd() {
        const params = this.form
        apiFaceSheetTemplateAdd({ ...params }).then(() => {
            setTimeout(() => this.$router.go(-1), 500)
        })
    }

    // 编辑打印机
    handleFaceSheetEdit() {
        const params = this.form
        const id: number = this.identity as number
        apiFaceSheetTemplateEdit({ ...params, id }).then(() => {
            setTimeout(() => this.$router.go(-1), 500)
        })
    }

    newExpress() {
        const text = this.$router.resolve('/setting/delivery/express')
        window.open(text.href, '_blank')
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        const query: any = this.$route.query
        this.identity = query.id
        this.getFreightPaymentFunc()
        this.getExpressDataFunc()
        if (this.identity) {
            this.getFaceSheetDetail()
        }
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.ls-input {
    width: 280px;
}

.desc {
    color: #999999;
    display: block;
    height: 20px;
    line-height: 30px;
}

.copy {
    color: #4073fa;
    margin-left: 16px;
    cursor: pointer;
}
.card-title {
    font-size: 14px;
    font-weight: 500;
}
</style>
