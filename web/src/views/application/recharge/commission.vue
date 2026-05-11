<template>
    <div class="recharge-commission">
        <div class="ls-card">
            <el-alert title="温馨提示：查看充值分销佣金记录，佣金在充值成功后即时结算到上级可提现余额。" type="info" :closable="false" show-icon>
            </el-alert>
            <div class="journal-search m-t-16">
                <el-form ref="formRef" inline :model="form" label-width="100px" size="small" class="ls-form">
                    <el-form-item label="充值用户">
                        <el-input v-model="form.keyword" placeholder="用户编号/昵称/手机号"></el-input>
                    </el-form-item>
                    <el-form-item label="佣金层级">
                        <el-select v-model="form.level" placeholder="全部">
                            <el-option label="全部" value=""></el-option>
                            <el-option label="一级佣金" value="1"></el-option>
                            <el-option label="二级佣金" value="2"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="佣金状态">
                        <el-select v-model="form.status" placeholder="全部">
                            <el-option label="全部" value=""></el-option>
                            <el-option label="已结算" value="1"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="结算时间">
                        <el-date-picker
                            v-model="tableData"
                            type="datetimerange"
                            align="right"
                            unlink-panels
                            range-separator="至"
                            start-placeholder="开始时间"
                            end-placeholder="结束时间"
                            :picker-options="pickerOptions"
                            @change="splitTime"
                            value-format="yyyy-MM-dd HH:mm:ss"
                        >
                        </el-date-picker>
                    </el-form-item>

                    <el-button size="small" type="primary" @click="getList(1)">查询</el-button>
                    <el-button size="small" @click="onReset">重置</el-button>
                </el-form>
            </div>
        </div>

        <div class="m-t-16 ls-card">
            <el-table :data="pager.lists" v-loading="pager.loading" style="width: 100%" size="mini" :header-cell-style="{ background: '#f5f8ff' }">
                <el-table-column prop="sn" label="佣金编号" min-width="160"></el-table-column>
                <el-table-column prop="recharge_order_sn" label="充值订单编号" min-width="160"></el-table-column>
                <el-table-column label="充值用户" min-width="150">
                    <template slot-scope="scope">
                        <div class="flex">
                            <el-image class="flex-none" style="width: 36px; height: 36px; border-radius: 50%" :src="scope.row.from_user_avatar" />
                            <div class="m-l-8">
                                <div>{{ scope.row.from_user_nickname }}</div>
                                <div class="muted">{{ scope.row.from_user_sn }}</div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="获佣用户" min-width="150">
                    <template slot-scope="scope">
                        <div class="flex">
                            <el-image class="flex-none" style="width: 36px; height: 36px; border-radius: 50%" :src="scope.row.user_avatar" />
                            <div class="m-l-8">
                                <div>{{ scope.row.user_nickname }}</div>
                                <div class="muted">{{ scope.row.user_sn }}</div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="level_desc" label="佣金层级" min-width="100"></el-table-column>
                <el-table-column prop="recharge_amount" label="充值金额(¥)" min-width="110"></el-table-column>
                <el-table-column prop="ratio" label="佣金比例(%)" min-width="110"></el-table-column>
                <el-table-column prop="earnings" label="佣金金额(¥)" min-width="110">
                    <template slot-scope="scope">
                        <span style="color: #E6A23C; font-weight: 500">{{ scope.row.earnings }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="status_desc" label="状态" min-width="80"></el-table-column>
                <el-table-column prop="settle_time" label="结算时间" min-width="160"></el-table-column>
                <el-table-column prop="create_time" label="创建时间" min-width="160"></el-table-column>
            </el-table>

            <div class="m-t-16 flex row-right">
                <ls-pagination v-model="pager" @change="getList()" />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { RequestPaging } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'
import { apiRechargeCommissionLists } from '@/api/application/recharge'
@Component({
    components: {
        LsPagination
    }
})
export default class RechargeCommission extends Vue {
    tableData: any = []
    pager: RequestPaging = new RequestPaging()
    form = {
        keyword: '',
        level: '',
        status: '',
        start_time: '',
        end_time: ''
    }
    pickerOptions: any = {
        shortcuts: [
            {
                text: '最近一周',
                onClick(picker: any) {
                    const end = new Date()
                    const start = new Date()
                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
                    picker.$emit('pick', [start, end])
                }
            },
            {
                text: '最近一个月',
                onClick(picker: any) {
                    const end = new Date()
                    const start = new Date()
                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
                    picker.$emit('pick', [start, end])
                }
            },
            {
                text: '最近三个月',
                onClick(picker: any) {
                    const end = new Date()
                    const start = new Date()
                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 90)
                    picker.$emit('pick', [start, end])
                }
            }
        ]
    }

    splitTime() {
        if (this.tableData != null) {
            this.form.start_time = this.tableData[0]
            this.form.end_time = this.tableData[1]
        }
    }

    onReset() {
        this.form = {
            keyword: '',
            level: '',
            status: '',
            start_time: '',
            end_time: ''
        }
        this.tableData = []
        this.getList()
    }

    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apiRechargeCommissionLists,
            params: {
                ...this.form
            }
        })
    }

    created() {
        this.getList()
    }
}
</script>

<style lang="scss" scoped></style>
