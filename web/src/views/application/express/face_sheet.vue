<template>
    <div>
        <div class="ls-card">
            <el-button size="small" type="primary" @click="goTaskAdd">新增电子面单模版</el-button>

            <el-table
                ref="paneTable"
                class="m-t-24"
                :data="pager.lists"
                v-loading="pager.loading"
                style="width: 100%"
                size="mini"
            >
                <el-table-column prop="name" label="模版名称" min-width="180"></el-table-column>
                <el-table-column prop="template_id" label="模版编号" min-width="180"></el-table-column>
                <el-table-column prop="express_desc" label="快递公司" min-width="180"></el-table-column>
                <el-table-column label="操作" min-width="180">
                    <template slot-scope="scope">
                        <el-button type="text" size="small" @click="goTaskEdit(scope.row.id)">编辑</el-button>

                        <!-- 删除发件人 -->
                        <ls-dialog
                            class="m-l-10 m-t-4 m-b-4 inline"
                            :content="'确定要删除这个模版吗？请谨慎操作'"
                            @confirm="onDel(scope.row.id)"
                        >
                            <el-button type="text" size="mini" slot="trigger">删除 </el-button>
                        </ls-dialog>
                    </template>
                </el-table-column>
            </el-table>

            <!-- 分页 -->
            <div class="m-t-24 flex row-right">
                <ls-pagination v-model="pager" @change="getList" />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { apiFaceSheetTemplateLists, apiFaceSheetTemplateDel } from '@/api/application/express'
import { Component, Prop, Vue } from 'vue-property-decorator'
import LsPagination from '@/components/ls-pagination.vue'
import { RequestPaging } from '@/utils/util'
import { PageMode } from '@/utils/type'
import LsDialog from '@/components/ls-dialog.vue'
@Component({
    components: {
        LsDialog,
        LsPagination
    }
})
export default class Task extends Vue {
    // 分页
    pager: RequestPaging = new RequestPaging()

    // 获取列表
    getList() {
        this.pager
            .request({
                callback: apiFaceSheetTemplateLists,
                params: {}
            })
            .catch(() => {
                this.$message.error('数据请求失败，刷新重载!')
            })
    }

    // 删除这个打印机
    onDel(id: any) {
        apiFaceSheetTemplateDel({ id: id })
            .then(() => {
                // 删除成功就请求新列表
                this.getList()
            })
            .catch(() => {
                this.$message.error('删除失败!')
            })
    }

    // 新增
    goTaskAdd() {
        this.$router.push({
            path: '/express/face_sheet_edit',
            query: {
                mode: PageMode.ADD
            }
        })
    }

    // 编辑
    goTaskEdit(id: any) {
        this.$router.push({
            path: '/express/face_sheet_edit',
            query: {
                id: id,
                mode: PageMode.EDIT
            }
        })
    }

    created() {
        this.getList()
    }
}
</script>

<style lang="scss"></style>
