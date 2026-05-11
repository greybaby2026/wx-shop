<template>
    <div class="ls-release">
        <el-form label-width="120px" :model="form" :rules="rules" size="small" class="ls-form" ref="form">
            <div class="ls-card ls-release__header">
                <el-page-header @back="$router.go(-1)" :content="id ? '编辑商品' : '新增商品'"></el-page-header>
            </div>
            <div class="ls-card ls-release__content m-t-16" v-loading="loading">
                <el-tabs v-model="activeName">
                    <el-tab-pane label="基础设置" name="basic">
                        <add-basic v-model="form" :lists="otherList" @refresh="getGoodsOtherList" />
                    </el-tab-pane>
                    <el-tab-pane label="价格库存" name="price">
                        <add-price v-model="specData" />
                    </el-tab-pane>
                    <el-tab-pane :label="form.type == 1 ? '物流设置' : '发货设置'" name="logistics">
                        <add-logistics lazy :lists="otherList" v-model="form" />
                    </el-tab-pane>
                    <el-tab-pane label="商品详情" name="details">
                        <add-details lazy v-model="form" />
                    </el-tab-pane>
                    <el-tab-pane label="销售设置" name="sales">
                        <add-sales lazy v-model="form" :isGatherGoods="!isGatherGoods" />
                    </el-tab-pane>
                </el-tabs>
            </div>
            <div class="ls-release__footer bg-white ls-fixed-footer">
                <div class="btns row-center flex" style="height: 100%">
                    <el-button v-if="activeName != 'basic'" size="small" @click="onNextStep(false)">上一步</el-button>
                    <el-button v-if="activeName == 'sales'" size="small" type="primary" @click="handleSave"
                        >保存</el-button
                    >
                    <el-button v-if="activeName != 'sales'" size="small" type="primary" @click="onNextStep"
                        >下一步</el-button
                    >
                </div>
            </div>
        </el-form>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import AddBasic from '@/components/goods/add-basic.vue'
import AddPrice from '@/components/goods/add-price.vue'
import AddDetails from '@/components/goods/add-details.vue'
import AddSales from '@/components/goods/add-sales.vue'
import AddLogistics from '@/components/goods/add-logistics.vue'
import { apiGoodsAdd, apiGoodsDetail, apiGoodsEdit, apiGoodsOtherList } from '@/api/goods'
import { apiGatherGoodsDetails, apiGatherGoodsEdit } from '@/api/application/goods_collection'
@Component({
    components: {
        AddBasic,
        AddPrice,
        AddDetails,
        AddSales,
        AddLogistics
    }
})
export default class GoodsRelease extends Vue {
    $refs!: { form: any }
    // 商品id
    id!: any
    isGatherGoods!: any
    loading = true
    activeName = 'basic'
    otherList = {
        supplier_list: [],
        category_list: [],
        brand_list: [],
        unit_list: [],
        freight_list: []
    }
    // 必传字段与对应的选项卡名字
    requireFields = [
        {
            fields: ['code', 'name', 'category_id', 'goods_image'],
            activeName: 'basic'
        },
        {
            fields: ['spec_value_list[0].sell_price', 'spec_value_list[0].stock'],
            activeName: 'price'
        },
        {
            fields: ['express_money', 'express_template_id', 'delivery_content'],
            activeName: 'logistics'
        }
    ]
    // 规格绑定的数据
    specData: any = {
        spec_type: 1,
        spec_value: [],
        spec_value_list: [],
        specs_single: {}
    }
    // 表单
    form: any = {
        type: 1,
        is_virtualdelivery: 1,
        after_pay: 1,
        after_delivery: 1,
        delivery_content: '',
        delivery_template_id: '',
        name: '',
        code: '',
        category_id: [],
        express_type: 1,
        express_money: '',
        express_template_id: '',
        video_source: 1,
        video_cover: '',
        video: '',
        supplier_id: '',
        brand_id: '',
        unit_id: '',
        poster: '',
        is_express: 1,
        is_selffetch: 1,
        delivery_type: 0,
        goods_image: [],
        spec_value_list: [
            {
                id: '',
                image: '',
                sell_price: '',
                lineation_price: '',
                cost_price: '',
                stock: '',
                volume: '',
                weight: '',
                bar_code: ''
            }
        ],
        stock_warning: '',
        virtual_sales_num: '',
        status: 0,
        limit_type: 1,
        limit_value: '',
        content: '',
        is_address: 0
    }
    // 规则验证
    rules = {
        name: [
            {
                required: true,
                message: '请输入商品名称',
                trigger: ['blur', 'change']
            }
        ],
        code: [
            {
                required: true,
                message: '请输入商品编码',
                trigger: ['blur', 'change']
            }
        ],
        category_id: [
            {
                type: 'array',
                required: true,
                message: '请选择分类',
                trigger: ['blur', 'change']
            }
        ],
        goods_image: [
            {
                type: 'array',
                required: true,
                message: '请添加商品轮播图',
                trigger: ['blur', 'change']
            }
        ],
        video: [
            {
                required: true,
                message: '请添加视频',
                trigger: ['blur', 'change']
            }
        ],
        express_money: [
            {
                required: true,
                message: '请输入运费金额',
                trigger: ['blur', 'change']
            }
        ],
        express_template_id: [
            {
                required: true,
                message: '请选择运费模板',
                trigger: ['blur', 'change']
            }
        ],
        delivery_content: [
            {
                required: true,
                message: '请输入发货内容',
                trigger: ['blur', 'change']
            }
        ],
        delivery_type: [
            {
                required: true,
                message: '请选择发货类型',
                trigger: ['blur', 'change']
            }
        ]
    }

    @Watch('form', { deep: true })
    formChange(val: any) {}
    @Watch('specData', { deep: true })
    specDataChange(val: any) {
        this.form.spec_type = val.spec_type
        // console.log(11111)
        if (val.spec_type == 1) {
            this.form.spec_value_list = [val.specs_single]
            this.form.spec_value = []
            return
        }

        this.form.spec_value_list = val.spec_value_list.map((item: any) => {
            return {
                ...item,
                id: item.id || '',
                image: item.image || '',
                sell_price: item.sell_price !== undefined ? item.sell_price : '',
                lineation_price: item.lineation_price !== undefined ? item.lineation_price : '',
                cost_price: item.cost_price !== undefined ? item.cost_price : '',
                stock: item.stock !== undefined ? item.stock : '',
                volume: item.volume !== undefined ? item.volume : '',
                weight: item.weight !== undefined ? item.weight : '',
                bar_code: item.bar_code !== undefined ? item.bar_code : ''
            }
        })
        this.form.spec_value = val.spec_value
    }
    // methods
    getGoodsDetail() {
        apiGoodsDetail(this.id)
            .then((res: any) => {
                this.initData(res)
                this.form = res
            })
            .catch(() => {
                this.$router.back()
            })
    }

    getGoodsCollectionDetail() {
        apiGatherGoodsDetails(this.id)
            .then((res: any) => {
                this.initData(res)
                this.form = res
                if (this.form.poster === null) {
                    this.form.poster = ''
                }
                if (this.form.video_source === null) {
                    this.form.video_source = 1
                }
            })
            .catch(() => {
                this.$router.back()
            })
    }

    onNextStep(isNext = true) {
        switch (this.activeName) {
            case 'basic':
                this.activeName = 'price'
                break
            case 'price':
                this.activeName = isNext ? 'logistics' : 'basic'
                break
            case 'logistics':
                this.activeName = isNext ? 'details' : 'price'
                break
            case 'details':
                this.activeName = isNext ? 'sales' : 'logistics'
                break
            case 'sales':
                this.activeName = 'details'
                break
        }
    }

    handleSave() {
        if (this.form.spec_type == 2) {
            this.form.spec_value_list = this.specData.spec_value_list.map((item: any) => {
                return {
                    ...item,
                    id: item.id || '',
                    image: item.image || '',
                    sell_price: item.sell_price !== undefined ? item.sell_price : '',
                    lineation_price: item.lineation_price !== undefined ? item.lineation_price : '',
                    cost_price: item.cost_price !== undefined ? item.cost_price : '',
                    stock: item.stock !== undefined ? item.stock : '',
                    volume: item.volume !== undefined ? item.volume : '',
                    weight: item.weight !== undefined ? item.weight : '',
                    bar_code: item.bar_code !== undefined ? item.bar_code : ''
                }
            })
            this.form.spec_value = this.specData.spec_value
        }

        this.$refs.form.validate((valid: boolean, object: any) => {
            if (valid) {
                if (this.form.spec_type == 2) {
                    const hasEmptyPrice = this.form.spec_value_list.some(
                        (item: any) =>
                            item.sell_price === '' ||
                            item.sell_price === undefined ||
                            item.sell_price === null ||
                            String(item.sell_price).trim() === ''
                    )
                    if (hasEmptyPrice || this.form.spec_value_list.length === 0) {
                        this.$message.error('请输入规格明细的商品价格')
                        this.activeName = 'price'
                        return
                    }
                    const hasEmptyStock = this.form.spec_value_list.some(
                        (item: any) =>
                            item.stock === '' ||
                            item.stock === undefined ||
                            item.stock === null ||
                            String(item.stock).trim() === ''
                    )
                    if (hasEmptyStock || this.form.spec_value_list.length === 0) {
                        this.$message.error('请输入规格明细的库存')
                        this.activeName = 'price'
                        return
                    }
                }
                const loading = this.$loading({
                    lock: true,
                    text: '保存中...',
                    spinner: 'el-icon-loading'
                })
                let api = null
                if (this.isGatherGoods) {
                    api = apiGatherGoodsEdit(this.form)
                } else {
                    api = this.id ? apiGoodsEdit(this.form) : apiGoodsAdd(this.form)
                }
                api.then(() => {
                    this.$router.go(-1)
                }).finally(() => {
                    loading.close()
                })
            } else {
                const fieldsitem = this.requireFields.find(item => {
                    for (const value of item.fields) {
                        if (object[value]) {
                            this.$message.error(object[value][0].message)
                            return true
                        }
                    }
                })
                fieldsitem && (this.activeName = fieldsitem.activeName)
                return false
            }
        })
    }
    // 初始化规格数据
    initData(data: any = {}) {
        const spec_value = [
            {
                has_image: false,
                id: '',
                name: '',
                spec_list: [
                    {
                        id: '',
                        value: '',
                        image: ''
                    }
                ]
            }
        ]
        const spec_value_list = [
            {
                id: '',
                image: '',
                sell_price: '',
                lineation_price: '',
                cost_price: '',
                stock: '',
                volume: '',
                weight: '',
                bar_code: ''
            }
        ]

        const specData: any = {
            spec_value: data.spec_value || spec_value,
            spec_value_list: data.spec_value_list || spec_value_list,
            spec_type: data.spec_type || 1,
            specs_single: spec_value_list[0]
        }
        specData.spec_value.forEach((item: any) => {
            item.has_image = false
            item.spec_list.forEach((sitem: any) => {
                sitem.image = ''
            })
        })

        if (data.spec_type == 1) {
            specData.spec_value = spec_value
            specData.specs_single = data.spec_value_list[0]
        }
        Object.assign(this.specData, specData)
        this.loading = false
    }

    getGoodsOtherList() {
        apiGoodsOtherList({
            type: 'all'
        }).then((res: any) => {
            this.otherList = res
        })
    }
    created() {
        this.id = this.$route.query.id
        this.isGatherGoods = this.$route.query.type || ''

        this.getGoodsOtherList()
        if (this.isGatherGoods) {
            this.getGoodsCollectionDetail()
            return
        }
        if (this.id) {
            this.getGoodsDetail()
            return
        }
        this.initData()
    }
}
</script>
<style lang="scss" scoped>
.ls-release {
    padding-bottom: 80px;
    &__content {
        padding-top: 0;
    }
}
</style>
