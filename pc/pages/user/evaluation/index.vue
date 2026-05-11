<template>
    <div class="user-evaluate">
        <el-tabs v-model="type" @tab-click="handleClick">
            <el-tab-pane
                v-for="(item, index) in evaluate"
                :key="index"
                :label="item.name"
                :name="item.type"
            >
                <template v-if="item.lists.length">
                    <evaluation-list :list="item.lists" :type="item.type" />
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
                    :img="require('~/assets/images/news_null.png')"
                    text="暂无评价~"
                ></null-data>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>

<script>
import headerMixins from "@/mixins/header"
export default {
    mixins: [headerMixins],
    layout: 'user-layout',

    watchQuery: true,
    async asyncData({ $get, query }) {
        const type = query.type || '0'
        let {
            data: { lists, count },
            code,
        } = await $get('goods_comment/commentGoodsLists', {
            params: { type: type, page_size: 10 },
        })

        if(code == 1) {
           return {
               commentList: { lists, count },
               type,
           }
        }
    },
    components: {},
    data() {
        return {
            commentList: {},
            type: '0',
            evaluate: [
                {
                    type: '0',
                    lists: [],
                    name: '待评价',
                    count: 0,
                    page: 1,
                },
                {
                    type: '1',
                    lists: [],
                    name: '已评价',
                    count: 0,
                    page: 1,
                },
            ],
        }
    },
    methods: {
        handleClick() {
            this.getEvaluateList()
        },
        async getEvaluateList() {
            const { type, evaluate } = this
            const item = evaluate.find((item) => item.type == type)
            const {
                data: { lists, count },
                code,
            } = await this.$get('goods_comment/commentGoodsLists', {
                params: {
                    page_size: 10,
                    page_no: item.page,
                    type,
                },
            })
            if (code == 1) {
                this.commentList = { lists, count }
            }
        },

        changePage(val) {
            this.evaluate.some((item) => {
                if (item.type == this.type) {
                    item.page = val
                }
            })
            this.getEvaluateList()
        },
    },
    watch: {
        commentList: {
            immediate: true,
            handler(val) {
                this.evaluate.some((item) => {
                    if (item.type == this.type) {
                        Object.assign(item, val)
                        console.log(item)
                        return true
                    }
                })
            },
        },
    },
}
</script>

<style lang="scss">
.user-evaluate {
    padding: 10px 10px;
}
</style>
