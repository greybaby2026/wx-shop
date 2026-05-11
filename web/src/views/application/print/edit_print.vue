<template>
    <div>
        <!-- 头部导航 -->
        <div class="ls-card">
            <el-page-header @back="$router.go(-1)" :content="!identity ? '新增打印机' : '编辑打印机'" />
        </div>

        <div class="ls-card m-t-24 m-b-60">
            <el-form
                ref="printData"
                :hide-required-asterisk="false"
                :rules="rules"
                class="m-l-24"
                :model="printData"
                label-width="120px"
            >
                <el-form-item label="打印机名称" prop="name" label-position="right">
                    <el-input v-model="printData.name" placeholder="请输入打印机名称" class="ls-input"></el-input>
                </el-form-item>

                <el-form-item label="设备类型" prop="type" label-position="right">
                    <el-select v-model="printData.type" placeholder="请选择">
                        <el-option
                            v-for="(item, index) in printType"
                            :key="index"
                            :label="item.label"
                            :value="item.value"
                        >
                        </el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="终端号" prop="machine_code" label-position="right">
                    <el-input
                        v-model="printData.machine_code"
                        placeholder="请输入设备终端号"
                        class="ls-input"
                    ></el-input>
                </el-form-item>

                <el-form-item prop="private_key" label="打印机密钥" label-position="right">
                    <el-input
                        v-model="printData.private_key"
                        placeholder="请输入打印机密钥"
                        class="ls-input"
                    ></el-input>
                </el-form-item>

                <el-form-item label="应用ID" prop="client_id" label-position="right">
                    <el-input v-model="printData.client_id" placeholder="请输入应用ID" class="ls-input"></el-input>
                    <span class="desc">应用id在易联云-开发者中心-应用中心获取</span>
                </el-form-item>

                <el-form-item label="应用密钥" prop="client_secret" label-position="right">
                    <el-input
                        v-model="printData.client_secret"
                        placeholder="请输入应用密钥"
                        class="ls-input"
                    ></el-input>
                    <span class="desc">apiKey在易联云-开发者中心-应用中心获取</span>
                </el-form-item>

                <el-form-item label="小票模版" prop="template_id" label-position="right">
                    <el-select v-model="printData.template_id" placeholder="请选择">
                        <el-option
                            v-for="(item, index) in printTemplate"
                            :key="index"
                            :label="item.template_name"
                            :value="item.id"
                        >
                        </el-option>
                    </el-select>
                    <span class="m-l-20 primary pointer" @click="newTemplate">新建小票模版</span>
                    <span class="primary m-l-10 m-r-10">|</span>
                    <span class="primary pointer" @click="getPrintTemplateFunc">刷新</span>
                </el-form-item>

                <el-form-item label="打印联数" label-position="right" required>
                    <el-radio-group v-model="printData.print_number">
                        <el-radio :label="1">1联</el-radio>
                        <el-radio :label="2">2联</el-radio>
                        <el-radio :label="3">3联</el-radio>
                        <el-radio :label="4">4联</el-radio>
                    </el-radio-group>
                </el-form-item>

                <el-form-item label="自动打印" label-position="right" required>
                    <el-switch
                        v-model="printData.auto_print"
                        :active-value="1"
                        :inactive-value="0"
                        :active-color="styleConfig.primary"
                        inactive-color="#f4f4f5"
                        @change="changeStatusPaymentSet(scope.$index, index)"
                    />
                    <span class="muted m-l-20">订单付款后自动打印小票，默认关闭</span>
                </el-form-item>

                <el-form-item label="状态" label-position="right" required>
                    <el-switch
                        v-model="printData.status"
                        :active-value="1"
                        :inactive-value="0"
                        :active-color="styleConfig.primary"
                        inactive-color="#f4f4f5"
                        @change="changeStatusPaymentSet(scope.$index, index)"
                    />
                    <span class="muted m-l-20">关闭和开启打印机的使用，默认关闭</span>
                </el-form-item>
            </el-form>
        </div>

        <!-- 底部保存或取消 -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex m-t-15">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="onSubmit('printData')">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiPrintType, apiTemplateLists, apiAddPrint, apiEditPrint, apiPrintDetail } from '@/api/application/print'
@Component
export default class PrintEdit extends Vue {
    /** S Data **/

    identity: Number = 1

    status: any = ''

    printType: Array<Object> = []

    printTemplate: Array<Object> = []

    // 打印机设置的数据
    printData: any = {
        name: '', //	是	string	打印机名称
        type: 1, //	是	integer	设备类型
        machine_code: '', //	是	string	终端号
        private_key: '', //	是	string	打印机密钥
        client_id: '', //	是	string	应用ID
        client_secret: '', //	是	string	应用密钥
        template_id: '请选择', //	是	integer	模板id
        print_number: 1, //是	integer	打印联数 范围：1到4
        auto_print: 1, //	是	integer	自动打印 0-否 1-是
        status: 1 //	是	integer	启动状态 0-否 1-是
    }

    // 表单验证
    rules: any = {
        name: [{ required: true, message: '请输入打印机名称', trigger: 'blur' }],
        type: [{ required: true, message: '请选择设备类型', trigger: 'change' }],
        machine_code: [{ required: true, message: '请输入终端号', trigger: 'blur' }],
        private_key: [{ required: true, message: '请输入打印机密钥', trigger: 'blur' }],
        client_id: [{ required: true, message: '请输入应用ID', trigger: 'blur' }],
        client_secret: [{ required: true, message: '请输入应用密钥', trigger: 'blur' }],
        template_id: [{ required: true, message: '请选择模板', trigger: 'change' }]
    }

    /** E Data **/

    /** S Methods **/
    newTemplate() {
        const text = this.$router.resolve('/print/edit_template')
        window.open(text.href, '_blank')
    }

    // 获取打印机类型数据
    async getPrintDetail() {
        const res = await apiPrintDetail({ id: this.identity })
        this.printData = res
    }

    getPrintTypeFunc() {
        apiPrintType({})
            .then((res: any) => {
                this.printType = res
            })
            .catch(() => {
                this.$message.error('数据初始化失败，请刷新重载！')
            })
    }

    // 获取小票模版数据
    getPrintTemplateFunc() {
        apiTemplateLists({})
            .then((res: any) => {
                this.printTemplate = res.lists
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
                this.handlePrintAdd()
            } else {
                this.handlePrintEdit()
            }
        })
    }

    handlePrintAdd() {
        const params = this.printData
        apiAddPrint({ ...params }).then(() => {
            setTimeout(() => this.$router.go(-1), 500)
        })
    }

    // 编辑打印机
    handlePrintEdit() {
        const params = this.printData
        const id: number = this.identity as number
        apiEditPrint({ ...params, id }).then(() => {
            setTimeout(() => this.$router.go(-1), 500)
        })
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        const query: any = this.$route.query
        this.identity = query.id
        this.getPrintTypeFunc()
        this.getPrintTemplateFunc()
        if (this.identity) {
            this.getPrintDetail()
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
