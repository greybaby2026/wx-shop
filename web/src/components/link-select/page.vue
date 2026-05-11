<template>
    <div class="detail" v-loading="pager.loading">
        <div class="flex m-b-20">
            <div class="m-r-10">页面搜索</div>
            <el-input
                size="small"
                placeholder="请输入页面名称"
                style="width: 220px"
                v-model="name"
                @keyup.enter.native="getList(1)"
            >
                <el-button slot="append" icon="el-icon-search" @click="getList(1)"></el-button>
            </el-input>
        </div>
        <el-table ref="table" :data="pager.lists" style="width: 100%" height="370px" size="mini">
            <el-table-column width="45">
                <template slot-scope="scope">
                    <el-checkbox
                        :value="selectData.id == scope.row.id"
                        @change="onSelect($event, scope.row)"
                    ></el-checkbox>
                </template>
            </el-table-column>
            <el-table-column prop="name" label="页面名称"> </el-table-column>
            <el-table-column prop="create_time" label="创建时间"> </el-table-column>
        </el-table>
        <div class="flex row-right m-t-10">
            <ls-pagination v-model="pager" @change="getList()" />
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import LsPagination from '@/components/ls-pagination.vue'
import { RequestPaging } from '@/utils/util'
import { apiThemePageLists } from '@/api/shop'
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
                callback: apiThemePageLists,
                params: {
                    type: 1,
                    name: this.name
                }
            })
            .then((res: any) => {})
    }
    onSelect($event: any, item: any) {
        if (!$event) {
            this.selectData = {}
            return
        }
        this.selectData = item
    }
    created() {
        this.getList()
    }
}
</script>

<style scoped lang="scss"></style>
