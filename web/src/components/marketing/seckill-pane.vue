<template>
    <div class="seckill-pane">
        <div class="pane-header">
            <el-button size="mini" type="primary" @click="$router.push('/seckill/edit')">新增秒杀活动</el-button>
        </div>
        <div class="pane-table m-t-16">
            <el-table ref="paneTable" :data="value" style="width: 100%" size="mini">
                <el-table-column prop="name" label="活动名称" min-width="100"> </el-table-column>
                <el-table-column prop="activity_time" label="活动时间" min-width="150"> </el-table-column>
                <el-table-column prop="closing_order" label="秒杀订单" min-width="100"> </el-table-column>

                <el-table-column label="秒杀销售额" min-width="100">
                    <template slot-scope="scope"> ￥{{ scope.row.sales_amount }} </template>
                </el-table-column>
                <el-table-column prop="sales_volume" label="秒杀销售量" min-width="100"> </el-table-column>
                <el-table-column label="活动状态" min-width="100">
                    <template slot-scope="scope">
                        <el-tag size="medium" type="danger" v-if="scope.row.status == 1">未开始</el-tag>
                        <el-tag size="medium" type="success" v-else-if="scope.row.status == 2">进行中</el-tag>
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
                                    path: '/seckill/edit',
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
                                    path: '/seckill/edit',
                                    query: { id: scope.row.id }
                                })
                            "
                            >编辑</el-button
                        >

                        <!-- 状态更改为确认活动 -->
                        <ls-dialog
                            v-if="scope.row.status == 1"
                            class="inline m-l-10"
                            :content="`确认开始秒杀：${scope.row.name}？请谨慎操作。`"
                            @confirm="handleStart(scope.row.id)"
                        >
                            <el-button slot="trigger" type="text" size="small">开始秒杀</el-button>
                        </ls-dialog>

                        <!-- 状态更改为结束活动 -->
                        <ls-dialog
                            class="inline m-l-10"
                            v-if="scope.row.status == 2"
                            :content="`确定结束秒杀：${scope.row.name}？请谨慎操作。`"
                            @confirm="handleStop(scope.row.id)"
                        >
                            <el-button slot="trigger" type="text" size="small">结束秒杀</el-button>
                        </ls-dialog>

                        <!-- 删除活动 -->
                        <ls-dialog
                            class="inline m-l-10"
                            :content="`确定删除：${scope.row.name}？请谨慎操作。\n*秒杀活动删除后，未支付订单会被系统自动关闭。`"
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
import { apiSeckillDel, apiSeckillOpen, apiSeckillStop } from '@/api/marketing/seckill'

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
        apiSeckillDel({
            id: ids
        }).then(() => {
            this.$emit('refresh')
        })
    }

    // 开始活动
    handleStart(ids: number) {
        apiSeckillOpen({
            id: ids
        }).then(() => {
            this.$emit('refresh')
        })
    }

    // 结束活动
    handleStop(ids: number) {
        apiSeckillStop({
            id: ids
        }).then(() => {
            this.$emit('refresh')
        })
    }
}
</script>

<style scoped lang="scss"></style>
