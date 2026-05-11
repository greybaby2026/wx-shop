<!-- 新增/编辑用户等级 -->
<template>
    <div class="user-grade-edit">
        <!-- 导航头部 -->
        <div class="ls-card">
            <el-page-header
                v-if="!disabled"
                @back="$router.go(-1)"
                :content="mode == 'add' ? '新增用户等级' : '编辑用户等级'"
            />
            <el-page-header v-else @back="$router.go(-1)" content="用户等级详情" />
        </div>

        <!-- 主要内容 -->
        <el-form :rules="formRules" ref="formRef" :model="form" label-width="120px" size="small">
            <div class="ls-card m-t-16">
                <div class="card-title">等级信息</div>
                <div class="card-content m-t-24">
                    <el-form-item label="等级名称" prop="name">
                        <el-input :disabled="disabled" v-model="form.name" placeholder="请输入等级名称"></el-input>
                    </el-form-item>
                    <el-form-item label="等级级别" prop="rank">
                        <el-input
                            class="ls-input"
                            v-model.number="form.rank"
                            placeholder="请输入等级级别"
                            :disabled="(mode === 'edit' && isLevel === 1) || disabled"
                        ></el-input>
                        <span class="m-l-10">级</span>
                        <div class="muted xs m-r-16">权重数字越大表示等级越高，等级权重不能相同。填写大于1的整数</div>
                    </el-form-item>
                    <el-form-item label="等级图标" required>
                        <material-select :limit="1" v-model="form.image" :disabled="disabled" />
                        <div class="flex">
                            <div class="muted xs m-r-16">建议尺寸：100*100像素，jpg，jpeg，png图片类型</div>
                        </div>
                    </el-form-item>
                    <el-form-item label="等级背景图" required>
                        <material-select :limit="1" v-model="form.background_image" :disabled="disabled" />
                        <div class="flex">
                            <div class="muted xs m-r-16">建议尺寸：800*500像素，jpg，jpeg，png图片类型</div>
                        </div>
                    </el-form-item>
                    <el-form-item label="等级描述" prop="remark">
                        <el-input
                            :disabled="disabled"
                            class="ls-input-textarea"
                            v-model="form.remark"
                            placeholder="请输入等级描述"
                            type="textarea"
                            :rows="3"
                        ></el-input>
                    </el-form-item>
                </div>
            </div>
            <div class="ls-card m-t-16">
                <div class="card-title">等级权益</div>
                <div class="card-content m-t-24">
                    <el-form-item label="等级折扣" prop="level_discount">
                        <div>
                            <el-radio :label="0" v-model="form.level_discount" :disabled="disabled"
                                >无等级折扣</el-radio
                            >
                        </div>
                        <div>
                            <el-radio :label="1" v-model="form.level_discount" :disabled="disabled">
                                <span class="m-r-5">参与等级折扣</span>
                                <el-input
                                    class="ls-input"
                                    v-model="form.discount"
                                    size="small"
                                    :disabled="disabled"
                                ></el-input>
                                <span class="m-l-5">折</span>
                            </el-radio>
                        </div>
                        <div class="muted xs m-r-16">
                            购买符合条件的商品时，可以使用等级折扣进行优惠。填写0到10之间的数字，可以保留小数点2位数字
                        </div>
                    </el-form-item>
                </div>
            </div>
            <div class="ls-card m-t-16" v-if="!(mode === 'edit' && form.rank === 1)">
                <div class="card-title">等级条件</div>
                <div class="card-content m-t-24">
                    <el-form-item label="等级条件" prop="condition.condition_type">
                        <el-radio-group class="m-r-16" v-model="form.condition.condition_type" :disabled="disabled">
                            <el-radio class="m-r-16" :label="0">满足以下任意条件</el-radio>
                            <el-radio class="m-r-16" :label="1">满足以下全部条件</el-radio>
                        </el-radio-group>
                        <!-- 多选框 -->
                        <div class="flex-col">
                            <div class="flex m-t-18">
                                <el-checkbox
                                    v-model="form.condition.is_single_money"
                                    :true-label="1"
                                    :false-label="0"
                                    :disabled="disabled"
                                    @change="conditionChange($event, 'single_money')"
                                >
                                    <div class="flex">
                                        <span class="m-r-5">单笔消费金额</span>
                                    </div>
                                </el-checkbox>
                                <el-input
                                    class="ls-input"
                                    v-model="form.condition.single_money"
                                    :disabled="disabled"
                                    type="number"
                                >
                                    <template slot="append">元</template>
                                </el-input>
                            </div>
                            <div class="flex m-t-18">
                                <el-checkbox
                                    v-model="form.condition.is_total_money"
                                    :disabled="disabled"
                                    :true-label="1"
                                    :false-label="0"
                                    @change="conditionChange($event, 'total_money')"
                                >
                                    <div class="flex">
                                        <span class="m-r-5">累计消费金额</span>
                                    </div>
                                </el-checkbox>
                                <el-input
                                    class="ls-input"
                                    v-model="form.condition.total_money"
                                    :disabled="disabled"
                                    type="number"
                                >
                                    <template slot="append">元</template>
                                </el-input>
                            </div>
                            <div class="flex m-t-18">
                                <el-checkbox
                                    v-model="form.condition.is_total_num"
                                    :disabled="disabled"
                                    :true-label="1"
                                    :false-label="0"
                                    @change="conditionChange($event, 'total_num')"
                                >
                                    <div class="flex">
                                        <span class="m-r-5">累计消费次数</span>
                                    </div>
                                </el-checkbox>

                                <el-input
                                    class="ls-input"
                                    v-model="form.condition.total_num"
                                    :disabled="disabled"
                                    type="number"
                                >
                                    <template slot="append">次</template>
                                </el-input>
                            </div>
                        </div>
                    </el-form-item>
                </div>
            </div>
        </el-form>

        <!-- 底部保存或取消 -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="onSubmit()" :disabled="disabled">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import MaterialSelect from '@/components/material-select/index.vue'
import { PageMode } from '@/utils/type'
import { apiUserLevelAdd, apiUserLevelDetail, apiUserLevelEdit } from '@/api/user/user'
import { UserLevelDetail_Req, UserLevelDetail_Res, UserLevelAdd_Req, UserLevelEdit_Req } from '@/api/user/user.d'
@Component({
    components: {
        MaterialSelect
    }
})
export default class GradeEdit extends Vue {
    /** S Data **/
    mode: string = PageMode.ADD // 当前页面【add: 添加用户等级 | edit: 编辑用户等级】
    identity: number | null = null // 当前编辑用户的身份ID  valid: mode = 'edit'
    isLevel: number | null = null // 用户等级

    disabled: any = false //是否禁用 查看详情时禁用

    form = {
        name: '', // 等级名称
        rank: 0, // 级别
        image: '', // 图标
        background_image: '', // 背景图
        remark: '', // 备注
        level_discount: 0, // 设置等级折扣：0-无等级折扣；1-参与等级折扣
        discount: 10, // 等级折扣

        condition: {
            condition_type: 0, // 设置等级条件：0-满足以下任意条件；1-满足以下全部条件
            is_single_money: 0, // 单笔消费金额是否为空
            is_total_money: 0, // 累计消费金额是否为空
            is_total_num: 0, // 累计消费次数是否为空
            single_money: '', // 单笔消费金额（空表示没选中）
            total_money: '', // 累计消费金额（空表示没选中）
            total_num: '' // 累计消费次数 （空表示没选中)
        }
    }

    formRules = {
        name: [
            {
                required: true,
                message: '请输入等级名称',
                trigger: 'blur'
            }
        ],
        rank: [
            {
                required: true,
                message: '请输入等级级别',
                trigger: 'blur'
            },
            {
                type: 'number',
                // min: 2,
                required: true,
                message: '请输入大于1的整数',
                trigger: 'blur'
            }
        ],
        level_discount: [
            {
                required: true,
                message: '请选择等级折扣',
                // validator: (rule: object, level_discount: number, callback: any) => {
                // 	let discount = this.$refs.formRef.discount;
                // 	if (level_discount === 0) {
                // 		callback()
                // 	} else {
                // 		if (!discount) {
                // 			callback(new Error('请输入折扣等级'))
                // 		}
                // 		if (discount < 0 || discount < 10) {
                // 			callback(new Error('请输入0到10之间的数字，可以保留小数点2位数字'))
                // 		}
                // 	}
                // },
                trigger: 'change'
            }
        ],
        'condition.condition_type': [
            {
                required: true,
                message: '请选择等级条件',
                trigger: 'change'
            }
        ]
    }
    $refs!: {
        formRef: any
    }
    /** E Data **/

    /** S Methods **/
    // 表单提交
    onSubmit() {
        // 验证表单格式是否正确
        this.$refs.formRef.validate((valid: boolean): any => {
            if (!valid) {
                return
            }

            // 请求发送
            switch (this.mode) {
                case PageMode.ADD:
                    return this.handleUserLevelAdd()
                case PageMode.EDIT:
                    return this.handleUserLevelEdit()
            }
        })
    }

    // 新增用户等级
    handleUserLevelAdd() {
        // @ts-ignore
        const form = this.form as UserLevelAdd_Req
        apiUserLevelAdd(form)
            .then(() => {
                // this.$message.success('添加成功!')
                setTimeout(() => this.$router.go(-1), 500)
            })
            .catch(() => {
                //this.$message.error('保存失败!')
            })
    }

    // 编辑用户等级
    handleUserLevelEdit() {
        const params = this.form
        const id: number = this.identity as number
        // @ts-ignore
        apiUserLevelEdit({ ...params, id } as UserLevelEdit_Req)
            .then(() => {
                // this.$message.success('修改成功!')
                setTimeout(() => this.$router.go(-1), 500)
                //this.initFormDataForUserLevelEdit()
            })
            .catch(() => {
                // this.$message.error('保存失败!')
            })
    }
    // 表单初始化数据 [编辑模式] mode => edit
    initFormDataForUserLevelEdit() {
        apiUserLevelDetail({
            id: this.identity as number
        })
            .then((res: UserLevelDetail_Res) => {
                Object.keys(res).map(key => {
                    this.$set(this.form, key, res[key])
                })
            })
            .catch(() => {
                // this.$message.error('数据初始化失败，请刷新重载！')
            })
    }
    // 复选框变化
    conditionChange(val: any, type: string) {
        if (!val) {
            // @ts-ignore
            this.form.condition[type] = ''
        }
    }
    /** E Methods **/

    /** S Life Cycle **/
    created() {
        const query: any = this.$route.query

        if (query.mode) {
            this.mode = query.mode
        }
        this.disabled = this.$route.query.disabled != null ? true : false

        // 编辑模式：初始化数据
        if (this.mode === PageMode.EDIT) {
            this.identity = query.id * 1
            this.isLevel = query.level * 1
            this.initFormDataForUserLevelEdit()
        }
    }
    /** E Life Cycle **/
    // 监听等级条件是否有输入，输入即勾选
}
</script>

<style lang="scss" scoped>
.ls-card {
    .ls-input {
        width: 133px;
    }

    .ls-input-textarea {
        width: 300px;
    }

    .card-title {
        font-size: 14px;
        font-weight: 500;
    }
}

.user-grade-edit {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;

    &__header {
        flex: none;
    }
}
</style>
