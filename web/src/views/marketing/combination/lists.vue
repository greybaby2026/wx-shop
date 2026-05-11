<template>
    <div class="ls-team">
        <div class="ls-team__top ls-card">
            <el-alert
                title="温馨提示：1.进行中的拼团商品可以修改名称和活动时间。"
                type="info"
                show-icon
                :closable="false"
            />

            <div class="team-search m-t-16">
                <el-form ref="form" inline :model="queryObj" label-width="100px" size="small">
                    <el-form-item label="活动名称">
                        <el-input
                            style="width: 180px"
                            v-model="queryObj.activity"
                            placeholder="请输入活动名称"
                        ></el-input>
                    </el-form-item>
                    <el-form-item label="商品信息">
                        <el-input style="width: 180px" v-model="queryObj.goods" placeholder="请输入商品信息"></el-input>
                    </el-form-item>
                    <el-form-item label="活动时间">
                        <date-picker :start-time.sync="queryObj.start_time" :end-time.sync="queryObj.end_time" />
                    </el-form-item>
                    <el-form-item label="" class="m-l-6">
                        <el-button size="mini" type="primary" @click="getList(1)">查询</el-button>
                        <el-button size="mini" @click="resetQueryObj">重置</el-button>

                        <export-data class="m-l-10" :method="apiTeamLists" :param="queryObj" :pageSize="pager._size" />
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="ls-team__content ls-card m-t-16">
            <el-tabs v-model="activeName" v-loading="pager.loading" @tab-click="getList(1)">
                <el-tab-pane
                    v-for="(item, index) in tabs"
                    :key="index"
                    :label="`${item.label}(${tabCount[item.name]})`"
                    :name="item.name"
                >
                    <team-pane v-model="pager.lists" :pager="pager" @refresh="getList()" />
                </el-tab-pane>
            </el-tabs>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import teamPane from '@/components/marketing/team/team-pane.vue'
import { RequestPaging } from '@/utils/util'
import { apiTeamLists } from '@/api/marketing/combination'
import DatePicker from '@/components/date-picker.vue'
import ExportData from '@/components/export-data/index.vue'
import { teamType } from '@/utils/type'

@Component({
    components: {
        teamPane,
        DatePicker,
        ExportData
    }
})
export default class team extends Vue {
    apiTeamLists = apiTeamLists

    tabs = [
        {
            label: '全部',
            name: teamType[0]
        },
        {
            label: '未开始',
            name: teamType[1]
        },
        {
            label: '进行中',
            name: teamType[2]
        },
        {
            label: '已结束',
            name: teamType[3]
        }
    ]

    queryObj = {
        activity: '',
        status: '',
        goods: '',
        end_time: '',
        start_time: ''
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
        this.handleTabsChange()

        this.pager
            .request({
                callback: apiTeamLists,
                params: this.queryObj
            })
            .then(res => {
                this.tabCount = res?.extend
            })
    }

    handleTabsChange() {
        const active = this.activeName
        const TeamType = teamType as any
        const status = TeamType[active] === TeamType.all ? '' : TeamType[active]
        this.$set(this.queryObj, 'status', status)
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
.ls-team {
    &__top {
        padding-bottom: 6px;
    }
    .team-search {
        .ls-input-price {
            width: 180px;
        }
    }
    .ls-team__content {
        padding-top: 0;
    }
}
</style>
