<template>
    <div class="freight-template">
        <div class="freight-template__top ls-card">
            <el-page-header @back="$router.go(-1)" :content="$route.meta.title"></el-page-header>
            <div class="ls-top__search m-t-20">
                <el-form ref="form" inline :model="queryObj" label-width="80px" size="small">
                    <el-form-item label="模版名称">
                        <el-input style="width: 280px" v-model="queryObj.name" placeholder="请输入模版名称"></el-input>
                    </el-form-item>
                    <el-form-item label="计费方式">
                        <el-select v-model="queryObj.charge_way" placeholder="请选择计费方式">
                            <el-option label="件数计费" :value="1"></el-option>
                            <el-option label="重量计费" :value="2"></el-option>
                            <el-option label="体积计费" :value="3"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label class="m-l-20">
                        <el-button size="small" type="primary" @click="getList(1)">查询</el-button>
                        <el-button size="small" @click="handleReset">重置</el-button>
                        <export-data
                            class="m-l-10"
                            :pageSize="pager.size"
                            :method="apiFreightLists"
                            :param="queryObj"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <div class="freight-template__content ls-card m-t-16">
            <div class="ls-content__btns">
                <el-button size="small" type="primary" @click="$router.push('/setting/delivery/freight_edit')"
                    >新增运费模版</el-button
                >
            </div>
            <div class="ls-content__table m-t-16">
                <el-table :data="pager.lists" style="width: 100%" size="mini" v-loading="pager.loading">
                    <el-table-column prop="name" label="模版名称"></el-table-column>
                    <el-table-column prop="charge_way_name" label="计费方式"> </el-table-column>
                    <el-table-column prop="remark" label="备注"> </el-table-column>
                    <el-table-column prop="create_time" label="创建时间"> </el-table-column>
                    <el-table-column fixed="right" label="操作">
                        <template slot-scope="scope">
                            <el-button
                                type="text"
                                size="small"
                                @click="
                                    $router.push({
                                        path: '/setting/delivery/freight_edit',
                                        query: { id: scope.row.id }
                                    })
                                "
                                >编辑</el-button
                            >
                            <ls-dialog
                                class="m-l-10 inline"
                                :content="`确定删除：${scope.row.name}？`"
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
import { RequestPaging } from '@/utils/util'
import { Component, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import { apiFreightDel, apiFreightLists } from '@/api/setting/delivery'
import ExportData from '@/components/export-data/index.vue'
@Component({
    components: {
        LsDialog,
        LsPagination,
        ExportData
    }
})
export default class FreightTemplate extends Vue {
    queryObj = {
        name: '',
        charge_way: ''
    }
    pager = new RequestPaging()
    apiFreightLists = apiFreightLists
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apiFreightLists,
            params: {
                ...this.queryObj
            }
        })
    }
    handleReset() {
        this.queryObj = {
            name: '',
            charge_way: ''
        }
        this.getList()
    }

    handleDelete(id: number) {
        apiFreightDel({ id }).then(() => {
            this.getList()
        })
    }

    created() {
        this.getList()
    }
}
</script>
<style lang="scss" scoped>
.freight-template {
    &__top {
        padding-bottom: 6px;
    }
}
</style>
