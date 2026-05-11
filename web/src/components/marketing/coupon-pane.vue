<template>
    <div class="coupon-pane">
        <div class="pane-header">
            <el-button size="mini" type="primary" @click="$router.push('/coupon/edit')">新增优惠券</el-button>
        </div>
        <div class="pane-table m-t-16">
            <el-table ref="paneTable" :data="value" style="width: 100%" size="mini">
                <!-- 优惠券的编号 -->
                <el-table-column prop="sn" label="优惠券编号" min-width="120"> </el-table-column>

                <!-- 优惠券名称 -->
                <el-table-column prop="name" label="优惠券名称" min-width="100"> </el-table-column>

                <!-- 优惠的额度内容等 -->
                <el-table-column prop="discount_content" label="优惠内容" min-width="160"> </el-table-column>

                <!-- 推广方式 -->
                <el-table-column label="推广方式" min-width="100">
                    <template slot-scope="scope">
                        <el-tag size="medium" type="success" v-if="scope.row.get_type == 1">买家领取</el-tag>
                        <el-tag size="medium" type="info" v-else>卖家发放</el-tag>
                    </template>
                </el-table-column>

                <!-- 可以用券的时间 -->
                <el-table-column prop="use_time_text" label="用券时间" min-width="280"> </el-table-column>

                <!-- 优惠券发放的总量 -->
                <el-table-column prop="send_total_text" label="发放总量" min-width="90"> </el-table-column>

                <!-- 优惠券已经领取的数量 -->
                <el-table-column prop="receive_number" label="已领取" min-width="90"> </el-table-column>

                <!-- 优惠券剩余数量 -->
                <el-table-column prop="surplus_number" label="剩余" min-width="90"> </el-table-column>

                <!-- 优惠券已使用数量 -->
                <el-table-column prop="use_number" label="已使用" min-width="90"> </el-table-column>

                <!-- 优惠券状态 -->
                <el-table-column label="优惠券状态" min-width="100">
                    <template slot-scope="scope">
                        <el-tag size="medium" type="danger" v-if="scope.row.status == 1">未开始</el-tag>
                        <el-tag size="medium" type="success" v-else-if="scope.row.status == 2">进行中</el-tag>
                        <el-tag size="medium" type="info" v-else>已结束</el-tag>
                    </template>
                </el-table-column>

                <!-- 优惠券创建时间 -->
                <el-table-column prop="create_time" label="创建时间" min-width="160"> </el-table-column>

                <el-table-column fixed="right" label="操作" min-width="180">
                    <template slot-scope="scope">
                        <!-- 优惠券详情 -->
                        <el-button
                            type="text"
                            size="small"
                            @click="
                                $router.push({
                                    path: '/coupon/edit',
                                    query: {
                                        id: scope.row.id,
                                        disabled: true,
                                        status: 0
                                    }
                                })
                            "
                            >详情</el-button
                        >

                        <!-- 编辑优惠券 -->
                        <el-button
                            type="text"
                            v-if="scope.row.status !== 3"
                            size="small"
                            @click="
                                $router.push({
                                    path: '/coupon/edit',
                                    query: {
                                        id: scope.row.id,
                                        status: scope.row.status,
                                        disabled: scope.row.status == 2 ? true : false
                                    }
                                })
                            "
                            >编辑</el-button
                        >

                        <!-- 卖家发放优惠券 -->
                        <el-button
                            v-if="scope.row.get_type == 2 && scope.row.status !== 3"
                            type="text"
                            size="small"
                            @click="
                                $router.push({
                                    path: '/coupon/grant',
                                    query: { id: scope.row.id }
                                })
                            "
                            >卖家发放</el-button
                        >

                        <!-- 状态更改为开始领取 -->
                        <ls-dialog
                            v-if="scope.row.status == 1"
                            title="开始领取"
                            class="inline m-l-12"
                            :content="`开始领取：${scope.row.name}`"
                            @confirm="couponOpen(scope.row.id)"
                        >
                            <el-button slot="trigger" type="text" size="small">开始领取</el-button>
                        </ls-dialog>

                        <!-- 状态更改为结束领取 -->
                        <ls-dialog
                            class="inline m-l-12"
                            title="结束领取"
                            v-if="scope.row.status == 2"
                            :content="`确定结束领取：${scope.row.name}？结束领取的优惠券不能重新开始领取，请谨慎操作`"
                            @confirm="couponStop(scope.row.id)"
                        >
                            <el-button slot="trigger" type="text" size="small">结束领取</el-button>
                        </ls-dialog>

                        <!-- 删除优惠券 -->
                        <ls-dialog
                            class="inline m-l-12"
                            title="删除优惠券"
                            :content="`确定删除：${scope.row.name}（${scope.row.sn}）？请谨慎操作`"
                            @confirm="couponDel(scope.row.id)"
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
    apiCouponDel, //删除优惠券
    apiCouponSort, //优惠券排序
    apiCouponOpen, //优惠券开始发放
    apiCouponStop //优惠券结束发放
} from '@/api/marketing/coupon'

@Component({
    components: {
        LsDialog,
        LsPagination,
        PopoverInput
    }
})
export default class couponPane extends Vue {
    $refs!: { paneTable: any }
    @Prop() value: any
    @Prop() pager!: any

    // 删除优惠券
    couponDel(ids: number) {
        apiCouponDel({
            id: ids
        }).then(() => {
            // this.$message.success('修改成功!')
            this.$emit('refresh')
        })
    }

    // 开始优惠券
    couponOpen(ids: number) {
        apiCouponOpen({
            id: ids
        }).then(() => {
            // this.$message.success('开启成功!')
            this.$emit('refresh')
        })
    }

    // 结束优惠券
    couponStop(ids: number) {
        apiCouponStop({
            id: ids
        }).then(() => {
            // this.$message.success('关闭成功!')
            this.$emit('refresh')
        })
    }

    // 优惠券排序
    couponSort(sort: string, id: number) {
        apiCouponSort({
            id,
            sort
        }).then(() => {
            this.$emit('refresh')
        })
    }
}
</script>

<style scoped lang="scss">
.primary {
    color: $--color-primary;
}
</style>
