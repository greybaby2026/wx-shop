<template>
    <div class="Team-pane">
        <div class="pane-header">
            <el-button size="mini" type="primary" @click="$router.push('/combination/edit')">新增拼团活动</el-button>
        </div>
        <div class="pane-table m-t-16">
            <el-table ref="paneTable" :data="value" style="width: 100%" size="mini">
                <!-- 活动的编号 -->
                <!-- <el-table-column prop="sn" label="活动编号" min-width="100">
                </el-table-column> -->

                <!-- 活动名称 -->
                <el-table-column prop="name" label="活动名称" min-width="100"> </el-table-column>

                <!-- 优惠的额度内容等 -->
                <!-- <el-table-column prop="goods_num" label="优惠商品" min-width="100">
                </el-table-column> -->

                <!-- 活动时间 -->
                <el-table-column prop="activity_time" label="活动时间" min-width="250"> </el-table-column>

                <!-- 可以用券的时间 -->
                <!-- <el-table-column prop="browse_volume" label="浏览量" min-width="100">
                </el-table-column> -->

                <!-- 活动剩余数量 -->
                <el-table-column prop="closing_order" label="拼团订单" min-width="100"> </el-table-column>

                <!-- 活动发放的总量 -->
                <el-table-column prop="sales_amount" label="拼团销售额" min-width="100"> </el-table-column>

                <!-- 活动已经领取的数量 -->
                <el-table-column prop="sales_volume" label="拼团销售量" min-width="100"> </el-table-column>

                <!-- 活动状态 -->
                <el-table-column label="活动状态" min-width="100">
                    <template slot-scope="scope">
                        <el-tag size="medium" type="danger" v-if="scope.row.status == 1">未开始</el-tag>
                        <el-tag size="medium" type="success" v-else-if="scope.row.status == 2">进行中</el-tag>
                        <el-tag size="medium" type="info" v-else>已结束</el-tag>
                    </template>
                </el-table-column>

                <!-- 活动创建时间 -->
                <el-table-column prop="create_time" label="创建时间" min-width="180"> </el-table-column>

                <el-table-column fixed="right" label="操作" min-width="280">
                    <template slot-scope="scope">
                        <!-- 活动详情 -->
                        <el-button
                            type="text"
                            size="small"
                            @click="
                                $router.push({
                                    path: '/combination/edit',
                                    query: {
                                        id: scope.row.id,
                                        disabled: true,
                                        status: 0
                                    }
                                })
                            "
                            >详情</el-button
                        >

                        <!-- 编辑活动 -->
                        <el-button
                            type="text"
                            v-if="scope.row.status !== 3"
                            size="small"
                            @click="
                                $router.push({
                                    path: '/combination/edit',
                                    query: {
                                        id: scope.row.id,
                                        status: scope.row.status,
                                        disabled: scope.row.status == 2 ? true : false
                                    }
                                })
                            "
                            >编辑</el-button
                        >

                        <!-- 卖家发放活动 -->
                        <el-button
                            v-if="scope.row.get_type == 2"
                            type="text"
                            size="small"
                            @click="
                                $router.push({
                                    path: '/Team/grant',
                                    query: { id: scope.row.id }
                                })
                            "
                            >卖家发放</el-button
                        >

                        <!-- 状态更改为确认活动 -->
                        <ls-dialog
                            v-if="scope.row.status == 1"
                            title="确认活动"
                            class="inline m-l-12"
                            :content="`确认活动：${scope.row.name}`"
                            @confirm="TeamOpen(scope.row.id)"
                        >
                            <el-button slot="trigger" type="text" size="small">确认活动</el-button>
                        </ls-dialog>

                        <!-- 状态更改为结束活动 -->
                        <ls-dialog
                            class="inline m-l-12"
                            title="结束活动"
                            v-if="scope.row.status == 2"
                            :content="`确定结束活动：${scope.row.name}？结束活动的活动不能重新开始，请谨慎操作`"
                            @confirm="TeamStop(scope.row.id)"
                        >
                            <el-button slot="trigger" type="text" size="small">结束活动</el-button>
                        </ls-dialog>

                        <!-- 删除活动 -->
                        <ls-dialog
                            class="inline m-l-12"
                            title="删除活动"
                            :content="`确定删除：${scope.row.name}（${scope.row.sn}）？请谨慎操作`"
                            @confirm="TeamDel(scope.row.id)"
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
import {
    apiTeamDel, //删除活动
    // apiTeamSort,  //活动排序
    apiTeamConfirm, //活动开始发放
    apiTeamStop //活动结束发放
} from '@/api/marketing/combination'

@Component({
    components: {
        LsDialog,
        LsPagination,
        PopoverInput
    }
})
export default class TeamPane extends Vue {
    $refs!: { paneTable: any }
    @Prop() value: any
    @Prop() pager!: any

    // 删除活动
    TeamDel(ids: number) {
        apiTeamDel({
            id: ids
        }).then(() => {
            this.$message.success('修改成功!')
            this.$emit('refresh')
        })
    }

    // 开始活动
    TeamOpen(ids: number) {
        apiTeamConfirm({
            id: ids
        }).then(() => {
            this.$message.success('开启成功!')
            this.$emit('refresh')
        })
    }

    // 结束活动
    TeamStop(ids: number) {
        apiTeamStop({
            id: ids
        }).then(() => {
            this.$message.success('关闭成功!')
            this.$emit('refresh')
        })
    }

    // 活动排序
    // TeamSort(sort: string, id: number) {
    //     apiTeamSort({
    //         id,
    //         sort,
    //     }).then(() => {
    //         this.$emit("refresh");
    //     });
    // }
}
</script>

<style scoped lang="scss">
.primary {
    color: $--color-primary;
}
</style>
