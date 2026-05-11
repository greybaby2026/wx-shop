<template>
    <view :class="themeName">
        <!-- #ifndef  H5 -->
        <u-sticky offset-top="0" h5-nav-height="0" bg-color="transparent">
            <u-navbar
                title="售后详情"
                :is-fixed="true"
                :border-bottom="false"
                title-color="white"
                :background="{ background: 'rgba(256,256, 256,' + 0 + ')' }"
            ></u-navbar>
        </u-sticky>
        <!-- #endif -->

        <view class="white p-l-50 p-r-50 p-t-50 p-b-20 lg">
            <span v-if="detail.sub_status == 11"> 等待商家处理 </span>
            <span v-if="detail.sub_status == 15 && detail.refund_method == 1"> 等待商家退款 </span>
            <span v-if="detail.sub_status == 21"> 退款成功 </span>
            <span v-if="detail.sub_status == 31 || detail.sub_status == 32"> 退款关闭 </span>
            <span v-if="detail.sub_status == 12"> 请寄回商品 </span>
            <span v-if="detail.sub_status == 13"> 等待商家处理 </span>
            <span v-if="detail.sub_status == 14"> 等待商家退款 </span>
            <span v-if="detail.sub_status == 33"> 退款关闭 </span>
        </view>
        <view class="white p-l-50 p-b-20 header-text p-r-50">
            <span v-if="detail.sub_status == 11 && detail.refund_method == 1">
                等待商家处理中，若商家拒绝，您可以联系商家协商
            </span>
            <span v-if="detail.sub_status == 11 && detail.refund_method == 2">
                您已提交退货申请，请耐心等待商家处理
            </span>
            <span v-if="detail.sub_status == 15 && detail.refund_method == 1">
                商家已审核通过您的售后申请，正在为您退款中
            </span>
            <span v-if="detail.sub_status == 21"> 商家已为您退款，感谢您对我们的支持 </span>
            <span v-if="detail.sub_status == 31">
                因您主动取消退款，本次退款申请已关闭，如问题仍未解决，售后保障期内，您可以重新发起售后申请
            </span>
            <span v-if="detail.sub_status == 32">
                因商家拒绝退款，本次退款申请已关闭，如问题仍未解决，售后保障期内，您可以重新发起售后申请
            </span>
            <span v-if="detail.sub_status == 12">
                请将商品寄回商家指定地址，确认无误后，我们将为您快速退款
            </span>
            <span v-if="detail.sub_status == 13">
                您已寄回商品，商家收到商品并确认无误后，将会为您处理退款
            </span>
            <span v-if="detail.sub_status == 14"> 商家已收到您退回来的商品，正在为您退款中 </span>
            <span v-if="detail.sub_status == 33">
                因商家拒绝收货，本次退款申请已关闭，如问题仍未解决，售后保障期内，您可以重新发起售后申请
            </span>
        </view>
        <view class="flex row-center">
            <template v-if="detail.refund_method == 1">
                <image
                    v-if="detail.sub_status == 11"
                    :src="$getImageUri('/resource/image/shopapi/default/after_sale1.png')"
                    style="height: 142rpx; width: 646rpx"
                ></image>
                <image
                    v-if="detail.sub_status == 15"
                    :src="$getImageUri('/resource/image/shopapi/default/after_sale5.png')"
                    style="height: 142rpx; width: 646rpx"
                ></image>
            </template>

            <template v-if="detail.refund_method == 2">
                <image
                    v-if="detail.sub_status == 11"
                    :src="$getImageUri('/resource/image/shopapi/default/after_sale4.png')"
                    style="height: 142rpx; width: 646rpx"
                ></image>
                <image
                    v-if="detail.sub_status == 12"
                    :src="$getImageUri('/resource/image/shopapi/default/after_sale2.png')"
                    style="height: 142rpx; width: 646rpx"
                ></image>
                <image
                    v-if="detail.sub_status == 13"
                    :src="$getImageUri('/resource/image/shopapi/default/after_sale2.png')"
                    style="height: 142rpx; width: 646rpx"
                ></image>
                <image
                    v-if="detail.sub_status == 14"
                    :src="$getImageUri('/resource/image/shopapi/default/after_sale3.png')"
                    style="height: 142rpx; width: 646rpx"
                ></image>
            </template>
        </view>
        <template v-if="detail.btns != undefined">
            <view
                class="p-24 p-l-30 m-l-20 m-r-20 bg-white br20 m-b-20"
                v-if="detail.btns.express_btn"
            >
                <view class="flex row-between">
                    <view class="bold">退货信息</view>
                    <view>
                        <u-icon name="file-text"></u-icon>
                        <span
                            class="m-l-10"
                            @click="
                                onCopy(
                                    detail.return_address.contact +
                                        detail.return_address.mobile +
                                        completeAddress
                                )
                            "
                        >
                            复制
                        </span>
                    </view>
                </view>
                <view class="flex row-between m-t-20 col-top">
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
                <view class="flex row-between m-t-20">
                    <view>
                        <view> 我已寄出 </view>
                        <view class="muted"> 填写物流单号 </view>
                    </view>
                    <view class="express-box" @click="toExpressInfo(0)"> 填写单号 </view>
                </view>
            </view>
        </template>
        <template v-if="detail.express_name && detail.sub_status == 13">
            <view class="p-24 p-l-30 m-l-20 m-r-20 bg-white br20 m-b-20">
                <view class="flex row-between">
                    <view class="bold">退货信息</view>
                    <view @click="handleDetail">
                        查看详情
                        <u-icon name="arrow-right"></u-icon>
                    </view>
                </view>
                <view class="flex row-center express-info">
                    <view class="">{{ detail.express_name }} {{ detail.invoice_no }}</view>
                </view>
                <view class="flex row-center muted m-t-20" @click="toExpressInfo(1)">
                    <view class=""
                        ><u-icon name="edit-pen" class="m-l-10"></u-icon> 修改物流单号</view
                    >
                </view>
            </view>
        </template>
        <template>
            <view class="p-24 p-l-30 m-l-20 m-r-20 bg-white br20 m-b-20" v-if="detail.admin_remark">
                <view class="flex row-between col-top">
                    <view style="flex-shrink: 0"> 拒绝原因 </view>
                    <view class="m-l-20">
                        {{ detail.admin_remark }}
                    </view>
                </view>
            </view>
        </template>
        <view class="m-l-20 m-r-20 bg-white br20" style="margin-bottom: 150rpx">
            <view class="p-t-40 flex p-l-30 row-between p-r-24">
                <view class="bold">退款信息</view>
                <view @click="gotoService">
                    <u-icon name="chat"></u-icon>
                    <span class="m-l-10"> 联系商家 </span>
                </view>
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
                <view class="flex row-between">
                    <view> 退款方式 </view>
                    <view>
                        {{ detail.refund_method_desc }}
                    </view>
                </view>
                <view class="p-t-40 flex row-between">
                    退款金额<price
                        class="m-r-12"
                        :content="detail.refund_amount"
                        color="#FF2C3C"
                        mainSize="28rpx"
                        minorSize="26rpx"
                    ></price>
                </view>
                <view class="p-t-40 flex row-between"
                    ><view> 退款原因 </view>
                    <view>
                        {{ detail.refund_reason }}
                    </view>
                </view>
                <view class="p-t-40 flex row-between">
                    <view> 申请时间 </view>
                    <view>
                        {{ detail.create_time }}
                    </view>
                </view>
                <view class="p-t-40 flex row-between" @click="onCopy(detail.order_sn)">
                    <view> 订单编号 </view>
                    <view>
                        {{ detail.order_sn }}
                        <u-icon name="file-text" class="m-l-10"></u-icon>
                    </view>
                </view>
                <view class="p-t-40 flex row-between" @click="onCopy(detail.sn)">
                    <view> 售后编号 </view>
                    <view>
                        {{ detail.sn }}
                        <u-icon name="file-text" class="m-l-10"></u-icon>
                    </view>
                </view>
                <view class="p-t-40 flex row-between col-top">
                    <view style="flex-shrink: 0"> 退款说明 </view>
                    <view class="m-l-20">
                        {{ detail.refund_remark }}
                    </view>
                </view>
                <view class="p-t-40 flex row-between col-top">
                    <view style="flex-shrink: 0"> 退款凭证 </view>
                    <view class="m-l-20 flex">
                        <u-image
                            v-for="(item, index) in detail.voucher"
                            @tap="previewImage(detail.voucher, index)"
                            :key="item"
                            :src="item"
                            width="100rpx"
                            height="100rpx"
                            class="m-r-10"
                        ></u-image>
                    </view>
                </view>
            </view>
        </view>

        <template v-if="detail.btns != undefined">
            <view class="footer bg-white p-20">
                <view class="flex row-right">
                    <view
                        class="br60 btn inline m-l-24"
                        @click="showCancel = true"
                        v-if="detail.btns.cancel_btn"
                    >
                        取消退款
                    </view>
                    <view
                        class="br60 btn inline m-l-24"
                        @click="toExpressInfo(1)"
                        v-if="detail.express_name && detail.sub_status == 13"
                    >
                        修改物流单号
                    </view>

                    <view
                        class="br60 btn inline m-l-24"
                        @click="toApplyAfter"
                        v-if="detail.btns.reapply_btn"
                    >
                        重新申请
                    </view>
                </view>
            </view>
        </template>

        <u-modal
            v-model="showCancel"
            width="540"
            :confirm-style="{ 'border-left': '1rpx solid #e5e5e5' }"
            @confirm="cancelApplyFun"
            :show-title="false"
            @cancel="showCancel = false"
            :showCancelButton="true"
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
        gotoService() {
            this.$Router.push({
                path: '/bundle/pages/artificial_service/artificial_service'
            })
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

        // 撤销审核
        cancelApplyFun() {
            apiAfterSaleCancel({ id: this.detail.master_id }).then((res) => {
                this.getRefundDetailFun()
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
        },

        toApplyAfter() {
            this.$Router.push({
                path: '/bundle/pages/post_after_sale/post_after_sale',
                query: {
                    id: this.detail.order_goods_id
                }
            })
        },

        //复制
        onCopy(str) {
            copy(str)
        },
        previewImage(imgArr, current) {
            console.log(urls, current)
            const urls = imgArr.map((item) => item)
            uni.previewImage({
                current,
                // 当前显示图片的http链接
                urls // 需要预览的图片http链接列表
            })
        },
        handleDetail() {
            this.$Router.push({
                path: '/bundle/pages/express_detail/express_detail',
                query: {
                    id: this.id
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
page {
    background-image: linear-gradient(126deg, #e64d33 28.96%, #ec8543 100%);
    background-size: 100% 40%; /* 宽度100%，高度50% */
    background-repeat: no-repeat;
}
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
.header-text {
    color: rgba($color: white, $alpha: 0.6);
}
.express-box {
    display: flex;

    padding: 5px 16rpx;
    justify-content: center;
    align-items: center;
    border-radius: 20px;
    border: 1px solid #333;
}
.express-info {
    margin-top: 20rpx;
    border-radius: 8px;
    background: #fafafd;
    height: 80rpx;
    line-height: 100rpx;
}
</style>
