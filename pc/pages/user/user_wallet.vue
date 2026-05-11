<template>
    <div class="user-wallet-container">
        <template v-if="!charge">
            <div class="user-wallet-header lg">我的钱包</div>
            <div class="user-wallet-content">
                <div class="wallet-info-box flex">
                    <div class="user-wallet-info">
                        <div class="xs title">我的余额</div>
                        <div
                            class="nr white flex"
                            style="font-weight: 500; align-items: baseline"
                        >
                            ¥<label style="font-size: 24px">{{
                                wallet.user_money || 0
                            }}</label>
                            <span class="m-l-10 charge" @click="charge = true">
                                充值
                            </span>
                        </div>
                    </div>
                    <div class="user-wallet-info" style="margin-left: 144px">
                        <div class="xs title">累计消费</div>
                        <div
                            class="nr white flex"
                            style="font-weight: 500; align-items: baseline"
                        >
                            ¥<label style="font-size: 24px">{{
                                wallet.total_order_amount || 0
                            }}</label>
                        </div>
                    </div>
                </div>

                <el-tabs
                    v-model="activeName"
                    class="mt10"
                    @tab-click="handleClick"
                >
                    <el-tab-pane
                        v-for="(item, index) in userWallet"
                        :label="item.name"
                        :name="item.type"
                        :key="index"
                    >
                        <user-wallet-table
                            :type="item.type"
                            :list="item.lists"
                        />
                        <div
                            class="pagination row-center m-t-10"
                            v-if="item.count"
                        >
                            <el-pagination
                                hide-on-single-page
                                background
                                layout="prev, pager, next"
                                prev-text="上一页"
                                next-text="下一页"
                                :page-size="10"
                                :total="item.count"
                                @current-change="changePage"
                            >
                            </el-pagination>
                        </div>
                    </el-tab-pane>
                </el-tabs>
            </div>
        </template>
        <template v-else>
            <div class="user-wallet-header lg">充值</div>
            <div class="user-wallet-content m-l-20">
                可用余额
                <span class="m-l-20">¥{{ wallet.user_money || 0 }}</span>
            </div>
            <div class="user-wallet-content m-l-20">充值金额</div>
            <div class="flex">
                <div
                    v-for="(item, index) in rechargeTemplateList.data.lists"
                    :key="item.id"
                    class="charge-container"
                    :class="{ active: activeIndex == index }"
                    @click="handlecharge(index, item.id)"
                >
                    <div>
                        <div class="bold lg">{{ item.money }}元</div>
                        <div class="xs m-t-5">
                            {{ item.tips }}
                        </div>
                    </div>
                </div>
                <el-popover
                    placement="bottom"
                    width="300"
                    trigger="click"
                    v-model="visible"
                >
                    <el-input
                        placeholder="请输入充值金额"
                        v-model="rechargeMoney"
                    >
                        <template slot="prepend">¥</template>
                    </el-input>
                    <div style="text-align: right; margin-top: 5px">
                        <el-button
                            size="mini"
                            type="text"
                            @click="visible = false"
                            >取消</el-button
                        >
                        <el-button
                            type="primary"
                            size="mini"
                            @click="handlecomfirm"
                            >确定</el-button
                        >
                    </div>
                    <div
                        class="charge-container"
                        :class="{ active: activeIndex == -1 }"
                        @click="handlecharge(-1)"
                        slot="reference"
                    >
                        <div class="bold lg" v-if="!hascomfirm">自定义金额</div>
                        <div class="bold lg" v-else>{{ rechargeMoney }}元</div>
                        <div class="xs m-t-5" v-if="!hascomfirm">
                            最低{{ wallet.recharge_min_amount }}元起
                        </div>
                        <div class="xs m-t-5" v-else style="color: red">
                            修改
                        </div>
                    </div>
                </el-popover>
            </div>
            <div
                class="user-wallet-content m-l-20 title lg bold"
                v-if="payWayArr.length"
            >
                请选择支付方式
            </div>
            <div class="flex m-t-16 m-l-20">
                <div class v-for="(item, index) in payWayArr" :key="index">
                    <div
                        class="pay-way flex row-center"
                        @click="orderPay(item.pay_way)"
                    >
                        <img :src="item.icon" alt />
                        <span class="m-l-16 xxl">{{ item.name }}</span>
                    </div>
                </div>
            </div>
        </template>
        <el-dialog
            title="微信支付"
            :visible.sync="showWxpay"
            width="700px"
            center
            @close="clearTimer"
        >
            <div class="flex flex-col row-center black">
                <!-- <img class="pay-code" :src="payInfo.config.code_url" alt=""/> -->
                <vue-qr
                    class="bicode"
                    :logoScale="20"
                    :margin="0"
                    :dotScale="1"
                    :text="payInfo.config.code_url"
                ></vue-qr>
                <div class="m-t-8" style="font-size: 18px">
                    微信扫一扫，完成支付
                </div>
                <div class="pay-money flex">
                    <span>需支付金额：</span>
                    <span class="primary">
                        <price-formate
                            :price="payInfo.config.order_amount"
                            :subscript-size="18"
                            :first-size="28"
                            :second-size="28"
                        />
                    </span>
                </div>
            </div>
        </el-dialog>
    </div>
</template>

<script>
import UserWalletTable from "@/components/userWalletTable.vue";
import headerMixins from "@/mixins/header";
export default {
    mixins: [headerMixins],
    components: {
        UserWalletTable,
    },
    layout: "user-layout",
    data() {
        return {
            showWxpay: false,
            hascomfirm: false,
            payInfo: {
                config: {
                    code_url: "",
                    order_amount: 0,
                },
            },
            order_id: "",
            payWayArr: [],
            rechargeMoney: "",
            visible: false,
            activeIndex: -1,
            charge: false,
            activeName: "bnw",
            payWayArr: [],
            userWallet: [
                {
                    type: "bnw",
                    lists: [],
                    name: "全部记录",
                    count: 0,
                    page: 1,
                },
                {
                    type: "bnw_inc",
                    lists: [],
                    name: "收入记录",
                    count: 0,
                    page: 1,
                },
                {
                    type: "bnw_dec",
                    lists: [],
                    name: "消费记录",
                    count: 0,
                    page: 1,
                },
            ],
        };
    },
    async asyncData({ $get, query }) {
        let wallet = {};
        let recodeList = [];
        let rechargeTemplateList = await $get("recharge/rechargeTemplateLists");
        let walletRes = await $get("user/wallet");
        let recodeRes = await $get("account_log/lists", {
            params: {
                page_no: 1,
                page_size: 10,
                type: "bnw",
            },
        });
        if (walletRes.code == 1) {
            console.log(walletRes);
            wallet = walletRes.data;
        }
        console.log(recodeRes);
        if (recodeRes.code == 1) {
            recodeList = recodeRes.data;
        }
        return {
            wallet,
            recodeList,
            rechargeTemplateList,
        };
    },
    fetch() {
        this.handleClick();
    },
    methods: {
        handleClick() {
            this.getRecodeList();
        },
        async handlecomfirm() {
            if (this.rechargeMoney < this.wallet.recharge_min_amount) {
                return this.$message({
                    message: `充值金额最低${this.wallet.recharge_min_amount}元起`,
                    type: "error",
                });
            }
            this.hascomfirm = true;
            const { data } = await this.$post("recharge/recharge", {
                money: this.rechargeMoney,
                pay_way: 2,
            });
            this.order_id = data.order_id;
            const { code, data: paywayArr } = await this.$get("pay/payway", {
                params: {
                    from: data.from,
                    order_id: data.order_id,
                    scene: 4, // pc端场景为4
                },
            });
            if (code == 1) {
                this.payWayArr = paywayArr.lists;
                if (!this.payWayArr.length)
                    return this.$message({
                        message: "请联系管理员配置支付方式",
                        type: "error",
                    });
            }
            this.visible = false;
        },
        async handlecharge(index, templateid) {
            this.activeIndex = index;
            if (index != -1) {
                const { code: recharge_code, data } = await this.$post(
                    "recharge/recharge",
                    {
                        pay_way: 2,
                        template_id: templateid,
                    }
                );
                if (!recharge_code) return;

                this.order_id = data.order_id;

                const { code, data: paywayArr } = await this.$get(
                    "pay/payway",
                    {
                        params: {
                            from: data.from,
                            order_id: data.order_id,
                            scene: 4, // pc端场景为4
                        },
                    }
                );

                if (code == 1) {
                    this.payWayArr = paywayArr.lists;
                    if (!this.payWayArr.length)
                        return this.$message({
                            message: "请联系管理员配置支付方式",
                            type: "error",
                        });
                }
            }
        },
        async orderPay(payWay) {
            const loading = this.$loading({
                lock: true,
                text: "请稍后...",
                spinner: "el-icon-loading",
            });
            try {
                const { data, code, msg } = await this.$post("pay/prepay", {
                    order_id: this.order_id,
                    pay_way: payWay,
                    // order_source: client,
                    from: "recharge",
                });
                loading.close();
                if (code == 1 && data.pay_way == 2) {
                    // 微信支付
                    this.payInfo = data;
                    this.showWxpay = true;
                    this.createTimer();
                } else if (code == 1 && data.pay_way == 3) {
                    // 支付宝支付
                    let divForm = document.getElementsByTagName("divform");
                    if (divForm.length) {
                        document.body.removeChild(divForm[0]);
                    }
                    const div = document.createElement("divform");
                    div.innerHTML = data.config; // data.config就是接口返回的form 表单字符串
                    document.body.appendChild(div);
                    document.forms[0].submit();
                }
            } catch (error) {
                loading.close();
            }
        },
        clearTimer() {
            clearInterval(this.timer);
        },
        createTimer() {
            if (this.timer) clearInterval(this.timer);
            this.timer = setInterval(() => {
                this.getOrder();
            }, 2000);
        },
        async getOrder() {
            const { data, code, msg } = await this.$get("pay/payStatus", {
                params: {
                    from: "recharge",
                    order_id: this.order_id,
                },
            });
            if (code == 1) {
                this.order = data;
                if (data.pay_status == 1) {
                    clearInterval(this.timer);
                    this.showWxpay = false;
                    this.$message({
                        message: "支付成功",
                        type: "success",
                    });
                }
            }
        },
        changePage(val) {
            this.userWallet.some((item) => {
                if (item.type == this.activeName) {
                    item.page = val;
                }
            });
            this.getRecodeList();
        },

        async getRecodeList() {
            const { activeName, userWallet } = this;
            const item = userWallet.find((item) => item.type == activeName);
            const {
                data: { lists, count },
                code,
            } = await this.$get("account_log/lists", {
                params: {
                    page_size: 10,
                    page_no: item.page,
                    type: activeName,
                },
            });
            if (code == 1) {
                this.recodeList = { lists, count };
            }
        },
    },
    watch: {
        recodeList: {
            immediate: true,
            handler(val) {
                this.userWallet.some((item) => {
                    if (item.type == this.activeName) {
                        Object.assign(item, val);
                        return true;
                    }
                });
            },
        },
    },
};
</script>

<style lang="scss" scoped>
.user-wallet-container {
    padding: 10px 10px 60px 10px;
    .user-wallet-header {
        padding: 10px 5px;
        border-bottom: 1px solid #e5e5e5;
    }
    .user-wallet-content {
        margin-top: 17px;
        .wallet-info-box {
            padding: 24px;
            background: linear-gradient(
                87deg,
                $--color-primary 0%,
                #ff9e2c 100%
            );
            .user-wallet-info {
                .title {
                    color: #ffdcd7;
                    margin-bottom: 8px;
                }
            }
        }
    }
}
.charge {
    cursor: pointer;
}
.charge-container {
    border: 1px solid rgba(230, 230, 230, 1);
    border-radius: 5px;
    text-align: center;
    padding: 20px;
    width: 144px;
    height: 80px;
    margin-left: 20px;
    margin-top: 20px;
    cursor: pointer;
}
.active {
    border: 1px solid rgba(253, 103, 133, 1);
    background-color: #ffe9ee;
}
.pay-way {
    width: 200px;
    height: 68px;
    cursor: pointer;
    margin-right: 32px;
    border: 1px dashed $--border-color-base;

    img {
        width: 30px;
        height: 30px;
    }
}
</style>
