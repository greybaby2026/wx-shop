<template>
    <div class="detail" v-loading="pager.loading">
        <div class="flex row m-b-10 m-l-10">
            <div class="flex-1">
                <el-checkbox v-model="selectAll">全选</el-checkbox>
            </div>
            <div class="flex">
                <!-- <div class="m-r-10">推广方式</div>
                <el-select class="m-r-10" v-model="get_type">
                    <el-option label="全部" :value="1"></el-option>

                    <el-option label="卖家发放" :value="2"></el-option>
                </el-select> -->

                <div class="m-r-10">优惠券搜索</div>
                <el-input
                    size="small"
                    placeholder="请输入优惠券名称"
                    style="width: 220px"
                    v-model="name"
                    @keyup.enter.native="getList(1)"
                >
                    <el-button slot="append" icon="el-icon-search" @click="getList(1)"></el-button>
                </el-input>
            </div>
        </div>
        <el-table ref="table" :data="pager.lists" style="width: 100%" height="370px" size="mini" row-key="id">
            <el-table-column width="45">
                <template slot-scope="{ row }">
                    <el-checkbox :value="selectItem(row)" @change="handleSelect($event, row)"></el-checkbox>
                </template>
            </el-table-column>

            <el-table-column label="优惠券名称" min-width="180" prop="name"> </el-table-column>

            <el-table-column label="优惠门槛" min-width="180" prop="discount_content"> </el-table-column>
            <el-table-column label="剩余" width="100" prop="surplus_number"> </el-table-column>
            <el-table-column label="优惠券状态" prop="status_desc"> </el-table-column>
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
import { apiCouponCommonLists } from '@/api/marketing/coupon'
@Component({
    components: {
        LsPagination
    }
})
export default class Detail extends Vue {
    @Inject('visible') visible!: any
    $refs!: { table: any }
    @Prop() value!: any
    @Prop() limit!: 10
    name = ''
    pager = new RequestPaging()
    get_type = 1
    @Watch('visible', { deep: true, immediate: true })
    visibleChange(val: any) {
        if (val.val) {
            this.getList()
        }
    }

    get selectData() {
        return this.value
    }
    set selectData(val) {
        this.$emit('input', val)
    }
    get selectItem() {
        return (row: any) => {
            return this.selectData.some((item: any) => item.id == row.id)
        }
    }
    get selectAll() {
        const {
            pager: { lists }
        } = this
        const ids: any[] = this.selectData.map((item: any) => item.id)
        if (!lists.length) {
            return false
        }
        return lists.every(item => ids.includes(item.id))
    }

    set selectAll(val) {
        const {
            pager: { lists }
        } = this
        if (val) {
            for (let i = 0; i < lists.length; i++) {
                const item = lists[i]
                const ids: any[] = this.selectData.map((item: any) => item.id)
                if (!ids.includes(item.id)) {
                    if (this.checkLength()) {
                        return
                    }
                    this.selectData.push(item)
                }
            }
        } else {
            lists.forEach(row => {
                this.setSelectData(row)
            })
        }
    }

    handleSelect($event: boolean, row: any) {
        if ($event) {
            if (this.checkLength()) {
                return
            }
            this.selectData.push(row)
        } else {
            this.setSelectData(row)
        }
    }

    setSelectData(row: any) {
        const index = this.selectData.findIndex((item: any) => item.id == row.id)
        if (index != -1) {
            this.selectData.splice(index, 1)
        }
    }
    checkLength() {
        if (this.selectData.length >= this.limit) {
            this.$message({
                message: `最多选择${this.limit}张优惠券`,
                type: 'warning'
            })
            return true
        }
        return false
    }
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager
            .request({
                callback: apiCouponCommonLists,
                params: {
                    name: this.name,
                    get_type: this.get_type
                }
            })
            .then((res: any) => {})
    }
}
</script>

<style scoped lang="scss"></style>
