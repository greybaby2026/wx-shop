<template>
    <div class="ls-details">
        <div class="ls-card ls-detail__header">
            <el-page-header @back="$router.go(-1)" content="查看采集详情"></el-page-header>
        </div>
        <div class="ls-details__table ls-card m-t-16">
            <el-tabs v-model="activeName" v-loading="pager.loading" @tab-click="getLists(1)">
                <el-tab-pane
                    v-for="(item, index) in tabs"
                    :key="index"
                    :label="`${item.label}(${tabCount[item.status]})`"
                    :name="item.status"
                >
                    <details-pane v-model="pager.lists" :pager="pager" :tabStatus="index" @refresh="getLists()" />
                </el-tab-pane>
            </el-tabs>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiGoodsGatherLists } from '@/api/application/goods_collection'
import { RequestPaging } from '@/utils/util'
import { GatherDetailMode } from '@/utils/type'
import DetailsPane from './components/details-pane.vue'
@Component({
    components: {
        DetailsPane
    }
})
export default class GatherDetails extends Vue {
    activeName: any = 'wait'
    // 采集记录ID
    id!: any
    tabs = [
        {
            label: '待处理',
            status: GatherDetailMode[0]
        },
        {
            label: '已处理',
            status: GatherDetailMode[1]
        }
    ]

    tabCount = {
        wait: 0, //待处理
        already: 0 //已处理
    }

    pager = new RequestPaging()

    // 获取列表信息
    getLists(page?: number): void {
        page && (this.pager.page = page)
        const status: any = this.activeName == 'wait' ? 0 : GatherDetailMode[this.activeName]
        this.pager
            .request({
                callback: apiGoodsGatherLists,
                params: {
                    log_id: this.id,
                    status
                }
            })
            .then(res => {
                this.tabCount = res?.extend
            })
    }

    created() {
        this.id = this.$route.query.id
        this.getLists()
    }
}
</script>
