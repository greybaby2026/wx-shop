<template>
  <view class="goods-detail" :class="themeName">
    <!-- #ifndef H5 -->
    <navbar title="商品详情" :background="{ background: `rgba(256,256,256,${percent})` }"
      :titleColor="`rgba(0,0,0,${percent})`" :immersive="true"></navbar>
    <!-- #endif -->
    <!-- 轮播图 -->
    <bubble top="220rpx" :page="2" :discharge="isDischarge"></bubble>
    <goods-swiper :images="goodsInfo.goods_image" :video="goodsInfo.video" :video-cover="goodsInfo.video_cover" />
    <view class="info-header" v-if="
      goodsInfo.member_price !== '' &&
      !!userInfo.id &&
      goodsInfo.member_level.name
    " :style="'background-image: url(' +
        $getImageUri('resource/image/shopapi/default/good_vip.png') +
        ')'
        ">
      <view class="vip-name">
        <view class="line1"> {{ goodsInfo.member_level.name }}价 </view>
      </view>
      <price :content="goodsInfo.member_price" :weight="500" color="#E0A356" mainSize="48rpx" minorSize="48rpx">
      </price>
      <!-- 原价 -->
      <price style="margin-left: 10rpx" color="gray" :content="goodsSpecOptions.spec.sell_price || goodsInfo.sell_price"
        :line-through="true" v-if="goodsInfo.is_show_price && Number(goodsSpecOptions.spec.sell_price || goodsInfo.sell_price)" />
    </view>
    <!-- 商品基础信息 -->
    <view class="goods-info" :style="{
      'margin-top':
        goodsInfo.member_price !== '' && !!userInfo.id ? '-90rpx' : '30rpx'
    }">
      <view class="flex row-between col-top">
        <view class="flex" v-if="
          !(
            goodsInfo.member_price !== '' &&
            !!userInfo.id &&
            goodsInfo.member_level.name
          )
        ">
          <!-- 原价 -->
          <price :content="goodsSpecOptions.spec.sell_price || goodsInfo.sell_price" main-size="46rpx"
            minor-size="32rpx" fontWeight="bolder" :color="themeColor">
            <view slot="suffix" class="muted line-through m-l-12 xxs">
              <!-- 划线价 -->
              <price :content="goodsSpecOptions.spec.lineation_price ||
                goodsInfo.lineation_price
                " :line-through="true" v-if="Number(goodsInfo.lineation_price)" />
            </view>
          </price>
        </view>
      </view>
      <view class="flex discounts" @tap="showCoupons = true" v-if="goodsCouponSpecs.length">
        <block v-for="item in goodsCouponSpecs" :key="item">
          <view class="coupon-tag xxs" v-if="item.condition_type == 1">无门槛¥{{ handleSpot(item.money) }}</view>

          <view class="coupon-tag xxs" v-if="item.condition_type == 2">
            满¥{{ handleSpot(item.condition_money) }}减¥{{
              handleSpot(item.money)
            }}</view>
          <view class="coupon-tag xxs" v-if="item.condition_type == 3">
            满¥{{ handleSpot(item.condition_money) }}打{{
              handleSpot(item.discount_ratio)
            }}折</view>
        </block>
        <view style="margin-left: auto" :style="{ color: themeColor }">
          <text>领券</text>
          <u-icon name="arrow-right" class="muted nr" :style="{ color: themeColor }" />
        </view>
      </view>

      <view class="lg goods-main">
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
    <view class="flex row-between muted xs goods-stock"
	 :style="{'margin': (goodsInfo.stock_show || goodsInfo.is_sales_num || goodsInfo.is_click_num) ? '1rpx 20rpx 30rpx 20rpx' : '-20rpx 20rpx 30rpx 20rpx'}"
	 >
      <template v-if="goodsInfo.stock_show">
        <view>库存: {{ goodsInfo.total_stock }}</view>
      </template>
      <view v-if="goodsInfo.is_sales_num">销量: {{ goodsInfo.sales_num || "0" }}</view>
      <view v-if="goodsInfo.is_click_num">浏览量: {{ goodsInfo.click_num || "0" }}</view>
    </view>

    <!-- S 优惠券popup -->
    <u-popup v-model="showCoupons" mode="bottom" border-radius="14" :closeable="false" :safe-area-inset-bottom="true"
      mask-close-able>
      <view class="coupons">
        <view class="coupons-header">
          <text>领券</text>
          <u-icon name="close" color="#666666" @tap="showCoupons = false" />
        </view>

        <view class="coupons-main">
          <scroll-view :scroll-y="true" style="height: 700rpx; touch-action: none">
            <view class="coupons-item" v-for="item in goodsCouponList" :key="item.id">
              <coupon-card mode="get" :discount_max_money="item.discount_max_money"
                :discount_ratio="item.discount_ratio" :condition_type="item.condition_type" :name="item.name"
                :money="item.money" :condition="item.condition" :time="item.effective_time" :scene="item.use_scene"
                :coupon-id="item.id" :gotten="!!item.is_receive" :button="{
                  name: '领取',
                  theme: 'primary',
                  disable: !!item.is_available,
                }" @button="getCoupon" />
            </view>
            <u-empty v-if="!goodsCouponList.length" text="暂无优惠券" src="/static/images/empty/coupon_icon.png"
              :icon-size="180" />
          </scroll-view>
        </view>
      </view>
    </u-popup>
    <!-- E 优惠券 -->

    <view class="nr commission flex" v-if="distribution.is_show && distribution.earnings > 0">
      <text class="flex-none">佣金</text>
      <text class="m-l-20" style="color: red">{{ Number(distribution.ratio).toFixed(2) }}%</text>
      <text class="m-l-20 muted">( 预计{{ distribution.earnings }} ) </text>
      <view style="margin-left: auto">
        <view @tap="showShare = true">
          立即分享
          <u-icon name="arrow-right" />
        </view>
      </view>
    </view>
    <view class="specification">
      <view class="nr specification-item m-b-50" @click="handleSend" v-if="goodsInfo.type == 1">
        <text class="muted flex-none">配送</text>
        <view class="m-l-20">
          <view v-if="!goodsInfo.address">免运费 </view>
          <view v-else>快递：{{ goodsInfo.free_shipping_tips || "以提交订单时为准" }}
          </view>
          <view class="xs muted skuline" v-if="goodsInfo.address">配送至：{{
            goodsInfo.address.province +
            " " +
            goodsInfo.address.city +
            " " +
            goodsInfo.address.district
          }}
          </view>
          <view class="xs muted skuline" v-if="!goodsInfo.address">请完善地址信息
          </view>
        </view>
        <u-icon name="arrow-right" class="muted" style="margin-left: auto" />
      </view>
      <!-- S 规格 -->
      <view class="nr specification-item m-b-50" @click="onGoodsOptions">
        <text class="muted flex-none">选择</text>
        <text class="m-l-20 skuline">已选：{{ specValueStr }}</text>
        <u-icon name="arrow-right" class="muted" style="margin-left: auto" />
      </view>
      <view class="nr specification-item m-b-42" @click="showSafeguard = true"
        v-if="goodsInfo.service_guarantee.length">
        <text class="muted flex-none m-r-20">保障</text>
        <view class="skuline">
          <text v-for="(item, index) in goodsInfo.service_guarantee" :key="item.id">{{ item.name
          }}<text v-show="index != goodsInfo.service_guarantee.length - 1" class="m-l-10 m-r-10">·</text></text>
        </view>

        <u-icon name="arrow-right" class="muted" style="margin-left: auto" />
      </view>
    </view>
    <!-- S 保障popup -->
    <u-popup v-model="showSafeguard" mode="bottom" border-radius="14" :closeable="false" :safe-area-inset-bottom="true"
      mask-close-able>
      <view class="coupons">
        <view class="coupons-header">
          <text>保障</text>
          <u-icon name="close" color="#666666" @tap="showSafeguard = false" />
        </view>

        <view class="coupons-main" style="background-color: white">
          <scroll-view :scroll-y="true" style="height: 700rpx; touch-action: none">
            <view class="coupons-item" style="margin-bottom: 40rpx" v-for="(item, index) in goodsInfo.service_guarantee"
              :key="item.id">
              <view style="font-size: 28rpx; font-weight: 500">{{
                item.name
              }}</view>
              <view class="muted m-t-10">{{ item.content }}</view>
            </view>
          </scroll-view>
          <button class="popup-btn" @click="showSafeguard = false">
            我知道了
          </button>
        </view>
      </view>
    </u-popup>
    <!-- E 保障 -->
    <!-- 规格选择Popup -->
    <goods-spec v-model="showGoodsSpec" :isNoMarking="true" :defaultInfo="{
      image: goodsInfo.image,
      price: goodsInfo.sell_price,
      stock: goodsInfo.total_stock,
      unit: goodsInfo.unit_name,
      limit_type: goodsInfo.limit_type,
      limit_value: goodsInfo.limit_value,
      buy_num: goodsInfo.buy_num,
      cart_goods_num: goodsInfo.cart_goods_num,
    }" @closer="showGoodsSpec = false" :stock_show="goodsInfo.stock_show" :reCheck="reCheck" :cartId="cartId"
      :buttons="specButtonsList" :spec-list="goodsInfo.spec_value" :spec-map="goodsInfo.spec_value_list"
      @buy="handleBuy" @buyLuck="handleBuyLuck" @cart="handleCartAdd" @change="changeGoodsSpec" />
    <!-- E 规格 -->

    <!-- diy -->
    <view v-for="(item, index) in pagesData" :key="index">
      <template v-if="item.name == 'reviews'">
        <w-reviews v-if="item.show" :content="item.content" :styles="item.styles" :comment="goodsComment"
          :goods-id="goods_id" />
      </template>
      <template v-if="item.name == 'goodsrecom'">
        <w-goodsrecom v-if="item.show" :content="item.content" :styles="item.styles" />
      </template>
    </view>

    <!-- 商品介绍 -->
    <view class="detail">
      <view class="detail-title nr">详情</view>
      <u-parse :html="goodsInfo.content" :show-with-animation="true"></u-parse>
    </view>
    <!-- 商品下架提示语 -->
    <view class="goodDown" v-if="!goodsInfo.status">
      <view class="goodDown-text">商品已经下架啦~要不要瞧瞧别的~ </view>
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

        <view class="icon-item" @tap="goCart">
          <u-badge :count="cartNum" :offset="[-10, -10]"></u-badge>
          <u-icon name="shopping-cart" size="42" />
          <text style="line-height: 32rpx">购物车</text>
        </view>
      </view>
      <view class="button-group">
        <template v-if="luckId">
            <view class="button-item button-item--buy" @click="onBuyLuck"
              :style="{ opacity: goodsInfo.status == 0 ? 0.5 : 1 }">立即领取</view>
          </template>
        <template v-else>
          <template v-if="goodsInfo.presell">
          <view class="button-item button-item--buy" @click="onPresell"
            :style="{ opacity: goodsInfo.status == 0 ? 0.5 : 1 }">正在预售中，前往参与</view>
        </template>
          <template v-else>
            <view class="button-item button-item--cart" @click="onCart" v-if="goodsInfo.type == 1"
              :style="{ opacity: goodsInfo.status == 0 ? 0.5 : 1 }">加入购物车</view>
            <view class="button-item button-item--buy" @click="onBuy"
              :style="{ opacity: goodsInfo.status == 0 ? 0.5 : 1 }">立即购买</view>
          </template>
        </template>
      </view>
    </view>

    <share-popup v-model="showShare" :share-id="goods_id" pagePath="pages/goods_detail/goods_detail" :type="1"
      :poster_img="goodsInfo.poster" :config="{
        name: goodsInfo.name,
        image: goodsInfo.image,
      }" />
    <!-- 页面状态 -->
    <page-status :status="pageStatus">
      <template #error>
        <u-empty :text="pageErrorMsg" src="/static/images/empty/shop.png" :icon-size="280"></u-empty>
      </template>
    </page-status>
    <u-back-top :scroll-top="scrollTop" :top="1000" :customStyle="{
      backgroundColor: '#FFF',
      color: '#000',
      boxShadow: '0px 3px 6px rgba(0, 0, 0, 0.1)',
    }">
    </u-back-top>
  </view>
</template>

<script>
import { apiGoodsDetail } from "@/api/goods";
import { apiCartAdd, apiCouponGet, apiCartNum } from "@/api/store";
import {
  apiGoodsCollect,
  apiGoodsMarketing,
  apicheckCanBuy,
} from "@/api/goods";
import { baseURL, basePath } from "@/config/app";
import { apiGetPage } from "@/api/store";
import { PageStatusEnum, OrderTypeEnum } from "@/utils/enum";
import { mapGetters, mapState } from "vuex";
import { strToParams } from "@/utils/tools";
import { apiAddressLists } from "@/api/user";
import { apiserviceConfig } from "@/api/app";
import ServiceMixin from "@/mixins/service";

export default {
  name: "GoodsDetail",
  mixins: [ServiceMixin],

  data() {
    return {
      serciveConfig: {},
      addressId: "",
      addressList: [],
      isDischarge: false,
      // 页面状态
      pageStatus: PageStatusEnum["LOADING"],
      pagesData: [],
      pageErrorMsg: "", // 页面异常信息
      scrollTop: 0,
      percent: 0,
      showShare: false,
      goodsInfo: {
        free_shipping_tips: "",
        service_guarantee: [],
        member_level: {},
        address: {},
        stock_show: true,
      }, // 商品信息
      goodsComment: {}, // 用户评价
      goods_id: "", // 商品ID
      showSafeguard: false, //保障 ：显示｜隐藏
      isGoodsCollect: false, // 商品收藏

      showGoodsSpec: false, // 商品规格: 显示 | 隐藏
      showCoupons: false, // 领券：显示 | 隐藏

      goodsSpecOptions: {
        spec: {},
      }, // 已选商品规格
      specButtonsList: [], // 规格按钮
      goodsCouponList: [], // 优惠券列表

      goodsCouponActive: "", // 使用的优惠券
      // 规格按钮组
      specButtonsGroups: {
        buy: {
          event: "buy",
          text: "立即购买",
        },
        buyLuck: {
          event: "buyLuck",
          text: "立即领取",
        },
        cart: {
          event: "cart",
          text: "加入购物车",
        },
      },
      distribution: {},
      reCheck: 0, //标识购物车内重选
      cartId: 0,
      luckId: "", //积分抽奖领取ID
    };
  },

  computed: {
    ...mapGetters([
      "isLogin",
      "ServiceConfig",
      "userInfo",
      "appConfig",
      "shareConfig",
    ]),

    ...mapState({
      cartNum: (state) => state.cart.cartNum,
    }),
    // 根据端获取客服配置
    // getSerciveConfig() {

    //     console.log(this.serciveConfig[serviceEnum[getClient()]], 'service')
    //     return this.serciveConfig[serviceEnum[getClient()]]
    // },
    // 优惠券【视图】
    goodsCouponSpecs() {
      // 展示数量
      const COUNT = 3;
      const coupons = this.goodsCouponList;
      // 优惠券列表数量
      const Len = coupons.length;
      // 承载容器
      const payload = [];

      for (let i = 0; i < COUNT && i <= Len - 1; ++i) {
        const value = coupons[i];
        payload.push(value);
      }

      return payload;
    },

    // 已选商品规格
    specValueStr() {
      return this.goodsSpecOptions?.spec?.spec_value_str ?? "";
    },

    // 优惠券小数处理
    handleSpot() {
      return (val) => {
        let result = val;
        let decimalsArr = val.split(".");
        if (decimalsArr[1] == "00") {
          result = decimalsArr[0]; //5.00形式=>5
        } else if (decimalsArr[1] != "00" && decimalsArr[1] % 10 == 0) {
          result =
            Number(decimalsArr[0]) +'.'+
            Number(decimalsArr[1].substr(0, decimalsArr[1].length - 1)); //5.10形式=>5.1
        }
        return result;
      };
    },
  },

  methods: {
    gotoService() {
      this.$Router.push({
        path: "/bundle/pages/artificial_service/artificial_service",
      });
    },
    //预售
    onPresell() {
      this.$Router.push({
        path: "/bundle/pages/presell_detail/presell_detail",
        query: {
          id: this.goods_id,
        },
      });
    },
    // 获取地址列表
    apiAddressListsFun() {
      apiAddressLists().then((res) => {
        this.addressList = res;
        console.log(res);
      });
    },
    // 获取装修页面
    getPage() {
      apiGetPage({
        type: 5,
        goods_id: this.goods_id,
      }).then((data) => {
        const { common, content } = data;

        this.pagesData = content;
        uni.setNavigationBarTitle({
          title: common.title,
        });
      });
    },
    // 更改规格
    changeGoodsSpec(options) {
      this.goodsSpecOptions = options;
      console.log(options);
    },

    // 点击加入购物车按钮
    onCart() {
      //判断商品下架
      if (this.goodsInfo.status == 0) return;
      this.showGoodsSpec = true;
      this.specButtonsList = [this.specButtonsGroups.cart];
    },

    // 更改商品收藏状态
    changeCollect(value) {
      value = !!value ? 0 : 1;
      if (!this.isLogin) return this.$Router.push("/pages/login/login");

      apiGoodsCollect({
        goods_id: this.goods_id,
        is_collect: value,
      })
        .then((data) => {
          this.isGoodsCollect = value;
        })
        .catch((err) => {
          console.log(err);
        });
    },

    goHome() {
      this.$Router.replaceAll({
        path: "/pages/index/index",
      });
    },
    // 去购物车
    goCart() {
      this.$Router.replaceAll({
        path: "/pages/shop_cart/shop_cart",
      });
    },

    // 点击立即购买按钮
    onBuyLuck() {
      // 判断商品下架
      if (this.goodsInfo.status == 0) return;
      this.showGoodsSpec = true;
      this.specButtonsList = [this.specButtonsGroups.buyLuck];
    },

    // 点击立即购买按钮
    onBuy() {
      // 判断商品下架
      if (this.goodsInfo.status == 0) return;
      this.showGoodsSpec = true;
      this.specButtonsList = [this.specButtonsGroups.buy];
    },

    // 领取优惠券
    getCoupon(id) {
      if (!this.isLogin) return this.$Router.push("/pages/login/login");

      apiCouponGet({
        id,
      })
        .then(() => {
          this.$toast({
            title: "领取成功",
          });
          this.initMarketing();
        })
        .catch((err) => {
          console.log(err);
          // this.$toast({ title: '' })
        });
    },

    // 点击已选规格
    onGoodsOptions() {
      this.showGoodsSpec = true;
      this.specButtonsList = [this.specButtonsGroups.buy];

      if (this.goodsInfo.type == 1) {
        this.specButtonsList = [
          {
            ...this.specButtonsGroups.cart,
            style: {
              border: `solid 1px ${this.themeColor}`,
              color: this.themeColor,
              backgroundColor: "#FFFFFF",
            },
          },
          this.specButtonsGroups.buy,
        ];
      }
    },

    // 处理 加入购物车
    handleCartAdd({ spec, number }) {
      if (!this.isLogin) return this.$Router.push("/pages/login/login");

      apiCartAdd({
        item_id: spec.id,
        goods_num: number,
      })
        .then(() => {
          this.$toast({
            title: "已加入购物车",
          });
          this.$store.dispatch("getCartNum");
          // 商品详情数据
          this.initGoodsDetail();
        })
        .catch((err) => {
          throw new Error(err);
        });
    },
    handleBuyLuck({ spec, number }) {
      this.$Router.push({
        path: "/pages/goods_order/goods_order",
        query: {
          from: OrderTypeEnum["LUCK"],
          // address_id: this.addressId,
          payload: {
            source: "buy_now",
            draw_record_id: this.luckId,
            goods: [
              {
                goods_num: number,
                item_id: spec.id,
              },
            ],
          },
        },
      });
    },

    // 处理 立即购买
    handleBuy({ spec, number }) {
      const form = this.goodsInfo.type == 1 ? "GOODS" : "VIRTUAL";
      this.$Router.push({
        path: "/pages/goods_order/goods_order",
        query: {
          from: OrderTypeEnum[form],
          address_id: this.addressId,
          payload: {
            source: "buy_now",
            goods: [
              {
                goods_num: number,
                item_id: spec.id,
              },
            ],
          },
        },
      });
    },

    // 初始化商品详情数据
    initGoodsDetail() {
      return new Promise((resolve, reject) => {
        apiGoodsDetail({
          id: this.goods_id,
          visit: 1,
          address_id: this.addressId,
        })
          .then((data) => {
            this.goodsInfo = data;
            const hasComment = JSON.stringify(data.goods_comment) !== "[]";
            this.goodsComment = hasComment ? data.goods_comment : {};
            this.isGoodsCollect = !!data.is_collect;
            this.distribution = this.goodsInfo.distribution;
            return data;
          })
          .then((data) => {
            // #ifdef H5
            // 设置分享
            this.$store.dispatch("setWxShare", {
              shareImage: data?.image,
              shareDesc: data?.name,
            });
            // #endif
            resolve(data);
          })
          .catch((err) => {
            reject(err);
          });
      });
    },
    async getServiceConfig() {
      const data = await apiserviceConfig();
      this.serciveConfig = data;
    },
    // 初始化优惠券
    initMarketing() {
      return new Promise((resolve, reject) => {
        apiGoodsMarketing({
          id: this.goods_id,
        })
          .then((data) => {
            this.goodsCouponList = data.coupon;
            resolve(data);
          })
          .catch((err) => {
            reject(err);
          });
      });
    },
    handleSend() {
      uni.$once("changeAddress", async (id) => {
        this.addressId = id;
        await this.initGoodsDetail();
      });
      this.$Router.push({
        path: "/pages/address/address",
        query: {
          type: 1,
        },
      });
    },
  },
  onPageScroll(e) {
    const top = uni.upx2px(100);
    const { scrollTop } = e;
    this.percent = scrollTop / top > 1 ? 1 : scrollTop / top;
    this.scrollTop = scrollTop;
  },
  async onLoad() {
    const options = this.$Route.query;
    //重选规格
    if (options["reCheck"] == 1) {
      //标识购物车购物车内多规格库存不足重选
      (this.reCheck = options["reCheck"]), (this.cartId = options["cart_id"]);
      setTimeout(() => {
        this.showGoodsSpec = true;
      }, 200);
    }
    if (options["luckId"]) {
      //积分抽奖领取ID
      this.luckId = options["luckId"];
    }

    // 商品ID赋值：点击 | 扫码
    options["scene"]
      ? (this.goods_id = strToParams(options["scene"])["id"])
      : (this.goods_id = options.id);

    try {
      if (!this.goods_id) throw new Error("该商品不存在");
      await this.getPage();
      // 商品详情数据
      await this.initGoodsDetail();
      await this.apiAddressListsFun();
      // 促销
      await this.initMarketing();
      this.getServiceConfig();
      this.pageStatus = PageStatusEnum["NORMAL"];
    } catch (err) {
      console.log(err);
      this.pageErrorMsg = err.message;
      this.pageStatus = PageStatusEnum["ERROR"];
    }
    // #ifdef H5
    setTimeout(() => {
      const shareconfig = this.shareConfig.find((i) => {
        return i.type == 2 && i.page == 2;
      });
      const titile = shareconfig.title
        .replace(/\{goodname\}/g, this.goodsInfo.name)
        .replace(/\{price\}/g, this.goodsInfo.sell_price);
      const sharedesc = shareconfig.synopsis
        .replace(/\{goodname\}/g, this.goodsInfo.name)
        .replace(/\{price\}/g, this.goodsInfo.sell_price);
      // 设置分享
      this.$store.dispatch("setWxShare", {
        shareTitle: titile,
        shareImage: shareconfig.image,
        shareDesc: sharedesc,
      });
    }, 1000);

    // #endif
  },
  //分享好友
  onShareAppMessage() {
    const { name, image } = this.goodsInfo;
    return {
      title: name,
      path: `/pages/goods_detail/goods_detail?id=${this.goods_id}&invite_code=${this.userInfo.code}`,
      imageUrl: image,
    };
  },

  //分享朋友圈
  onShareTimeline() {
    const { name, image } = this.goodsInfo;
    return {
      title: name,
      query: `id=${this.goods_id}&invite_code=${this.userInfo.code}`, // 分享参数，注意这里使用query而不是path
      imageUrl: image,
    };
  },
  onHide() {
    this.isDischarge = true;
  },
  async onShow() {
    // await this.getPage();
    // 商品详情数据
    // await this.initGoodsDetail();
    // await this.apiAddressListsFun();
    // await this.initMarketing();

    this.isDischarge = false;
  },
};
</script>

<style lang="scss" scoped>
.button {
  padding: 0 !important;
}

.goods-detail {
  padding-bottom: calc(100rpx + 100rpx + constant(safe-area-inset-bottom));
  padding-bottom: calc(100rpx + 100rpx + env(safe-area-inset-bottom));

  .goods-stock {
	margin: 1rpx 20rpx 30rpx 20rpx;
    background-color: #ffffff;
    padding: 20rpx 24rpx;
    border-radius: 0rpx 0rpx 30rpx 30rpx;
  }
}

.goods-info {
  margin: 0 20rpx 0 20rpx;
  padding: 20rpx 24rpx;
  background-color: #ffffff;
  border-radius: 30rpx 30rpx 0 0;

  // 会员价
  .vip-price {
    margin-left: 20rpx;
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
        content: "";
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
    padding: 20rpx 0 10rpx 0;

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

.discounts {
  display: flex;
  padding: 20rpx 0 10rpx 0;
  // margin-top: 20rpx;
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
      content: "";
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
  padding: 50rpx 24rpx 1rpx 24rpx;
  border-radius: 30rpx;
  margin: 0rpx 20rpx 30rpx 20rpx;

  background-color: #ffffff;

  .specification-item {
    display: flex;
  }
}

.detail {
  padding: 24rpx;
  margin-top: 20rpx;
  background-color: #ffffff;
  margin-left: 20rpx;
  margin-right: 20rpx;
  border-radius: 30rpx;

  &-title {
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
    width: 268rpx;
    font-size: $-font-size-xxs;
    color: $-color-lighter;

    .icon-item {
      position: relative;
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

  .popup-btn {
    @include background_color();
    color: white;
    height: 80rpx;
    line-height: 80rpx;
    border-radius: 50rpx;
    font-size: 32rpx;
    margin-top: 25rpx;
  }
}

.share-money {
  border-radius: 8rpx;
  padding: 8rpx 16rpx;
  color: #7b3e0e;
  background: linear-gradient(90deg, #fdeed8 0%, #fccf9c 100%);
}

.goodDown {
  position: fixed;
  bottom: calc(100rpx + env(safe-area-inset-bottom));
  bottom: calc(100rpx + constant(safe-area-inset-bottom));
  height: 88rpx;
  left: 0;
  right: 0;
  background-color: rgba(119, 119, 119, 1);

  &-text {
    line-height: 88rpx;
    text-align: center;
    color: white;
  }
}

.commission {
  margin: 0 20rpx 30rpx 20rpx;
  padding: 40rpx 24rpx;
  background-color: #ffffff;
  border-radius: 30rpx;
}

.info-header {
  background-size: contain;
  height: 240rpx;
  padding: 30rpx 24rpx;

  .vip-name {
    font-size: 24rpx;
    background-color: #e0a356;
    width: 150rpx;
    border-radius: 25rpx;
    text-align: center;
    line-height: 30rpx;
    padding: 5rpx 0;
    margin-bottom: 10rpx;
    position: relative;

    &::before {
      position: absolute;
      content: "";
      top: -7rpx;
      right: 10rpx;
      width: 0;
      height: 0;
      border-top: 50px solid #e0a356;
      border-right: 50px solid transparent;
      border-left: 50px solid transparent;
      transform: scale(0.15);
    }
  }
}
</style>
