<template>
    <div class="ls-seckill-edit">
        <div class="ls-card ls-seckill-edit__header">
            <el-page-header
                @back="$router.go(-1)"
                :content="id ? (disabled ? '预售活动详情' : '编辑预售活动') : '新增预售活动'"
            ></el-page-header>
        </div>
        <div class="ls-seckill-content">
            <el-form v-loading="loading" ref="form" :model="formData" label-width="120px" size="small" :rules="rules">
                <div class="ls-card m-t-16">
                    <div class="nr weight-500 m-b-20">活动设置</div>
                    <el-form-item label="活动名称" prop="name">
                        <el-input
                            class="ls-input"
                            v-model="formData.name"
                            :disabled="disabled || formData.status != 0"
                            placeholder="请输入活动名称"
                        ></el-input>
                    </el-form-item>
                    <el-form-item label="预售类型" required>
                        <el-radio-group :disabled="disabled || formData.status != 0" v-model="formData.type">
                            <el-radio :label="0">全款预售</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="活动时间" prop="start_time">
                        <date-picker-new
                            :disabled="disabled || formData.status != 0"
                            type="datetimerange"
                            :start-time.sync="formData.start_time"
                            :end-time.sync="formData.end_time"
                        />
                        <div class="muted">预售活动开始和结束时间，可以手动提前结束活动</div>
                    </el-form-item>
                    <el-form-item label="活动备注">
                        <el-input
                            class="ls-input"
                            type="textarea"
                            :rows="5"
                            v-model="formData.remark"
                            placeholder=""
                            :disabled="disabled || formData.status != 0"
                        ></el-input>
                    </el-form-item>
                </div>
                <div class="ls-card m-t-16">
                    <div class="nr weight-500 m-b-20">活动商品</div>

                    <el-form-item label="预售商品" required>
                        <goods-select
                            v-model="selectGoods"
                            :disabled="disabled || formData.status != 0"
                            mode="table"
                            :is-spec="true"
                            :limit="25"
                            :extend="{
                                name: '预售',
                                price: [
                                    {
                                        title: '预售价格',
                                        key: 'price'
                                    }
                                ]
                            }"
                        >
                            <el-button size="mini" type="primary">选择商品</el-button>
                        </goods-select>
                    </el-form-item>
                </div>
                <div class="ls-card m-t-16">
                    <div class="nr weight-500 m-b-20">活动规则</div>
                    <el-form-item label="发货时间" required>
                        <el-select style="width: 150px" v-model="formData.send_type">
                            <el-option :value="0" label="支付成功">支付成功</el-option>
                            <el-option :value="1" label="预售结束">预售结束</el-option>
                        </el-select>
                        <el-input
                            class="ls-input m-l-10"
                            v-model="formData.send_type_day"
                            placeholder="请输入"
                            style="width: 200px"
                        >
                            <template slot="append">天内</template>
                        </el-input>
                    </el-form-item>
                    <el-form-item label="每单限制" required>
                        <el-radio-group :disabled="formData.status != 0" v-model="formData.buy_limit">
                            <el-radio :label="0">不限制</el-radio>
                            <el-radio :label="1">限制</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item prop="buy_limit_num" v-if="formData.buy_limit !== 0" label="">
                        <el-input
                            v-model="formData.buy_limit_num"
                            :disabled="formData.status != 0"
                            @input="handleInput($event, 'max_buy')"
                            type="number"
                        >
                            <template slot="append">件</template>
                        </el-input>
                        <div class="muted">每件商品单笔订单最多购买的件数。</div>
                    </el-form-item>
                </div>
            </el-form>
        </div>

        <div class="ls-seckill-edit__footer bg-white ls-fixed-footer">
            <div class="btns row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" :disabled="disabled" @click="handleSave">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import MaterialSelect from '@/components/material-select/index.vue'
import GoodsSelect from '@/components/presellgoods-select/index.vue'
import DatePickerNew from '@/components/date-picker-new.vue'
import { apiPreSellAdd, apiPreSellDetail, apiPresellEdit } from '@/api/marketing/presell'
@Component({
    components: {
        MaterialSelect,
        DatePickerNew,
        GoodsSelect
    }
})
export default class AddBrand extends Vue {
    $refs!: { form: any }
    id!: any
    loading = false
    disabled = false
    formData: any = {
        name: '',
        start_time: '',
        status: 0,
        type: 0,
        end_time: '',
        goods: [],
        max_buy: 0,
        send_type: '',
        buy_limit: 0,
        buy_limit_num: '',
        send_type_day: ''
    }
    selectGoods = []
    rules = {
        name: [
            {
                required: true,
                message: '请输入活动名称',
                trigger: ['blur']
            }
        ],
        start_time: [
            {
                required: true,
                message: '选择活动时间',
                trigger: 'blur'
            }
        ],

        max_buy: [
            {
                required: true,
                message: '请输入每单限制数量',
                trigger: 'blur'
            }
        ]
    }
    @Watch('selectGoods', { deep: true })
    selectGoodsChange(val: any[]) {
        console.log(val, 123)

        this.formData.goods = val.map((item: any) => {
            return {
                goods_id: item.id,
                items: item.item.map((sitem: any) => ({
                    item_id: sitem.id,
                    price: sitem.price
                })),
                virtual_click: item.virtual_click,
                virtual_sale: item.virtual_sale
            }
        })
    }
    checkGoods() {
        const goods = this.formData.goods
        console.log(goods)

        if (!goods.length) {
            this.$message.error('请选择预售商品')
            return false
        }

        for (let i = 0; i < goods.length; i++) {
            for (let j = 0; j < goods[i].items.length; j++) {
                if (!goods[i].items[j].price) {
                    this.$message.error(`请输入商品预售价`)
                    return false
                }
            }
        }
        return true
    }
    handleInput(val: any, type: string) {
        if (val <= 0 && val !== '') {
            this.formData[type] = 1
        }
    }
    handleSave() {
        console.log(this.formData)

        this.$refs.form.validate((valid: boolean, object: any) => {
            if (valid) {
                if (!this.checkGoods()) {
                    return
                }
                const api = this.id ? apiPresellEdit(this.formData) : apiPreSellAdd(this.formData)
                api.then(() => {
                    this.$router.go(-1)
                })
            } else {
                return false
            }
        })
    }

    getDetail() {
        this.loading = true
        apiPreSellDetail({ id: this.id }).then((res: any) => {
            this.formData = res
            this.selectGoods = res.goods
            console.log(this.selectGoods, '123123')
            this.loading = false
        })
    }

    created() {
        this.id = this.$route.query.id
        this.disabled = Boolean(this.$route.query.disabled)
        this.id && this.getDetail()
    }
}
</script>

<style lang="scss" scoped>
.ls-seckill-edit {
    padding-bottom: 80px;
    .ls-seckill-content {
        .ls-card {
            padding-bottom: 6px;
        }
    }

    .ls-input {
        width: 400px;
    }
}
</style>
