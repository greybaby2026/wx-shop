<template>
    <view class="goods-detail" :class="themeName">
        <navbar
            title="商品详情"
            :background="{ background: `rgba(256,256,256,${percent})` }"
            :titleColor="`rgba(0,0,0,${percent})`"
            :immersive="true"
        ></navbar>
        <!-- 轮播图 -->
        <goods-swiper
            :images="goodsInfo.goods_image"
            :video="goodsInfo.video"
            :video-cover="goodsInfo.video_cover"
        />
        <bubble top="220rpx" :page="2" :discharge="isDischarge"></bubble>
        <!-- 拼团信息 -->
        <view class="goods-team">
            <view class="goods-team__price">
                <view>
                    <view style="color: white">拼团优惠</view>
                    <view>
                        <text class="tip"> {{ activityInfo.people_num }}人团 </text>
                        <price
                            :content="activityInfo.min_team_price"
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

            <view class="goods-team__time">
                <text class="xs" style="color: white">距离活动结束</text>
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
                <view class="goods-name">
                    {{ goodsInfo.name }}
                </view>
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
            <view v-if="goodsInfo.is_sales_num">销量: {{ goodsInfo.sales_num }}</view>
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
                price: activityInfo.min_team_price,
                stock: goodsInfo.total_stock,
                unit: goodsInfo.unit_name,
                limit_type: activityInfo.max_buy == 0 ? 1 : 3,
                limit_value: activityInfo.max_buy
            }"
            @closer="showGoodsSpec = false"
            :stock_show="goodsInfo.stock_show"
            :goods="goodsInfo.id"
            :buttons="specButtonsList"
            :spec-list="goodsInfo.spec_value"
            :spec-map="goodsInfo.spec_value_list"
            @join="handleJoin"
            @team="handleTeam"
            @change="changeGoodsSpec"
        />

        <!-- S 拼团 -->
        <!-- 拼团玩法 -->
        <view class="team-desc">
            <view class="team-desc__title">拼团玩法</view>
            <view class="team-desc__main">
                <view class="team-desc__item"><text class="num">1</text> 开团/参团</view>
                <view class="team-desc__item--space"></view>
                <view class="team-desc__item"><text class="num">2</text>团满即成新团</view>
                <view class="team-desc__item--space"></view>
                <view class="team-desc__item"><text class="num">3</text>满员发货</view>
            </view>
        </view>

        <!-- 拼团队伍 -->
        <view class="team-activity" v-if="activityTeamList.length">
            <view class="team-activity__title">正在拼团中</view>
            <view
                class="team-activity__item"
                v-for="(item, index) in activityTeamList"
                :key="item.id"
            >
                <view class="flex-1 flex row-between">
                    <view class="flex">
                        <u-image :src="item.avatar" width="80" height="80" shape="circle" />
                        <text class="m-l-20 nr">{{ item.nickname }}</text>
                    </view>
                    <view class="flex-col col-bottom">
                        <view class="sm">
                            <text>还差</text>
                            <text :style="{ color: themeColor }">{{
                                item.people - item.join
                            }}</text>
                            <text>人成团</text>
                        </view>
                        <view class="flex xs muted">
                            <text class="m-r-10">剩余</text>
                            <count-down
                                :time-end="item.invalid_time"
                                color="#999999"
                                separator-color="#999999"
                                bg-color="transparent"
                                height="24"
                            />
                        </view>
                    </view>
                </view>
                <view class="team-activity__item-button" @click="joinTeamOrder(item.id)"
                    >去参团</view
                >
            </view>
        </view>
        <!-- E 拼团 -->

        <!-- diy -->
        <view v-for="(item, index) in pagesData" :key="index">
            <template v-if="item.name == 'reviews'">
                <w-reviews
                    v-if="item.show"
                    :content="item.content"
                    :styles="item.styles"
                    :comment="goodsComment"
                    :goods-id="goodsInfo.id"
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
                <view class="icon-item" @tap="gotoService">
                    <u-icon name="kefu-ermai" size="42" />
                    <text style="line-height: 32rpx">客服</text>
                </view>
            </view>
            <view class="button-group">
                <view class="button-item button-item--normal" @click="handleBuy">单独购买</view>
                <view class="button-item button-item--primary" @click="onTeam">立即开团</view>
            </view>
        </view>

        <!-- 分享 -->
        <share-popup
            v-model="showShare"
            :share-id="team_id"
            page-path="bundle/pages/goods_team_detail/goods_team_detail"
            :type="3"
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
import { apiGoodsTeamDetail, apiGoodsTeamPlace } from '@/api/goods'
import { apiCartAdd, apiCouponGet } from '@/api/store'
import { apiGoodsCollect, apiGoodsMarketing } from '@/api/goods'
import { baseURL, basePath } from '@/config/app'
import { apiGetPage } from '@/api/store'
import { PageStatusEnum, OrderTypeEnum } from '@/utils/enum'
import { mapGetters } from 'vuex'
import { strToParams } from '@/utils/tools'
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
                service_guarantee: []
            }, // 商品信息
            goodsComment: {}, // 用户评价
            team_id: '', // 拼团ID
            goods_id: '', // 商品ID
            showSafeguard: false, //保障 ：显示｜隐藏

            isGoodsCollect: false, // 商品收藏

            showGoodsSpec: false, // 商品规格: 显示 | 隐藏

            goodsTeamFoundID: '', // 参团ID
            goodsSpecOptions: {}, // 已选商品规格
            specButtonsList: [], // 规格按钮

            // 规格按钮组
            specButtonsGroups: {
                team: {
                    event: 'team',
                    text: '立即开团'
                },
                join: {
                    event: 'join',
                    text: '确认'
                }
            }
        }
    },

    computed: {
        ...mapGetters(['isLogin', 'ServiceConfig', 'userInfo', 'appConfig', 'shareConfig']),

        // 拼团活动信息
        activityInfo() {
            return this.goodsInfo.activity ?? {}
        },

        // 选中规格信息
        goodsSpecInfo() {
            return this.goodsSpecOptions.spec ?? {}
        },

        // 开团列表
        activityTeamList() {
            return this.goodsInfo.found ?? []
        },

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
                goods_id: this.goodsInfo.id
            }).then((data) => {
                const { common, content } = data

                this.pagesData = content
                uni.setNavigationBarTitle({ title: common.title })
            })
        },

        // 更改规格
        changeGoodsSpec(options) {
            this.goodsSpecOptions = options
            console.log(options)
        },

        // 点击参团按钮
        onTeam() {
            this.showGoodsSpec = true
            this.specButtonsList = [this.specButtonsGroups.team]
        },

        // 更改商品收藏状态
        changeCollect(value) {
            value = !!value ? 0 : 1
            if (!this.isLogin) return this.$Router.push('/pages/login/login')

            apiGoodsCollect({
                goods_id: this.goods_id,
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

        // 点击已选规格
        onGoodsOptions() {
            this.showGoodsSpec = true
            this.specButtonsList = [this.specButtonsGroups.team]
        },

        // 处理 开团
        handleTeam({ spec, number }) {
            if (!this.isLogin) return this.$Router.push('/pages/login/login')

            this.$Router.push({
                path: '/pages/goods_order/goods_order',
                query: {
                    from: OrderTypeEnum['TEAM'],
                    address_id: this.addressId,

                    payload: {
                        team_id: this.goodsInfo.activity.id,
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

        // 处理 参团
        handleJoin({ spec, number }) {
            if (!this.isLogin) return this.$Router.push('/pages/login/login')
            this.$Router.push({
                path: '/pages/goods_order/goods_order',
                query: {
                    address_id: this.addressId,
                    from: OrderTypeEnum['TEAM'],
                    payload: {
                        found_id: this.goodsTeamFoundID,
                        team_id: this.goodsInfo.activity.id,
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

        // 点击参团
        joinTeamOrder(foundID) {
            this.showGoodsSpec = true
            this.goodsTeamFoundID = foundID
            this.specButtonsList = [this.specButtonsGroups.join]
        },

        // 处理 单独购买
        handleBuy() {
            this.$Router.replace({
                path: '/pages/goods_detail/goods_detail',
                query: {
                    id: this.goods_id
                }
            })
        },

        // 初始化商品详情数据
        initGoodsDetail() {
            return new Promise((resolve, reject) => {
                apiGoodsTeamDetail({
                    id: this.team_id,
                    visit: 1,
                    address_id: this.addressId
                })
                    .then((data) => {
                        this.goodsInfo = data
                        this.goods_id = data.id
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
        options['scene']
            ? (this.team_id = strToParams(options['scene'])['id'])
            : (this.team_id = options.id)

        try {
            if (!this.team_id) throw new Error('该商品不存在')

            // 商品详情数据
            await this.initGoodsDetail()
            // 页面DIY
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
                .replace(/\{price\}/g, this.activityInfo.min_team_price)
            const sharedesc = shareconfig.synopsis
                .replace(/\{goodname\}/g, this.goodsInfo.name)
                .replace(/\{price\}/g, this.activityInfo.min_team_price)
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
            path: `bundle/pages/goods_team_detail/goods_team_detail?id=${this.team_id}&invite_code=${this.userInfo.code}`,
            imageUrl: image
        }
    },
	//分享朋友圈
	onShareTimeline() {
	const { name, image } = this.goodsInfo
		return {
			title: name,
			query: `id=${this.team_id}&invite_code=${this.userInfo.code}`, // 分享参数，注意这里使用query而不是path
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
.button {
    padding: 0 !important;
}
.goods-detail {
    padding-bottom: calc(100rpx + 20rpx + constant(safe-area-inset-bottom));
    padding-bottom: calc(100rpx + 20rpx + env(safe-area-inset-bottom));
}

.goods-team {
    display: flex;
    height: 100rpx;
    background: url(../../static/images/temp-bg.png) no-repeat;
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
        .tip {
            font-size: 24rpx;
            color: black;
            background-color: white;
            padding: 5rpx 10rpx;
            border-radius: 10rpx;
            margin-right: 10rpx;
        }
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

.team-activity {
    padding: 0 24rpx;
    margin: 20rpx 20rpx 0 20rpx;
    background-color: #ffffff;
    border-radius: 30rpx;
    &__title {
        height: 80rpx;
        line-height: 80rpx;
        font-size: $-font-size-nr;
    }

    &__item {
        display: flex;
        align-items: center;
        height: 120rpx;

        &-button {
            display: flex;
            align-items: center;
            height: 58rpx;
            border-radius: 60px;
            padding: 0 28rpx;
            margin-left: 30rpx;
            color: #ffffff;
            @include background_color();
        }
    }
}

.team-desc {
    padding: 0 24rpx;
    margin: 20rpx 20rpx 0 20rpx;
    background-color: #ffffff;
    border-radius: 30rpx;

    &__title {
        height: 80rpx;
        line-height: 80rpx;
        font-size: $-font-size-nr;
    }

    &__main {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 90rpx;
        border-top: $-solid-border;
    }

    &__item {
        font-size: $-font-size-xs;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        .num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 28rpx;
            width: 28rpx;
            border: 1px solid $-color-normal;
            border-radius: 50%;
            margin-right: 4rpx;
        }

        &--space {
            flex: 1;
            border-top: $-dotted-border;
            margin: 0 20rpx;
        }
    }
}

.specification {
    padding: 50rpx 24rpx 1rpx 24rpx;

    border-radius: 30rpx;
    margin: 20rpx 20rpx;
    background-color: #ffffff;
    .specification-item {
        display: flex;
    }
}

.detail {
    padding: 24rpx;
    margin: 20rpx 20rpx 0rpx 20rpx;
    background-color: #ffffff;
    border-radius: 30rpx;
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
            width: 90rpx;
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

            &--normal {
                @include font_color();
                @include border_color();

                border-width: 1px;
                border-style: solid;
            }

            &--primary {
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
