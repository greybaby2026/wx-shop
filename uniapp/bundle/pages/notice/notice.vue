<!-- 消息详情 -系统/收益 -->
<template>
   <mescroll-uni ref="notice" bgColor="white" top="0"
   @init="mescrollInit" :up="upOption" @down="downCallback" @up="upCallback">
        <view class="notice-container">
            <view v-for="(item, index) in lists" :key="index" class="notice-item m-b-20">
                <view class="flex row-between item-header">
                    <view class="header-title md">{{item.title}}</view>
                    <view class="header-time muted xs">{{item.create_time}}</view>
                </view>
                <view class="item-main">
                    <view class="content sm lighter">{{item.content}}</view>
                </view>
            </view>

        </view>
        
        <loading-view v-if="loading"></loading-view>
    </mescroll-uni>
</template>

<script>
    import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins.js";
    import {
        apiNoticeListGet
    } from "@/api/store.js"
    export default {
        mixins: [MescrollMixin],
        data() {
            return {
                loading: true,
                type: '',
                lists: [{
                    title: 'hszz',
                    create_time: '2021/8/18 11:00',
                    content: '剑未佩妥，出门已是江湖',
                }, {
                    title: 'hsz',
                    create_time: '2021/8/18 11:00',
                    content: '剑未佩妥，出门已是江湖。剑未佩妥，出门已是江湖剑未佩妥，出门已是江湖。出门已是江湖剑未佩妥，出门已是江湖。出门已是江湖剑未佩妥，出门已是江湖。',
                }],
                
                upOption: {
                    auto: false, // 不自动加载
                    noMoreSize: 4, //如果列表已无数据,可设置列表的总数量要大于半页才显示无更多数据;避免列表数据过少(比如只有一条数据),显示无更多数据会不好看; 默认5
                    empty: {
                        icon: '/static/images/empty/evaluate.png',
                        tip: '暂无消息', // 提示
                        fixed: true,
                        top: "200rpx",
                    }
                }
            };
        },

        methods: {            
            upCallback({ num, size }) {
                apiNoticeListGet({
                	type: this.type,
                	page_no: num,
                	page_size: size,
                }).then((res) => {
                	if (num == 1) this.lists = []; //如果是第一页需手动置空列表
                	this.lists = this.lists.concat(res.lists); //追加新数据
                	this.mescroll.endSuccess(res.lists.length, res.count);
                    
                    setTimeout(() => {
                        this.loading = false
                    }, 500)
                }).catch(err => {
                	ref.endBySize()
                })
            }
        },
        
        onLoad(options) {
            this.type = options.type
        }
    }
</script>

<style lang="scss">
    .notice-container {
        padding: 0 20rpx;
        margin-top: 8rpx;

        .notice-item {
            background-color: white;
            padding: 0rpx 20rpx 30rpx;
            border-radius: 10rpx;

            .item-header {
                padding: 19rpx 0;
                border-bottom: $-solid-border;

                .header-title {
                    font-weight: bold;
                }
            }

            .item-main {
                .content {
                    margin-top: 15rpx;
                }
            }
        }

        // .data-null {
        //     padding-top: 100rpx;
        // }
    }
</style>
