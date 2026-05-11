<!-- 用户标签 -->
<template>
    <div class="user-tag">
        <div class="ls-card header">
            <!-- 提示内容 -->
            <el-alert
                class="xxl"
                title="温馨提示：1.根据用户特征、消费行为等要素给用户分配标签，进行针对性管理；2.同一用户可打上多个标签；3.暂时不支持自动标签。"
                type="info"
                :closable="false"
                show-icon
            >
            </el-alert>
            <!-- 搜索框 -->
            <el-form
                ref="formRef"
                :model="form"
                inline
                label-width="70px"
                size="small"
                class="m-t-20"
                @submit.native.prevent
            >
                <el-form-item label="标签名称">
                    <el-input class="ls-select-keyword" v-model="form.name" placeholder="请输入标签名称"></el-input>
                    <el-button class="m-l-20" size="small" type="primary" @click="getUserLabelList(1)">查询</el-button>
                    <el-button class="m-l-20" size="small" @click="onReset">重置</el-button>
                    <!-- 导出按钮 -->
                    <export-data
                        class="m-l-10"
                        :pageSize="pager.size"
                        :method="apiUserLabelList"
                        :param="form"
                    ></export-data>
                </el-form-item>
            </el-form>
        </div>

        <div class="ls-user_tag ls-card m-t-20">
            <div class="list-header">
                <el-button size="small" type="primary" @click="onUserLabelAdd">新增用户标签</el-button>
                <ls-dialog
                    class="m-l-10 inline"
                    title="确定批量删除"
                    content="关联此标签的用户将移除该标签，请谨慎操作。"
                    @confirm="onUserLabelDelAll"
                >
                    <el-button size="small" slot="trigger">删除</el-button>
                </ls-dialog>
            </div>
            <div class="list-table m-t-16">
                <el-table
                    :data="pager.lists"
                    style="width: 100%"
                    v-loading="pager.loading"
                    size="mini"
                    :header-cell-style="{ background: '#f5f8ff' }"
                    @selection-change="handleSelectionChange"
                >
                    <el-table-column type="selection" width="55"> </el-table-column>
                    <el-table-column prop="name" label="标签名称" min-width="" width=""> </el-table-column>
                    <el-table-column prop="num" label="用户数" min-width="" width=""> </el-table-column>
                    <el-table-column label="操作" min-width="">
                        <template slot-scope="scope">
                            <el-button type="text" size="small" @click="onUserLabelEdit(scope.row)">编辑</el-button>
                            <ls-dialog
                                class="m-l-10 inline"
                                :title="`确定删除：${scope.row.name}`"
                                content="关联此标签的用户将移除该标签，请谨慎操作。"
                                @confirm="onUserLabelDel(scope.row)"
                            >
                                <el-button type="text" size="small" slot="trigger">删除</el-button>
                            </ls-dialog>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <div class="flex row-right m-t-16 row-right">
                <ls-pagination v-model="pager" @change="getUserLabelList()" />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiUserLabelList, apiUserLabelDel } from '@/api/user/user'
import { LabelLists_Req } from '@/api/user/user.d.ts'
import { PageMode } from '@/utils/type'
import { RequestPaging } from '@/utils/util'
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
export default class userTag extends Vue {
    /** S Data **/
    // 分页请求
    pager: RequestPaging = new RequestPaging()
    // 查询表单
    form: LabelLists_Req = {
        name: '' //标签名称
    }
    // 被选中的
    multipleSelection = []

    apiUserLabelList = apiUserLabelList
    /** E Data **/

    /** S Methods **/
    // 重置搜索框内容
    onReset() {
        this.form.name = ''
        this.getUserLabelList()
    }
    // 获取用户标签列表
    getUserLabelList(page?: number): void {
        page && (this.pager.page = page)
        // 分页请求
        this.pager
            .request({
                callback: apiUserLabelList,
                params: this.form
            })
            .catch(() => {
                this.$message.error('数据请求失败，刷新重载')
            })
    }
    // 新增用户标签
    onUserLabelAdd() {
        this.$router.push({
            path: '/user/tag_edit',
            query: {
                mode: PageMode.ADD
            }
        })
    }
    // 编辑用户标签
    onUserLabelEdit(item: any) {
        this.$router.push({
            path: '/user/tag_edit',
            query: {
                mode: PageMode.EDIT,
                id: item.id
            }
        })
    }

    // 删除用户标签
    onUserLabelDel(item: any) {
        apiUserLabelDel({
            ids: [item.id]
        })
            .then(() => {
                this.getUserLabelList()
                this.$message.success('删除成功!')
            })
            .catch(() => {
                //this.$message.error("删除失败")
            })
    }
    // 批量删除
    onUserLabelDelAll() {
        let idArray: Array<Object> = []
        if (this.multipleSelection.length <= 0) {
            this.$message.error('请选择要删除的标签!')
            return
        }
        this.multipleSelection.forEach((item: any) => {
            idArray = [...idArray, item.id]
        })
        apiUserLabelDel({
            ids: idArray
        })
            .then(() => {
                this.getUserLabelList()
                this.$message.success('删除成功!')
            })
            .catch(() => {
                //this.$message.error("删除失败")
            })
    }
    // 导出用户标签
    onExportUserLabel() {}
    handleSelectionChange(val: any) {
        this.multipleSelection = val
    }
    getRowKeys(row: any) {
        return row.id
    }
    /** E Methods **/
    /** S Life Cycle **/
    created() {
        this.getUserLabelList()
    }
    /** E Life Cycle **/
}
</script>
