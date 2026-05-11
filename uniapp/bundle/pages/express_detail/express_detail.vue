<template>
    <view
        :class="themeName"
        class="p-t-50"
        :style="{
            'background-image': `url(${$getImageUri(
                '/resource/image/shopapi/default/after_sale6.png'
            )})`,
            'background-size': '750rpx 750rpx',
            'background-repeat': 'no-repeat'
        }"
    >
        <!-- #ifndef  H5 -->
        <u-sticky offset-top="0" h5-nav-height="0" bg-color="transparent">
            <u-navbar
                title="寄件详情"
                :is-fixed="true"
                :border-bottom="false"
                title-color="white"
                :background="{ background: 'rgba(256,256, 256,' + 0 + ')' }"
            ></u-navbar>
        </u-sticky>
        <!-- #endif -->
        <view class="m-l-20 m-r-20 bg-white br20">
            <view class="p-t-20 flex p-l-30 row-between p-r-24 p-b-20">
                <view class="bold">{{ detail.express_name }} {{ detail.invoice_no }}</view>
                <view @click="onCopy(detail.invoice_no)">
                    <u-icon name="file-text" class="m-l-10"></u-icon>复制单号</view
                >
            </view>
        </view>

        <view class="m-l-20 m-r-20 bg-white br20 m-t-20">
            <view class="p-t-20 flex p-l-30 row-between p-r-24">
                <view class="bold">寄件信息</view>
            </view>
            <template v-if="detail.goods_snap != undefined">
                <view class="flex p-24 p-l-30">
                    <u-image
                        :src="detail.goods_snap.image"
                        width="120rpx"
                        height="120rpx"
                        mode="aspectFill"
                        border-radius="20"
                    ></u-image>
                    <view class="m-l-24" style="width: 100%">
                        <view class="nr line-2 m-b-20">
                            {{ detail.goods_snap.goods_name }}
                        </view>
                        <view class="xs muted flex row-between">
                            <price
                                class="m-r-12"
                                :content="detail.goods_price"
                                color="#101010"
                                mainSize="28rpx"
                                minorSize="26rpx"
                            ></price>

                            <text>X {{ detail.goods_snap.goods_num }}</text>
                        </view>
                    </view>
                </view>
            </template>

            <view class="lighter p-24">
                <view class="flex row-between col-top">
                    <view> 商家地址 </view>
                    <view style="text-align: right; color: #999999" class="m-l-20">
                        <view>
                            {{ detail.return_address.contact }}{{ detail.return_address.mobile }}
                        </view>
                        <view>
                            {{ completeAddress }}
                        </view>
                    </view>
                </view>
                <view class="flex row-between col-top m-t-30">
                    <view> 寄件时间 </view>
                    <view style="text-align: right; color: #999999" class="m-l-20">
                        <view>
                            {{ detail.return_address.update_time }}
                        </view>
                    </view>
                </view>
            </view>
        </view>

        <template v-if="detail.btns != undefined">
            <view class="footer bg-white p-20">
                <view class="flex row-right">
                    <view
                        class="br60 btn inline m-l-24"
                        v-if="detail.express_name && detail.sub_status == 13"
                        @click="toExpressInfo(1)"
                    >
                        修改物流单号
                    </view>
                </view>
            </view>
        </template>
    </view>
</template>

<script>
import { apiAfterSaleDetail, apiAfterSaleCancel } from '@/api/order.js'
import { copy } from '@/utils/tools.js'

export default {
    data() {
        return {
            id: 13,
            detail: {},
            address: '',
            showCancel: false
        }
    },
    computed: {
        completeAddress() {
            return (
                this.detail.return_address.province +
                this.detail.return_address.city +
                this.detail.return_address.district +
                this.detail.return_address.address
            )
        }
    },
    methods: {
        onCopy(str) {
            copy(str)
        },
        getRefundDetailFun() {
            apiAfterSaleDetail({
                id: this.id
            }).then((res) => {
                this.detail = res
                try {
                    this.address =
                        res.address + ', ' + res.return_contact + ': ' + res.return_contact_mobile
                } catch (err) {
                    console.log(err)
                }
            })
        },

        // 去填写快递单号
        toExpressInfo(type) {
            this.$Router.push({
                path: '/bundle/pages/input_express_info/input_express_info',
                query: {
                    id: this.detail.master_id,
                    order_id: this.id,
                    change: type
                }
            })
        }
    },

    onLoad() {
        const options = this.$Route.query
        this.id = options.id
        this.getRefundDetailFun()
    },
    onShow() {
        this.getRefundDetailFun()
    }
}
</script>

<style lang="scss">
.header {
    @include background_linear(90deg, 50%, 100%);
}

.sign {
    color: #555;
    font-size: 24rpx;
    padding: 4rpx 10rpx;
    background-color: #f4f4f4;
}

.footer {
    left: 0;
    bottom: 0;
    width: 100%;
    height: 150rpx;
    position: fixed;
    padding-bottom: env(safe-area-inset-bottom);
    padding: 0 20rpx constant(safe-area-inset-bottom) 20rpx;
}

.btn {
    padding: 10rpx 40rpx;
    border-width: 1rpx;
    border-style: solid;
    @include font_color();
    @include border_color();
}
</style>
