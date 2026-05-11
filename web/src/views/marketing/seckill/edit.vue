<template>
    <div class="ls-seckill-edit">
        <div class="ls-card ls-seckill-edit__header">
            <el-page-header
                @back="$router.go(-1)"
                :content="id ? (disabled ? '秒杀活动详情' : '编辑秒杀活动') : '新增秒杀活动'"
            ></el-page-header>
        </div>
        <div class="ls-seckill-content">
            <el-form
                v-loading="loading"
                ref="form"
                :model="formData"
                label-width="120px"
                size="small"
                :rules="rules"
                :disabled="disabled"
            >
                <div class="ls-card m-t-16">
                    <div class="nr weight-500 m-b-20">活动设置</div>
                    <el-form-item label="活动名称" prop="name">
                        <el-input class="ls-input" v-model="formData.name" placeholder="请输入活动名称"></el-input>
                    </el-form-item>
                    <el-form-item label="活动时间" prop="start_time">
                        <date-picker-new
                            :disabled="formData.status != 1"
                            type="datetimerange"
                            :start-time.sync="formData.start_time"
                            :end-time.sync="formData.end_time"
                        />
                        <div class="muted">秒杀活动开始和结束时间，可以手动提前结束活动</div>
                    </el-form-item>
                    <el-form-item label="活动备注">
                        <el-input
                            class="ls-input"
                            type="textarea"
                            :rows="5"
                            v-model="formData.explain"
                            placeholder=""
                        ></el-input>
                    </el-form-item>
                </div>
                <div class="ls-card m-t-16">
                    <div class="nr weight-500 m-b-20">活动商品</div>

                    <el-form-item label="秒杀商品" required>
                        <goods-select
                            v-model="selectGoods"
                            :disabled="disabled || formData.status != 1"
                            mode="table"
                            :is-spec="true"
                            :limit="25"
                            :extend="{
                                name: '秒杀',
                                price: [
                                    {
                                        title: '秒杀价格',
                                        key: 'seckill_price'
                                    }
                                ]
                            }"
                        >
                            <el-button size="mini" type="primary">选择秒杀商品</el-button>
                        </goods-select>
                    </el-form-item>
                </div>
                <div class="ls-card m-t-16">
                    <div class="nr weight-500 m-b-20">活动规则</div>
                    <el-form-item label="每单限制" required>
                        <el-radio-group :disabled="formData.status != 1" v-model="formData.max_buy">
                            <el-radio :label="0">不限制</el-radio>
                            <el-radio :label="formData.max_buy != 0 ? formData.max_buy : ''">限制</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item prop="max_buy" v-if="formData.max_buy !== 0" label="">
                        <el-input
                            v-model="formData.max_buy"
                            :disabled="formData.status != 1"
                            @input="handleInput($event, 'max_buy')"
                            type="number"
                        ></el-input>
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
import GoodsSelect from '@/components/goods-select/index.vue'
import DatePickerNew from '@/components/date-picker-new.vue'
import { apiSeckillAdd, apiSeckillDetail, apiSeckillEdit } from '@/api/marketing/seckill'
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
        explain: '',
        start_time: '',
        status: 1,
        end_time: '',
        goods: [],
        max_buy: 0
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
        this.formData.goods = val.map((item: any) => {
            return {
                goods_id: item.id,
                items: item.item.map((sitem: any) => ({
                    item_id: sitem.id,
                    seckill_price: sitem.seckill_price
                })),
                virtual_click_num: item.virtual_click_num,
                virtual_sales_num: item.virtual_sales_num
            }
        })
    }
    checkGoods() {
        const goods = this.formData.goods
        if (!goods.length) {
            this.$message.error('请选择秒杀商品')
            return false
        }
        for (let i = 0; i < goods.length; i++) {
            for (let j = 0; j < goods[i].items.length; j++) {
                if (!goods[i].items[j].seckill_price) {
                    this.$message.error(`请输入商品秒杀价`)
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
        this.$refs.form.validate((valid: boolean, object: any) => {
            if (valid) {
                if (!this.checkGoods()) {
                    return
                }
                const api = this.id ? apiSeckillEdit(this.formData) : apiSeckillAdd(this.formData)
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
        apiSeckillDetail({ id: this.id }).then((res: any) => {
            this.formData = res
            this.selectGoods = res.goods
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
