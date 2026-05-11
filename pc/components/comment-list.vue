<template>
    <div class="comment-list">
        <div class="comment-con">
            <template v-if="commentList.length">
                <div class="item flex" v-for="(item, index) in commentList" :key="index">
                    <div class="avatar m-r-8">
                        <img :src="item.avatar" alt="" />
                    </div>
                    <div class="content flex-1">
                        <div>{{ item.nickname }}</div>
                        <div class="lighter" style="margin: 5px 0 10px">
                            <span>{{ item.create_time }}</span>
                            <span>|</span>
                            <span>规格：{{ item.spec_value_str }}</span>
                        </div>
                        <div>
                            {{ item.comment }}
                        </div>
                        <div class="comment-imglist flex">
                            <div class="item" v-for="(img, index) in item.image" :key="index">
                                <el-image :preview-src-list="item.image" :src="img" style="height: 100%; width: 100%"
                                    fit="contain"></el-image>
                            </div>
                        </div>
                        <div class="flex reply m-t-16" v-if="item.reply">
                            <div class=" flex-none">商家回复：</div>
                            <div class="lighter">
                                {{ item.reply }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pagination flex row-center" style="padding: 38px 0" v-if="count">
                    <el-pagination background hide-on-single-page layout="prev, pager, next" :total="count"
                        prev-text="上一页" next-text="下一页" :page-size="10" @current-change="changePage">
                    </el-pagination>
                </div>
            </template>
            <null-data v-else :img="require('~/assets/images/news_null.png')" text="暂无评价~"></null-data>
        </div>
    </div>
</template>

<script>
    export default {
        components: {},
        props: {
            list: {
                type: Array,
                default: () => [],
            },
            type: Number,
            goodsId: [String, Number],
        },
        data() {
            return {
                commentList: [],
                count: 0,
                page: 1,
            };
        },
        created() {
            this.getCommentList();
        },
        methods: {
            async getCommentList() {
                const {
                    data,
                    code
                } = await this.$get("pc/goodsCommentLists", {
                    params: {
                        id: this.type, // 0-全部；1-晒图；2-好评；3-中评；4-差评；
                        goods_id: this.goodsId,
                        page_size: 10,
                        page_no: this.page,
                    },
                });
                if (code == 1) {
                    this.commentList = data.lists;
                    this.count = data.count;
                }
            },
            changePage(current) {
                this.page = current;
                this.getCommentList();
            },
        },
        
        
        // 监听商品id变化
        watch: {
            goodsId(val) {
                this.getCommentList()
            }
        },
    };
</script>

<style lang="scss" scoped>
    .comment-list {
        .comment-con {
            &>.item {
                padding: 20px;
                border-bottom: 1px dashed #e5e5e5;
                align-items: flex-start;

                .avatar {
                    img {
                        border-radius: 50%;
                        width: 44px;
                        height: 44px;
                    }
                }

                .comment-imglist {
                    margin-top: 10px;

                    .item {
                        width: 80px;
                        height: 80px;
                        margin-right: 6px;
                    }
                }

                .reply {
                    background-color: #f0f0f0;
                    align-items: flex-start;
                    padding: 10px;
                }
            }
        }
    }
</style>
