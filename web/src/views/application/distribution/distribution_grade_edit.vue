<template>
    <div class="ls-add-admin">
        <div class="ls-card">
            <el-page-header @back="$router.go(-1)" :content="mode == 'edit' ? '编辑分销等级' : '新增分销等级'" />
        </div>

        <div class="ls-card ls-coupon-edit__form m-t-10">
            <div class="nr weight-500 m-b-20">等级信息</div>
            <el-form ref="form" :model="form" label-width="120px" size="small">
                <!-- 等级名称 -->
                <el-form-item label="等级名称" required>
                    <el-input :disabled="disabled" class="ls-input" v-model="form.name" placeholder="请输入等级名称">
                    </el-input>
                </el-form-item>

                <!-- 等级级别 -->
                <el-form-item label="等级级别" required>
                    <el-input :disabled="disabled" class="ls-input" v-model="form.weights" placeholder="请输入等级级别">
                    </el-input>
                    <span class="desc">级别数字越大表示等级越高，等级级别不能相同。填写大于1的整数。</span>
                </el-form-item>

                <el-form-item label="等级描述">
                    <el-input
                        type="textarea"
                        :disabled="disabled"
                        placeholder="请输入内容"
                        style="width: 400px"
                        rows="8"
                        v-model="form.remark"
                        maxlength="30"
                        show-word-limit
                    />
                </el-form-item>
            </el-form>
        </div>

        <div class="ls-card ls-coupon-edit__form m-t-10">
            <div class="nr weight-500 m-b-20">等级佣金</div>
            <el-form ref="form" :model="form" label-width="120px" size="small">
                <el-form-item label="自购佣金比例" required>
                    <el-input
                        :disabled="disabled"
                        class="ls-input"
                        v-model="form.self_ratio"
                        placeholder="请输入自购佣金比例"
                    >
                    </el-input>
                </el-form-item>

                <!-- 一级佣金比例 -->
                <el-form-item label="一级佣金比例" required>
                    <el-input
                        :disabled="disabled"
                        class="ls-input"
                        v-model="form.first_ratio"
                        placeholder="请输入一级佣金比例"
                    >
                    </el-input>
                </el-form-item>

                <!-- 二级佣金比例 -->
                <el-form-item label="二级佣金比例" required>
                    <el-input
                        :disabled="disabled"
                        class="ls-input"
                        v-model="form.second_ratio"
                        placeholder="请输入二级佣金比例"
                    >
                    </el-input>
                    <span class="desc">佣金支持小数点后2位，等级佣金总和不能超过100%</span>
                </el-form-item>
            </el-form>
        </div>
        <div class="ls-card ls-coupon-edit__form m-t-10" v-if="flag == 0">
            <div class="nr weight-500 m-b-20">等级信息</div>
            <el-form ref="form" :model="form" label-width="120px" size="small">
                <!-- 等级条件 -->
                <el-form-item label="等级条件">
                    <el-radio :disabled="disabled" v-model="form.update_relation" :label="1">满足以下任何条件</el-radio>
                    <el-radio :disabled="disabled" v-model="form.update_relation" :label="2">满足以下全部条件</el-radio>
                </el-form-item>

                <!-- 消费金额 -->
                <el-form-item label="">
                    <div class="m-b-10">
                        单笔消费金额<el-input
                            :disabled="disabled"
                            class="ls-input m-l-10"
                            v-model="form.update_condition[0].singleConsumptionAmount"
                            placeholder=""
                        >
                        </el-input
                        >元
                    </div>

                    <div class="m-b-10">
                        累计消费金额<el-input
                            :disabled="disabled"
                            class="ls-input m-l-10"
                            v-model="form.update_condition[1].cumulativeConsumptionAmount"
                            placeholder=""
                        >
                        </el-input
                        >元
                    </div>

                    <div class="m-b-10">
                        累计消费次数<el-input
                            :disabled="disabled"
                            class="ls-input m-l-10"
                            v-model="form.update_condition[2].cumulativeConsumptionTimes"
                            placeholder=""
                        >
                        </el-input
                        >次
                    </div>

                    <div class="m-b-10">
                        已结算佣金收入<el-input
                            :disabled="disabled"
                            class="ls-input m-l-10"
                            v-model="form.update_condition[3].returnedCommission"
                            placeholder=""
                        >
                        </el-input
                        >元
                    </div>
                </el-form-item>
            </el-form>
        </div>

        <!-- 底部保存或取消 -->
        <div class="bg-white ls-fixed-footer flex row-center">
            <div class="row-center flex">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" :disabled="disabled" @click="onSubmit('form')">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import {
    apiDistributionGradeAdd,
    apiDistributionGradeDetail,
    apiDistributionGradeEdit
} from '@/api/distribution/distribution'

@Component
export default class DistributionGradeEdit extends Vue {
    /** S Data **/
    identity: number | null = null // 当前编辑用户的身份ID  valid: mode = 'edit'
    roleList: Array<object> = [] // 角色的数据

    mode: any
    id: any
    flag: any = 1
    disabled: any = false

    // 添加管理员表单数据
    form: any = {
        name: '', //是	string	等级名称
        weights: '', //是	integer	等级级别 须大于1
        self_ratio: '', //是	decimal	自购佣金比例 1-100之间，保留2位小数
        first_ratio: '', //是	decimal	一级佣金比例 1-100之间，保留2位小数
        second_ratio: '', //是	decimal	二级佣金比例 1-100之间，保留2位小数
        update_relation: 1, //是	integer	升级关系 1-满足任意条件 2-满足全部条件
        update_condition: [
            {
                singleConsumptionAmount: '' //否	decimal	单笔消费金额
            },
            {
                cumulativeConsumptionAmount: '' //否	decimal	累计消费金额
            },
            {
                cumulativeConsumptionTimes: '' //否	integer	累计消费次数
            },
            {
                returnedCommission: '' //否	decimal	已结算佣金收入
            }
        ], //是	array	升级条件,至少传一个条件
        remark: '' //否	string	等级备注
    }
    /** E Data **/

    /** S Methods **/
    // 点击表单提交
    onSubmit(formName: string) {
        // 请求发送
        switch (this.mode) {
            case 'add':
                return this.GradeAdd()
            case 'edit':
                return this.GradeEdit()
        }
    }

    // 编辑
    GradeEdit() {
        this.form.update_condition = this.form.update_condition
        apiDistributionGradeEdit({ ...this.form }).then(() => {
            this.$router.go(-1)
        })
    }

    // 添加
    GradeAdd() {
        apiDistributionGradeAdd({ ...this.form }).then(() => {
            this.$router.go(-1)
        })
    }

    // 详情
    detail() {
        apiDistributionGradeDetail({ id: this.id })
            .then(res => {
                if (res.update_condition) {
                    const data = res.update_condition
                    const arr = [
                        {
                            singleConsumptionAmount: data.singleConsumptionAmount
                        },
                        {
                            cumulativeConsumptionAmount: data.cumulativeConsumptionAmount
                        },
                        {
                            cumulativeConsumptionTimes: data.cumulativeConsumptionTimes
                        },
                        { returnedCommission: data.returnedCommission }
                    ]
                    res.update_condition = arr
                }
                this.form = res
            })
            .catch(() => {
                this.$message.error('数据获取失败!')
            })
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        const query: any = this.$route.query

        if (query.mode) {
            this.mode = query.mode
        }
        if (query.flag) {
            this.flag = query.flag
        }
        if (query.disabled) {
            this.disabled = true
        }

        // 编辑模式：初始化数据
        if (this.mode === 'edit') {
            this.id = query.id
            this.detail()
        }
        // 获取角色列表
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.desc {
    color: #999;
    display: block;
}
.ls-add-admin {
    padding-bottom: 80px;

    .ls-input {
        width: 280px;
        margin-right: 20px;
    }
}
</style>
