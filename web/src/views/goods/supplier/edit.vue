<template>
    <div class="ls-supplier-edit">
        <div class="ls-card ls-supplier-edit__header">
            <el-page-header @back="$router.go(-1)" :content="id ? '编辑供应商' : '新增供应商'"></el-page-header>
        </div>

        <div class="ls-card ls-supplier-edit__form m-t-10" v-loading="loading">
            <el-form ref="form" :model="form" label-width="120px" size="small" :rules="rules">
                <el-form-item label="供应商编号" prop="code" required>
                    <el-input class="ls-input" v-model="form.code" placeholder="请输入供应商编号"></el-input>
                </el-form-item>
                <el-form-item label="供应商名称" required prop="name">
                    <el-input class="ls-input" v-model="form.name" placeholder="请输入供应商名称"></el-input>
                </el-form-item>
                <el-form-item label="供应商分类" required prop="supplier_category_id">
                    <el-select class="ls-input" v-model="form.supplier_category_id" placeholder="请选择供应商分类">
                        <el-option
                            v-for="item in categoryLists"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                        ></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="联系人">
                    <el-input class="ls-input" v-model="form.contact" placeholder="请输入联系人"></el-input>
                </el-form-item>
                <el-form-item label="联系人手机">
                    <el-input class="ls-input" v-model="form.mobile" placeholder="请输入联系人手机"></el-input>
                </el-form-item>
                <el-form-item label="座机号码">
                    <el-input class="ls-input" v-model="form.landline" placeholder="请输入座机号码"></el-input>
                </el-form-item>
                <el-form-item label="邮箱">
                    <el-input class="ls-input" v-model="form.email" placeholder=""></el-input>
                </el-form-item>
                <el-form-item label="所属地区">
                    <area-select
                        :province.sync="form.province_id"
                        :city.sync="form.city_id"
                        :district.sync="form.district_id"
                    />
                </el-form-item>
                <el-form-item label="详细地址">
                    <el-input
                        class="ls-input"
                        v-model="form.address"
                        type="textarea"
                        rows="3"
                        placeholder="请输入详细地址"
                    ></el-input>
                </el-form-item>
                <el-form-item label="银行账号">
                    <el-input class="ls-input" v-model="form.bank_account" placeholder=""></el-input>
                </el-form-item>
                <el-form-item label="开户银行">
                    <el-input class="ls-input" v-model="form.bank" placeholder=""></el-input>
                </el-form-item>
                <el-form-item label="持卡人姓名">
                    <el-input class="ls-input" v-model="form.cardholder_name" placeholder=""></el-input>
                </el-form-item>
                <el-form-item label="税务登记号">
                    <el-input class="ls-input" v-model="form.tax_id" placeholder=""></el-input>
                </el-form-item>
                <el-form-item label="排序">
                    <el-input class="ls-input" v-model="form.sort" placeholder=""></el-input>
                </el-form-item>
            </el-form>
        </div>

        <div class="ls-supplier-edit__footer bg-white ls-fixed-footer">
            <div class="btns row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="handleSave">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import AreaSelect from '@/components/area-select.vue'
import { apiSupplierAdd, apiSupplierCategoryLists, apiSupplierDetail, apiSupplierEdit } from '@/api/goods'
@Component({
    components: {
        AreaSelect
    }
})
export default class AddSupplier extends Vue {
    $refs!: { form: any }
    id!: any
    loading = false
    form = {
        code: '',
        name: '',
        supplier_category_id: '',
        contact: '',
        mobile: '',
        landline: '',
        email: '',
        city_id: '',
        district_id: '',
        province_id: '',
        address: '',
        bank_account: '',
        bank: '',
        cardholder_name: '',
        tax_id: '',
        sort: ''
    }

    rules = {
        code: [
            {
                required: true,
                message: '请输入供应商编号',
                trigger: ['blur', 'change']
            }
        ],
        name: [
            {
                required: true,
                message: '请输入供应商名称',
                trigger: ['blur', 'change']
            }
        ],
        supplier_category_id: [
            {
                required: true,
                message: '请选择供应商分类',
                trigger: ['blur', 'change']
            }
        ]
    }
    categoryLists = []
    handleSave() {
        this.$refs.form.validate((valid: boolean) => {
            if (valid) {
                const api = this.id ? apiSupplierEdit(this.form) : apiSupplierAdd(this.form)
                api.then(() => {
                    this.$router.go(-1)
                })
            } else {
                return false
            }
        })
    }
    getSupplierCategory() {
        apiSupplierCategoryLists({}).then((res: any) => {
            this.categoryLists = res?.lists
        })
    }

    getSupplierDetail() {
        this.loading = true
        apiSupplierDetail(this.id).then((res: any) => {
            this.form = res
            this.loading = false
        })
    }

    created() {
        this.id = this.$route.query.id
        this.id && this.getSupplierDetail()
        this.getSupplierCategory()
    }
}
</script>

<style lang="scss" scoped>
.ls-supplier-edit {
    padding-bottom: 80px;
    .ls-input {
        width: 380px;
    }
}
</style>
