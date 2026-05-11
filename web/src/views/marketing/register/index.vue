<template>
    <div class="ls-card">
        <el-alert
            type="info"
            show-icon
            :closable="false"
            title="温馨提示：1、用户注册奖励包含积分、余额、优惠券；2、其中优惠券只能选择推广方式为【卖家发放】的类型。"
        ></el-alert>
        <el-form ref="form" :model="form" :rules="rules" label-width="120px" size="small">
            <div class="card-content m-t-24">
                <el-form-item label="是否开启" prop="status">
                    <el-radio-group v-model="form.status">
                        <el-radio :label="1">开启</el-radio>
                        <el-radio :label="0">关闭</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="注册奖励">
                    <el-checkbox v-model="form.user_integral_status" :true-label="1" :false-label="0">积分</el-checkbox>
                    <el-checkbox v-model="form.user_money_status" :true-label="1" :false-label="0">余额</el-checkbox>
                    <el-checkbox v-model="form.coupon_status" :true-label="1" :false-label="0">优惠券</el-checkbox>
                </el-form-item>
                <el-form-item label="积分" prop="user_integral_num" v-if="form.user_integral_status">
                    <el-input v-model="form.user_integral_num">
                        <template slot="append">积分</template>
                    </el-input>
                </el-form-item>
                <el-form-item label="余额" prop="user_integral_num" v-if="form.user_money_status">
                    <el-input v-model="form.user_money_num">
                        <template slot="append">元</template>
                    </el-input>
                </el-form-item>
                <el-form-item label="优惠券" prop="" v-if="form.coupon_status">
                    <coupon-select v-model="form.coupon_array" :limit="10"></coupon-select>
                </el-form-item>
                <el-form-item label="活动弹窗模版" prop="style">
                    <div class="flex">
                        <div
                            style="width: 200px"
                            class="container"
                            :class="{ active: form.style == 1 ? true : false }"
                            @click="handleselect(1)"
                        >
                            <el-image :src="require('./images/style1.jpg')"></el-image>
                        </div>
                        <div
                            style="width: 200px"
                            class="m-l-30 container"
                            :class="{ active: form.style == 2 ? true : false }"
                            @click="handleselect(2)"
                        >
                            <el-image :src="require('./images/style2.jpg')"></el-image>
                        </div>
                    </div>
                </el-form-item>
            </div>
        </el-form>
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <!-- <el-button size="small" @click="onResetFrom">重置</el-button> -->
                <el-button size="small" type="primary" @click="onSubmitFrom">保存</el-button>
            </div>
        </div>
    </div>
</template>
<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import CouponSelect from '@/components/coupon-select/index.vue'
import { apiregisterSave, apiregisterdetial } from '@/api/marketing/register'
@Component({
    components: {
        CouponSelect
    }
})
export default class Register extends Vue {
    /** S Data **/
    // 表单数据
    form: any = {
        status: 0,
        user_integral_status: 1,
        user_money_status: 1,
        coupon_status: 1,
        user_integral_num: '',
        user_money_num: '',
        coupon_array: [],
        style: 1
    }

    // 表单验证
    rules: object = {
        status: [{ required: true, message: '必填项不能为空', trigger: 'blur' }],
        user_integral_num: [{ required: true, message: '必填项不能为空', trigger: 'blur' }],
        style: [{ required: true, message: '必填项不能为空', trigger: 'blur' }]
    }
    /** E Data **/
    handleselect(val: number) {
        this.form.style = val
    }
    getData() {
        apiregisterdetial().then((res: any) => {
            this.form = res
        })
    }
    onSubmitFrom() {
        this.form.coupon_array = this.form.coupon_array.map((item: any) => {
            return { id: item.id }
        })
        apiregisterSave(this.form).then(() => {
            this.getData()
        })
    }
    created() {
        this.getData()
    }
}
</script>

<style lang="scss" scoped>
.container {
    padding: 10px 20px;
    border: 1px solid rgba(187, 187, 187, 1);
    border-radius: 5px;
    cursor: pointer;
}
.active {
    border: 2px solid rgba(64, 115, 250, 1);
}
</style>
