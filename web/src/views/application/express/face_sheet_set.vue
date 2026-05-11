<template>
    <div class="ls-add-admin">
        <div class="ls-card">
            <el-alert
                title="
            温馨提示：电子面单打印配置流程；
            1.系统支持的面单类型为快递100，需前往快递100官方申请账号并企业认证得到授权Key和Secret；
            2.需购买快递100云打印机，从打印机底部标签中可获取打印机设备编号(siid)，其他打印机不可用；
            3.需新建发件人信息模板，以供打印时选择发件人；
            4.需新建电子面单模板，以供打印时选择电子面单模板；
            5.特别注意：创建电子面单模板时用到的电子面单客户账号和密码，需自行联系快递公司开通获取并购买面单打印份额；"
                type="info"
                show-icon
                :closable="false"
            >
            </el-alert>
        </div>

        <div class="ls-card ls-coupon-edit__form m-t-10">
            <el-form ref="form" :model="form" label-width="120px" size="small">
                <el-form-item label="面单类型" prop="send_total_type">
                    <el-radio-group v-model="form.type">
                        <el-radio v-for="(item, index) in faceSheetType" :key="index" :label="item.key">{{
                            item.value
                        }}</el-radio>
                    </el-radio-group>
                    <span class="desc">支持的第三方面单打印方式</span>
                </el-form-item>

                <el-form-item label="授权Key" prop="key">
                    <el-input class="ls-input" placeholder="请输入授权Key" v-model="form.key"></el-input>
                    <span class="desc">授权Key在快递100企业管理后台 -> 我的信息 -> 企业信息获取</span>
                </el-form-item>

                <el-form-item label="Secret" prop="secret">
                    <el-input class="ls-input" placeholder="请输入Secret" v-model="form.secret"></el-input>
                    <span class="desc">Secret在快递100企业管理后台 -> 我的信息 -> 企业信息获取</span>
                </el-form-item>

                <el-form-item label="设备编号siid" prop="siid">
                    <el-input class="ls-input" placeholder="请输入设备编号siid" v-model="form.siid"></el-input>
                    <span class="desc">设备编号siid在快递100面单打印机底部标签获取</span>
                </el-form-item>
            </el-form>
        </div>

        <!-- 底部保存或取消 -->
        <div class="bg-white ls-fixed-footer row-center flex">
            <div class="row-center flex">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="onSubmit('form')">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import { apiFaceSheetConfigSetting, apiGetFaceSheetConfig, apiBetFaceSheetType } from '@/api/application/express'

@Component({
    components: {}
})
export default class DistributionResultSet extends Vue {
    /** S Data **/

    faceSheetType: Array<Object> = []

    form: any = {
        type: 1,
        key: '',
        secret: '',
        siid: ''
    }

    /** E Data **/

    /** S Methods **/

    async onSubmit() {
        await apiFaceSheetConfigSetting({ ...this.form })
        this.detail()
    }

    async getFaceSheetTypeFunc() {
        const res = await apiBetFaceSheetType({})
        this.faceSheetType = res
    }

    // 详情
    async detail() {
        const res = await apiGetFaceSheetConfig({})
        this.form = res
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.detail()
        this.getFaceSheetTypeFunc()
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.ls-add-admin {
    padding-bottom: 80px;

    .ls-input {
        width: 380px;
    }

    .desc {
        display: block;
        color: #999;
        font-size: 12px;
    }
}
</style>
