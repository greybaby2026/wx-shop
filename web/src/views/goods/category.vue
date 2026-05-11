<template>
    <div class="ls-category">
        <div class="ls-category__top ls-card">
            <el-alert
                title="温馨提示：1.用户可以根据商品分类搜索商品，请正确创建分类；2.点击分类名称前符号，显示该商品分类的下级分类。"
                type="info"
                show-icon
                :closable="false"
            >
            </el-alert>
        </div>
        <div class="ls-category__content m-t-16 ls-card">
            <div class="ls-content__btns">
                <el-button size="small" type="primary" @click="$router.push('/goods/category_edit')"
                    >新增商品分类</el-button
                >
            </div>
            <div class="ls-content__table m-t-16" v-loading="pager.loading">
                <u-table
                    :data="pager.lists"
                    ref="plTreeTable"
                    fixed-columns-roll
                    :height="800"
                    :treeConfig="{
                        children: 'sons',
                        expandAll: false
                    }"
                    use-virtual
                    row-id="id"
                    :border="false"
                    size="mini"
                >
                    <u-table-column :tree-node="true" prop="name" label="分类名称" />
                    <u-table-column label="分类图片">
                        <template slot-scope="scope">
                            <div class="flex" v-if="scope.row.image">
                                <el-image fit="cover" :src="scope.row.image" style="width: 58px; height: 58px" />
                            </div>
                        </template>
                    </u-table-column>
                    <u-table-column label="是否显示">
                        <template slot-scope="scope">
                            <el-switch
                                v-model="scope.row.is_show"
                                :active-value="1"
                                :inactive-value="0"
                                @change="handleStatus($event, scope.row.id)"
                            >
                            </el-switch>
                        </template>
                    </u-table-column>
                    <u-table-column prop="goods_num" label="商品数量"> </u-table-column>
                    <u-table-column prop="sort" label="排序"> </u-table-column>
                    <u-table-column label="操作">
                        <template slot-scope="scope">
                            <div class="flex">
                                <div>
                                    <el-button
                                        type="text"
                                        size="small"
                                        @click="
                                            $router.push({
                                                path: '/goods/category_edit',
                                                query: { id: scope.row.id }
                                            })
                                        "
                                        >编辑</el-button
                                    >
                                    <el-divider direction="vertical"></el-divider>
                                </div>
                                <ls-dialog
                                    :content="`确定删除：${scope.row.name}？请谨慎操作。`"
                                    @confirm="handleDelete(scope.row.id)"
                                >
                                    <el-button slot="trigger" type="text" size="small">删除</el-button>
                                </ls-dialog>
                            </div>
                        </template>
                    </u-table-column>
                </u-table>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { RequestPaging } from '@/utils/util'
import { Component, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import { apiCategoryDel, apiCategoryLists, apiCategoryStatus } from '@/api/goods'
@Component({
    components: {
        LsDialog,
        LsPagination
    }
})
export default class Category extends Vue {
    $refs!: { plTreeTable: any }
    pager = new RequestPaging()

    getList(): void {
        this.pager
            .request({
                callback: apiCategoryLists,
                params: { pager_type: 1 }
            })
            .then(res => {})
    }
    handleStatus(value: number, id: number) {
        apiCategoryStatus({
            id,
            is_show: value
        }).then(() => {
            this.getList()
        })
    }

    handleDelete(id: number) {
        apiCategoryDel(id).then(() => {
            this.getList()
        })
    }

    created() {
        this.getList()
    }
    activated() {
        this.getList()
    }
}
</script>

<style lang="scss" scoped></style>
