<template>
    <ls-dialog
        :title="formData.id ? '编辑地址' : '新增地址'"
        class="m-l-10 inline"
        width="800px"
        top="20vh"
        ref="Dialog"
        @confirm="handleConfirm"
        @close="handleColse"
    >
        <el-form ref="form" size="mini" label-width="120px">
            <el-form-item label="添加地址">电话号码、手机号码选填一项，备注为可填项，其余均为必填项</el-form-item>
            <el-form-item label="联系人"
                ><el-input v-model="formData.contact" placeholder="请输入"></el-input
            ></el-form-item>
            <el-form-item label="所在地区"
                ><area-select
                    width="280px"
                    :province.sync="formData.province_id"
                    :city.sync="formData.city_id"
                    :district.sync="formData.district_id"
            /></el-form-item>
            <el-form-item label="详细地址"
                ><el-input v-model="formData.address" placeholder="请输入"></el-input
            ></el-form-item>
            <el-form-item label="电话号码">
                <el-input v-model="formData.phone_code" placeholder="请输入" style="width: 100px"></el-input>
                <span class="m-l-5 m-r-5"> - </span>
                <el-input v-model="formData.phone_number" placeholder="请输入" style="width: 150px"></el-input>
                <span class="m-l-5 m-r-5"> - </span>
                <el-input v-model="formData.phone_extension" placeholder="请输入" style="width: 150px"></el-input>
            </el-form-item>
            <el-form-item label="手机号码"
                ><el-input v-model="formData.mobile" placeholder="请输入"></el-input
            ></el-form-item>
            <el-form-item label="备注"
                ><el-input v-model="formData.remarks" placeholder="请输入"></el-input
            ></el-form-item>
        </el-form>
    </ls-dialog>
</template>
<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import AreaSelect from '@/components/area-select.vue'
import LsDialog from '@/components/ls-dialog.vue'
import { apiaddressLists, apiaddDetial, apiaddressAdd, apiaddressEdit } from '@/api/application/address'

@Component({
    components: {
        AreaSelect,
        LsDialog
    }
})
export default class LsWithdrawalDetails extends Vue {
    $refs!: {
        Dialog: any
    }
    formData: any = {
        id: '',
        contact: '',
        mobile: '',
        phone_code: '',
        phone_number: '',
        phone_extension: '',
        province_id: '',
        city_id: '',
        district_id: '',
        address: '',
        remarks: ''
    }
    @Prop() value: any
    openDialog(val: any) {
        if (val) {
            this.formData.id = val
            this.getData()
        }

        this.$refs.Dialog.open()
    }
    getData() {
        apiaddDetial({ id: this.formData.id }).then(res => {
            this.formData = res
        })
    }
    async handleConfirm() {
        this.formData.id ? await apiaddressEdit(this.formData) : await apiaddressAdd(this.formData)
        // this.$refs.Dialog.close()
        this.$emit('reflsh')
    }
    handleColse() {
        this.$emit('colse')
        this.$emit('reflsh')
    }
}
</script>
