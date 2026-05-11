<template>
    <div class="ls-goods">
        <div class="ls-goods__top ls-card">
            <el-alert
                title="温馨提示：1.新增商品时可选统一规格或者多规格，满足商品不同销售属性场景；2.商品销售状态分为销售中且库存足够时才可下单购买。"
                type="info"
                show-icon
                :closable="false"
            >
            </el-alert>
            <div class="goods-search m-t-16">
                <el-form class="ls-form" ref="form" inline :model="queryObj" label-width="80px" size="small">
                    <el-form-item label="商品搜索">
                        <el-input v-model="queryObj.keyword" placeholder="请输入商品名称/编码"></el-input>
                    </el-form-item>
                    <el-form-item label="条形码">
                        <el-input v-model="queryObj.bar_code" placeholder="请输入商品条形码"></el-input>
                    </el-form-item>
                    <el-form-item label="商品分类">
                        <el-cascader
                            v-model="queryObj.category_id"
                            :options="categoryList"
                            :props="{
                                multiple: false,
                                checkStrictly: true,
                                label: 'name',
                                value: 'id',
                                children: 'sons',
                                emitPath: false
                            }"
                            clearable
                            filterable
                        ></el-cascader>
                    </el-form-item>
                    <el-form-item label="商品类型">
                        <el-select v-model="queryObj.goods_type" placeholder="请选择商品类型">
                            <el-option label="全部" value=""></el-option>
                            <el-option
                                v-for="(item, key) in typeList"
                                :key="key"
                                :label="item"
                                :value="key"
                            ></el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="供应商">
                        <el-select v-model="queryObj.supplier_id" placeholder="请选择供应商">
                            <el-option label="全部" value=""></el-option>
                            <el-option
                                v-for="item in supplierList"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id"
                            ></el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="参与活动">
                        <el-select v-model="queryObj.activity_type" placeholder="请选择活动类型">
                            <el-option label="全部" value=""></el-option>
                            <el-option
                                v-for="(item, key) in activityList"
                                :key="key"
                                :label="item"
                                :value="key"
                            ></el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="" class="m-l-6">
                        <el-button size="small" type="primary" @click="getList(1)">查询</el-button>
                        <el-button size="small" @click="handleReset">重置</el-button>
                        <export-data
                            class="m-l-10"
                            :pageSize="pager.size"
                            :method="apiGoodsLists"
                            :param="queryObj"
                            :status="{ type: activeStatus }"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <div class="ls-goods__content ls-card m-t-16">
            <el-tabs v-model="activeName" v-loading="pager.loading" @tab-click="getList(1)">
                <el-tab-pane :label="`全部(${tabCount.all_count})`" name="all">
                    <goods-pane
                        v-model="pager.lists"
                        :pager="pager"
                        :categoryList="categoryList"
                        @refresh="getList()"
                    />
                </el-tab-pane>
                <el-tab-pane lazy :label="`销售中(${tabCount.sales_count})`" name="sale">
                    <goods-pane
                        v-model="pager.lists"
                        :pager="pager"
                        :categoryList="categoryList"
                        @refresh="getList()"
                    />
                </el-tab-pane>
                <el-tab-pane lazy :label="`库存预警(${tabCount.warning_count})`" name="inventory">
                    <goods-pane
                        v-model="pager.lists"
                        :pager="pager"
                        :categoryList="categoryList"
                        @refresh="getList()"
                    />
                </el-tab-pane>
                <el-tab-pane lazy :label="`已售罄(${tabCount.sellout_count})`" name="soldout">
                    <goods-pane
                        v-model="pager.lists"
                        :pager="pager"
                        :categoryList="categoryList"
                        @refresh="getList()"
                    />
                </el-tab-pane>
                <el-tab-pane lazy :label="`仓库中(${tabCount.storage_count})`" name="warehouse">
                    <goods-pane
                        v-model="pager.lists"
                        :pager="pager"
                        :categoryList="categoryList"
                        @refresh="getList()"
                    />
                </el-tab-pane>
            </el-tabs>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import GoodsPane from '@/components/goods/goods-pane.vue'
import ExportData from '@/components/export-data/index.vue'
import { RequestPaging } from '@/utils/util'
import { apiGoodsLists, apiGoodsOtherList } from '@/api/goods'
import { GoodsType } from '@/utils/type'
@Component({
    components: {
        GoodsPane,
        ExportData
    }
})
export default class Goods extends Vue {
    queryObj = {
        keyword: '',
        category_id: '',
        supplier_id: '',
        goods_type: '',
        activity_type: '',
        bar_code: ''
    }
    supplierList: any[] = []
    categoryList: any[] = []
    typeList: any = []
    activityList: any = []
    tabCount = {
        all_count: 0,
        sales_count: 0,
        sellout_count: 0,
        storage_count: 0,
        warning_count: 0
    }
    pager = new RequestPaging()
    activeName: any = 'all'
    activeStatus: any = ''
    apiGoodsLists = apiGoodsLists
    getList(page?: number): void {
        this.activeStatus = GoodsType[this.activeName]
        page && (this.pager.page = page)
        this.pager
            .request({
                callback: apiGoodsLists,
                params: {
                    type: GoodsType[this.activeName],
                    ...this.queryObj
                }
            })
            .then((res: any) => {
                this.tabCount = res?.extend
            })
    }
    handleReset() {
        this.queryObj = {
            keyword: '',
            category_id: '',
            supplier_id: '',
            goods_type: '',
            activity_type: '',
            bar_code: ''
        }
        this.getList()
    }

    getGoodsOtherList() {
        apiGoodsOtherList({
            type: 'list'
        }).then((res: any) => {
            this.categoryList = res?.category_list
            this.supplierList = res?.supplier_list
            this.typeList = res?.type_list
            this.activityList = res?.activity_list
        })
    }
    created() {
        this.getGoodsOtherList()
        this.getList()
    }
    activated() {
        this.getGoodsOtherList()
        this.getList()
    }
}
</script>

<style lang="scss" scoped>
.ls-goods {
    &__top {
        padding-bottom: 0px;
    }
    .goods-search {
        .ls-input-price {
            width: 180px;
        }
    }
    .ls-goods__content {
        padding-top: 0;
    }
}
</style>
