<template>
    <div class="lucky-draw-pane">
        <div class="list-header">
            <el-button size="small" type="primary" @click="goToADD">新增积分抽奖</el-button>
        </div>
        <div class="list-table m-t-16">
            <el-table
                :data="value"
                style="width: 100%"
                size="mini"
                v-loading="pager.loading"
                :header-cell-style="{ background: '#f5f8ff' }"
            >
                <el-table-column prop="name" label="活动名称"> </el-table-column>
                <el-table-column prop="" label="活动时间" min-width="120">
                    <template slot-scope="scope">
                        <div class="flex">
                            <span>开始</span>
                            <div class="m-l-8">
                                {{ scope.row.start_time_desc }}
                            </div>
                        </div>
                        <div class="flex">
                            <span>结束</span>
                            <div class="m-l-8">
                                {{ scope.row.end_time_desc }}
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="join_num" label="抽奖次数"> </el-table-column>
                <el-table-column prop="win_num" label="中奖人数"> </el-table-column>
                <el-table-column prop="status_desc" label="活动状态"> </el-table-column>
                <el-table-column prop="create_time" label="创建时间" min-width="120"> </el-table-column>
                <el-table-column fixed="right" label="操作" min-width="100">
                    <template slot-scope="scope">
                        <el-button type="text" size="small" @click="goToDetails(scope.row)">详情</el-button>
                        <el-button v-if="scope.row.status != 2" type="text" size="small" @click="goToEdit(scope.row)"
                            >编辑</el-button
                        >
                        <el-button v-if="scope.row.status != 0" type="text" size="small" @click="goTolog(scope.row)"
                            >抽奖记录</el-button
                        >
                        <ls-dialog
                            class="m-l-10 inline"
                            :content="`确定开始活动：${scope.row.name}`"
                            @confirm="onStart(scope.row)"
                            v-if="scope.row.status == 0"
                        >
                            <el-button v-if="scope.row.status == 0" type="text" size="small" slot="trigger"
                                >开始活动</el-button
                            >
                        </ls-dialog>
                        <ls-dialog
                            class="m-l-10 inline"
                            :content="`确定停止活动：${scope.row.name}`"
                            @confirm="onStop(scope.row)"
                            v-if="scope.row.status == 1"
                        >
                            <el-button v-if="scope.row.status == 1" type="text" size="small" slot="trigger"
                                >停止活动</el-button
                            >
                        </ls-dialog>
                        <ls-dialog
                            class="m-l-10 inline"
                            :content="`确定删除积分抽奖：${scope.row.name}`"
                            @confirm="onDelete(scope.row)"
                        >
                            <el-button type="text" size="small" slot="trigger">删除</el-button>
                        </ls-dialog>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <!-- 底部分页栏  -->
        <div class="flex row-right m-t-16 row-right">
            <ls-pagination v-model="pager" @change="$emit('refresh')" />
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Prop } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import { apiLuckyDrawDelete, apiLuckyDrawEnd, apiLuckyDrawStart } from '@/api/marketing/lucky_draw'
import { PageMode } from '@/utils/type'
@Component({
    components: {
        LsPagination,
        LsDialog
    }
})
export default class LuckyDrawPane extends Vue {
    @Prop() value: any // 列表数据
    @Prop() pager!: any // 含有分页信息的数据

    onDelete(value: any) {
        apiLuckyDrawDelete({
            id: value.id
        })
            .then(() => {
                this.$emit('refresh')
            })
            .catch((err: any) => {})
    }
    onStart(value: any) {
        apiLuckyDrawStart({
            id: value.id
        })
            .then(() => {
                this.$emit('refresh')
            })
            .catch((err: any) => {})
    }
    onStop(value: any) {
        apiLuckyDrawEnd({
            id: value.id
        })
            .then(() => {
                this.$emit('refresh')
            })
            .catch((err: any) => {})
    }

    // 添加
    goToADD() {
        this.$router.push({
            path: '/lucky_draw/edit',
            query: {
                mode: PageMode.ADD
            }
        })
    }

    goToEdit(val: any) {
        this.$router.push({
            path: '/lucky_draw/edit',
            query: {
                mode: PageMode.EDIT,
                status: val.status,
                id: val.id
            }
        })
    }

    goToDetails(val: any) {
        this.$router.push({
            path: '/lucky_draw/edit',
            query: {
                mode: PageMode.EDIT,
                status: val.status,
                id: val.id,
                type: 'details'
            }
        })
    }

    goTolog(val: any) {
        this.$router.push({
            path: '/lucky_draw/log',
            query: {
                id: val.id,
                create_time: val.create_time
            }
        })
    }
}
</script>

<style lang="scss" scoped></style>
