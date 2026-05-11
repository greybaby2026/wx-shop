<template>
    <div class="detail" v-loading="pager.loading">
        <div class="flex row m-b-10">
            <div class="flex">
                <div class="m-r-10">商品搜索</div>
                <el-input
                    size="small"
                    placeholder="请输入商品名称"
                    style="width: 180px"
                    v-model="searchData.name"
                    @keyup.enter.native="getList(1)"
                >
                </el-input>

                <div class="m-l-10">参与分销</div>
                <el-select v-model="searchData.is_distribution" style="width: 180px" class="m-l-10">
                    <el-option label="全部"></el-option>
                    <el-option label="参与" :value="1"></el-option>
                    <el-option label="不参与" :value="0"></el-option>
                </el-select>

                <div class="m-l-10">会员折扣</div>
                <el-select v-model="searchData.is_discount" style="width: 180px" class="m-l-10">
                    <el-option label="全部"></el-option>
                    <el-option label="参与" :value="1"></el-option>
                    <el-option label="不参与" :value="0"></el-option>
                </el-select>
                <div class="m-l-10">
                    <el-button type="primary" icon="el-icon-search" @click="getList(1)"></el-button>
                </div>
            </div>
        </div>
        <div class="m-b-10">
            <el-checkbox v-if="type == 'multiple'" v-model="selectAll">全选</el-checkbox>
        </div>
        <el-table ef="table" :data="pager.lists" style="width: 100%" height="370px" size="mini" row-key="id">
            <!-- <el-table-column fixed="left" type="selection" width="55"> </el-table-column> -->
            <el-table-column width="45" v-if="type == 'single'">
                <template slot-scope="{ row }">
                    <el-checkbox :value="selectItem(row)" @change="handleSelect($event, row)"></el-checkbox>
                </template>
            </el-table-column>
            <el-table-column v-if="type == 'multiple'" width="45">
                <template slot-scope="{ row }">
                    <el-checkbox :value="selectItem(row)" @change="handleSelect($event, row)"></el-checkbox>
                </template>
            </el-table-column>

            <el-table-column label="商品信息" min-width="180">
                <template slot-scope="scope">
                    <div class="flex">
                        <el-image
                            class="flex-none"
                            style="width: 48px; height: 48px"
                            :src="scope.row.image"
                            fit="cover"
                        />
                        <div class="goods-info m-l-8">
                            <div class="line-2">{{ scope.row.name }}</div>
                            <div>
                                <el-tag v-if="scope.row.spec_type == 2" size="mini">多规格</el-tag>
                            </div>
                        </div>
                    </div>
                </template>
            </el-table-column>

            <el-table-column prop="price" label="价格" width="120"> </el-table-column>
            <el-table-column prop="total_stock" label="库存" width="120"> </el-table-column>
            <el-table-column prop="is_distribution" label="参与分销" width="120">
                <template slot-scope="scope">
                    <div>{{ scope.row.is_distribution ? '参与' : '不参与' }}</div>
                </template>
            </el-table-column>
            <el-table-column prop="is_discount" label="会员折扣" width="120">
                <template slot-scope="scope">
                    <div>{{ scope.row.is_discount ? '参与' : '不参与' }}</div>
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
import { RequestPaging, throttle } from '@/utils/util'
import { apiGoodsCommonLists } from '@/api/goods'
@Component({
    components: {
        LsPagination
    }
})
export default class Detail extends Vue {
    @Inject('visible') visible!: any
    $refs!: { table: any }
    @Prop() value!: any
    @Prop() goods!: any
    @Prop({ default: 'single' }) type!: 'multiple' | 'single'
    @Prop() limit!: number
    @Prop({ default: false }) isSpec!: boolean
    @Prop({ default: () => {} }) params!: Record<any, any>
    // 是否展示虚拟商品
    @Prop({ default: false }) showVirtualGoods?: boolean
    searchData = {
        name: '',
        is_distribution: '',
        is_discount: ''
    }
    pager = new RequestPaging()
    selectedObj: any = {}

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
            if (this.type == 'single') {
                return this.selectData.id == row.id
            }
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
    getList(page?: number): void {
        // showVirtualGoods为true时，type=1，此时展示虚拟商品
        let type = undefined
        if (this.showVirtualGoods) {
            type = 0
        }

        page && (this.pager.page = page)
        this.pager
            .request({
                callback: apiGoodsCommonLists,
                params: {
                    ...this.searchData,
                    is_spec: this.isSpec,
                    ...this.params,
                    type: type // 为了显示出虚拟商品。
                }
            })
            .then((res: any) => {})
    }
    handleSelect($event: boolean, row: any) {
        console.log(row)

        if (this.type == 'single') {
            if ($event) {
                this.selectData = row
            } else {
                this.selectData = {}
            }
        } else if ($event) {
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
                message: `最多选择${this.limit}件商品`,
                type: 'warning'
            })
            return true
        }
        return false
    }
}
</script>

<style scoped lang="scss"></style>
