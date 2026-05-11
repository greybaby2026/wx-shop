<template>
    <div class="ls-seckill">
        <div class="ls-seckill__top ls-card">
            <el-alert
                title="温馨提示：进行中的预售商品可以修改名称和活动时间。"
                type="info"
                show-icon
                :closable="false"
            />

            <div class="seckill-search m-t-16">
                <el-form ref="form" inline :model="queryObj" label-width="80px" size="small">
                    <el-form-item label="活动名称">
                        <el-input v-model="queryObj.activity" placeholder="请输入活动名称"></el-input>
                    </el-form-item>
                    <el-form-item label="商品信息">
                        <el-input v-model="queryObj.goods" placeholder="请输入商品名称/编号"></el-input>
                    </el-form-item>
                    <el-form-item label="活动时间">
                        <date-picker :start-time.sync="queryObj.start_time" :end-time.sync="queryObj.end_time" />
                    </el-form-item>
                    <el-form-item label="" class="m-l-6">
                        <el-button size="mini" type="primary" @click="getList(1)">查询</el-button>
                        <el-button size="mini" @click="resetQueryObj">重置</el-button>

                        <export-data
                            class="m-l-10"
                            :page-size="pager.size"
                            :method="apiSeckillLists"
                            :param="queryObj"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="ls-seckill__content ls-card m-t-16">
            <el-tabs v-model="activeName" v-loading="pager.loading" @tab-click="getList(1)">
                <el-tab-pane
                    v-for="(item, index) in tabs"
                    :key="index"
                    :label="`${item.label}(${tabCount[item.name]})`"
                    :name="item.name"
                >
                    <presell-pane v-model="pager.lists" :pager="pager" @refresh="getList()" />
                </el-tab-pane>
            </el-tabs>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import PresellPane from '@/components/marketing/presell-pane.vue'
import { RequestPaging } from '@/utils/util'
import { apiPresellLists } from '@/api/marketing/presell'
import DatePicker from '@/components/date-picker.vue'
import ExportData from '@/components/export-data/index.vue'
import { PreSellType } from '@/utils/type'

@Component({
    components: {
        DatePicker,
        ExportData,
        PresellPane
    }
})
export default class SeckillLists extends Vue {
    tabs = [
        {
            label: '全部',
            name: PreSellType[0]
        },
        {
            label: '未开始',
            name: PreSellType[1]
        },
        {
            label: '进行中',
            name: PreSellType[2]
        },
        {
            label: '已结束',
            name: PreSellType[3]
        }
    ]

    queryObj = {
        activity: '',
        end_time: '',
        start_time: '',
        goods: ''
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
    apiSeckillLists = apiPresellLists
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager
            .request({
                callback: apiPresellLists,
                params: {
                    status: PreSellType[this.activeName] == '0' ? '' : Number(PreSellType[this.activeName]) - 1,
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
.ls-seckill {
    &__top {
        padding-bottom: 6px;
    }
    .seckill-search {
        .ls-input-price {
            width: 180px;
        }
    }
    .ls-seckill__content {
        padding-top: 0;
    }
}
</style>
