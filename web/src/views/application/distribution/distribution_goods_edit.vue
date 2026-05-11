<template>
    <div class="ls-coupon-edit">
        <div class="ls-card ls-coupon-edit__header">
            <el-page-header @back="$router.go(-1)" content="设置分销佣金"></el-page-header>
        </div>

        <!-- 优惠设置 -->
        <div class="ls-card ls-coupon-edit__form m-t-10">
            <div class="nr weight-500 m-b-20">商品信息</div>
            <el-form ref="distributionData" :model="distributionData" label-width="120px" size="small">
                <el-form-item label="商品编号" prop="" required>
                    <template>
                        {{ distributionData.code }}
                    </template>
                </el-form-item>

                <el-form-item label="商品图片" prop="" required>
                    <template>
                        <el-image class="flex-none" style="width: 58px; height: 58px" :src="distributionData.image" />
                    </template>
                </el-form-item>

                <el-form-item label="商品名称" prop="" required>
                    <template>
                        {{ distributionData.name }}
                    </template>
                </el-form-item>
            </el-form>
        </div>

        <!-- 发放设置 -->
        <div class="ls-card ls-coupon-edit__form m-t-10 m-b-50">
            <div class="nr weight-500 m-b-20">佣金设置</div>
            <el-form ref="distributionData" :model="distributionData" label-width="120px" size="small">
                <!-- 优惠的面额 -->
                <el-form-item label="分销状态" required>
                    <el-radio v-model="distributionData.is_distribution" :label="1">参与分销</el-radio>
                    <el-radio v-model="distributionData.is_distribution" :label="0">不参与分销</el-radio>
                    <span class="desc">是否参与分销，选择不参与分销将不产生分销佣金</span>
                </el-form-item>

                <!-- 佣金规则 -->
                <el-form-item class="" label="佣金规则" required>
                    <el-radio v-model="distributionData.rule" :label="1">默认分销等级佣金规则</el-radio>
                    <el-radio v-model="distributionData.rule" :label="2">单独设置</el-radio>

                    <el-table
                        class="m-t-24"
                        :cell-style="{ background: '#fff' }"
                        ref="paneTable"
                        :data="distributionData.ratio_data"
                        style="width: 100%"
                        size="mini"
                    >
                        <el-table-column prop="name_desc" label="分销等级" min-width="140"></el-table-column>

                        <el-table-column label="商品规格" min-width="180">
                            <template slot-scope="scope">
                                <el-table
                                    class="tab-item"
                                    :data="scope.row.items"
                                    :show-header="false"
                                    style="width: 100%"
                                    size="mini"
                                >
                                    <el-table-column prop="spec_value_str"> </el-table-column>
                                </el-table>
                            </template>
                        </el-table-column>

                        <el-table-column label="价格" min-width="100">
                            <template slot-scope="scope">
                                <el-table
                                    class="tab-item"
                                    :data="scope.row.items"
                                    :show-header="false"
                                    style="width: 100%"
                                    size="mini"
                                >
                                    <el-table-column prop="sell_price"> </el-table-column>
                                </el-table>
                            </template>
                        </el-table-column>
                        <el-table-column label="自购佣金比例(%)" min-width="100">
                            <template slot-scope="scope">
                                <el-table
                                    class="tab-item"
                                    :data="scope.row.items"
                                    :show-header="false"
                                    style="width: 100%"
                                    size="mini"
                                >
                                    <el-table-column prop="self_ratio">
                                        <template slot-scope="scopes">
                                            <!-- {{scope.row.items}} -->
                                            <span v-if="distributionData.rule == 1">{{ scopes.row.self_ratio }}</span>
                                            <el-input
                                                v-else
                                                style="width: 50px; height: 10px"
                                                v-model="
                                                    distributionData.ratio_data[scope.$index].items[scopes.$index]
                                                        .self_ratio
                                                "
                                                placeholder="请输入内容"
                                            ></el-input>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </template>
                        </el-table-column>
                        <el-table-column label="一级佣金比例(%)" min-width="100">
                            <template slot-scope="scope">
                                <el-table
                                    class="tab-item"
                                    :data="scope.row.items"
                                    :show-header="false"
                                    :highlight-current-row="false"
                                    style="width: 100%"
                                    size="mini"
                                >
                                    <el-table-column prop="first_ratio">
                                        <template slot-scope="scopes">
                                            <span v-if="distributionData.rule == 1">{{ scopes.row.first_ratio }}</span>
                                            <el-input
                                                v-else
                                                v-model="
                                                    distributionData.ratio_data[scope.$index].items[scopes.$index]
                                                        .first_ratio
                                                "
                                                placeholder="请输入内容"
                                            ></el-input>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </template>
                        </el-table-column>
                        <el-table-column label="二级佣金比例(%)" min-width="100">
                            <template slot-scope="scope">
                                <el-table
                                    class="tab-item"
                                    :data="scope.row.items"
                                    :show-header="false"
                                    style="width: 100%"
                                    size="mini"
                                >
                                    <el-table-column prop="second_ratio">
                                        <template slot-scope="scopes">
                                            <span v-if="distributionData.rule == 1">{{ scopes.row.second_ratio }}</span>
                                            <el-input
                                                v-else
                                                v-model="
                                                    distributionData.ratio_data[scope.$index].items[scopes.$index]
                                                        .second_ratio
                                                "
                                                placeholder="请输入内容"
                                            ></el-input>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-form-item>
            </el-form>
        </div>

        <!-- 底部 -->
        <div class="ls-coupon-edit__footer bg-white ls-fixed-footer">
            <div class="btns row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="submit('distributionData')">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiDistributionGoodsDetails, apiDistributionGoodsSet } from '@/api/distribution/distribution'
@Component
export default class AddSupplier extends Vue {
    /** S Data **/

    id!: any //当前的ID

    distributionData = {
        ratio_data: [
            {
                level_id: 1,
                item_id: 1,
                self_ratio: 10,
                first_ratio: 10,
                second_ratio: 10
            },
            {
                level_id: 1,
                item_id: 2,
                self_ratio: 20,
                first_ratio: 20,
                second_ratio: 20
            },
            {
                level_id: 4,
                item_id: 1,
                self_ratio: 30,
                first_ratio: 30,
                second_ratio: 30
            }
        ], //佣金比例
        id: '',
        goods_id: '',
        rule: 1, // 佣金规则1是默认  2是单独
        is_distribution: [] //是否参与 0 不 1是
    }

    goodsSelectData: any = []

    rules = {
        //表单验证
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
        ]
    }

    /** E Data **/

    /** S Method **/

    submit(formName: string) {
        // 验证表单格式是否正确
        const refs = this.$refs[formName] as HTMLFormElement
        refs.validate((valid: boolean): any => {
            if (!valid) {
                return
            }

            this.couponAdd()
        })
    }

    // 设置时初始化数据结构成为接口适用的数据
    base(data: any) {
        const obj: any = {
            goods_id: data.id || data.goods_id,
            is_distribution: data.is_distribution,
            rule: data.rule,
            ratio_data: []
        }

        Object.keys(data.ratio_data).map((res, index) => {
            for (let i = 0; i < data.ratio_data[index].items.length; i++) {
                const el = data.ratio_data[index].items[i]
                obj.ratio_data.push({
                    level_id: el.level_id,
                    item_id: el.item_id,
                    self_ratio: el.self_ratio,
                    first_ratio: el.first_ratio,
                    second_ratio: el.second_ratio
                })
            }
        })

        return obj
    }

    // 设置分销商品
    couponAdd() {
        const edit = this.base(this.distributionData)

        apiDistributionGoodsSet({ ...edit })
            .then(res => {
                this.$message.success('设置成功!')
                setTimeout(() => this.$router.go(-1), 500)
            })
            .catch(() => {
                this.$message.error('设置失败!')
            })
    }

    // 获取分销商品详情
    getCouponInfo() {
        apiDistributionGoodsDetails({ id: this.id }).then((res: any) => {
            this.distributionData = { ...res }
        })
    }

    /** E Method **/

    created() {
        this.id = this.$route.query.id
        this.id && this.getCouponInfo()
    }
}
</script>

<style lang="scss" scoped>
::v-deep .tab-item td,
.tab-item th {
    border: none !important;
}
::v-deep .tab-item::before {
    background: none !important;
}
::v-deep .el-input--small .el-input__inner {
    height: 20px !important;
}
::v-deep .item .el-table tbody tr {
    pointer-events: none;
}
.desc {
    display: block;
    color: #999;
    font-size: 12px;
}
</style>
