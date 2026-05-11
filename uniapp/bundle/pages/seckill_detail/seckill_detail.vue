<template>
    <view class="goods-detail" :class="themeName">
        <navbar
            title="商品详情"
            :background="{ background: `rgba(256,256,256,${percent})` }"
            :titleColor="`rgba(0,0,0,${percent})`"
            :immersive="true"
        ></navbar>
        <bubble top="220rpx" :page="2" :discharge="isDischarge"></bubble>
        <!-- 轮播图 -->
        <goods-swiper
            :images="goodsInfo.goods_image"
            :video="goodsInfo.video"
            :video-cover="goodsInfo.video_cover"
        />
        <!-- 秒杀信息 -->
        <view class="goods-seckill">
            <view class="goods-seckill__price">
                <view>
                    <view style="color: white">限时秒杀</view>
                    <view>
                        <price
                            :content="activityInfo.min_seckill_price"
                            main-size="46rpx"
                            minor-size="32rpx"
                            color="#FFFFFF"
                        />
                        <price
                            style="margin-left: 10rpx"
                            color="white"
                            :content="goodsInfo.min_price"
                            :line-through="true"
                            v-if="Number(goodsInfo.min_price)"
                        />
                    </view>
                </view>
            </view>

            <view class="goods-seckill__time">
                <text class="xs m-b-10" style="color: white">距离活动结束</text>
                <count-down
                    :time-end="activityInfo.end_time"
                    color="black"
                    bg-color="white"
                    separator-color="white"
                />
            </view>
        </view>
        <!-- 商品基础信息 -->
        <view class="goods-info">
            <view class="row-between goods-main">
                <view class="goods-name lg m-r-20">{{ goodsInfo.name }}</view>
                <view class="goods-items">
                    <view class="goods-item" @tap="showShare = true">
                        <u-icon name="share" size="36" />
                        <text class="m-t-5 muted xxs">分享</text>
                    </view>
                    <view class="goods-item" @tap="changeCollect(isGoodsCollect)">
                        <template v-if="isGoodsCollect">
                            <u-icon name="star-fill" :color="themeColor" size="36" />
                        </template>
                        <template v-else>
                            <u-icon name="star" size="36" />
                        </template>
                        <text class="m-t-5 muted xxs">收藏</text>
                    </view>
                </view>
            </view>
        </view>
        <view class="flex row-between muted xs goods-tip">
            <template v-if="goodsInfo.stock_show">
                <view>库存: {{ goodsInfo.total_stock }}</view>
            </template>
            <view v-if="goodsInfo.is_sales_num">已抢: {{ activityInfo.sales_volume }}</view>
            <view v-if="goodsInfo.is_click_num">浏览量: {{ activityInfo.browse_volume }}</view>
        </view>
        <view class="specification">
            <view
                class="nr specification-item m-b-50"
                @click="handleSend"
                v-if="goodsInfo.type == 1"
            >
                <text class="muted flex-none">配送</text>
                <view class="m-l-20">
                    <view v-if="!goodsInfo.address">免运费 </view>
                    <view v-else
                        >快递：{{ goodsInfo.free_shipping_tips || '以提交订单时为准' }}
                    </view>
                    <view class="xs muted skuline" v-if="goodsInfo.address"
                        >配送至：{{
                            goodsInfo.address.province +
                            ' ' +
                            goodsInfo.address.city +
                            ' ' +
                            goodsInfo.address.district
                        }}
                    </view>
                    <view class="xs muted skuline" v-if="!goodsInfo.address">请完善地址信息 </view>
                </view>
                <u-icon name="arrow-right" class="muted" style="margin-left: auto" />
            </view>
            <!-- S 规格 -->
            <view class="nr specification-item m-b-50" @click="onGoodsOptions">
                <text class="muted flex-none">选择</text>
                <text class="m-l-20 skuline">已选：{{ specValueStr }}</text>
                <u-icon name="arrow-right" class="muted" style="margin-left: auto" />
            </view>
            <view
                class="nr specification-item m-b-42"
                @click="showSafeguard = true"
                v-if="goodsInfo.service_guarantee.length"
            >
                <text class="muted flex-none m-r-20">保障</text>
                <view class="skuline">
                    <text v-for="(item, index) in goodsInfo.service_guarantee" :key="item.id"
                        >{{ item.name
                        }}<text
                            v-show="index != goodsInfo.service_guarantee.length - 1"
                            class="m-l-10 m-r-10"
                            >·</text
                        ></text
                    >
                </view>
                <u-icon name="arrow-right" class="muted" style="margin-left: auto" />
            </view>
        </view>
        <!-- S 保障popup -->
        <u-popup
            v-model="showSafeguard"
            mode="bottom"
            border-radius="14"
            :closeable="false"
            :safe-area-inset-bottom="true"
            mask-close-able
        >
            <view class="coupons">
                <view class="coupons-header">
                    <text>保障</text>
                    <u-icon name="close" color="#666666" @tap="showSafeguard = false" />
                </view>

                <view class="coupons-main" style="background-color: white">
                    <scroll-view :scroll-y="true" style="height: 700rpx; touch-action: none">
                        <view
                            class="coupons-item"
                            v-for="item in goodsInfo.service_guarantee"
                            :key="item.id"
                        >
                            <view style="font-size: 28rpx; font-weight: 500">{{ item.name }}</view>
                            <view class="muted m-t-10">{{ item.content }}</view>
                        </view>
                    </scroll-view>
                    <button class="popup-btn" @click="showSafeguard = false">我知道了</button>
                </view>
            </view>
        </u-popup>
        <!-- E 保障 -->
        <!-- 规格选择Popup -->
        <goods-spec
            v-model="showGoodsSpec"
            :defaultInfo="{
                image: goodsInfo.image,
                price: activityInfo.min_seckill_price,
                stock: goodsInfo.total_stock,
                unit: goodsInfo.unit_name,
                limit_type: activityInfo.max_buy == 0 ? 1 : 3,
                limit_value: activityInfo.max_buy
            }"
            :stock_show="goodsInfo.stock_show"
            price-key="seckill_price"
            :spec-list="goodsInfo.spec_value"
            :spec-map="goodsInfo.spec_value_list"
            :goods="goodsInfo.id"
            :buttons="specButtonsList"
            @buy="handleBuy"
            @change="changeGoodsSpec"
            @closer="showGoodsSpec = false"
        />
        <!-- E 规格 -->

        <!-- diy -->
        <view v-for="(item, index) in pagesData" :key="index">
            <template v-if="item.name == 'reviews'">
                <w-reviews
                    v-if="item.show"
                    :content="item.content"
                    :styles="item.styles"
                    :comment="goodsComment"
                    :goods-id="goodsID"
                />
            </template>
            <template v-if="item.name == 'goodsrecom'">
                <w-goodsrecom v-if="item.show" :content="item.content" :styles="item.styles" />
            </template>
        </view>

        <!-- 商品介绍 -->
        <view class="detail">
            <view class="detail-title nr">商品详情</view>
            <u-parse :html="goodsInfo.content" :show-with-animation="true"></u-parse>
        </view>

        <!-- 功能按钮组 -->
        <view class="functions">
            <view class="icon-group">
                <view class="icon-item" @tap="goHome">
                    <u-icon name="home" size="42" />
                    <text style="line-height: 32rpx">首页</text>
                </view>

                <view class="icon-item" @tap="gotoService" v-if="ServiceConfig.way != 5">
                    <u-icon name="kefu-ermai" size="42" />
                    <text style="line-height: 32rpx">客服</text>
                </view>
            </view>
            <view class="button-group">
                <view class="button-item button-item--cart">
                    <router-link
                        :to="{
                            path: '/pages/goods_detail/goods_detail',
                            query: { id: goodsInfo.id }
                        }"
                    >
                        原价购买</router-link
                    >
                </view>

                <view class="button-item button-item--buy" @click="onBuy">立即抢购</view>
            </view>
        </view>
        <share-popup
            v-model="showShare"
            :share-id="seckillID"
            page-path="bundle/pages/seckill_detail/seckill_detail"
            :type="2"
            :config="{
                image: goodsInfo.image,
                name: goodsInfo.name
            }"
        />
        <!-- 页面状态 -->
        <page-status :status="pageStatus">
            <template #error>
                <u-empty
                    :text="pageErrorMsg"
                    src="/static/images/empty/shop.png"
                    :icon-size="280"
                ></u-empty>
            </template>
        </page-status>
        <u-back-top
            :scroll-top="scrollTop"
            :top="1000"
            :customStyle="{
                backgroundColor: '#FFF',
                color: '#000',
                boxShadow: '0px 3px 6px rgba(0, 0, 0, 0.1)'
            }"
        ></u-back-top>
    </view>
</template>

<script>
import { apiSeckillDetail } from '@/api/activity'
import { apiCouponGet } from '@/api/store'
import { apiGoodsCollect, apiGoodsMarketing } from '@/api/goods'

import { apiGetPage } from '@/api/store'
import { PageStatusEnum } from '@/utils/enum'
import { mapGetters } from 'vuex'
import { strToParams } from '@/utils/tools'
import { OrderTypeEnum } from '@/utils/enum'
import ServiceMixin from '@/mixins/service'

export default {
    name: 'GoodsDetail',
    mixins: [ServiceMixin],

    data() {
        return {
            addressId: '',
            isDischarge: false,
            // 页面状态
            pageStatus: PageStatusEnum['LOADING'],
            pagesData: [],
            pageErrorMsg: '', // 页面异常信息
            scrollTop: 0,
            percent: 0,
            showShare: false,
            goodsInfo: {
                free_shipping_tips: '',
                service_guarantee: [],
                address: {}
            }, // 商品信息
            goodsComment: {}, // 用户评价
            seckillID: '', // 秒杀ID
            goodsID: '', // 商品ID
            isGoodsCollect: false, // 商品收藏
            showSafeguard: false, //保障 ：显示｜隐藏

            showGoodsSpec: false, // 商品规格: 显示 | 隐藏
            goodsSpecOptions: {}, // 已选商品规格
            specButtonsList: [], // 规格按钮

            // 规格按钮组
            specButtonsGroups: {
                buy: {
                    event: 'buy',
                    text: '立即抢购'
                }
            },
            activityInfo: {}
        }
    },

    computed: {
        ...mapGetters(['isLogin', 'ServiceConfig', 'userInfo', 'appConfig', 'shareConfig']),

        // 已选商品规格
        specValueStr() {
            return this.goodsSpecOptions?.spec?.spec_value_str ?? ''
        }
    },

    methods: {
        gotoService() {
            this.$Router.push({
                path: '/bundle/pages/artificial_service/artificial_service'
            })
        },
        handleSend() {
            uni.$once('changeAddress', async (id) => {
                this.addressId = id
                await this.initGoodsDetail()
            })
            this.$Router.push({
                path: '/pages/address/address',
                query: {
                    type: 1
                }
            })
        },
        // 获取装修页面
        getPage() {
            apiGetPage({
                type: 5,
                goods_id: this.goodsID
            }).then((data) => {
                const { common, content } = data

                this.pagesData = content
                uni.setNavigationBarTitle({
                    title: common.title
                })
            })
        },
        // 更改规格
        changeGoodsSpec(options) {
            this.goodsSpecOptions = options
            console.log(options)
        },

        // 点击加入购物车按钮
        onCart() {
            this.showGoodsSpec = true
            this.specButtonsList = [this.specButtonsGroups.cart]
        },

        // 更改商品收藏状态
        changeCollect(value) {
            value = !!value ? 0 : 1
            if (!this.isLogin) return this.$Router.push('/pages/login/login')

            apiGoodsCollect({
                goods_id: this.goodsID,
                is_collect: value
            })
                .then((data) => {
                    this.isGoodsCollect = value
                })
                .catch((err) => {
                    console.log(err)
                })
        },
        goHome() {
            this.$Router.replaceAll({
                path: '/pages/index/index'
            })
        },

        // 去购物车
        goCart() {
            this.$Router.replaceAll({
                path: '/pages/shop_cart/shop_cart'
            })
        },

        // 点击立即购买按钮
        onBuy() {
            this.showGoodsSpec = true
            this.specButtonsList = [this.specButtonsGroups['buy']]
        },

        // 点击已选规格
        onGoodsOptions() {
            this.showGoodsSpec = true
            this.specButtonsList = [this.specButtonsGroups['buy']]
        },

        // 处理 立即购买
        handleBuy({ spec, number }) {
            this.$Router.push({
                path: '/pages/goods_order/goods_order',
                query: {
                    from: OrderTypeEnum['SECKILL'],
                    payload: {
                        address_id: this.addressId,

                        seckill_id: this.activityInfo?.id,
                        goods: [
                            {
                                goods_num: number,
                                item_id: spec.id
                            }
                        ]
                    }
                }
            })
        },

        // 初始化商品详情数据
        initGoodsDetail() {
            return new Promise((resolve, reject) => {
                apiSeckillDetail({
                    id: this.seckillID,
                    address_id: this.addressId
                })
                    .then((data) => {
                        this.goodsInfo = data
                        this.goodsID = data.id
                        this.activityInfo = data.activity
                        const hasComment = JSON.stringify(data.goods_comment) !== '[]'
                        this.goodsComment = hasComment ? data.goods_comment : {}
                        this.isGoodsCollect = !!data.is_collect
                        return data
                    })
                    .then((data) => {
                        // #ifdef H5
                        // 设置分享
                        this.$store.dispatch('setWxShare', {
                            shareImage: data?.image,
                            shareDesc: data?.name
                        })
                        // #endif
                        resolve(data)
                    })
                    .catch((err) => {
                        reject(err)
                    })
            })
        }
    },
    onPageScroll(e) {
        const top = uni.upx2px(100)
        const { scrollTop } = e
        this.percent = scrollTop / top > 1 ? 1 : scrollTop / top
        this.scrollTop = scrollTop
    },
    async onLoad() {
        const options = this.$Route.query

        // 商品ID赋值：点击 | 扫码
        this.seckillID = options['scene'] ? strToParams(options['scene'])['id'] : options.id

        try {
            if (!this.seckillID) throw new Error('该商品不存在')

            // 商品详情数据
            await this.initGoodsDetail()
            // diy信息
            await this.getPage()
            this.pageStatus = PageStatusEnum['NORMAL']
        } catch (errMsg) {
            console.log(errMsg)
            this.pageErrorMsg = errMsg
            this.pageStatus = PageStatusEnum['ERROR']
        }
        // #ifdef H5
        setTimeout(() => {
            const shareconfig = this.shareConfig.find((i) => {
                return i.type == 2 && i.page == 2
            })
            const titile = shareconfig.title
                .replace(/\{goodname\}/g, this.goodsInfo.name)
                .replace(/\{price\}/g, this.activityInfo.min_seckill_price)
            const sharedesc = shareconfig.synopsis
                .replace(/\{goodname\}/g, this.goodsInfo.name)
                .replace(/\{price\}/g, this.activityInfo.min_seckill_price)
            // 设置分享
            this.$store.dispatch('setWxShare', {
                shareTitle: titile,
                shareImage: shareconfig.image,
                shareDesc: sharedesc
            })
        }, 1000)

        // #endif
    },

    onShareAppMessage() {
        const { name, image } = this.goodsInfo
        return {
            title: name,
            path: `/bundle/pages/seckill_detail/seckill_detail?id=${this.seckillID}&invite_code=${this.userInfo.code}`,
            imageUrl: image
        }
    },
	
	//分享朋友圈
	onShareTimeline() {
	const { name, image } = this.goodsInfo
		return {
			title: name,
			query: `id=${this.seckillID}&invite_code=${this.userInfo.code}`, // 分享参数，注意这里使用query而不是path
			imageUrl: image
	
		}
	},
	
    onHide() {
        this.isDischarge = true
    },
    onShow() {
        this.isDischarge = false
    }
}
</script>

<style lang="scss" scoped>
.goods-detail {
    padding-bottom: calc(100rpx + 20rpx + constant(safe-area-inset-bottom));
    padding-bottom: calc(100rpx + 20rpx + env(safe-area-inset-bottom));
}

.goods-seckill {
    display: flex;
    height: 100rpx;
    background: url(../../static/images/seckill-bg.png) no-repeat;
    background-size: contain;
    height: 240rpx;
    padding: 30rpx 24rpx;
    &__price {
        color: white;
        flex: 1;
        display: flex;
        justify-content: space-between;
        padding: 0 20rpx;
        // @include background_linear();
    }

    &__time {
        display: flex;
        flex-direction: column;
        align-items: center;

        width: 240rpx;
        // @include background_color(0.3);
    }
}

.goods-info {
    padding: 20rpx 24rpx;
    background-color: #ffffff;
    margin: -90rpx 20rpx 0rpx 20rpx;
    border-radius: 30rpx 30rpx 0 0;
    .share {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 134rpx;
        height: 60rpx;
        margin-right: -24rpx;
        border-radius: 30px 0 0 30px;
        background-color: #f6f6f6;
    }

    .goods-main {
        display: flex;
        justify-content: space-between;
        padding: 20rpx 0 20rpx 0;

        .goods-name {
            font-weight: 500;
        }
        .goods-items {
            flex-shrink: 0;
            width: 150rpx;
            display: flex;
            justify-content: flex-end;

            .goods-item {
                font-size: 28rpx;
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-left: 20rpx;
            }
        }
    }
}
.goods-tip {
    margin: 2rpx 20rpx 0 20rpx;
    border-radius: 0rpx 0rpx 30rpx 30rpx;
    background-color: #ffffff;
    padding: 20rpx 24rpx;
}
.discounts {
    display: flex;
    padding: 24rpx;
    margin-top: 20rpx;
    background-color: #ffffff;

    .coupon-tag {
        box-sizing: border-box;
        position: relative;
        display: flex;
        justify-content: center;
        min-width: 140rpx;
        height: 36rpx;
        padding: 0 20rpx;
        border-radius: 4px;
        margin-right: 10rpx;
        color: #ffffff;

        @include background_color();
        &::before,
        &::after {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            display: block;
            content: '';
            width: 10rpx;
            height: 10rpx;
            border-radius: 50%;
            background-color: #ffffff;
        }

        &::before {
            right: -5rpx;
        }

        &::after {
            left: -5rpx;
        }
    }
}

.specification {
    border-radius: 30rpx;
    padding: 50rpx 24rpx 1rpx 24rpx;
    margin: 20rpx;
    background-color: #ffffff;
    .specification-item {
        display: flex;
    }
}

.detail {
    padding: 24rpx;
    margin: 20rpx 20rpx 0rpx 20rpx;
    border-radius: 30rpx;
    background-color: #ffffff;

    &-title {
        text-align: center;
        font-weight: 500;
        padding-bottom: 24rpx;
    }
    ::v-deep image {
        vertical-align: top;
    }

    // #ifdef H5
    ::v-deep img {
        vertical-align: top;
        display: inline;
    }

    // #endif
    // #ifdef MP-WEIXIN || APP-PLUS
    ::v-deep ._img {
        vertical-align: top;
        display: inline;
    }

    // #endif
}

.functions {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    height: calc(100rpx + constant(safe-area-inset-bottom));
    height: calc(100rpx + env(safe-area-inset-bottom));
    padding: 0 24rpx constant(safe-area-inset-bottom) 24rpx;
    padding: 0 24rpx env(safe-area-inset-bottom) 24rpx;
    border-top: $-solid-border;
    background-color: #ffffff;

    .icon-group {
        display: flex;
        justify-content: space-around;
        font-size: $-font-size-xxs;
        color: $-color-lighter;
        width: 200rpx;

        .icon-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    }

    .button-group {
        flex: 1;
        display: flex;
        justify-content: space-between;

        .button-item {
            flex: 1;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 74rpx;
            margin-left: 14rpx;
            border-radius: 60px;

            &--cart {
                @include font_color();
                @include border_color();

                border-width: 1px;
                border-style: solid;
            }

            &--buy {
                @include background_color();
                color: #ffffff;
            }
        }
    }
}

.coupons {
    background-color: #f6f6f6;

    &-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100rpx;
        padding: 0 30rpx;
        font-weight: 500;
        font-size: $-font-size-md;
        background-color: #ffffff;
    }

    &-main {
        padding: 20rpx;

        .coupons-item {
            &:nth-child(n + 2) {
                margin-top: 20rpx;
            }
        }
    }
}
.coupons {
    background-color: #f6f6f6;

    &-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100rpx;
        padding: 0 30rpx;
        font-weight: 500;
        font-size: $-font-size-md;
        background-color: #ffffff;
    }

    &-main {
        padding: 20rpx;

        .coupons-item {
            &:nth-child(n + 2) {
                margin-top: 20rpx;
            }
        }
    }
    .popup-btn {
        @include background_color();
        color: white;
        height: 80rpx;
        line-height: 80rpx;
        border-radius: 50rpx;
        font-size: 32rpx;
        margin-top: 20rpx;
    }
}
</style>
