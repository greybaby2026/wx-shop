<template>
    <div class="help_manage">
        <div class="ls-card">
            <!-- 操作提示 -->
            <el-alert
                title="温馨提示：1、平台发布的操作说明，公告文档，常见问题。用户可在商城的公告功能查看；2、公告文档排序值越小越前，排序值相同时新增文章在前；"
                type="info"
                show-icon
                :closable="false"
            />

            <!-- 头部表单 -->
            <div class="m-t-20">
                <el-form :inline="true" :model="searchForm" class="demo-form-inline" size="small">
                    <!-- 标题 -->
                    <el-form-item label="标题">
                        <el-input v-model="searchForm.name" placeholder="请输入公告标题"></el-input>
                    </el-form-item>

                    <!-- 搜索查询 -->
                    <el-form-item class="m-l-24">
                        <el-button type="primary" size="mini" @click="onSearch">查询 </el-button>
                        <el-button size="mini" @click="resetSearch">重置</el-button>
                        <export-data
                            class="m-l-10"
                            :pageSize="pager.size"
                            :method="apiNoticeLists"
                            :param="searchForm"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="ls-card m-t-24">
            <!-- 新增公告按钮 -->
            <div class="add-btn">
                <el-button type="primary" @click="goNoticeAdd" size="mini">新增 </el-button>
            </div>

            <!-- 公告数据列表 -->
            <div class="m-t-24">
                <el-table :data="pager.lists" v-loading="pager.loading" size="mini" style="width: 100%">
                    <el-table-column sortable prop="id" label="ID" min-width="70" />
                    <el-table-column prop="name" label="标题" min-width="280">
                        <template slot-scope="scope">
                            <div class="line-1">{{ scope.row.name }}</div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="image" label="封面图" min-width="180">
                        <template slot-scope="scope">
                            <el-image :src="scope.row.image" style="width: 80px; height: 80px" fit="cover"></el-image>
                        </template>
                    </el-table-column>
                    <el-table-column prop="status" label="状态" min-width="120">
                        <template slot-scope="scope">
                            <el-switch
                                v-model="scope.row.status"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                                @change="changeShowSwitchStatus($event, scope.row)"
                            />
                        </template>
                    </el-table-column>
                    <el-table-column prop="views" label="浏览量" min-width="120" />
                    <el-table-column prop="sort" label="排序" min-width="120" />
                    <el-table-column sortable prop="create_time" label="创建时间" min-width="200" />
                    <el-table-column label="操作" min-width="200">
                        <!-- 操作 -->
                        <template slot-scope="scope">
                            <el-button type="text" size="mini" @click="goNoticeEdit(scope.row)">编辑 </el-button>
                            <ls-dialog title="删除公告" class="m-l-10 inline" @confirm="onNoticeDel(scope.row)">
                                <el-button type="text" size="mini" slot="trigger">删除 </el-button>
                            </ls-dialog>
                        </template>
                    </el-table-column>
                </el-table>

                <!-- 分页 -->
                <div class="m-t-24 flex row-right">
                    <ls-pagination v-model="pager" @change="getNoticeList" />
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
import { apiNoticeLists, apiNoticeStatus, apiNoticeDel } from '@/api/application/notice'
import { RequestPaging } from '@/utils/util'
import { PageMode } from '@/utils/type'

@Component({
    components: {
        LsDialog,
        LsPagination,
        ExportData
    }
})
export default class HelpManage extends Vue {
    /** S Data **/

    apiNoticeLists = apiNoticeLists

    // 搜索表单
    searchForm = {
        name: '' // 公告标题
    }
    // 分页
    pager: RequestPaging = new RequestPaging()

    /** E Data **/

    /** S Methods **/
    // 搜索
    onSearch() {
        this.pager.page = 1
        this.getNoticeList()
    }

    // 重置搜索
    resetSearch() {
        this.$set(this.searchForm, 'name', '')
        this.getNoticeList()
    }

    // 更改显示开关状态
    changeShowSwitchStatus(value: 0 | 1, data: any) {
        apiNoticeStatus({
            id: data.id,
            status: value
        }).then(() => {
            this.getNoticeList()
        })
    }

    // 新增公告文章
    goNoticeAdd() {
        this.$router.push({
            path: '/notice/notice_edit',
            query: {
                mode: PageMode.ADD
            }
        })
    }

    // 编辑公告文章
    goNoticeEdit(data: any) {
        this.$router.push({
            path: '/notice/notice_edit',
            query: {
                id: data.id,
                mode: PageMode.EDIT
            }
        })
    }

    // 获取公告管理列表
    getNoticeList() {
        this.pager
            .request({
                callback: apiNoticeLists,
                params: {
                    name: this.searchForm.name
                }
            })
            .catch(() => {
                this.$message.error('数据请求失败，刷新重载!')
            })
    }

    // 删除公告文章
    onNoticeDel(data: any) {
        apiNoticeDel({ id: data.id }).then(() => {
            this.getNoticeList()
        })
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.getNoticeList()
    }

    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped></style>
