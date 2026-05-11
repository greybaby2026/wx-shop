<!-- 用户提现 -->
<template>
    <div class="user-withdrawal">
        <div class="ls-card">
            <el-alert
                class="xxl"
                title="温馨提示： 1.审核会员的佣金提现申请；2.佣金提现支持微信、支付宝转账；3.提现失败会退回全部金额。"
                type="info"
                :closable="false"
                show-icon
            >
            </el-alert>
            <div class="journal-search m-t-16">
                <el-form ref="formRef" inline :model="form" label-width="70px" size="small" class="ls-form">
                    <el-form-item label="提现单号" prop="sn">
                        <el-input v-model="form.sn" placeholder="请输入"></el-input>
                    </el-form-item>
                    <el-form-item label="会员信息">
                        <el-input v-model="form.user_info" placeholder="请输入用户昵称/编号/手机号"></el-input>
                    </el-form-item>
                    <el-form-item label="提现方式" prop="type">
                        <el-select v-model="form.type" placeholder="全部">
                            <el-option label="全部" value=""></el-option>
                            <el-option label="钱包余额" value="1"></el-option>
                            <el-option label="微信零钱" value="2"></el-option>
                            <el-option label="银行卡" value="3"></el-option>
                            <el-option label="微信收款码" value="4"></el-option>
                            <el-option label="支付宝收款码" value="5"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="提现时间">
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
                    <!-- 导出按钮 -->
                    <export-data
                        class="m-l-10"
                        :method="apiWithdrawLists"
                        :param="form"
                        :pageSize="pager._size"
                    ></export-data>
                </el-form>
            </div>
        </div>
        <!-- 提现记录表 -->
        <div class="ls-withdrawal__centent ls-card m-t-16">
            <el-tabs v-model="form.status" v-loading="pager.loading" @tab-click="getList(1)">
                <el-tab-pane :label="`全部(${tabCount.all})`" name=" ">
                    <withdraw-pane v-model="pager.lists" :pager="pager" @refresh="getList()" />
                </el-tab-pane>
                <el-tab-pane :label="`待提现(${tabCount.status_wait})`" name="1">
                    <withdraw-pane v-model="pager.lists" :pager="pager" @refresh="getList()" />
                </el-tab-pane>
                <el-tab-pane lazy :label="`提现中(${tabCount.status_ing})`" name="2">
                    <withdraw-pane v-model="pager.lists" :pager="pager" @refresh="getList()" />
                </el-tab-pane>
                <el-tab-pane lazy :label="`提现成功(${tabCount.status_success})`" name="3">
                    <withdraw-pane v-model="pager.lists" :pager="pager" @refresh="getList()" />
                </el-tab-pane>
                <el-tab-pane lazy :label="`提现失败(${tabCount.status_fail})`" name="4">
                    <withdraw-pane v-model="pager.lists" :pager="pager" @refresh="getList()" />
                </el-tab-pane>
            </el-tabs>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import { apiWithdrawLists } from '@/api/finance/finance'
import { RequestPaging } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'
import WithdrawPane from '@/components/finance/withdraw-pane.vue'
import ExportData from '@/components/export-data/index.vue'
@Component({
    components: {
        LsPagination,
        WithdrawPane,
        ExportData
    }
})
export default class UserWithdrawal extends Vue {
    /** S Data **/
    // 日期选择器
    pickerOptions = {
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

    apiWithdrawLists = apiWithdrawLists

    tableData = []
    // 顶部查询表单
    pager: RequestPaging = new RequestPaging()
    // 顶部查询表单
    form = {
        nickname: '', // 用户昵称
        user_sn: '', // 用户编号
        sn: '', // 提现单号
        type: '', // 提现类型 1-钱包余额；2-微信零钱；3-银行卡;4-微信收款码; 5-支付宝收款码
        status: ' ', // 提现状态 1-待提现 2-提现中 3-提现成功 4-提现失败 ' '-全部
        start_time: '',
        end_time: '',
        user_info: '' //会员信息 用户昵称/用户编号
    }
    // 各状态数量
    tabCount = {
        all: 0,
        status_wait: 0,
        status_ing: 0,
        status_success: 0,
        status_fail: 0
    }
    /** E Data **/

    /** S Methods **/
    splitTime() {
        if (this.tableData != null) {
            this.form.start_time = this.tableData[0]
            this.form.end_time = this.tableData[1]
        }
    }
    // 重置
    onReset() {
        this.tableData = []
        this.form.sn = ''
        this.form.type = ''
        this.form.start_time = ''
        this.form.end_time = ''
        this.form.user_info = ''

        this.getList()
    }
    // 提现列表
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager
            .request({
                callback: apiWithdrawLists,
                params: {
                    ...this.form
                }
            })
            .then((res: any) => {
                this.tabCount = res?.extend
            })
    }
    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.getList()
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped></style>
