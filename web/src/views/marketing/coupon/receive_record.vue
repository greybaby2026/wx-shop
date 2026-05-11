<template>
    <div class="ls-goods">
        <div class="ls-goods__top ls-card">
            <el-alert
                title="温馨提示：1.查看优惠券领取记录；2.未使用的优惠券可以作废。"
                type="info"
                show-icon
                :closable="false"
            >
            </el-alert>

            <div class="coupon-search m-t-16">
                <el-form ref="form" inline :model="couponSearchData" label-width="100px" size="small">
                    <el-form-item label="用户">
                        <el-input style="width: 280px" v-model="couponSearchData.nickname" placeholder="请输入用户名称">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="优惠券名称">
                        <el-input style="width: 280px" v-model="couponSearchData.name" placeholder="请输入优惠券名称">
                        </el-input>
                    </el-form-item>
                    <!-- <el-form-item label="已领券状态"> -->
                    <el-form-item label="使用状态">
                        <el-select v-model="couponSearchData.status" placeholder="全部">
                            <el-option
                                v-for="item in options"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value"
                            >
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="领取时间">
                        <date-picker
                            :start-time.sync="couponSearchData.start_time"
                            :end-time.sync="couponSearchData.end_time"
                        />
                    </el-form-item>
                    <el-form-item label="" class="m-l-6">
                        <el-button size="mini" type="primary" @click="getCouponRecord(1)">查询</el-button>
                        <el-button size="mini" @click="resetcouponSearchData">重置</el-button>

                        <export-data
                            class="m-l-10"
                            :pageSize="pager.size"
                            :method="apiCouponRecord"
                            :param="couponSearchData"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="m-t-24 ls-card">
            <div class="m-b-24">
                <el-button type="primary" size="mini" @click="selectAll">当页全选</el-button>
                <ls-dialog
                    title="作废优惠券"
                    class="inline m-l-24"
                    :content="`确定批量做废吗?请谨慎操作`"
                    @confirm="couponDel"
                >
                    <el-button slot="trigger" size="mini" type="primary">批量作废</el-button>
                </ls-dialog>
            </div>

            <el-table
                ref="paneTable"
                :data="pager.lists"
                @selection-change="selectionChange"
                :default-sort="{ prop: 'create_time', order: 'descending' }"
                v-loading="pager.loading"
                style="width: 100%"
                size="mini"
            >
                <!-- <el-table-column fixed="left" type="selection" width="55"> </el-table-column> -->
                <el-table-column width="45">
                    <template slot-scope="{ row }">
                        <!-- <el-checkbox :value="selectItem(row)" @change="handleSelect($event, row)"></el-checkbox> -->
                        <el-checkbox
                            @change="handleSelect($event, row)"
                            :disabled="row.status !== 0"
                            :value="selectItem(row)"
                        ></el-checkbox>
                    </template>
                </el-table-column>
                <el-table-column prop="name" label="优惠券名称" min-width="150"> </el-table-column>
                <el-table-column prop="discount_content" label="用户昵称" min-width="140">
                    <template slot-scope="scope">
                        <el-popover placement="top" width="200" trigger="hover">
                            <div class="flex">
                                <span class="flex-none m-r-20">头像：</span>
                                <el-image :src="scope.row.avatar" style="width: 40px; height: 40px; border-radius: 50%">
                                </el-image>
                            </div>
                            <div class="flex m-t-20 col-top">
                                <span class="flex-none m-r-20">昵称：</span>
                                <span>{{ scope.row.nickname }}</span>
                            </div>
                            <div class="flex m-t-20 col-top">
                                <span class="flex-none m-r-20">编号：</span>
                                <span>{{ scope.row.sn }}</span>
                            </div>
                            <div slot="reference" class="pointer" @click="toUser(scope.row.user_id)">
                                {{ scope.row.nickname }}
                            </div>
                        </el-popover>
                    </template>
                </el-table-column>
                <el-table-column prop="coupon_code" label="已领券券码" min-width="180"> </el-table-column>
                <el-table-column prop="create_time" sortable label="领取时间" min-width="180"> </el-table-column>
                <!-- <el-table-column prop="status_text" label="已领券状态" min-width="180"> -->
                <el-table-column prop="status_text" label="使用状态" min-width="180"> </el-table-column>
                <el-table-column prop="use_time" sortable label="使用时间" min-width="180">
                    <template slot-scope="scope">
                        {{ scope.row.use_time || '-' }}
                    </template>
                </el-table-column>
                <el-table-column prop="invalid_time" sortable label="过期时间" min-width="180"> </el-table-column>
                <el-table-column fixed="right" label="操作" min-width="180">
                    <template slot-scope="scope">
                        <ls-dialog
                            title="作废优惠券"
                            class="inline"
                            v-if="scope.row.status_text === '未使用'"
                            :content="`确定作废：${scope.row.name}？请谨慎操作`"
                            @confirm="couponDel([scope.row.cl_id])"
                        >
                            <el-button slot="trigger" type="text" size="small">作废</el-button>
                        </ls-dialog>

                        <el-button
                            @click="toOrder(scope.row.order_id)"
                            v-if="scope.row.status_text === '已使用'"
                            slot="trigger"
                            type="text"
                            size="small"
                            >查看订单</el-button
                        >
                    </template>
                </el-table-column>
            </el-table>

            <div class="m-t-24 flex row-right">
                <ls-pagination v-model="pager" @change="getCouponRecord()"></ls-pagination>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import LsPagination from '@/components/ls-pagination.vue'
import LsDialog from '@/components/ls-dialog.vue'
import { RequestPaging } from '@/utils/util'
import { apiCouponRecord, apiCouponVoid } from '@/api/marketing/coupon'
import DatePicker from '@/components/date-picker.vue'
import ExportData from '@/components/export-data/index.vue'
@Component({
    components: {
        LsPagination,
        LsDialog,
        DatePicker,
        ExportData
    }
})
export default class Goods extends Vue {
    /** S Data **/
    $refs!: { paneTable: any }

    apiCouponRecord = apiCouponRecord

    couponSearchData = {
        nickname: '',
        status: '',
        name: '',
        start_time: '',
        end_time: ''
    }

    options = [
        { value: '', label: '全部' },
        { value: '0', label: '未使用' },
        { value: '1', label: '已使用' },
        { value: '2', label: '已过期' },
        { value: '3', label: '已作废' }
    ]

    pager = new RequestPaging()

    selectIds: Array<any> = []

    /** E Data **/

    /** S Method **/

    // 获取优惠券记录数据
    getCouponRecord(page?: number): void {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apiCouponRecord,
            params: {
                ...this.couponSearchData
            }
        })
    }

    // 选择某条数据
    selectionChange(val: any[]) {
        this.selectIds = val.map(item => item.cl_id)
    }
    get selectItem() {
        return (val: any) => {
            return this.selectIds.some(i => {
                return i == val.cl_id
            })
        }
    }
    // 全选优惠券
    selectAll() {
        // this.$refs.paneTable.toggleAllSelection()
        if (this.selectIds.length != 0) {
            this.selectIds = []
        } else {
            this.pager.lists.forEach(i => {
                if (i.status == 0) {
                    return this.selectIds.push(i.cl_id)
                }
            })
        }
    }
    handleSelect($event: boolean, row: any) {
        if ($event) {
            this.selectIds.push(row.cl_id)
        } else {
            this.selectIds = this.selectIds.filter((i: any) => {
                return i !== row.cl_id
            })
        }
    }

    //  作废优惠券
    couponDel(ids: Array<Number>) {
        if (this.selectIds.length == 0 && !ids) {
            return this.$message.error('请先选择要作废的优惠券')
        }
        apiCouponVoid({
            cl_id: Array.isArray(ids) ? ids : this.selectIds
        }).then(res => {
            this.resetcouponSearchData()
        })
        this.selectIds = []
    }

    // 重置搜索领取记录
    resetcouponSearchData() {
        Object.keys(this.couponSearchData).map(key => {
            this.$set(this.couponSearchData, key, '')
        })
        this.getCouponRecord()
    }

    // 去订单详情
    toOrder(id: any) {
        this.$router.push({
            path: '/order/order_detail',
            query: { id: id }
        })
    }

    // 去用户详情
    toUser(id: any) {
        this.$router.push({
            path: '/user/user_details',
            query: { id: id }
        })
    }

    /** E Method **/

    created() {
        this.getCouponRecord()
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
