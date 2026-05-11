<template>
    <div class="ls-team-edit">
        <div class="ls-card ls-team-edit__header">
            <el-page-header v-if="status == 0" @back="$router.go(-1)" content="拼团活动详情" />
            <el-page-header v-else @back="$router.go(-1)" :content="!id ? '新增拼团活动' : '编辑拼团活动'" />
        </div>

        <div class="ls-card ls-team-edit__form m-t-10">
            <div class="lg weight-500 m-b-20">基本信息</div>
            <el-form :disabled="status == 0" ref="teamList" :model="teamList" label-width="120px" size="small">
                <!-- 活动的名称 -->
                <el-form-item label="活动名称" required>
                    <el-input
                        :disabled="status != 2 ? disabled : false"
                        class="ls-input"
                        v-model="teamList.name"
                        placeholder="请输入活动名称"
                        size="small"
                    >
                    </el-input>
                </el-form-item>

                <el-form-item label="活动时间" required>
                    <date-picker-new
                        type="datetimerange"
                        :disabled="disabled"
                        :start-time.sync="teamList.start_time"
                        :end-time.sync="teamList.end_time"
                    />
                    <span class="desc">拼团活动开始和结束时间，可以手动提前结束活动</span>
                </el-form-item>

                <el-form-item label="活动说明">
                    <el-input
                        class="m-t-10"
                        :disabled="status != 2 ? disabled : false"
                        style="width: 530px"
                        type="textarea"
                        :rows="7"
                        placeholder="请输入活动说明"
                        v-model="teamList.explain"
                    >
                    </el-input>
                </el-form-item>
            </el-form>
        </div>

        <!-- 活动商品 -->
        <div class="ls-card ls-team-edit__form m-t-10">
            <div class="lg weight-500 m-b-20">活动商品</div>
            <el-form
                ref="teamList"
                :model="teamList"
                label-width="120px"
                size="small"
                :disabled="status != 2 ? disabled : false"
            >
                <!-- 参与分销 -->
                <!-- <el-form-item label="参与分销" required>
                    <el-radio border :disabled="disabled" v-model="teamList.is_distribution" :label="0">不参与分销</el-radio>
                    <el-radio border :disabled="disabled" v-model="teamList.is_distribution" :label="1">参与分销</el-radio>
                </el-form-item> -->

                <!-- 用券时间选择 -->
                <el-form-item label="拼团商品">
                    <goods-select
                        v-model="selectGoods"
                        mode="table"
                        :is-spec="true"
                        :limit="25"
                        :disabled="status == 0 || disabled || status == 2"
                        :extend="{
                            name: '拼团',
                            price: [
                                {
                                    title: '拼团价格',
                                    key: 'team_price'
                                }
                            ]
                        }"
                    >
                        <el-button size="mini" type="primary">选择拼团商品</el-button>
                    </goods-select>
                </el-form-item>
            </el-form>
        </div>

        <!-- 活动规则 -->
        <div class="ls-card ls-team-edit__form m-t-10">
            <div class="lg weight-500 m-b-20">活动规则</div>
            <el-form ref="teamList" :model="teamList" label-width="120px" size="small">
                <!-- 成团有效期 -->
                <el-form-item label="成团有效期" required>
                    <el-input
                        :disabled="disabled"
                        class="ls-input"
                        v-model="teamList.effective_time"
                        placeholder="请输入成团有效期"
                    >
                        <template slot="append">分钟</template>
                    </el-input>
                    <span class="desc">开团后的成团有效期，超出时间还未成团则拼团失败</span>
                </el-form-item>

                <!-- 成团人数 -->
                <el-form-item label="成团人数" required>
                    <el-input
                        :disabled="disabled"
                        class="ls-input"
                        v-model="teamList.people_num"
                        placeholder="请输入成团人数"
                    >
                        <template slot="append">人</template>
                    </el-input>
                    <span class="desc">设置商品拼团人数，最少两人成团</span>
                </el-form-item>

                <!-- 虚拟成团 -->
                <el-form-item label="虚拟成团" required>
                    <el-radio border :disabled="disabled" v-model="teamList.is_automatic" :label="1">开启</el-radio>
                    <el-radio border :disabled="disabled" v-model="teamList.is_automatic" :label="0">关闭</el-radio>
                    <span class="desc"
                        >开启虚拟成团后，在成团有效期内人数不够的团，系统会虚拟用户使拼团成功。虚拟用户不生成订单，只需对买家发货。</span
                    >
                </el-form-item>

                <!-- 起购次数 -->
                <!-- <el-form-item label="起购次数" required>
                    <el-radio-group v-model="teamList.min_buy">
                        <div class="m-t-10">
                            <el-radio border :disabled="disabled" :label="0">不限制</el-radio>
                        </div>

                        <div class="m-t-24 flex">
                            <el-radio border :disabled="disabled" :label="teamList.min_buy!=0?teamList.min_buy:1">
                                限制
                            </el-radio>
                            <el-input @change="buyBuy" :disabled="disabled || teamList.min_buy==0"
                                v-model="teamList.min_buy" size="small">
                                <template slot="append">件</template>
                            </el-input>
                        </div>
                    </el-radio-group>
                    <span class="desc">每件商品单笔订单最少购买的件数</span>
                </el-form-item> -->

                <!-- 每单限制 -->
                <el-form-item label="每单限制" required>
                    <el-radio-group v-model="teamList.max_buy">
                        <div class="m-t-10">
                            <el-radio border :disabled="disabled" :label="0">不限制</el-radio>
                        </div>

                        <div class="m-t-24 flex">
                            <el-radio border :disabled="disabled" :label="teamList.max_buy == 0 ? 1 : teamList.max_buy">
                                限制
                            </el-radio>
                            <el-input
                                @change="buyBuy2"
                                :disabled="disabled || teamList.max_buy == 0"
                                v-model="teamList.max_buy"
                                size="small"
                            >
                                <template slot="append">件</template>
                            </el-input>
                        </div>
                    </el-radio-group>
                    <span class="desc">每件商品单笔订单最多购买的件数。起购限制会影响每单限制，请合理设置。</span>
                </el-form-item>

                <!-- 优惠券 -->
                <!-- <el-form-item label="优惠券" required>
                    <el-radio border :disabled="disabled" v-model="teamList.is_coupon" :label="0">不允许使用</el-radio>
                    <el-radio border :disabled="disabled" v-model="teamList.is_coupon" :label="1">允许使用</el-radio>
                    <span class="desc">本次活动中的商品是否可以使用优惠券。选择允许使用时，满足优惠券使用条件即可使用优惠券</span>
                </el-form-item> -->
            </el-form>
        </div>

        <!-- 底部 -->
        <div class="ls-team-edit__footer bg-white ls-fixed-footer">
            <div class="btns row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" :disabled="status != 2 ? disabled : false" type="primary" @click="submit()"
                    >保存</el-button
                >
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import AreaSelect from '@/components/area-select.vue'
import GoodsSelect from '@/components/goods-select/index.vue'
import DatePickerNew from '@/components/date-picker-new.vue'
import { apiTeamAdd, apiTeamDetail, apiTeamEdit } from '@/api/marketing/combination'
@Component({
    components: {
        AreaSelect,
        GoodsSelect,
        DatePickerNew
    }
})
export default class AddSupplier extends Vue {
    /** S Data **/

    id!: any //当前的ID

    disabled: any = false //是否禁用

    status: any = 0 //状态 1=未开始 2=进行中 3=已结束

    teamList: any = {
        name: '', //必选	string	拼团活动名称
        min_buy: 1, //必选	int	起购限制: 0=不限制
        max_buy: 1, //必选	int	每单限制: 0=不限制
        people_num: '', //必选	int	拼团所需人数
        is_coupon: 1, //必选	int	是否能用活动 0=否， 1=是
        is_distribution: 1, //必选	int	是否参与分销功能 0=否，1=是
        is_automatic: 1, //必选	int	是否开启虚拟自动成团
        goods: [], //必选	array	参与活动的商品，PS: 具体格式看请求示例
        explain: '', //否	string	活动说明
        effective_time: '', //必选	int	拼团失效时间, 分钟
        start_time: '', //必选	datetime	活动开始时间, 如: 2021-07-26 11:52:32
        end_time: '' //必选	datetime	活动开始时间, 如: 2021-07-26 11:52:32 不能少于开始时间
    }

    selectGoods: any = [] //商品选择的商品数据

    /** E Data **/

    @Watch('selectGoods', { deep: true })
    selectGoodsChange(val: any[]) {
        console.log('val', val)

        this.teamList.goods = val.map((item: any) => {
            return {
                goods_id: item.id,
                items: item.item.map((sitem: any) => ({
                    item_id: sitem.id,
                    team_price: sitem.team_price
                })),
                virtual_click_num: item.virtual_click_num,
                virtual_sales_num: item.virtual_sales_num
            }
        })
    }

    checkGoods() {
        const goods = this.teamList.goods
        if (!goods.length) {
            this.$message.error('请选择拼团商品')
            return false
        }
        for (let i = 0; i < goods.length; i++) {
            for (let j = 0; j < goods[i].items.length; j++) {
                if (!goods[i].items[j].team_price) {
                    this.$message.error(`请输入商品拼团价`)
                    return false
                }
            }
        }
        return true
    }

    /** S Method **/

    // 起购不允许为空
    buyBuy(event: any) {
        if (event.detail === '') {
            this.teamList.min_buy = 0
        }
    }

    // 起购不允许为空
    buyBuy2(event: any) {
        if (event === '') {
            this.teamList.max_buy = 0
        }
    }

    // 提交
    submit() {
        if (!this.checkGoods()) {
            return
        }
        // // 新增还是删除
        if (this.id) {
            this.teamEdit()
        } else {
            this.teamAdd()
        }
    }

    // 编辑活动
    teamEdit() {
        apiTeamEdit({ ...this.teamList })
            .then(res => {
                setTimeout(() => this.$router.go(-1), 500)
            })
            .catch(() => {
                this.$message.error('修改失败!')
            })
    }

    // 添加活动
    teamAdd() {
        apiTeamAdd({ ...this.teamList })
            .then(res => {
                setTimeout(() => this.$router.go(-1), 500)
            })
            .catch(() => {
                this.$message.error('添加失败!')
            })
    }

    // 获取活动详情
    getteamDetail() {
        apiTeamDetail({ id: this.id }).then((res: any) => {
            this.teamList = res
            this.selectGoods = res.goods
        })
    }

    /** E Method **/

    created() {
        this.status = this.$route.query.status
        this.disabled = this.$route.query.disabled == 'true' ? true : false
        this.id = this.$route.query.id
        this.id && this.getteamDetail()
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
