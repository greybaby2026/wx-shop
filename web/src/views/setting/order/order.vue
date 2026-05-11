<template>
    <div class="setting-order">
        <!-- 提示 -->
        <!-- <div class="ls-card">
			<el-alert title="温馨提示：应工信部的要求,请务必填写公安备案号和网站备案号,保存的备案信息,将展示在后台登陆页面" type="info" :closable="false"
				show-icon />
		</div> -->

        <!-- 主要内容 -->
        <el-form ref="formRef" :model="form" label-width="130px" size="small">
            <!-- 订单设置 -->
            <div class="ls-card">
                <div class="card-title">订单设置</div>
                <div class="card-content m-t-24 order-setting">
                    <el-form-item label="系统取消待付款订单">
                        <div>
                            <el-radio :label="0" v-model="form.cancel_unpaid_orders"
                                >关闭系统自动取消待付款订单</el-radio
                            >
                        </div>
                        <div>
                            <el-radio :label="1" v-model="form.cancel_unpaid_orders">
                                <span class="m-r-5">订单提交后</span>
                                <el-input class="ls-input" v-model="form.cancel_unpaid_orders_times" size="small">
                                </el-input>
                                <span class="m-l-5">分钟内未付款/未确认付款，系统自动取消</span>
                            </el-radio>
                        </div>
                    </el-form-item>

                    <el-form-item label="买家取消待发货订单">
                        <div>
                            <el-radio :label="0" v-model="form.cancel_unshipped_orders"
                                >关闭买家取消待发货订单</el-radio
                            >
                        </div>
                        <div>
                            <el-radio :label="1" v-model="form.cancel_unshipped_orders">
                                <span class="m-r-5">待发货订单</span>
                                <el-input class="ls-input" v-model="form.cancel_unshipped_orders_times" size="small">
                                </el-input>
                                <span class="m-l-5">分钟内允许买家取消</span>
                            </el-radio>
                        </div>
                    </el-form-item>

                    <el-form-item label="系统确认收货">
                        <div>
                            <el-radio :label="0" v-model="form.automatically_confirm_receipt"
                                >关闭系统自动确认收货</el-radio
                            >
                        </div>
                        <div>
                            <el-radio :label="1" v-model="form.automatically_confirm_receipt">
                                <span class="m-r-5">订单发货后</span>
                                <el-input
                                    class="ls-input"
                                    v-model="form.automatically_confirm_receipt_days"
                                    size="small"
                                ></el-input>
                                <span class="m-l-5">天内，系统自动确认收货</span>
                            </el-radio>
                        </div>
                    </el-form-item>

                    <el-form-item label="完成可售后时效">
                        <div>
                            <el-radio :label="0" v-model="form.after_sales">关闭售后维权</el-radio>
                        </div>
                        <div>
                            <el-radio :label="1" v-model="form.after_sales">
                                <span class="m-r-5">订单确认收货后</span>
                                <el-input class="ls-input" v-model="form.after_sales_days" size="small"></el-input>
                                <span class="m-l-5">天内，可申请售后维权</span>
                            </el-radio>
                            <div class="muted xs">订单完成后，多长时间内可申请维权，关闭则订单完成后不可维权</div>
                        </div>
                    </el-form-item>
                </div>
            </div>

            <!-- 库存设置 -->
            <div class="ls-card m-t-16">
                <div class="card-title">库存设置</div>
                <div class="card-content m-t-24">
                    <el-form-item label="库存占用时机">
                        <!-- <el-radio-group class="m-r-16" v-model="form.inventory_occupancy"> -->
                        <el-radio class="m-r-16" v-model="form.inventory_occupancy" :label="1"
                            >订单提交占用库存</el-radio
                        >
                        <!-- </el-radio-group> -->
                        <div class="muted xs">订单提交占用库存，库存不足则无法提交订单</div>
                    </el-form-item>
                    <el-form-item label="取消订单退回库存">
                        <el-radio-group class="m-r-16" v-model="form.return_inventory">
                            <el-radio :label="1">需要退回库存</el-radio>
                            <el-radio :label="0">无需退回库存</el-radio>
                        </el-radio-group>
                        <div class="muted xs">订单提交占用库存，库存不足则无法提交订单</div>
                    </el-form-item>
                </div>
            </div>

            <!-- 其他设置 -->
            <div class="ls-card m-t-16">
                <div class="card-title">其他设置</div>
                <div class="card-content m-t-24">
                    <el-form-item label="取消订单退回优惠券">
                        <el-radio-group class="m-r-16" v-model="form.return_coupon">
                            <el-radio :label="1">需要退还优惠券</el-radio>
                            <el-radio :label="0">无需退还优惠券</el-radio>
                        </el-radio-group>
                        <div class="muted xs">
                            待付款、待发货订单取消时，需要退还优惠券。注意优惠券受发放条件限制可能退还失败。
                        </div>
                    </el-form-item>
                </div>
            </div>
        </el-form>

        <!--  表单功能键  -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <!-- <el-button size="small" @click="$router.go(-1)">取消</el-button> -->
                <el-button size="small" type="primary" @click="setSettingOrder()">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import { apiOrderConfig, apiOrderConfigSet } from '@/api/setting/order'
@Component({
    components: {}
})
export default class SettingOrder extends Vue {
    /** S Data **/
    form = {
        cancel_unpaid_orders: 0, //系统取消待付款订单 0-关闭系统自动取消待付款订单 1-订单提交{cancel_unpaid_orders_times}分钟内未付款，系统自动取消
        cancel_unpaid_orders_times: 0, // 取消未付款订单时间,单位：分钟
        cancel_unshipped_orders: 0, // 买家取消待发货订单 0-关闭买家取消待发货订单 1-待发货订单{cancel_unshipped_orders_times}分钟内允许买家取消
        cancel_unshipped_orders_times: 0, // 取消待发货订单时间,单位：分钟
        automatically_confirm_receipt: 0, // 系统自动确认收货 0-关闭系统自动确认收货 1-订单发货后{automatically_confirm_receipt_days}天内，系统 自动确认收货
        automatically_confirm_receipt_days: 0, // 系统自动收货时间,单位：天
        after_sales: 0, // 买家售后维权时效 0-关闭售后维权 1-订单确认收货{after_sales_days}天内，可申请售后维权
        after_sales_days: 0, // 售后维权时间，单位：天
        inventory_occupancy: 1, // 库存占用时机 1-订单提交占用库存
        return_inventory: 0, // 取消未付款/未发货的订单退回库存 0-无需退回库存 1-需要退回库存
        return_coupon: 0 // 取消未付款/未发货订单退回优惠券券 0-无需退还优惠券 1-需要退还优惠券
    }
    /** E Data **/

    // 获取交易设置
    getSettingOrder() {
        apiOrderConfig()
            .then((res: any) => {
                // this.$message.success('数据请求成功!')
                this.form = res
            })
            .catch(() => {
                this.$message.error('数据请求失败!')
            })
    }

    // 修改交易设置
    setSettingOrder() {
        apiOrderConfigSet(this.form)
            .then((res: any) => {
                this.getSettingOrder()
                this.$message.success('保存成功!')
            })
            .catch(() => {
                // this.$message.error('保存失败!')
            })
    }
    /** S Life Cycle **/
    created() {
        this.getSettingOrder()
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.ls-card {
    .ls-input {
        width: 100px;
        height: 50px;
    }

    .card-title {
        font-size: 14px;
        font-weight: 500;
    }

    .order-setting {
        ::v-deep .el-radio-group {
            /* #ifndef APP-NVUE */
            display: flex;
            /* #endif */
            flex-direction: column;
            align-items: flex-start;
        }

        ::v-deep .el-form-item {
            // display: flex;
            // align-items: flex-start;
        }
    }
}
.setting-order {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;
    &__header {
        flex: none;
    }
}
</style>
