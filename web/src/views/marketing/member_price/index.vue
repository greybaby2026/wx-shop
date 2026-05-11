<template>
    <div class="member_price">
        <div class="ls-card">
            <el-alert
                class="xxl"
                title="温馨提示：默认根据会员等级设置的会员折扣自动打折，可以单独设置会员价，拼团和秒杀等营销活动设置参与会员价但是不生效"
                type="info"
                :closable="false"
                show-icon
            />

            <div class="m-t-15">
                <el-form ref="formRef" inline :model="formData" label-width="80px" size="small" class="ls-form">
                    <el-form-item label="商品名称">
                        <el-input v-model="formData.name" placeholder="请输入商品名称"></el-input>
                    </el-form-item>

                    <el-form-item label="商品状态">
                        <el-select v-model="formData.status" placeholder="全部">
                            <div v-for="(value, key) in statusList" :key="key">
                                <el-option :label="value" :value="key"></el-option>
                            </div>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="商品类型">
                        <el-select v-model="formData.goods_type" placeholder="全部">
                            <div v-for="(value, key) in typeList" :key="key">
                                <el-option :label="value" :value="key"></el-option>
                            </div>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="参与折扣">
                        <el-select v-model="formData.discount" placeholder="全部">
                            <div v-for="(value, key) in discountList" :key="key">
                                <el-option :label="value" :value="key"></el-option>
                            </div>
                        </el-select>
                    </el-form-item>

                    <el-form-item>
                        <el-button size="small" type="primary" @click="onSearch"> 查询 </el-button>
                        <el-button size="small" @click="resetSearch"> 重置 </el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="ls-card m-t-15">
            <div>
                <ls-dialog
                    class="inline m-l-10"
                    content="确定批量参与折扣？请谨慎操作。"
                    :disabled="disabledBtn"
                    @confirm="handleBatchParticipate"
                >
                    <el-button slot="trigger" size="small" :disabled="disabledBtn"> 参与折扣 </el-button>
                </ls-dialog>

                <ls-dialog
                    class="inline m-l-10"
                    content="确定批量取消参与折扣？请谨慎操作。"
                    :disabled="disabledBtn"
                    @confirm="handleBatchNoParticipate"
                >
                    <el-button slot="trigger" size="small" :disabled="disabledBtn"> 取消参与 </el-button>
                </ls-dialog>
            </div>
            <div class="list-table m-t-16">
                <el-table
                    :data="pager.lists"
                    v-loading="pager.loading"
                    style="width: 100%"
                    size="mini"
                    :header-cell-style="{ background: '#f5f8ff' }"
                    @selection-change="handleSelectionChange"
                >
                    <el-table-column type="selection" width="55"> </el-table-column>

                    <el-table-column prop="image" label="商品名称" width="240">
                        <template slot-scope="scope">
                            <div class="flex">
                                <el-image class="flex-none" style="width: 58px; height: 58px" :src="scope.row.image" />
                                <div class="goods-info m-l-8">
                                    <div class="line-2">
                                        {{ scope.row.name }}
                                    </div>
                                    <el-tag v-if="scope.row.spec_type == 2" size="mini"> 多规格 </el-tag>
                                </div>
                            </div>
                        </template>
                    </el-table-column>

                    <el-table-column prop="price" label="价格">
                        <template slot-scope="scope">
                            <div>¥{{ scope.row.price }}</div>
                        </template>
                    </el-table-column>

                    <el-table-column prop="total_stock" label="库存"></el-table-column>

                    <el-table-column prop="sales_num" label="销量"></el-table-column>

                    <el-table-column prop="status_desc" label="商品状态"> </el-table-column>

                    <el-table-column prop="is_discount" label="参与折扣">
                        <template slot-scope="scope">
                            <span>{{ scope.row.is_discount == 1 ? '参与' : '不参与' }}</span>
                        </template>
                    </el-table-column>

                    <el-table-column fixed="right" label="操作">
                        <template slot-scope="scope">
                            <el-button
                                type="text"
                                size="small"
                                @click="
                                    $router.push({
                                        name: 'member_price_edit',
                                        query: { id: scope.row.id }
                                    })
                                "
                            >
                                设置会员价
                            </el-button>

                            <ls-dialog
                                v-if="scope.row.is_discount == 0"
                                class="m-l-10 inline"
                                content="是否确认商品参与会员折扣？"
                                @confirm="handleBatchParticipate([scope.row.id])"
                            >
                                <el-button slot="trigger" type="text" size="small"> 参与 </el-button>
                            </ls-dialog>

                            <ls-dialog
                                v-if="scope.row.is_discount == 1"
                                class="m-l-10 inline"
                                content="是否确认商品不参与会员折扣？"
                                @confirm="handleBatchNoParticipate([scope.row.id])"
                            >
                                <el-button slot="trigger" type="text" size="small"> 不参与 </el-button>
                            </ls-dialog>
                        </template>
                    </el-table-column>
                </el-table>
            </div>

            <!-- 底部分页栏  -->
            <div class="flex row-right m-t-16 row-right">
                <ls-pagination v-model="pager" @change="getMemberPriceList()" />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import { RequestPaging } from '@/utils/util'
import {
    apiMemberPriceLists,
    apiMemberPriceJoin,
    apiMemberPriceQuit,
    apiMemberPriceOtherLists
} from '@/api/marketing/member_price'
import { NavigationGuardNext, Route } from 'vue-router'

@Component({
    components: {
        LsDialog,
        LsPagination
    }
})
export default class memberPrice extends Vue {
    apiMemberPriceLists = apiMemberPriceLists

    selectIds: any[] = []

    get disabledBtn() {
        return !this.selectIds.length
    }

    // 表单数据
    formData = {
        name: '', // 商品名称
        status: '', // 商品状态:1-销售中，0-仓库中
        goods_type: '', // 商品类型：1-实物商品，2-虚拟商品
        discount: '' // 参与折扣：1-是；0-否
    }

    statusList = [] // 状态列表
    typeList = [] // 类型列表
    discountList = [] // 折扣状态列表

    // 分页查询
    pager: RequestPaging = new RequestPaging()

    // 搜索
    onSearch() {
        this.pager.page = 1
        this.getMemberPriceList()
    }

    // 重置搜索
    resetSearch() {
        Object.keys(this.formData).map(key => {
            this.$set(this.formData, key, '')
        })
        this.getMemberPriceList()
    }

    // 获取会员折扣列表
    getMemberPriceList() {
        this.pager
            .request({
                callback: apiMemberPriceLists,
                params: { ...this.formData }
            })
            .catch(() => {
                this.$message.error('数据请求失败，刷新重载！')
            })
    }

    // 获取其他列表
    getOtherLists() {
        apiMemberPriceOtherLists({}).then(res => {
            console.log('res', res)
            this.statusList = res.status_list
            this.typeList = res.type_list
            this.discountList = res.discount_list
        })
    }

    // 多选
    handleSelectionChange(val: any[]) {
        this.selectIds = val.map(item => item.id)
    }

    // 批量参与折扣
    handleBatchParticipate(goods_ids: any) {
        apiMemberPriceJoin({
            goods_ids: Array.isArray(goods_ids) ? goods_ids : this.selectIds
        }).then(() => {
            this.getMemberPriceList()
        })
    }

    // 批量不参与折扣
    handleBatchNoParticipate(goods_ids: any) {
        apiMemberPriceQuit({
            goods_ids: Array.isArray(goods_ids) ? goods_ids : this.selectIds
        }).then(() => {
            this.getMemberPriceList()
        })
    }

    created() {
        const query: any = this.$route.query
        // if (query.page) {
        //     this.pager.page = query.page
        // }
        this.getMemberPriceList()
        this.getOtherLists()
    }
}
</script>

<style></style>
