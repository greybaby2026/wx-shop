<template>
    <div>
        <div class="ls-card">
            <el-button size="small" type="primary" @click="goTaskAdd">新增打印机</el-button>

            <el-table
                ref="paneTable"
                class="m-t-24"
                :data="pager.lists"
                v-loading="pager.loading"
                style="width: 100%"
                size="mini"
            >
                <el-table-column prop="name" label="打印机名称" min-width="180"></el-table-column>
                <el-table-column prop="type_desc" label="设备类型" min-width="180"></el-table-column>
                <el-table-column prop="name" label="自动打印" min-width="180">
                    <template slot-scope="scope">
                        <el-switch
                            v-model="scope.row.auto_print"
                            :active-value="1"
                            :inactive-value="0"
                            :active-color="styleConfig.primary"
                            inactive-color="#f4f4f5"
                            @change="handleStatusPrintSet(scope.row)"
                        />
                    </template>
                </el-table-column>
                <el-table-column prop="name" label="状态" min-width="180">
                    <template slot-scope="scope">
                        {{ scope.row.status ? '开启' : '关闭' }}
                    </template>
                </el-table-column>
                <el-table-column prop="create_time" label="创建时间" min-width="180"></el-table-column>

                <el-table-column label="操作" min-width="180">
                    <template slot-scope="scope">
                        <el-button type="text" size="small" @click="goTaskEdit(scope.row.id)">编辑</el-button>

                        <!-- 测试打印 -->
                        <ls-dialog
                            class="m-l-10 inline"
                            :content="'确定要测试打印吗？'"
                            @confirm="onTestPrintFunc(scope.row.id)"
                        >
                            <el-button type="text" size="mini" slot="trigger">测试打印 </el-button>
                        </ls-dialog>

                        <!-- 删除打印机 -->
                        <ls-dialog
                            class="m-l-10 m-t-4 m-b-4 inline"
                            :content="'确定要删除这个打印机吗？请谨慎操作'"
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
import { apiPrintLists, apiDelPrint, apiTestPrint, apiAutoPrint } from '@/api/application/print'
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
                callback: apiPrintLists,
                params: {}
            })
            .catch(() => {
                this.$message.error('数据请求失败，刷新重载!')
            })
    }

    // 是否自动打印
    async handleStatusPrintSet(row: any) {
        await apiAutoPrint({ id: row.id })
        this.getList()
    }

    // 测试打印
    async onTestPrintFunc(id: number) {
        await apiTestPrint({ id })
    }

    // 删除这个打印机
    onDel(id: any) {
        apiDelPrint({ id: id })
            .then(() => {
                // 删除成功就请求新列表
                this.getList()
                this.$message.success('删除成功!')
            })
            .catch(() => {
                this.$message.error('删除失败!')
            })
    }

    // 新增
    goTaskAdd() {
        this.$router.push({
            path: '/print/edit_print',
            query: {
                mode: PageMode.ADD
            }
        })
    }

    // 编辑
    goTaskEdit(id: any) {
        this.$router.push({
            path: '/print/edit_print',
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
