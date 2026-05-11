<template>
    <view :class="themeName" class="p-20">
        <!-- 提示 -->
        <!-- <u-notice-bar :list="noticeList"></u-notice-bar> -->

        <!-- 申请售后头部商品 -->
        <view class="m-t-20 bg-white flex p-24 p-l-30 br20">
            <u-image
                :src="goodsData.goods_image"
                width="160rpx"
                height="160rpx"
                mode="aspectFill"
                style="flex-shrink: 0"
            ></u-image>
            <view class="m-l-12">
                <view class="nr line-2 m-b-10">
                    {{ goodsData.goods_name }}
                </view>
                <view class="flex row-between">
                    <view class="xs muted skuline">
                        {{ goodsData.spec_value_str }}
                    </view>
                    <view class="muted"> x{{ goodsData.goods_num }} </view>
                </view>
            </view>
        </view>

        <!-- 售后方式选择 -->
        <view class="bg-white m-t-24 p-t-24 p-b-24 br20" v-if="refund_method == 0">
            <view class="flex row-between p-r-20 p-l-20" @click="getAfterSaleGoods(1)">
                <view class="">
                    <view class="md normal">仅退款</view>
                    <view class="xs muted m-t-10">未收到货，与卖家协商同意无需退货只需退款</view>
                </view>
                <u-icon name="arrow-right" size="22"></u-icon>
            </view>
        </view>
        <view class="bg-white m-t-24 p-t-24 p-b-24 br20" v-if="refund_method == 0">
            <view class="flex row-between p-r-20 p-l-20" @click="getAfterSaleGoods(2)">
                <view class="">
                    <view class="md normal">退货退款</view>
                    <view class="xs muted m-t-10">已收到货，需退还收到的实物</view>
                </view>
                <u-icon name="arrow-right" size="22"></u-icon>
            </view>
        </view>

        <view class="" v-else>
            <!-- <view class="p-t-24 p-l-15">
                <view class="flex row-between bb p-b-24">
                    <text class="nr normal">数量</text>
                    <text class="p-r-20">{{ goodsData.goods_num }}</text>
                </view>
            </view> -->
            <view class="p-t-24 p-l-15 bg-white br20 m-t-24" @click="showPop = true">
                <view class="flex row-between p-b-24">
                    <view class="nr normal">退款原因</view>
                    <view class="m-r-20">
                        <text class="p-r-15 muted nr">{{ reasonIndex }}</text>
                        <u-icon name="arrow-right"></u-icon>
                    </view>
                </view>
            </view>
            <view class="p-t-24 p-l-15 bg-white br20 m-t-24">
                <view class="flex row-between p-b-24">
                    <view class="nr normal">退款金额</view>
                    <price
                        class="m-r-20"
                        :content="goodsData.total_pay_price"
                        color="#FF2C3C"
                        mainSize="28rpx"
                        minorSize="26rpx"
                    ></price>
                </view>
                <view class="flex row-right p-b-24 muted">
                    <span class="m-r-20" style="font-size: 26rpx">
                        最多¥{{ Number(goodsData.total_pay_price).toFixed(2)
                        }}<span v-if="goodsData.express_status == 0">
                            ，含运费¥{{ goodsData.express_price }}
                        </span>
                    </span>
                </view>
            </view>

            <view class="p-t-24 p-l-15 p-r-15 bg-white br20 m-t-24">
                <view class="p-b-24">
                    <view class="nr normal flex-none"
                        >补充描述<span class="muted">（选填）</span></view
                    >
                    <view class="flex-1 m-t-24">
                        <textarea
                            class="bg-body"
                            style="width: 100%"
                            placeholder="补充描述，有助于商家更好的处理售后问题"
                            v-model="remark"
                            name="textarea"
                        ></textarea>
                    </view>
                </view>
                <view class="p-t-24 p-l-15 p-b-44">
                    <view class="flex row-between p-b-24">
                        <view class="nr normal">上传凭证</view>
                        <view class="muted nr"> （选填，最多可上传3张） </view>
                    </view>

                    <uploader
                        v-model="fileList"
                        mutiple
                        :deletable="true"
                        preview-size="160rpx"
                        :maxUpload="3"
                        image-fit="aspectFill"
                    />
                </view>
            </view>
        </view>

        <view
            v-if="refund_method !== 0"
            class="post-btn flex row-center br60 lg white"
            :disabled="isLoading"
            @click="applyRefundFun"
        >
            提交申请
        </view>

        <u-popup id="popup" v-model="showPop" border-radius="14" mode="bottom" :closeable="true">
            <view class="bg-white">
                <view class="pop-header flex row-center md normal"> 退款原因 </view>
                <scroll-view style="height: 800rpx" :scroll-y="true">
                    <radio-group @change="radioChange" class="m-t-20">
                        <label
                            v-for="(item, index) in goodsData.reason"
                            :key="index"
                            class="reason-item flex row-between"
                            @click="showPop = false"
                        >
                            <view class="reason-desc nr">
                                {{ item }}
                            </view>
                            <radio
                                style="transform: scale(0.7)"
                                color="#FF2C3C"
                                :value="index + ''"
                            ></radio>
                        </label>
                    </radio-group>
                </scroll-view>
            </view>
        </u-popup>
    </view>
</template>

<script>
import { apiAfterSaleGoods, apiAfterSalePost } from '@/api/order.js'
import { debounce } from '@/utils/tools'
export default {
    data() {
        return {
            noticeList: ['订单所产生的运费，如已经发货的订单平台是不予退回的，请谨慎操作！'],
            id: 1,
            current: 0, //选择原因的索引
            refund_method: 0, //是否显示售后方式选择
            showPop: false, //显示退货退款原因弹窗
            reasonIndex: '请选择退款原因', //退货退款原因数据
            remark: '', //  备注退款原因
            fileList: [], //上传凭证
            goodsData: {
                goods_name: '哈比树儿童滑滑梯室内汽车滑梯家用玩具宝宝小型秋千组合游乐园',
                goods_image:
                    'https://img0.baidu.com/it/u=2635130093,4202492912&amp;fm=26&amp;fmt=auto&amp;gp=0.jpg',
                spec_value_str: '红色,L码'
            },
            isLoading: false
        }
    },

    onLoad() {
        const options = this.$Route.query
        this.id = options.id
        this.getAfterSaleGoods()
    },

    methods: {
        // 获取商品自订单商品
        getAfterSaleGoods(methods = '') {
            if (methods != '') {
                this.refund_method = methods
            }
            if (this.refund_method == 1) {
                uni.setNavigationBarTitle({
                    title: '仅退款'
                })
            }
            if (this.refund_method == 2) {
                uni.setNavigationBarTitle({
                    title: '退货退款'
                })
            }
            apiAfterSaleGoods({
                order_goods_id: this.id,
                refund_method: methods
            }).then((res) => {
                this.goodsData = res
            })
        },

        // 选择退款原因
        radioChange(event) {
            this.reasonIndex = this.goodsData.reason[event.detail.value]
        },

        // 提交售后
        applyRefundFun() {
            if (this.reasonIndex == '请选择退款原因') {
                return this.$toast({
                    title: '请选择退款原因'
                })
            }
            this.isLoading = true
            apiAfterSalePost({
                refund_remark: this.remark,
                refund_method: this.refund_method,
                refund_reason: this.reasonIndex,
                order_goods_id: this.id,
                voucher: this.fileList
            })
                .then((res) => {
                    uni.redirectTo({
                        url: `/bundle/pages/after_sale/after_sale`
                    })
                })
                .finally(() => {
                    this.isLoading = false
                })
        }
    },
    async onShow() {
        this.applyRefundFun = debounce(this.applyRefundFun, 500, this)
    }
}
</script>

<style lang="scss">
.bb {
    border-bottom: 1rpx solid #f2f2f2;
}

textarea {
    flex: 1;
    height: 172rpx;
    border-radius: 10rpx;
    padding: 20rpx;
    box-sizing: border-box;
}

.pop-header {
    line-height: 42rpx;
    padding: 30rpx;
}

.reason-item {
    padding: 20rpx;

    .reason-desc {
        line-height: 46rpx;
    }
}

.post-btn {
    height: 84rpx;
    margin: 40rpx 24rpx 0 24rpx;
    @include background_color();
}
</style>
