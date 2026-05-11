<template>
    <div class="ls-goods">
        <div class="ls-goods__top ls-card">
            <el-alert
                title="温馨提示：1.批量打印待发货的订单，电子面单打印后会自动标识订单为已发货，避免二次打印；2.拼团中的订单需要等待拼团成功后才能发货；"
                type="info"
                show-icon
                :closable="false"
            >
            </el-alert>

            <div class="-search m-t-16">
                <!-- 头部表单 -->
                <el-form inline :model="SearchData" label-width="100px" size="mini">
                    <el-form-item label="订单信息">
                        <el-input style="width: 280px" v-model="SearchData.order_sn" placeholder="请输入订单编号">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="用户信息">
                        <el-input style="width: 280px" v-model="SearchData.userInfo" placeholder="请输入用户昵称">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="商品信息">
                        <el-input style="width: 280px" v-model="SearchData.goods_name" placeholder="请输入商品名称">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="收货信息">
                        <el-input style="width: 280px" v-model="SearchData.contact_info" placeholder="请输入收货人姓名">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="下单时间">
                        <date-picker :start-time.sync="SearchData.start_time" :end-time.sync="SearchData.end_time" />
                    </el-form-item>
                    <el-form-item label="" class="m-l-6">
                        <el-button size="small" type="primary" @click="getBatchLists(1)">查询</el-button>
                        <el-button size="small" @click="resetSearchData">重置</el-button>

                        <export-data class="m-l-10" :pageSize="pager.size" :method="apiOrderLists" :param="SearchData">
                        </export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="m-t-24 ls-card">
            <ls-dialog top="20vh" class="inline m-b-24" width="35vw" title="打印电子面单" @confirm="prints">
                <el-button slot="trigger" type="primary" size="small">打印电子面单</el-button>
                <div class="flex row-center" style="height: 20vh">
                    <div>
                        <el-form :model="form" label-width="100px" size="small">
                            <el-form-item label="发件人模版" required>
                                <el-select v-model="form.sender_id" placeholder="请选择">
                                    <el-option
                                        v-for="(item, index) in senderTemplate"
                                        :key="index"
                                        :label="item.name"
                                        :value="item.id"
                                    >
                                    </el-option>
                                </el-select>
                            </el-form-item>

                            <el-form-item label="电子面单模版" required>
                                <el-select v-model="form.template_id" placeholder="请选择">
                                    <el-option
                                        v-for="(item, index) in faceSheetTemplate"
                                        :key="index"
                                        :label="item.name"
                                        :value="item.id"
                                    >
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-form>
                    </div>
                </div>
            </ls-dialog>

            <!-- 数据表格 -->
            <el-table
                :data="pager.lists"
                ref="paneTable"
                style="width: 100%"
                size="mini"
                v-loading="pager.loading"
                @selection-change="selectionChange"
            >
                <!-- 选择 -->
                <el-table-column fixed="left" type="selection" width="55"></el-table-column>
                <!-- 订单编号 -->
                <el-table-column label="订单编号" prop="sn" width="180"></el-table-column>
                <!-- 卖家的信息 -->
                <el-table-column label="用户信息" prop="nickname" min-width="120">
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
                                <span>{{ scope.row.user_sn }}</span>
                            </div>
                            <div slot="reference" class="pointer" @click="toUser(scope.row.user_id)">
                                {{ scope.row.nickname }}
                            </div>
                        </el-popover>
                    </template>
                </el-table-column>

                <!-- 商品的图片 -->
                <el-table-column label="商品图片" min-width="90">
                    <template slot-scope="scope">
                        <div
                            class="m-t-10"
                            style="height: 65px"
                            v-for="(item, index) in scope.row.order_goods"
                            :key="index"
                        >
                            <el-image
                                v-if="index < 3"
                                :src="item.goods_image"
                                style="width: 58px; height: 58px"
                                class="flex-none"
                            >
                            </el-image>
                        </div>
                        <div
                            class="muted m-t-5 flex"
                            v-if="scope.row.order_goods.length > 2"
                            @click="toOrder(scope.row.id)"
                        >
                            共{{ scope.row.order_goods.length }}件商品
                            <i class="el-icon-arrow-right"></i>
                        </div>
                    </template>
                </el-table-column>

                <!-- 商品的信息 -->
                <el-table-column label="商品信息" min-width="260">
                    <template slot-scope="scope">
                        <div
                            :style="{
                                'margin-bottom': scope.row.order_goods.length > 2 ? '15px' : ''
                            }"
                        >
                            <div
                                class="m-t-10 goods"
                                @click="toOrder(scope.row.id)"
                                v-for="(item, index) in scope.row.order_goods"
                                :key="index"
                            >
                                <div v-if="index < 3">
                                    <div class="flex row-between normal p-r-24 line-1" style="line-height: 12px">
                                        <span class="line-1 name">{{ item.goods_name }}</span>
                                    </div>
                                    <div class="xs lighter flex line-1 p-r-24">规格：{{ item.spec_value_str }}</div>
                                    <div class="xs muted flex p-r-24 line-1">
                                        价格：<span class="normal m-r-10">¥{{ item.goods_price }}</span> 数量：<span
                                            class="normal"
                                            >{{ item.goods_num }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <!-- 实际付款金额 -->
                <el-table-column prop="order_amount" label="实付金额" min-width="120"> </el-table-column>

                <!-- 收货人 -->
                <el-table-column prop="contact" label="收货人" min-width="120">
                    <template slot-scope="scope">
                        <el-popover placement="top" width="300" trigger="hover">
                            <div>收件人：{{ scope.row.contact }}</div>
                            <div class="m-t-10">手机号：{{ scope.row.mobile }}</div>
                            <div class="m-t-10">
                                地&nbsp;&nbsp;&nbsp;址：
                                {{ scope.row.delivery_address }}
                            </div>
                            <el-tag size="medium" slot="reference">{{ scope.row.contact }}</el-tag>
                        </el-popover>
                    </template>
                </el-table-column>

                <!-- 配送方式 -->
                <el-table-column label="配送方式" prop="order_status_desc" min-width="120">
                    <template slot-scope="scope">
                        <span>{{ scope.row.delivery_type_desc }}</span>
                    </template>
                </el-table-column>

                <!-- 支付状态 -->
                <el-table-column label="支付状态" prop="pay_status_desc" min-width="120">
                    <template slot-scope="scope">
                        <span style="color: #ff2214" v-if="scope.row.pay_status_desc == '未支付'">{{
                            scope.row.pay_status_desc
                        }}</span>
                        <span v-else>{{ scope.row.pay_status_desc }}</span>
                    </template>
                </el-table-column>

                <!-- 订单的状态 -->
                <el-table-column label="订单状态" prop="order_status_desc" min-width="120">
                    <template slot-scope="scope">
                        <span>{{ scope.row.order_status_desc }}</span>
                    </template>
                </el-table-column>

                <!-- 操作 -->
                <el-table-column fixed="right" label="操作" min-width="250">
                    <template slot-scope="scope">
                        <div class="flex">
                            <ls-dialog
                                top="20vh"
                                class="inline"
                                width="35vw"
                                title="打印电子面单"
                                @confirm="prints([scope.row.id])"
                            >
                                <el-button slot="trigger" type="text" size="mini">打印电子面单</el-button>
                                <div class="flex row-center" style="height: 20vh">
                                    <div>
                                        <el-form :model="form" label-width="100px" size="small">
                                            <el-form-item label="发件人模版" required>
                                                <el-select v-model="form.sender_id" placeholder="请选择">
                                                    <el-option
                                                        v-for="(item, index) in senderTemplate"
                                                        :key="index"
                                                        :label="item.name"
                                                        :value="item.id"
                                                    >
                                                    </el-option>
                                                </el-select>
                                            </el-form-item>

                                            <el-form-item label="电子面单模版" required>
                                                <el-select v-model="form.template_id" placeholder="请选择">
                                                    <el-option
                                                        v-for="(item, index) in faceSheetTemplate"
                                                        :key="index"
                                                        :label="item.name"
                                                        :value="item.id"
                                                    >
                                                    </el-option>
                                                </el-select>
                                            </el-form-item>
                                        </el-form>
                                    </div>
                                </div>
                            </ls-dialog>
                        </div>
                    </template>
                </el-table-column>
            </el-table>

            <div class="m-t-24 flex row-right">
                <ls-pagination v-model="pager" @change="getBatchLists()"></ls-pagination>
            </div>
        </div>

        <el-dialog
            title=" "
            top="40vh"
            :close-on-press-escape="false"
            :close-on-click-modal="false"
            :show-close="false"
            :visible.sync="dialogTableVisible"
        >
            <div style="padding: 30px 0">
                <div>
                    <el-progress :stroke-width="10" :percentage="progress" :format="format"></el-progress>
                </div>
                <div class="flex row-center m-t-30 normal md">正在打印中，请勿操作</div>
            </div>
        </el-dialog>
    </div>
</template>

<script lang="ts">
// 订单头部筛选未完成
import { Component, Vue } from 'vue-property-decorator'
import LsPagination from '@/components/ls-pagination.vue'
import LsDialog from '@/components/ls-dialog.vue'
import { RequestPaging } from '@/utils/util'
import DatePicker from '@/components/date-picker.vue'
import { apiOrderLists } from '@/api/order/order'
import { apiFaceSheetTemplateLists, apiFaceSheetSenderLists, apiFaceSheetPrint } from '@/api/application/express'
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

    progress = 0 //进度条
    index = 0 //打印到第几份

    dialogTableVisible = false

    apiOrderLists = apiOrderLists

    selectIds: any = []

    senderTemplate: Array<object> = []
    faceSheetTemplate: Array<object> = []

    form = {
        template_id: '',
        sender_id: ''
    }

    SearchData = {
        order_sn: '',
        user_info: '',
        goods_name: '',
        contact_info: '',
        start_time: '',
        end_time: '',
        order_status: 1
    }

    pager = new RequestPaging()

    /** E Data **/

    /** S Method **/

    format(progress: number) {
        return this.index + '/' + this.selectIds.length
    }

    // 打印以及批量打印
    prints(arr: any) {
        if (Array.isArray(arr) == true) {
            this.selectIds = arr
            arr = this.selectIds
        } else {
            arr = this.selectIds
        }
        if (arr.length <= 0) {
            return this.$message.error('请选择打印的订单')
        }
        this.dialogTableVisible = true
        const index = this.index

        // 递归出口
        if (arr[index] == undefined) {
            this.getBatchLists()
            this.clearStatus()
            return
        }

        apiFaceSheetPrint({
            order_ids: [arr[index]],
            ...this.form
        })
            .then(res => {
                if (res.code != 1) {
                    this.clearStatus()
                    return
                }
                setTimeout(() => {
                    this.index += 1
                    const res = 100 / this.selectIds.length
                    this.progress = res * this.index
                    this.prints(arr) //递归此函数
                }, 200)
            })
            .catch(err => {
                this.clearStatus()
            })
    }

    // 清空状态
    clearStatus() {
        this.dialogTableVisible = false
        this.index = 0
        this.selectIds = []
        ;(this.$refs.paneTable as any).clearSelection()
        this.progress = 0
    }

    // 选择某条数据
    selectionChange(val: any[]) {
        this.selectIds = val.map(item => item.id)
    }

    // 全选
    selectAll() {
        ;(this.$refs.paneTable as any).toggleAllSelection()
    }

    // 电子面单模版
    async getFaceSheetTemplateFunc() {
        const res = await apiFaceSheetTemplateLists({})
        this.faceSheetTemplate = res.lists
    }

    // 发件人模版
    async getSenderTemplateFunc() {
        const res = await apiFaceSheetSenderLists({})
        this.senderTemplate = res.lists
    }

    // 获取数据
    getBatchLists(page?: number): void {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apiOrderLists,
            params: {
                ...this.SearchData,
                delivery_type: 1
            }
        })
    }

    // 重置搜索记录
    resetSearchData() {
        Object.keys(this.SearchData).map(key => {
            this.$set(this.SearchData, key, '')
        })
        this.getBatchLists()
    }

    /** E Method **/

    created() {
        this.getBatchLists()
        this.getSenderTemplateFunc()
        this.getFaceSheetTemplateFunc()
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
    .goods {
        height: 65px;
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
