<template>
    <div class="user-order bg-white">
        <el-tabs v-model="activeName" @tab-click="handleClick">
            <el-tab-pane
                v-for="(item, index) in order"
                :key="index"
                :label="item.name"
                :name="item.type"
            >
                <template v-if="item.lists.length">
                    <order-list :list="item.lists" @refresh="getOrderList" />
                    <div class="pagination row-center" v-if="item.count">
                        <el-pagination
                            hide-on-single-page
                            background
                            layout="prev, pager, next"
                            :total="item.count"
                            prev-text="上一页"
                            next-text="下一页"
                            :page-size="10"
                            @current-change="changePage"
                        >
                        </el-pagination>
                    </div>
                </template>
                <null-data
                    v-else
                    :img="require('~/assets/images/order_null.png')"
                    text="暂无订单~"
                ></null-data>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>

<script>
import headerMixins from "@/mixins/header";
export default {
    mixins: [headerMixins],
    layout: "user-layout",
    async asyncData({ $get, $post }) {
        const {
            data: { lists, count },
            code,
        } = await $get("order/lists", {
            params: {
                page_size: 10,
                type: "all",
            },
        });
        if (code == 1) {
            return {
                orderList: { lists, count },
            };
        }
    },
    components: {},
    data() {
        return {
            activeName: "all",
            order: [
                {
                    type: "all",
                    lists: [],
                    name: "全部",
                    count: 0,
                    page: 1,
                },
                {
                    type: "pay",
                    lists: [],
                    name: "待付款",
                    count: 0,
                    page: 1,
                },
                {
                    type: "delivery",
                    lists: [],
                    name: "待发货",
                    count: 0,
                    page: 1,
                },
                {
                    type: "take",
                    lists: [],
                    name: "待收货",
                    count: 0,
                    page: 1,
                },
                {
                    type: "finish",
                    lists: [],
                    name: "已完成",
                    count: 0,
                    page: 1,
                },
                {
                    type: "close",
                    lists: [],
                    name: "已关闭",
                    count: 0,
                    page: 1,
                },
            ],
        };
    },
    methods: {
        handleClick() {
            this.getOrderList();
        },
        async getOrderList() {
            const { activeName, order } = this;
            const item = order.find((item) => item.type == activeName);
            const {
                data: { lists, count },
                code,
            } = await this.$get("order/lists", {
                params: {
                    page_size: 10,
                    page_no: item.page,
                    type: activeName,
                },
            });
            if (code == 1) {
                this.orderList = { lists, count };
            }
        },
        changePage(val) {
            this.order.some((item) => {
                if (item.type == this.activeName) {
                    item.page = val;
                }
            });
            this.getOrderList();
        },
    },
    watch: {
        orderList: {
            immediate: true,
            handler(val) {
                this.order.some((item) => {
                    if (item.type == this.activeName) {
                        Object.assign(item, val);
                        console.log(item);
                        return true;
                    }
                });
            },
        },
    },
};
</script>

<style lang="scss">
.user-order {
    padding: 10px 10px 20px;
}
</style>
