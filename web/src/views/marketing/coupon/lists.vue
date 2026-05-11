<template>
    <div class="ls-coupon">
        <div class="ls-coupon__top ls-card">
            <el-alert
                title="温馨提示：1.优惠券在发放时间内只要未关闭未删除符合条件就能领取；2.优惠券已关闭不能继续领取已发放的优惠券在用券时间内能继续使用；3.优惠券已删除不能继续领取已发放的优惠券不能继续使用。"
                type="info"
                show-icon
                :closable="false"
            />

            <div class="coupon-search m-t-16">
                <el-form ref="form" inline :model="queryObj" label-width="100px" size="small">
                    <el-form-item label="优惠券名称">
                        <el-input
                            style="width: 180px"
                            v-model="queryObj.name"
                            placeholder="请输入优惠券名称"
                        ></el-input>
                    </el-form-item>
                    <el-form-item label="推广方式">
                        <el-select v-model="queryObj.get_type" style="width: 160px" placeholder="请选择推广方式">
                            <el-option
                                v-for="item in options"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value"
                            >
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="创建时间">
                        <date-picker :start-time.sync="queryObj.start_time" :end-time.sync="queryObj.end_time" />
                    </el-form-item>
                    <el-form-item label="" class="m-l-6">
                        <el-button size="mini" type="primary" @click="getList(1)">查询</el-button>
                        <el-button size="mini" @click="resetQueryObj">重置</el-button>

                        <export-data
                            class="m-l-10"
                            :pageSize="pager.size"
                            :method="apiCouponLists"
                            :param="queryObj"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="ls-coupon__content ls-card m-t-16">
            <el-tabs v-model="activeName" v-loading="pager.loading" @tab-click="getList(1)">
                <el-tab-pane
                    v-for="(item, index) in tabs"
                    :key="index"
                    :label="`${item.label}(${tabCount[item.name]})`"
                    :name="item.name"
                >
                    <coupon-pane v-model="pager.lists" :pager="pager" @refresh="getList()" />
                </el-tab-pane>
            </el-tabs>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import CouponPane from '@/components/marketing/coupon-pane.vue'
import { RequestPaging } from '@/utils/util'
import { apiCouponLists } from '@/api/marketing/coupon'
import DatePicker from '@/components/date-picker.vue'
import ExportData from '@/components/export-data/index.vue'
import { CouponType } from '@/utils/type'

@Component({
    components: {
        CouponPane,
        DatePicker,
        ExportData
    }
})
export default class Coupon extends Vue {
    apiCouponLists = apiCouponLists

    tabs = [
        {
            label: '全部',
            name: CouponType[0]
        },
        {
            label: '未开始',
            name: CouponType[1]
        },
        {
            label: '进行中',
            name: CouponType[2]
        },
        {
            label: '已结束',
            name: CouponType[3]
        }
    ]

    options = [
        { value: '', label: '全部' },
        { value: '1', label: '买家领取' },
        { value: '2', label: '卖家发放' }
    ]

    queryObj = {
        name: '',
        end_time: '',
        start_time: '',
        get_type: ''
    }
    lists = []
    tabCount = {
        all: 0, //全部
        not: 0, //未开始
        conduct: 0, //进行中
        end: 0 //已结束
    }
    pager = new RequestPaging()
    activeName: any = 'all'

    getList(page?: number): void {
        page && (this.pager.page = page)
        const status = CouponType[this.activeName] == '0' ? '' : CouponType[this.activeName]

        this.pager
            .request({
                callback: apiCouponLists,
                params: {
                    status: status,
                    ...this.queryObj
                }
            })
            .then(res => {
                this.tabCount = res?.extend
            })
    }
    resetQueryObj() {
        Object.keys(this.queryObj).map(key => {
            this.$set(this.queryObj, key, '')
        })
        this.getList()
    }
    created() {
        this.getList()
    }
}
</script>

<style lang="scss" scoped>
.ls-coupon {
    &__top {
        padding-bottom: 6px;
    }
    .coupon-search {
        .ls-input-price {
            width: 180px;
        }
    }
    .ls-coupon__content {
        padding-top: 0;
    }
}
</style>
