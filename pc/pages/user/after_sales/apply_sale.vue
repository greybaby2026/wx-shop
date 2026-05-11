<template>
    <div class="apply-sale-list">
        <div class="goods-info">
            <div class="table-head flex">
                <div class="info">商品信息</div>
                <div class="num flex row-center">数量</div>
                <div class="act-pay flex row-center">实付</div>
            </div>
            <div class="table-content flex mt10">
                <div class="info flex">
                    <div class="flex">
                        <el-image style="width: 72px;height: 72px; flex: none" :src="goods.image" />
                        <div class="m-l-10" style="flex: 1; align-self: flex-start">
                            <div class="line2">{{goods.goods_name}}</div>
                            <div class="m-t-10 muted sm">{{goods.spec_value_str}}</div>
                        </div>
                    </div>
                </div>
                <div class="num flex row-center" style="align-self: flex-start">
                    {{goods.goods_num}}
                </div>
                
                <div class="act-pay flex row-center" style="align-self: flex-start">
                    <price-formate :price="orderInfo.total_pay_price" />
                </div>
            </div>
        </div>
        <div class="apply-form">
            <el-form ref="form" :model="form" :rules="rules" label-width="100px">
                <el-form-item label="退款类型：" prop="applyType">
                    <el-radio-group v-model="applyType" @change="applyRadioChange">
                        <el-radio label="仅退款"></el-radio>
                        <el-radio label="退货退款"></el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="退款原因：" prop="reason">
                    <el-select v-model="form.reason" placeholder="请选择">
                        <el-option :label="item" :value="item" v-for="(item, index) in reason[applyType=='仅退款'?1:2]" :key="index" />
                    </el-select>
                </el-form-item>
                <el-form-item label="退款说明：" prop="desc">
                    <el-input style="width: 600px" type="textarea" v-model="form.desc" placeholder="退款说明（200字以内）"
                        maxlength="200" show-word-limit resize="none" rows="5" />
                </el-form-item>
                <el-form-item>
                    <upload isSlot :file-list="fileList" @success="uploadSuccess" :limit="3">
                        <div class="column-center" style="height: 100%">
                            <i class="el-icon-camera xs" style="font-size: 24px" />
                            <!-- <div class="muted xs" style="line-height: 0px">上传凭证</div> -->
                        </div>
                    </upload>
                    <div class="xs muted">最多可上传3张图片，支持jpg、png格式，图片大小1M以内</div>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="onSubmit">提交申请</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script>
import upload from "@/components/public/upload";
import headerMixins from "@/mixins/header"
export default {
    mixins: [headerMixins],
    components: {
        upload,
    },
    layout: "user-layout",
    name: "applySale",
    async asyncData({ $get, $post, query }) {
        let goods = {};
        let reason = [];
        let res = await $get("pc/afterSaleApplyPage", {
            params: {
                order_goods_id: query.order_id
            },
        });
        console.log(res)
        if (res.code == 1) {
            goods = res.data.goods_snap;
            reason = res.data.reason;
        }
        return {
            reason,
            goods,
            orderInfo: res.data
        };
    },
    data() {
        return {
            applyType: "仅退款",
            form: {
                applyType: 1,
                reason: "",
                desc: "",
            },
            rules: {
                applyType: [{ required: true, message: "请选择退款类型" }],
                reason: [
                    {
                        required: true,
                        message: "请选择退款原因",
                        triggle: "blur",
                    },
                ],
            },
            fileList: [],
        };
    },
    methods: {
        applyRadioChange(value) {
            value == "仅退款"
                ? (this.form.applyType = 1)
                : (this.form.applyType = 2);
        },

        onSubmit(e) {
            this.$refs["form"].validate((valid) => {
                if (valid) {
                    if (this.$route.query.afterSaleId) {
                        this.applyAgainFun();
                    } else {
                        this.$applyAfterSale();
                    }
                } else {
                    return false;
                }
            });
        },

        onUploadChange(e) {
            let fileList = Object.assign([], this.fileList);
            fileList.push(e);
            this.fileList = fileList;
            console.log("onChange", e, " fileList:", this.fileList);
        },

        uploadSuccess(e) {
            let fileList = Object.assign([], e);
            this.fileList = fileList;
            console.log("onSuccess", e, " fileList:", this.fileList);
        },

        // 重新申请
        async $applyAgain() {
            let fileList = [];
            this.fileList.forEach((item) => {
                fileList.push(item.response.data);
            });
            const data = {
                id: this.$route.query.afterSaleId,
                reason: this.form.reason,
                refund_type: this.form.applyType,
                remark: this.form.desc,
                img: fileList.length <= 0 ? "" : fileList[0].base_url,
            };
            let res = await $post("after_sale/again", data);
            if (res.code == 1) {
                this.$message({
                    message: "提交成功",
                    type: "success",
                });
                this.$router.push(
                    "/user/after_sales/apply_result?afterSaleId=" +
                        res.data.after_sale_id
                );
            }
        },

        // 初次申请售后
        async $applyAfterSale() {
            let fileList = [];
            this.fileList.forEach((item) => {
              fileList.push(item.response.data.uri);
            });
              console.log(fileList)

            const data = {
                order_goods_id: this.$route.query.order_id,
                refund_reason: this.form.reason,
                refund_method: this.form.applyType,
                refund_remark: this.form.desc,
                // 目前只支持单个
                voucher: fileList,
            };
            let res = await this.$post("after_sale/apply", data);
            if (res.code == 1) {
                this.$message({
                    message: "提交成功",
                    type: "success",
                });
                this.$router.push(
                    "/user/after_sales/apply_result?afterSaleId=" +
                        res.data.after_sale_goods_id
                );
            }
        },
    },
};
</script>

<style lang="scss" scoped>
.apply-sale-list {
    padding: 10px;
    .goods-info {
        .table-head {
            padding: 10px 20px;
            border-bottom: 1px solid #e5e5e5;
        }
        .table-content {
            padding: 10px 20px;
            border-bottom: 1px solid #e5e5e5;
        }
        .info {
            width: 500px;
        }
        .price {
            width: 100px;
        }
        .num {
            width: 100px;
        }
        .sum {
            width: 100px;
        }
        .act-pay {
            width: 100px;
        }
    }
    .apply-form {
        margin-top: 24px;
    }
}
</style>