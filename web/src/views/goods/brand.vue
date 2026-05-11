<template>
    <div class="ls-brand">
        <div class="ls-brand__top ls-card">
            <el-alert
                title="温馨提示：1.用户可以根据商品品牌搜索商品；2.发布商品时可以选择商品对应的品牌。"
                type="info"
                show-icon
                :closable="false"
            ></el-alert>
            <div class="ls-top__search m-t-16">
                <el-form ref="form" inline :model="queryObj" label-width="80px" size="small" @submit.native.prevent>
                    <el-form-item label="品牌名称">
                        <el-input style="width: 280px" v-model="queryObj.name" placeholder="请输入品牌名称"></el-input>
                    </el-form-item>
                    <el-form-item label class="m-l-20">
                        <el-button size="small" type="primary" @click="getList(1)">查询</el-button>
                        <el-button size="small" @click="handleReset">重置</el-button>
                        <export-data
                            class="m-l-10"
                            :pageSize="pager.size"
                            :method="apiBrandLists"
                            :param="queryObj"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <div class="ls-brand__content ls-card m-t-16">
            <div class="ls-content__btns">
                <el-button size="small" type="primary" @click="$router.push('/goods/brand_edit')">新增品牌</el-button>
            </div>
            <div class="ls-content__table m-t-16">
                <el-table :data="pager.lists" style="width: 100%" size="mini" v-loading="pager.loading">
                    <el-table-column prop="name" label="品牌名称"></el-table-column>
                    <el-table-column prop="city" label="品牌图片">
                        <template slot-scope="scope">
                            <el-image
                                fit="cover"
                                class="flex-none"
                                style="width: 58px; height: 58px"
                                :src="scope.row.image"
                            />
                        </template>
                    </el-table-column>
                    <el-table-column label="排序" prop="sort"> </el-table-column>
                    <el-table-column prop="city" label="显示状态">
                        <template slot-scope="scope">
                            <el-switch
                                v-model="scope.row.is_show"
                                :active-value="1"
                                :inactive-value="0"
                                @change="handleStatus($event, scope.row.id)"
                            ></el-switch>
                        </template>
                    </el-table-column>
                    <el-table-column fixed="right" label="操作">
                        <template slot-scope="scope">
                            <el-button
                                type="text"
                                size="small"
                                @click="
                                    $router.push({
                                        path: '/goods/brand_edit',
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
import { apiBrandDel, apiBrandLists, apiBrandStatus } from '@/api/goods'
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
export default class Brand extends Vue {
    queryObj = {
        name: ''
    }
    pager = new RequestPaging()
    apiBrandLists = apiBrandLists
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apiBrandLists,
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
    handleStatus(value: number, id: number) {
        apiBrandStatus({
            id,
            is_show: value
        }).then(() => {
            this.getList()
        })
    }

    handleDelete(id: number) {
        apiBrandDel({ id }).then(() => {
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
.ls-brand {
    &__top {
        padding-bottom: 6px;
    }
}
</style>
