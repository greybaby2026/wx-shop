<template>
    <div class="withdraw-pane">
        <div class="pane-table">
            <el-button size="small" type="primary" @click="toAdd">新增砍价活动</el-button>
            <div class="list-table m-t-16">
                <el-table
                    ref="valueRef"
                    :data="value"
                    style="width: 100%"
                    size="mini"
                    :header-cell-style="{ background: '#f5f8ff' }"
                >
                    <!-- <el-table-column prop="sn" label="活动编号">
					</el-table-column> -->
                    <el-table-column prop="name" label="活动名称"> </el-table-column>
                    <!-- <el-table-column prop="goods_count" label="活动商品">
						<template slot-scope="scope">
							<div>{{scope.row.goods_count}}件</div>
						</template>
					</el-table-column> -->
                    <el-table-column prop="" label="活动时间" min-width="150">
                        <template slot-scope="scope">
                            <div>{{ scope.row.start_time_desc }}~{{ scope.row.end_time_desc }}</div>
                        </template>
                    </el-table-column>
                    <!-- <el-table-column prop="visited" label="浏览量">
					</el-table-column> -->
                    <el-table-column prop="order_count" label="砍价订单"> </el-table-column>
                    <el-table-column prop="total_amount" label="砍价销售额">
                        <template slot-scope="scope">
                            <div>¥{{ scope.row.total_amount }}</div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="total_num" label="砍价销售量"> </el-table-column>
                    <el-table-column prop="" label="活动状态">
                        <template slot-scope="scope">
                            <el-tag size="medium" type="danger" v-if="scope.row.status == 1">{{
                                scope.row.status_desc
                            }}</el-tag>
                            <el-tag size="medium" type="success" v-if="scope.row.status == 2">{{
                                scope.row.status_desc
                            }}</el-tag>
                            <el-tag size="medium" type="info" v-if="scope.row.status == 3">{{
                                scope.row.status_desc
                            }}</el-tag>
                        </template>
                    </el-table-column>
                    <el-table-column prop="create_time" label="创建时间"> </el-table-column>
                    <el-table-column label="操作" min-width="160" fixed="right">
                        <template slot-scope="scope">
                            <el-button type="text" slot="trigger" size="small" @click="toDetails(scope.row)"
                                >详情
                            </el-button>
                            <!-- <el-button v-if="scope.row.status != 1" type="text" slot="trigger" size="small"
								@click="toData(scope.row)">数据
							</el-button> -->
                            <el-button
                                v-if="scope.row.status != 3"
                                type="text"
                                slot="trigger"
                                size="small"
                                @click="toEdit(scope.row)"
                                >编辑
                            </el-button>
                            <ls-dialog
                                v-if="scope.row.status == 1"
                                class="m-l-10 inline"
                                :title="`确定开始砍价：${scope.row.name}(${scope.row.sn}）`"
                                content="活动确认后不能编辑修改砍价商品价格信息, 请谨慎操作。"
                                @confirm="onConfirm(scope.row.id)"
                            >
                                <el-button type="text" slot="trigger" size="small">开始砍价 </el-button>
                            </ls-dialog>
                            <ls-dialog
                                v-if="scope.row.status == 2"
                                class="m-l-10 inline"
                                :title="`确定结束砍价：${scope.row.name}(${scope.row.sn}）`"
                                content="砍价活动结束后不能重新开始, 请谨慎操作。"
                                @confirm="onStop(scope.row.id)"
                            >
                                <el-button type="text" slot="trigger" size="small">结束砍价 </el-button>
                            </ls-dialog>
                            <ls-dialog
                                class="m-l-10 inline"
                                :title="`确定删除：${scope.row.name}(${scope.row.sn}）`"
                                content="砍价活动删除时,未支付订单会被系统自动关闭。砍价活动删除后不能查看活动数据, 请谨慎操作。"
                                @confirm="onDelete(scope.row.id)"
                            >
                                <el-button type="text" slot="trigger" size="small">删除 </el-button>
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
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import { PageMode } from '@/utils/type'
import { apiBargainActivityDelete, apiBargainActivityStop, apiBargainActivityConfirm } from '@/api/marketing/bargain.ts'
@Component({
    components: {
        LsDialog,
        LsPagination
    }
})
export default class BargainPane extends Vue {
    $refs!: {
        valueRef: any
    }
    @Prop() value: any
    @Prop() pager!: any
    status = true

    // 新增
    toAdd() {
        this.$router.push({
            path: '/bargain/bargain_edit',
            query: {
                mode: PageMode.ADD
            }
        })
    }

    // 详情
    toDetails(item: any) {
        this.$router.push({
            path: '/bargain/bargain_edit',
            query: {
                mode: PageMode.EDIT,
                id: item.id,
                status: '0'
            }
        })
    }

    // 编辑
    toEdit(item: any) {
        this.$router.push({
            path: '/bargain/bargain_edit',
            query: {
                mode: PageMode.EDIT,
                id: item.id,
                status: item.status
            }
        })
    }

    // 数据详情
    toData(item: any) {
        // this.$router.push({
        // 	// path: "/bargain/bargain_edit",
        // 	query: {
        // 		id: item.id,
        // 	},
        // });
    }

    // 删除
    onDelete(id: any) {
        apiBargainActivityDelete({
            id: id
        }).then((res: any) => {
            this.$emit('refresh')
        })
    }

    // 结束
    onStop(id: number) {
        apiBargainActivityStop({
            id: id
        }).then((res: any) => {
            this.$emit('refresh')
        })
    }

    // 确定
    onConfirm(id: number) {
        apiBargainActivityConfirm({
            id: id
        }).then((res: any) => {
            this.$emit('refresh')
        })
    }
}
</script>

<style scoped lang="scss"></style>
