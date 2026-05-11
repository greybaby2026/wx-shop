<template>
    <div class="free-shipping-edit">
        <div class="ls-card free-shipping-edit__header">
            <el-page-header
                @back="$router.go(-1)"
                :content="id ? (disabled ? '包邮活动详情' : '编辑包邮活动') : '新增包邮活动'"
            ></el-page-header>
        </div>
        <div class="free-shipping-content">
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
                        <el-input
                            :disabled="formData.status == 2"
                            class="ls-input"
                            v-model="formData.name"
                            placeholder="请输入活动名称"
                        ></el-input>
                    </el-form-item>
                    <el-form-item label="活动时间" prop="start_time">
                        <date-picker-new
                            :disabled="formData.status != 0"
                            type="datetimerange"
                            :start-time.sync="formData.start_time"
                            :end-time.sync="formData.end_time"
                        />
                        <div class="muted">活动开始和结束时间，可以手动提前结束活动</div>
                    </el-form-item>
                </div>
                <div class="ls-card m-t-16">
                    <div class="nr weight-500 m-b-20">活动规则</div>

                    <el-form-item label="活动对象" required>
                        <el-radio-group :disabled="formData.status != 0" v-model="formData.target_user_type">
                            <el-radio :label="1">全部用户</el-radio>
                        </el-radio-group>
                        <div class="muted">全部用户都参与该场包邮活动</div>
                    </el-form-item>
                    <el-form-item label="活动对象" required>
                        <el-radio-group :disabled="formData.status != 0" v-model="formData.target_goods_type">
                            <el-radio :label="1">全部商品</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="活动规则" required>
                        <el-radio-group :disabled="formData.status != 0" v-model="formData.condition_type">
                            <el-radio :label="1">按订单实付金额包邮</el-radio>
                            <el-radio :label="2">按购买件数包邮</el-radio>
                        </el-radio-group>
                    </el-form-item>

                    <el-form-item>
                        <el-table :data="formData.region" style="width: 100%" size="mini">
                            <el-table-column label="配送区域" prop="region_name" min-width="250"> </el-table-column>

                            <el-table-column
                                min-width="150"
                                :label="formData.condition_type == 1 ? '订单金额' : '购买件数'"
                            >
                                <template slot-scope="scope">
                                    <div class="flex">
                                        <span> 满 </span>
                                        <el-input
                                            style="width: 170px"
                                            class="m-8"
                                            type="number"
                                            :disabled="formData.status != 0"
                                            v-model="scope.row.threshold"
                                        >
                                            <template slot="append">
                                                {{ formData.condition_type == 1 ? '元' : '件' }}
                                            </template>
                                        </el-input>
                                        <span> 包邮 </span>
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column fixed="right" label="操作" min-width="100">
                                <template slot-scope="scope" v-if="scope.$index > 0">
                                    <el-button type="text" size="small" @click="handleEdit(scope.row.area_id)">
                                        编辑
                                    </el-button>
                                    <el-button type="text" size="small" @click="handleDelete(scope.row.area_id)">
                                        删除
                                    </el-button>
                                </template>
                            </el-table-column>
                        </el-table>
                    </el-form-item>
                    <el-form-item>
                        <el-button
                            :disabled="disabled || formData.status != 0"
                            size="small"
                            type="primary"
                            @click="handleShowDelivery"
                        >
                            +添加规则
                        </el-button>
                    </el-form-item>
                </div>
            </el-form>
        </div>
        <delivery-area ref="deliveryArea" :area-id="areaId" :default-region="formData.region" @change="areaChange" />
        <div class="free-shipping-edit__footer bg-white ls-fixed-footer">
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
import DeliveryArea from '@/components/setting/delivery-area/index.vue'
import { apiFreeShippingAdd, apiFreeShippingDetail, apiFreeShippingEdit } from '@/api/marketing/free_shipping'
import { getNonDuplicateID } from '@/utils/util'
@Component({
    components: {
        MaterialSelect,
        DeliveryArea,
        DatePickerNew,
        GoodsSelect
    }
})
export default class AddBrand extends Vue {
    $refs!: { deliveryArea: any; form: any }
    id!: any
    loading = false
    disabled = false
    formData: any = {
        name: '',
        start_time: '',
        end_time: '',
        target_user_type: 1,
        target_goods_type: 1,
        condition_type: 1,
        status: 0,
        region: [
            {
                area_id: '',
                region_id: '100000',
                region_name: '全国统一运费',
                threshold: ''
            }
        ]
    }
    areaId = ''
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
    handleShowDelivery() {
        this.areaId = getNonDuplicateID()
        this.$refs.deliveryArea.show()
    }
    handleEdit(index: string) {
        this.areaId = index
        this.$refs.deliveryArea.show()
    }
    handleDelete(areaId: string) {
        this.areaId = areaId
        const index = this.formData.region.findIndex((item: any) => item.area_id == areaId)
        if (index != -1) {
            this.formData.region.splice(index, 1)
        }
        this.$nextTick(() => {
            this.$refs.deliveryArea.clearSelectArea()
        })
    }
    areaChange(value: any[]) {
        const index = this.formData.region.findIndex((item: any) => item.area_id == this.areaId)
        const region_id = value.map(item => item.value).join()
        const region_name = value.map(item => item.label).join('、')
        if (index != -1) {
            this.formData.region[index].region_id = region_id
            this.formData.region[index].region_name = region_name
            return
        }
        this.formData.region.push({
            area_id: this.areaId,
            region_id,
            region_name,
            threshold: ''
        })
    }
    handleInput(val: any, type: string) {
        if (val <= 0 && val !== '') {
            this.formData[type] = 1
        }
    }
    handleSave() {
        this.$refs.form.validate((valid: boolean, object: any) => {
            if (valid) {
                const api = this.id ? apiFreeShippingEdit(this.formData) : apiFreeShippingAdd(this.formData)
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
        apiFreeShippingDetail({ id: this.id }).then((res: any) => {
            this.formData = res
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
.free-shipping-edit {
    padding-bottom: 80px;
    .free-shipping-content {
        .ls-card {
            padding-bottom: 6px;
        }
    }

    .ls-input {
        width: 400px;
    }
}
</style>
