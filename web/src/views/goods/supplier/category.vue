<template>
    <div class="ls-supplier-category">
        <div class="ls-category__top ls-card">
            <el-alert title="温馨提示：管理供应商分类；" type="info" show-icon :closable="false"> </el-alert>
        </div>
        <div class="ls-category__content ls-card m-t-16">
            <div class="ls-content__btns">
                <el-button size="small" type="primary" @click="handleAdd">新增供应商分类</el-button>
            </div>
            <div class="ls-content__table m-t-16">
                <el-table :data="pager.lists" size="mini" v-loading="pager.loading">
                    <el-table-column prop="name" label="分类名称"></el-table-column>
                    <el-table-column prop="sort" label="排序"> </el-table-column>
                    <el-table-column prop="create_time" label="创建时间"> </el-table-column>
                    <el-table-column fixed="right" label="操作">
                        <template slot-scope="scope">
                            <el-button type="text" size="small" @click="handleEdit(scope.row)">编辑</el-button>
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
                <ls-pagination v-model="pager" @change="getList" />
            </div>
        </div>
        <add-supplier-category ref="addSupplierCategory" :value="form" @refresh="getList" />
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import AddSupplierCategory from '@/components/goods/add-supplier-category.vue'
import { apiSupplierCategoryDel, apiSupplierCategoryLists } from '@/api/goods'
import { RequestPaging } from '@/utils/util'
@Component({
    components: {
        LsDialog,
        LsPagination,
        AddSupplierCategory
    }
})
export default class SupplierCategory extends Vue {
    $refs!: { addSupplierCategory: any }
    loading = false
    pager = new RequestPaging()
    form: any = {
        name: '',
        sort: ''
    }
    lists = []
    count = 0

    handleAdd() {
        this.form = {
            name: '',
            sort: ''
        }
        this.$refs.addSupplierCategory.openDialog()
    }
    handleEdit({ id, name, sort }: any) {
        this.form = {
            id,
            name,
            sort
        }
        this.$refs.addSupplierCategory.openDialog()
    }

    handleDelete(id: number) {
        apiSupplierCategoryDel(id).then(() => {
            this.getList()
        })
    }

    getList() {
        this.pager
            .request({
                callback: apiSupplierCategoryLists
            })
            .then((res: any) => {
                this.lists = res?.lists
                this.count = res?.count
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

<style lang="scss" scoped></style>
