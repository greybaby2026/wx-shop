<template>
    <div>
        <div class="ls-card">
            <el-button size="small" type="primary" @click="goTaskAdd">新增发件人模版</el-button>

            <el-table
                ref="paneTable"
                class="m-t-24"
                :data="pager.lists"
                v-loading="pager.loading"
                style="width: 100%"
                size="mini"
            >
                <el-table-column prop="name" label="发件人" min-width="180"></el-table-column>
                <el-table-column prop="mobile" label="发件人手机" min-width="180"></el-table-column>
                <el-table-column prop="district_desc" label="发件人地区" min-width="180"></el-table-column>
                <el-table-column prop="address" label="发件人地址" min-width="180"></el-table-column>
                <el-table-column label="操作" min-width="180">
                    <template slot-scope="scope">
                        <el-button type="text" size="small" @click="goTaskEdit(scope.row.id)">编辑</el-button>

                        <!-- 删除发件人 -->
                        <ls-dialog
                            class="m-l-10 m-t-4 m-b-4 inline"
                            :content="'确定要删除这个发件人吗？请谨慎操作'"
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
import { apiFaceSheetSenderLists, apiFaceSheetSenderDel } from '@/api/application/express'
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
                callback: apiFaceSheetSenderLists,
                params: {}
            })
            .catch(() => {
                this.$message.error('数据请求失败，刷新重载!')
            })
    }

    // 删除这个打印机
    onDel(id: any) {
        apiFaceSheetSenderDel({ id: id })
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
            path: '/express/sender_edit',
            query: {
                mode: PageMode.ADD
            }
        })
    }

    // 编辑
    goTaskEdit(id: any) {
        this.$router.push({
            path: '/express/sender_edit',
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
