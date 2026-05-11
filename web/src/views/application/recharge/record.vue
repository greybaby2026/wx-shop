<template>
    <div class="ls-goods">
        <div class="ls-goods__top ls-card">
            <el-alert title="温馨提示：1.下单未支付状态不能重新进行支付。" type="info" show-icon :closable="false">
            </el-alert>

            <div class="-search m-t-16">
                <!-- 头部表单 -->
                <el-form ref="form" inline :model="SearchData" label-width="100px" size="small">
                    <el-form-item label="订单信息">
                        <el-input style="width: 280px" v-model="SearchData.sn" placeholder="请输入充值单号"> </el-input>
                    </el-form-item>
                    <el-form-item label="用户信息">
                        <el-input style="width: 280px" v-model="SearchData.nickname" placeholder="请输入用户昵称">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="支付方式">
                        <el-select v-model="SearchData.pay_way" placeholder="全部">
                            <el-option v-for="item in pay_way" :key="item.name" :label="item.label" :value="item.name">
                            </el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="订单状态">
                        <el-select v-model="SearchData.pay_status" placeholder="全部">
                            <el-option
                                v-for="item in pay_status"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value"
                            >
                            </el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="下单时间">
                        <el-select style="width: 120px" v-model="SearchData.type_time" placeholder="全部">
                            <el-option label="全部" value=""></el-option>
                            <el-option label="下单时间" :value="2"></el-option>
                            <el-option label="支付时间" :value="1"></el-option>
                        </el-select>
                        <date-picker :start-time.sync="SearchData.start_time" :end-time.sync="SearchData.end_time" />
                    </el-form-item>
                    <el-form-item label="" class="m-l-6">
                        <el-button size="small" type="primary" @click="getRecord(1)">查询</el-button>
                        <el-button size="small" @click="resetSearchData">重置</el-button>

                        <export-data
                            class="m-l-10"
                            :pageSize="pager.size"
                            :method="apiRechargeRecord"
                            :param="SearchData"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="m-t-24 ls-card">
            <!-- 数据表格 -->
            <el-table ref="paneTable" :data="pager.lists" v-loading="pager.loading" style="width: 100%" size="mini">
                <el-table-column prop="sn" label="订单编号" width="180"> </el-table-column>
                <el-table-column prop="discount_content" label="用户昵称" min-width="180">
                    <template slot-scope="scope">
                        <div class="flex">
                            <img :src="scope.row.avatar" alt="" />
                            <span class="m-l-10 line-1">{{ scope.row.nickname }}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="order_amount" label="充值金额" width="180"> </el-table-column>
                <el-table-column prop="give_money" label="赠送余额" width="180"> </el-table-column>
                <el-table-column prop="pay_way" label="支付方式" width="180"> </el-table-column>
                <el-table-column prop="pay_time" label="支付时间" width="180"> </el-table-column>
                <el-table-column prop="pay_status" label="订单状态" width="180"> </el-table-column>
                <el-table-column prop="create_time" label="下单时间" width="180"> </el-table-column>
            </el-table>

            <div class="m-t-24 flex row-right">
                <ls-pagination v-model="pager" @change="getRecord()"></ls-pagination>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
// 订单头部筛选未完成
import { Component, Vue } from 'vue-property-decorator'
import LsPagination from '@/components/ls-pagination.vue'
import LsDialog from '@/components/ls-dialog.vue'
import { RequestPaging } from '@/utils/util'
import DatePicker from '@/components/date-picker.vue'
import { apiRechargeRecord } from '@/api/application/recharge'
import ExportData from '@/components/export-data/index.vue'
@Component({
    components: {
        LsPagination,
        LsDialog,
        DatePicker,
        ExportData
    }
})
export default class GooRechargeRecord extends Vue {
    /** S Data **/
    $refs!: { paneTable: any }

    apiRechargeRecord = apiRechargeRecord

    SearchData = {
        nickname: '',
        pay_status: '',
        sn: '',
        start_time: '',
        end_time: '',
        type_time: ''
    }

    // 支付方式
    pay_way = [
        { name: '', label: '全部' },
        { name: '2', label: '微信支付' },
        { name: '3', label: '支付宝支付' }
    ]

    // 支付状态
    pay_status = [
        { value: '', label: '全部' },
        { value: '0', label: '未支付' },
        { value: '1', label: '已支付' }
    ]

    pager = new RequestPaging()

    /** E Data **/

    /** S Method **/

    // 获取充值记录数据
    getRecord(page?: number): void {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apiRechargeRecord,
            params: {
                ...this.SearchData
            }
        })
    }

    // 重置搜索领取记录
    resetSearchData() {
        Object.keys(this.SearchData).map(key => {
            this.$set(this.SearchData, key, '')
        })
        this.getRecord()
    }

    /** E Method **/

    created() {
        this.getRecord()
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

    img {
        width: 50px;
        height: 50px;
    }
}
</style>
