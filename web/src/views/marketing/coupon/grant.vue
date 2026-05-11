<template>
    <div class="ls-coupon-edit">
        <div class="ls-card ls-coupon-edit__header">
            <el-page-header @back="$router.go(-1)" content="发放优惠券"></el-page-header>
        </div>

        <!-- 优惠设置 -->
        <div class="ls-card ls-coupon-edit__form m-t-10">
            <div class="lg weight-500 m-b-20">优惠券信息</div>
            <el-form ref="couponInfo" :model="couponInfo" label-width="120px" size="small">
                <el-form-item label="优惠券编号：" prop="" required>
                    <template>
                        {{ this.couponInfo.sn }}
                    </template>
                </el-form-item>

                <el-form-item label="优惠券名称：" prop="" required>
                    <template>
                        {{ this.couponInfo.name }}
                    </template>
                </el-form-item>

                <el-form-item label="推广方式：" prop="" required>
                    <template>
                        {{ this.couponInfo.get_method }}
                    </template>
                </el-form-item>

                <el-form-item label="用券时间：" prop="" required>
                    <template>
                        {{ this.couponInfo.use_time_text }}
                    </template>
                </el-form-item>

                <el-form-item label="发放总量：" prop="" required>
                    <template>
                        {{ this.couponInfo.send_total }}
                    </template>
                </el-form-item>

                <el-form-item label="剩余发放量：" prop="" required>
                    <template>
                        {{ this.couponInfo.surplus_number }}
                    </template>
                </el-form-item>
            </el-form>
        </div>

        <!-- 发放设置 -->
        <div class="ls-card ls-coupon-edit__form m-t-10 m-b-60">
            <div class="lg weight-500 m-b-20">发放设置</div>
            <el-form ref="couponInfo" :model="couponInfo" label-width="120px" size="small">
                <!-- 优惠的面额 -->
                <el-form-item label="每人发放张数">
                    <el-input
                        style="width: 280px"
                        v-model.number="couponInfo.send_user_num"
                        placeholder="请输入发放的优惠券数量"
                    ></el-input>
                </el-form-item>

                <!-- 用券时间选择 -->
                <el-form-item label="发放范围" required>
                    <div>
                        <user-select v-model="userSelectData" is_distribution="">
                            <el-button slot="trigger" size="mini" type="primary">选择用户</el-button>
                        </user-select>
                    </div>
                </el-form-item>
            </el-form>
        </div>

        <!-- 底部 -->
        <div class="ls-coupon-edit__footer bg-white ls-fixed-footer">
            <div class="btns row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="submit('couponInfo')">保存 </el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import AreaSelect from '@/components/area-select.vue'
import UserSelect from '@/components/marketing/user-select.vue'
import { apiCouponInfo, apiCouponSend } from '@/api/marketing/coupon'
@Component({
    components: {
        AreaSelect,
        UserSelect
    }
})
export default class AddSupplier extends Vue {
    /** S Data **/

    id!: any //当前的ID

    couponInfo: any = {
        id: this.id, //优惠券ID
        send_user_num: '', //必选	float	每人发放数量
        send_user: [] //用户的ID数组, use_goods_type=2 / 3 时必填, 且数组不可为空
    }

    userSelectData: any = []

    /** E Data **/

    /** S Method **/

    submit(formName: string) {
        // 验证表单格式是否正确
        // const refs = this.$refs[formName] as HTMLFormElement;
        // refs.validate((valid: boolean): any => {
        //     if (!valid) return;
        this.couponSend()
        // });
    }

    // 添加优惠券
    couponSend() {
        if (this.couponInfo.send_user_num == undefined) {
            return this.$message.error('请输入要发放的优惠券数量！!')
        }
        if (this.couponInfo.send_user_num < 0 || isNaN(this.couponInfo.send_user_num)) {
            return this.$message.error('不允许输入负数与非数字')
        }
        if (this.userSelectData.length == 0) {
            return this.$message.error('请输入要发放的用户！!')
        }
        const params = {
            id: this.id, //优惠券ID
            send_user_num: this.couponInfo.send_user_num, //必选	float	每人发放数量
            send_user: this.userSelectData.map((item: any) => item.id) //用户的ID数组, use_goods_type=2 / 3 时必填, 且数组不可为空
        }
        apiCouponSend({ ...params }).then(res => {
            setTimeout(() => this.$router.go(-1), 500)
        })
    }

    // 获取优惠券信息
    getCouponInfo() {
        apiCouponInfo(this.id).then((res: any) => {
            this.couponInfo = res
        })
    }

    /** E Method **/

    created() {
        this.id = this.$route.query.id
        this.id && this.getCouponInfo()
    }
}
</script>

<style lang="scss" scoped></style>
