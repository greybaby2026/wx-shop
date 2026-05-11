<template>
    <div class="pages-lists">
        <div class="pages-lists__top ls-card">
            <el-alert
                title="温馨提示：微页面指定为店铺首页后不能删除。"
                type="info"
                :closable="false"
                show-icon
            ></el-alert>
            <div class="ls-top__search m-t-16">
                <el-form ref="form" inline :model="queryObj" label-width="80px" size="small">
                    <el-form-item label="页面名称">
                        <el-input style="width: 280px" v-model="queryObj.name" placeholder="请输入页面名称"></el-input>
                    </el-form-item>
                    <el-form-item label class="m-l-20">
                        <el-button size="small" type="primary" @click="getList(1)">查询</el-button>
                        <el-button size="small" @click="handleReset">重置</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <div class="pages-lists__content ls-card m-t-16">
            <div class="ls-content__btns">
                <template-select @select="handleSelect">
                    <el-button slot="trigger" size="small" type="primary">新建微页面</el-button>
                </template-select>
            </div>
            <div class="ls-content__table m-t-16">
                <el-table :data="pager.lists" style="width: 100%" size="mini" v-loading="pager.loading">
                    <el-table-column label="页面名称">
                        <template slot-scope="scope">
                            <span class="m-r-10">{{ scope.row.name }}</span>
                            <el-tag v-if="scope.row.is_home" size="mini">首页</el-tag>
                        </template>
                    </el-table-column>

                    <el-table-column prop="update_time" label="更新时间"> </el-table-column>
                    <el-table-column prop="create_time" label="创建时间"> </el-table-column>
                    <el-table-column fixed="right" label="操作">
                        <template slot-scope="scope">
                            <div class="inline m-r-10">
                                <el-button
                                    type="text"
                                    size="small"
                                    @click="
                                        $router.push({
                                            path: '/decorate/index',
                                            query: { id: scope.row.id }
                                        })
                                    "
                                    >编辑</el-button
                                >
                            </div>
                            <template v-if="!scope.row.is_home">
                                <ls-dialog
                                    class="inline m-r-10"
                                    :content="`确定删除：${scope.row.name}？请谨慎操作。`"
                                    @confirm="handleDelete(scope.row.id)"
                                >
                                    <el-button slot="trigger" type="text" size="small">删除</el-button>
                                </ls-dialog>
                                <ls-dialog
                                    class="inline"
                                    :content="`确定设置页面：${scope.row.name}为首页？`"
                                    @confirm="handleSetHome(scope.row.id)"
                                >
                                    <el-button slot="trigger" type="text" size="small">设置首页</el-button>
                                </ls-dialog>
                            </template>
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
import TemplateSelect from '@/components/shop/template-select/index.vue'
import LsPagination from '@/components/ls-pagination.vue'
import { apiThemePageAdd, apiThemePageDel, apiThemePageLists, apiThemePageSetHome } from '@/api/shop'
@Component({
    components: {
        LsDialog,
        LsPagination,
        TemplateSelect
    }
})
export default class PagesLists extends Vue {
    queryObj = {
        name: ''
    }
    pager = new RequestPaging()

    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apiThemePageLists,
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
        apiThemePageDel({ id }).then(() => {
            this.getList()
        })
    }
    handleSetHome(id: number) {
        apiThemePageSetHome({ id }).then(() => {
            this.getList()
        })
    }
    handleSelect(data: any) {
        apiThemePageAdd(data).then(res => {
            this.$router.push({
                path: '/decorate/index',
                query: { id: res.id }
            })
        })
    }

    created() {
        this.getList()
    }
}
</script>
<style lang="scss" scoped>
.pages-lists {
    &__top {
        padding-bottom: 6px;
    }
}
</style>
