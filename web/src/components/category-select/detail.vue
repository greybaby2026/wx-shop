<template>
    <div class="detail" v-loading="pager.loading">
        <div class="flex m-b-20">
            <div class="m-r-10">分类搜索</div>
            <el-input
                size="small"
                placeholder="请输入分类名称"
                style="width: 220px"
                v-model="name"
                @keyup.enter.native="getList(1)"
            >
                <el-button slot="append" icon="el-icon-search" @click="getList(1)"></el-button>
            </el-input>
        </div>
        <el-table ref="table" :data="pager.lists" style="width: 100%" height="370px" size="mini">
            <el-table-column label="分类名称" prop="name"> </el-table-column>
            <el-table-column label="商品数量" prop="num"> </el-table-column>
            <el-table-column label="分类级别">
                <template slot-scope="{ row }">
                    <div v-if="row.level == 1">一级分类</div>
                    <div v-if="row.level == 2">二级分类</div>
                    <div v-if="row.level == 3">三级分类</div>
                </template>
            </el-table-column>
            <el-table-column label="选择" width="100">
                <template slot-scope="{ row }">
                    <el-checkbox :value="selectData.id == row.id" @change="handleSelect($event, row)"></el-checkbox>
                </template>
            </el-table-column>
        </el-table>
        <div class="flex row-right m-t-20">
            <ls-pagination v-model="pager" @change="getList()" />
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Inject, Prop, Vue, Watch } from 'vue-property-decorator'
import LsPagination from '@/components/ls-pagination.vue'
import { RequestPaging } from '@/utils/util'
import { apiCategoryCommonLists } from '@/api/goods'
@Component({
    components: {
        LsPagination
    }
})
export default class Detail extends Vue {
    @Inject('visible') visible!: any
    $refs!: { table: any }
    @Prop() value!: any
    @Prop() level!: number
    name = ''
    pager = new RequestPaging()
    get selectData() {
        return this.value
    }
    set selectData(val) {
        this.$emit('input', val)
    }
    @Watch('visible', { deep: true, immediate: true })
    visibleChange(val: any) {
        if (val.val) {
            this.getList()
        }
    }
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager
            .request({
                callback: apiCategoryCommonLists,
                params: {
                    name: this.name,
                    level: this.level
                }
            })
            .then((res: any) => {})
    }
    handleSelect($event: any, item: any) {
        if (!$event) {
            this.selectData = {}
            return
        }
        this.selectData = {
            name: item.name,
            id: item.id
        }
    }
}
</script>

<style scoped lang="scss"></style>
