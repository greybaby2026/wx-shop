<template>
    <div class="service-lists">
        <div class="ls-card">
            <el-alert title="温馨提示：添加在线客服。" type="info" show-icon :closable="false"></el-alert>
        </div>

        <div class="ls-card m-t-16">
            <!-- 新增客服 -->
            <router-link to="/service/edit">
                <el-button type="primary" size="small">新增客服</el-button>
            </router-link>

            <!-- 客服列表 -->
            <div class="m-t-24">
                <el-table :data="pager.lists" v-loading="pager.loading" style="width: 100%" size="mini">
                    <el-table-column label="客服头像" min-width="80">
                        <template v-slot="scope">
                            <div class="flex">
                                <el-image :src="scope.row.avatar" style="width: 58px; height: 58px" fit="contain" />
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="account" label="客服账号" min-width="100" />
                    <el-table-column prop="nickname" label="客服昵称" min-width="100" />
                    <el-table-column prop="sort" label="排序" min-width="60" />
                    <el-table-column prop="disable" label="状态" min-width="80">
                        <template v-slot="scope">
                            <el-switch
                                v-model="scope.row.disable"
                                :active-value="0"
                                :inactive-value="1"
                                @change="changeStatus($event, scope.row.id)"
                            />
                        </template>
                    </el-table-column>
                    <el-table-column prop="create_time" label="创建时间" min-width="100"></el-table-column>

                    <el-table-column label="操作" width="200">
                        <!-- 操作 -->
                        <template v-slot="scope">
                            <router-link
                                :to="{
                                    path: '/service/edit',
                                    query: { id: scope.row.id }
                                }"
                                class="m-r-10"
                            >
                                <el-button type="text" size="small">编辑</el-button>
                            </router-link>
                            <span class="m-r-10" @click="enterKefu(scope.row.id)">
                                <el-button type="text" size="small">进入工作台</el-button>
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
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apikefuLists, apikefuDel, apikefuStatus, apikefuLogin } from '@/api/application/service'
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
export default class SelffetchShop extends Vue {
    // 表单数据
    pager: RequestPaging = new RequestPaging()
    /** E Data **/

    /** S Methods **/

    // 获取列表数据
    getLists(page?: number): void {
        page && (this.pager.page = page)
        // 请求管理员列表
        this.pager.request({
            callback: apikefuLists
        })
    }

    // 删除
    handleDelete(id: number) {
        apikefuDel({ id }).then(() => {
            // 删除成功就请求新列表
            this.getLists()
        })
    }

    // 更改状态
    changeStatus(value: 0 | 1, id: number) {
        apikefuStatus({
            id,
            disable: value
        }).then(() => {
            this.getLists()
        })
    }
    // 进入工作台
    enterKefu(id: number) {
        apikefuLogin({ id }).then(res => {
            window.open(res.url)
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
