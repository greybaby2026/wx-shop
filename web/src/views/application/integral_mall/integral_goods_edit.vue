<template>
    <div class="integral-goods-edit">
        <!-- Header -->
        <div class="ls-card">
            <el-page-header @back="$router.back()" :content="pageTitle" />
        </div>
        <div class="ls-card m-t-16" style="padding-top: 0" v-loading="loading">
            <el-tabs value="basic">
                <el-tab-pane label="基本信息" name="basic">
                    <el-form ref="formRef" :model="formData" :rules="rules" label-width="120px" size="small">
                        <el-form-item label="兑换类型" required>
                            <div class="flex exchange-type">
                                <div
                                    v-for="(item, index) in exchangeType"
                                    :key="index"
                                    class="flex"
                                    :class="{
                                        active: item.value == formData.type,
                                        disabled: identity
                                    }"
                                    @click="!identity && (formData.type = item.value)"
                                >
                                    <i class="iconfont" :class="[item.icon]"></i>
                                    <span class="m-l-4">{{ item.label }}</span>
                                </div>
                            </div>
                        </el-form-item>
                        <el-form-item label="商品名称" prop="name">
                            <el-input v-model="formData.name" placeholder="请输入商品名称"></el-input>
                        </el-form-item>
                        <el-form-item label="商品编号" v-if="formData.type == 1">
                            <el-input v-model="formData.code" placeholder="请输入商品编号"></el-input>
                        </el-form-item>
                        <el-form-item label="商品封面" prop="image">
                            <material-select v-model="formData.image" />
                            <div class="muted">建议尺寸：800*800</div>
                        </el-form-item>
                        <el-form-item label="市场价">
                            <el-input v-model="formData.market_price" type="number" placeholder="请输入市场价">
                                <template slot="append">元</template>
                            </el-input>
                        </el-form-item>
                        <el-form-item label="发放库存" prop="stock">
                            <el-input v-model="formData.stock" type="number" placeholder="请输入发放库存"></el-input>
                        </el-form-item>
                        <el-form-item label="兑换方式" required v-if="formData.type == 1">
                            <el-radio-group v-model="formData.exchange_way">
                                <el-radio :label="1">积分</el-radio>
                                <el-radio :label="2">积分+余额</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label="兑换积分" prop="need_integral" class="inline">
                            <el-input v-model="formData.need_integral" type="number" placeholder="请输入">
                                <template slot="append">积分</template>
                            </el-input>
                        </el-form-item>
                        <template v-if="formData.exchange_way == 2 && formData.type == 1">
                            <span class="p-10"> + </span>
                            <el-form-item prop="need_money" class="inline" label-width="0">
                                <el-input v-model="formData.need_money" type="number" placeholder="请输入">
                                    <template slot="append">元</template>
                                </el-input>
                            </el-form-item>
                        </template>

                        <el-form-item label="物流配送" required v-if="formData.type == 1">
                            <el-radio-group v-model="formData.delivery_way">
                                <el-radio :label="1">快递</el-radio>
                                <el-radio :label="0">无需物流</el-radio>
                            </el-radio-group>
                            <div class="muted">
                                修改后的配送状态仅对后面的订单生效，对前面已经付款的订单没影响，会按照订单当时的的设置来进行操作
                            </div>
                        </el-form-item>
                        <el-form-item label="快递运费" required v-if="formData.type == 1 && formData.delivery_way == 1">
                            <el-radio-group v-model="formData.express_type">
                                <el-radio :label="1">包邮</el-radio>
                                <el-radio :label="2">统一运费</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item v-if="formData.express_type == 2 && formData.type == 1" prop="express_money">
                            <el-input v-model="formData.express_money" type="number" placeholder="请输入">
                                <template slot="append">元</template>
                            </el-input>
                        </el-form-item>
                        <el-form-item label="红包面值" prop="balance" v-if="formData.type == 2">
                            <el-input v-model="formData.balance" type="number" placeholder="请输入红包面值"></el-input>
                            <div class="muted">兑换的红包会以余额的形式发放</div>
                        </el-form-item>
                        <el-form-item label="排序">
                            <el-input v-model="formData.sort" type="number" placeholder="请输入"></el-input>
                            <div class="muted">默认值为0,数值越大越排前</div>
                        </el-form-item>
                        <el-form-item label="商品状态">
                            <el-switch v-model="formData.status" :active-value="1" :inactive-value="0"> </el-switch>
                        </el-form-item>
                    </el-form>
                </el-tab-pane>
                <el-tab-pane label="商品详情" name="detail">
                    <editor v-model="formData.content" />
                </el-tab-pane>
            </el-tabs>
        </div>
        <!-- Footer -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.back()">取消</el-button>
                <el-button size="small" type="primary" @click="onSubmit"> 保存 </el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import MaterialSelect from '@/components/material-select/index.vue'
import Editor from '@/components/editor.vue'
import { apiIntegralGoodsAdd, apiIntegralGoodsDetail, apiIntegralGoodsEdit } from '@/api/application/integral_mall'
@Component({
    components: {
        MaterialSelect,
        Editor
    }
})
export default class IntegralGoodsEdit extends Vue {
    /** S Data **/
    loading = false
    identity: number | null = null
    exchangeType = [
        {
            icon: 'icon_goods',
            label: '商品',
            value: 1
        },
        {
            icon: 'icon_xycj_cj',
            label: '红包',
            value: 2
        }
    ]
    // 添加客服表单数据
    formData = {
        type: 1, //兑换类型； 1商品 2-红包
        name: '', //商品名称
        code: '', //商品编号
        image: '', //商品封面
        market_price: '', //市场价
        stock: '', //发放库存
        exchange_way: 1, //兑换方式 1-积分 2-积分加余额；
        need_integral: '', //所需积分
        need_money: '', //所需金额
        delivery_way: 1, //配送方式 0-无需物流 1-快递 (类型为商品时必填)
        express_type: 1, //运费类型：1-包邮,2-统一运费 (配送方式为快递时必填)
        express_money: '', //统一运费金额 （运费类型为统一运费时必填）
        sort: 0, //排序
        status: 1, //商品状态
        content: '', //商品内容详情
        balance: '' // 红包金额
    }

    // 表单校验
    rules = {
        name: [
            {
                required: true,
                message: '请输入商品名称',
                trigger: ['blur', 'change']
            }
        ],
        image: [
            {
                required: true,
                message: '请输入选择商品封面',
                trigger: ['blur', 'change']
            }
        ],
        stock: [
            {
                required: true,
                message: '请输入发放库存',
                trigger: ['blur', 'change']
            }
        ],
        need_integral: [
            {
                required: true,
                message: '请输入兑换积分',
                trigger: ['blur', 'change']
            }
        ],
        need_money: [
            {
                required: true,
                message: '请输入兑换金额',
                trigger: ['blur', 'change']
            }
        ],
        balance: [
            {
                required: true,
                message: '请输入红包面值',
                trigger: ['blur', 'change']
            }
        ],
        express_money: [
            {
                required: true,
                message: '请输入运费',
                trigger: ['blur', 'change']
            }
        ]
    }

    /** E Data **/
    get pageTitle() {
        if (this.identity) {
            return '编辑积分商品'
        }
        return '新增积分商品'
    }

    // 点击表单提交
    onSubmit() {
        // 验证表单格式是否正确
        const refs = this.$refs.formRef as HTMLFormElement
        refs.validate((valid: boolean): any => {
            if (!valid) {
                return
            }

            // 请求发送
            const request = this.identity
                ? apiIntegralGoodsEdit({ id: this.identity, ...this.formData })
                : apiIntegralGoodsAdd(this.formData)
            request.then(data => {
                setTimeout(() => {
                    this.$router.back()
                }, 1000)
            })
        })
    }

    // 获取详情
    getDetails() {
        this.loading = true
        apiIntegralGoodsDetail({
            id: this.identity as number
        })
            .then(res => {
                Object.keys(this.formData).map(key => {
                    this.$set(this.formData, key, res[key])
                })
            })
            .finally(() => {
                this.loading = false
            })
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        const query: any = this.$route.query
        this.identity = query.id
        if (!this.identity) {
            return
        }
        this.getDetails()
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.integral-goods-edit {
    .exchange-type {
        & > div {
            padding: 8px 15px;
            border: $--border-base;
            line-height: 1.3;
            margin-right: 15px;
            border-radius: 4px;
            cursor: pointer;
            &.active {
                color: $--color-primary;
                border-color: currentColor;
            }
            &.disabled {
                cursor: not-allowed;
            }
        }
    }
}
</style>
