<template>
    <div class="after-sales-pane">
        <div class="pane-table">
            <el-table :data="value" style="width: 100%" size="mini">
                <el-table-column label="售后单号" prop="sn" min-width="180"></el-table-column>
                <el-table-column label="用户" min-width="120">
                    <template slot-scope="scope">
                        <el-popover placement="top" width="200" trigger="hover">
                            <div class="flex">
                                <span class="flex-none m-r-20">头像：</span>
                                <el-image :src="scope.row.avatar" style="width: 40px; height: 40px; border-radius: 50%">
                                </el-image>
                            </div>
                            <div class="flex m-t-20 col-top">
                                <span class="flex-none m-r-20">昵称：</span>
                                <span>{{ scope.row.nickname }}</span>
                            </div>
                            <div class="flex m-t-20 col-top">
                                <span class="flex-none m-r-20">编号：</span>
                                <span>{{ scope.row.user_sn }}</span>
                            </div>
                            <div slot="reference" class="pointer" @click="toUser(scope.row.user_id)">
                                {{ scope.row.nickname }}
                            </div>
                        </el-popover>
                    </template>
                </el-table-column>
                <el-table-column label="订单编号" prop="order_sn" min-width="150"></el-table-column>
                <!-- 商品的图片 -->
                <el-table-column label="商品图片" min-width="90">
                    <template slot-scope="scope">
                        <div
                            @click="click(scope.row)"
                            class="m-t-10"
                            v-for="(item, index) in scope.row.after_sale_goods"
                            :key="index"
                        >
                            <el-image
                                v-if="index < 3"
                                :src="item.goods_snap.image"
                                style="width: 58px; height: 58px"
                                class="flex-none"
                            >
                            </el-image>
                        </div>
                        <div
                            @click="click(scope.row)"
                            class="muted m-t-5 flex"
                            v-if="scope.row.after_sale_goods.length > 3"
                        >
                            共{{ scope.row.after_sale_goods.length }}件商品
                            <i class="el-icon-arrow-right"></i>
                        </div>
                    </template>
                </el-table-column>

                <!-- 商品的信息 -->
                <el-table-column label="商品信息" min-width="250">
                    <template slot-scope="scope">
                        <div
                            @click="click(scope.row)"
                            :style="{
                                'margin-bottom': scope.row.after_sale_goods.length > 2 ? '30px' : ''
                            }"
                        >
                            <div class="goods" v-for="(item, index) in scope.row.after_sale_goods" :key="index">
                                <div class="" v-if="index < 3">
                                    <div class="flex row-between normal p-r-24 line-1">
                                        <span class="line-1 name">{{ item.goods_snap.goods_name }}</span>
                                    </div>
                                    <div class="xs lighter flex line-1 p-r-24">
                                        规格：{{ item.goods_snap.spec_value_str }}
                                    </div>
                                    <div class="xs muted flex p-r-24 line-1">
                                        价格：<span class="normal m-r-10">¥{{ item.goods_snap.original_price }}</span>
                                        数量：<span class="normal">{{ item.goods_snap.goods_num }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="售后类型" prop="refund_type_desc" min-width="100"></el-table-column>
                <el-table-column label="退款方式" prop="refund_method_desc" min-width="100"></el-table-column>
                <el-table-column label="退款金额" prop="refund_total_amount" min-width="100"></el-table-column>
                <el-table-column label="售后状态" prop="sub_status_desc" min-width="180">
                    <template slot-scope="scope">
                        <div>{{ scope.row.status_desc }}</div>
                        <div>{{ scope.row.sub_status_desc }}</div>
                    </template>
                </el-table-column>
                <el-table-column label="申请时间" prop="create_time" min-width="180"></el-table-column>
                <el-table-column fixed="right" label="操作" min-width="150">
                    <template slot-scope="scope">
                        <el-button type="text" size="small" @click="click(scope.row)">售后详情</el-button>

                        <ls-dialog
                            v-if="scope.row.sub_status === 11"
                            class="inline m-l-10"
                            :content="`同意该订单的售后？请谨慎操作。`"
                            @confirm="afterSaleAgree(scope.row.id)"
                        >
                            <el-button slot="trigger" type="text" size="small">同意</el-button>
                        </ls-dialog>

                        <ls-dialog
                            v-if="scope.row.sub_status === 11"
                            class="inline m-l-10"
                            :content="`拒绝该订单的售后？请谨慎操作。`"
                            @confirm="afterSaleRefuse(scope.row.id)"
                        >
                            <el-button slot="trigger" type="text" size="small">拒绝</el-button>
                        </ls-dialog>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <div class="pane-footer m-t-16 flex row-right">
            <ls-pagination v-model="pager" @change="$emit('refresh')" />
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import { apiAfterSaleAgree, apiAfterSaleRefuse } from '@/api/order/order'
@Component({
    components: {
        LsDialog,
        LsPagination
    }
})
export default class OrderPane extends Vue {
    @Prop() value: any
    @Prop() pager!: any

    // 跳转
    click(event: any) {
        this.$router.push({
            path: '/order/after_sales_detail',
            query: { id: event.id }
        })
    }

    toUser(id: any) {
        this.$router.push({
            path: '/user/user_details',
            query: { id: id }
        })
    }

    // 同意售后
    afterSaleAgree(id: number) {
        apiAfterSaleAgree({ id }).then(res => {
            this.$emit('refresh')
        })
    }

    // 拒绝售后
    afterSaleRefuse(id: number) {
        apiAfterSaleRefuse({ id }).then(res => {
            this.$emit('refresh')
        })
    }
}
</script>

<style scoped lang="scss">
.goods:hover {
    height: 100%;
    cursor: pointer;
    .name {
        color: $--color-primary;
    }
}
</style>
