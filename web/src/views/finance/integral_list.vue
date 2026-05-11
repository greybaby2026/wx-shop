<!-- 积分明细 -->
<template>
    <div class="user-withdrawal">
        <div class="ls-card">
            <el-alert class="xxl" title="温馨提示： 1.会员账户积分变动记录。" type="info" :closable="false" show-icon>
            </el-alert>
            <div class="journal-search m-t-16">
                <el-form ref="formRef" inline :model="form" label-width="70px" size="small" class="ls-form">
                    <el-form-item label="用户信息">
                        <el-select v-model="isNameSN" placeholder="用户编号" style="width: 120px">
                            <el-option label="用户编号" value="0"></el-option>
                            <el-option label="用户昵称" value="1"></el-option>
                            <el-option label="手机号码" value="2"></el-option>
                        </el-select>
                        <el-input v-if="isNameSN == 0" v-model="form.sn" placeholder=""> </el-input>
                        <el-input v-if="isNameSN == 1" v-model="form.nickname" placeholder=""></el-input>
                        <el-input v-if="isNameSN == 2" v-model="form.mobile" placeholder=""></el-input>
                    </el-form-item>
                    <el-form-item label="变动类型" prop="type">
                        <el-select v-model="form.change_type" placeholder="全部">
                            <div v-for="(value, key) in changeTypeList" :key="key">
                                <el-option :label="value" :value="key"></el-option>
                            </div>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="记录时间">
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
                        :method="apiAccountLog"
                        :param="form"
                        type="integral"
                        :pageSize="pager._size"
                    ></export-data>
                </el-form>
            </div>
        </div>
        <!-- 提现记录表 -->
        <div class="ls-withdrawal__centent ls-card m-t-16">
            <div class="list-table m-t-16">
                <el-table
                    :data="pager.lists"
                    style="width: 100%"
                    v-loading="pager.loading"
                    size="mini"
                    :header-cell-style="{ background: '#f5f8ff' }"
                >
                    <el-table-column prop="nickname" label="用户昵称"> </el-table-column>
                    <el-table-column prop="sn" label="用户编号"> </el-table-column>
                    <el-table-column prop="mobile" label="手机号码"> </el-table-column>
                    <el-table-column prop="change_amount" label="变动积分"> </el-table-column>
                    <el-table-column prop="left_amount" label="剩余积分"> </el-table-column>
                    <el-table-column prop="change_type_desc" label="变动类型"> </el-table-column>
                    <el-table-column prop="association_sn" label="来源单号"> </el-table-column>
                    <el-table-column prop="create_time" label="记录时间"> </el-table-column>
                </el-table>
            </div>
            <div class="flex row-right m-t-16 row-right">
                <ls-pagination v-model="pager" @change="getList()" />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import { apiAccountLog, apiIntegralChangeType } from '@/api/finance/finance'
import { PageMode } from '@/utils/type'
import { RequestPaging } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'
import ExportData from '@/components/export-data/index.vue'
@Component({
    components: {
        LsPagination,
        ExportData
    }
})
export default class IntegralList extends Vue {
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
    tableData = []
    // 顶部查询表单
    pager: RequestPaging = new RequestPaging()
    isNameSN = '' // 选择用户编号0 选择用户昵称1 手机号码2
    // 顶部查询表单
    form = {
        nickname: '', // 用户昵称
        sn: '', // 用户编号
        changeType: '', // 变动类型
        start_time: '',
        end_time: '',
        mobile: '', // 手机号
        type: 'integral' // 类型 bnw-不可提现余额类型 integral-积分类型
    }
    // 变动类型数据
    changeTypeList = []

    apiAccountLog = apiAccountLog
    /** E Data **/

    // 监听用户信息查询框条件
    @Watch('isNameSN', {
        immediate: true
    })
    getChange(val: any) {
        // 初始值
        this.form.sn = ''
        this.form.nickname = ''
        this.form.mobile = ''
    }

    /** S Methods **/
    splitTime() {
        if (this.tableData != null) {
            this.form.start_time = this.tableData[0]
            this.form.end_time = this.tableData[1]
        }
    }
    // 重置
    onReset() {
        this.form = {
            nickname: '', // 用户昵称
            sn: '', // 用户编号
            changeType: '', // 变动类型
            start_time: '',
            end_time: '',
            mobile: '',
            type: 'integral'
        }
        this.tableData = []
        this.getList()
    }
    // 资金记录
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager
            .request({
                callback: apiAccountLog,
                params: {
                    ...this.form
                }
            })
            .then((res: any) => {})
    }

    // 变动类型数据
    getSearchList() {
        apiIntegralChangeType().then((res: any) => {
            this.changeTypeList = res
        })
    }
    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.getList()
        this.getSearchList()
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped></style>
