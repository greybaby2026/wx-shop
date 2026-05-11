<template>
    <view class="order-manage">
        <!-- Header -->
        <view class="header bg-white">
            <u-search
                placeholder="请输入订单编号/商品名称"
                :height="72"
                :show-action="false"
                @search="change(current)"
                v-model="keyword"
            ></u-search>
        </view>

        <!-- Nav -->
        <view class="nav bg-white">
            <u-tabs
                name="name"
                :show-bar="true"
                :list="list"
                :is-scroll="true"
                :height="80"
                :current="current"
                :offset="[5, 0]"
                @change="change"
                active-color="#3868F9"
            />
        </view>
        <!-- Section -->
        <view class="section">
            <swiper
                :duration="400"
                style="height: 100%; overflow: hidden"
                @change="change"
                :current="current"
            >
                <swiper-item v-for="(items, index) in list" :key="index">
                    <view style="height: 130%" v-show="index == current">
                        <mescroll-uni
                            ref="mescrollRef"
                            @init="mescrollInit"
                            @up="upCallback"
                            :up="upOption"
                            @down="downCallback"
                            height="100%"
                            bottom="200rpx"
                        >
                            <view class="title muted sm"> 共{{ items.number }}笔订单 </view>

                            <block v-for="(items2, index2) in items.lists" :key="index2">
                                <order-card :data="items2">
                                    <template v-if="items2.businesse_btn.editaddress_btn">
                                        <router-link
                                            :to="'/pages/address_edit/address_edit?id=' + items2.id"
                                        >
                                            <button class="btn hollow br60 flex row-center normal">
                                                修改地址
                                            </button>
                                        </router-link>
                                    </template>
                                    <template v-if="items2.businesse_btn.content_btn">
                                        <router-link
                                            :to="'/pages/order_detail/order_detail?id=' + items2.id"
                                        >
                                            <button class="btn hollow br60 flex row-center normal">
                                                发货内容
                                            </button>
                                        </router-link>
                                    </template>
                                    <template v-if="items2.businesse_btn.confirm_pay_btn">
                                        <button
                                            class="btn solid br60 flex row-center normal"
                                            @click="openFunc(items2, 'confirmpay')"
                                        >
                                            确认付款
                                        </button>
                                    </template>
                                    <template v-if="items2.businesse_btn.cancel_btn">
                                        <button
                                            class="btn hollow br60 flex row-center normal"
                                            @click="openFunc(items2, 'close')"
                                        >
                                            取消订单
                                        </button>
                                    </template>
                                    <template v-if="items2.businesse_btn.deliver_btn">
                                        <router-link
                                            :to="
                                                '/bundle/pages/deliver_goods/deliver_goods?id=' +
                                                items2.id +
                                                '&type=' +
                                                items2.order_type
                                            "
                                        >
                                            <button class="btn solid br60 flex row-center normal">
                                                去发货
                                            </button>
                                        </router-link>
                                    </template>
                                    <template v-if="items2.businesse_btn.confirm_btn">
                                        <button
                                            class="btn solid br60 flex row-center normal"
                                            @click="openFunc(items2, 'confirm')"
                                        >
                                            确认收货
                                        </button>
                                    </template>
                                    <template v-if="items2.businesse_btn.logistics_btn">
                                        <button
                                            class="btn hollow br60 flex row-center normal"
                                            @click="toTraces(items2.id)"
                                        >
                                            查看物流
                                        </button>
                                    </template>

                                    <template v-if="items2.businesse_btn.verification_btn">
                                        <!-- <router-link :to="'/pages/verification_order/verification_order'"> -->
                                        <button
                                            @click="openFunc(items2, 'verification')"
                                            class="btn solid br60 flex row-center normal"
                                        >
                                            提货核销
                                        </button>
                                        <!-- </router-link> -->
                                    </template>
                                </order-card>
                            </block>
                        </mescroll-uni>
                    </view>
                </swiper-item>
            </swiper>
        </view>

        <modal height="100rpx" v-model="close" @confirm="orderSetting">
            <view class="black n" style="height: 200rpx"> 确认取消该订单吗? </view>
        </modal>

        <modal height="100rpx" v-model="del" @confirm="orderSetting">
            <view class="black nr" style="height: 200rpx"> 确认删除该订单吗? </view>
        </modal>

        <modal height="100rpx" v-model="confirm" @confirm="orderSetting">
            <view class="black nr" style="height: 200rpx"> 确认用户已收到货？请谨慎处理！ </view>
        </modal>
        <modal height="100rpx" v-model="confirmpay" @confirm="orderSetting">
            <view class="black nr" style="height: 200rpx"> 确认用户已付款？请谨慎处理！ </view>
        </modal>
        <modal height="100rpx" v-model="verification" @confirm="orderSetting">
            <view class="black nr" style="height: 200rpx"> 确认核销该订单？ </view>
        </modal>
        <modal height="100rpx" v-model="verificationDialog" @confirm="orderSetting(1)">
            <view class="black nr" style="height: 200rpx">
                {{ verifyTips }}
            </view>
        </modal>

        <u-modal :cancelShow="false" title="查看物流" v-model="recycle">
            <view class="black nr p-40" style="height: 200rpx">
                <view>快递公司： {{ logistics.express_name || '-' }}</view>
                <view class="m-t-20"
                    >物流单号： {{ logistics.invoice_no || '-' }}
                    <text
                        v-if="logistics.invoice_no"
                        class="copy"
                        @click="copy(logistics.invoice_no)"
                        >复制</text
                    ></view
                >
            </view>
        </u-modal>

        <u-toast ref="uToast" />
    </view>
</template>

<script>
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins.js'
import {
    apiOrderList,
    apiOrderClose,
    apiOrderConfirm,
    apiOrderLogistics,
    apiOrderDelete,
    apiVerificationOrderConfirm,
    apiOrderConfirmpay
} from '@/api/order'
import { debounce, copy } from '@/utils/tools.js'
export default {
    mixins: [MescrollMixin],
    data() {
        return {
            keyword: '',
            list: [
                {
                    name: '待付款',
                    type: 0,
                    number: 0,
                    lists: []
                },
                {
                    name: '待发货',
                    type: 1,
                    number: 0,
                    lists: []
                },
                {
                    name: '待收货',
                    type: 2,
                    number: 0,
                    lists: []
                },
                {
                    name: '已完成',
                    type: 3,
                    number: 0,
                    lists: []
                },
                {
                    name: '全部',
                    type: '',
                    number: 0,
                    lists: []
                }
            ],
            current: 0,

            action: '',
            verifyTips: '',

            close: false,
            del: false,
            confirm: false,
            confirmpay: false,
            recycle: false,
            verification: false,
            verificationDialog: false,
            // 物流
            logistics: {
                shipping_name: '-',
                invoice_no: ''
            },

            upOption: {
                empty: {
                    icon: '/static/images/empty/order.png',
                    tip: '暂无相关订单！' // 提示
                }
            }
        }
    },

    updated() {},

    methods: {
        toTraces(id) {
            this.$Router.push({
                path: '/bundle/pages/orderTraces/orderTraces',
                query: { id }
            })
        },
        change(event) {
            let index
            event.detail ? (index = event.detail.current) : (index = event)
            this.current = Number(index)
            this.$refs.mescrollRef[this.current].mescroll.resetUpScroll()
        },

        upCallback(page) {
            const index = this.current
            const pageNum = page.num
            const pageSize = page.size

            apiOrderList({
                keyword: this.keyword,
                type: this.list[index].type,
                page_no: pageNum,
                page_size: pageSize
            })
                .then(({ lists, size, more, count }) => {
                    // 如果是第一页或是搜索时需手动置空列表
                    if (pageNum == 1 || this.keyword) this.list[index].lists = []
                    // 重置列表数据
                    this.list[index].number = count
                    this.list[index].lists = [...this.list[index].lists, ...lists]
                    this.$refs.mescrollRef[index].mescroll.endSuccess(lists.length, count)
                })
                .catch((err) => {
                    this.$refs.mescrollRef[index].mescroll.endErr()
                })
        },

        copy(content) {
            copy(content)
        },

        async openFunc(item, action) {
            if (action == 'recycle') {
                const res = await apiOrderLogistics({
                    id: item.id
                })
                this.logistics = res.order
            }

            this.curData = item
            this.action = action
            this[action] = true
            console.log(this[action])
        },

        // 操作订单
        async orderSetting(isConfirm = 0) {
            let id = this.curData.id,
                action = this.action

            if (action == 'close') {
                await apiOrderClose({
                    id
                })
            } else if (action == 'del') {
                await apiOrderDelete({
                    id
                })
            } else if (action == 'confirm') {
                await apiOrderConfirm({
                    id
                })
            } else if (action == 'confirmpay') {
                await apiOrderConfirmpay({
                    id
                })
            } else if (action == 'verification') {
                const res = await apiVerificationOrderConfirm({ id, confirm: isConfirm })
                if (res.code === 10) {
                    this.verifyTips = res.msg
                    this.verificationDialog = true
                    return
                }
            }

            this.$refs.mescrollRef[this.current].mescroll.resetUpScroll()
        }
    },

    onShow() {
        // 使用防抖是为了防止v-show的时候出发多条数据，所以使用防抖触发多次的时候可以只成为触发一次，优化性能请求
        this.upCallback = debounce(this.upCallback, 200, this)
        // this.$nextTick(() => {
        //     this.$refs.mescrollRef[this.current].mescroll.resetUpScroll()
        // })
        // uni.getSystemInfo({
        //     success: (res) => {
        //         this.height = res.windowHeight - 107 + 'px';
        //     }
        // });
    }
}
</script>

<style lang="scss">
/*根元素需要有固定的高度*/
page {
    height: 100%;
    box-sizing: border-box;

    .order-manage {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .header {
        padding: 14rpx 24rpx;
        border-bottom: $-solid-border;
    }

    .section {
        overflow: hidden;
        flex: 1;
        min-height: 0;

        .title {
            padding: 20rpx;
        }

        .btn {
            margin-left: 20rpx;
            width: 180rpx;
            height: 64rpx;
            font-size: $-font-size-sm;
        }

        .solid {
            color: $-color-white;
            background-color: $-color-primary;
        }

        .hollow {
            color: $-color-lighter;
            border: 1px solid #dbdbdb;
        }
    }

    .copy {
        font-size: $-font-size-xs;
        margin-left: 20rpx;
        padding: 4rpx 10rpx;
        border-radius: 4rpx;
        color: $-color-primary;
        background-color: rgba(64, 175, 250, 0.1);
    }
}
</style>
