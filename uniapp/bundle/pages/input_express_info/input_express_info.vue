<template>
    <view :class="themeName">
        <view class="p-24 p-l-30 m-l-20 m-r-20 bg-white br20 m-b-20 m-t-30">
            <view class="bold">退货商品</view>
            <view class="flex m-t-20">
                <u-image
                    :src="orderDetial.goods_snap.image"
                    width="120rpx"
                    height="120rpx"
                    mode="aspectFill"
                    border-radius="20"
                ></u-image>
                <view class="m-l-24" style="width: 100%">
                    <view class="nr line-2 m-b-20">
                        {{ orderDetial.goods_snap.goods_name }}
                    </view>
                    <view class="xs muted flex row-between">
                        <price
                            class="m-r-12"
                            :content="orderDetial.goods_price"
                            color="#101010"
                            mainSize="28rpx"
                            minorSize="26rpx"
                        ></price>

                        <text>X {{ orderDetial.goods_snap.goods_num }}</text>
                    </view>
                </view>
            </view>
        </view>
        <view class=" ">
            <view class="flex m-24 bg-white br20">
                <view class="label nr normal">物流公司</view>
                <input
                    type="text"
                    v-model="form.express_name"
                    class="input"
                    placeholder="请输入物流公司名称"
                />
            </view>

            <view class="flex m-24 bg-white br20">
                <view class="label nr normal">快递单号</view>
                <input
                    type="text"
                    v-model="form.invoice_no"
                    class="input"
                    placeholder="请输入快递单号"
                />
            </view>

            <!-- <view class="flex">
                <view class="label nr normal">备注说明</view>
                <input type="text" v-model="form.express_remark" class="input" placeholder="选填" />
            </view> -->
        </view>
        <!-- 
        <view class="m-t-24 bg-white p-24">
            <view class="flex p-b-24">
                <view class="nr">上传凭证</view>
                <view class="m-r-12 muted sm"> （选填，最多可上传3张） </view>
            </view>

            <uploader
                v-model="form.express_image"
                :deletable="true"
                preview-size="160rpx"
                maxUpload="3"
                image-fit="aspectFill"
            />
        </view> -->

        <view class="btn flex row-center br60 lg white" @click="applyExpressInfo"> 提交 </view>
    </view>
</template>

<script>
import { apiExpressInfo, apichangeExpressInfo, apiAfterSaleDetail } from '@/api/order.js'
import { debounce } from '@/utils/tools'

export default {
    data() {
        return {
            form: {
                id: '', //售后ID
                express_name: '', //快递公司
                invoice_no: '', //快递单号
                express_remark: '', //备注
                express_image: [] //快递凭证
            },
            change: 0,
            orderId: '',
            orderDetial: {}
        }
    },

    methods: {
        getRefundDetailFun() {
            apiAfterSaleDetail({
                id: this.orderId
            }).then((res) => {
                this.orderDetial = res
            })
        },
        applyExpressInfo() {
            if (this.change == 1) {
                apichangeExpressInfo({ ...this.form }).then((res) => {
                    this.$toast({ title: '提交成功' })
                    setTimeout(() => {
                        uni.navigateBack(1)
                    }, 1000)
                })
            } else {
                apiExpressInfo({ ...this.form }).then((res) => {
                    this.$toast({ title: '提交成功' })
                    setTimeout(() => {
                        uni.navigateBack(1)
                    }, 1000)
                })
            }
        }
    },

    onLoad() {
        const options = this.$Route.query
        this.form.id = options.id
        this.change = options.change
        this.orderId = options.order_id
    },

    async onShow() {
        this.applyExpressInfo = debounce(this.applyExpressInfo, 500, this)
        this.getRefundDetailFun()
    }
}
</script>

<style lang="scss">
.label {
    margin: 24rpx 0;
    width: 180rpx;
    text-align: center;
}

.input {
    width: 80%;
}

.btn {
    height: 84rpx;
    margin: 50rpx 24rpx 0 24rpx;
    @include background_color();
}
</style>
