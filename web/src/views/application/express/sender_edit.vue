<template>
    <div>
        <!-- 头部导航 -->
        <div class="ls-card">
            <el-page-header @back="$router.go(-1)" :content="!identity ? '新增发件人' : '编辑发件人'" />
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
                <el-form-item label="发件人" prop="name" label-position="right">
                    <el-input v-model="form.name" placeholder="请输入发件人名称" class="ls-input"></el-input>
                </el-form-item>

                <el-form-item label="发件人手机" prop="mobile" label-position="right">
                    <el-input v-model="form.mobile" placeholder="请输入发件人手机" class="ls-input"></el-input>
                </el-form-item>

                <el-form-item label="发件地区" prop="province_id" label-position="right">
                    <area-select
                        width="280px"
                        :province.sync="form.province_id"
                        :city.sync="form.city_id"
                        :district.sync="form.district_id"
                    />
                </el-form-item>

                <el-form-item prop="address" label="详细地址" label-position="right">
                    <el-input v-model="form.address" placeholder="请输入详细地址" class="ls-input"></el-input>
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
import { apiFaceSheetSenderEdit, apiFaceSheetSenderDetail, apiFaceSheetSenderAdd } from '@/api/application/express'
import AreaSelect from '@/components/area-select.vue'
@Component({
    components: {
        AreaSelect
    }
})
export default class PrintEdit extends Vue {
    /** S Data **/

    identity: Number = 1

    status: any = ''

    // 数据
    form: any = {
        name: '', //	是	string
        province_id: '', //	是	int	省id
        city_id: '', //	是	int	市id
        district_id: '', //	是	int	区id
        address: '' //	是	string	地址
    }

    // 表单验证
    rules: any = {
        name: [{ required: true, message: '请输入发件人名称', trigger: 'blur' }],
        mobile: [{ required: true, message: '请输入发件人手机', trigger: 'blur' }],
        province_id: [{ required: true, message: '请选择地区', trigger: 'change' }],
        address: [{ required: true, message: '请输入发件人详细地址', trigger: 'blur' }]
    }

    /** E Data **/

    /** S Methods **/

    // 获取打印机类型数据
    async getSenderDetail() {
        const res = await apiFaceSheetSenderDetail({ id: this.identity })
        this.form = res
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
                this.handleSenderAdd()
            } else {
                this.handleSenderEdit()
            }
        })
    }

    handleSenderAdd() {
        const params = this.form
        apiFaceSheetSenderAdd({ ...params }).then(() => {
            setTimeout(() => this.$router.go(-1), 500)
        })
    }

    // 编辑打印机
    handleSenderEdit() {
        const params = this.form
        const id: number = this.identity as number
        apiFaceSheetSenderEdit({ ...params, id }).then(() => {
            setTimeout(() => this.$router.go(-1), 500)
        })
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        const query: any = this.$route.query
        this.identity = query.id
        if (this.identity) {
            this.getSenderDetail()
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
