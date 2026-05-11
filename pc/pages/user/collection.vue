<template>
    <div class="user-collection">
        <div class="collection-header lg">
            收藏
        </div>
        <div class="collection-list m-t-10" v-show="goodsCount > 0">
            <div>
                <div class="goods-item flex row-between" v-for="(item, index) in collectList" :key="index">
                    <div style="display: flex;flex-direction: flex;">
                        <el-image style="width: 72px; height: 72px" fit="cover" :src="item.image" lazy />
                        <div class="goods-info">
                            <div class="goods-name line2">{{item.name}}</div>
                            <div class="primary nr" style="font-weight: 500;">¥{{item.min_price}}</div>
                        </div>
                    </div>
                    <div class="flex">
                        <nuxt-link class="to-buy-btn primary flex row-center" :to="'/goods_details/?id='+item.goods_id">去购买</nuxt-link>
                        <el-popconfirm title="确定删除该收藏吗？" confirm-button-text='确定' cancel-button-text='取消'
                            icon="el-icon-info" icon-color="red" @confirm="cancelCollection(item.goods_id)">
                            <div class="cancel-btn lighter flex row-center" slot="reference">取消收藏</div>
                        </el-popconfirm>
                    </div>
                </div>
            </div>
            <div class="flex row-center" style="margin-top: 30px">
                <el-pagination hide-on-single-page background layout="prev, pager, next" :total="goodsCount"
                    prev-text="上一页" next-text="下一页" :page-size="pageSize" @current-change="changePage" />
            </div>
        </div>
        <div class=" data-null" v-show="goodsCount <= 0">
            <img style="width: 150px;height: 150px;" src="@/assets/images/profit_null.png" />
            <div class="muted xs">暂无收藏商品～</div>
        </div>
    </div>
</template>

<script>
import headerMixins from "@/mixins/header"
export default {
    mixins: [headerMixins],
    layout: "user-layout",
    async asyncData({ $post, $get }) {
        let collectList = [];
        let goodsCount = 0;
        let pageSize = 5;
        let res = await $get("collect/lists", {
            params: { page_no: 1, page_size: pageSize },
        });
        if (res.code == 1) {
            collectList = res.data.lists;
            goodsCount = res.data.count;
        }
        return {
            collectList,
            goodsCount,
            pageSize,
        };
    },
    data() {
        return {
            currentPage: 1,
        };
    },
    methods: {
        async changePage(currentPage) {
            this.currentPage = currentPage;
            let res = await this.$get("collect/lists", {
                params: { page_no: currentPage, page_size: this.pageSize },
            });
            if (res.code == 1) {
                this.collectList = res.data.lists;
                this.goodsCount = res.data.count;
            }
        },
        async cancelCollection(id) {
            let res = await this.$post("collect/handleCollectgoods", {
                is_collect: 0,
                goods_id: id,
            });
            if (res.code == 1) {
                this.$message.success("已取消收藏")
                this.changePage(this.currentPage);
            }
        },
    },
};
</script>

<style lang="scss">
.user-collection {
    padding: 10px;
    .collection-header {
        padding: 10px 5px;
        border-bottom: 1px solid $--border-color-base;
    }
    .collection-list {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 690px;
        .goods-item {
            padding: 26px 20px;
            border-bottom: 1px dashed $--border-color-base;
            .goods-info {
                margin-left: 10px;
                width: 500px;
                flex: 1;
                .goods-name {
                    margin-bottom: 8px;
                }
            }
            .to-buy-btn {
                border: 1px solid $--color-primary;
                height: 32px;
                width: 104px;
                font-size: 13px;
            }
            .cancel-btn {
                margin-left: 10px;
                height: 32px;
                width: 104px;
                border: 1px solid $--border-color-base;
                font-size: 13px;
                cursor: pointer;
            }
        }
    }
    .data-null {
        text-align: center;
        padding-top: 100px;
    }
}
</style>