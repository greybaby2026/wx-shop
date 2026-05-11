<template>
    <div class="withdraw-pane">
        <div class="pane-table">
            <div class="list-table">
                <el-table
                    ref="valueRef"
                    :data="value"
                    style="width: 100%"
                    size="mini"
                    :header-cell-style="{ background: '#f5f8ff' }"
                >
                    <el-table-column prop="sn" label="提现单号"> </el-table-column>
                    <el-table-column label="用户信息" min-width="140">
                        <template slot-scope="scope">
                            <div class="flex">
                                <el-image style="width: 58px; height: 58px" class="flex-none" :src="scope.row.avatar">
                                </el-image>
                                <div class="m-l-8">
                                    <!-- <div>用户编号：{{scope.row.user_sn}}</div> -->
                                    <div>{{ scope.row.nickname }}</div>
                                    <!-- <div>用户等级：{{scope.row.level_name}}</div> -->
                                </div>
                            </div>
                        </template>
                    </el-table-column>
                    <!-- <el-table-column prop="mobile" label="手机号码">
					</el-table-column> -->
                    <el-table-column prop="money" label="提现金额">
                        <template slot-scope="scope">
                            <div>¥{{ scope.row.money }}</div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="handling_fee" label="手续费">
                        <template slot-scope="scope">
                            <div>¥{{ scope.row.handling_fee }}</div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="left_money" label="到账金额">
                        <template slot-scope="scope">
                            <div style="color: #ff2c3c">¥{{ scope.row.left_money }}</div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="type_desc" label="提现方式"> </el-table-column>
                    <el-table-column prop="status_desc" label="提现状态">
                        <template slot-scope="scope">
                            <div v-if="scope.row.status == 1" style="color: #02b46d">
                                {{ scope.row.status_desc }}
                            </div>
                            <div v-else-if="scope.row.status == 4" style="color: #ff2c3c">
                                {{ scope.row.status_desc }}
                            </div>
                            <div v-else>{{ scope.row.status_desc }}</div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="apply_remark" label="提现备注"> </el-table-column>
                    <el-table-column prop="create_time" label="申请时间"> </el-table-column>
                    <el-table-column label="操作" min-width="120" fixed="right">
                        <template slot-scope="scope">
                            <ls-dialog class="inline m-r-10" title="提现详情" width="60%" top="50px">
                                <el-button
                                    type="text"
                                    slot="trigger"
                                    size="small"
                                    @click="onWithdrawDetail(scope.row.id)"
                                    >详情</el-button
                                >
                                <slot>
                                    <ls-withdrawal-details v-model="withdrawDetail"> </ls-withdrawal-details>
                                </slot>
                            </ls-dialog>
                            <ls-dialog
                                class="inline m-r-10"
                                title="审核通过"
                                width="60%"
                                v-if="scope.row.status == 1"
                                @confirm="onWithdrawPass(scope.row.id)"
                                cancel="onClearPass"
                            >
                                <el-button type="text" slot="trigger" size="small" style="color: #02b46d">通过 </el-button>
                                <slot>
                                    <el-form ref="valueRef" label-width="240px" size="small">
                                        <el-form-item label="确认审核通过？">
                                            <div class="xxs lighter">审核通过后，系统将自动处理转账</div>
                                        </el-form-item>
                                        <el-form-item label="审核说明">
                                            <el-input
                                                v-model="pass_remark"
                                                placeholder="请输入审核说明（选填）"
                                                type="textarea"
                                                style="width: 400px"
                                            ></el-input>
                                        </el-form-item>
                                    </el-form>
                                </slot>
                            </ls-dialog>
                            <ls-dialog
                                class="inline m-r-10"
                                title="审核拒绝"
                                width="60%"
                                v-if="scope.row.status == 1"
                                @confirm="onWithdrawRefuse(scope.row.id)"
                                cancel="onClearRefuse"
                            >
                                <el-button type="text" slot="trigger" size="small" style="color: #ff2c3c">拒绝 </el-button>
                                <slot>
                                    <el-form ref="valueRef" label-width="240px" size="small">
                                        <el-form-item label="拒绝原因" required>
                                            <el-input
                                                v-model="refuse_remark"
                                                placeholder="请输入拒绝原因"
                                                type="textarea"
                                                style="width: 400px"
                                            ></el-input>
                                            <div class="xxs lighter">审核拒绝后，提现金额会全部退回佣金账户</div>
                                        </el-form-item>
                                    </el-form>
                                </slot>
                            </ls-dialog>
                            <ls-dialog
                                class="inline m-r-10"
                                v-if="scope.row.status == 2 && scope.row.type == 2"
                                content="查询微信零钱发放结果，发放成功则自动标记提现成功，否则标记失败退回佣金。"
                                @confirm="onWithdrawSearch(scope.row.id)"
                            >
                                <el-button type="text" slot="trigger" size="small">查询结果 </el-button>
                            </ls-dialog>
                            <ls-dialog
                                class="inline m-r-10"
                                title="转账"
                                width="60%"
                                v-if="scope.row.status == 2 && scope.row.type != 2"
                                @confirm="onTransfer(scope.row.id)"
                            >
                                <el-button type="text" slot="trigger" size="small">转账 </el-button>
                                <slot>
                                    <el-form ref="valueRef" label-width="240px" size="small">
                                        <el-form-item label="转账操作" required>
                                            <!-- 单选按钮 -->
                                            <el-radio-group class="m-r-16" v-model="isTransfer">
                                                <el-radio :label="1">转账成功</el-radio>
                                                <el-radio :label="2">转账失败</el-radio>
                                            </el-radio-group>
                                            <div class="xxs lighter">转账失败后，提现金额会全部退回佣金账户</div>
                                        </el-form-item>
                                        <el-form-item label="转账凭证">
                                            <material-select :limit="1" v-model="imgTransfer" />
                                            <div class="muted xs m-r-16">
                                                建议尺寸：400*400像素，支持jpg，jpeg，png格式
                                            </div>
                                        </el-form-item>
                                        <el-form-item label="转账说明">
                                            <el-input
                                                v-model="transfer_remark"
                                                placeholder="请输入提现说明"
                                                type="textarea"
                                                style="width: 400px"
                                            ></el-input>
                                        </el-form-item>
                                    </el-form>
                                </slot>
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
import LsWithdrawalDetails from '@/components/finance/ls-withdrawal-details.vue'
import LsPagination from '@/components/ls-pagination.vue'
import MaterialSelect from '@/components/material-select/index.vue'
// import PopoverInput from '@/components/popover-input.vue'
import {
    apiWithdrawSearch,
    apiWithdrawDetail,
    apiWithdrawPass,
    apiWithdrawRefuse,
    apiTransferSuccess,
    apiTransferFail
} from '@/api/finance/finance'
@Component({
    components: {
        LsDialog,
        LsPagination,
        LsWithdrawalDetails,
        MaterialSelect
        // PopoverInput,
    }
})
export default class GoodsPane extends Vue {
    $refs!: {
        valueRef: any
    }
    @Prop() value: any
    @Prop() pager!: any
    status = true
    selectIds: any[] = []
    withdrawDetail = {}
    // 是否审核通过
    pass_remark = ''
    refuse_remark = ''

    isTransfer = '' // 1-通过 2-不通过
    imgTransfer = '' // 转账凭证
    transfer_remark = ''

    // 查询结果
    onWithdrawSearch(id: any) {
        apiWithdrawSearch({
            id: id
        }).then(() => {
            this.$emit('refresh')
        })
    }

    // 详情
    onWithdrawDetail(id: any) {
        apiWithdrawDetail({
            id: id
        }).then((res: any) => {
            this.withdrawDetail = res
            // this.$emit('refresh')
        })
    }

    // 审核通过
    onWithdrawPass(id: number) {
        apiWithdrawPass({
            id: id as number,
            audit_remark: this.pass_remark
        }).then(() => {
            this.$emit('refresh')
        })
        this.pass_remark = ''
    }

    // 审核拒绝
    onWithdrawRefuse(id: number) {
        if (!this.refuse_remark) {
            this.$message.error('请输入拒绝原因')
            return
        }
        apiWithdrawRefuse({
            id: id as number,
            audit_remark: this.refuse_remark
        }).then(() => {
            this.$emit('refresh')
        })
        this.refuse_remark = ''
    }

    onClearPass() {
        this.pass_remark = ''
    }

    onClearRefuse() {
        this.refuse_remark = ''
    }

    // 转账
    onTransfer(id: number) {
        //
        //
        const res =
            this.isTransfer == '1'
                ? apiTransferSuccess({
                      id: id as number,
                      transfer_remark: this.transfer_remark,
                      transfer_voucher: this.imgTransfer
                  })
                : apiTransferFail({
                      id: id as number,
                      transfer_remark: this.transfer_remark,
                      transfer_voucher: this.imgTransfer
                  })
        res.then(() => {
            this.$emit('refresh')
        })

        this.isTransfer = ''
        this.transfer_remark = ''
        this.imgTransfer = ''
    }
}
</script>

<style scoped lang="scss"></style>
