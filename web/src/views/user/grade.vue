<!-- 用户等级 -->
<template>
    <div class="user-grade">
        <div class="ls-card">
            <el-alert
                class="xxl"
                title="温馨提示：1.管理用户等级，系统默认等级不能删除；2.删除用户等级时，会重新调整用户等级为系统默认等级，请谨慎操作。"
                type="info"
                :closable="false"
                show-icon
            >
            </el-alert>
        </div>

        <div class="ls-user__grade ls-card m-t-20">
            <div class="list-header">
                <el-button size="small" type="primary" @click="onUserLevelAdd()">新增用户等级</el-button>
            </div>
            <div class="list-table m-t-16">
                <el-table
                    :data="pager.lists"
                    style="width: 100%"
                    v-loading="pager.loading"
                    :default-sort="{ prop: 'rank', order: 'ascending' }"
                    :header-cell-style="{ background: '#f5f8ff' }"
                    size="mini"
                >
                    <el-table-column prop="rank" label="等级级别" min-width="100px">
                        <template slot-scope="scope">
                            <div v-if="scope.row.rank == 1">{{ scope.row.rank }}级（默认等级）</div>
                            <div v-else>{{ scope.row.rank }}级</div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="name" label="等级名称" min-width="100px"> </el-table-column>
                    <el-table-column prop="image" label="等级图标" min-width="100px">
                        <template slot-scope="scope">
                            <el-image :src="scope.row.image" style="width: 34px; height: 34px"></el-image>
                        </template>
                    </el-table-column>
                    <el-table-column prop="num" label="用户数" min-width="100px"> </el-table-column>
                    <el-table-column prop="discount" label="等级折扣" min-width="100px"> </el-table-column>
                    <el-table-column fixed="right" label="操作" min-width="120">
                        <template slot-scope="scope">
                            <el-button type="text" size="small" @click="onUserLevelSee(scope.row)">详情</el-button>
                            <el-button type="text" size="small" @click="onUserLevelEdit(scope.row)">编辑</el-button>
                            <ls-dialog
                                class="m-l-10 inline"
                                @confirm="onUserLevelDel(scope.row)"
                                v-if="scope.row.rank !== 1"
                            >
                                <el-button type="text" size="small" slot="trigger">删除</el-button>
                            </ls-dialog>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <div class="flex row-right m-t-16 row-right">
                <ls-pagination v-model="pager" @change="getUserLevelList()" />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiUserLevelList, apiUserLevelDel } from '@/api/user/user'
import { PageMode } from '@/utils/type'
import { RequestPaging } from '@/utils/util'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
@Component({
    components: {
        LsDialog,
        LsPagination
    }
})
export default class MemberGrade extends Vue {
    /** S Data **/
    // 分页请求
    pager: RequestPaging = new RequestPaging()
    /** E Data **/

    /** S Methods **/
    // 获取用户等级列表
    getUserLevelList() {
        this.pager
            .request({
                callback: apiUserLevelList,
                params: {}
            })
            .catch(() => {
                this.$message.error('数据请求失败，刷新重载!')
            })
    }
    // 新增用户等级
    onUserLevelAdd() {
        this.$router.push({
            path: '/user/grade_edit',
            query: {
                mode: PageMode.ADD
            }
        })
    }
    // 编辑用户等级
    onUserLevelEdit(item: any) {
        this.$router.push({
            path: '/user/grade_edit',
            query: {
                mode: PageMode.EDIT,
                id: item.id,
                level: item.rank
            }
        })
    }

    // 查看用户等级
    onUserLevelSee(item: any) {
        this.$router.push({
            path: '/user/grade_edit',
            query: {
                mode: PageMode.EDIT,
                id: item.id,
                level: item.rank,
                disabled: 'true'
            }
        })
    }

    // 删除用户等级
    onUserLevelDel(item: any) {
        apiUserLevelDel({
            id: item.id as number
        })
            .then(() => {
                this.getUserLevelList()
                this.$message.success('删除成功!')
            })
            .catch(() => {
                this.$message.error('删除失败')
            })
    }
    /** E Methods **/
    /** S Life Cycle **/
    created() {
        this.getUserLevelList()
    }

    /** E Life Cycle **/
}
</script>
