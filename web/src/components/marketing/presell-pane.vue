<template>
    <div class="seckill-pane">
        <div class="pane-header">
            <el-button size="mini" type="primary" @click="$router.push('/presell/edit')">新增预售活动</el-button>
        </div>
        <div class="pane-table m-t-16">
            <el-table ref="paneTable" :data="value" style="width: 100%" size="mini">
                <el-table-column prop="name" label="活动名称" min-width="100"> </el-table-column>
                <el-table-column prop="" label="活动时间" min-width="150">
                    <template slot-scope="scope"> {{ scope.row.start_time }}~{{ scope.row.end_time }} </template>
                </el-table-column>
                <el-table-column prop="sale_order" label="预售订单" min-width="100"> </el-table-column>

                <el-table-column label="预售销售额" min-width="100">
                    <template slot-scope="scope"> ￥{{ scope.row.sale_money }} </template>
                </el-table-column>
                <el-table-column prop="sale_nums" label="预售销售量" min-width="100"> </el-table-column>
                <el-table-column label="活动状态" min-width="100">
                    <template slot-scope="scope">
                        <el-tag size="medium" type="danger" v-if="scope.row.status == 0">未开始</el-tag>
                        <el-tag size="medium" type="success" v-else-if="scope.row.status == 1">进行中</el-tag>
                        <el-tag size="medium" type="info" v-else>已结束</el-tag>
                    </template>
                </el-table-column>

                <!-- 活动创建时间 -->
                <el-table-column prop="create_time" label="创建时间" min-width="120"> </el-table-column>

                <el-table-column fixed="right" label="操作" width="200">
                    <template slot-scope="scope">
                        <!-- 活动详情 -->
                        <el-button
                            type="text"
                            size="small"
                            @click="
                                $router.push({
                                    path: '/presell/edit',
                                    query: { id: scope.row.id, disabled: true }
                                })
                            "
                            >详情</el-button
                        >

                        <!-- 编辑活动 -->
                        <el-button
                            type="text"
                            v-if="scope.row.status != 3"
                            size="small"
                            @click="
                                $router.push({
                                    path: '/presell/edit',
                                    query: { id: scope.row.id }
                                })
                            "
                            >编辑</el-button
                        >

                        <!-- 状态更改为确认活动 -->
                        <ls-dialog
                            v-if="scope.row.status == 0"
                            class="inline m-l-10"
                            :content="`确认开始预售：${scope.row.name}？请谨慎操作。`"
                            @confirm="handleStart(scope.row.id)"
                        >
                            <el-button slot="trigger" type="text" size="small">开始预售</el-button>
                        </ls-dialog>

                        <!-- 状态更改为结束活动 -->
                        <ls-dialog
                            class="inline m-l-10"
                            v-if="scope.row.status == 1"
                            :content="`确定结束预售：${scope.row.name}？请谨慎操作。`"
                            @confirm="handleStop(scope.row.id)"
                        >
                            <el-button slot="trigger" type="text" size="small">结束预售</el-button>
                        </ls-dialog>

                        <!-- 删除活动 -->
                        <ls-dialog
                            class="inline m-l-10"
                            :content="`确定删除：${scope.row.name}？请谨慎操作。`"
                            @confirm="handleDelete(scope.row.id)"
                        >
                            <el-button slot="trigger" type="text" size="small">删除</el-button>
                        </ls-dialog>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <div class="pane-footer m-t-16 flex row-right">
            <ls-pagination v-model="pager" @change="$emit('refresh')" />
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import PopoverInput from '@/components/popover-input.vue'
import { apiPreselllDel, apiPreselllOpen, apiPreselllStop } from '@/api/marketing/presell'

@Component({
    components: {
        LsDialog,
        LsPagination,
        PopoverInput
    }
})
export default class SeckillPane extends Vue {
    $refs!: { paneTable: any }
    @Prop() value: any
    @Prop() pager!: any

    // 删除活动
    handleDelete(ids: number) {
        apiPreselllDel({
            id: ids
        }).then(() => {
            this.$emit('refresh')
        })
    }

    // 开始活动
    handleStart(ids: number) {
        apiPreselllOpen({
            id: ids
        }).then(() => {
            this.$emit('refresh')
        })
    }

    // 结束活动
    handleStop(ids: number) {
        apiPreselllStop(ids).then(() => {
            this.$emit('refresh')
        })
    }
}
</script>

<style scoped lang="scss"></style>
