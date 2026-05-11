<!-- -->
<template>
    <div class="ls-dialog__trigger">
        <div class="ls-dialog__trigger" @click="onTrigger">
            <!-- 触发弹窗 -->
            <slot name="trigger"></slot>
        </div>
        <el-dialog
            coustom-class="ls-dialog__content"
            :title="title"
            :visible="visible"
            :width="width"
            :top="top"
            :append-to-body="true"
            center
            :before-close="close"
            :close-on-click-modal="true"
            :modal="true"
        >
            <!-- 弹窗主要内容-->
            <div class="dialog-content">
                <el-form
                    :rules="valueRules"
                    ref="valueRef"
                    :model="form"
                    label-width="120px"
                    size="small"
                    :disabled="status == 1"
                >
                    <el-form-item label="奖品类型" prop="type">
                        <!-- 单选按钮 -->
                        <el-radio-group class="m-r-16" v-model="form.type">
                            <el-radio :label="0">未中奖</el-radio>
                            <el-radio :label="1">积分</el-radio>
                            <el-radio :label="2">优惠券</el-radio>
                            <el-radio :label="3">余额</el-radio>
                            <el-radio :label="4">商品</el-radio>
                        </el-radio-group>
                    </el-form-item>

                    <el-form-item label="" prop="type_value">
                        <goods-select
                            v-model="goodsSelectData"
                            mode="list"
                            :limit="1"
                            :show-virtual-goods="true"
                            v-if="form.type == 4"
                        >
                        </goods-select>

                        <div class="" v-if="form.type == 1">
                            <el-input
                                class="ls-input"
                                v-model="form.type_value"
                                placeholder="请输入积分"
                                style="width: 300px"
                            >
                                <template slot="append">积分</template>
                            </el-input>
                        </div>
                        <div class="" v-if="form.type == 3">
                            <el-input
                                class="ls-input"
                                v-model="form.type_value"
                                placeholder="请输入金额"
                                style="width: 300px"
                            >
                                <template slot="append">元</template>
                            </el-input>
                            <div class="muted xs">余额发放至用户钱包余额</div>
                        </div>
                        <div class="flex" v-if="form.type == 2">
                            <el-select class="ls-select" v-model="form.type_value" placeholder="全部">
                                <div v-for="(value, key) in coupon" :key="key">
                                    <el-option :label="value.name" :value="value.id"></el-option>
                                </div>
                            </el-select>
                            <div class="m-l-10">
                                <router-link target="_blank" to="/coupon/edit" class="m-r-10">
                                    <el-button type="text" size="small">新建优惠券</el-button>
                                </router-link>
                                <el-button size="small" type="text" @click="couponLists">刷新</el-button>
                            </div>
                        </div>
                    </el-form-item>
                    <el-form-item label="奖品名称" prop="name">
                        <!-- 单选按钮 -->
                        <el-input
                            class="ls-input"
                            v-model="form.name"
                            :maxlength="20"
                            placeholder="请输入奖品名称"
                            style="width: 300px"
                        >
                        </el-input>
                    </el-form-item>

                    <el-form-item label="奖品数量" prop="num" v-if="form.type != 0">
                        <el-input
                            class="ls-input"
                            :min="1"
                            v-model.number="form.num"
                            placeholder="请输入奖品数量"
                            style="width: 300px"
                        >
                        </el-input>
                    </el-form-item>
                    <el-form-item label="奖品图片" prop="image">
                        <div class="">
                            <material-select :limit="1" v-model="form.image" />
                            <div class="muted xs m-r-16">建议尺寸：100*100</div>
                        </div>
                    </el-form-item>
                    <!-- <el-form-item
                        label="抽中概率"
                        :prop="mode == 'add' ? 'probability' : 'probability_desc'"
                        v-if="form.type != 0"
                    >
                        <el-input
                            class="ls-input"
                            v-model="form.probability"
                            placeholder="请输入抽中概率"
                            style="width: 300px"
                            v-if="mode == 'add'"
                        >
                            <template slot="append">%</template>
                        </el-input>
                        <el-input
                            class="ls-input"
                            v-model="form.probability_desc"
                            placeholder="请输入抽中概率"
                            style="width: 300px"
                            v-if="mode == 'edit'"
                        >
                            <template slot="append">%</template>
                        </el-input>
                        <div class="muted xs">概率不能超过100%，可保留两位小数</div>
                    </el-form-item> -->

                    <!-- <el-form-item label="抽中提示语" prop="tips">
                        <el-input
                            class="ls-input"
                            v-model="form.tips"
                            placeholder="请输入抽中提示语"
                            style="width: 300px"
                        >
                        </el-input>
                    </el-form-item> -->
                </el-form>
            </div>

            <!-- 底部弹窗页脚 -->
            <div slot="footer" class="dialog-footer">
                <el-button size="small" @click="close">取消</el-button>
                <el-button size="small" @click="onSubimt" type="primary" :disabled="status == 1">确认</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import GoodsSelect from '@/components/goods-select/index.vue'

import {
    apiLuckyDrawEdit,
    apiLuckyDrawDetail,
    apiLuckyDrawAdd,
    apiLuckyDrawGetPrizeType
} from '@/api/marketing/lucky_draw'
import { apiCouponLists } from '@/api/marketing/coupon'
import MaterialSelect from '@/components/material-select/index.vue'
import { deepClone } from '@/utils/util'
import { apiGoodsDetail } from '@/api/goods'
@Component({
    components: {
        MaterialSelect,
        GoodsSelect
    }
})
export default class LsLuckyDrawChange extends Vue {
    @Prop() index?: number // 编辑礼品行数
    @Prop() val?: object // 礼品数据{}
    @Prop() status?: string // 是否禁用
    @Prop() mode?: string // 模式
    @Prop({
        default: '编辑奖品'
    })
    title!: string //弹窗标题
    @Prop({
        default: '700px'
    })
    width!: string | number //弹窗的宽度
    @Prop({
        default: '20vh'
    })
    top!: string | number //弹窗的距离顶部位置
    /** S Data **/
    visible = false
    $refs!: {
        valueRef: any
    }
    form = {
        name: '', // 奖品名称
        image: '', // 奖品图片
        type: 0, // 奖品类型 // 0-未中奖; 1-积分; 2-优惠券; 3-余额;
        type_value: 0, // 奖品值
        type_desc: '', // 奖品描述
        num: 0, // 奖品数量
        // probability: 0, // 中奖概率
        tips: '', // 中奖提示
        status: '',
        probability_desc: 0
    }

    coupon = []
    goodsSelectData: any = []
    // 表单验证
    valueRules = {
        name: [
            {
                required: true,
                message: '请输入奖品名称',
                trigger: 'blur'
            }
        ],
        image: [
            {
                required: true,
                message: '请选择奖品图片',
                trigger: 'blur'
            }
        ],
        type: [
            {
                required: true,
                message: '请选择奖品类型',
                trigger: 'change'
            }
        ],
        tips: [
            {
                required: true,
                message: '请输入中奖提示',
                trigger: 'blur'
            }
        ],
        num: [
            {
                required: true,
                message: '请输入奖品数量',
                trigger: 'blur'
            },
            {
                validator: (rule: object, value: string, callback: any) => {
                    if (Number(value) <= 0) {
                        callback(new Error('请输入大于0的数'))
                    } else {
                        callback()
                    }
                },
                trigger: 'blur'
            }
        ],
        type_value: [
            {
                required: true,
                message: '请输入',
                trigger: 'blur'
            }
            // {
            // 	validator: (rule: object, value: string, callback: any) => {
            // 		if (Number(value) <= 0 && this.form.type != 2) {
            //
            //
            // 			callback(new Error('请输入大于0的数'))
            // 		} else {
            // 			callback();
            // 		}
            // 	},
            // 	trigger: 'blur'
            // },
        ]
        // probability: [
        //     {
        //         required: true,
        //         message: '请输入中奖概率',
        //         trigger: 'blur'
        //     },
        //     {
        //         validator: (rule: object, value: string, callback: any) => {
        //             if (Number(value) <= 0) {
        //                 callback(new Error('请输入大于0的数'))
        //             } else {
        //                 callback()
        //             }
        //         },
        //         trigger: 'blur'
        //     }
        // ],
        // probability_desc: [
        //     {
        //         required: true,
        //         message: '请输入中奖概率',
        //         trigger: 'blur'
        //     },
        //     {
        //         validator: (rule: object, value: string, callback: any) => {
        //             if (Number(value) <= 0) {
        //                 callback(new Error('请输入大于0的数'))
        //             } else {
        //                 callback()
        //             }
        //         },
        //         trigger: 'blur'
        //     }
        // ]
    }
    /** E Data **/

    // 0-未中奖; 1-积分; 2-优惠券; 3-余额 4-商品;
    @Watch('val', {
        immediate: true
    })
    getValue(val: any) {
        // this.form = val
        this.form = deepClone(val)
    }

    @Watch('form.probability_desc', {
        immediate: true
    })
    getProbability(val: any) {
        if (this.mode == 'edit') {
            // this.form.probability = val * 1
        }
    }

    @Watch('form.type', {
        immediate: true
    })
    getType(val: any) {
        if (val == 0) {
            this.form.type_desc = '未中奖'
        } else if (val == 1) {
            this.form.type_desc = '积分'
        } else if (val == 2) {
            this.form.type_desc = '优惠券'
        } else if (val == 3) {
            this.form.type_desc = '余额'
        } else if (val == 4) {
            this.form.type_desc = '商品'
        }
    }

    goToCouponEdit() {
        window.open('/admin/coupon/edit', '_blank')
    }

    couponLists() {
        apiCouponLists({
            status: 2,
            get_type: 2
        }).then((res: any) => {
            this.coupon = res.lists
        })
    }

    /** S Methods **/

    onSubimt() {
        this.$refs.valueRef.validate((valid: boolean): any => {
            if (!valid) {
                return
            }
            this.form.type_value = this.form.type_value * 1

            if (this.form.type == 4) {
                this.form.type_value = this.goodsSelectData[0].id
            }

            this.$emit('setPrize', this.form, this.index)
            this.visible = false
        })
    }

    async onTrigger() {
        this.couponLists()
        // this.form.type == 4
        if (this.form.type == 4 && this.form.type_value) {
            const res = await apiGoodsDetail(this.form.type_value)

            this.goodsSelectData[0] = res
        }
        // if(this.form.status == '' || this.form.status == null) {
        // 	this.form.type = 0
        // }

        this.visible = true
    }

    // 关闭弹窗
    close() {
        this.visible = false

        // 重制表单内容
        // this.$set(this.form, 'name', '')
        // this.$set(this.form, 'image', '')
        // this.$set(this.form, 'type', 0)
        // this.$set(this.form, 'type_value', 0)
        // this.$set(this.form, 'num', 0)
        // this.$set(this.form, 'probability', 0)
        // this.$set(this.form, 'tips', '')
    }
    /** E Methods **/

    /** S Life Cycle **/
    /** E Life Cycle **/
}
</script>

<style scoped lang="scss">
.dialog-content {
    min-height: 500px;
}
.ls-dialog__trigger {
    height: 100%;
    width: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
</style>
