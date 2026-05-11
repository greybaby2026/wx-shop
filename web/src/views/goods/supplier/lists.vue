<template>
    <div class="ls-supplier">
        <div class="ls-supplier__top ls-card">
            <el-alert
                title="温馨提示：发布商品时可以选择供货商，方便货源管理。"
                type="info"
                show-icon
                :closable="false"
            >
            </el-alert>
            <div class="ls-top__search m-t-16">
                <el-form ref="form" inline :model="queryObj" label-width="90px" size="small">
                    <el-form-item label="供应商名称">
                        <el-input
                            style="width: 280px"
                            v-model="queryObj.name"
                            placeholder="请输入供应商名称"
                        ></el-input>
                    </el-form-item>
                    <el-form-item label="" class="m-l-20">
                        <el-button size="small" type="primary" @click="getList(1)">查询</el-button>
                        <el-button size="small" @click="handleReset">重置</el-button>
                        <export-data
                            class="m-l-10"
                            :pageSize="pager.size"
                            :method="apiSupplierLists"
                            :param="queryObj"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <div class="ls-supplier__content ls-card m-t-16">
            <div class="ls-content__btns">
                <el-button size="small" type="primary" @click="$router.push('/goods/supplier/edit')"
                    >新增供应商</el-button
                >
            </div>
            <div class="ls-content__table m-t-16">
                <el-table :data="pager.lists" style="width: 100%" size="mini" v-loading="pager.loading">
                    <el-table-column prop="code" label="编号" min-width="100"> </el-table-column>
                    <el-table-column prop="name" label="供应商" min-width="150"> </el-table-column>
                    <el-table-column prop="supplier_category" label="供应商分类" min-width="150"> </el-table-column>
                    <el-table-column prop="contact" label="联系人" min-width="150"> </el-table-column>
                    <el-table-column prop="mobile" label="联系人手机" min-width="150"> </el-table-column>
                    <el-table-column prop="landline" label="座机号码" min-width="150"> </el-table-column>
                    <el-table-column prop="sort" label="排序" min-width="100"> </el-table-column>
                    <el-table-column prop="create_time" label="创建时间" min-width="150"> </el-table-column>

                    <el-table-column fixed="right" label="操作" min-width="140">
                        <template slot-scope="scope">
                            <el-button
                                type="text"
                                size="small"
                                @click="
                                    $router.push({
                                        path: '/goods/supplier/edit',
                                        query: { id: scope.row.id }
                                    })
                                "
                                >编辑</el-button
                            >
                            <ls-dialog
                                class="m-l-10 inline"
                                :content="`确定删除：${scope.row.name}？请谨慎操作。`"
                                @confirm="handleDelete(scope.row.id)"
                            >
                                <el-button slot="trigger" type="text" size="small">删除</el-button>
                            </ls-dialog>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <div class="flex row-right m-t-16">
                <ls-pagination v-model="pager" @change="getList()" />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { apiSupplierDel, apiSupplierLists } from '@/api/goods'
import { RequestPaging } from '@/utils/util'
import { Component, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import ExportData from '@/components/export-data/index.vue'
@Component({
    components: {
        LsDialog,
        LsPagination,
        ExportData
    }
})
export default class Supplier extends Vue {
    queryObj = {
        name: ''
    }
    pager = new RequestPaging()
    apiSupplierLists = apiSupplierLists
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apiSupplierLists,
            params: {
                ...this.queryObj
            }
        })
    }
    handleReset() {
        this.queryObj = {
            name: ''
        }
        this.getList()
    }

    handleDelete(id: number) {
        apiSupplierDel(id).then(() => {
            this.getList()
        })
    }

    created() {
        this.getList()
    }
    activated() {
        this.getList()
    }
}
</script>

<style lang="scss" scoped>
.ls-supplier {
    &__top {
        padding-bottom: 6px;
    }
}
</style>
