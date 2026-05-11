<template>
    <div>
        <!-- 头部导航 -->
        <div class="ls-card">
            <el-page-header @back="$router.go(-1)" :content="!identity ? '新增小票模版' : '编辑小票模版'" />
        </div>

        <div class="flex col-top">
            <div class="ls-card m-t-24 m-b-60 m-r-24">
                <div class="title">基本信息</div>

                <el-form
                    ref="printData"
                    style="width: 500px"
                    :hide-required-asterisk="false"
                    :rules="rules"
                    class="m-l-24"
                    :model="printData"
                    label-width="120px"
                >
                    <el-form-item label="模版名称" prop="template_name" label-position="right">
                        <el-input
                            v-model="printData.template_name"
                            placeholder="请输入模版名称"
                            class="ls-input"
                        ></el-input>
                    </el-form-item>

                    <el-form-item label="小票名称" prop="ticket_name" label-position="right">
                        <el-input
                            v-model="printData.ticket_name"
                            placeholder="请输入小票名称"
                            class="ls-input"
                        ></el-input>
                    </el-form-item>

                    <el-form-item label="商城名称" label-position="right" required>
                        <el-switch
                            v-model="printData.show_shop_name"
                            :active-value="1"
                            :inactive-value="0"
                            :active-color="styleConfig.primary"
                            inactive-color="#f4f4f5"
                        />
                        <span class="muted m-l-20">隐藏和显示商城名称，默认显示</span>
                    </el-form-item>

                    <el-form-item label="配送方式" required>
                        <el-radio-group v-model="printData.type">
                            <el-radio :label="0">物流配送</el-radio>
                            <el-radio :label="1">门店自提</el-radio>
                        </el-radio-group>
                    </el-form-item>

                    <el-form-item label="提货信息" label-position="right" required v-show="printData.type === 1">
                        <el-checkbox :true-label="1" :false-label="0" v-model="printData.verification_info.pickup_code"
                            >核销码
                        </el-checkbox>
                        <el-checkbox :true-label="1" :false-label="0" v-model="printData.verification_info.contacts"
                            >提货人
                        </el-checkbox>
                        <el-checkbox :true-label="1" :false-label="0" v-model="printData.verification_info.mobile">
                            联系方式
                        </el-checkbox>
                    </el-form-item>

                    <el-form-item label="门店信息" label-position="right" required v-show="printData.type === 1">
                        <el-checkbox :true-label="1" :false-label="0" v-model="printData.selffetch_shop.shop_name"
                            >自提门店
                        </el-checkbox>
                        <el-checkbox :true-label="1" :false-label="0" v-model="printData.selffetch_shop.contacts"
                            >联系人
                        </el-checkbox>
                        <el-checkbox :true-label="1" :false-label="0" v-model="printData.selffetch_shop.shop_address">
                            门店地址
                        </el-checkbox>
                    </el-form-item>

                    <el-form-item label="收货信息" label-position="right" required v-show="printData.type === 0">
                        <el-checkbox :true-label="1" :false-label="0" v-model="printData.consignee_info.name"
                            >收货人
                        </el-checkbox>
                        <el-checkbox :true-label="1" :false-label="0" v-model="printData.consignee_info.mobile"
                            >联系方式
                        </el-checkbox>
                        <el-checkbox :true-label="1" :false-label="0" v-model="printData.consignee_info.address"
                            >收货地址
                        </el-checkbox>
                    </el-form-item>

                    <el-form-item label="买家留言" label-position="right" required>
                        <el-switch
                            v-model="printData.show_buyer_message"
                            :active-value="1"
                            :inactive-value="0"
                            :active-color="styleConfig.primary"
                            inactive-color="#f4f4f5"
                        />
                        <span class="muted m-l-20">隐藏和显示买家留言，默认显示</span>
                    </el-form-item>

                    <el-form-item label="二维码" label-position="right" required>
                        <el-switch
                            v-model="printData.show_qrcode"
                            :active-value="1"
                            :inactive-value="0"
                            :active-color="styleConfig.primary"
                            inactive-color="#f4f4f5"
                        />
                        <span class="muted m-l-20">关闭和开启打印小票尾部二维码，默认关闭</span>
                    </el-form-item>

                    <el-form-item label="二维码链接" label-position="right">
                        <el-input
                            v-model="printData.qrcode_content"
                            placeholder="请输入二维码链接"
                            class="ls-input"
                        ></el-input>
                    </el-form-item>

                    <el-form-item label="二维码名称" label-position="right">
                        <el-input
                            v-model="printData.qrcode_name"
                            placeholder="请输入二维码名称"
                            class="ls-input"
                        ></el-input>
                    </el-form-item>

                    <el-form-item label="底部信息" prop="bottom" label-position="right" required>
                        <el-input
                            v-model="printData.bottom"
                            placeholder="谢谢惠顾，欢迎下次光临"
                            class="ls-input"
                        ></el-input>
                    </el-form-item>
                </el-form>
            </div>

            <div class="ls-card m-b-60 m-t-24">
                <div class="title">小票预览</div>
                <div class="phone">
                    <div class="bb flex row-center sm m-t-5 p-b-12">
                        {{ printData.ticket_name || '小票名称' }}
                    </div>
                    <div v-if="printData.show_shop_name" class="bb flex row-center m-t-12 xl p-b-12">商城名称</div>
                    <div class="sm bb p-l-10">
                        <div class="m-t-12">订单编号: MWE20012345738754657834</div>
                        <div class="m-t-12">支付方式: 微信支付</div>
                        <div class="m-t-12 p-b-12">配送方式: {{ printData.type == 0 ? '物流配送' : '门店自提' }}</div>
                    </div>
                    <div class="bb p-l-10 p-r-10">
                        <div class="m-t-12 flex row-between">
                            <div>商品名称</div>
                            <div>数量</div>
                            <div>金额</div>
                        </div>
                        <div class="m-t-12 flex row-between">
                            <div>男子运动鞋</div>
                            <div>1</div>
                            <div>¥300</div>
                        </div>
                        <div class="m-t-12 flex row-between p-b-12">
                            <div>白色休闲鞋</div>
                            <div>2</div>
                            <div>¥100</div>
                        </div>
                    </div>
                    <div class="p-l-10 p-r-10 bb">
                        <div class="m-t-12 flex row-between">
                            <div>订单原价</div>
                            <div>¥400</div>
                        </div>
                        <div class="m-t-12 flex row-between">
                            <div>会员折扣</div>
                            <div>-¥20.00</div>
                        </div>
                        <div class="m-t-12 flex row-between">
                            <div>优惠券</div>
                            <div>-¥30.00</div>
                        </div>
                        <div class="m-t-12 flex row-between">
                            <div>商品改价</div>
                            <div>+¥10.00</div>
                        </div>
                        <div class="m-t-12 flex row-between">
                            <div>运费</div>
                            <div>+¥10.00</div>
                        </div>
                        <div class="m-t-12 p-b-12 flex row-between">
                            <div>实付金额</div>
                            <div>¥370.00</div>
                        </div>
                    </div>
                    <div class="bb p-b-12 p-l-10 p-r-10" v-show="printData.type === 0">
                        <div class="bold m-t-12 weight-500">收货信息</div>
                        <div class="m-t-12" v-if="printData.consignee_info.name">收货人: 米先生</div>
                        <div class="m-t-12" v-if="printData.consignee_info.mobile">联系方式: 18778786555</div>
                        <div class="m-t-12" v-if="printData.consignee_info.address">
                            收货地址: 山西省 太原市 小区店 创业街
                        </div>
                    </div>
                    <div class="bb p-b-12 p-l-10 p-r-10" v-show="printData.type === 1">
                        <div class="bold m-t-12 weight-500">门店信息</div>
                        <div class="m-t-12" v-if="printData.selffetch_shop.shop_name">门店名称: 商业街店铺</div>
                        <div class="m-t-12" v-if="printData.selffetch_shop.contacts">门店联系人: 13800138000</div>
                        <div class="m-t-12" v-if="printData.selffetch_shop.shop_address">
                            门店地址: 广东省番禺区 小店街
                        </div>
                    </div>
                    <div class="bb p-b-12 p-l-10 p-r-10" v-show="printData.type === 1">
                        <div class="bold m-t-12 weight-500">提货信息</div>
                        <div class="m-t-12" v-if="printData.verification_info.pickup_code">核销码: 254220</div>
                        <div class="m-t-12" v-if="printData.verification_info.contacts">提货人: 王冰冰</div>
                        <div class="m-t-12" v-if="printData.verification_info.mobile">联系方式: 13800138000</div>
                    </div>
                    <div class="m-t-12 p-b-12 bb p-l-10" v-if="printData.show_buyer_message">
                        买家留言: 请发申通快递，谢谢
                    </div>

                    <div class="flex row-center bb m-t-12 p-b-12" v-if="printData.show_qrcode">
                        <img style="width: 100px; height: 100px" src="@/assets/images/qr_code.png" />
                    </div>

                    <div class="m-t-12 p-b-5 flex row-center">
                        {{ printData.bottom }}
                    </div>
                </div>
            </div>
        </div>

        <!-- 底部保存或取消 -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex m-t-12">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="onSubmit('printData')">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiAddTemplate, apiEditTemplate, apiTemplateDetail } from '@/api/application/print'
import { PaymentConfig_Req, PaymentConfigGet_Req } from '@/api/setting/payment.d'
import MaterialSelect from '@/components/material-select/index.vue'
@Component({
    components: {
        MaterialSelect
    }
})
export default class PrintEdit extends Vue {
    /** S Data **/

    identity = 1
    type = 0
    distribution_mode = 1 // 配送方式： 1-物流  2-自提
    // 打印机设置的数据
    printData: any = {
        template_name: '', //	是	string	模板名称
        ticket_name: '', //	是	string	小票名称
        show_shop_name: 1, //	是	integer	是否显示商城名称 0-否 1-是
        template_id: 0, //	是	integer	模板id
        selffetch_shop: {
            shop_name: 1,
            contacts: 1,
            shop_address: 1
        }, //	是	object	自提门店信息
        verification_info: {
            pickup_code: 1,
            contacts: 1,
            mobile: 1
        }, //	是	object	提货信息
        show_buyer_message: 1, //	是	integer	是否显示买家留言 0-否 1-是
        consignee_info: {
            name: 1,
            mobile: 1,
            address: 1
        }, //	是	object	收货人信息
        show_qrcode: 1, //	是	integer	是否显示二维码 0-否 1-是
        bottom: '谢谢惠顾，欢迎下次光临', //	是	string	底部信息
        qrcode_name: '', //	否	string	二维码名称
        qrcode_content: '' //	否	string	二维码内容
    }

    // 表单验证
    rules: any = {
        template_name: [{ required: true, message: '请输入模版名称', trigger: 'blur' }],
        ticket_name: [{ required: true, message: '输入小票名称', trigger: 'blur' }],
        bottom: [{ required: true, message: '请输入底部信息', trigger: 'blur' }],
        qrcode_content: [{ required: true, message: '请输入二维码链接', trigger: 'blur' }],
        qrcode_name: [{ required: true, message: '请输入二维码名称', trigger: 'blur' }]
    }

    /** E Data **/

    /** S Methods **/
    async getTemplateDetail() {
        const res = await apiTemplateDetail({ id: this.identity })
        this.printData = res
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
                this.handleTemplateAdd()
            } else {
                this.handleTemplateEdit()
            }
        })
    }

    // 添加模版
    async handleTemplateAdd() {
        const params = this.printData
        await apiAddTemplate({ ...params })
        this.$router.back()
    }

    // 编辑模版
    handleTemplateEdit() {
        const params = this.printData
        const id = this.identity
        apiEditTemplate({ ...params, id })
            .then(() => {
                setTimeout(() => this.$router.go(-1), 500)
            })
            .catch(() => {
                this.$message.error('编辑失败!')
            })
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        const query: any = this.$route.query
        this.identity = query.id
        if (this.identity) {
            this.getTemplateDetail()
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

.title {
    font-size: 14px;
    padding-bottom: 14px;
    margin-bottom: 14px;
    border-bottom: 1px solid #e5e5e5;
}

.phone {
    margin: 0 auto;
    width: 300px;
    padding: 10px 0;
    border: 1px solid #e5e5e5;
    .bb {
        border-bottom: 1px dashed #e5e5e5;
    }
}
</style>
