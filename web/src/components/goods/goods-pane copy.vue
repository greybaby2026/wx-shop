<template>
    <div class="goods-pane">
        <div class="pane-header">
            <el-button size="small" type="primary" @click="$router.push('/goods/release')">新增商品</el-button>
            <el-button size="small" type="success" @click="$router.push('/goods_collection/index')">商品采集</el-button>
            <ls-dialog
                class="m-l-10 inline"
                content="确定批量上架销售？请谨慎操作。"
                :disabled="disabledBtn"
                @confirm="handleBatchStatus({ status: 1 })"
            >
                <el-button slot="trigger" size="small" :disabled="disabledBtn">上架销售</el-button>
            </ls-dialog>
            <ls-dialog
                class="m-l-10 inline"
                content="确定批量放入仓库？请谨慎操作。"
                :disabled="disabledBtn"
                @confirm="handleBatchStatus({ status: 0 })"
            >
                <el-button slot="trigger" size="small" :disabled="disabledBtn">放入仓库</el-button>
            </ls-dialog>

            <ls-dialog
                class="m-l-10 inline"
                content="确定批量删除？请谨慎操作。"
                :disabled="disabledBtn"
                @confirm="handleBatchDelete"
            >
                <el-button slot="trigger" :disabled="disabledBtn" size="small">删除</el-button>
            </ls-dialog>

            <ls-dialog
                class="m-l-10 inline"
                width="500px"
                title="移动分类"
                :disabled="disabledBtn"
                @confirm="handleMoveCategory"
            >
                <el-button slot="trigger" :disabled="disabledBtn" size="small">移动分类</el-button>
                移动至
                <el-cascader
                    v-model="categoryIds"
                    style="width: 380px"
                    :options="categoryList"
                    :props="{
                        multiple: true,
                        checkStrictly: true,
                        label: 'name',
                        value: 'id',
                        children: 'sons',
                        emitPath: false
                    }"
                    clearable
                    filterable
                />
            </ls-dialog>
        </div>
        <div class="pane-table m-t-16">
            <el-table ref="paneTable" :data="value" style="width: 100%" size="mini" @selection-change="handleSelect">
                <el-table-column fixed="left" type="selection" width="55"> </el-table-column>
                <el-table-column prop="code" label="商品编码" width="120"> </el-table-column>
                <el-table-column prop="code" label="商品图片" width="100">
                    <template slot-scope="scope">
                        <el-image
                            fit="cover"
                            class="flex-none"
                            style="width: 58px; height: 58px"
                            :src="scope.row.image"
                        />
                    </template>
                </el-table-column>

                <el-table-column label="商品名称" min-width="200" :show-overflow-tooltip="true">
                    <template slot-scope="scope">
                        <div class="ls-edit-wrap flex">
                            <div class="line-1">{{ scope.row.name }}</div>
                            <popover-input
                                :width="400"
                                type="text"
                                :value="scope.row.name"
                                @confirm="handleRename($event, scope.row.id)"
                            >
                                <el-button
                                    style="padding: 0"
                                    class="ls-edit"
                                    type="text"
                                    icon="el-icon-edit"
                                ></el-button>
                            </popover-input>
                        </div>
                        <div>
                            <el-tag v-if="scope.row.spec_type == 2" size="mini" class="m-r-6">多规格</el-tag>
                            <el-tag
                                v-if="scope.row.goods_activity.includes(2)"
                                size="mini"
                                effect="dark"
                                type="danger"
                                class="m-r-6"
                                >秒杀</el-tag
                            >
                            <el-tag
                                v-if="scope.row.goods_activity.includes(3)"
                                size="mini"
                                effect="dark"
                                type="warning"
                                class="m-r-6"
                                >砍价</el-tag
                            >
                            <el-tag
                                v-if="scope.row.goods_activity.includes(1)"
                                size="mini"
                                effect="dark"
                                type="success"
                                class="m-r-6"
                                >拼团</el-tag
                            >
                            <el-tag
                                size="mini"
                                effect="dark"
                                type="success"
                                class="m-r-6"
                                v-if="scope.row.goods_activity.includes(4)"
                                >预售</el-tag
                            >
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="price" label="价格" min-width="100"> </el-table-column>
                <el-table-column prop="total_stock" label="库存" min-width="100"> </el-table-column>
                <el-table-column prop="sales_num" label="销量" min-width="100"> </el-table-column>
                <el-table-column prop="click_num" label="浏览量" min-width="100"> </el-table-column>
                <el-table-column label="销售状态" min-width="100">
                    <template slot-scope="scope">
                        <el-tag size="medium" type="success" v-if="scope.row.status == 1">销售中</el-tag>
                        <el-tag size="medium" type="info" v-else>仓库中</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="排序" width="100">
                    <template slot-scope="scope">
                        <div class="ls-edit-wrap">
                            <span>{{ scope.row.sort }}</span>
                            <popover-input tips="数值越大，越靠前" @confirm="handleSort($event, scope.row.id)">
                                <el-button class="ls-edit" type="text" icon="el-icon-edit"></el-button>
                            </popover-input>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="create_time" label="创建日期" min-width="100"> </el-table-column>
                <el-table-column fixed="right" label="操作" width="180">
                    <template slot-scope="scope">
                        <div>
                            <div class="inline m-r-10">
                                <el-button
                                    type="text"
                                    size="small"
                                    @click="
                                        $router.push({
                                            path: '/goods/release',
                                            query: { id: scope.row.id }
                                        })
                                    "
                                    >编辑</el-button
                                >
                            </div>
                            <ls-dialog
                                v-if="scope.row.status == 1"
                                class="inline m-r-10"
                                :content="`确定放入仓库：${scope.row.name}？请谨慎操作。`"
                                @confirm="
                                    handleBatchStatus({
                                        ids: [scope.row.id],
                                        status: 0
                                    })
                                "
                            >
                                <el-button slot="trigger" type="text" size="small">放入仓库</el-button>
                            </ls-dialog>
                            <ls-dialog
                                v-else
                                class="inline m-r-10"
                                :content="`确定上架销售：${scope.row.name}？请谨慎操作。`"
                                @confirm="
                                    handleBatchStatus({
                                        ids: [scope.row.id],
                                        status: 1
                                    })
                                "
                            >
                                <el-button slot="trigger" type="text" size="small">上架销售</el-button>
                            </ls-dialog>
                            <ls-dialog
                                class="inline"
                                :content="`确定删除：${scope.row.name}？请谨慎操作。`"
                                @confirm="handleBatchDelete([scope.row.id])"
                            >
                                <el-button slot="trigger" type="text" size="small">删除</el-button>
                            </ls-dialog>
                        </div>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <div class="pane-footer m-t-16 flex row-between">
            <div class="btns flex">
                <div class="m-r-16">
                    <el-checkbox
                        :value="selectIds.length == value.length"
                        @change="handleselectAll"
                        :disabled="!value.length"
                        >当页全选</el-checkbox
                    >
                </div>
                <ls-dialog
                    class="inline"
                    content="确定批量上架销售？请谨慎操作。"
                    :disabled="disabledBtn"
                    @confirm="handleBatchStatus({ status: 1 })"
                >
                    <el-button slot="trigger" size="small" :disabled="disabledBtn">上架销售</el-button>
                </ls-dialog>
                <ls-dialog
                    class="m-l-10 inline"
                    content="确定批量放入仓库？请谨慎操作。"
                    :disabled="disabledBtn"
                    @confirm="handleBatchStatus({ status: 0 })"
                >
                    <el-button slot="trigger" size="small" :disabled="disabledBtn">放入仓库</el-button>
                </ls-dialog>

                <ls-dialog
                    class="m-l-10 inline"
                    content="确定批量删除？请谨慎操作。"
                    :disabled="disabledBtn"
                    @confirm="handleBatchDelete"
                >
                    <el-button slot="trigger" :disabled="disabledBtn" size="small">删除</el-button>
                </ls-dialog>
            </div>
            <ls-pagination v-model="pager" @change="$emit('refresh')" />
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import PopoverInput from '@/components/popover-input.vue'
import { apiGoodsDel, apiGoodsRename, apiGoodsSort, apiGoodsStatus, apiMoveCategory } from '@/api/goods'

@Component({
    components: {
        LsDialog,
        LsPagination,
        PopoverInput
    }
})
export default class GoodsPane extends Vue {
    $refs!: { paneTable: any }
    @Prop() value: any
    @Prop() pager!: any
    @Prop() categoryList!: any
    status = true
    selectIds: any[] = []
    categoryIds: number[] = []

    get disabledBtn() {
        return !this.selectIds.length
    }

    // 批量上架/下架商品
    handleBatchStatus({ status, ids }: any) {
        apiGoodsStatus({
            ids: ids ? ids : this.selectIds,
            status
        }).then(() => {
            this.$emit('refresh')
        })
    }
    // 批量删除商品
    handleBatchDelete(ids: any) {
        apiGoodsDel({
            ids: Array.isArray(ids) ? ids : this.selectIds
        }).then(() => {
            this.$emit('refresh')
        })
    }
    handleMoveCategory() {
        console.log('ids', this.selectIds, this.categoryIds)
        apiMoveCategory({
            ids: this.selectIds,
            category_id: this.categoryIds
        }).then(() => {
            this.$emit('refresh')
        })
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
    handleRename(val: string, id: number) {
        apiGoodsRename({
            id,
            name: val
        }).then(() => {
            this.$emit('refresh')
        })
    }
}
</script>

<style scoped lang="scss"></style>
