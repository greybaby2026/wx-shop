<template>
    <div class="apply-result-container">
        <div class="result-header flex">
            <img style="width: 40px;height: 40px;align-self: flex-start" src="~/assets/images/pay_success.png" />
            <div class="m-l-16">
                <div class="apply-title" style="font-weight: 500;">提交申请</div>
                <div class="muted m-t-8">申请已提交，请耐心等待商家处理…</div>
            </div>
        </div>
        <div class="result-content">
            <div class="result-item flex">
                <div class="label">退款类型：</div>
                <div class="desc">{{lists.refund_method_desc}}</div>
            </div>
            <div class="result-item flex">
                <div class="label">退款原因：</div>
                <div class="desc">{{lists.refund_reason}}</div>
            </div>
            <div class="result-item flex">
                <div class="label">退款金额：</div>
                <div class="desc">
                    <price-formate :price="lists.refund_amount" showSubscript color="red" />
                </div>
            </div>
            <div class="result-item flex">
                <div class="label">退款说明：</div>
                <div class="column desc">
                    <div class="m-b-16">{{lists.refund_remark}}</div>
                    <el-image style="width: 76px;height: 76px;" :src="item" v-for="(item,index) in lists.voucher" :key="index"
                        :preview-src-list="lists.voucher" />
                </div>
            </div>
        </div>

        <div class="">
            <el-button class="return-btn flex row-center sm" size="small" @click.stop="$router.go(-2)">返回</el-button>
        </div>
    </div>
</template>

<script>
import headerMixins from "@/mixins/header"
export default {
    mixins: [headerMixins],
    layout: "user-layout",
    async asyncData({ $get, query }) {
        const data = {
            id: query.afterSaleId,
        };
        let lists = {};
        let res = await $get("after_sale/detail", { params: data });
        if (res.code == 1) {
            lists = res.data;
        }
        return {
            lists,
        };
    },
    data() {
        return {};
    },
    methods: {},
};
</script>

<style lang="scss" scoped>
.apply-result-container {
    padding: 10px;
    .result-header {
        padding: 46px 20px;
        border-bottom: 1px solid #e5e5e5;
        .apply-title {
            font-size: 24px;
        }
    }
    .result-content {
        padding: 24px 20px;
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
    .return-btn {
        width: 120px;
        height: 36px;
        margin-left: 126px;
    }
}
</style>
