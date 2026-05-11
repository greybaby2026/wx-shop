<template>
    <view class=""  v-if="flag==true">
        <mescroll-uni ref="mescrollRef" bgColor="white" top="0rpx" @init="mescrollInit" :down="downOption" :up="upOption"
            @down="downCallback" @up="upCallback">
            <view class="recharge-code">
                
                <view class="list">
                    <view v-for="(item, index) in lists" :key="index" class="item bg-white">
                        <view class="flex row-between">
                            <view class="black mb10">{{item.tips}}</view>
                            <view class="xl primary">+{{item.order_amount}}</view>
                        </view>
                        <view class="xs muted">{{item.create_time}}</view>
                    </view>
                </view>
                
            </view>
        </mescroll-uni>
    </view>
</template>

<script>
    // +----------------------------------------------------------------------
    // | LikeShop100%开源免费商用电商系统
    // +----------------------------------------------------------------------
    // | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
    // | 开源版本可自由商用，可去除界面版权logo
    // | 商业版本务必购买商业授权，以免引起法律纠纷
    // | 禁止对系统程序代码以任何目的，任何形式的再发布
    // | Gitee下载：https://gitee.com/likeshop_gitee/likeshop
    // | 访问官网：https://www.likemarket.net
    // | 访问社区：https://home.likemarket.net
    // | 访问手册：http://doc.likemarket.net
    // | 微信公众号：好象科技
    // | 好象科技开发团队 版权所有 拥有最终解释权
    // +----------------------------------------------------------------------
    // | Author: LikeShopTeam
    // +----------------------------------------------------------------------
    import {
        apiRechargeRecord
    } from '@/api/user'
    import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins.js";
    import MescrollMoreItemMixin from "@/components/mescroll-uni/mixins/mescroll-more-item.js";
    export default {
        mixins: [MescrollMixin, MescrollMoreItemMixin],
        data() {
            return {
                flag: false,
                
                lists: [],

                downOption: {
                    auto: false // 不自动加载 (mixin已处理第一个tab触发downCallback)
                },

                upOption: {
                    auto: true, // 不自动加载
                    noMoreSize: 4, //如果列表已无数据,可设置列表的总数量要大于半页才显示无更多数据;避免列表数据过少(比如只有一条数据),显示无更多数据会不好看; 默认5
                    empty: {
                        icon: '/static/images/empty/order.png',
                        tip: '暂无充值记录', // 提示
                        fixed: true,
                        top: "200rpx",
                    }
                }
            }
        },

        methods: {
            upCallback(page) {
                let num = page.num;
                let size = page.size
                apiRechargeRecord({
                    page_no: num,
                    page_size: size
                }).then(res => {
                    console.log(res)
                    if (page.num == 1) this.lists = []; //如果是第一页需手动置空列表
                    this.lists = this.lists.concat(res.lists); //追加新数据
                    this.mescroll.endSuccess(res.lists.length, res.count);
                }).catch(err => {
                    this.mescroll.endErr()
                })
            }
        },
        
        onReady() {
            this.flag = true
            this.upCallback({num: 1,size: 10})
        }
    }
</script>

<style lang="scss">
    .recharge-code {
        padding-top: 20rpx;

        .list {
            .item {
                padding: 20rpx 30rpx;

                &:not(:last-of-type) {
                    border-bottom: 1rpx solid #E5E5E5;
                }
            }
        }
    }
</style>
