<template>
    <div class="detail" v-loading="pager.loading" style="height: 475px">
        <div class="activity-lists" v-show="!showGoods">
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
            <el-table
                ref="table"
                :data="pager.lists"
                style="width: 100%"
                height="370px"
                size="mini"
                @cell-click="handleCellClick"
            >
                <el-table-column label="活动名称" prop="name"> </el-table-column>
                <el-table-column label="活动状态">
                    <template slot-scope="scope">
                        <el-tag
                            size="medium"
                            :type="type == 'presell' ? 'success' : 'danger'"
                            v-if="scope.row.status == 1"
                            >{{ scope.row.status_text }}</el-tag
                        >
                        <el-tag
                            size="medium"
                            :type="type == 'presell' ? 'danger' : 'success'"
                            v-else-if="scope.row.status == 2"
                            >{{ scope.row.status_text }}</el-tag
                        >
                        <el-tag size="medium" type="info" v-else>{{ scope.row.status_text }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="商品数量" prop="goods_num"> </el-table-column>
                <el-table-column label="活动时间" min-width="120">
                    <template slot-scope="scope"> {{ scope.row.start_time }} ~ {{ scope.row.end_time }} </template>
                </el-table-column>
            </el-table>
            <div class="flex row-right m-t-20">
                <ls-pagination v-model="pager" @change="getList()" />
            </div>
        </div>
        <div class="goods-list" v-show="showGoods">
            <div class="flex m-b-20">
                <el-button type="text" icon="el-icon-arrow-left" @click="showGoods = false">返回</el-button>
                <div class="m-r-10 m-l-20">商品搜索</div>
                <el-input
                    size="small"
                    placeholder="请输入商品名称"
                    style="width: 220px"
                    v-model="goodsName"
                    @keyup.enter.native="getGoodsList"
                >
                    <el-button slot="append" icon="el-icon-search" @click="getGoodsList"></el-button>
                </el-input>
            </div>
            <el-table
                ref="table"
                :data="goodsLists"
                style="width: 100%"
                height="420px"
                size="mini"
                @selection-change="handleSelect"
            >
                <el-table-column v-if="single" width="45" label="">
                    <template slot-scope="scope">
                        <el-checkbox
                            :value="scope.row.id == value.id"
                            @change="selectChange($event, scope.row)"
                            v-if="type != 'presell'"
                        />
                        <el-checkbox
                            :value="scope.row.goods_id == value.id"
                            @change="selectChange($event, scope.row)"
                            v-if="type == 'presell'"
                        />
                    </template>
                </el-table-column>
                <el-table-column v-else width="45" type="selection"> </el-table-column>

                <el-table-column label="商品信息" min-width="150">
                    <template slot-scope="scope">
                        <div class="flex">
                            <el-image
                                class="flex-none"
                                style="width: 58px; height: 58px"
                                :src="scope.row.image"
                                fit="cover"
                            />
                            <div class="goods-info m-l-8">
                                <div class="line-2">{{ scope.row.name }}</div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="商品价格">
                    <template slot-scope="scope">
                        ￥{{ scope.row.min_activity_price }}
                        <span v-if="scope.row.min_activity_price != scope.row.max_activity_price">
                            ~ {{ scope.row.max_activity_price }}</span
                        >
                    </template>
                </el-table-column>
                <el-table-column label="商品库存" prop="total_stock"> </el-table-column>
            </el-table>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Inject, Prop, Vue, Watch } from 'vue-property-decorator'
import LsPagination from '@/components/ls-pagination.vue'
import { RequestPaging } from '@/utils/util'
import { apiGetActivity, apiGetActivityGoods } from '@/api/marketing'
@Component({
    components: {
        LsPagination
    }
})
export default class Detail extends Vue {
    @Inject('visible') visible!: any
    $refs!: { table: any }
    @Prop() value!: any
    @Prop() type!: string
    @Prop({ default: false }) single!: boolean
    name = ''
    goodsName = ''
    activeId = ''
    pager = new RequestPaging()
    showGoods = false
    goodsLists = []
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
        } else {
            this.showGoods = false
            this.name = ''
            this.goodsLists = []
        }
    }
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager
            .request({
                callback: apiGetActivity,
                params: {
                    keyword: this.name,
                    type: this.type
                }
            })
            .then((res: any) => {})
    }

    getGoodsList() {
        apiGetActivityGoods({
            keyword: this.goodsName,
            type: this.type,
            activity_id: this.activeId
        }).then(res => {
            this.goodsLists = res
        })
    }

    handleCellClick(row: any) {
        this.goodsName = ''
        this.showGoods = true
        this.goodsLists = []
        this.activeId = row.id
        this.getGoodsList()
    }

    handleSelect(val: any[]) {
        this.selectData = val
    }

    selectChange($event: boolean, val: any) {
        if ($event) {
            this.selectData = val
        } else {
            this.selectData = {}
        }
    }
}
</script>

<style scoped lang="scss">
.detail {
    .activity-lists {
        ::v-deep.el-table__row {
            cursor: pointer;
        }
    }
}
</style>
