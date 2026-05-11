<template>
    <div class="admin">
        <div class="ls-card">
            <!-- 头部表单 -->
            <div class="m-t-20">
                <el-form :inline="true" :model="form" label-width="80px" size="small">
                    <!-- 提货码 -->
                    <el-form-item label="自提门店">
                        <el-select v-model="form.shop_id" placeholder="请选择自提门店">
                            <el-option label="全部" value></el-option>
                            <el-option
                                v-for="item in shopList"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id"
                            ></el-option>
                        </el-select>
                    </el-form-item>
                    <!-- 提货码 -->
                    <el-form-item label="提货码">
                        <el-input v-model="form.pickup_code" placeholder="请输入提货码/扫码枪扫描" />
                    </el-form-item>

                    <!-- 核销状态 -->
                    <el-form-item label="核销状态">
                        <el-select v-model="form.verification_status" placeholder="请选择状态">
                            <el-option label="全部" value></el-option>
                            <el-option label="待核销" :value="0"></el-option>
                            <el-option label="已核销" :value="1"></el-option>
                        </el-select>
                    </el-form-item>

                    <!-- 订单信息 -->
                    <el-form-item label="订单信息">
                        <el-input v-model="form.order_sn" placeholder="请输入订单编号" />
                    </el-form-item>

                    <!-- 用户信息 -->
                    <el-form-item label="用户信息">
                        <el-input v-model="form.user_info" placeholder="请输入用户昵称/编号/手机号码" />
                    </el-form-item>

                    <!-- 商品名称 -->
                    <el-form-item label="商品信息">
                        <el-input v-model="form.goods_name" placeholder="请输入商品名称/编号" />
                    </el-form-item>

                    <!-- 收货人信息 -->
                    <el-form-item label="收货人信息">
                        <el-input v-model="form.contact_info" placeholder="请输入收货人姓名/手机号码" />
                    </el-form-item>

                    <!-- 订单类型 -->
                    <el-form-item label="订单类型">
                        <el-select v-model="form.order_type_admin" placeholder="请选择订单类型">
                            <el-option label="全部" value></el-option>
                            <el-option label="普通" :value="0"></el-option>
                            <el-option label="拼团" :value="1"></el-option>
                            <el-option label="秒杀" :value="2"></el-option>
                            <el-option label="砍价" :value="3"></el-option>
                        </el-select>
                    </el-form-item>

                    <!-- 订单状态 -->
                    <el-form-item label="订单状态">
                        <el-select v-model="form.order_status" placeholder="请选择订单状态">
                            <el-option label="全部" value></el-option>
                            <el-option label="待付款" :value="0"></el-option>
                            <el-option label="待发货" :value="1"></el-option>
                            <el-option label="待收货" :value="2"></el-option>
                            <el-option label="已完成" :value="3"></el-option>
                            <el-option label="已关闭" :value="4"></el-option>
                        </el-select>
                    </el-form-item>

                    <!-- 时间 -->
                    <el-form-item label="时间类型">
                        <div class="flex">
                            <el-select style="width: 120px" v-model="form.time_type" placeholder="全部">
                                <el-option label="全部" value></el-option>
                                <el-option label="下单时间" value="create_time"></el-option>
                                <el-option label="支付时间" value="pay_time"></el-option>
                            </el-select>

                            <date-picker :start-time.sync="form.start_time" :end-time.sync="form.end_time" />
                        </div>
                    </el-form-item>

                    <!-- 搜索查询 -->
                    <el-form-item class="m-l-24">
                        <el-button type="primary" @click="search">查询</el-button>
                        <el-button @click="resetSearch">重置</el-button>
                        <!-- <el-button>导出</el-button> -->
                        <export-data
                            class="m-l-10"
                            :method="apiSelffetchVerificationList"
                            :param="form"
                            :pageSize="pager._size"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="ls-card m-t-16">
            <!-- 管理员数据列表 -->
            <div class="m-t-24">
                <el-table :data="pager.lists" v-loading="pager.loading" style="width: 100%" size="mini">
                    <!-- 订单编号 -->
                    <el-table-column prop="sn" label="订单编号" min-width="180" />
                    <!-- 订单类型 -->
                    <el-table-column prop="order_type_desc" label="订单类型" min-width="100" />
                    <!-- 用户 -->
                    <el-table-column prop="nickname" label="用户名称" min-width="180">
                        <div class="flex" slot-scope="scope">
                            <el-image :src="scope.row.avatar" style="width: 40px; height: 40px" fit="fill" />
                            <span class="m-l-10">{{ scope.row.nickname }}</span>
                        </div>
                    </el-table-column>
                    <!-- 商品信息 -->

                    <!-- 商品的图片 -->
                    <el-table-column label="商品图片" min-width="90">
                        <template slot-scope="scope">
                            <div class="m-t-10" v-for="(item, index) in scope.row.order_goods" :key="index">
                                <el-image
                                    v-if="index < 3"
                                    :src="item.goods_image"
                                    style="width: 58px; height: 58px"
                                    class="flex-none"
                                ></el-image>
                            </div>
                            <div
                                class="muted m-t-5 flex"
                                v-if="scope.row.order_goods.length > 3"
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
                                    'margin-bottom': scope.row.order_goods.length > 2 ? '30px' : ''
                                }"
                            >
                                <div
                                    class="goods"
                                    @click="toOrder(scope.row.id)"
                                    v-for="(item, index) in scope.row.order_goods"
                                    :key="index"
                                >
                                    <div class v-if="index < 3">
                                        <div class="flex row-between normal p-r-24 line-1">
                                            <span class="line-1 name">
                                                {{ item.goods_name }}
                                            </span>
                                        </div>
                                        <div class="xs lighter flex line-1 p-r-24">规格：{{ item.spec_value_str }}</div>
                                        <div class="xs muted flex p-r-24 line-1">
                                            价格：
                                            <span class="normal m-r-10">¥{{ item.goods_price }}</span>
                                            数量：
                                            <span class="normal">
                                                {{ item.goods_num }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </el-table-column>

                    <!-- 实付金额 -->
                    <el-table-column prop="order_amount" label="实付金额" min-width="100" />

                    <!-- 收货人 -->
                    <el-table-column prop="contact" label="提货人" min-width="120">
                        <template slot-scope="scope">
                            <el-popover placement="top" width="300" trigger="hover">
                                <div>提货人：{{ scope.row.contact }}</div>
                                <div class="m-t-10">手机号：{{ scope.row.mobile }}</div>
                                <div class="m-t-10">
                                    门店地址：
                                    {{ { ...scope.row.selffetch_shop }.detailed_address }}
                                </div>
                                <el-tag size="medium" slot="reference">
                                    {{ scope.row.contact }}
                                </el-tag>
                            </el-popover>
                        </template>
                    </el-table-column>

                    <!-- 配送方式 -->
                    <!-- <el-table-column
                        prop="delivery_type_desc"
                        label="配送方式"
                        width="180"
                    />-->

                    <!-- 核销状态 -->
                    <el-table-column prop="verification_status_desc" label="核销状态" width="100" />

                    <!-- 订单状态 -->
                    <el-table-column prop="order_status_desc" label="订单状态" width="120" />

                    <!-- 核销员 -->
                    <el-table-column prop="verifier_name" label="核销员" width="120" />

                    <!-- 核销时间 -->
                    <el-table-column prop="verification_time" label="核销时间" width="180" />

                    <el-table-column label="操作" min-width="200" fixed="right">
                        <!-- 操作 -->
                        <template slot-scope="scope">
                            <!-- 详情 -->
                            <el-button
                                type="text"
                                size="small"
                                v-if="scope.row.admin_order_btn.detail_btn"
                                @click="goSelffetchOrderDetail(scope.row.id)"
                                >详情</el-button
                            >

                            <!-- 提货核销 & 核销提示 -->
                            <ls-dialog
                                title="提货核销"
                                class="inline m-l-10"
                                v-if="scope.row.admin_order_btn.verification_btn"
                                :content="`确定核销订单(${scope.row.sn})吗?请谨慎操作`"
                                @confirm="onSelffetchOrderVerification(scope.row.id)"
                            >
                                <el-button type="text" size="small" slot="trigger">提货核销</el-button>
                            </ls-dialog>
                            <ls-dialog
                                ref="verifyDialogRef"
                                title="温馨提示"
                                v-if="scope.row.admin_order_btn.verification_btn"
                                :content="verifyTips"
                                @confirm="onSelffetchOrderVerification(scope.row.id, 1)"
                            />

                            <!-- 提货查询 -->
                            <ls-dialog
                                class="m-l-10 inline"
                                v-if="scope.row.admin_order_btn.verification_query_btn"
                                title="提货查询"
                                :cancel-button-text="false"
                                width="580px"
                                @open="onSelffetchOrderQuery(scope.row.id)"
                            >
                                <div slot="default">
                                    <div>
                                        <div class="normal">提货人信息</div>
                                        <el-form :model="tempOrderInfo" label-width="120px" size="small" :inline="true">
                                            <el-form-item label="提货人:">{{ tempOrderInfo.contact }}</el-form-item>
                                            <el-form-item label="联系方式:">{{ tempOrderInfo.mobile }}</el-form-item>
                                        </el-form>
                                    </div>
                                    <div>
                                        <div class="normal">核销信息</div>
                                        <el-form :model="tempOrderInfo" label-width="120px" size="small" :inline="true">
                                            <el-form-item label="自提门店:">{{ tempOrderInfo.shop_name }}</el-form-item>
                                            <el-form-item label="核销员编号:">{{
                                                tempOrderInfo.verifier_sn
                                            }}</el-form-item>
                                            <el-form-item label="核销员:">
                                                {{ tempOrderInfo.verifier_name }}
                                            </el-form-item>
                                            <el-form-item label="核销时间:">
                                                {{ tempOrderInfo.verification_time }}
                                            </el-form-item>
                                        </el-form>
                                    </div>
                                    <div>
                                        <div class="normal">商品信息</div>
                                        <div
                                            class="flex m-t-10 goods-item"
                                            v-for="(item, index) in tempOrderInfo.order_goods"
                                            :key="index"
                                        >
                                            <el-image
                                                :src="item.goods_image"
                                                style="width: 58px; height: 58px"
                                                class="flex-none"
                                            />
                                            <div class="m-l-24">
                                                <div class="flex row-between normal p-r-24">
                                                    <span class="name">
                                                        {{ item.goods_name }}
                                                    </span>
                                                </div>
                                                <div class="xs lighter flex line-1 p-r-24">
                                                    规格：{{ item.spec_value_str }}
                                                </div>
                                                <div class="xs muted flex p-r-24 line-1">
                                                    价格：
                                                    <span class="normal m-r-10"> ¥{{ item.goods_price }} </span>
                                                    数量：
                                                    <span class="normal">
                                                        {{ item.goods_num }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <el-button type="text" size="small" slot="trigger">提货查询</el-button>
                            </ls-dialog>
                        </template>
                    </el-table-column>
                </el-table>

                <!-- 分页 -->
                <div class="m-t-24 pagination">
                    <ls-pagination v-model="pager" @change="getSelffetchVerifierList" />
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import {
    apiSelffetchVerificationList,
    apiSelffetchVerificationQuery,
    apiSelffetchVerification,
    apiSelffetchShopList
} from '@/api/application/selffetch'
import { PageMode } from '@/utils/type'
import { RequestPaging } from '@/utils/util'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import DatePicker from '@/components/date-picker.vue'
import ExportData from '@/components/export-data/index.vue'

@Component({
    components: {
        LsDialog,
        DatePicker,
        LsPagination,
        ExportData
    }
})
export default class SelffetchVerification extends Vue {
    $refs!: { paneTable: any; verifyDialogRef: any }
    /** S Data **/
    // 表单数据
    form = {
        shop_id: '', //自提门店
        pickup_code: '', // 提货码
        verification_status: '', // 核销状态: 0-待核销; 1-已核销;
        order_sn: '', // 订单信息
        user_info: '', // 用户信息
        goods_name: '', // 商品名称
        contact_info: '', // 收货人信息
        time_type: '', // 时间类型: create_time-下单时间; pay_time-支付时间
        start_time: '', // 开始时间
        end_time: '', // 结束时间
        order_type_admin: '', // 订单类型
        order_status: '' // 订单状态
    }
    pager: RequestPaging = new RequestPaging()
    tempOrderInfo = {} // 暂存订单信息：用于订单查询
    apiSelffetchVerificationList = apiSelffetchVerificationList
    shopList: any[] = []
    verifyTips = ''
    /** E Data **/

    /** S Methods **/
    // 搜索
    search() {
        this.pager.page = 1
        if ((this.form.start_time || this.form.end_time) && !this.form.time_type) {
            return this.$message.error('选择时间必须选择时间类型!')
        }
        this.getSelffetchVerifierList()
    }

    // 重置搜索
    resetSearch() {
        Object.keys(this.form).map(key => {
            this.$set(this.form, key, '')
        })
        this.getSelffetchVerifierList()
    }

    // 获取列表数据
    getSelffetchVerifierList() {
        // 请求管理员列表
        this.pager
            .request({
                callback: apiSelffetchVerificationList,
                params: this.form
            })
            .catch(() => {
                this.$message.error('数据请求失败，刷新重载!')
            })
    }

    // 订单核销
    onSelffetchOrderVerification(id: number, isConfirm = 0) {
        apiSelffetchVerification({
            id,
            confirm: isConfirm
        }).then(res => {
            if (res.code === 10) {
                this.verifyTips = res.msg
                this.$nextTick(() => {
                    this.$refs.verifyDialogRef?.open()
                })
                return
            }
            this.getSelffetchVerifierList()
        })
    }

    // 去订单详情
    goSelffetchOrderDetail(id: number) {
        this.$router.push({
            path: '/order/order_detail',
            query: { id: id + '' }
        })
    }

    // 订单查询
    onSelffetchOrderQuery(id: number) {
        apiSelffetchVerificationQuery({
            id
        }).then(data => {
            this.tempOrderInfo = data
        })
    }
    //
    getShopLists() {
        apiSelffetchShopList({
            page_type: 0
        }).then(res => {
            this.shopList = res?.lists
        })
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.getSelffetchVerifierList()
        this.getShopLists()
    }

    /** E Life Cycle **/
}
</script>
s

<style lang="scss" scoped>
.pagination {
    padding-right: 5%;
    display: flex;
    justify-content: flex-end;
}

.goods-item {
    padding: 20px;
    border-radius: 7px;
    border: $--border-base;
}
</style>
