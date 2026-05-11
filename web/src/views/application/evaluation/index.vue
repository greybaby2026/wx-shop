<template>
    <div class="ls-evaluation">
        <div class="ls-evaluation__top ls-card">
            <div class="ls-top__search m-t-16">
                <el-form ref="form" inline :model="queryObj" label-width="80px" size="small">
                    <el-form-item label="商品名称">
                        <el-input placeholder="请输入商品名称" v-model="queryObj.goods_name"></el-input>
                    </el-form-item>
                    <el-form-item label="商品状态">
                        <el-select v-model="queryObj.status" placeholder="请选择商品状态">
                            <el-option label="全部" value></el-option>
                            <el-option label="仓库中" :value="0"></el-option>
                            <el-option label="销售中" :value="1"></el-option>
                        </el-select>
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
                    <el-form-item label class="m-l-20">
                        <el-button size="small" type="primary" @click="getList(1)">查询</el-button>
                        <el-button size="small" @click="handleReset">重置</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <div class="ls-evaluation__content ls-card m-t-16">
            <div class="ls-content__table m-t-16">
                <el-table ref="table" :data="pager.lists" style="width: 100%" size="mini" v-loading="pager.loading">
                    <el-table-column label="商品信息" min-width="240">
                        <template slot-scope="scope">
                            <div class="flex">
                                <el-image
                                    class="flex-none"
                                    :src="scope.row.image"
                                    style="width: 58px; height: 58px"
                                ></el-image>
                                <div class="goods-info m-l-8">
                                    <div class="line-2">
                                        {{ scope.row.name }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column label="商品分类" prop="category_text" min-width="140"></el-table-column>
                    <el-table-column label="价格" prop="price_text" min-width="120"></el-table-column>
                    <el-table-column label="总库存" prop="total_stock" min-width="80"></el-table-column>
                    <el-table-column label="总销量" prop="sales_num" min-width="80"></el-table-column>
                    <el-table-column label="状态" prop="status_text" min-width="80"></el-table-column>
                    <el-table-column label="评价数" prop="comment_text" min-width="80"></el-table-column>
                    <el-table-column label="发布时间" prop="create_time" min-width="150"></el-table-column>
                    <el-table-column fixed="right" label="操作" width="100">
                        <template slot-scope="scope">
                            <el-button
                                type="text"
                                size="small"
                                @click="
                                    $router.push({
                                        path: '/evaluation/add',
                                        query: {
                                            goods_id: scope.row.id
                                        }
                                    })
                                "
                                >添加评价</el-button
                            >
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <div class="ls-content__footer m-t-16 flex row-right">
                <div class="pagination">
                    <ls-pagination v-model="pager" @change="getList()" />
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiGoodsCommentAssistantLists, apiGoodsOtherList } from '@/api/goods'
import { RequestPaging, timeFormat } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'
import LsDialog from '@/components/ls-dialog.vue'
import DatePicker from '@/components/date-picker.vue'
import ExportData from '@/components/export-data/index.vue'
@Component({
    components: {
        LsDialog,
        LsPagination,
        DatePicker,
        ExportData
    }
})
export default class Evaluation extends Vue {
    $refs!: { table: any }
    pager = new RequestPaging()
    reply = ''
    categoryList = []
    queryObj: any = {
        goods_name: '',
        status: '',
        category_id: ''
    }
    apiGoodsCommentAssistantLists = apiGoodsCommentAssistantLists

    getList(size?: number): void {
        size && (this.pager.page = size)
        this.pager.request({
            callback: apiGoodsCommentAssistantLists,
            params: {
                ...this.queryObj
            }
        })
    }

    handleReset() {
        this.queryObj = {
            goods_name: '',
            status: '',
            category_id: ''
        }
        this.getList()
    }
    getGoodsOtherList() {
        apiGoodsOtherList({
            type: 'list'
        }).then((res: any) => {
            this.categoryList = res?.category_list
        })
    }
    created() {
        this.getGoodsOtherList()
        this.getList()
    }
}
</script>
<style lang="scss" scoped>
.ls-evaluation {
    &__top {
        padding-bottom: 6px;
    }
}
</style>
