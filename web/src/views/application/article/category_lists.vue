<template>
    <div class="category_lists">
        <div class="ls-card">
            <!-- 操作提示 -->
            <el-alert
                title="温馨提示：*平台发布文章，可在商城新闻资讯栏目查看。"
                type="info"
                show-icon
                :closable="false"
            />
        </div>

        <div class="ls-card m-t-24">
            <!-- 新增公告按钮 -->
            <div class="add-btn">
                <article-category-edit title="新增分类" @refresh="getCategoryList">
                    <el-button slot="trigger" type="primary" size="mini">新增分类</el-button>
                </article-category-edit>
            </div>

            <!-- 公告数据列表 -->
            <div class="m-t-24">
                <el-table :data="pager.lists" v-loading="pager.loading" size="mini" style="width: 100%">
                    <el-table-column sortable prop="id" label="ID" min-width="70" />
                    <el-table-column prop="name" label="文章分类">
                        <template slot-scope="scope">
                            <div class="line-1">{{ scope.row.name }}</div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="is_show" label="分类状态">
                        <template slot-scope="scope">
                            <el-switch
                                v-model="scope.row.is_show"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                                @change="changeStatus($event, scope.row)"
                            />
                        </template>
                    </el-table-column>
                    <el-table-column prop="sort" label="排序" />
                    <el-table-column sortable prop="create_time" label="创建时间" />
                    <el-table-column label="操作">
                        <!-- 操作 -->
                        <template slot-scope="scope">
                            <div class="flex">
                                <article-category-edit title="编辑分类" @refresh="getCategoryList" :cid="scope.row.id">
                                    <el-button slot="trigger" type="text" size="mini">编辑</el-button>
                                </article-category-edit>
                                <ls-dialog
                                    title="删除分类"
                                    :content="'确认要删除 ' + scope.row.name"
                                    class="m-l-10 inline"
                                    @confirm="onCategoryDel(scope.row)"
                                >
                                    <el-button type="text" size="mini" slot="trigger">删除 </el-button>
                                </ls-dialog>
                            </div>
                        </template>
                    </el-table-column>
                </el-table>

                <!-- 分页 -->
                <div class="m-t-24 flex row-right">
                    <ls-pagination v-model="pager" @change="getCategoryList" />
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import ExportData from '@/components/export-data/index.vue'
import { apiArticleCategoryLists, apiArticleCategoryDelete, apiArticleCategoryIsShow } from '@/api/application/article'
import { RequestPaging } from '@/utils/util'
import { PageMode } from '@/utils/type'
import ArticleCategoryEdit from '@/components/marketing/article/article-category-edit.vue'

@Component({
    components: {
        LsDialog,
        LsPagination,
        ExportData,
        ArticleCategoryEdit
    }
})
export default class HelpManage extends Vue {
    /** S Data **/

    // 分页
    pager: RequestPaging = new RequestPaging()

    /** E Data **/

    /** S Methods **/

    // 更改显示开关状态
    changeStatus(value: 0 | 1, row: any) {
        apiArticleCategoryIsShow({
            id: row.id
        })
            .then(() => {
                this.getCategoryList()
            })
            .catch((err: any) => {})
    }

    // 获取分类列表
    getCategoryList() {
        this.pager
            .request({
                callback: apiArticleCategoryLists
                // params: {},
            })
            .catch((err: any) => {})
    }

    // 删除该分类
    onCategoryDel(row: any) {
        apiArticleCategoryDelete({
            id: row.id
        }).then(() => {
            this.getCategoryList()
        })
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.getCategoryList()
    }

    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped></style>
