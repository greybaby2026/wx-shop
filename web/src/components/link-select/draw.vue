<template>
    <div class="detail" v-loading="pager.loading">
        <div class="flex m-b-20">
            <div class="m-r-10">活动搜索</div>
            <el-input
                size="small"
                placeholder="请输入活动名称"
                style="width: 220px"
                v-model="name"
                @keyup.enter.native="getList(1)"
            >
                <el-button slot="append" icon="el-icon-search" @click="getList(1)"></el-button>
            </el-input>
        </div>
        <el-table ref="table" :data="pager.lists" style="width: 100%" height="370px" size="mini">
            <el-table-column width="45" label="">
                <template slot-scope="scope">
                    <el-checkbox :value="scope.row.id == selectData.id" @change="onSelect($event, scope.row)" />
                </template>
            </el-table-column>
            <el-table-column label="活动名称" prop="name"> </el-table-column>
            <el-table-column label="活动状态">
                <template slot-scope="scope">
                    <el-tag size="medium" type="success" v-if="scope.row.status == 1">{{
                        scope.row.status_desc
                    }}</el-tag>
                    <el-tag size="medium" type="info" v-else>{{ scope.row.status_desc }}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="活动时间" min-width="120">
                <template slot-scope="scope">
                    {{ scope.row.start_time_desc }} ~
                    {{ scope.row.end_time_desc }}
                </template>
            </el-table-column>
        </el-table>
        <div class="flex row-right m-t-20">
            <ls-pagination v-model="pager" @change="getList()" />
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import LsPagination from '@/components/ls-pagination.vue'
import { RequestPaging } from '@/utils/util'
import { apiLuckyDrawLists } from '@/api/marketing/lucky_draw'
@Component({
    components: {
        LsPagination
    }
})
export default class Page extends Vue {
    $refs!: { table: any }
    @Prop() value!: any
    name = ''
    pager = new RequestPaging()

    get selectData() {
        return this.value
    }
    set selectData(val) {
        this.$emit('input', val)
    }

    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager
            .request({
                callback: apiLuckyDrawLists,
                params: {
                    name: this.name
                }
            })
            .then((res: any) => {})
    }
    onSelect($event: any, item: any) {
        if ($event) {
            this.selectData = item
        } else {
            this.selectData = {}
        }
    }
    created() {
        this.getList()
    }
}
</script>

<style scoped lang="scss"></style>
