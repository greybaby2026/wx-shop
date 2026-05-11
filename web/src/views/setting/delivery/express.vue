<template>
    <div class="express-company">
        <div class="express-company__top ls-card">
            <el-page-header @back="$router.go(-1)" :content="$route.meta.title"></el-page-header>
            <div class="ls-top__search m-t-20">
                <el-form ref="form" inline :model="queryObj" label-width="80px" size="small">
                    <el-form-item label="快递名称">
                        <el-input style="width: 280px" v-model="queryObj.name" placeholder="请输入快递名称"></el-input>
                    </el-form-item>
                    <el-form-item label class="m-l-20">
                        <el-button size="small" type="primary" @click="getList(1)">查询</el-button>
                        <el-button size="small" @click="handleReset">重置</el-button>
                    </el-form-item>
                    <export-data
                        class="m-l-10"
                        :pageSize="pager.size"
                        :method="apiExpressLists"
                        :param="queryObj"
                    ></export-data>
                </el-form>
            </div>
        </div>
        <div class="express-company__content ls-card m-t-16">
            <div class="ls-content__btns">
                <el-button size="small" type="primary" @click="$router.push('/setting/delivery/express_edit')"
                    >新增快递公司</el-button
                >
            </div>
            <div class="ls-content__table m-t-16">
                <el-table :data="pager.lists" style="width: 100%" size="mini" v-loading="pager.loading">
                    <el-table-column prop="name" label="快递公司"> </el-table-column>
                    <el-table-column label="快递图标">
                        <template slot-scope="{ row }">
                            <el-image v-if="row.icon" style="width: 30px; height: 30px" :src="row.icon"></el-image>
                        </template>
                    </el-table-column>

                    <el-table-column prop="code" label="快递编码"> </el-table-column>
                    <el-table-column prop="code100" label="快递100编码"> </el-table-column>
                    <el-table-column prop="codebird" label="快递鸟编码"> </el-table-column>
                    <el-table-column prop="sort" label="排序"> </el-table-column>
                    <el-table-column fixed="right" label="操作">
                        <template slot-scope="scope">
                            <el-button
                                type="text"
                                size="small"
                                @click="
                                    $router.push({
                                        path: '/setting/delivery/express_edit',
                                        query: { id: scope.row.id }
                                    })
                                "
                                >编辑</el-button
                            >
                            <span class="p-8 primary">|</span>
                            <ls-dialog
                                class="inline"
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
import { apiExpressDel, apiExpressLists } from '@/api/setting/delivery'
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
        name: ''
    }
    pager = new RequestPaging()
    apiExpressLists = apiExpressLists
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apiExpressLists,
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
        apiExpressDel({ id }).then(() => {
            this.getList()
        })
    }

    created() {
        this.getList()
    }
}
</script>
<style lang="scss" scoped>
.express-company {
    &__top {
        padding-bottom: 6px;
    }
}
</style>
