<template>
    <div class="free-shipping">
        <div class="ls-seckill__top ls-card">
            <el-alert
                title="温馨提示：按场次来新增包邮活动，每一场的活动时间不能重叠（不包括已结束的活动）。"
                type="info"
                show-icon
                :closable="false"
            />

            <div class="seckill-search m-t-16">
                <el-form ref="form" inline :model="queryObj" label-width="80px" size="small">
                    <el-form-item label="活动名称">
                        <el-input v-model="queryObj.name" placeholder="请输入活动名称"></el-input>
                    </el-form-item>
                    <el-form-item label="活动时间">
                        <date-picker :start-time.sync="queryObj.start_time" :end-time.sync="queryObj.end_time" />
                    </el-form-item>
                    <el-form-item label="" class="m-l-6">
                        <el-button size="mini" type="primary" @click="getList(1)">查询</el-button>
                        <el-button size="mini" @click="resetQueryObj">重置</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <div class="ls-seckill__content ls-card m-t-16" style="padding-top: 0">
            <el-tabs v-model="activeName" v-loading="pager.loading" @tab-click="getList(1)">
                <el-tab-pane
                    v-for="(item, index) in tabs"
                    :key="index"
                    :label="`${item.label}(${tabCount[item.name]})`"
                    :name="item.name"
                >
                </el-tab-pane>
                <div class="seckill-pane">
                    <div class="pane-header">
                        <el-button size="mini" type="primary" @click="$router.push('/free_shipping/edit')">
                            新增包邮活动
                        </el-button>
                    </div>
                    <div class="pane-table m-t-16">
                        <el-table ref="paneTable" :data="pager.lists" style="width: 100%" size="mini">
                            <el-table-column prop="name" label="活动名称" min-width="120"> </el-table-column>
                            <el-table-column prop="activity_time" label="活动时间" min-width="210">
                                <template #default="{ row }">
                                    <div>开始时间： {{ row.start_time }}</div>
                                    <div>结束时间： {{ row.end_time }}</div>
                                </template>
                            </el-table-column>
                            <el-table-column prop="order_num" label="活动订单" min-width="100"> </el-table-column>

                            <el-table-column label="活动销售额" min-width="100">
                                <template slot-scope="scope"> ￥{{ scope.row.order_amount }} </template>
                            </el-table-column>
                            <el-table-column label="活动状态" min-width="100">
                                <template slot-scope="scope">
                                    <el-tag size="medium" type="danger" v-if="scope.row.status == 0">未开始</el-tag>
                                    <el-tag size="medium" type="success" v-else-if="scope.row.status == 1">
                                        进行中
                                    </el-tag>
                                    <el-tag size="medium" type="info" v-else>已结束</el-tag>
                                </template>
                            </el-table-column>

                            <!-- 活动创建时间 -->
                            <el-table-column prop="create_time" label="创建时间" min-width="150"> </el-table-column>

                            <el-table-column fixed="right" label="操作" width="200">
                                <template #default="scope">
                                    <!-- 活动详情 -->
                                    <el-button
                                        type="text"
                                        size="small"
                                        v-if="scope.row.btn.detail_btn"
                                        @click="
                                            $router.push({
                                                path: '/free_shipping/edit',
                                                query: { id: scope.row.id, disabled: true }
                                            })
                                        "
                                    >
                                        详情
                                    </el-button>

                                    <!-- 编辑活动 -->
                                    <el-button
                                        type="text"
                                        v-if="scope.row.btn.edit_btn"
                                        size="small"
                                        @click="
                                            $router.push({
                                                path: '/free_shipping/edit',
                                                query: { id: scope.row.id }
                                            })
                                        "
                                    >
                                        编辑
                                    </el-button>

                                    <!-- 状态更改为确认活动 -->
                                    <ls-dialog
                                        v-if="scope.row.btn.start_btn"
                                        class="inline m-l-10"
                                        :content="`确认开始活动：${scope.row.name}？请谨慎操作。`"
                                        @confirm="handleStart(scope.row.id)"
                                    >
                                        <el-button slot="trigger" type="text" size="small">开始活动</el-button>
                                    </ls-dialog>

                                    <!-- 状态更改为结束活动 -->
                                    <ls-dialog
                                        class="inline m-l-10"
                                        v-if="scope.row.btn.end_btn"
                                        :content="`确定结束活动：${scope.row.name}？请谨慎操作。`"
                                        @confirm="handleStop(scope.row.id)"
                                    >
                                        <el-button slot="trigger" type="text" size="small">结束活动</el-button>
                                    </ls-dialog>

                                    <!-- 删除活动 -->
                                    <ls-dialog
                                        v-if="scope.row.btn.delete_btn"
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
                        <ls-pagination v-model="pager" @change="getList()" />
                    </div>
                </div>
            </el-tabs>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import SeckillPane from '@/components/marketing/seckill-pane.vue'
import { RequestPaging } from '@/utils/util'
import DatePicker from '@/components/date-picker.vue'
import LsPagination from '@/components/ls-pagination.vue'
import LsDialog from '@/components/ls-dialog.vue'
import {
    apiFreeShippingDel,
    apiFreeShippingLists,
    apiFreeShippingOpen,
    apiFreeShippingStop
} from '@/api/marketing/free_shipping'

export enum FreeShippingEnum {
    'all' = '',
    'wait' = 0,
    'ing' = 1,
    'end' = 2
}

@Component({
    components: {
        DatePicker,
        LsPagination,
        LsDialog,
        SeckillPane
    }
})
export default class FreeShipping extends Vue {
    tabs = [
        {
            label: '全部',
            name: 'all'
        },
        {
            label: '未开始',
            name: 'wait'
        },
        {
            label: '进行中',
            name: 'ing'
        },
        {
            label: '已结束',
            name: 'end'
        }
    ]

    queryObj = {
        name: '',
        end_time: '',
        start_time: ''
    }
    tabCount = {
        all: 0, //全部
        wait: 0, //未开始
        ing: 0, //进行中
        end: 0 //已结束
    }
    pager = new RequestPaging()
    activeName: any = 'all'
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager
            .request({
                callback: apiFreeShippingLists,
                params: {
                    status: FreeShippingEnum[this.activeName],
                    ...this.queryObj
                }
            })
            .then(res => {
                this.tabCount = res?.extend
            })
    }
    resetQueryObj() {
        Object.keys(this.queryObj).map(key => {
            this.$set(this.queryObj, key, '')
        })
        this.getList()
    }

    // 删除活动
    handleDelete(ids: number) {
        apiFreeShippingDel({
            id: ids
        }).then(() => {
            this.getList()
        })
    }

    // 开始活动
    handleStart(ids: number) {
        apiFreeShippingOpen({
            id: ids
        }).then(() => {
            this.getList()
        })
    }

    // 结束活动
    handleStop(ids: number) {
        apiFreeShippingStop({
            id: ids
        }).then(() => {
            this.getList()
        })
    }
    created() {
        this.getList()
    }
}
</script>

<style lang="scss" scoped></style>
