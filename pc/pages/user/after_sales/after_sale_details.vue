<template>
    <div class="apply-detail">
        <div class="apply-detail-content">
            <el-table :data="lists.goods_snap" style="width: 100%">
                <el-table-column prop="date" label="商品信息" max-width="180">
                    <template slot-scope="scope">
                        <div class="flex">
                            <el-image
                                style="width: 80px; height: 80px"
                                :src="scope.row.image"
                                fit="fit"
                            ></el-image>
                            <div class="m-l-10">
                                <div class="line-2">
                                    {{ scope.row.goods_name }}
                                </div>
                                <div>
                                    {{ scope.row.spec_value_str }}
                                </div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="goods_num" label="数量" width="180">
                </el-table-column>
                <el-table-column prop="name" label="实付" width="180">
                    <template slot-scope="scope">
                        <price-formate
                            :price="lists.refund_amount"
                            showSubscript
                        />
                    </template>
                </el-table-column>

                <el-table-column prop="address" label="申请状态" width="180">
                    <template>{{ lists.sub_status_desc }}</template>
                </el-table-column>
            </el-table>

            <div class="m-t-30">
                <div class="result-content">
                    <div class="result-item flex">
                        <div class="label">退款类型:</div>
                        <div class="desc">{{ lists.refund_method_desc }}</div>
                    </div>
                    <div class="result-item flex">
                        <div class="label">退款原因:</div>
                        <div class="desc">{{ lists.refund_reason }}</div>
                    </div>
                    <div class="result-item flex">
                        <div class="label">退款金额:</div>
                        <div class="desc">
                            <price-formate
                                :price="lists.refund_amount"
                                showSubscript
                                color="red"
                            />
                        </div>
                    </div>
                    <div class="result-item flex">
                        <div class="label">申请时间:</div>
                        <div class="desc">{{ lists.create_time }}</div>
                    </div>
                    <div class="result-item flex">
                        <div class="label">退款编号:</div>
                        <div class="desc">{{ lists.sn }}</div>
                    </div>
                    <div class="result-item flex">
                        <div class="label">退款说明:</div>
                        <div class="column desc">
                            <div class="m-b-16">{{ lists.refund_remark }}</div>
                            <el-image
                                style="width: 76px; height: 76px"
                                :src="item"
                                v-for="(item, index) in lists.voucher"
                                :key="index"
                                :preview-src-list="[lists.voucher]"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="apply-detail-address flex"
                v-if="lists.btns.express_btn"
            >
                退货地址：{{ lists.return_address.contact || "-" }},
                {{ lists.return_address.mobile || "-" }},{{
                    lists.return_address.complete_address || "-"
                }}

                <div class="copy pointer" @click="onCopy">复制</div>
            </div>

            <div class="btn-group flex row-center m-t-60">
                <el-button
                    class="apply-btn flex row-center sm"
                    size="small"
                    slot="reference"
                    v-if="lists.btns.cancel_btn"
                    @click="cancel(lists.master_id)"
                    >撤销申请</el-button
                >
                <nuxt-link
                    class="lighter"
                    :to="
                        '/user/after_sales/input_express?id=' + lists.master_id
                    "
                >
                    <el-button
                        class="apply-btn flex row-center sm"
                        size="small"
                        v-if="lists.btns.express_btn"
                        >填写快递单号
                    </el-button>
                </nuxt-link>
            </div>
        </div>
    </div>
</template>

<script>
import headerMixins from "@/mixins/header";
export default {
    mixins: [headerMixins],
    layout: "user-layout",

    data() {
        return {
            lists: {
                btns: {},
                goods_snap: [],
                shop: {},
            },
        };
    },

    mounted() {
        this.getDetail();
    },

    methods: {
        async cancel(afterSaleId) {
            let res = await this.$post("after_sale/cancel", {
                id: afterSaleId,
            });
            if (res.code == 1) {
                this.$message({
                    message: res.msg,
                    type: "success",
                });
                setTimeout(() => {
                    this.$router.go(-1);
                }, 500);
            }
        },

        async getDetail() {
            let res = await this.$get("after_sale/detail", {
                params: {
                    id: this.$route.query.afterSaleId,
                },
            });
            if (res.code == 1) {
                let goods = [res.data.goods_snap];
                res.data.goods_snap = goods;
                console.log(goods);
                this.lists = res.data;
            }
        },

        onCopy() {
            // this.deliverOrder.invoice_no;
            let oInput = document.createElement("input");
            oInput.value = this.lists.address + this.lists.return_address;
            document.body.appendChild(oInput);
            oInput.select();
            document.execCommand("Copy");
            this.$message.success("复制成功");
            oInput.remove();
        },
    },
};
</script>

<style lang="scss" scoped>
.apply-detail {
    padding: 10px;
    .apply-detail-header {
        padding: 15px 0;
        border-bottom: 1px solid #e5e5e5;
    }
    .apply-detail-address {
        margin: 0 10px;
        padding-top: 16px;
        border-top: 1px solid #e5e5e5;

        .copy {
            margin-left: 20px;
            padding: 2px 6px;
            color: $--color-primary;
            background-color: rgba(255, 44, 60, 0.2);
        }
    }
    .result-content {
        padding: 24px 0px;
        .result-item {
            margin-bottom: 16px;
            &:not(:last-of-type) {
                .label {
                    width: 82px;
                    align-self: flex-start;
                    text-align: right;
                    &::before {
                        content: "* ";
                        color: red;
                    }
                }
            }
            .label {
                width: 82px;
                align-self: flex-start;
                text-align: right;
            }
            .desc {
                margin-left: 24px;
                width: 680px;
            }
        }
    }
    .apply-detail-content {
        .btn-group {
            .apply-btn {
                border: 1px solid #cccccc;
                border-radius: 2px;
                width: 100px;
                height: 32px;
                align-self: flex-start;
                margin-right: 10px;
            }
        }
    }
}
</style>
