<template>
    <div class="after-sales-list">
        <div v-for="(items) in lists" :key="items.id">
            <div class="after-sales-header flex row-between" v-if="type != 'apply'">
                <div class="flex">
                    <div class="lighter sm">
                        申请时间：{{items.create_time}}
                    </div>
                    <div class="lighter sm" style="margin-left: 110px">
                        申请状态：{{items.sub_status_desc}}
                    </div>
                </div>
                <div class="primary sm" style="margin-right: 12px">
                    {{items.refund_method_desc}}
                </div>
            </div>
            <div class="after-sales-content"
                :class="{shadow: type != 'apply', border: type == 'apply', qborder: type != 'apply'}">
                <div class="goods-item flex row-between">
                    <div class="flex">
                        <el-image style="width: 72px;height: 72px" :src="items.goods_snap.image" />
                        <div class="goods-info">
                            <div class="goods-name noraml line1">
                                {{items.goods_snap.goods_name}}
                            </div>
                            <div class="muted sm m-t-8 m-b-8">
                                {{items.goods_snap.spec_value_str}}
                            </div>
                            <price-formate :price="items.goods_price" showSubscript color="#FF2C3C" />
                        </div>
                    </div>
                    <div class="flex wrap m-b-4" :style="{width: type != 'apply' ? null : '210px'}">
                        <el-button class="apply-btn flex row-center m-r-20 sm"
                            :disabled="items.after_sale && items.after_sale.able_apply == 0"
                            @click.stop="goPage(items.id)" v-show="type == 'apply'" size="small">申请售后
                        </el-button>
                        <template v-if="items.btns">
                            <el-button class="apply-btn flex row-center m-t-10 m-r-20 sm"
                                :disabled="items.after_sale && items.after_sale.able_apply == 0"
                                @click.stop="goPage(items.order_goods_id)" v-show="items.btns.reapply_btn" size="small">重新申请
                            </el-button>
                            <el-button class="apply-btn flex row-center m-t-10 m-r-20 sm" v-show="type != 'apply'"
                                size="small" @click="goToDetail(items.sub_id)">查看详情</el-button>
                            <el-button class="apply-btn flex row-center m-r-20 sm" v-show="items.btns.cancel_btn"
                                size="small" @click.stop="cancelApply(items.master_id)">撤销申请</el-button>
                            <nuxt-link class="lighter" :to="'/user/after_sales/input_express?id='+items.master_id" v-show="items.btns.express_btn">
                                <el-button class="apply-btn flex row-center m-t-10 m-r-20 sm" 
                                    size="small">填写快递单号</el-button>
                            </nuxt-link>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { AfterSaleType } from "@/utils/type";
export default {
    props: {
        type: {
            type: String,
            default: AfterSaleType.NORMAL,
        },
        lists: {
            type: Array,
            default: () => [{ btns: { express_btn: false } }],
        },
    },
    data() {
        return {};
    },
    methods: {
        goToDetail(id) {
            switch (this.type) {
                case AfterSaleType.NORMAL:
                    this.$router.push("/goods_details/" + id);
                    break;
                case AfterSaleType.HANDLING:
                case AfterSaleType.FINISH:
                    this.$router.push(
                        "/user/after_sales/after_sale_details?afterSaleId=" + id
                    );
                    break;
            }
        },

        goPage(orderId) {
            this.$router.push(
                "/user/after_sales/apply_sale?order_id=" + orderId
            );
        },

        async cancelApply(afterSaleId) {
            console.log(afterSaleId);
            let res = await this.$post("after_sale/cancel", {
                id: afterSaleId,
            });
            if (res.code == 1) {
                this.$message({
                    message: res.msg,
                    type: "success",
                });
                this.$emit("refresh");
            }
        },
    },
};
</script>

<style lang="scss" scoped>
.after-sales-list {
    .after-sales-header {
        border: 1px solid #e5e5e5;
        border-bottom: 0;
        background-color: #f2f2f2;
    }
    .after-sales-content {
        padding: 6px 0;
        .goods-item {
            padding: 10px 20px;
            .goods-info {
                margin-left: 10px;
                width: 500px;
            }
            .apply-btn:hover {
                border: 1px solid $--color-primary;
            }
            .apply-btn {
                border: 1px solid #cccccc;
                border-radius: 2px;
                width: 100px;
                height: 32px;
                align-self: flex-start;
                &:nth-of-type(2n) {
                    margin-left: 10px;
                }
                &:nth-of-type(3) {
                    margin-top: 10px;
                    margin-left: 0px;
                }
                &:nth-of-type(4) {
                    margin-top: 10px;
                }
            }
        }
    }
    .shadow {
        box-shadow: 0px 3px 4px rgba(0, 0, 0, 0.08);
    }
    .border {
        border: 0 !important;
        border-bottom: 1px solid #e5e5e5 !important;
    }
    .qborder {
        border: 1px solid #e5e5e5 !important;
    }
}
</style>