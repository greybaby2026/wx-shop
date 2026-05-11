<template>
    <div class="integral-goods">
        <div class="ls-card" style="padding-bottom: 8px">
            <el-alert
                title="温馨提示：1.添加积分商城商品；2.兑换类型为“红包”时，无需物流配送，付完款直接是已完成状态；兑换类型为“商品”时，可以做发货操作。"
                type="info"
                show-icon
                :closable="false"
            ></el-alert>
            <div class="form-data m-t-16">
                <el-form inline :model="formData" label-width="80px" size="small">
                    <el-form-item label="商品名称">
                        <el-input v-model="formData.name" placeholder="请输入商品名称"></el-input>
                    </el-form-item>
                    <el-form-item label="兑换类型">
                        <el-select v-model="formData.type" placeholder="请选择兑换类型">
                            <el-option label="全部" value=""></el-option>
                            <el-option label="商品" :value="1"></el-option>
                            <el-option label="红包" :value="2"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="商品状态">
                        <el-select v-model="formData.status" placeholder="请选择商品状态">
                            <el-option label="全部" value=""></el-option>
                            <el-option label="上架" :value="1"></el-option>
                            <el-option label="下架" :value="0"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label class="m-l-20">
                        <el-button size="small" type="primary" @click="getList(1)"> 查询 </el-button>
                        <el-button size="small" @click="handleReset"> 重置 </el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <div class="ls-card m-t-16">
            <el-button size="small" type="primary" @click="$router.push('/integral_mall/integral_goods_edit')">
                新增积分商品
            </el-button>

            <div class="table-content m-t-16">
                <el-table :data="pager.lists" style="width: 100%" size="mini" v-loading="pager.loading">
                    <el-table-column prop="id" label="ID" width="80"></el-table-column>
                    <el-table-column prop="name" label="商品名称" show-overflow-tooltip>
                        <template slot-scope="scope">
                            <div class="flex">
                                <el-image
                                    fit="cover"
                                    class="flex-none"
                                    style="width: 58px; height: 58px"
                                    :src="scope.row.image"
                                />
                                <div class="m-l-10">{{ scope.row.name }}</div>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="stock" label="库存"></el-table-column>
                    <el-table-column prop="type_desc" label="兑换类型"></el-table-column>
                    <el-table-column prop="need_desc" label="兑换积分"></el-table-column>
                    <el-table-column label="商品状态">
                        <template slot-scope="scope">
                            <el-switch
                                v-model="scope.row.status"
                                :active-value="1"
                                :inactive-value="0"
                                @change="handleStatus($event, scope.row.id)"
                            ></el-switch>
                        </template>
                    </el-table-column>

                    <el-table-column prop="sort" label="排序"></el-table-column>
                    <el-table-column fixed="right" label="操作">
                        <template slot-scope="scope">
                            <el-button
                                type="text"
                                size="small"
                                @click="
                                    $router.push({
                                        path: '/integral_mall/integral_goods_edit',
                                        query: { id: scope.row.id }
                                    })
                                "
                            >
                                编辑
                            </el-button>
                            <ls-dialog
                                class="m-l-10 inline"
                                :content="`确定删除：${scope.row.name}？请谨慎操作。`"
                                @confirm="handleDelete(scope.row.id)"
                            >
                                <el-button slot="trigger" type="text" size="small"> 删除 </el-button>
                            </ls-dialog>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <div class="flex row-right m-t-16">
                <ls-pagination v-model="pager" @change="getList()" />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiIntegralGoodsDel, apiIntegralGoodsLists, apiIntegralGoodsStatus } from '@/api/application/integral_mall'
import { RequestPaging } from '@/utils/util'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
@Component({
    components: {
        LsDialog,
        LsPagination
    }
})
export default class IntegralGoods extends Vue {
    formData = {
        name: '',
        type: '',
        status: ''
    }

    pager = new RequestPaging()
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apiIntegralGoodsLists,
            params: this.formData
        })
    }
    handleReset() {
        Object.keys(this.formData).forEach(key => {
            this.$set(this.formData, key, '')
        })
        this.getList()
    }
    handleStatus(value: number, id: number) {
        apiIntegralGoodsStatus({
            id,
            status: value
        }).then(() => {
            this.getList()
        })
    }

    handleDelete(id: number) {
        apiIntegralGoodsDel({ id }).then(() => {
            this.getList()
        })
    }

    created() {
        this.getList()
    }
}
</script>
