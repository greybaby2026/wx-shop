<template>
    <div class="ls-order">
        <div class="ls-order__top ls-card">
            <!-- 顶部提示信息 -->
            <el-alert
                title="温馨提示：1.订单状态有待付款;待发货;待收货;已完成;已关闭；2.待付款订单取消后则为已关闭;待付款订单支付后则为待发货;待发货订单发货后则为待收货;待收货订单收货后则为已完成。"
                type="info"
                show-icon
                :closable="false"
            ></el-alert>

            <!-- 头部信息筛选 -->
            <div class="ls-top__search m-t-16">
                <el-form ref="form" inline :model="form" label-width="80px" size="small">
                    <el-form-item label="订单信息">
                        <el-input style="width: 280px" placeholder="请输入订单编号" v-model="form.order_sn"></el-input>
                    </el-form-item>
                    <el-form-item label="用户信息">
                        <el-input style="width: 280px" placeholder="请输入用户昵称" v-model="form.user_info"></el-input>
                    </el-form-item>
                    <el-form-item label="商品名称">
                        <el-input
                            style="width: 280px"
                            placeholder="请输入商品名称"
                            v-model="form.goods_name"
                        ></el-input>
                    </el-form-item>
                    <el-form-item label="收货人信息">
                        <el-input
                            style="width: 280px"
                            placeholder="请输入收货人姓名"
                            v-model="form.contact_info"
                        ></el-input>
                    </el-form-item>
                    <el-form-item label="订单来源">
                        <el-select v-model="form.order_terminal" placeholder="全部">
                            <el-option label="全部" value></el-option>
                            <el-option
                                v-for="(item, index) in otherLists.order_terminal_lists"
                                :key="index"
                                :label="item"
                                :value="index"
                            ></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="订单类型">
                        <el-select v-model="form.order_type_admin" placeholder="全部">
                            <el-option label="全部" value></el-option>
                            <el-option
                                v-for="(item, index) in otherLists.order_type_lists"
                                :key="index"
                                :label="item"
                                :value="index"
                            ></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="支付方式">
                        <el-select v-model="form.pay_way" placeholder="全部">
                            <el-option label="全部" value></el-option>
                            <el-option
                                v-for="(item, index) in otherLists.pay_way_lists"
                                :key="index"
                                :label="item"
                                :value="index"
                            ></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="支付状态">
                        <el-select v-model="form.pay_status" placeholder="全部">
                            <el-option label="全部" value></el-option>
                            <el-option
                                v-for="(item, index) in otherLists.pay_status_lists"
                                :key="index"
                                :label="item"
                                :value="index"
                            ></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="配送方式">
                        <el-select v-model="form.delivery_type" placeholder="全部">
                            <el-option label="全部" value></el-option>
                            <el-option
                                v-for="(item, index) in otherLists.delivery_type_lists"
                                :key="index"
                                :label="item"
                                :value="index"
                            ></el-option>
                        </el-select>
                    </el-form-item>
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

                    <el-form-item label class="m-l-6">
                        <el-button size="small" type="primary" @click="getOrderLists(1)">查询</el-button>
                        <el-button size="small" @click="reset">重置</el-button>

                        <export-data
                            class="m-l-10"
                            :pageSize="pager.size"
                            :method="apiOrderLists"
                            :status="{ order_status: activeStatus }"
                            :param="form"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="ls-order__table ls-card m-t-16">
            <el-tabs v-model="activeName" @tab-click="getOrderLists(1)">
                <el-tab-pane
                    v-for="(item, index) in tabs"
                    :key="index"
                    :label="`${item.label}(${tabCount[item.name]})`"
                    :name="item.name"
                ></el-tab-pane>
            </el-tabs>
            <order-pane v-model="pager.lists" :pager="pager" @refresh="getOrderLists()" />
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import OrderPane from '@/components/order/order-pane.vue'
import DatePicker from '@/components/date-picker.vue'
import { apiOrderLists, apiOtherLists } from '@/api/order/order'
import ExportData from '@/components/export-data/index.vue'
import { RequestPaging } from '@/utils/util'
import { OrderType } from '@/utils/type'
@Component({
    components: {
        OrderPane,
        DatePicker,
        ExportData
    }
})
export default class Order extends Vue {
    // S Data
    activeName: any = 'all_count' //全部;
    activeStatus: any = ''
    apiOrderLists = apiOrderLists

    tabs = [
        {
            label: '全部',
            name: OrderType[0]
        },
        {
            label: '待付款',
            name: OrderType[1]
        },
        {
            label: '待发货',
            name: OrderType[2]
        },
        {
            label: '待收货',
            name: OrderType[3]
        },
        {
            label: '已完成',
            name: OrderType[4]
        },
        {
            label: '已关闭',
            name: OrderType[5]
        }
    ]

    index = 0

    pager = new RequestPaging()

    tabCount = {
        all_count: 0, //全部
        pay_count: 0, //待支付
        delivery_count: 0, //待收货
        receive_count: 0, //待发货
        finish_count: 0, //已完成
        close_count: 0 //已关闭
    }

    form = {
        order_sn: '', //否	string	订单信息
        user_info: '', //否	string	用户信息
        goods_name: '', //否	string	商品名称
        contact_info: '', //否	string	收货人信息
        order_terminal: '', //否	int	订单来源;1-微信小程序;2-微信公众号;3-手机H5;4-PC;5-苹果app;6-安卓app;
        order_type_admin: '', //否	int	订单类型;0-普通订单;1-拼团订单;2-秒杀订单;3-砍价订单
        pay_way: '', //否	int	支付方式:1-余额支付;2-微信支付;3-支付宝支付;
        pay_status: '', //否	int	支付状态;0-待支付;1-已支付;
        delivery_type: '', //否	int	配送方式;1-快递发货;2-上门自提;3-同城配送
        time_type: '', //否	string	时间类型:create_time-下单时间;pay_time-支付时间
        start_time: '', //否	string	开始时间
        end_time: '' //否	string	结束时间
    }

    otherLists: any = {
        order_terminal_lists: [], //来源
        order_type_lists: [], //订单类型
        pay_way_lists: [], //余额支付
        pay_status_lists: [], //支付状态
        delivery_type_lists: [] //配送方式
    }
    // E Data

    // S Methods

    // 获取订单信息
    getOrderLists(page?: number) {
        page && (this.pager.page = page)
        if (this.form.start_time != '' || this.form.end_time != '') {
            if (this.form.time_type == '') {
                return this.$message.error('选择时间必须选择时间类型!')
            }
        }

        const status: any = this.activeName == 'all_count' ? '' : OrderType[this.activeName]
        ;(this.activeStatus = status == '' ? '' : status - 1),
            this.pager
                .request({
                    callback: apiOrderLists,
                    params: {
                        order_status: status == '' ? '' : status - 1,
                        ...this.form
                    }
                })
                .then(res => {
                    this.tabCount = res.extend
                })
    }

    // 重置搜索
    reset() {
        Object.keys(this.form).map(key => {
            this.$set(this.form, key, '')
        })
        this.getOrderLists()
    }

    // 获取订单其他 方式数据
    getOtherMethodList() {
        apiOtherLists().then((res: any) => {
            this.otherLists = res
        })
    }
    // E Methods

    // S  life cycle

    created() {
        // 获取订单信息
        this.getOrderLists()
        // 获取其他方式数据
        this.getOtherMethodList()
    }
    activated() {
        // 获取订单信息
        this.getOrderLists()
        // 获取其他方式数据
        this.getOtherMethodList()
    }

    // E life cycle
}
</script>

<style lang="scss" scoped>
.ls-order {
    .ls-order__table {
        padding-top: 0;
    }
}
</style>
