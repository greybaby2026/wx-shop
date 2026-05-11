<template>
    <view class="order-logistics u-skeleton" :class="themeName">
        <view class="express">
            <template v-if="parcel.length > 1">
                <view style="color: white" v-if="pagerData.express_status == 2">
                    商家已部分发货，剩余商品将会尽快发出
                </view>
                <view style="color: white" v-if="pagerData.express_status == 1">
                    你的订单被分成多个包裹发出
                </view>
            </template>

            <scroll-view
                :scroll-top="scrollTop"
                scroll-x="true"
                class="scroll-view m-t-30"
                v-if="parcel.length > 1"
            >
                <view
                    class="pacel m-l-10"
                    v-for="(item, index) in parcel"
                    :key="item.id"
                    @click="handleParcel(index)"
                    :class="activeParcel == index ? 'active-parcel' : ''"
                >
                    <template v-if="item.send_type == 1">
                        <view> 包裹{{ index + 1 }}</view>
                        <view class="xxs" :class="activeParcel == index ? 'muted' : ''">
                            共{{ deliverNum(item.order_goods_info) }}件</view
                        >
                    </template>
                    <template v-if="item.send_type == 2">
                        <view> 无需物流</view>
                        <view class="xxs" :class="activeParcel == index ? 'muted' : ''">
                            共{{ deliverNum(item.order_goods_info) }}件</view
                        >
                    </template>
                </view>
                <view
                    class="pacel m-l-10"
                    v-if="pagerData.express_status == 2"
                    :class="activeParcel == 'wait_goods' ? 'active-parcel' : ''"
                    @click="activeParcel = 'wait_goods'"
                >
                    <view> 待发货</view>
                    <view class="xxs" :class="activeParcel == index ? 'muted' : ''">
                        共{{ waitNum() }}件</view
                    >
                </view>
            </scroll-view>
            <view class="express-card p-24" v-if="activeParcel != 'wait_goods'">
                <template v-if="parcel_info.send_type == 1">
                    <view class="flex row-between">
                        <view class="flex">
                            <u-image
                                v-if="parcel_info"
                                :src="$getImageUri(parcel_info.express_icon)"
                                width="40rpx"
                                height="40rpx"
                                mode="aspectFill"
                            ></u-image>
                            <view class="sm m-l-10">
                                {{ parcel_info.express_name }} {{ parcel_info.invoice_no }}
                            </view>
                        </view>
                        <view class="sm muted" @click="onCopy(parcel_info.invoice_no)">
                            复制单号
                        </view>
                    </view>
                    <view class="m-t-24" v-if="parcel_info">
                        <u-time-line>
                            <u-time-line-item
                                v-for="(item, index) in parcel_info.logistics_info.traces"
                                :key="index"
                                v-show="index < 1 || down"
                            >
                                <template v-slot:content>
                                    <view v-if="item == '暂无物流信息'">
                                        <view class="u-order-desc">暂无物流信息</view>
                                        <view class="u-order-time muted xxs">暂无物流信息</view>
                                    </view>
                                    <view v-else>
                                        <view class="u-order-desc">{{ item[1] }}</view>
                                        <view class="u-order-time muted">{{ parcel_info.logistics_info.express_field === 'code100' ? item[0] : item[2] }}</view>
                                    </view>
                                </template>
                            </u-time-line-item>
                        </u-time-line>
                        <view
                            class="xs m-l-30"
                            style="color: rgba(33, 137, 255, 1)"
                            @click="down = true"
                            v-if="!down && parcel_info.logistics_info.traces.length > 1"
                        >
                            查看物流轨迹 <u-icon name="arrow-down"></u-icon
                        ></view>
                        <view
                            class="xs m-l-30"
                            style="color: rgba(33, 137, 255, 1)"
                            @click="down = false"
                            v-if="down && parcel_info.logistics_info.traces.length > 1"
                        >
                            收起
                            <u-icon name="arrow-up"></u-icon
                        ></view>
                    </view>
                    <view class="m-t-20 m-b-20">
                        <u-divider half-width="350" :useSlot="false"></u-divider>
                    </view>
                </template>
                <template v-if="parcel_info.send_type == 2">
                    <view class="m-l-24 m-r-24 m-b-24">无需物流</view>
                </template>
                <view class="flex">
                    <view class="icon_acept muted xxs"> 收 </view>
                    <view class="m-l-20">
                        <view class="">{{ receipt_address_info.addresss }} </view>
                        <view class="xs muted">
                            {{ receipt_address_info.contact }}
                            {{ receipt_address_info.mobile }}
                        </view>
                    </view>
                </view>
            </view>
            <view class="express-card p-24" v-if="activeParcel != 'wait_goods'">
                <view
                    class="flex m-b-20"
                    v-for="(item, index) in parcel_info.order_goods_info"
                    :key="index"
                >
                    <u-image
                        :src="item.goods_image"
                        width="100rpx"
                        height="100rpx"
                        mode="aspectFill"
                    ></u-image>
                    <view class="flex-col m-l-20 row-around" style="height: 100rpx; width: 100%">
                        <view class="line line"> {{ item.goods_name }} </view>
                        <view class="flex row-between">
                            <view class="muted"> ¥{{ item.goods_price }} </view>
                            <view class="muted"> x{{ item.delivery_num }} </view>
                        </view>
                    </view>
                </view>
            </view>
            <view class="express-card p-24" v-if="activeParcel == 'wait_goods'">
                <view class="flex m-b-20" v-for="(item, index) in wait_delivery_goods" :key="index">
                    <u-image
                        :src="item.goods_image"
                        width="100rpx"
                        height="100rpx"
                        mode="aspectFill"
                    ></u-image>
                    <view class="flex-col m-l-20 row-around" style="height: 100rpx; width: 100%">
                        <view class="line line"> {{ item.goods_name }} </view>
                        <view class="flex row-between">
                            <view class="muted">
                                {{ item.goods_price }}
                            </view>
                            <view class="muted"> x{{ item.goods_num - item.delivery_num }} </view>
                        </view>
                    </view>
                </view>
            </view>
        </view>
        <u-skeleton :loading="loadingSkeleton" :animation="true" bgColor="#FFFFFF" />
    </view>
</template>

<script>
import { apiOrderLogistics } from '@/api/order'
import { apiIntegralOrderTraces } from '@/api/integral_mall'
import { copy } from '@/utils/tools.js'

export default {
    name: 'OrderLogistics',

    data() {
        return {
            loadingSkeleton: true,
            pagerData: {},
            parcel: [{ send_type: '' }],
            parcel_info: {
                express_icon: '',
                order_goods_info: []
            },
            receipt_address_info: {
                contact: '',
                mobile: ''
            },
            down: false,
            activeParcel: 0,
            wait_delivery_goods: []
        }
    },
    watch: {
        parcel: {
            handler(val) {
                this.parcel_info = val[0]
            },
            deep: true,
            immediate: true
        }
    },
    methods: {
        onCopy(str) {
            copy(str)
        },
        // 初始化物流数据
        initLogisticsData() {
            const api =
                this.type == 'integral'
                    ? apiIntegralOrderTraces({
                          id: this.order_id
                      })
                    : apiOrderLogistics({
                          id: this.order_id
                      })
            api.then((data) => {
                this.pagerData = data
                this.parcel = data.parcel_info
                this.receipt_address_info = data.receipt_address_info
                this.wait_delivery_goods = data.wait_delivery_goods
                // this.order = data.order
                // this.take = data.take
                // this.finish = data.finish
                // this.shipment = data.shipment
                // this.pay = data.pay
                // this.buy = data.buy
                return data
            }).then((data) => {
                this.loadingSkeleton = false

                // const traces = data.delivery?.traces
                // 【运输中】数据结构格式化
                // if (traces.length) {
                //     const newTrace = traces.shift()
                //     data.delivery = {
                //         ...data.delivery,
                //         new_trace: newTrace
                //     }
                // }
                // this.delivery = data.delivery
                // this.loadingSkeleton = false
            })
        },

        handleParcel(index) {
            this.activeParcel = index
            this.parcel_info = this.parcel[index]
        },
        deliverNum(val) {
            let num = 0
            val.map((i) => {
                num += Number(i.delivery_num)
            })
            return num
        },
        waitNum() {
            let num = 0
            this.wait_delivery_goods.map((i) => {
                num += Number(i.goods_num - i.delivery_num)
            })
            return num
        }
    },

    async onLoad() {
        const options = this.$Route.query

        try {
            if (!options.order_id) throw new Error('物流异常')
            this.order_id = options.order_id
            this.type = options.type
            await this.initLogisticsData()
        } catch (err) {
            console.log(err)
            setTimeout(() => {
                this.$Router.back()
            }, 0.5 * 1000)
        }
    }
}
</script>

<style lang="scss" scoped>
.order-logistics {
}

.express {
    height: 300rpx;
    background-color: #46abef;
    padding: 32rpx;
    .pacel {
        display: inline-block;
        border-radius: 7px;
        width: 180rpx;
        height: 100rpx;
        padding: 10rpx 20rpx;
        background-color: rgba(255, 255, 255, 0.4);
        color: #fff;
    }

    .express-card {
        background-color: white;
        border-radius: 7px;
        margin-top: 40rpx;
    }
    &-goods {
        position: relative;
        width: 160rpx;
        height: 160rpx;

        &::before {
            z-index: 9;
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            display: block;
            content: '共' attr(data-count) '件商品';
            padding: 5rpx 20rpx;
            font-size: $-font-size-xs;
            text-align: center;
            border-radius: 60px;
            background-color: rgba(0, 0, 0, 0.6);
            color: #ffffff;
        }
    }

    &-info {
        display: flex;
        flex-direction: column;
        margin-left: 24rpx;
    }
}

.logistics {
    padding: 30rpx 24rpx 30rpx calc(24rpx + 40rpx / 2);
    background-color: #ffffff;
}
.scroll-view {
    white-space: nowrap;
    width: 100%;
}
.icon_acept {
    border-radius: 50%;
    border: 1px solid rgba(236, 236, 236, 1);
    padding: 8rpx 10rpx;
}
.active-parcel {
    color: black !important;
    background-color: white !important;
}
</style>
