<template>
    <div>
        <div class="ls-card">
            <el-alert
                title="温馨提示：商品服务保障是提供给消费者的一种保障和支持机制，旨在保障消费者在购买商品后享有一定的权益和服务。"
                type="info"
                show-icon
                :closable="false"
            ></el-alert>
        </div>
        <div class="m-t-16 ls-card">
            <div class="ls-content__btns">
                <el-button size="small" type="primary" @click="handleAdd">新增保障</el-button>
            </div>
            <div class="m-t-16">
                <el-table ref="table" :data="pager.lists" style="width: 100%" size="mini">
                    <el-table-column fixed label="名称" min-width="100" prop="name"></el-table-column>
                    <el-table-column fixed label="说明" min-width="100" prop="content"></el-table-column>
                    <el-table-column fixed="right" label="操作" min-width="100">
                        <template slot-scope="scope">
                            <el-button type="text" size="small" @click="handleEdit(scope.row)">编辑</el-button>
                            <ls-dialog
                                class="m-l-10 inline"
                                :content="`确定删除：${scope.row.name}？请谨慎操作。`"
                                @confirm="handleDelete(scope.row.id)"
                            >
                                <el-button slot="trigger" type="text" size="small">删除</el-button>
                            </ls-dialog>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <div class="flex row-right m-t-16">
                <ls-pagination v-model="pager" @change="getList()" />
            </div>
        </div>
        <add-guarantee ref="AddGuarantee" :value="form" @refresh="getList(1)" />
    </div>
</template>
<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { RequestPaging } from '@/utils/util'
import { apigoodsServiceGuaranteelists, apigoodsServiceGuaranteetDel } from '@/api/goods'
import LsPagination from '@/components/ls-pagination.vue'
import LsDialog from '@/components/ls-dialog.vue'
import DatePicker from '@/components/date-picker.vue'
import ExportData from '@/components/export-data/index.vue'
import AddGuarantee from '@/components/goods/add-guarantee.vue'

@Component({
    components: {
        LsDialog,
        LsPagination,
        DatePicker,
        ExportData,
        AddGuarantee
    }
})
export default class Guarantee extends Vue {
    $refs!: { AddGuarantee: any }

    pager = new RequestPaging()
    form: any = {
        name: '',
        content: ''
    }
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apigoodsServiceGuaranteelists
        })
    }
    handleAdd() {
        this.form = {
            name: '',
            content: ''
        }
        this.$refs.AddGuarantee.openDialog()
    }
    handleEdit({ id, name, content }: any) {
        this.form = {
            id,
            name,
            content
        }
        this.$refs.AddGuarantee.openDialog()
    }
    handleDelete(id: number) {
        apigoodsServiceGuaranteetDel({ id }).then(() => {
            this.getList()
        })
    }
    created() {
        this.getList()
    }
}
</script>
