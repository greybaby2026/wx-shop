<!-- 抽奖记录 -->
<template>
    <div class="lucky-draw-log">
        <!-- 导航头部 -->
        <div class="ls-card">
            <el-page-header @back="$router.go(-1)" content="抽奖记录" />
        </div>

        <!-- 活动信息 -->
        <el-form label-width="120px" size="small">
            <div class="ls-card m-t-16">
                <div class="card-title">活动信息</div>
                <div class="card-content m-t-24">
                    <el-form-item label="活动名称">
                        {{ desc.name }}
                    </el-form-item>
                    <el-form-item label="活动时间">
                        {{ desc.start_time_desc }} - {{ desc.end_time_desc }}
                    </el-form-item>
                    <el-form-item label="活动说明">
                        {{ desc.remark }}
                    </el-form-item>
                    <el-form-item label="活动时状态">
                        {{ desc.status_desc }}
                    </el-form-item>
                    <el-form-item label="创建时间">
                        {{ create_time }}
                    </el-form-item>
                </div>
            </div>
        </el-form>

        <!-- 查询 -->
        <div class="m-t-16 ls-card">
            <el-form ref="form" inline :model="form" label-width="70px" size="small">
                <el-form-item label="用户信息">
                    <el-input
                        class="ls-select-keyword"
                        v-model="form.user_info"
                        placeholder="请输入用户编号/昵称/手机号码"
                    >
                    </el-input>
                </el-form-item>
                <el-form-item label="奖品类型">
                    <el-select class="ls-select" v-model="form.prize_type" placeholder="全部">
                        <div v-for="(value, key) in prizeTypeList" :key="key">
                            <el-option :label="value.label" :value="value.value"></el-option>
                        </div>
                    </el-select>
                </el-form-item>
                <el-form-item label="中奖状态">
                    <el-select class="ls-select" v-model="form.status" placeholder="全部">
                        <el-option label="未中奖" :value="0"></el-option>
                        <el-option label="中奖" :value="1"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="中奖时间">
                    <date-picker :start-time.sync="form.start_time" :end-time.sync="form.end_time" />
                </el-form-item>

                <el-button size="small" type="primary" @click="query(1)">查询</el-button>
                <el-button size="small" @click="onReset()">重置</el-button>

                <!-- 导出按钮 -->
                <export-data
                    class="m-l-10"
                    :method="apiLuckyDrawRecord"
                    :param="form"
                    type="bw"
                    :pageSize="pager.size"
                ></export-data>
            </el-form>

            <!-- 列表 -->
            <div class="list-table m-t-16">
                <el-table
                    :data="pager.lists"
                    style="width: 100%"
                    size="mini"
                    v-loading="pager.loading"
                    :header-cell-style="{ background: '#f5f8ff' }"
                >
                    <el-table-column label="用户信息">
                        <template slot-scope="scope">
                            <div class="flex">
                                <el-image :src="scope.row.avatar" style="width: 34px; height: 34px"></el-image>
                                <div class="m-l-10">
                                    <div>
                                        {{ scope.row.nickname }}
                                    </div>

                                    <div class="muted">编号：{{ scope.row.sn }}</div>
                                </div>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="name" label="奖品名称"> </el-table-column>
                    <el-table-column prop="prize_type_desc" label="奖品类型"> </el-table-column>
                    <!-- <el-table-column prop="prize_content" label="奖品内容">
                        <template slot-scope="scope">
                            <div v-if="scope.row.type != 4">{{ scope.row.prize_content }}</div>
                            <div v-else>{{ scope.row.name }}</div>
                        </template>
                    </el-table-column> -->
                    <el-table-column prop="status_desc" label="中奖状态"> </el-table-column>
                    <el-table-column prop="is_send_desc" label="兑换状态"> </el-table-column>
                    <el-table-column prop="create_time" label="中奖时间" min-width="120"> </el-table-column>
                    <el-table-column fixed="right" label="操作" min-width="100">
                        <template slot-scope="scope">
                            <el-button
                                type="text"
                                size="small"
                                slot="trigger"
                                v-if="scope.row.is_send === 1 && scope.row.type === 4"
                                @click="goOrder(scope.row.order_id)"
                                >查看订单</el-button
                            >
                            <span v-else>-</span>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <!-- 底部分页栏  -->
            <div class="flex row-right m-t-16 row-right">
                <ls-pagination v-model="pager" @change="query()" />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiLuckyDrawRecord, apiLuckyDrawGetPrizeType, apiLuckyDrawDetail } from '@/api/marketing/lucky_draw'
import ExportData from '@/components/export-data/index.vue'

import { RequestPaging } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'
import DatePicker from '@/components/date-picker.vue'
@Component({
    components: {
        LsPagination,
        DatePicker,
        ExportData
    }
})
export default class LuckyDrawLog extends Vue {
    // 分页查询
    pager: RequestPaging = new RequestPaging()

    form = {
        id: 0,
        start_time: '', //
        end_time: '', //
        user_info: '', // 支持手机号、昵称、用户编号
        prize_type: '', // 奖品类型
        status: '' // 中奖状态
    }
    prizeTypeList = []

    desc = {}
    create_time = ''
    apiLuckyDrawRecord = apiLuckyDrawRecord
    luckyDrawDetail() {
        apiLuckyDrawDetail({
            id: this.form.id
        }).then((res: any) => {
            this.desc = res
        })
    }

    luckyDrawGetPrizeType() {
        apiLuckyDrawGetPrizeType()
            .then((res: any) => {
                this.prizeTypeList = res
            })
            .catch((err: any) => {})
    }

    query(page?: number): void {
        page && (this.pager.page = page)
        this.pager
            .request({
                callback: apiLuckyDrawRecord,
                params: {
                    ...this.form
                }
            })
            .catch(() => {
                this.$message.error('数据请求失败，刷新重载')
            })
    }

    goOrder(id: string) {
        this.$router.push({
            path: '/order/order_detail',
            query: { id }
        })
    }

    onReset() {
        this.form.start_time = ''
        this.form.end_time = ''
        this.form.user_info = ''
        this.form.prize_type = ''
        this.form.status = ''
        this.query()
    }

    created() {
        const query: any = this.$route.query
        this.form.id = query.id
        this.query()
        this.luckyDrawGetPrizeType()
        this.luckyDrawDetail()
        this.create_time = query.create_time
    }
}
</script>

<style lang="scss" scoped></style>
