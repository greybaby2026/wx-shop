<template>
    <div class="after-sales-pane">
        <ls-dialog
            class="inline"
            content="确定批量上架销售？请谨慎操作。"
            :disabled="disabledBtn"
            @confirm="handleBatchStatus({ type: 'multiple', status: 1 })"
        >
            <el-button slot="trigger" size="small" :disabled="disabledBtn">批量上架</el-button>
        </ls-dialog>
        <ls-dialog
            class="m-l-10 inline"
            content="确定批量放入仓库？请谨慎操作。"
            :disabled="disabledBtn"
            @confirm="handleBatchStatus({ type: 'multiple', status: 0 })"
        >
            <el-button slot="trigger" size="small" :disabled="disabledBtn">放入仓库</el-button>
        </ls-dialog>

        <div class="pane-table m-t-15">
            <el-table
                ref="paneTable"
                :data="value"
                style="width: 100%"
                size="mini"
                @selection-change="handleSelect"
                v-loading="pager.loading"
            >
                <el-table-column fixed="left" type="selection" width="55" :selectable="selectableFn"></el-table-column>
                <el-table-column prop="gather_url" label="采集链接" min-width="280">
                    <template slot-scope="scope">
                        <div :style="{ color: '#ff3333', overflow: 'hidden', display: '-webkit-box', '-webkit-box-orient': 'vertical', '-webkit-line-clamp': 2 }" v-if="!scope.row.gather_status">
                            {{ scope.row.gather_url }}
                        </div>
                        <div :style="{ overflow: 'hidden', display: '-webkit-box', '-webkit-box-orient': 'vertical', '-webkit-line-clamp': 2 }" v-else>{{ scope.row.gather_url }}</div>
                    </template>
                </el-table-column>
                <el-table-column label="商品信息" min-width="290">
                    <template slot-scope="scope">
                        <div class="flex">
                            <div v-if="scope.row.goods_info.goods_image">
                                <el-image
                                    fit="cover"
                                    class="flex-none"
                                    style="width: 58px; height: 58px"
                                    :src="scope.row.goods_info.goods_image"
                                />
                            </div>
                            <div class="m-l-8">
                                <div class="ls-edit-wrap flex">
                                    <div>{{ scope.row.goods_info.goods_name }}</div>
                                </div>
                                <div>
                                    <el-tag v-if="scope.row.goods_info.is_more_sku === 1" size="mini" class="m-r-6"
                                        >多规格</el-tag
                                    >
                                </div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="channel_desc" label="渠道" min-width="120"> </el-table-column>
                <el-table-column prop="gather_status_desc" label="状态" min-width="120">
                    <template slot-scope="scope">
                        <el-tag size="medium" type="success" v-if="scope.row.gather_status === 1">成功</el-tag>
                        <el-tag size="medium" type="danger" v-else>失败</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="create_time" label="成功时间" min-width="160">
                    <template slot-scope="scope">
                        <div v-if="!scope.row.gather_status">-</div>
                        <div v-else>{{ scope.row.create_time }}</div>
                    </template>
                </el-table-column>
                <el-table-column fixed="right" label="操作" min-width="180">
                    <template slot-scope="scope">
                        <div v-if="!disabledBtn && scope.row.gather_status">
                            <div class="inline m-r-10">
                                <el-button
                                    type="text"
                                    size="small"
                                    @click="
                                        $router.push({
                                            path: '/goods/release',
                                            query: { id: scope.row.id, type: 'goods_collection' }
                                        })
                                    "
                                    >编辑</el-button
                                >
                            </div>
                            <ls-dialog
                                class="inline m-r-10"
                                :content="`确定放入仓库？请谨慎操作。`"
                                @confirm="
                                    handleBatchStatus({
                                        type: 'single',
                                        ids: [scope.row.id],
                                        status: 0
                                    })
                                "
                            >
                                <el-button slot="trigger" type="text" size="small">放入仓库</el-button>
                            </ls-dialog>
                            <ls-dialog
                                class="inline m-r-10"
                                :content="`确定上架销售？请谨慎操作。`"
                                @confirm="
                                    handleBatchStatus({
                                        type: 'single',
                                        ids: [scope.row.id],
                                        status: 1
                                    })
                                "
                            >
                                <el-button slot="trigger" type="text" size="small">上架销售</el-button>
                            </ls-dialog>
                        </div>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <div class="pane-footer m-t-16 flex row-right">
            <ls-pagination v-model="pager" @change="$emit('refresh')" />
        </div>
        <IsLoading ref="isLoadingRef" title=" " />
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import PopoverInput from '@/components/popover-input.vue'
import { apiGoodsSort } from '@/api/goods'
import { apiCreateGoods } from '@/api/application/goods_collection'
import IsLoading from './is-loading.vue'

@Component({
    components: {
        LsDialog,
        LsPagination,
        PopoverInput,
        IsLoading
    }
})
export default class GoodsPane extends Vue {
    $refs!: { paneTable: any; isLoadingRef: any }
    @Prop() value: any
    @Prop() pager!: any
    @Prop() tabStatus!: number
    status = true
    selectIds: any[] = []

    get disabledBtn() {
        return !this.selectIds.length && this.tabStatus === 1
    }

    // 上架/下架商品
    handleBatchStatus({ type, status, ids }: any) {
        if (type === 'multiple' && !this.selectIds.length) {
            return this.$message.error('请先选择商品')
        }
        this.$refs.isLoadingRef.openDialog()
        apiCreateGoods({
            ids: ids ? ids : this.selectIds,
            status
        })
            .then(() => {
                this.$emit('refresh')
                this.$message.success('操作成功')
            })
            .finally(() => {
                this.$refs.isLoadingRef.closeDialog()
            })
    }

    // 过滤商品
    selectableFn(row: any) {
        if (this.disabledBtn || !row.gather_status) {
            return false
        }
        return true
    }

    handleSelect(val: any[]) {
        this.selectIds = val.map(item => item.id)
    }
    // 全选商品
    handleselectAll() {
        this.$refs.paneTable.toggleAllSelection()
    }

    handleSort(sort: string, id: number) {
        apiGoodsSort({
            id,
            sort
        }).then(() => {
            this.$emit('refresh')
        })
    }
}
</script>
