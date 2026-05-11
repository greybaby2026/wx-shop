<template>
    <div class="after-sales">
        <div class="p-10">
            <el-alert title="订单所产生的运费，如已经发货的订单平台是不予退回的，请谨慎操作！" type="warning" show-icon :closable="false"></el-alert>
        </div>
        <div class="after-sales-header">
            <el-tabs v-model="activeName" @tab-click="handleClick">
                <el-tab-pane
                    v-for="(item, index) in afterSale"
                    :key="index"
                    :label="item.name + '(' + extand[index] + ')'"
                    :name="item.type"
                >
                    <template v-if="item.lists.length">
                        <after-sales-list
                            :type="item.type"
                            :lists="item.lists"
                            @refresh="getAfterSaleList"
                            @show="onInputShow"
                        />
                        <div class="pagination row-center m-t-20" v-if="item.count">
                            <el-pagination
                                hide-on-single-page
                                background
                                layout="prev, pager, next"
                                :total="item.count"
                                prev-text="上一页"
                                next-text="下一页"
                                :page-size="10"
                                @current-change="changePage"
                            ></el-pagination>
                        </div>
                    </template>
                </el-tab-pane>
            </el-tabs>
        </div>
    </div>
</template>

<script>
import { AfterSaleType } from "@/utils/type";
import afterSalesList from '@/components/afterSalesList'
import headerMixins from "@/mixins/header"
export default {
    mixins: [headerMixins],
    layout: "user-layout",
    components: {
        afterSalesList
    },
    data() {
        return {
            activeName: AfterSaleType.NORMAL,
            afterSale: [
                {
                    type: AfterSaleType.NORMAL,
                    lists: [],
                    name: "售后申请",
                    count: 0,
                    page: 1,
                },
                {
                    type: AfterSaleType.HANDLING,
                    lists: [],
                    name: "处理中",
                    count: 0,
                    page: 1,
                },
                {
                    type: AfterSaleType.FINISH,
                    lists: [],
                    name: "已处理",
                    count: 0,
                    page: 1,
                },
            ],
        };
    },
    async asyncData({ $get, $post }) {
        let afterList = [];
        let extand = {
            0: 0,
            1: 0,
            2: 0
        }
        let res = await $get("after_sale/lists", {
            params: { type: 'apply', page_no: 1, page_size: 10 },
        });
        console.log(res)
        if (res.code == 1) {
            const { lists, count, extend } = res.data;
            afterList = { lists, count };
            extand[0] = extend['apply']
            extand[1] = extend['ing']
            extand[2] = extend['finish']
        }
        return {
            afterList: afterList,
            extand
        };
    },
    methods: {
        handleClick() {
            this.getAfterSaleList();
        },

        onInputShow(e) {
            this.aid = e;
            this.showInput = true;
        },

        changePage(val) {
            this.afterSale.some((item) => {
                if (item.type == this.activeName) {
                    item.page = val;
                }
            });
            this.getAfterSaleList();
        },
        async getAfterSaleList() {
            const { activeName, afterSale } = this;
            const item = afterSale.find((item) => item.type == activeName);
            const {
                data: { lists, count, extend },
                code,
            } = await this.$get("after_sale/lists", {
                params: {
                    page_size: 10,
                    page_no: item.page,
                    type: activeName,
                },
            });
            if (code == 1) {
                this.afterList = { lists, count };
                this.extend = extend
            }
        },
    },
    watch: {
        afterList: {
            immediate: true,
            handler(val) {
                this.afterSale.some((item) => {
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

<style lang="scss">
.after-sales {
    .after-sales-header {
        padding: 10px;
    }
}
</style>
