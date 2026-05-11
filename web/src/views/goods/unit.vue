<template>
    <div class="ls-unit">
        <div class="ls-unit__top ls-card">
            <el-alert title="温馨提示：管理商品单位。" type="info" show-icon :closable="false"> </el-alert>
            <div class="ls-top__search m-t-16">
                <el-form ref="form" inline :model="queryObj" label-width="80px" size="small">
                    <el-form-item label="单位名称">
                        <el-input style="width: 280px" v-model="queryObj.name" placeholder="请输入单位名称"></el-input>
                    </el-form-item>
                    <el-form-item label class="m-l-20">
                        <el-button size="small" type="primary" @click="getList(1)">查询</el-button>
                        <el-button size="small" @click="handleReset">重置</el-button>
                        <export-data
                            class="m-l-10"
                            :pageSize="pager.size"
                            :method="apiUnitLists"
                            :param="queryObj"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <div class="ls-unit__content ls-card m-t-16">
            <div class="ls-content__btns">
                <el-button size="small" type="primary" @click="handleAdd">新增单位</el-button>
            </div>
            <div class="ls-content__table m-t-16">
                <el-table :data="pager.lists" size="mini" v-loading="pager.loading">
                    <el-table-column prop="name" label="单位名称"></el-table-column>
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
                <ls-pagination v-model="pager" @change="getList()" />
            </div>
        </div>

        <add-unit ref="addUnit" :value="form" @refresh="getList(1)" />
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import AddUnit from '@/components/goods/add-unit.vue'
import { apiUnitDel, apiUnitLists } from '@/api/goods'
import { RequestPaging } from '@/utils/util'
import GoodsSelect from '@/components/goods-select/index.vue'
import ExportData from '@/components/export-data/index.vue'
@Component({
    components: {
        LsDialog,
        LsPagination,
        AddUnit,
        GoodsSelect,
        ExportData
    }
})
export default class Unit extends Vue {
    $refs!: { addUnit: any }
    pager = new RequestPaging()
    queryObj = {
        name: ''
    }
    form: any = {
        name: '',
        sort: ''
    }
    apiUnitLists = apiUnitLists
    handleAdd() {
        this.form = {
            name: '',
            sort: ''
        }
        this.$refs.addUnit.openDialog()
    }
    handleEdit({ id, name, sort }: any) {
        this.form = {
            id,
            name,
            sort
        }
        this.$refs.addUnit.openDialog()
    }

    handleDelete(id: number) {
        apiUnitDel(id).then(() => {
            this.getList()
        })
    }
    handleReset() {
        this.queryObj = {
            name: ''
        }
        this.getList()
    }
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apiUnitLists,
            params: this.queryObj
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
.ls-unit {
    &__top {
        padding-bottom: 6px;
    }
}
</style>
