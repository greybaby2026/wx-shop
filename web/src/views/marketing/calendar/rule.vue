<template>
    <div class="ls-add-admin">
        <div class="ls-card ls-coupon-edit__form">
            <div class="nr weight-500 m-b-20">签到设置</div>
            <el-form ref="list" :model="list" label-width="120px" size="small">
                <el-form-item label="应用状态">
                    <el-radio v-model="list.is_open" :label="0">关闭</el-radio>
                    <el-radio v-model="list.is_open" :label="1">开启</el-radio>
                    <span class="desc">关闭或开启积分签到功能，关闭后商城的积分签到处于未开启状态</span>
                </el-form-item>
            </el-form>
        </div>

        <div class="ls-card ls-coupon-edit__form m-t-16">
            <div class="nr weight-500 m-b-20">签到规则</div>

            <el-form label-width="120px">
                <el-form-item label="每天签到奖励">
                    <el-checkbox v-model="status">积分</el-checkbox>
                    <el-input v-model="list.daily.integral"></el-input>
                </el-form-item>

                <el-form-item label="连续签到奖励">
                    <el-table :data="list.continuous" style="width: 100%" size="mini">
                        <el-table-column prop="days" min-width="100" label="连续天数">
                            <template slot-scope="scope">
                                <el-input style="width: 200px" v-model="scope.row.days" show-word-limit />
                            </template>
                        </el-table-column>
                        <el-table-column min-width="100" prop="integral" label="连续奖励">
                            <template slot-scope="scope">
                                <el-input style="width: 200px" v-model="scope.row.integral" show-word-limit />
                            </template>
                        </el-table-column>
                        <el-table-column min-width="100" prop="sort" label="排序">
                            <template slot-scope="scope">
                                <el-input style="width: 200px" v-model="scope.row.sort" show-word-limit />
                            </template>
                        </el-table-column>
                        <el-table-column fixed="right" label="操作" min-width="120">
                            <template slot-scope="scope">
                                <div class="flex">
                                    <ls-dialog
                                        class="inline flex row-center m-r-24"
                                        :content="'确认删除这条连续签到奖励吗？'"
                                        width="30vw"
                                        @confirm="delRule(scope.row, scope.$index)"
                                    >
                                        <el-button type="text" size="small" slot="trigger">删除</el-button>
                                    </ls-dialog>
                                </div>
                            </template>
                        </el-table-column>
                    </el-table>

                    <el-button type="primary" @click="handleAddItem"> 添加 </el-button>
                </el-form-item>

                <el-form-item label="每天签到奖励">
                    <el-input class="m-t-10" type="textarea" :rows="10" placeholder="请输入内容" v-model="list.remark">
                    </el-input>
                </el-form-item>
            </el-form>
        </div>

        <!-- 底部保存或取消 -->
        <div class="bg-white flex row-center ls-fixed-footer">
            <div class="row-center flex">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="onSubmit('form')">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import lsDialog from '@/components/ls-dialog.vue'
import {
    apiCalendarGetRule,
    apiCalendarSetRule,
    apiCalendarAddRule,
    apiCalendarEditRule,
    apiCalendarDelRule
} from '@/api/marketing/calendar'

@Component({
    components: {
        lsDialog
    }
})
export default class CalendarRuleEdit extends Vue {
    /** S Data **/

    // 规则信息
    list: any = {
        is_open: 1,
        daily: {
            integral_status: false,
            integral: ''
        },
        continuous: [],
        remark: ''
    }

    // 连续签到奖励信息
    signAdd = {
        days: '', //是	integer	连续签到天数
        integral_status: '0', //是	integer	赠送积分状态 0-关闭 1-开启
        integral: '' //是	integer	赠送积分数量
    }

    /** E Data **/

    /** S Methods **/
    // 添加连续签到奖励数据
    addRule() {
        apiCalendarAddRule({ ...this.signAdd }).then(res => {
            this.detail()
            // this.$message.success("添加成功!");
        })
    }

    setting(row: any) {
        this.signAdd.days = row.days
        this.signAdd.integral_status = row.integral_status
        this.signAdd.integral = row.integral
    }

    // 编辑连续签到奖励
    editRule(data: any) {
        apiCalendarEditRule({
            ...data
        }).then(res => {
            this.detail()
            // this.$message.success("修改成功!");
        })
    }

    // 删除连续签到天数
    delRule(data: any, index: number) {
        let ableDelete = false
        if (Object.keys(data).length === 3) {
            ableDelete = true
        }

        if (ableDelete) {
            this.list.continuous.splice(index, 1)
            return this.$message.success('删除成功!')
        }

        apiCalendarDelRule({ id: data.id }).then(res => {
            this.detail()
            // this.$message.success("删除成功!");
        })
    }

    // 设置签到规则
    onSubmit() {
        this.list.daily.integral_status = this.list.daily.integral_status ? 1 : 0
        apiCalendarSetRule({ ...this.list })
            .then(() => {
                this.detail()
                // this.$message.success("修改成功!");
            })
            .catch(() => {
                // this.$message.error("数据获取失败!");
            })
    }

    // 详情
    detail() {
        apiCalendarGetRule({})
            .then(res => {
                this.list = res
            })
            .catch(() => {
                // this.$message.error("数据获取失败!");
            })
    }

    handleAddItem() {
        this.list.continuous.push({
            days: '',
            integral: '',
            sort: ''
        })
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.detail()
    }
    /** E Life Cycle **/

    get status() {
        if (this.list.daily.integral_status == 1) {
            return true
        }
        return false
    }

    set status(event: any) {
        this.list.daily.integral_status = event
    }
}
</script>

<style lang="scss" scoped>
.ls-add-admin {
    padding-bottom: 80px;

    .ls-input {
        width: 380px;
    }

    .desc {
        display: block;
        color: #999;
        font-size: 12px;
    }

    .add-btn {
        width: 800px;
        height: 40px;
        box-sizing: border-box;
        border: 2px solid #f5f5f5;
    }

    .item {
        padding: 30px 0;
        margin-bottom: 50rpx;
        position: relative;
        background-color: #f5f5f5;
        .del {
            right: 10px;
            top: 0px;
            font-size: 24px;
            position: absolute;
        }
    }
}
</style>
