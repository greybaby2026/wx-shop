<template>
    <div class="user-tag-edit">
        <!-- 导航头部 -->
        <div class="ls-card">
            <el-page-header v-if="status == 0" @back="$router.go(-1)" content="砍价活动详情" />
            <el-page-header v-else @back="$router.go(-1)" :content="mode === 'add' ? '新增砍价活动' : '编辑砍价活动'" />
        </div>

        <!-- 主要内容 -->
        <el-form
            :rules="formRules"
            ref="formRef"
            :model="form"
            label-width="120px"
            size="small"
            :disabled="status == 0"
        >
            <div class="ls-card m-t-16">
                <div class="card-title">基本信息</div>
                <div class="card-content m-t-24">
                    <el-form-item label="活动名称" prop="name">
                        <el-input v-model="form.name" placeholder="请输入活动名称"></el-input>
                    </el-form-item>
                    <el-form-item label="活动时间" required>
                        <date-picker-new
                            :disabled="status == 2"
                            type="datetimerange"
                            :start-time.sync="form.start_time"
                            :end-time.sync="form.end_time"
                        />
                    </el-form-item>
                    <el-form-item label="活动描述" prop="remark">
                        <el-input
                            class="ls-input-textarea"
                            v-model="form.remark"
                            placeholder="请输入活动描述"
                            type="textarea"
                            :rows="3"
                        >
                        </el-input>
                    </el-form-item>
                </div>
            </div>

            <div class="ls-card m-t-16">
                <div class="card-title">活动商品</div>
                <div class="card-content m-t-24">
                    <el-form-item label="砍价商品" prop="" required>
                        <goods-select
                            :disabled="status == 2 || status == 0"
                            v-model="selectGoods"
                            mode="table"
                            :is-spec="true"
                            :limit="25"
                            :extend="{
                                name: '砍价',
                                price: [
                                    {
                                        title: '首刀金额',
                                        key: 'first_knife'
                                    },
                                    {
                                        title: '最低售价',
                                        key: 'floor_price'
                                    }
                                ]
                            }"
                        >
                            <el-button size="mini" type="primary">选择砍价商品</el-button>
                        </goods-select>
                    </el-form-item>
                </div>
            </div>

            <div class="ls-card m-t-16">
                <div class="card-title">活动规则</div>
                <div class="card-content m-t-24">
                    <el-form-item label="购买方式" prop="buy_condition">
                        <el-radio-group class="m-r-16" v-model="form.buy_condition" :disabled="status == 2">
                            <el-radio class="m-r-16" :label="1">砍到任意金额可购买</el-radio>
                            <el-radio :label="2">砍到底价价格才可购买</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="砍价有效期" prop="valid_period">
                        <el-input
                            class="ls-input"
                            placeholder="请输入金额"
                            v-model="form.valid_period"
                            :disabled="status == 2"
                        >
                            <template slot="append">分钟</template>
                        </el-input>
                        <div class="muted xs">用户发起砍价到砍价截止的时长，超出时间没有达到帮砍人数视为砍价失败</div>
                    </el-form-item>
                    <el-form-item label="帮砍人数" prop="help_num">
                        <el-input
                            class="ls-input"
                            placeholder="请输入金额"
                            v-model="form.help_num"
                            :disabled="status == 2"
                        >
                            <template slot="append">人</template>
                        </el-input>
                        <div class="muted xs">设置帮砍人数，至少设置1人</div>
                    </el-form-item>
                    <el-form-item label="每刀金额" prop="knife_amount_type" required>
                        <el-radio-group class="m-r-16" v-model="form.knife_amount_type" :disabled="status == 2">
                            <el-radio class="m-r-16" :label="1">每刀固定金额</el-radio>
                            <el-radio :label="2">每刀任意金额</el-radio>
                        </el-radio-group>
                        <div class="muted xs m-r-16">
                            选择每刀固定金额时，每人砍价金额相同，达到帮砍人数才可砍至底价
                        </div>
                    </el-form-item>
                    <el-form-item label="自己砍价" prop="self" required>
                        <div class="flex">
                            <el-switch
                                v-model="form.self"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                                :disabled="status == 2"
                            />
                            <span class="m-l-16">{{ form.self ? '允许' : '禁止' }}</span>
                        </div>
                    </el-form-item>
                    <el-form-item label="每人发起次数" prop="count">
                        <el-input
                            class="ls-input"
                            placeholder="请输入金额"
                            v-model="form.count"
                            :disabled="status == 2"
                        >
                            <template slot="append">次</template>
                        </el-input>
                        <div class="muted xs">活动期间内，同一个活动商品允许用户发起的砍价次数</div>
                    </el-form-item>
                    <!-- <el-form-item label="起购限制" prop="" required>
						<div>
							<el-radio :label="0" v-model="isBuyLimit" :disabled="status == 2">不限制</el-radio>
						</div>
						<div class="flex">
							<el-radio :label="1" v-model="isBuyLimit" :disabled="status == 2">
								限制
							</el-radio>
							<el-input class="ls-input" v-model.number="form.buy_limit" size="small" :disabled="status == 2">
								<template slot="append">件</template>
							</el-input>
						</div>
						<div class="muted xs">砍价成功后，每件商品单笔订单最少购买的件数</div>
					</el-form-item> -->
                    <el-form-item label="每单限制" prop="" required>
                        <div>
                            <el-radio :label="0" v-model="isOrderLimit" :disabled="status == 2">不限制</el-radio>
                        </div>
                        <div class="flex">
                            <el-radio :label="1" v-model="isOrderLimit" :disabled="status == 2"> 限制 </el-radio>
                            <el-input
                                class="ls-input"
                                v-model.number="form.order_limit"
                                size="small"
                                :disabled="status == 2"
                            >
                                <template slot="append">件</template>
                            </el-input>
                        </div>
                        <div class="muted xs">砍价成功后，每件商品单笔订单最多购买的件数</div>
                    </el-form-item>
                    <!-- <el-form-item label="优惠券" prop="use_coupon" required>
						<el-radio-group class="m-r-16" v-model="form.use_coupon" :disabled="status == 2">
							<el-radio class="m-r-16" :label="0">不允许使用</el-radio>
							<el-radio :label="1">允许使用</el-radio>
						</el-radio-group>
						<div class="muted xs m-r-16">
							本次活动中的商品是否可以使用优惠券。选择允许使用时，满足优惠券使用条件即可使用优惠券
						</div>
					</el-form-item> -->
                </div>
            </div>
        </el-form>

        <!-- 底部保存或取消 -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" :disabled="status == 0" type="primary" @click="onSubmit()">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import { PageMode } from '@/utils/type'
import { timeFormat } from '@/utils/util'
import {
    apiBargainActivityGoodsLists,
    apiBargainActivityChooseGoods,
    apiBargainActivityAdd,
    apiBargainActivityEdit,
    apiBargainActivityDetail
} from '@/api/marketing/bargain.ts'
import LsGoodsSelect from '@/components/marketing/bargain/ls-goods-select.vue'
import DatePickerNew from '@/components/date-picker-new.vue'
import GoodsSelect from '@/components/goods-select/index.vue'
@Component({
    components: {
        LsGoodsSelect,
        DatePickerNew,
        GoodsSelect
    }
})
export default class BargainEdit extends Vue {
    // /** S Data **/

    mode: string = PageMode.ADD // 当前页面【add: 添加用户等级 | edit: 编辑用户等级】
    identity: number | null = null // 当前编辑的ID  valid: mode = 'edit'
    status: number | null = null // 当前编辑的状态  valid: statsu = 1-未开始 2-进行中 0-详情
    form: any = {
        name: '', // 砍价活动名称
        start_time: '', // 砍价活动开始时间
        end_time: '', // 砍价活动结束时间
        remark: '', // 备注
        is_distribution: 0, // 是否参与分销 0-否 1-是
        buy_condition: 1, // 购买条件 1-任意金额可购买 2-砍到底价可购买
        valid_period: 0, // 有效期，单位：分钟，多少时间内砍价完成有效
        help_num: 0, // 帮砍人数
        knife_amount_type: 1, // 每刀金额类型 1-固定金额 2-随机金额
        self: 1, // 自己是否可以参与砍价 0-否 1-是
        count: 0, // 每个用户可发起次砍价次数
        buy_limit: 0, // 起购限制
        order_limit: 0, // 每单限制
        use_coupon: 0, // 是否允许使用优惠券 0-否 1-是
        goods: [] // 参与砍价的商品数据
    }

    tableData = ['', ''] // 选中的时间数组
    goods_ids = [] // 选中的商品id数组

    isBuyLimit = 0 // 起购限制， 0-不限制，1-限制
    isOrderLimit = 0 // 起购限制， 0-不限制，1-限制

    selectGoods: any = [] //商品选择的商品数据

    formRules = {
        name: [
            {
                required: true,
                message: '请输入活动名称',
                trigger: 'blur'
            }
        ],
        is_distribution: [
            {
                required: true,
                message: '请选择是否参与分销',
                trigger: 'change'
            }
        ],
        buy_condition: [
            {
                required: true,
                message: '请选择购买方式',
                trigger: 'change'
            }
        ],
        valid_period: [
            {
                required: true,
                message: '请输入砍价有效期',
                trigger: 'blur'
            }
        ],
        help_num: [
            {
                required: true,
                message: '请输入帮砍人数',
                trigger: 'blur'
            }
        ],
        count: [
            {
                required: true,
                message: '请输入每人发起次数',
                trigger: 'blur'
            }
        ]
    }
    $refs!: {
        formRef: any
    }
    /** E Data **/

    @Watch('isBuyLimit', {
        immediate: true
    })
    isBuyLimitChange(val: any) {
        if (val == 0) {
            this.$set(this.form, 'buy_limit', 0)
        }
    }
    @Watch('isOrderLimit', {
        immediate: true
    })
    isOrderLimitChange(val: any) {
        if (val == 0) {
            this.$set(this.form, 'order_limit', 0)
        }
    }

    @Watch('form.buy_limit', {
        immediate: true
    })
    buyLimitChange(val: any) {
        if (val == 0) {
            this.isBuyLimit = 0
        } else {
            this.isBuyLimit = 1
        }
    }
    @Watch('form.order_limit', {
        immediate: true
    })
    orderLimitChange(val: any) {
        if (val == 0) {
            this.isOrderLimit = 0
        } else {
            this.isOrderLimit = 1
        }
    }

    /** S Methods **/

    @Watch('selectGoods', { deep: true })
    selectGoodsChange(val: any[]) {
        this.form.goods = val.map((item: any) => {
            return {
                goods_id: item.id,
                items: item.item.map((sitem: any) => ({
                    item_id: sitem.id,
                    first_knife: sitem.first_knife,
                    floor_price: sitem.floor_price
                })),
                virtual_click_num: item.virtual_click_num,
                virtual_sales_num: item.virtual_sales_num
            }
        })
    }

    checkGoods() {
        const goods = this.form.goods
        if (!goods.length) {
            this.$message.error('请选择砍价商品')
            return false
        }
        for (let i = 0; i < goods.length; i++) {
            for (let j = 0; j < goods[i].items.length; j++) {
                if (!goods[i].items[j].first_knife) {
                    this.$message.error(`请输入砍价商品首刀金额`)
                    return false
                }
                if (!goods[i].items[j].floor_price) {
                    this.$message.error(`请输入砍价商品底价金额`)
                    return false
                }
            }
        }
        return true
    }

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
                    return this.handleAdd()
                case PageMode.EDIT:
                    return this.handleEdit()
            }
        })
    }

    // 新增
    handleAdd() {
        this.checkGoods()
        apiBargainActivityAdd(this.form)
            .then(() => {
                setTimeout(() => this.$router.go(-1), 500)
            })
            .catch(() => {})
    }

    // 编辑
    handleEdit() {
        this.checkGoods()
        apiBargainActivityEdit({
            ...this.form,
            id: this.identity
        }).then(() => {
            setTimeout(() => this.$router.go(-1), 500)
        })
    }
    // 表单初始化数据 [编辑模式] mode => edit
    initBargainActivityDetail() {
        apiBargainActivityDetail({
            id: this.identity
        })
            .then((res: any) => {
                this.form = res
                this.selectGoods = res.goods
            })
            .catch((err: any) => {})
    }
    /** E Methods **/

    /** S Life Cycle **/
    created() {
        const query: any = this.$route.query

        if (query.mode) {
            this.mode = query.mode
        }

        // 编辑模式：初始化数据
        if (this.mode === PageMode.EDIT) {
            this.identity = query.id * 1
            this.status = query.status * 1
            this.initBargainActivityDetail()

            // let start_time = timeFormat(Number(this.form.start_time))
            // let end_time = timeFormat(Number(this.form.end_time))

            // this.$set(this.form, 'start_time', start_time)
            // this.$set(this.form, 'end_time', end_time)

            // this.tableData[0] = this.form.start_time
            // this.tableData[1] = this.form.end_time
            // this.$forceUpdate()
        }
    }

    /** E Life Cycle **/
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

.user-tag-edit {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;

    &__header {
        flex: none;
    }
}

::v-deep .tab-item td,
.tab-item th {
    border: none !important;
}
::v-deep .tab-item::before {
    background: none !important;
}
::v-deep .tab-item td {
    margin-top: 5px;
    padding: 5px 0;
    height: 40px;
    box-sizing: border-box;
}
::v-deep .tab-item .el-input--small .el-input__inner {
    height: 25px !important;
}
::v-deep .item .el-table tbody tr {
    pointer-events: none;
}

.ls-team-edit {
    padding-bottom: 80px;
    .ls-input {
        width: 380px;
    }
    .desc {
        display: block;
        color: #999;
        font-size: 12px;
    }
}
</style>
