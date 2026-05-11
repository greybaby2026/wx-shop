<template>
    <div class="goods-pane">
        <div class="list-header">
            <el-button size="small" type="primary" @click="$emit('add')">添加直播商品</el-button>
            <el-button size="small" @click="$emit('synchronization')">同步商品库</el-button>
        </div>
        <div class="list-table m-t-16">
            <el-table
                :data="value"
                style="width: 100%"
                size="mini"
                v-loading="pager.loading"
                :header-cell-style="{ background: '#f5f8ff' }"
            >
                <el-table-column prop="name" label="商品名称"> </el-table-column>
                <el-table-column prop="price" label="商品价格"> </el-table-column>
                <el-table-column prop="url" label="商品链接"> </el-table-column>
                <el-table-column prop="live_status" label="状态">
                    <template>
                        <div class="" v-if="type == '1'">审核中</div>
                        <div class="" v-if="type == '2'">审核通过</div>
                        <div class="" v-if="type == '3'">审核驳回</div>
                    </template>
                </el-table-column>
                <el-table-column fixed="right" label="操作" min-width="100">
                    <template slot-scope="scope">
                        <ls-dialog
                            class="m-l-10 inline"
                            :content="`确定删除直播商品：${scope.row.name}`"
                            @confirm="onDelete(scope.row)"
                        >
                            <el-button type="text" size="small" slot="trigger">删除</el-button>
                        </ls-dialog>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <!-- 底部分页栏  -->
        <div class="flex row-right m-t-16 row-right">
            <ls-pagination v-model="pager" @change="$emit('refresh')" />
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Prop } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
@Component({
    components: {
        LsPagination,
        LsDialog
    }
})
export default class LBGoodsPane extends Vue {
    @Prop() value: any // 列表数据
    @Prop() pager!: any // 含有分页信息的数据
    @Prop() type?: string // 含有分页信息的数据

    onDelete(value: any) {
        this.$emit('onDel', value)
    }
}
</script>

<style lang="scss" scoped></style>
