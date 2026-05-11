<template>
    <view :class="themeName">
        <!-- #ifndef H5 -->

        <navbar title="订单详情"></navbar>
        <!-- #endif -->
        <view class="order-detail">
            <!-- Order Status -->
            <view class="order-status">
                <!-- 待支付 -->
                <template v-if="!orderInfo.order_status">
                    <view class="order-status-name" v-if="orderInfo.pay_way !== 5">{{
                        orderAction['name']
                    }}</view>
                    <view class="order-status-name" v-else>等待商家处理，请耐心等待</view>
                    <view class="order-status-desc" v-if="showCountDown">
                        <text class="m-r-10" v-if="orderInfo.pay_way !== 5">支付剩余</text>
                        <u-count-down
                            :timestamp="getCountDownSegment(orderInfo.cancel_unpaid_orders_time)"
                            :show-days="false"
                            :show-hours="getCountDownSegment(orderInfo.cancel_unpaid_orders_time) >= 3600 ? true : false"
                            :font-size="26"
                            :separator-size="26"
                            color="#FFFFFF"
                            separator-color="#FFFFFF"
                            bg-color="unset"
                            separator="zh"
                            @end="showCountDown = false"
                        />
                        <text class="m-l-10">自动关闭</text>
                    </view>
                </template>
                <!-- 订单状态【快递配送】 -->
                <template v-else-if="orderInfo.delivery_type !== 2">
                    <view class="order-status-name">{{ orderAction['name'] }}</view>
                    <template v-if="orderAction['desc']">
                        <view class="order-status-desc">{{ orderAction['desc'] }}</view>
                    </template>
                </template>
                <!-- 订单状态【门店自提】 -->
                <template v-else-if="orderInfo.delivery_type === 2">
                    <view class="order-status-name">{{
                        selffetchStatus[orderInfo.order_status]['name']
                    }}</view>
                    <template v-if="selffetchStatus[orderInfo.order_status]['desc']">
                        <view class="order-status-desc">{{
                            selffetchStatus[orderInfo.order_status]['desc']
                        }}</view>
                    </template>
                </template>
            </view>

            <!-- Address -->
            <view class="delivery">
                <!-- 快递配送 -->
                <template v-if="orderInfo.delivery_type == 1 || addressInfo.address">
                    <address-card :has-content="true" border-radius="7px">
                        <template slot="header">
                            <text>{{ addressInfo.contact }}</text>
                            <text class="m-l-10">{{ addressInfo.mobile }}</text>
                        </template>
                        <template slot="main">
                            <text>{{ addressInfo.province }}</text>
                            <text class="m-l-10">{{ addressInfo.city }}</text>
                            <text class="m-l-10">{{ addressInfo.district }}</text>
                            <text class="m-l-10">{{ addressInfo.address }}</text>
                        </template>
                    </address-card>
                </template>

                <!-- 门店自提 -->
                <template v-if="orderInfo.delivery_type === 2 || orderInfo.selffetch_shop">
                    <view class="contain receive address-card">
                        <view>
                            <u-icon name="man-add" size="35" />
                            <text class="black m-l-20 lg">{{ addressInfo.contact }}</text>
                            <text class="black m-l-20 lg">{{ addressInfo.mobile }}</text>
                        </view>
                        <view class="m-t-20">
                            <u-icon name="tags" size="35" />
                            <text class="black m-l-15 lg">{{ orderInfo.selffetch_shop.name }}</text>
                        </view>
                        <view class="flex row-between">
                            <view>
                                <view class="m-t-15 muted xs">
                                    门店地址：{{ orderInfo.selffetch_shop.detailed_address }}
                                </view>
                                <view class="m-t-15 muted xs">
                                    营业时间：{{ shopOpenTime }}
                                    <!-- {{ orderInfo.selffetch_shop.business_start_time }}-{{
                                        orderInfo.selffetch_shop.business_end_time
                                    }} -->
                                </view>
                            </view>
                            <view>
                                <u-image
                                    width="100rpx"
                                    height="100rpx"
                                    :src="orderInfo.selffetch_shop.image"
                                    @click="handleImage"
                                >
                                </u-image>
                            </view>
                        </view>
                        <view class="m-t-50 flex row-between">
                            <view @click="handleshowQR">
                                <u-icon name="scan" />
                                <text class="m-l-5">出示核销码</text>
                            </view>
                            <view @click="handlePhone">
                                <u-icon name="phone" />
                                <text class="m-l-5">联系自提点</text>
                            </view>
                            <view @click="handleLocation">
                                <u-icon name="map" />
                                <text class="m-l-5">导航自提点</text>
                            </view>
                        </view>
                    </view>
                    <!-- <address-card
                        :has-content="true"
                        placeholder="请选择门店地址"
                        border-radius="7px"
                    >
                        <template slot="header">
                            <text>{{ orderInfo.selffetch_shop.name }}</text>
                        </template>
                        <template slot="main">
                            <text>{{ orderInfo.selffetch_shop.detailed_address }}</text>
                        </template>
                        <template slot="footer">
                            <text>
                                <text>营业时间：</text>
                                <text>{{ orderInfo.selffetch_shop.business_start_time }}</text>
                                <text>-</text>
                                <text>{{ orderInfo.selffetch_shop.business_end_time }}</text>
                            </text>
                        </template>
                    </address-card> -->
                </template>
            </view>

            <!-- 提货码QR -->
            <u-popup v-model="showQR" mode="bottom" safe-area-inset-bottom border-radius="20">
                <view v-if="showQR" class="contain receive">
                    <view class="receive-qr flex-col row-center col-center p-30">
                        <view class="">核销码</view>
                        <text class="m-t-30">请将二维码展示给店员或者提供数字核销码</text>
                        <view
                            class="m-t-40 qr-contain"
                            :class="{ 'qr-contain--die': orderInfo.verification_status }"
                            v-if="showQR"
                        >
                            <tki-qrcode
                                ref="qrcode"
                                uni="px"
                                :val="orderInfo.pickup_code"
                                :size="138 * 2"
                                :showLoading="false"
                            />
                        </view>
                        <view class="m-t-30 black qr-code">{{ orderInfo.pickup_code }}</view>
                    </view>
                </view>
            </u-popup>
            <!-- <view v-if="orderInfo.delivery_type == 2" class="contain receive">
                <view v-if="orderInfo.verification_status" class="delivery--die">
                    <u-image
                        src="/static/images/delivery_die.png"
                        width="134"
                        height="98"
                        mode="scaleFill"
                    />
                </view>

                <template v-if="orderInfo.pickup_code && orderInfo.show_pickup_code">
                    <view class="receive-qr" v-if="orderInfo.order_status == 1">
                        <text class="xs lighter">请凭二维码取货</text>
                        <view
                            class="m-t-20 qr-contain"
                            :class="{ 'qr-contain--die': orderInfo.verification_status }"
                        >
                            <tki-qrcode
                                ref="qrcode"
                                uni="px"
                                :val="orderInfo.pickup_code"
                                :size="118 * 2"
                                :showLoading="false"
                            />
                        </view>
                        <view class="m-t-30 xs black qr-code"
                            >提货码：{{ orderInfo.pickup_code }}</view
                        >
                    </view>
                </template>

                <view class="nr receive-info">
                    <view class="receive-info-item">
                        <text class="normal">提货人</text>
                        <text class="black">{{ addressInfo.contact }}</text>
                    </view>
                    <view class="receive-info-item">
                        <text class="normal">联系方式</text>
                        <text class="black">{{ addressInfo.mobile }}</text>
                    </view>
                </view>
            </view> -->

            <!-- 拼团状态 -->
            <template v-if="OrderTypeEnum['TEAM'] === orderInfo.order_type">
                <view class="contain flex row-between p-20 nr">
                    <text>拼团状态</text>
                    <text :style="{ color: themeColor }">{{
                        teamStatus[orderInfo.is_team_success]
                    }}</text>
                </view>
            </template>

            <!-- 商品 -->
            <view class="contain order-goods">
                <view v-for="(goodsItem, goodsIndex) in goodsList" :key="goodsIndex">
                    <goods-card
                        :key="goodsIndex"
                        shape="rectangle"
                        :name="goodsItem.goods_name"
                        :image="goodsItem.goods_image"
                        :contain-style="{ height: '230rpx' }"
                        :image-style="{ width: '180rpx', height: '180rpx' }"
                        @tap="goGoodsDetail(goodsItem.goods_id)"
                    >
                        <view class="flex-1" slot="default">
                            <text class="muted xs m-t-10 skuline">{{
                                goodsItem.spec_value_str
                            }}</text>
                            <view class="m-t-10 flex row-between">
                                <view class="flex col-center">
                                    <price
                                        v-if="
                                            orderInfo.order_type == 0 || orderInfo.order_type == 4
                                        "
                                        :content="goodsItem.original_price"
                                        mainSize="32rpx"
                                        minorSize="24rpx"
                                    />
                                    <price
                                        v-else
                                        :content="goodsItem.goods_price"
                                        mainSize="32rpx"
                                        minorSize="24rpx"
                                    />
                                    <view
                                        class="vip-price flex m-l-10"
                                        v-if="goodsItem.member_price != 0"
                                    >
                                        <view class="price-name xxs">会员价</view>
                                        <view style="padding: 0 10rpx">
                                            <price
                                                :content="goodsItem.member_price"
                                                :weight="500"
                                                color="#7B3200"
                                            >
                                            </price>
                                        </view>
                                    </view>
                                </view>
                                <text class="lighter sm">x{{ goodsItem.goods_num }}</text>
                            </view>
                        </view>
                    </goods-card>
                    <!-- 售后 -->
                    <view class="order-goods__handle" v-if="goodsItem.after_sale_btn">
                        <!-- 申请售后 -->
                        <template v-if="goodsItem.after_sale_btn === 1">
                            <view
                                class="order-goods__handle-button"
                                @tap="
                                    goAfterSale(
                                        afterSaleStatus[goodsItem.after_sale_btn]['href'],
                                        goodsItem.id
                                    )
                                "
                            >
                                {{ afterSaleStatus[goodsItem.after_sale_btn]['name'] }}
                            </view>
                        </template>
                        <!-- 售后中 | 售后成功 | 售后失败 -->
                        <template v-else>
                            <view
                                class="order-goods__handle-button"
                                @tap="
                                    goAfterSale(
                                        afterSaleStatus[goodsItem.after_sale_btn]['href'],
                                        goodsItem.after_sale_id
                                    )
                                "
                            >
                                {{ afterSaleStatus[goodsItem.after_sale_btn]['name'] }}
                            </view>
                        </template>
                    </view>
                </view>
            </view>
            <!-- 虚拟发货内容 -->
            <template v-if="orderInfo.delivery_content || orderInfo.delivery_content1.length">
                <view class="order-info contain">
                    <view class="item">
                        发货信息：
                        <view
                            class="black m-t-10"
                            style="word-break: break-all"
                            v-if="orderInfo.delivery_content_type == 0"
                            >{{ orderInfo.delivery_content }}</view
                        >
                        <view class="black m-t-10" style="word-break: break-all" v-else>
                            <view v-for="item in orderInfo.delivery_content1" :key="item">
                                {{ item.name }}：{{ item.content }}
                            </view>
                        </view>
                        <view class="flex row-right m-t-30">
                            <view
                                class="copy-btn"
                                v-if="orderInfo.delivery_content_type == 0"
                                @click="handleCopy(orderInfo.delivery_content)"
                                >复制</view
                            >
                            <view
                                class="copy-btn"
                                v-if="orderInfo.delivery_content_type == 1"
                                @click="handletemplateCopy(orderInfo.delivery_content1)"
                                >复制</view
                            >
                        </view>
                    </view>
                </view>
            </template>
            <!-- 商品 -->
            <view
                class="contain order-bill"
                v-if="
                    (orderInfo.order_type == 5 &&
                        orderInfo.presell &&
                        orderInfo.order_status == 0) ||
                    (orderInfo.order_type == 5 && orderInfo.presell && orderInfo.order_status == 1)
                "
            >
                <template v-if="orderInfo.order_status == 0">
                    <text v-if="orderInfo.presell.send_type == 0"> 付款后 </text>
                    <text v-if="orderInfo.presell.send_type == 1"> 活动结束 </text>
                    {{ orderInfo.presell.send_type_day }}天内发货
                </template>
                <template v-if="orderInfo.order_status == 1">
                    {{ orderInfo.presell.order_send_text }}
                </template>
            </view>
            <!-- 账单 -->
            <view class="contain order-bill">
                <view class="cell order-bill__item">
                    <view class="cell-label">商品金额</view>
                    <view class="cell-content">
                        <price
                            v-if="orderInfo.order_type == 0 || orderInfo.order_type == 4"
                            :content="orderInfo.total_original_price"
                        />
                        <price v-else :content="orderInfo.goods_price" />
                    </view>
                </view>
                <view class="cell order-bill__item">
                    <view class="cell-label">运费</view>
                    <view class="cell-content">
                        <price :content="orderInfo.express_price" />
                    </view>
                </view>
                <view class="cell order-bill__item" v-if="orderInfo.discount_amount * 1">
                    <view class="cell-label">优惠券</view>
                    <view class="cell-content">
                        <price
                            :content="orderInfo.discount_amount"
                            :color="themeColor"
                            prefix="-￥"
                        />
                    </view>
                </view>
                <view class="cell order-bill__item" v-if="orderInfo.member_amount * 1">
                    <view class="cell-label">会员折扣</view>
                    <view class="cell-content">
                        <price
                            :content="orderInfo.member_amount"
                            :color="themeColor"
                            prefix="-￥"
                        />
                    </view>
                </view>
                <view class="cell order-bill__item" v-if="orderInfo.change_price * 1">
                    <view class="cell-label">商品改价</view>
                    <view class="cell-content">
                        <price :content="orderInfo.change_price" :color="themeColor" prefix="￥" />
                    </view>
                </view>
                <view class="cell order-bill__item">
                    <view class="cell-content">
                        <text class="muted m-r-10">实付款:</text>
                        <price
                            :content="orderInfo.order_amount"
                            mainSize="32rpx"
                            minorSize="24rpx"
                            :color="themeColor"
                        />
                    </view>
                </view>
            </view>

            <!-- 留言 -->
            <view v-if="orderInfo.user_remark" class="contain cell remark">
                <view class="cell-label">买家留言</view>
                <view class="cell-content muted" style="word-break: break-all">{{
                    orderInfo.user_remark
                }}</view>
            </view>

            <!-- 订单信息 -->
            <view class="contain order-info">
                <view class="cell order-info__item">
                    <view class="cell-label">订单编号</view>
                    <view class="cell-content">
                        <view class="flex-1">{{ orderInfo.sn }}</view>
                        <view class="button--copy" @tap="handleCopy(orderInfo.sn)">复制</view>
                    </view>
                </view>
                <view class="cell order-info__item">
                    <view class="cell-label">订单类型</view>
                    <view class="cell-content">{{ orderInfo.order_type_desc }}</view>
                </view>
                <view class="cell order-info__item">
                    <view class="cell-label">支付方式</view>
                    <view class="cell-content">{{ getPaywayName(orderInfo.pay_way) }}</view>
                </view>
                <view class="cell order-info__item">
                    <view class="cell-label">下单时间</view>
                    <view class="cell-content">{{ orderInfo.create_time }}</view>
                </view>
                <view v-if="orderInfo.pay_time" class="cell order-info__item">
                    <view class="cell-label">付款时间</view>
                    <view class="cell-content">{{ orderInfo.pay_time }}</view>
                </view>
                <view v-if="orderInfo.express_time" class="cell order-info__item">
                    <view class="cell-label">发货时间</view>
                    <view class="cell-content">{{ orderInfo.express_time }}</view>
                </view>
                <view v-if="orderInfo.confirm_take_time" class="cell order-info__item">
                    <view class="cell-label">成交时间</view>
                    <view class="cell-content">{{ orderInfo.confirm_take_time }}</view>
                </view>
            </view>

            <!-- Order Operational -->
            <view v-if="orderButtons.length" class="operational">
                <block v-for="buttonItem in orderButtons" :key="buttonItem.event">
                    <view
                        :class="['order-button', `order-button--${buttonItem.style}`]"
                        @tap="onOrderButtons(buttonItem['event'], orderInfo.id, orderInfo.pay_way)"
                        >{{ buttonItem.name }}</view
                    >
                </block>
            </view>

            <!-- 页面状态 -->
            <page-status :status="pageStatus">
                <template #error>
                    <u-empty
                        text="订单异常"
                        src="/static/images/empty/order.png"
                        :icon-size="280"
                    ></u-empty>
                </template>
            </page-status>
        </view>
    </view>
</template>

<script>
import { apiOrderDetail } from '@/api/order'
import OrderMixin from '@/mixins/order'
import { copy } from '@/utils/tools'
import { PageStatusEnum, PaymentStatusEnum, OrderTypeEnum } from '@/utils/enum'
import uImage from '../../components/uview-ui/components/u-image/u-image.vue'

export default {
    components: { uImage },
    name: 'OrderDetail',
    mixins: [OrderMixin],

    data() {
        return {
            showQR: false,
            // 页面状态
            pageStatus: PageStatusEnum['LOADING'],
            order_id: '', // 订单ID
            orderInfo: {
                delivery_content1: []
            }, // 订单信息
            goodsList: [], // 商品列表
            addressInfo: {}, // 地址信息

            orderButtons: [], // 订单按钮组

            showCountDown: true, // 显示倒计时

            // 订单状态
            orderStatus: {
                0: {
                    name: '等待买家付款'
                },
                1: {
                    name: '等待商家发货',
                    desc: '您的商品正在打包中，请耐心等待…'
                },
                2: {
                    name: '待收货',
                    desc: '您的商品正在路中，请耐心等待…'
                },
                3: {
                    name: '已完成',
                    desc: '商品已签收，期待再次购买！'
                },
                4: {
                    name: '订单已关闭',
                    desc: ''
                }
            },

            // 订单状态【自提门店】
            selffetchStatus: {
                2: {
                    name: '待取货',
                    desc: '请前往指定门店取货'
                },
                3: {
                    name: '已完成',
                    desc: '交易已完成，感谢您的购买！'
                },
                4: {
                    name: '订单已关闭',
                    desc: ''
                }
            },

            // 售后状态
            afterSaleStatus: {
                1: {
                    name: '申请售后',
                    href: '/bundle/pages/post_after_sale/post_after_sale'
                },
                2: {
                    name: '售后中',
                    href: '/bundle/pages/after_sale_detail/after_sale_detail'
                },
                3: {
                    name: '售后成功',
                    href: '/bundle/pages/after_sale_detail/after_sale_detail'
                },
                4: {
                    name: '售后失败',
                    href: '/bundle/pages/after_sale_detail/after_sale_detail'
                }
            },

            // 拼团状态
            teamStatus: {
                0: '拼团中',
                1: '拼团成功',
                2: '拼团失败'
            }
        }
    },

    computed: {
        OrderTypeEnum: () => OrderTypeEnum,
        orderAction() {
            return this.orderStatus[this.orderInfo.order_status] || {}
        },
        shopOpenTime() {
            if (!this.orderInfo.selffetch_shop) return
            const week = ['周一', '周二', '周三', '周四', '周五', '周六', '周日']
            let result = ''
            const weekdays = this.orderInfo.selffetch_shop?.weekdays.split(',').sort()
            const weekdayLen = weekdays.length
            if (weekdays.length == 7) {
                result = `每日${this.orderInfo.selffetch_shop.business_start_time.slice(
                    0,
                    5
                )}-${this.orderInfo.selffetch_shop.business_end_time.slice(0, 5)}`
            } else {
                weekdays.forEach((item, i) => {
                    for (let index = 1; index <= 7; index++) {
                        if (item == index) {
                            result += week[index - 1] + `${i == weekdayLen - 1 ? '' : '、'}`
                        }
                    }
                })
                result += `${this.orderInfo.selffetch_shop.business_start_time.slice(
                    0,
                    5
                )}-${this.orderInfo.selffetch_shop.business_end_time.slice(0, 5)}`
            }
            return result
        }
    },

    methods: {
        handleImage() {
            uni.previewImage({
                urls: [this.orderInfo.selffetch_shop.image]
            })
        },
        handleLocation() {
            uni.openLocation({
                latitude: Number(this.orderInfo.selffetch_shop.latitude),
                longitude: Number(this.orderInfo.selffetch_shop.longitude),
                address: this.orderInfo.selffetch_shop.detailed_address,
                name: this.orderInfo.selffetch_shop.name,
                fail: (res) => {
                    console.log(res)
                }
            })
        },

        handlePhone() {
            uni.makePhoneCall({
                phoneNumber: this.orderInfo.selffetch_shop.mobile
            })
        },
        handleshowQR() {
            if (!this.orderInfo.order_status) {
                return this.$toast({
                    title: '付款后可查看'
                })
            }

            this.showQR = true
            this.$nextTick(function () {
                const refQR = this.$refs['qrcode']
                refQR._makeCode()
            })
        },
        handletemplateCopy(str) {
            const string = str.map((item) => {
                return item.name + ':' + item.content
            })
            let result = ''
            string.forEach((i) => {
                result = result + i
            })
            copy(result)
        },
        handleCopy: copy,
        // 刷新订单数据
        refreshOrderData() {
            return apiOrderDetail({
                id: this.order_id
            })
                .then((data) => {
                    this.orderInfo = data
                    this.addressInfo = data.address
                    this.goodsList = data.order_goods
                    this.orderButtons = this.getOrderStatusButtons(data.btn) ?? []
                    return Promise.resolve(data)
                })
                .then((data) => {
                    if (data.delivery_type === 2) {
                        // 提货码
                        // this.$nextTick(function () {
                        //     const refQR = this.$refs['qrcode']
                        //     refQR._makeCode()
                        // })
                    }
                })
                .catch((err) => {
                    return Promise.reject(err)
                })
        },

        // 获取支付方式名称
        getPaywayName(payway) {
            switch (payway) {
                case 1:
                    return '余额支付'
                case 2:
                    return '微信支付'
                case 3:
                    return '支付宝支付'
                case 5:
                    return '线下支付'
            }
        },

        // 获取倒计时段
        getCountDownSegment(endTimestamp) {
            const startTimestamp = new Date().getTime() / 1000
            if (endTimestamp - startTimestamp <= 0) this.showCountDown = false
            return endTimestamp - startTimestamp
        },

        // 去商品详情
        goGoodsDetail(goods_id) {
            this.$Router.push({
                path: '/pages/goods_detail/goods_detail',
                query: { id: goods_id }
            })
        },

        // 去售后
        goAfterSale(href, id) {
            this.$Router.push({
                path: href,
                query: { id }
            })
        }
    },

    async onLoad() {
        const options = this.$Route.query
        if (!this.isLogin) return this.$Router.replace('/pages/login/login')

        try {
            if (!options.order_id) throw new Error('订单异常')
            this.order_id = options.order_id
            await this.refreshOrderData()
            this.pageStatus = PageStatusEnum['NORMAL']
        } catch (err) {
            console.log(err)
            // setTimeout(() => {
            // 	this.$Router.back()
            // }, 0.5 * 1000)
            this.pageStatus = PageStatusEnum['ERROR']
        }

        // 监听全局duringPayment事件
        // uni.$on('duringPayment', ({ result }) => {
        //     if (result === PaymentStatusEnum['SUCCESS']) {
        //         this.$Router.back()
        //         setTimeout(() => {
        //             this.$toast({ title: '支付成功' })
        //         }, 0.5 * 1000)
        //         this.refreshOrderData()
        //     }
        // })
    },

    onUnload() {
        // uni.$off('duringPayment')
    }
}
</script>

<style lang="scss" scoped>
.order-detail {
    padding: 0 20rpx calc(100rpx + 20rpx + constant(safe-area-inset-bottom)) 20rpx;
    padding: 0 20rpx calc(100rpx + 20rpx + env(safe-area-inset-bottom)) 20rpx;

    @include background_linear(); background-size: 200vw 200rpx;
    background-repeat: no-repeat;

    .contain {
        margin-top: 20rpx;
        border-radius: 7px;
        background-color: #ffffff;
    }

    // 单元格
    .cell {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: $-font-size-nr;

        &-label {
            width: 142rpx;
        }

        &-content {
            flex: 1;
            display: flex;
            align-items: center;
        }
    }
}

.order-status {
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 136rpx;
    // @include background_linear()
    color: #ffffff;

    // &::before {
    // 	z-index: 1;
    // 	position: absolute;
    // 	left: -20rpx;
    // 	right: -20rpx;
    // 	display: block;
    // 	content: "";
    // 	width: 100vw;
    // 	height: 200rpx;
    // 	@include background_linear()
    // }

    &-name {
        font-size: $-font-size-lg;
    }

    &-desc {
        font-size: $-font-size-sm;
    }
}

.order-goods {
    &__handle {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: 0 20rpx 20rpx 20rpx;

        &-button {
            padding: 8rpx 30rpx;
            border: $-solid-border;
            border-radius: 60px;
            font-size: $-font-size-sm;
        }
    }
}

.order-bill {
    padding: 20rpx;

    &__item {
        height: 60rpx;

        .cell-content {
            justify-content: flex-end;
        }
    }
}

.remark {
    padding: 24rpx 20rpx;
    align-items: flex-start !important;
}

.order-info {
    padding: 20rpx;

    &__item {
        height: 60rpx;
    }
    .copy-btn {
        font-size: 24rpx;
        padding: 6rpx 18rpx;
        border-radius: 8rpx;
        @include font_color();
        @include background_color(0.1);
    }
}

.receive {
    position: relative;

    .delivery--die {
        position: absolute;
        top: 0;
        right: 30rpx;
    }

    .receive-qr {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 460rpx;
    }

    .qr-contain {
        box-sizing: border-box;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 140px;
        height: 140px;
        border: 1px solid #cccccc;
        border-radius: 5px;

        &--die {
            position: relative;

            &::before {
                position: absolute;
                z-index: 99;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                display: block;
                content: '';
                background-color: rgba(255, 255, 255, 0.5);
            }
        }
    }

    .qr-code {
        padding: 8rpx 30rpx;
        border-radius: 60px;
        background-color: #f6f6f6;
    }

    .receive-info {
        padding-left: 20rpx;

        &-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100rpx;
            padding-right: 30rpx;

            &:nth-child(n + 2) {
                border-top: $-dashed-border;
            }
        }
    }
}

.operational {
    box-sizing: border-box;
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    height: calc(100rpx + constant(safe-area-inset-bottom));
    height: calc(100rpx + env(safe-area-inset-bottom));
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 0 20rpx constant(safe-area-inset-bottom) 20rpx;
    padding: 0 20rpx env(safe-area-inset-bottom) 20rpx;
    border-top: $-solid-border;
    background-color: #ffffff;

    .order-button {
        box-sizing: border-box;
        padding: 10rpx 30rpx;
        border-radius: 60px;
        font-size: $-font-size-nr;

        &:nth-child(n + 2) {
            margin-left: 20rpx;
        }

        &--primary {
            @include background_color();
            color: #ffffff;
        }

        &--normal {
            border-width: 1px;
            border-style: solid;
            @include border_color();
            @include font_color();
        }

        &--muted {
            border: $-solid-border;
            color: $-color-lighter;
        }
    }
}

.button--copy {
    padding: 4rpx 20rpx;
    border: $-solid-border;
    border-radius: 60px;
    font-size: $-font-size-xs;
}
.vip-price {
    margin-top: 5rpx;
    background-color: #ffe9ba;
    color: #ffd4b7;
    line-height: 36rpx;
    border-radius: 6rpx;
    overflow: hidden;

    .price-name {
        background-color: #101010;
        padding: 3rpx 12rpx;
        position: relative;
        overflow: hidden;

        &::after {
            content: '';
            display: block;
            width: 20rpx;
            height: 20rpx;
            position: absolute;
            right: -15rpx;
            background-color: #ffe9ba;
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            box-sizing: border-box;
        }
    }
}
.address-card {
    padding: 30rpx;
}
</style>
