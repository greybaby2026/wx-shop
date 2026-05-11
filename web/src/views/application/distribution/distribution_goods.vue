<template>
    <div class="ls-goods">
        <div class="ls-goods__top ls-card">
            <el-alert title="温馨提示：1.设置分销商品的佣金比例。" type="info" show-icon :closable="false"> </el-alert>

            <div class="coupon-search m-t-16">
                <el-form ref="form" inline :model="goodsSearchData" label-width="100px" size="small">
                    <el-form-item label="商品信息">
                        <el-input style="width: 280px" v-model="goodsSearchData.name" placeholder="商品名称/ID/编码">
                        </el-input>
                    </el-form-item>

                    <el-form-item label="商品分类">
                        <el-cascader
                            class="header-input"
                            v-model="goodsSearchData.category_id"
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

                    <el-form-item label="分销状态">
                        <el-select v-model="goodsSearchData.is_distribution" placeholder="全部">
                            <el-option
                                v-for="item in options"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value"
                            >
                            </el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="商品状态">
                        <el-select v-model="goodsSearchData.status" placeholder="全部">
                            <el-option
                                v-for="item in goodsStatus"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value"
                            >
                            </el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="" class="m-l-6">
                        <el-button size="small" type="primary" @click="getDistributionData(1)">查询</el-button>
                        <el-button size="small" @click="resetgoodsSearchData">重置</el-button>

                        <export-data
                            class="m-l-10"
                            :pageSize="pager.size"
                            :method="apiDistributionGoodsLists"
                            :param="goodsSearchData"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="m-t-24 ls-card">
            <div class="m-b-24">
                <el-button type="primary" size="mini" @click="selectAll">全选当前页</el-button>

                <ls-dialog
                    class="inline m-l-24"
                    title="取消分销"
                    :content="`确定批量不参与吗?请谨慎操作`"
                    @confirm="couponDel('', 0)"
                >
                    <el-button slot="trigger" size="mini" type="primary">取消分销</el-button>
                </ls-dialog>

                <ls-dialog
                    class="inline m-l-24"
                    title="参与分销"
                    :content="`确定批量参与吗?请谨慎操作`"
                    @confirm="couponDel('', 1)"
                >
                    <el-button slot="trigger" size="mini" type="primary">参与分销</el-button>
                </ls-dialog>
            </div>

            <el-table
                ref="paneTable"
                :data="pager.lists"
                @selection-change="selectionChange"
                v-loading="pager.loading"
                style="width: 100%"
                size="mini"
            >
                <el-table-column fixed="left" type="selection" width="55"> </el-table-column>
                <el-table-column prop="name" label="商品名称" min-width="180">
                    <template slot-scope="scope">
                        <div class="flex">
                            <el-image class="flex-none" style="width: 58px; height: 58px" :src="scope.row.image" />
                            <div class="goods-info m-l-8">
                                <div class="line-2">{{ scope.row.name }}</div>
                                <div>
                                    <el-tag v-if="scope.row.spec_type == 2" size="mini">多规格</el-tag>
                                </div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="coupon_code" label="价格" min-width="180">
                    <template slot-scope="scope">
                        <span>{{ scope.row.price }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="status_desc" label="商品状态" min-width="180">
                    <template slot-scope="scope">
                        <span>{{ scope.row.status_desc }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="is_distribution" label="分销状态" min-width="180">
                    <template slot-scope="scope">
                        {{ scope.row.is_distribution == 1 ? '参与' : '不参与' }}
                    </template>
                </el-table-column>
                <el-table-column fixed="right" label="操作" min-width="180">
                    <template slot-scope="scope">
                        <el-button
                            slot="trigger"
                            class="m-r-10"
                            type="text"
                            size="small"
                            @click="
                                $router.push({
                                    path: '/distribution/distribution_goods_edit',
                                    query: { id: scope.row.id }
                                })
                            "
                            >设置佣金</el-button
                        >

                        <ls-dialog
                            title="取消分销"
                            v-if="scope.row.is_distribution == 1"
                            class="inline m-l-10"
                            :content="`确定不参与：${scope.row.name}？请谨慎操作`"
                            @confirm="couponDel([scope.row.id], 0)"
                        >
                            <el-button slot="trigger" type="text" size="small">取消分销</el-button>
                        </ls-dialog>

                        <ls-dialog
                            title="参与分销"
                            class="inline m-l-10"
                            v-if="scope.row.is_distribution == 0"
                            :content="`确定参与：${scope.row.name}？请谨慎操作`"
                            @confirm="couponDel([scope.row.id], 1)"
                        >
                            <el-button slot="trigger" type="text" size="small">参与分销</el-button>
                        </ls-dialog>
                    </template>
                </el-table-column>
            </el-table>

            <div class="flex row-right m-t-24">
                <ls-pagination v-model="pager" @change="getDistributionData()"></ls-pagination>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import LsPagination from '@/components/ls-pagination.vue'
import LsDialog from '@/components/ls-dialog.vue'
import { RequestPaging } from '@/utils/util'
import ExportData from '@/components/export-data/index.vue'
import { apiDistributionGoodsLists, apiDistributionGoodsJoin } from '@/api/distribution/distribution'
import { apiGoodsOtherList } from '@/api/goods'
@Component({
    components: {
        LsPagination,
        LsDialog,
        ExportData
    }
})
export default class DistributionGoods extends Vue {
    /** S Data **/
    apiDistributionGoodsLists = apiDistributionGoodsLists

    $refs!: { paneTable: any }

    goodsSearchData = {
        name: '',
        code: '',
        status: '',
        supplier_id: '',
        category_id: '',
        is_distribution: ''
    }

    categoryList = []

    options = [
        { value: '', label: '全部' },
        { value: '1', label: '参与分销' },
        { value: '0', label: '取消分销' }
    ]

    goodsStatus = [
        { value: '', label: '全部' },
        { value: '1', label: '上架' },
        { value: '0', label: '下架' },
        { value: '-1', label: '已售馨' }
    ]

    pager = new RequestPaging()

    selectIds: Array<string> = []

    /** E Data **/

    /** S Method **/

    // 获取商品分类
    getCategoryList() {
        apiGoodsOtherList({}).then((res: any) => {
            this.categoryList = res.category_list
        })
    }

    // 获取分销列表
    getDistributionData(page?: number): void {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apiDistributionGoodsLists,
            params: {
                ...this.goodsSearchData
            }
        })
    }

    // 选择某条数据
    selectionChange(val: any[]) {
        this.selectIds = val.map(item => item.id)
    }

    // 全选
    selectAll() {
        this.$refs.paneTable.toggleAllSelection()
    }

    //  参不参与分销
    couponDel(ids: Array<number>, flag: number): void {
        apiDistributionGoodsJoin({
            ids: Array.isArray(ids) ? ids : this.selectIds,
            is_distribution: flag
        }).then(res => {
            this.resetgoodsSearchData()
        })
    }

    // 重置
    resetgoodsSearchData() {
        Object.keys(this.goodsSearchData).map(key => {
            this.$set(this.goodsSearchData, key, '')
        })
        this.getDistributionData()
    }

    /** E Method **/

    created() {
        this.getDistributionData()
        this.getCategoryList()
    }
}
</script>

<style lang="scss" scoped>
.ls-goods {
    &__top {
        padding-bottom: 6px;
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
