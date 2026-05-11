<template>
    <div>
        <div class="ls-card">
            <el-button size="small" type="primary" @click="goTemplateAdd">新增小票模版</el-button>

            <el-table
                ref="paneTable"
                class="m-t-24"
                :data="pager.lists"
                v-loading="pager.loading"
                style="width: 100%"
                size="mini"
            >
                <el-table-column prop="template_name" label="模板名称" min-width="180"></el-table-column>
                <el-table-column prop="create_time" label="创建时间" min-width="180"></el-table-column>

                <el-table-column label="操作" min-width="180">
                    <template slot-scope="scope">
                        <el-button type="text" size="small" @click="goTemplateEdit(scope.row.id)">编辑</el-button>

                        <!-- 删除小票模版 -->
                        <ls-dialog
                            class="m-l-10 m-t-4 m-b-4 inline"
                            :content="'确定要删除这个小票模版吗？请谨慎操作'"
                            @confirm="onDel(scope.row.id)"
                        >
                            <el-button type="text" size="mini" slot="trigger"> 删除 </el-button>
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
import { apiTemplateLists, apiDelTemplate } from '@/api/application/print'
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
export default class Template extends Vue {
    // 分页
    pager: RequestPaging = new RequestPaging()

    // 获取列表
    getList() {
        this.pager
            .request({
                callback: apiTemplateLists,
                params: {}
            })
            .catch(() => {
                this.$message.error('数据请求失败，刷新重载!')
            })
    }

    // 删除这个小票模版
    onDel(id: any) {
        apiDelTemplate({ id: id }).then(() => {
            // 删除成功就请求新列表
            this.getList()
        })
    }

    // 新增
    goTemplateAdd() {
        this.$router.push({
            path: '/print/edit_template',
            query: {
                mode: PageMode.ADD
            }
        })
    }

    // 编辑
    goTemplateEdit(id: any) {
        this.$router.push({
            path: '/print/edit_template',
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
