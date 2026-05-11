<template>
    <div class="ls-goods">
        <div class="ls-goods__top ls-card">
            <el-alert title="温馨提示：1.分销订单明细。" type="info" show-icon :closable="false"> </el-alert>

            <div class="coupon-search m-t-16">
                <el-form ref="form" inline :model="goodsSearchData" label-width="100px" size="small">
                    <el-form-item label="订单信息">
                        <el-input style="width: 280px" v-model="goodsSearchData.sn" placeholder="请输入订单编号">
                        </el-input>
                    </el-form-item>

                    <el-form-item label="买家信息">
                        <el-input style="width: 280px" v-model="goodsSearchData.keyword" placeholder="用户名称/编码">
                        </el-input>
                    </el-form-item>

                    <el-form-item label="分销商信息">
                        <el-input style="width: 280px" v-model="goodsSearchData.dkeyword" placeholder="分销商名称/编码">
                        </el-input>
                    </el-form-item>

                    <el-form-item label="商品信息">
                        <el-input style="width: 280px" v-model="goodsSearchData.name" placeholder="商品名称/编码">
                        </el-input>
                    </el-form-item>

                    <!-- <el-form-item label="分销商">
                        <el-input style="width: 280px" v-model="goodsSearchData.name" placeholder="请输入分销商信息">
                        </el-input>
                    </el-form-item> -->

                    <el-form-item label="佣金状态">
                        <el-select class="header-input" v-model="goodsSearchData.status" placeholder="全部">
                            <el-option
                                v-for="item in options"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value"
                            >
                            </el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="下单时间">
                        <date-picker
                            :start-time.sync="goodsSearchData.start_time"
                            :end-time.sync="goodsSearchData.end_time"
                        />
                    </el-form-item>

                    <el-form-item label="" class="m-l-6">
                        <el-button size="small" type="primary" @click="getDistributionData(1)">查询</el-button>
                        <el-button size="small" @click="resetgoodsSearchData">重置</el-button>

                        <export-data
                            class="m-l-10"
                            :pageSize="pager.size"
                            :method="apiDistributionOrdersLists"
                            :param="goodsSearchData"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="m-t-24 ls-card pane-table">
            <el-table ref="paneTable" :data="pager.lists" v-loading="pager.loading" style="width: 100%" size="mini">
                <el-table-column prop="name" label="买家" min-width="150">
                    <template slot-scope="scope">
                        <div class="flex">
                            <el-image
                                class="flex-none"
                                style="width: 58px; height: 58px"
                                :src="scope.row.buyer_avatar"
                            />
                            <div class="goods-info m-l-8">
                                <div class="line-2">{{ scope.row.buyer_nickname }}({{ scope.row.buyer_sn }})</div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="sn" label="订单编号" min-width="180"></el-table-column>
                <el-table-column prop="is_distribution_desc" label="商品信息" min-width="150">
                    <template slot-scope="scope">
                        <div class="flex">
                            <el-image class="flex-none" style="width: 58px; height: 58px" :src="scope.row.image" />
                            <div class="goods-info m-l-8">
                                <div class="line-2">
                                    {{ scope.row.goods_name }}
                                </div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="goods_price" label="商品单价" min-width="80"></el-table-column>
                <el-table-column prop="goods_num" label="购买数量" min-width="100"></el-table-column>
                <el-table-column prop="total_pay_price" label="实付金额" min-width="80"></el-table-column>
                <el-table-column prop="level_desc" label="当前分销等级" min-width="130"></el-table-column>
                <!-- 分销商的信息 -->
                <el-table-column label="分销商信息" prop="nickname" min-width="120">
                    <template slot-scope="scope">
                        <el-popover placement="top" width="200" trigger="hover">
                            <div class="flex">
                                <span class="flex-none m-r-20">头像：</span>
                                <el-image
                                    :src="scope.row.distribution_member_avatar"
                                    style="width: 40px; height: 40px; border-radius: 50%"
                                >
                                </el-image>
                            </div>
                            <div class="flex m-t-20 col-top">
                                <span class="flex-none m-r-20">昵称：</span>
                                <span>{{ scope.row.distribution_member_nickname }}</span>
                            </div>
                            <div class="flex m-t-20 col-top">
                                <span class="flex-none m-r-20">编号：</span>
                                <span>{{ scope.row.distribution_member_sn }}</span>
                            </div>
                            <div slot="reference" class="pointer">
                                {{ scope.row.distribution_member_nickname }}
                            </div>
                        </el-popover>
                    </template>
                </el-table-column>
                <el-table-column prop="ratio" label="当前佣金比例(%)" min-width="120"></el-table-column>
                <el-table-column prop="earnings" label="佣金金额" min-width="80"></el-table-column>
                <el-table-column prop="status_desc" label="佣金状态" min-width="80"></el-table-column>
                <el-table-column prop="settlement_time" label="结算时间" min-width="110"></el-table-column>
                <el-table-column prop="order_create_time" label="下单时间" min-width="110"></el-table-column>
            </el-table>

            <div class="m-t-24 flex row-right">
                <ls-pagination v-model="pager" @change="getDistributionData()"></ls-pagination>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import LsPagination from '@/components/ls-pagination.vue'
import LsDialog from '@/components/ls-dialog.vue'
import { RequestPaging } from '@/utils/util'
import DatePicker from '@/components/date-picker.vue'
import ExportData from '@/components/export-data/index.vue'
import { apiDistributionOrdersLists } from '@/api/distribution/distribution'
@Component({
    components: {
        LsPagination,
        LsDialog,
        DatePicker,
        ExportData
    }
})
export default class DistributionGoods extends Vue {
    /** S Data **/

    apiDistributionOrdersLists = apiDistributionOrdersLists

    // 分销订单列表
    distributionList: any = []

    // 搜索分销订单表单
    goodsSearchData = {
        sn: '',
        keyword: '',
        dkeyword: '',
        name: '',
        status: '',
        start_time: '',
        end_time: ''
    }

    // 佣金状态选择
    options = [
        { value: '', label: '全部' },
        { value: '1', label: '待结算' },
        { value: '2', label: '已入账' },
        { value: '3', label: '已失效' }
    ]

    // 分页
    pager = new RequestPaging()

    /** E Data **/

    /** S Method **/

    // 获取分销列表
    getDistributionData(page?: number): void {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apiDistributionOrdersLists,
            params: {
                ...this.goodsSearchData
            }
        })
    }

    // 重置
    resetgoodsSearchData() {
        Object.keys(this.goodsSearchData).map(key => {
            this.$set(this.goodsSearchData, key, '')
        })
        this.getDistributionData()
    }

    /** E Method **/

    created() {
        this.getDistributionData()
    }
}
</script>

<style lang="scss" scoped>
.ls-goods {
    &__top {
        padding-bottom: 6px;
    }
    .goods-search {
        .ls-input-price {
            width: 180px;
        }
    }
    .ls-goods__content {
        padding-top: 0;
    }
}
</style>
