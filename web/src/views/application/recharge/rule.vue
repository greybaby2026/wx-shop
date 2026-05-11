<template>
    <div class="ls-add-admin">
        <div class="ls-card ls-coupon-edit__form m-t-10">
            <div class="nr weight-500 m-b-20">充值设置</div>
            <el-form ref="list" :model="list" label-width="120px" size="small">
                <el-form-item label="应用状态">
                    <el-radio v-model="list.set.open" :label="0">关闭</el-radio>
                    <el-radio v-model="list.set.open" :label="1">开启</el-radio>
                    <span class="desc">关闭或开启充值功能，关闭后商城将不显示充值入口</span>
                </el-form-item>

                <el-form-item label="最低充值金额" prop="send_total_type">
                    <el-input placeholder="" v-model="list.set.min_amount"></el-input>
                    <span class="desc">最低充值金额要求，不填或填0表示不限制最低充值金额</span>
                </el-form-item>
            </el-form>
        </div>

        <div class="ls-card ls-coupon-edit__form m-t-10">
            <div class="nr weight-500 m-b-20">充值规则</div>
            <el-form ref="list" :model="list" label-width="120px" style="width: 1000px" size="small">
                <el-form-item v-for="(item, index) in list.rule" :key="index" :label="'规则' + (index + 1)">
                    <div class="item">
                        <el-form :model="item" label-width="120px">
                            <el-form-item required label="单笔充值满">
                                <el-input class="m-r-10" v-model="list.rule[index].money"></el-input>
                                元
                            </el-form-item>
                            <el-form-item  class="m-t-10" label="充值奖励">
                                <el-input class="m-r-10" v-model="list.rule[index].award[0].give_money"></el-input>
                                元
                            </el-form-item>
                        </el-form>

                        <div class="del m-t-5" @click="delRule(index)">
                            <i class="el-icon-delete"></i>
                        </div>
                    </div>
                </el-form-item>

                <el-form-item>
                    <div class="add-btn flex row-center" @click="addRule">+添加 {{ list.rule.length }}/10</div>
                </el-form-item>
            </el-form>
        </div>

        <div class="ls-card ls-coupon-edit__form m-t-10">
            <div class="nr weight-500 m-b-20">充值分销设置</div>
            <el-form ref="distributionForm" :model="list" label-width="120px" size="small">
                <el-form-item label="充值分销">
                    <el-radio v-model="list.distribution_set.open" :label="0">关闭</el-radio>
                    <el-radio v-model="list.distribution_set.open" :label="1">开启</el-radio>
                    <span class="desc">开启后，用户充值成功时将给上级发放佣金到可提现余额</span>
                </el-form-item>

                <el-form-item label="一级佣金比例">
                    <el-input placeholder="" v-model="list.distribution_set.first_ratio" style="width: 200px"></el-input>
                    <span class="m-l-10">%</span>
                    <span class="desc">充值用户的一级上级获得的佣金比例，如20表示充值金额的20%</span>
                </el-form-item>

                <el-form-item label="二级佣金比例">
                    <el-input placeholder="" v-model="list.distribution_set.second_ratio" style="width: 200px"></el-input>
                    <span class="m-l-10">%</span>
                    <span class="desc">充值用户的二级上级获得的佣金比例，如5表示充值金额的5%</span>
                </el-form-item>

                <el-form-item label="单笔佣金上限">
                    <el-input placeholder="" v-model="list.distribution_set.max_commission" style="width: 200px"></el-input>
                    <span class="m-l-10">元</span>
                    <span class="desc">单笔佣金最高金额，填0表示不限制</span>
                </el-form-item>

                <el-form-item label="">
                    <div class="desc" style="color: #E6A23C; font-size: 13px; line-height: 1.8;">
                        <div>⚠️ 重要说明：</div>
                        <div>1. 充值佣金在充值成功后即时结算到上级的可提现余额</div>
                        <div>2. 使用余额支付的购物订单不产生购物分销佣金</div>
                        <div>3. 充值不支持退款，佣金即时结算无风险</div>
                    </div>
                </el-form-item>
            </el-form>
        </div>

        <!-- 底部保存或取消 -->
        <div class="bg-white ls-fixed-footer flex row-center">
            <div class="row-center flex">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="onSubmit('form')">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import LsEditor from '@/components/editor.vue'
import lsDialog from '@/components/ls-dialog.vue'
import MaterialSelect from '@/components/material-select/index.vue'
import { apiRechargeGetRule, apiRechargeSetRule } from '@/api/application/recharge'

@Component({
    components: {
        MaterialSelect,
        lsDialog,
        LsEditor
    }
})
export default class RechargeRuleEdit extends Vue {
    /** S Data **/

    list: any = {
        set: {
            open: 1,
            min_amount: 100
        },
        rule: [
            {
                money: 50,
                award: [
                    {
                        give_money: 5
                    }
                ]
            }
        ],
        distribution_set: {
            open: 0,
            first_ratio: 20,
            second_ratio: 5,
            max_commission: 0
        }
    }

    /** E Data **/

    /** S Methods **/

    addRule() {
        if (this.list.rule.length >= 10) {
            return this.$message.error('不能继续添加了!')
        }
        this.list.rule.push({
            money: 0,
            award: [
                {
                    give_money: 0
                }
            ]
        })
    }

    // 删除规则规格项
    delRule(index: number) {
        if (this.list.rule.length <= 1) {
            return this.$message.error('已经是最后一个了!')
        }
        this.list.rule.splice(index, 1)
    }

    onSubmit() {
        const list = {
            rule: this.list.rule,
            ...this.list.set,
            distribution_open: this.list.distribution_set.open,
            first_ratio: this.list.distribution_set.first_ratio,
            second_ratio: this.list.distribution_set.second_ratio,
            max_commission: this.list.distribution_set.max_commission
        }
        try {
            list.rule.forEach((item: any) => delete item.id)
        } catch (error) {}
        apiRechargeSetRule({ ...list })
            .then(() => {
                this.detail()
                this.$message.success('修改成功!')
            })
            .catch(() => {
                // this.$message.error('数据获取失败!')
            })
    }

    // 详情
    detail() {
        apiRechargeGetRule({})
            .then(res => {
                this.list = res
            })
            .catch(() => {
                this.$message.error('数据获取失败!')
            })
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.detail()
    }
    /** E Life Cycle **/
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
        width: 830px;
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
