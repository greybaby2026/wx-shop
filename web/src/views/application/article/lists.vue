<template>
    <div class="article_lists">
        <div class="ls-card">
            <!-- 操作提示 -->
            <el-alert
                title="温馨提示：*平台发布文章，可在商城新闻资讯栏目查看。"
                type="info"
                show-icon
                :closable="false"
            />

            <!-- 头部表单 -->
            <!-- <div class="m-t-20">
                <el-form :inline="true" :model="searchForm" class="demo-form-inline" size="small"> -->
            <!-- 标题 -->
            <!-- <el-form-item label="标题">
                        <el-input v-model="searchForm.name" placeholder="请输入公告标题"></el-input>
                    </el-form-item> -->

            <!-- 搜索查询 -->
            <!-- <el-form-item class="m-l-24">
                        <el-button type="primary" size="mini" @click="onSearch">查询
                        </el-button>
                        <el-button size="mini" @click="resetSearch">重置</el-button>
                        <export-data class="m-l-10" :pageSize="pager.size" :method="apiNoticeLists" :param="searchForm"></export-data>
                    </el-form-item> -->
            <!-- </el-form>
            </div> -->
        </div>

        <div class="ls-card m-t-24">
            <!-- 新增资讯文章-->
            <div class="add-btn">
                <el-button type="primary" @click="goArticleAdd" size="mini">新增资讯 </el-button>
            </div>

            <!-- 资讯文章列表 -->
            <div class="m-t-24">
                <el-table :data="pager.lists" v-loading="pager.loading" size="mini" style="width: 100%">
                    <el-table-column sortable prop="id" label="ID" min-width="70" />
                    <el-table-column prop="image" label="文章封面">
                        <template slot-scope="scope">
                            <el-image :src="scope.row.image" style="width: 80px; height: 80px" fit="cover"></el-image>
                        </template>
                    </el-table-column>
                    <el-table-column prop="title" label="文章标题">
                        <template slot-scope="scope">
                            <div class="line-1">{{ scope.row.title }}</div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="cid_desc" label="文章分类">
                        <template slot-scope="scope">
                            <div class="line-1">{{ scope.row.cid_desc }}</div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="status" label="状态">
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
                    <el-table-column prop="visit" label="浏览量" />
                    <el-table-column prop="sort" label="排序" />
                    <el-table-column sortable prop="create_time" label="创建时间" />
                    <el-table-column label="操作">
                        <!-- 操作 -->
                        <template slot-scope="scope">
                            <el-button type="text" size="mini" @click="goArticleEdit(scope.row)">编辑 </el-button>
                            <ls-dialog title="删除公告" class="m-l-10 inline" @confirm="onArticleDel(scope.row)">
                                <el-button type="text" size="mini" slot="trigger">删除 </el-button>
                            </ls-dialog>
                        </template>
                    </el-table-column>
                </el-table>

                <!-- 分页 -->
                <div class="m-t-24 flex row-right">
                    <ls-pagination v-model="pager" @change="getArticleList" />
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
import { apiArticleDelete, apiArticleLists, apiArticleIsShow } from '@/api/application/article'
import { RequestPaging } from '@/utils/util'
import { PageMode } from '@/utils/type'

@Component({
    components: {
        LsDialog,
        LsPagination,
        ExportData
    }
})
export default class ArticleLists extends Vue {
    /** S Data **/

    // 分页
    pager: RequestPaging = new RequestPaging()

    /** E Data **/

    /** S Methods **/

    // 更改显示开关状态
    changeStatus(value: 0 | 1, row: any) {
        apiArticleIsShow({
            id: row.id
        })
            .then(() => {
                this.getArticleList()
            })
            .catch((err: any) => {})
    }

    // 新增资讯文章
    goArticleAdd() {
        this.$router.push({
            path: '/article/article_edit',
            query: {
                mode: PageMode.ADD
            }
        })
    }

    // 编辑资讯文章
    goArticleEdit(data: any) {
        this.$router.push({
            path: '/article/article_edit',
            query: {
                id: data.id,
                mode: PageMode.EDIT
            }
        })
    }

    // 获取资讯文章列表
    getArticleList() {
        this.pager
            .request({
                callback: apiArticleLists,
                params: {}
            })
            .catch((err: any) => {})
    }

    // 删除资讯文章
    onArticleDel(row: any) {
        apiArticleDelete({
            id: row.id
        }).then(() => {
            this.getArticleList()
        })
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.getArticleList()
    }

    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped></style>
