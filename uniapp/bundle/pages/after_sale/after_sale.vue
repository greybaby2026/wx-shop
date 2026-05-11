<template>
    <view :class="themeName">
        <u-tabs
            font-size="28"
            name="tabsName"
            inactive-color="#333"
            :active-color="themeColor"
            :list="list"
            :is-scroll="false"
            :current="current"
            @change="change"
        >
        </u-tabs>

        <mescroll-uni
            ref="mescrollRef"
            bgColor="white"
            top="90rpx"
            @init="mescrollInit"
            :down="downOption"
            :up="upOption"
            @down="downCallback"
            @up="upCallback"
        >
            <view
                class="bg-white p-24 m-b-16"
                @click="goPage('/bundle/pages/after_sale_detail/after_sale_detail', item.sub_id)"
                v-for="(item, index) in lists"
                :key="index"
            >
                <!-- item头部 -->
                <view class="header nr p-b-24 flex row-between">
                    <text>申请时间：{{ item.create_time }}</text>
                    <text>{{ item.refund_method_desc }}</text>
                </view>

                <!-- 商品信息 -->
                <view class="m-t-20 bg-white flex">
                    <u-image
                        :src="item.goods_snap.image"
                        width="180rpx"
                        height="180rpx"
                        mode="aspectFill"
                    ></u-image>
                    <view class="m-l-24" style="width: 100%">
                        <view class="nr line-2 m-b-20">
                            {{ item.goods_snap.goods_name }}
                        </view>
                        <view class="xs muted flex row-between">
                            <price
                                class="m-r-12"
                                :content="item.goods_price"
                                color="#101010"
                                mainSize="28rpx"
                                minorSize="26rpx"
                            ></price>

                            <text>X {{ item.goods_snap.goods_num }}</text>
                        </view>
                    </view>
                </view>

                <view class="apply-ststus lighter nr m-t-20">
                    {{ item.sub_status_desc }}
                </view>

                <view class="footer bg-white p-t-20">
                    <view class="flex row-right">
                        <view
                            class="br60 btn inline m-l-24"
                            @click.stop="openCancel(index)"
                            v-if="item.btns.cancel_btn"
                        >
                            撤销申请
                        </view>

                        <view
                            class="br60 btn inline m-l-24"
                            @click="toExpressInfo(0, item)"
                            v-if="item.btns.express_btn"
                        >
                            填写快递单号
                        </view>

                        <view
                            class="br60 btn inline m-l-24"
                            @click="
                                goPage(
                                    '/bundle/pages/post_after_sale/post_after_sale',
                                    item.order_goods_id
                                )
                            "
                            v-if="item.btns.reapply_btn"
                        >
                            重新申请
                        </view>
                    </view>
                </view>
            </view>
        </mescroll-uni>

        <u-modal
            v-model="showCancel"
            width="540"
            :confirm-style="{ 'border-left': '1rpx solid #e5e5e5' }"
            @confirm="cancelApplyFun"
            :showCancelButton="true"
            :show-title="false"
            @cancel="showCancel = false"
            confirm-text="确定"
            cancel-color="#999999"
            confirm-color="#101010"
            border-radius="10"
        >
            <view class="flex row-center m-t-40 bold" style="color: #101010; font-size: 30rpx">
                取消退款
            </view>
            <view style="margin: 40rpx 30rpx" class="md muted flex row-center">
                <view> 你确定要取消本次退款吗 </view>
            </view>
        </u-modal>
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
import { apiAfterSaleList, apiAfterSaleCancel } from '@/api/order.js'
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins.js'
import MescrollMoreItemMixin from '@/components/mescroll-uni/mixins/mescroll-more-item.js'
export default {
    mixins: [MescrollMixin, MescrollMoreItemMixin],
    data() {
        return {
            showCancel: false, //撤销申请的弹窗
            master_id: '', //撤销申请的ID

            list: [
                {
                    tabsName: '售后中',
                    isShow: true,
                    type: 'status_ing'
                },
                {
                    tabsName: '售后完成',
                    isShow: false,
                    type: 'status_success'
                },
                {
                    tabsName: '售后失败',
                    isShow: false,
                    type: 'status_fail'
                }
            ],
            current: 0,
            lists: [],

            downOption: {
                auto: false // 不自动加载 (mixin已处理第一个tab触发downCallback)
            },
            upOption: {
                auto: true, // 不自动加载
                noMoreSize: 4, //如果列表已无数据,可设置列表的总数量要大于半页才显示无更多数据;避免列表数据过少(比如只有一条数据),显示无更多数据会不好看; 默认5
                empty: {
                    icon: '/static/images/empty/order.png',
                    tip: '暂无售后数据', // 提示
                    fixed: true,
                    top: '200rpx'
                }
            }
        }
    },

    methods: {
        // 去填写快递单号
        toExpressInfo(type, item) {
            this.$Router.push({
                path: '/bundle/pages/input_express_info/input_express_info',
                query: {
                    id: item.master_id,
                    order_id: item.sub_id,
                    change: type
                }
            })
        },
        change(index) {
            for (let i = 0; i < this.list.length; i++) {
                this.list[i].isShow = false
            }
            this.current = index
            this.upCallback({
                num: 1,
                size: 10
            })
            this.list[index].isShow = true
        },

        upCallback(page) {
            let num = page.num
            let size = page.size

            apiAfterSaleList({
                type: this.list[this.current].type,
                page_no: num,
                page_size: size
            })
                .then((res) => {
                    if (page.num == 1) this.lists = [] //如果是第一页需手动置空列表
                    this.lists = this.lists.concat(res.lists) //追加新数据
                    this.mescroll.endSuccess(res.lists.length, res.count)
                })
                .catch((err) => {
                    this.mescroll.endErr()
                })
        },

        openCancel(index) {
            this.showCancel = true
            this.master_id = this.lists[index].master_id
        },

        // 撤销审核
        cancelApplyFun() {
            apiAfterSaleCancel({ id: this.master_id }).then((res) => {
                this.upCallback({
                    num: 1,
                    size: 10
                })
                this.$toast({ title: '取消成功' })
            })
        },

        // 去填写快递单号
        goPage(url, id) {
            this.$Router.push({
                path: url,
                query: {
                    id: id
                }
            })
        }
    },

    onShow() {
        this.upCallback({
            num: 1,
            size: 10
        })
    }
}
</script>
<style lang="scss">
.apply-ststus {
    border-radius: 6px;
    background: #f6f6f6;
    padding: 20rpx 40rpx;
}

.btn {
    padding: 10rpx 40rpx;
    border-width: 1rpx;
    border-style: solid;
    @include font_color();
    @include border_color();
}
</style>
