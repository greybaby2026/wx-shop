<template>
    <div class="ls-after-sales">
        <div class="ls-after-sales__top ls-card">
            <!-- <el-alert title="温馨提示：1.卖家"
                type="info" show-icon>
            </el-alert> -->
            <div class="ls-top__search m-t-16">
                <el-form ref="form" inline :model="form" label-width="80px" size="small">
                    <el-form-item label="售后单号">
                        <el-input style="width: 230px" placeholder="请输入售后单号" v-model="form.after_sale_sn">
                            <!-- <template slot="prepend">
                                <div>
                                    <el-select style="width: 120px" v-model="form.name" placeholder="请选择">
                                        <el-option label="" value="shanghai"></el-option>
                                        <el-option label="区域二" value="beijing"></el-option>
                                    </el-select>
                                </div>
                            </template> -->
                        </el-input>
                    </el-form-item>
                    <!-- 用户信息输入 -->
                    <el-form-item label="用户信息">
                        <el-input
                            style="width: 230px"
                            placeholder="请输入手机号/用户昵称/用户编号"
                            v-model="form.user_info"
                        ></el-input>
                    </el-form-item>
                    <!-- 商品名称 -->
                    <el-form-item label="商品名称">
                        <el-input
                            style="width: 230px"
                            placeholder="请输入商品名称/商品编码"
                            v-model="form.goods_info"
                        ></el-input>
                    </el-form-item>
                    <!-- 快递单号 -->
                    <el-form-item label="快递单号">
                        <el-input style="width: 230px" placeholder="请输入内容" v-model="form.invoice_no"></el-input>
                    </el-form-item>
                    <!-- 售后类型 -->
                    <el-form-item label="售后类型">
                        <el-select v-model="form.refund_type" placeholder="全部">
                            <el-option label="全部" value=""></el-option>
                            <el-option label="整单退款" value="1"></el-option>
                            <el-option label="商品售后" value="2"></el-option>
                        </el-select>
                    </el-form-item>
                    <!-- 售后方式 -->
                    <el-form-item label="售后方式">
                        <el-select v-model="form.refund_method" placeholder="请选择商品分类">
                            <el-option label="全部" value=""></el-option>
                            <el-option label="仅退款" value="1"></el-option>
                            <el-option label="退货退款" value="2"></el-option>
                        </el-select>
                    </el-form-item>
                    <!-- 申请时间 -->
                    <el-form-item label="申请时间">
                        <date-picker :start-time.sync="form.start_time" :end-time.sync="form.end_time" />
                    </el-form-item>
                    <el-form-item label="" class="m-l-6">
                        <el-button size="small" type="primary" @click="getAfterSaleLists(1)">查询</el-button>
                        <el-button size="small" @click="reset">重置</el-button>

                        <export-data
                            class="m-l-10"
                            :pageSize="pager.size"
                            :method="apiAfterSaleLists"
                            :status="{ status: activeStatus }"
                            :param="form"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <div class="ls-after-sales__table ls-card m-t-16">
            <el-tabs v-model="activeName" v-loading="pager.loading" @tab-click="getAfterSaleLists(1)">
                <el-tab-pane
                    v-for="(item, index) in tabs"
                    :key="index"
                    :label="`${item.label}(${tabCount[item.name]})`"
                    :name="item.name"
                >
                    <after-sales-pane v-model="pager.lists" :pager="pager" @refresh="getAfterSaleLists()" />
                </el-tab-pane>
            </el-tabs>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiAfterSaleLists } from '@/api/order/order'
import { RequestPaging } from '@/utils/util'
import DatePicker from '@/components/date-picker.vue'
import { AfterSaleType } from '@/utils/type'
import ExportData from '@/components/export-data/index.vue'
import AfterSalesPane from '@/components/order/after-sales-pane.vue'
@Component({
    components: {
        AfterSalesPane,
        DatePicker,
        ExportData
    }
})
export default class AfterSales extends Vue {
    // S Data
    activeName: any = 'all'
    activeStatus: any = ''

    apiAfterSaleLists = apiAfterSaleLists

    tabs = [
        {
            label: '全部',
            name: AfterSaleType[0]
        },
        {
            label: '售后中',
            name: AfterSaleType[1]
        },
        {
            label: '售后成功',
            name: AfterSaleType[2]
        },
        {
            label: '售后失败',
            name: AfterSaleType[3]
        }
    ]

    tabCount = {
        all: 0, //全部
        ing: 0, //售后中
        success: 0, //售后成功
        fail: 0 //售后拒绝
    }

    form: any = {
        after_sale_sn: '', //否	string	售后单号
        order_sn: '', //否	string	订单号
        user_info: '', //否	string	用户编码
        goods_info: '', //否	string	商品编码
        refund_type: '', //否	int	售后类型 1-整单退款 2-商品售后
        refund_method: '', //否	int	售后方式 1-仅退款 2-退货退款
        start_time: '', //否	string	申请开始时间 格式：年月日 时：分：秒
        end_time: '', //否	string	申请结束时间 格式：年月日 时：分：秒
        invoice_no: '' //快递单号
    }

    pager = new RequestPaging()

    // E Data

    // S Methods
    // 获取订单信息
    getAfterSaleLists(page?: number): void {
        page && (this.pager.page = page)
        const status: any = this.activeName == 'all' ? '' : AfterSaleType[this.activeName]
        this.activeStatus = status

        this.pager
            .request({
                callback: apiAfterSaleLists,
                params: {
                    status,
                    ...this.form
                }
            })
            .then(res => {
                this.tabCount = res?.extend
            })
    }

    // 重置搜索
    reset() {
        Object.keys(this.form).map(key => {
            this.$set(this.form, key, '')
        })
        this.getAfterSaleLists()
    }

    // E Methods

    // S  life cycle

    created() {
        // 获取售后订单信息
        this.getAfterSaleLists()
        // 获取其他方式数据
        // this.getOtherMethodList();
    }

    // E life cycle
}
</script>
