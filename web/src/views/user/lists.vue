<!-- 用户管理 -->
<template>
    <div class="user-management">
        <div class="ls-User__top ls-card">
            <el-alert
                class="xxl"
                title="温馨提示：1.管理用户信息，可以进行编辑、账户调整、等级调整和资料查看等操作。"
                type="info"
                :closable="false"
                show-icon
            ></el-alert>
            <div class="member-search m-t-16">
                <el-form ref="form" inline :model="form" label-width="70px" size="small">
                    <el-form-item label="用户信息">
                        <el-input
                            class="ls-select-keyword"
                            v-model="form.keyword"
                            placeholder="请输入用户编号/昵称/手机号码"
                        ></el-input>
                    </el-form-item>
                    <el-form-item label="用户等级">
                        <el-select class="ls-select" v-model="form.level" placeholder="全部">
                            <div v-for="(value, key) in userLevelList" :key="key">
                                <el-option :label="value.name" :value="value.id"></el-option>
                            </div>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="用户标签">
                        <el-select class="ls-select" v-model="form.label_id" placeholder="全部">
                            <div v-for="(value, key) in userLabelList" :key="key">
                                <el-option :label="value.name" :value="value.id"></el-option>
                            </div>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="消费金额">
                        <div class="flex">
                            <el-input
                                class="ls-input-price"
                                v-model="form.min_amount"
                                size="small"
                                clearable
                                placeholder="最低价"
                            ></el-input>
                            <div class="m-l-10 m-r-10 lighter">至</div>
                            <el-input
                                class="ls-input-price"
                                v-model="form.max_amount"
                                size="small"
                                clearable
                                placeholder="最高价"
                            ></el-input>
                        </div>
                    </el-form-item>
                    <el-form-item label="注册来源">
                        <el-select class="ls-select" v-model="form.source" placeholder="全部">
                            <div v-for="(value, key) in sourceList" :key="key">
                                <el-option :label="value" :value="key"></el-option>
                            </div>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="用户状态">
                        <el-select class="ls-select" v-model="form.disable" placeholder="全部">
                            <el-option label="冻结" :value="1"></el-option>
                            <el-option label="正常" :value="0"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="注册时间">
                        <el-date-picker
                            v-model="timeForm"
                            type="datetimerange"
                            align="right"
                            unlink-panels
                            range-separator="至"
                            start-placeholder="开始日期"
                            end-placeholder="结束日期"
                            :picker-options="pickerOptions"
                            @change="splitTime"
                            value-format="yyyy-MM-dd HH:mm:ss"
                        ></el-date-picker>
                    </el-form-item>

                    <el-button size="small" type="primary" @click="query()">查询</el-button>
                    <el-button size="small" @click="onReset()">重置</el-button>
                    <!-- 导出按钮 -->
                    <export-data
                        class="m-l-10"
                        :method="apiUserList"
                        :param="form"
                        :pageSize="pager.size"
                    ></export-data>
                </el-form>
            </div>
        </div>

        <div class="ls-User__centent ls-card m-t-16">
            <div class="list-header">
                <el-button size="small" type="primary" :disabled="!multipleSelection.length" @click="openDialogVisible"
                    >设置用户标签</el-button
                >
                <ls-dialog
                    class="inline m-l-10"
                    content="确定批量冻结会员？请谨慎操作。"
                    :disabled="!multipleSelection.length"
                    @confirm="handleBatchFrozen(1)"
                >
                    <el-button slot="trigger" size="small" :disabled="!multipleSelection.length">批量冻结</el-button>
                </ls-dialog>
                <ls-dialog
                    class="inline m-l-10"
                    content="确定取消冻结会员？请谨慎操作。"
                    :disabled="!multipleSelection.length"
                    @confirm="handleBatchFrozen(0)"
                >
                    <el-button slot="trigger" size="small" :disabled="!multipleSelection.length">取消冻结</el-button>
                </ls-dialog>
            </div>
            <div class="list-table m-t-16">
                <el-table
                    :data="pager.lists"
                    style="width: 100%"
                    size="mini"
                    v-loading="pager.loading"
                    :header-cell-style="{ background: '#f5f8ff' }"
                    @selection-change="handleSelectionChange"
                >
                    <el-table-column type="selection" width="55"></el-table-column>
                    <el-table-column prop="sn" label="用户编号"></el-table-column>
                    <el-table-column label="用户头像">
                        <template slot-scope="scope">
                            <div class="flex">
                                <el-image :src="scope.row.avatar" style="width: 34px; height: 34px"></el-image>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="nickname" label="用户昵称">
                        <template slot-scope="scope">
                            <div class="flex">
                                <div>{{ scope.row.nickname }}</div>
                                <div style="color: red" v-if="scope.row.user_delete == 1">(已注销)</div>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="name" label="用户等级"></el-table-column>
                    <el-table-column prop="mobile" label="手机号码"></el-table-column>
                    <el-table-column prop="total_user_money" label="钱包金额">
                        <template slot-scope="scope">
                            <div class="flex">
                                <div class v-if="scope.row.total_user_money">￥</div>
                                <div class>
                                    {{ scope.row.total_user_money }}
                                </div>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="total_order_amount" label="消费金额"></el-table-column>
                    <el-table-column label="用户状态" min-width="80">
                        <template slot-scope="scope">{{ scope.row.disable ? '冻结' : '正常' }}</template>
                    </el-table-column>

                    <el-table-column prop="login_time" label="最后登陆时间" min-width="120"></el-table-column>
                    <el-table-column prop="create_time" label="注册时间" min-width="120"></el-table-column>
                    <el-table-column fixed="right" label="操作" min-width="120">
                        <template slot-scope="scope">
                            <el-button @click="DetailsClick(scope.row)" type="text" size="small">详情</el-button>
                            <ls-dialog
                                v-if="scope.row.disable == 0 && !scope.row.user_delete"
                                class="m-l-10 inline"
                                content="是否确认冻结会员吗？冻结后，该账号将无法访问店铺"
                                @confirm="handleFrozen(scope.row)"
                            >
                                <el-button slot="trigger" type="text" size="small">冻结会员</el-button>
                            </ls-dialog>
                            <ls-dialog
                                v-if="scope.row.disable == 1 && !scope.row.user_delete"
                                class="m-l-10 inline"
                                content="是否取消冻结会员吗？"
                                @confirm="handleFrozen(scope.row)"
                            >
                                <el-button slot="trigger" type="text" size="small">取消冻结</el-button>
                            </ls-dialog>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <!-- 底部分页栏  -->
            <div class="flex row-right m-t-16 row-right">
                <ls-pagination v-model="pager" @change="getUserList()" />
            </div>
        </div>

        <!-- 设置用户标签弹出框 -->
        <el-dialog
            title="设置用户标签"
            :visible.sync="dialogVisible"
            width="60vh"
            top="40vh"
            center
            @close="closeDialog"
        >
            <el-form ref="form" inline :model="form" label-width="70px" size="small">
                <el-form-item label="用户标签">
                    <el-select v-model="labelValue" multiple placeholder="请选择">
                        <div v-for="(val, key) in userLabelList" :key="key">
                            <el-option :label="val.name" :value="val.id"></el-option>
                        </div>
                    </el-select>
                    <div class="xxs lighter">可以多选用户标签</div>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="dialogVisible = false">取 消</el-button>
                <el-button type="primary" @click="changeUserLabel">确 定</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiUserList, apiUserSearchList, apiUserSetLabel, apiUserSetInfo } from '@/api/user/user'
import { PageMode } from '@/utils/type'
import { RequestPaging } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'
import ExportData from '@/components/export-data/index.vue'
import LsDialog from '../../components/ls-dialog.vue'
@Component({
    components: {
        LsPagination,
        ExportData,
        LsDialog
    }
})
export default class UserManagement extends Vue {
    /** S Data **/
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
    form = {
        keyword: '', // 用户信息
        level: '', // 用户等级
        label_id: '', // 用户标签
        min_amount: '', // 消费最小金额
        max_amount: '', // 消费最大金额
        source: '', // 注册来源
        create_start_time: 0, // 注册开始时间（传时间戳）
        create_end_time: 0, // 注册结算时间（传时间戳）
        disable: '' //会员状态
    }
    // 日期选择器数据
    timeForm = []

    // 设置用户标签弹出框
    dialogVisible = false
    // 选中用户标签
    labelValue = []
    // 用户选择框数据
    userLevelList = {}
    userLabelList = {}
    sourceList = {}
    // 分页查询
    pager: RequestPaging = new RequestPaging()
    // 被选中的用户id
    multipleSelection = []
    /** E Data **/

    /** S Methods **/
    apiUserList = apiUserList // 传递给导出组件的api

    // 选中的用户触发事件
    handleSelectionChange(val: any) {
        this.multipleSelection = val
    }

    // 查询按钮
    query() {
        this.pager.page = 1
        if (this.form.min_amount && this.form.max_amount) {
            if (this.form.min_amount >= this.form.max_amount) {
                return this.$message.error('消费金额最低价应小于最高价')
            }
        }
        this.getUserList()
    }

    //获取用户列表数据
    getUserList() {
        this.pager.request({
            callback: apiUserList,
            params: {
                ...this.form
            }
        })
    }

    // 获取用户搜索条件列表
    getUserSearchList() {
        apiUserSearchList().then((res: any) => {
            this.userLevelList = res.user_level_list
            this.userLabelList = res.user_label_list
            this.sourceList = res.source_list
        })
    }
    // 转换为时间
    add(m: number) {
        return m < 10 ? '0' + m : m
    }
    baseTime(event: any) {
        const d = new Date(event)
        return `${this.add(d.getFullYear())}-${this.add(d.getMonth() + 1)}-${this.add(d.getDate())} ${this.add(
            d.getHours()
        )}:${this.add(d.getMinutes())}:${this.add(d.getSeconds())}`
    }
    // 拆分日期选择器时间
    splitTime() {
        if (this.timeForm != null) {
            this.form.create_start_time = new Date(this.timeForm[0]).getTime() / 1000
            this.form.create_end_time = new Date(this.timeForm[1]).getTime() / 1000
        }
    }
    // 重置按钮
    onReset() {
        this.form = {
            keyword: '', // 用户信息
            level: '', // 用户等级
            label_id: '', // 用户标签
            min_amount: '', // 消费最小金额
            max_amount: '', // 消费最大金额
            source: '', // 注册来源
            create_start_time: 0, // 注册开始时间（传时间戳）
            create_end_time: 0, // 注册结算时间（传时间戳）
            disable: '' //会员状态
        }
        this.timeForm = []
        this.getUserList()
    }
    // 打开设置用户标签弹窗
    openDialogVisible() {
        if (!this.multipleSelection) {
            this.$message.error('请选择用户!')
            return
        }
        if (this.multipleSelection.length <= 0) {
            this.$message.error('请选择用户!')
            return
        }
        this.dialogVisible = true
    }
    // 设置用户标签
    changeUserLabel() {
        let userIds: Array<Object> = []
        this.multipleSelection.forEach((item: any) => {
            userIds = [...userIds, item.id]
        })
        apiUserSetLabel({
            user_ids: userIds,
            label_ids: this.labelValue
        })
            .then(res => {
                this.getUserList()
            })
            .catch(res => {})
        this.dialogVisible = false
    }
    // 标签弹框关闭事件
    closeDialog() {
        this.labelValue = []
    }

    // 用户详情
    DetailsClick(item: any) {
        this.$router.push({
            path: '/user/user_details',
            query: {
                id: item.id
            }
        })
    }

    // 冻结用户
    handleFrozen(userInfo: any) {
        let { disable, id } = userInfo
        disable = disable == 0 ? 1 : 0
        this.setUserInfo(id, 'disable', disable)
    }
    //设置用户信息
    setUserInfo(id: number | number[], type: string, value: any) {
        apiUserSetInfo({
            user_id: id,
            field: type,
            value
        }).then(res => {
            this.getUserList()
        })
    }

    // 批量冻结
    handleBatchFrozen(value: any) {
        const userIds = this.multipleSelection.map((item: any) => item.id)
        this.setUserInfo(userIds, 'disable', value)
    }
    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.getUserList()
        this.getUserSearchList()
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.user-management {
    .ls-user__top {
        padding: 16px 24px;
    }
}
</style>
