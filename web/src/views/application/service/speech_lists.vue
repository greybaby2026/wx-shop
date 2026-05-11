<template>
    <div class="speech-lists">
        <div class="ls-card">
            <el-alert
                title="温馨提示：添加在线客服话术，方便客服快捷回复。"
                type="info"
                show-icon
                :closable="false"
            ></el-alert>
        </div>

        <div class="ls-card m-t-16">
            <!-- 新增客服 -->
            <el-button type="primary" size="small" @click="handleAdd">新增话术</el-button>

            <!-- 客服列表 -->
            <div class="m-t-24">
                <el-table :data="pager.lists" v-loading="pager.loading" style="width: 100%" size="mini">
                    <el-table-column prop="title" label="标题" min-width="200" />
                    <el-table-column prop="content" label="内容" min-width="300" />
                    <el-table-column prop="sort" label="排序" min-width="100" />
                    <el-table-column prop="create_time" label="创建时间" min-width="130" />

                    <el-table-column label="操作" width="200">
                        <!-- 操作 -->
                        <template v-slot="scope">
                            <span class="m-r-10">
                                <el-button type="text" size="small" @click="handleEdit(scope.row)">编辑</el-button>
                            </span>
                            <ls-dialog class="inline" @confirm="handleDelete(scope.row.id)">
                                <el-button type="text" size="small" slot="trigger">删除</el-button>
                            </ls-dialog>
                        </template>
                    </el-table-column>
                </el-table>

                <!-- 分页 -->
                <div class="m-t-24 pagination">
                    <ls-pagination v-model="pager" @change="getLists()" />
                </div>
            </div>
        </div>

        <speech-edit ref="speechEdit" :value="speechItem" @save="handleSave" />
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apikefuLangLists, apikefuLangDel, apikefuLangAdd, apikefuLangEdit } from '@/api/application/service'
import { RequestPaging } from '@/utils/util'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import SpeechEdit from '@/components/marketing/speech-edit.vue'

@Component({
    components: {
        LsDialog,
        LsPagination,
        SpeechEdit
    }
})
export default class SpeechLists extends Vue {
    $refs!: { speechEdit: any }
    // 表单数据
    speechItem = {}
    pager: RequestPaging = new RequestPaging()

    /** E Data **/

    /** S Methods **/

    // 获取列表数据
    getLists(page?: number): void {
        page && (this.pager.page = page)
        // 请求管理员列表
        this.pager.request({
            callback: apikefuLangLists
        })
    }

    // 删除
    handleDelete(id: number) {
        apikefuLangDel({ id }).then(() => {
            // 删除成功就请求新列表
            this.getLists()
        })
    }

    handleAdd() {
        this.speechItem = {
            title: '',
            content: '',
            sort: ''
        }
        this.$refs.speechEdit.openDialog()
    }
    handleEdit({ id, title, content, sort }: any) {
        this.speechItem = {
            id,
            title,
            content,
            sort
        }
        this.$refs.speechEdit.openDialog()
    }

    handleSave(value: any) {
        const api = value.id ? apikefuLangEdit(value) : apikefuLangAdd(value)
        api.then(() => {
            this.$refs.speechEdit.closeDialog()
            this.getLists(1)
        })
    }
    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.getLists()
    }

    /** E Life Cycle **/
}
</script>
s

<style lang="scss" scoped>
.pagination {
    padding-right: 5%;
    display: flex;
    justify-content: flex-end;
}
</style>
