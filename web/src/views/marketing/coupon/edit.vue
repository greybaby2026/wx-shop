<template>
    <div class="ls-coupon-edit">
        <div class="ls-card ls-coupon-edit__header">
            <el-page-header v-if="status == 0" @back="$router.go(-1)" content="优惠券详情" />
            <el-page-header v-else @back="$router.go(-1)" :content="!id ? '新增优惠券活动' : '编辑优惠券活动'" />
        </div>

        <el-form ref="couponList" :rules="rules" :model="couponList" label-width="120px" size="small">
            <div class="ls-card ls-coupon-edit__form m-t-10">
                <div class="lg weight-500 m-b-20">基本信息</div>

                <!-- 优惠券的名称 -->
                <el-form-item label="优惠券名称" prop="name" required>
                    <el-input
                        :disabled="status != 2 ? disabled : false"
                        class="ls-input"
                        v-model="couponList.name"
                        placeholder="请输入优惠券名称"
                    >
                    </el-input>
                </el-form-item>

                <!-- 发放的优惠券总量 -->
                <el-form-item label="发放总量" prop="send_total_type">
                    <div>
                        <el-radio
                            border
                            :disabled="status != 2 ? disabled : false"
                            v-model="couponList.send_total_type"
                            :label="1"
                        >
                            不限制数量</el-radio
                        >
                    </div>

                    <div class="flex m-t-24">
                        <el-radio
                            border
                            :disabled="status != 2 ? disabled : false"
                            v-model="couponList.send_total_type"
                            :label="2"
                        >
                            发放
                        </el-radio>
                        <el-input
                            class="ls-input"
                            :disabled="status != 2 ? disabled : false"
                            size="small"
                            v-model="couponList.send_total"
                        >
                            <template slot="append">张</template>
                        </el-input>
                    </div>
                    <span class="desc">编辑进行中的优惠券，发放总量只能增加不能减少，请谨慎设置。最多1000000张。</span>
                </el-form-item>

                <!-- 用券时间选择 -->
                <el-form-item label="用券时间" prop="send_total_type">
                    <div class="m-b-24">
                        <el-radio :disabled="disabled" v-model="couponList.use_time_type" :label="1">固定日期</el-radio>

                        <el-date-picker
                            :disabled="disabled"
                            v-model="couponList.use_time_start"
                            type="datetime"
                            placeholder="开始日期"
                        >
                        </el-date-picker>
                        至
                        <el-date-picker
                            :disabled="disabled"
                            v-model="couponList.use_time_end"
                            type="datetime"
                            placeholder="结束日期"
                        >
                        </el-date-picker>
                        可用
                    </div>

                    <div class="flex">
                        <el-radio border :disabled="disabled" v-model="couponList.use_time_type" :label="2">
                            领券当日起
                        </el-radio>
                        <el-input class="ls-input" :disabled="disabled" size="small" v-model="couponList.use_time">
                            <template slot="append">天内可用</template>
                        </el-input>
                    </div>

                    <span class="desc">至少需要填写1天</span>

                    <div class="flex">
                        <el-radio border :disabled="disabled" v-model="couponList.use_time_type" :label="3">
                            领券次日起
                        </el-radio>
                        <el-input class="ls-input" :disabled="disabled" size="small" v-model="couponList.use_time1">
                            <template slot="append">天内可用</template>
                        </el-input>
                    </div>
                    <span class="desc">至少需要填写1天</span>
                </el-form-item>
            </div>

            <!-- 优惠设置 -->
            <div class="ls-card ls-coupon-edit__form m-t-10">
                <div class="lg weight-500 m-b-20">优惠设置</div>
                <!-- 优惠的面额 -->
                <!-- <el-form-item label="优惠券面额" prop="money">
                    <el-input :disabled="disabled" class="ls-input" v-model="couponList.money"></el-input>
                    <span class="desc">面额需大于0元，支持两位小数</span>
                </el-form-item> -->
                <!-- 优惠券类型 -->
                <el-form-item label="优惠券类型" prop="condition_type">
                    <div>
                        <el-radio :disabled="disabled" border v-model="couponList.condition_type" :label="1"
                            >无门槛
                        </el-radio>

                        <el-radio :disabled="disabled" border v-model="couponList.condition_type" :label="2"
                            >满减券
                        </el-radio>
                        <el-radio :disabled="disabled" border v-model="couponList.condition_type" :label="3"
                            >折扣券
                        </el-radio>
                    </div>
                    <div class="m-t-10" v-if="couponList.condition_type == 1">
                        <span class="m-r-10"> 减 </span>
                        <el-input :disabled="disabled" class="min-input" v-model="couponList.money" size="small">
                            <template slot="append">元</template>
                        </el-input>
                    </div>
                    <div class="m-t-10" v-if="couponList.condition_type == 2">
                        <span class="m-r-10"> 满 </span>
                        <el-input
                            :disabled="disabled"
                            class="min-input"
                            v-model="couponList.condition_money"
                            size="small"
                        >
                            <template slot="append">元</template>
                        </el-input>
                        <span class="m-r-10 m-l-10"> 减 </span>
                        <el-input :disabled="disabled" class="min-input" v-model="couponList.money" size="small">
                            <template slot="append">元</template>
                        </el-input>
                    </div>
                    <div class="m-t-10" v-if="couponList.condition_type == 3">
                        <span class="m-r-10"> 满 </span>
                        <el-input
                            :disabled="disabled"
                            class="min-input"
                            v-model="couponList.condition_money"
                            size="small"
                        >
                            <template slot="append">元</template>
                        </el-input>
                        <span class="m-r-10 m-l-10"> 打 </span>
                        <el-input
                            :disabled="disabled"
                            class="min-input"
                            v-model="couponList.discount_ratio"
                            size="small"
                        >
                            <template slot="append">折</template>
                        </el-input>
                    </div>
                </el-form-item>
                <!-- 最高优惠 -->
                <el-form-item label="最高优惠" v-if="couponList.condition_type == 3">
                    <el-input
                        :disabled="disabled"
                        class="ls-input"
                        v-model="couponList.discount_max_money"
                        size="small"
                    >
                        <template slot="append">元</template>
                    </el-input>
                </el-form-item>

                <!-- 使用门槛 -->
                <!-- <el-form-item label="使用门槛" prop="condition_type">
                    <div>
                        <el-radio :disabled="disabled" border v-model="couponList.condition_type" :label="1"
                            >无门槛使用
                        </el-radio>
                    </div>
                    <div class="flex m-t-24">
                        <el-radio border :disabled="disabled" v-model="couponList.condition_type" :label="2">
                            订单满
                        </el-radio>
                        <el-input
                            class="ls-input"
                            :disabled="disabled"
                            size="small"
                            v-model="couponList.condition_money"
                        >
                            <template slot="append">元可使用</template>
                        </el-input>
                    </div>
                    <span class="desc">需要填写大于优惠券面额的数额</span>
                </el-form-item> -->

                <!-- 用券时间选择 -->
                <el-form-item label="使用范围" prop="use_goods_type">
                    <el-radio border :disabled="disabled" v-model="couponList.use_goods_type" :label="1"
                        >全场通用</el-radio
                    >
                    <el-radio border :disabled="disabled" v-model="couponList.use_goods_type" :label="2"
                        >部分商品可用
                    </el-radio>
                    <el-radio border :disabled="disabled" v-model="couponList.use_goods_type" :label="3"
                        >部分商品不可用
                    </el-radio>
                    <el-radio border :disabled="disabled" v-model="couponList.use_goods_type" :label="4"
                        >部分分类可用
                    </el-radio>
                    <div v-if="couponList.use_goods_type == 2 || couponList.use_goods_type == 3" class="m-t-24">
                        <goods-select
                            mode="ll"
                            :params="{ type: 0 }"
                            v-if="!disabled"
                            v-model="goodsSelectData"
                            :show-virtual-goods="true"
                        >
                            <el-button :disabled="disabled" slot="trigger" size="small" type="primary"
                                >选择商品</el-button
                            >
                        </goods-select>

                        <el-table
                            class="m-t-24"
                            v-if="goodsSelectData.length != 0"
                            ref="paneTable"
                            style="width: 100%"
                            size="small"
                            :data="goodsSelectData"
                        >
                            <el-table-column prop="code" label="商品编码" width="110"> </el-table-column>
                            <el-table-column label="商品信息" min-width="200">
                                <template slot-scope="scope">
                                    <div class="flex">
                                        <el-image
                                            class="flex-none"
                                            style="width: 58px; height: 58px"
                                            :src="scope.row.image"
                                        />
                                        <div class="goods-info m-l-8">
                                            <div class="line-2">{{ scope.row.name }}</div>
                                        </div>
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column label="价格" width="110">
                                <template slot-scope="scope">
                                    {{ scope.row.max_price }}
                                </template>
                            </el-table-column>
                            <el-table-column label="操作" width="110">
                                <template slot-scope="scope">
                                    <el-button
                                        :disabled="disabled"
                                        @click="removeSelectGoods(scope.row.id)"
                                        type="text"
                                        size="small"
                                        >移除</el-button
                                    >
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>
                    <div v-if="couponList.use_goods_type == 4" class="m-t-24">
                        <el-cascader
                            v-model="couponList.use_goods_category_ids"
                            style="width: 280px"
                            :options="categoryList"
                            :props="{
                                multiple: true,
                                checkStrictly: true,
                                label: 'name',
                                value: 'id',
                                children: 'sons',
                                emitPath: false
                            }"
                            clearable
                            filterable
                        ></el-cascader>
                    </div>
                </el-form-item>
            </div>

            <!-- 发放设置 -->
            <div class="ls-card ls-coupon-edit__form m-t-10">
                <div class="lg weight-500 m-b-20">发放设置</div>
                <!-- 优惠的面额 -->
                <el-form-item label="推广方式" prop="get_type" required>
                    <el-radio border :disabled="disabled" v-model="couponList.get_type" :label="1">买家领取</el-radio>
                    <el-radio border :disabled="disabled" v-model="couponList.get_type" :label="2">卖家发放</el-radio>
                </el-form-item>

                <!-- 领取次数 -->
                <el-form-item label="领取次数" prop="get_num_type">
                    <el-radio-group v-model="couponList.get_num_type">
                        <div class="m-t-10">
                            <el-radio border :disabled="disabled" :label="1">不限制领取次数</el-radio>
                        </div>

                        <div class="m-t-24 flex">
                            <el-radio border :disabled="disabled" :label="2"> 限制领取 </el-radio>
                            <el-input class="ls-input" :disabled="disabled" v-model="couponList.get_num" size="small">
                                <template slot="append">次</template>
                            </el-input>
                        </div>

                        <div class="m-t-24 flex">
                            <el-radio border :disabled="disabled" :label="3"> 每天限制领取 </el-radio>
                            <el-input class="ls-input" :disabled="disabled" size="small" v-model="couponList.get_num1">
                                <template slot="append">次</template>
                            </el-input>
                        </div>
                    </el-radio-group>
                    <span class="desc">领取次数只对买家领取的场景有效</span>
                </el-form-item>
            </div>
        </el-form>

        <!-- 底部 -->
        <div class="ls-coupon-edit__footer bg-white ls-fixed-footer">
            <div class="btns row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button
                    size="small"
                    :disabled="status != 2 ? disabled : false"
                    type="primary"
                    @click="submit('couponList')"
                    >保存</el-button
                >
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import AreaSelect from '@/components/area-select.vue'
import GoodsSelect from '@/components/goods-select/index.vue'
import { throttle } from '@/utils/util'
import { apiCouponAdd, apiCouponDetail, apiCouponEdit } from '@/api/marketing/coupon'
import { apiCategoryLists } from '@/api/goods'

@Component({
    components: {
        AreaSelect,
        GoodsSelect
    }
})
export default class AddSupplier extends Vue {
    /** S Data **/

    id!: any // 当前的ID

    disabled: any = false // 是否禁用

    status: any = 0 // 状态
    categoryList: any = []
    couponList = {
        name: '', // 优惠券名称
        money: 0, // 必选	float	优惠券面额
        condition_type: 1, // 必选	int	使用条件 1=无门槛，2=订单满多少钱
        condition_money: '', // 否	float	使用条件金额，当condition_type=2时必填
        send_total_type: 1, // 必选	int	发放数量类型 1=不限数量，2=固定数量
        send_total: '', // 否	int	发放数量，send_total_type=2时必填
        use_time_type: 1, // 必选	string	用券时间类型：1=固定时间；2=领券当天起；3=领券次日起
        use_time_start: '', // 否	datetime	用券开始时间：use_time_type=1时生效
        use_time_end: '', // 否	datetime	用券结束时间：use_time_type=1时生效
        use_time: '', // 否	int	多少天内可用：use_time_type=2/3时生效
        use_time1: '',
        get_type: 1, // 必选	int	领取类型：1=用户领取, 2=商家赠送
        get_num_type: 1, // 必选	int	领取次数类型：1=不限制领取次数；2=限制次数；3=每天限制数量
        get_num: '', // 否	int	领取次数类型: get_type=2/3时生效
        get_num1: '',
        use_goods_type: 1, // 必选	int	适用商品类型：1=全部商品；2=指定商品；3=指定商品不可用
        use_goods_ids: [], // 使用商品ID, use_goods_type=2 / 3 时必填, 且数组不可为空
        use_goods_category_ids: [], //可使用的分类id
        discount_ratio: '', //满减折扣
        discount_max_money: '', //最高优惠
        categoryList: []
    }

    goodsSelectData: any = []

    rules = {
        // 表单验证
        name: [
            {
                required: true,
                message: '请输入优惠券名称',
                trigger: ['blur', 'change']
            }
        ],
        money: [
            {
                required: true,
                message: '请输入优惠券面额',
                trigger: ['blur', 'change']
            }
        ],
        send_total: [
            {
                required: true,
                message: '请输入发放数量',
                trigger: ['blur', 'change']
            }
        ],
        send_total_type: [
            {
                required: true,
                message: '请选择用券时间',
                trigger: ['blur', 'change']
            }
        ]
    }

    /** E Data **/

    /** S Method **/

    // 初始化日期格式
    add(m: number) {
        return m < 10 ? '0' + m : m
    }

    baseTime(event: any) {
        const d = new Date(event)
        return `${this.add(d.getFullYear())}-${this.add(d.getMonth() + 1)}-${this.add(d.getDate())} ${this.add(
            d.getHours()
        )}:${this.add(d.getMinutes())}:${this.add(d.getSeconds())}`
    }

    // 开始时间
    startTime(event: string) {
        this.couponList.use_time_start = this.baseTime(event)
    }

    // 结束时间
    endTime(event: any) {
        this.couponList.use_time_end = this.baseTime(event)
    }

    // 获取到选择商品的数据
    goodsData(event: any) {
        if (event.length <= 0) {
            return
        }
        this.goodsSelectData = event
    }

    // 移除选中的商品
    removeSelectGoods(id: number) {
        console.log(id)
        for (let i = 0; i < this.goodsSelectData.length; i++) {
            if (this.goodsSelectData[i].id == id) {
                this.goodsSelectData.splice(i, 1)
            }
        }
    }

    submit(formName: string) {
        // 验证表单格式是否正确
        const refs = this.$refs[formName] as HTMLFormElement
        refs.validate((valid: boolean): any => {
            if (!valid) {
                return
            }

            if (this.couponList.get_num_type == 3) {
                this.couponList.get_num = this.couponList.get_num1
            }
            if (this.couponList.use_time_type == 3) {
                this.couponList.use_time = this.couponList.use_time1
            }
            if ((this, this.couponList.use_goods_type == 2 || this.couponList.use_goods_type == 3)) {
                this.couponList.use_goods_ids = this.goodsSelectData.map((item: any) => item.id)
            } else {
                this.couponList.use_goods_ids = []
            }
            // if ((this, this.couponList.use_goods_type == 4)) {
            //     this.couponList.use_goods_category_ids = this.goodsSelectData.map((item: any) => item.id)
            // }
            // // 新增还是删除
            if (this.id) {
                // console.log({ ...this.couponList }
                this.couponEdit()
            } else {
                this.couponAdd()
            }
        })
    }

    // 编辑优惠券
    couponEdit() {
        apiCouponEdit({ ...this.couponList }).then(res => {
            setTimeout(() => this.$router.go(-1), 500)
        })
    }

    // 添加优惠券
    couponAdd() {
        apiCouponAdd({ ...this.couponList }).then(res => {
            setTimeout(() => this.$router.go(-1), 500)
        })
    }

    // 获取优惠券详情
    getCouponDetail() {
        apiCouponDetail(this.id).then((res: any) => {
            if (res.get_num_type == 3) {
                res.get_num1 = res.get_num
                res.get_num = ''
            }
            if (res.use_time_type == 3) {
                res.use_time1 = res.use_time
                res.use_time = ''
            }
            if (res.goods) {
                this.goodsSelectData = res.goods
            }
            this.couponList = res
        })
    }

    /** E Method **/

    created() {
        this.status = this.$route.query.status
        this.disabled = this.$route.query.disabled == 'true'
        this.id = this.$route.query.id
        this.id && this.getCouponDetail()
        this.submit = throttle(this.submit, 2000)
        apiCategoryLists({ pager_type: 1 }).then((res: any) => {
            this.categoryList = res.lists
        })
    }
}
</script>

<style lang="scss" scoped>
.ls-coupon-edit {
    padding-bottom: 80px;
    .ls-input {
        width: 380px;
    }
    .min-input {
        width: 220px;
    }
    .desc {
        display: block;
        color: #999;
        font-size: 12px;
    }
}
</style>
