<template>
    <view class="goods-detail">
        <!-- 轮播图 -->
        <goods-swiper :images="goodsInfo.goods_image" :video="goodsInfo.video" :video-cover="goodsInfo.video_cover" />

        <!-- 商品基础信息 -->
        <view class="goods-info">
            <view class="flex">
                <view class="flex primary">
                    <view style="font-size: 36rpx;">¥{{ goodsInfo.min_price }}</view>
                    <text v-if="goodsInfo.min_price != goodsInfo.max_price">~</text>
                    <view style="font-size: 36rpx;" v-if="goodsInfo.min_price != goodsInfo.max_price">{{ goodsInfo.max_price }}</view>
                </view>
                <view class="goods-label flex m-l-30">
                    <view v-for="item in goodsInfo.goods_activity" v-if="item == 2">秒杀</view>
                    <view v-for="item in goodsInfo.goods_activity" v-if="item == 3">砍价</view>
                    <view v-for="item in goodsInfo.goods_activity" v-if="item == 1">拼团</view>
                </view>
            </view>
            <view class="goods-name lg">{{ goodsInfo.name }}</view>
            <view class="flex row-between muted xs">
                <view>类型: {{ goodsInfo.type == 1 ? '实物商品' : '虚拟商品' }}</view>
                <view>库存: {{ goodsInfo.total_stock }}</view>
                <view>销量: {{ goodsInfo.sales_num }}</view>
            </view>
        </view>


        <!-- S 规格 -->
        <view class="m-t-20">
            <view class="nr specification" @click="onGoodsOptions">
                <text class="muted label">商品编号</text>
                <text class="m-l-20">{{ goodsInfo.code  }}</text>
            </view>
            <view class="nr specification" @click="onGoodsOptions">
                <text class="muted label">配送方式</text>
                <text class="m-l-20">{{ goodsInfo.delivery_type || '快递发货' }}</text>
            </view>
            <view class="nr specification" @click="onGoodsOptions">
                <text class="muted label">商品规格</text>
				<text class="m-l-20">查看</text>
                <u-icon name="arrow-right" class="muted" style="margin-left: auto;" />
            </view>
        </view>

        <!-- 规格选择Popup -->
        <goods-spec v-model="showGoodsSpec" :specList="goodsInfo.spec_value"  :sku-list="goodsInfo.spec_value_list" :image="goodsInfo.image"></goods-spec>

        <!-- E 规格 -->


        <!-- 商品介绍 -->
        <view class="detail">
            <view class="detail-title nr">商品详情</view>
            <u-parse :html="goodsInfo.content" :show-with-animation="true"></u-parse>
        </view>
    </view>
</template>

<script>
import {
    apiGoodsDetail
} from '@/api/goods'
import {
    baseURL,
    basePath
} from '@/config/app'
import {
    PageStatusEnum,
    OrderTypeEnum
} from '@/utils/enum'
import {
    mapGetters
} from 'vuex'
import {
    strToParams
} from '@/utils/tools'

export default {
    name: 'GoodsDetail',

    data() {
        return {
            // 页面状态
            pageStatus: PageStatusEnum['LOADING'],
            pagesData: [],
            pageErrorMsg: '', // 页面异常信息

            showShare: false,
            goodsInfo: {}, // 商品信息

            showGoodsSpec: false, // 商品规格: 显示 | 隐藏

            goodsSpecOptions: {}, // 已选商品规格
            specButtonsList: [], // 规格按钮
        }
    },

    computed: {
        ...mapGetters(['isLogin']),

        // 已选商品规格
        specValueStr() {
            return this.goodsSpecOptions?.spec?.spec_value_str ?? ''
        },

    },

    methods: {
        // 更改规格
        changeGoodsSpec(options) {
            this.goodsSpecOptions = options
            console.log(options)
        },


        // 点击已选规格
        onGoodsOptions() {
            this.showGoodsSpec = true
            // this.specButtonsList = [{
            //     ...this.specButtonsGroups.cart,
            //     style: {
            //         border: `solid 1px #40AFFA`,
            //         color: this.themeColor,
            //         backgroundColor: '#FFFFFF',
            //     }
            // },
            // this.specButtonsGroups.buy
            // ]
        },

        // 初始化商品详情数据
        initGoodsDetail() {
            return new Promise((resolve, reject) => {
                apiGoodsDetail({
                    id: this.goods_id,
                    visit: 1,
                }).then(data => {
                    this.goodsInfo = data
                    const hasComment = JSON.stringify(data.goods_comment) !== '[]'
                    this.goodsComment = hasComment ? data.goods_comment : {}
                    this.isGoodsCollect = !!data.is_collect
                }).then(data => {
                    // #ifdef H5
                    // 设置分享
                    this.$store.dispatch('setWxShare', {
                        shareImage: data?.image,
                        shareDesc: data?.name
                    })
                    // #endif
                    resolve(data)
                }).catch(err => {
                    reject(err)
                })
            })
        }
    },

    async onLoad() {
        const options = this.$Route.query

        // 商品ID赋值：点击 | 扫码
        options['scene'] ?
            this.goods_id = strToParams(options['scene'])['id'] :
            this.goods_id = options.id

        try {
            if (!this.goods_id) throw new Error('该商品不存在')

            // 商品详情数据
            await this.initGoodsDetail()
            // 促销
            this.pageStatus = PageStatusEnum['NORMAL']
        } catch (err) {
            console.log(err)
            this.pageErrorMsg = err.message
            this.pageStatus = PageStatusEnum['ERROR']
        }
    },

    onShareAppMessage() {
        const {
            name,
            image
        } = this.goodsInfo
        return {
            title: name,
            path: `/pages/goods_detail/goods_detail?id=${this.goods_id}&invite_code=${this.userInfo.code}`,
            imageUrl: image
        }
    }
}
</script>

<style lang="scss" scoped>
.goods-detail {
    padding-bottom: calc(100rpx + 20rpx + constant(safe-area-inset-bottom));
    padding-bottom: calc(100rpx + 20rpx + env(safe-area-inset-bottom));
}

.goods-info {
    padding: 20rpx 24rpx;
    background-color: #FFFFFF;

    .goods-label {
        :nth-child(1) {
            background: linear-gradient(#F95F2F, #FF2C3C);
            color: #F6f6f6;
            border-radius: 4px;
            padding: 3rpx 10rpx;
            margin-left: 10rpx;
        }

        :nth-child(2) {
            background: linear-gradient(#FEB429, #FF7D2C);
            color: #F6f6f6;
            border-radius: 4px;
            padding: 3rpx 10rpx;
            margin-left: 10rpx;
        }

        :nth-child(3) {
            background: linear-gradient(#FF369A, #FF2C3C);
            color: #F6f6f6;
            border-radius: 4px;
            padding: 3rpx 10rpx;
            margin-left: 10rpx;
        }
    }

    .primary {
        color: #FF4141;
    }

    .share {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 134rpx;
        height: 60rpx;
        margin-right: -24rpx;
        border-radius: 30px 0 0 30px;
        background-color: #F6f6f6;
    }

    .goods-name {
        padding: 20rpx 0 10rpx 0;
        font-weight: 500;
    }

    .btn {
        margin-left: 10px;
        font-size: 24rpx;
        padding: 6rpx 20rpx;
        border-radius: 8rpx;
        color: $-color-primary;
        background: rgba($color: $-color-primary, $alpha: .1);
    }
}

.specification {
    display: flex;
    padding: 24rpx;
    background-color: #FFFFFF;
    border-bottom: 1px solid #F6f6f6;

    .label {
        width: 130rpx;
    }
}

.detail {
    padding: 24rpx;
    margin-top: 20rpx;
    background-color: #FFFFFF;

    &-title {
        text-align: center;
        font-weight: 500;
        padding-bottom: 24rpx;
    }
}
</style>
