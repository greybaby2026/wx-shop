<template>
    <div class="evaluation-list">
        <!-- 待评价 -->
        <div class="list1" v-if="type == 0">
            <div class="item flex" v-for="(item, index) in list" :key="index">
                <div class="goods">
                    <div class="goods-item flex ">
                        <el-image class="goods-img" :src="item.goods_image" alt="" />
                        <div class="goods-info flex1">
                            <div class="goods-name flex  row-between" style="align-items: flex-start;">
                                <div class="line-1" style="width: 600px">
                                    {{ item.goods_name }}
                                </div>
                                <div class="operate flex-col row-end">
                                    <nuxt-link :to="`/user/evaluation/evaluate?id=${item.id}`"
                                        class="btn lighter sm flex row-center">去评价</nuxt-link>
                                </div>
                            </div>
                            <div class="sm lighter m-b-8">{{item.spec_value_str}}</div>
                            <div class="primary">
                                <price-formate :price="item.goods_price" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 已评价 -->
        <div class="list2" v-if="type == 1">
            <div class="item flex col-top" v-for="(item, index) in list" :key="index">
                <div class="">
                    <el-image :src="publicData.my.avatar" style="height: 44px; width: 44px; border-radius: 50%;"></el-image>
                </div>
                <div class="m-l-20" v-if="item.goods_comment">
                   <div class="lg m-b-10">
                       {{ publicData.my.nickname }}
                   </div>
                   <div class="muted sm">评价时间：{{ item.goods_comment.create_time }}</div>
                   <div class="m-t-10">
                       {{ item.goods_comment.comment }}
                   </div>
                   <div class="comment-imglist flex ">
                       <div class="item" v-for="(img, index) in item.goods_comment.goods_comment_image" :key="index">
                           <el-image :preview-src-list="item.goods_comment.goods_comment_image.map(it => it.uri)" :src="img.uri" style="height: 100%; width: 100%"
                               fit="contain"></el-image>
                       </div>
                   </div>
                   <div class="flex  reply m-t-16" v-if="item.goods_comment.reply">
                   <!-- <div class="flex  reply m-t-16"> -->
                       <div class=" flex-none">商家回复：</div>
                       <div class="lighter">
                           {{ item.goods_comment.reply }}
                       </div>
                   </div>
                   <div class="goods flex m-t-16">
                       <el-image class="goods-img" :src="item.goods_image" alt="" />
                       <div class="goods-info m-l-10">
                           <div class="line1 mb8">{{item.goods_name}}</div>
                           <div class="sm lighter m-b-8">{{item.spec_value_str}}</div>
                           <div class="m-t-8 primary">
                               <price-formate :price="item.goods_price" />
                           </div>
                       </div>
                   </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    import { mapMutations, mapActions, mapState } from 'vuex'
    export default {
        props: {
            list: {
                type: Array,
                default: () => [],
            },
            type: {
                type: String,
            },
        },


        computed: {
            ...mapState(['publicData']),
        },
    };
</script>

<style lang="scss" scoped>
    .evaluation-list {
        padding: 0 10px;

        .list1 {
            .item {
                border-bottom: 1px solid #e5e5e5;

                .item-hd {
                    height: 40px;
                    background: #f2f2f2;
                    padding: 0 20px;

                    .status {
                        width: 300px;
                        text-align: right;
                    }
                }

                align-items: stretch;

                .goods {
                    padding: 17px 0;

                    .goods-item {
                        padding: 10px 20px;

                        .goods-img {
                            margin-right: 10px;
                            width: 72px;
                            height: 72px;
                        }
                    }
                }

                .operate {
                    width: 200px;

                    .btn {
                        width: 104px;
                        height: 32px;
                        border: 1px solid #E5E5E5;
                        border-radius: 2px;
                        cursor: pointer;
                    }
                }
            }
        }

        .list2 {
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

                .goods {
                    padding: 15px;
                    background-color: #f0f0f0;
                    width: 930px;
                    .goods-img {
                        width: 72px;
                        height: 72px;
                    }
                }
            }
        }
    }
</style>
