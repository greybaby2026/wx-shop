<template>
  <view class="goods-order" :class="themeName">
    <!-- 配送方式 -->
    <view class="contain delivery" v-if="orderInfo.is_address">
      <view class="delivery-tabs">
        <u-tabs
          :list="deliveryTypeTabsList"
          :is-scroll="false"
          :height="88"
          :active-color="themeColor"
          :bar-style="{ top: '100%' }"
          :bar-width="100"
          :current="deliveryTypeTabsIndex"
          @change="changeDeliveryType"
          v-show="deliveryTypeTabsList.length == 2"
        />
      </view>

      <!-- 快递配送 -->
      <view
        class="delivery-contain"
        v-show="deliveryActive['sign'] === 'express'"
        @tap="onAddressSelect"
      >
        <address-card
          :action="true"
          :has-content="JSON.stringify(addressInfo) !== '[]'"
        >
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
      </view>
      <!-- v-if="from != 4" -->
      <!-- 门店自提 -->
      <view
        class="delivery-contain"
        v-show="deliveryActive['sign'] === 'store'"
        @tap="onStoreSelect"
      >
        <address-card
          :action="true"
          :has-content="!!orderFrom.selffetch_shop_id"
          placeholder="请选择门店地址"
        >
          <template slot="header">
            <text>{{ selffetchStoreInfo.name }}</text>
          </template>
          <template slot="main">
            <text>{{ selffetchStoreInfo.detailed_address }}</text>
          </template>
          <template slot="footer">
            <text>
              <text>营业时间：</text>
              <text>{{ selffetchStoreInfo.business_start_time }}</text>
              <text>-</text>
              <text>{{ selffetchStoreInfo.business_end_time }}</text>
            </text>
          </template>
        </address-card>
      </view>
    </view>

    <!-- 门店自提表单 -->
    <view
      class="contain store-from"
      v-show="deliveryActive['sign'] === 'store'"
      v-if="orderInfo.is_address"
    >
      <view class="store-from-item">
        <text>提货人</text>
        <u-input
          v-model="orderFrom.contact"
          class="flex-1"
          type="text"
          input-align="right"
          placeholder="请输入提货人"
          :clearable="false"
        />
      </view>
      <view class="store-from-item">
        <text>联系方式</text>
        <u-input
          :maxlength="11"
          v-model="orderFrom.mobile"
          class="flex-1"
          type="text"
          input-align="right"
          placeholder="请输入联系方式"
          :clearable="false"
        />
      </view>
    </view>

    <!-- 订单商品 -->
    <view class="contain">
      <!-- 商品列表 -->
      <view class="goods">
        <view v-for="item in orderGoodsList" :key="item.id" class="goods-item">
          <goods-card
            shape="rectangle"
            :name="item.goods_name"
            :image="item.item_image ? item.item_image : item.image"
            :contain-style="{ 'border-raius': 0, height: '220rpx' }"
            :image-style="{ width: '160rpx', height: '160rpx' }"
            :disableMsg="item.msg"
          >
            <view class="flex-1" slot="default">
              <text
                class="muted xs m-t-10 red"
                v-show="item.msg == '找不到商品'"
                >该商品不存在</text
              >
              <text class="muted xs m-t-10 skuline">{{
                item.spec_value_str
              }}</text>
              <view class="m-t-10 flex row-between">
                <view class="flex" v-show="item.original_price">
                  <!-- 售价 -->
                  <price
                    :content="item.original_price"
                    mainSize="32rpx"
                    minorSize="24rpx"
                  />

                  <!-- 普通商品或者虚拟商品会员价 用户有登陆且且该商品有设置会员价才显示 -->
                  <view
                    class="vip-price flex m-l-10"
                    v-if="
                      !!item.member_price &&
                      (orderInfo.order_type == 0 || orderInfo.order_type == 4)
                    "
                  >
                    <view class="price-name xxs">会员价</view>
                    <view style="padding: 0 10rpx">
                      <price
                        :content="item.sell_price"
                        :weight="500"
                        color="#7B3200"
                      >
                      </price>
                    </view>
                  </view>
                  <!-- 其他活动价 -->
                  <view
                    class="vip-price flex m-l-10"
                    v-if="
                      orderInfo.order_type == 1 ||
                      orderInfo.order_type == 2 ||
                      orderInfo.order_type == 3 ||
                      orderInfo.order_type == 5
                    "
                  >
                    <view
                      class="price-name xxs"
                      v-if="orderInfo.order_type == 1"
                      >拼团价</view
                    >
                    <view
                      class="price-name xxs"
                      v-if="orderInfo.order_type == 2"
                      >秒杀价</view
                    >
                    <view
                      class="price-name xxs"
                      v-if="orderInfo.order_type == 3"
                      >砍价</view
                    >
                    <view
                      class="price-name xxs"
                      v-if="orderInfo.order_type == 5"
                      >预售</view
                    >
                    <view style="padding: 0 10rpx">
                      <price
                        :content="item.sell_price"
                        :weight="500"
                        color="#7B3200"
                      >
                      </price>
                    </view>
                  </view>
                </view>

                <text class="lighter sm" v-show="!!item.goods_num"
                  >x{{ item.goods_num }}</text
                >
              </view>
            </view>
          </goods-card>
          <!-- 商品小附件 -->
          <view class="goods-item-widget" v-if="from != 4">
            <view
              v-if="deliveryActive['sign'] === 'express' && !item.is_express"
              class="delivery-tag"
            >
              该商品不支持快递配送</view
            >
            <view
              v-if="deliveryActive['sign'] === 'store' && !item.is_selffetch"
              class="delivery-tag"
            >
              该商品不支持门店自提</view
            >
          </view>
        </view>
      </view>

      <!-- 买家留言 -->
      <view class="cell remark">
        <view class="cell-label">买家留言</view>
        <view class="cell-content">
          <u-input
            v-model="orderFrom.user_remark"
            class="flex-1"
            type="text"
            :clearable="false"
            :maxlength="150"
            placeholder="请添加备注（150字以内）"
          />
        </view>
      </view>
    </view>

    <!-- 优惠 -->
    <view class="contain discounts" v-if="!isMarketing">
      <!-- 优惠券 -->
      <view class="cell discounts-item" @tap="showCoupons = true">
        <view class="cell-lable">优惠券</view>
        <view class="cell-content m-r-10" :style="{ color: themeColor }">{{
          couponsActive.name || ""
        }}</view>
        <u-icon name="arrow-right" />
      </view>
      <!-- 积分抵扣 -->
      <!-- 			<view class="cell discounts-item">
				<view class="cell-label">积分抵扣</view>
				<view class="cell-content"></view>
				<u-checkbox 
				  v-model="" 
				  class="u-checkbox"
				  shape="circle" 
				  :label-disabled="false" 
				  :active-color="themeColor"
				/>
			</view> -->
    </view>

    <!-- 订单清算 -->
    <view class="contain clearing">
      <view class="cell clearing-item">
        <view class="cell-label">商品金额</view>
        <view class="cell-content">
          <price
            v-if="orderInfo.order_type == 0 || orderInfo.order_type == 4"
            :content="orderInfo.total_goods_original_price"
          />
          <price v-else :content="orderInfo.total_goods_price" />
        </view>
      </view>
      <view class="cell clearing-item">
        <view class="cell-label">运费</view>
        <view class="cell-content">
          <price :content="orderInfo.express_price" />
        </view>
      </view>

      <view
        class="cell clearing-item"
        v-if="!isMarketing && orderInfo.discount_amount > 0"
      >
        <view class="cell-label">优惠券</view>
        <view class="cell-content">
          <price
            :content="orderInfo.discount_amount"
            prefix="-￥"
            :color="themeColor"
          />
        </view>
      </view>
      <view class="cell clearing-item" v-if="orderInfo.member_amount > 0">
        <view class="cell-label">会员折扣</view>
        <view class="cell-content">
          <price
            :content="orderInfo.member_amount"
            :color="themeColor"
            prefix="-￥"
          />
        </view>
      </view>
    </view>
    <!-- 不可购买商品 -->
    <view class="goods-disable" v-if="disableGoods.length">
      <view class="flex">
        <view v-for="(item, index) in disableGoods" :key="item.id">
          <image
            :src="item.goods.image"
            mode="scaleToFill"
            class="m-r-10 image"
            v-if="index <= 2"
          />
        </view>
        <view> {{ disableGoods.length }}件商品无法购买 </view>
      </view>

      <view class="text" @click="showDisabled = true">查看</view>
    </view>
    <!-- 订单操作 -->
    <view class="operational">
      <view class="order-amount">
        <text>合计:</text>
        <price
          :content="orderInfo.order_amount"
          mainSize="38rpx"
          minorSize="30rpx"
          :color="themeColor"
        />
      </view>
      <view
        class="operational-button"
        @tap="onSubmitOrder"
        :style="{ opacity: goodsLength == 0 ? 0.5 : 1 }"
        >提交订单
      </view>
    </view>

    <!-- 优惠券【弹窗】 -->
    <u-popup
      v-model="showCoupons"
      mode="bottom"
      border-radius="14"
      :closeable="false"
      :safe-area-inset-bottom="true"
      @open="openCouponsPopup"
      @close="closeCouponsPopup"
    >
      <view class="coupons">
        <view class="coupons-header">
          <text>优惠券</text>
          <u-icon name="close" color="#666666" @tap="showCoupons = false" />
        </view>

        <view class="coupons-main">
          <view class="coupons-main__tabs">
            <u-tabs
              v-if="showCoupons"
              :list="couponTabsList"
              :is-scroll="false"
              :current="couponTabsIndex"
              :height="80"
              :active-color="themeColor"
              :bar-style="{ top: '100%' }"
              :bar-width="100"
              :show-bar="true"
              @change="(index) => (couponTabsIndex = index)"
            />
          </view>

          <view class="coupons-main__contain">
            <!-- 可用优惠券 -->
            <scroll-view
              :scroll-y="true"
              style="height: 700rpx"
              v-show="couponsTabActive['sign'] === 'usable'"
            >
              <view
                class="coupons-item"
                v-for="item in couponsInfo.can_use"
                :key="item.id"
              >
                <coupon-card
                  mode="radio"
                  :discount_max_money="item.discount_max_money"
                  :discount_ratio="item.discount_ratio"
                  :condition_type="item.condition_type"
                  :name="item.name"
                  :money="item.money"
                  :condition="item.condition_tips"
                  :time="item.use_time_text2"
                  :scene="item.goods_tips"
                  :coupon-id="item.id"
                  :checked.sync="item.checked"
                  :desc="{
                    title: '',
                    content: '',
                  }"
                  @change="changeCouponUse(item, $event)"
                />
              </view>
              <u-empty
                v-if="!couponsInfo.can_use.length"
                text="暂无优惠券"
                src="/static/images/empty/coupon_icon.png"
                :icon-size="180"
              />
            </scroll-view>
            <!-- 不可用优惠券 -->
            <scroll-view
              :scroll-y="true"
              style="height: 700rpx"
              v-show="couponsTabActive['sign'] === 'disabled'"
            >
              <view
                class="coupons-item"
                v-for="item in couponsInfo.not_can_use"
                :key="item.id"
              >
                <coupon-card
                  mode="normal"
                  :discount_max_money="item.discount_max_money"
                  :discount_ratio="item.discount_ratio"
                  :condition_type="item.condition_type"
                  :name="item.name"
                  :money="item.money"
                  :condition="item.condition_tips"
                  :time="item.use_time_text2"
                  :scene="item.goods_tips"
                  :coupon-id="item.id"
                  :desc="{
                    title: item.fail_use_tips,
                    content: item.fail_use_detail,
                  }"
                />
              </view>
              <u-empty
                v-if="!couponsInfo.not_can_use.length"
                text="暂无优惠券"
                src="/static/images/empty/coupon_icon.png"
                :icon-size="180"
              />
            </scroll-view>
          </view>
        </view>

        <view class="coupons-footer">
          <view class="coupons-footer-button" @tap="useCoupon">确认</view>
        </view>
      </view>
    </u-popup>
    <!-- 不可下单商品列表弹框 -->
    <u-popup
      v-model="showDisabled"
      mode="bottom"
      border-radius="14"
      :closeable="false"
      :safe-area-inset-bottom="true"
      @close="showDisabled = false"
    >
      <view class="coupons-header">
        <text>以下商品不可购买</text>
        <u-icon name="close" color="#666666" @tap="showDisabled = false" />
      </view>
      <scroll-view :scroll-y="true" style="height: 700rpx">
        <view v-for="item in disableGoods" :key="item.id" class="goods-item">
          <goods-card
            shape="rectangle"
            :name="item.goods.goods_name"
            :image="item.goods.image"
            :contain-style="{ 'border-raius': 0, height: '220rpx' }"
            :image-style="{ width: '160rpx', height: '160rpx' }"
            :disableMsg="item.msg"
          >
            <view class="flex-1" slot="default">
              <text class="muted xs m-t-10 red">{{ item.msg }}</text>
              <view class="m-t-10 flex row-between">
                <view class="flex" v-show="item.goods.sell_price">
                  <!-- 售价 -->
                  <price
                    :content="item.goods.sell_price"
                    mainSize="32rpx"
                    minorSize="24rpx"
                  />

                  <!-- 普通商品或者虚拟商品会员价 用户有登陆且且该商品有设置会员价才显示 -->
                  <view
                    class="vip-price flex m-l-10"
                    v-if="
                      !!item.goods.member_price &&
                      (orderInfo.order_type == 0 || orderInfo.order_type == 4)
                    "
                  >
                    <view class="price-name xxs">会员价</view>
                    <view style="padding: 0 10rpx">
                      <price
                        :content="
                          item.goods.member_price || item.goods.sell_price
                        "
                        :weight="500"
                        color="#7B3200"
                      >
                      </price>
                    </view>
                  </view>
                  <!-- 其他活动价 -->
                  <view
                    class="vip-price flex m-l-10"
                    v-if="
                      orderInfo.order_type == 1 ||
                      orderInfo.order_type == 2 ||
                      orderInfo.order_type == 3
                    "
                  >
                    <view
                      class="price-name xxs"
                      v-if="orderInfo.order_type == 1"
                      >拼团价</view
                    >
                    <view
                      class="price-name xxs"
                      v-if="orderInfo.order_type == 2"
                      >秒杀价</view
                    >
                    <view
                      class="price-name xxs"
                      v-if="orderInfo.order_type == 3"
                      >砍价</view
                    >
                    <view style="padding: 0 10rpx">
                      <price
                        :content="item.goods.sell_price"
                        :weight="500"
                        color="#7B3200"
                      >
                      </price>
                    </view>
                  </view>
                </view>

                <text class="lighter sm" v-show="!!item.goods.goods_num"
                  >x{{ item.goods.goods_num }}</text
                >
              </view>
            </view>
          </goods-card>
        </view>
      </scroll-view>
      <view class="coupons-footer">
        <view class="coupons-footer-button" @tap="showDisabled = false"
          >我知道了</view
        >
      </view>
    </u-popup>

    <!-- 页面状态 -->
    <page-status :status="pageStatus">
      <template #error>
        <u-empty
          :text="pageErrorMsg"
          src="/static/images/empty/order.png"
          :icon-size="280"
        />
      </template>
    </page-status>
    <!-- 不能购买modal弹框 -->
    <u-modal
      :value="showModal"
      :show-confirm-button="false"
      :show-title="false"
      :mask-close-able="false"
    >
      <view class="modal">
        <view class="modal-text-title">很抱歉，购买失败</view>
        <view class="modal-text-content m-t-40 m-b-40">商品不能购买</view>
        <button
          class="br60 modal-btn"
          size="md"
          hover-class="none"
          @tap="handledisBuy"
        >
          我知道了
        </button>
      </view>
    </u-modal>
  </view>
</template>

<script>
/**
 * @description 订单结算页
 * @query { Number | OrderTypeEnum } from 订单类型: 0-商品；1-拼团；2-秒杀；3-砍价；4- 虚拟商品。
 * @query { Object } payload 请求数据参数
 * @example { from: OrderTypeEnum['GOODS'], payload: {source: 'buy_now', goods: {goods_num: 1, item_id: 24} }}
 */

import { apiOrderPlace, apiDeliveryType } from "@/api/order";
import { apiSeckillPlace } from "@/api/activity";
import { apiGoodsTeamPlace } from "@/api/goods";
import { apiBargainSettle } from "@/api/bargain";
import { apiCouponGoods } from "@/api/store";
import { apiSubscribe } from "@/api/app";
import { handleClientEvent } from "@/utils/tools";
import {
  PageStatusEnum,
  OrderTypeEnum,
  OrderSourceEnum,
  PaymentStatusEnum,
  PayWayEnum,
} from "@/utils/enum";
import { nextTick } from "process";
import { apicheckCanBuy } from "@/api/goods";

export default {
  name: "GoodsOrder",

  data() {
    return {
      from: "", // 来源页面: 0-商品；1-拼团；2-秒杀；3-砍价。
      // 页面状态
      pageStatus: PageStatusEnum["LOADING"],
      pageErrorMsg: "", // 页面异常信息

      // 配送方式Tabs列表
      deliveryTypeTabsList: [
        {
          id: 1,
          name: "快递发货",
          sign: "express",
        },
        {
          id: 2,
          name: "上门自提",
          sign: "store",
        },
      ],
      deliveryTypeTabsIndex: 0, // 配送方式Tabs索引

      pageQueryPayload: {}, // 承载页面参数
      // 订单表单
      orderFrom: {
        delivery_type: "", // 配送方式: 1-快递发货; 2-门店自提;
        user_remark: "", // 用户备注
        coupon_list_id: "", // 优惠券ID
        address_id: "", // 地址ID 【快递发货】
        selffetch_shop_id: "", // 自提门店ID 【门店自提】
        contact: "", // 提货人 【门店自提】
        mobile: "", // 联系电话 【门店自提】
      },

      /** S 优惠券 **/
      showCoupons: false, // 优惠券: 显示|隐藏【弹窗】
      // 优惠券Tabs列表
      couponTabsList: [
        {
          sign: "usable",
          name: "可使用优惠券",
        },
        {
          sign: "disabled",
          name: "不可使用优惠券",
        },
      ],
      couponTabsIndex: 0, // 优惠券Tabs索引
      // 优惠券信息
      couponsInfo: {
        can_use: [],
        not_can_use: [],
      },
      couponsActive: {}, // 当前使用优惠券
      /** E 优惠券 **/

      addressInfo: {}, // 地址信息
      selffetchStoreInfo: {}, // 自提门店信息
      orderInfo: {}, // 订单信息
      orderGoodsList: [], // 订单商品
      disableGoods: [], //不可下单商品
      showDisabled: false, //不可下单商品
      goodsLength: 0,
      showModal: false, //不可下单弹框
    };
  },

  computed: {
    // 过滤订单表单冗余参数
    orderFormParams() {
      const from = {
        ...this.orderFrom,
      };
      // 根据物流方式过滤
      switch (from.delivery_type) {
        case 1:
          delete from.selffetch_shop_id;
          delete from.contact;
          delete from.mobile;
          break;
        case 2:
          delete from.address_id;
          break;
      }
      return from;
    },

    // 根据订单类型扩展订单参数
    orderExtendParams() {
      const params = new Object();

      switch (this.from) {
        // 普通商品
        case OrderTypeEnum["GOODS"]:
          break;
        // 拼团商品
        case OrderTypeEnum["TEAM"]:
          params.source = OrderSourceEnum["BUY_NOW"];
          break;
        // 秒杀商品
        case OrderTypeEnum["SECKILL"]:
          params.source = OrderSourceEnum["BUY_NOW"];
          break;
        // 砍价商品
        case OrderTypeEnum["BARGAIN"]:
          params.source = OrderSourceEnum["BUY_NOW"];
          break;
        // 预售商品
        case OrderTypeEnum["PRESELL"]:
          params.source = OrderSourceEnum["BUY_NOW"];
          break;
      }

      return {
        ...params,
        order_type: this.from,
      };
    },

    // 是否营销商品
    isMarketing() {
      switch (this.from) {
        // 普通商品
        case OrderTypeEnum["VIRTUAL"]:
        case OrderTypeEnum["GOODS"]:
          return false;
        default:
          return true;
      }
    },

    // 当前配送方式Tab
    deliveryActive() {
      return this.deliveryTypeTabsList[this.deliveryTypeTabsIndex];
    },

    // 当前优惠券Tab
    couponsTabActive() {
      return this.couponTabsList[this.couponTabsIndex];
    },
  },
  watch: {
    //合并不可购买商品和可购商品
    disableGoods(val) {
      val.forEach((i) => {
        this.orderGoodsList.push({
          ...i.goods,
          msg: i.msg,
        });
      });
    },
    immediate: true,
  },
  methods: {
    // 初始化配送方式
    initDeliveryType() {
      return new Promise((resolve, reject) => {
        apiDeliveryType()
          .then((data) => {
            const { express, selffetch } = data;
            // 订单配送方式ID组
            const deliveries = [];
            // 计入配送方式列表ID组
            express.is_express && deliveries.push(1);
            selffetch.is_selffetch && deliveries.push(2);
            // 过滤配送方式
            this.deliveryTypeTabsList = this.deliveryTypeTabsList.filter(
              (item) => deliveries.includes(item["id"])
            );
            this.deliveryTypeTabsList.forEach((item) => {
              switch (item.id) {
                case 1:
                  item.name = express.express_name;
                  break;
                case 2:
                  item.name = selffetch.selffetch_name;
                  break;
              }
            });
            // 初始化订单表单的配送方式
            this.orderFrom.delivery_type = this.deliveryActive["id"];
            resolve(data);
          })
          .catch((errMsg) => reject(errMsg ?? "获取不到配送方式"));
      });
    },

    // 根据商品类型初始化订单数据
    initOrderData() {
      return new Promise((resolve, reject) => {
        apiOrderPlace({
          ...this.orderFormParams,
          ...this.pageQueryPayload,
          ...this.orderExtendParams,
          action: "settle",
        })
          .then((data) => {
            this.addressInfo = data.address;
            this.orderFrom.address_id = data.address?.id;
            this.goodsLength = data.goods.length;
            this.orderGoodsList = data.goods;
            this.disableGoods = data.goods_disabled;
            this.orderInfo = data;
            this.orderFrom.contact = data.selffetch_info.contact;
            this.orderFrom.mobile = data.selffetch_info.mobile;
            resolve(data);
          })
          .catch((errMsg) => reject(errMsg));
      });
    },

    /** S 优惠券 **/
    // 初始化优惠券列表
    initCouponGoods() {
      return new Promise((resolve, reject) => {
        let params = null;
        const { goods, cart_id, source } = this.pageQueryPayload;

        // buy_now | cart 处理：请求优惠券列表的参数处理
        switch (source) {
          case OrderSourceEnum["BUY_NOW"]:
            const goodsItems = goods.map((item) => ({
              num: item.goods_num,
              item_id: item.item_id,
            }));
            params = {
              goods: goodsItems,
              source: 1,
            };
            break;
          case OrderSourceEnum["CART"]:
            params = {
              cart_ids: [...cart_id],
              source: 2,
            };
            break;
        }

        apiCouponGoods({
          ...params,
        })
          .then((data) => {
            data.can_use.forEach((item) => (item.checked = false));
            // 有可用优惠券默认选择第一个
            if (data.can_use[0]) {
              data.can_use[0].checked = true;
              this.couponsActive = data.can_use[0];
            }
            return data;
          })
          .then((data) => {
            this.couponsInfo = data;
            // Tab名称处理（优惠券数量）
            this.couponTabsList.forEach((item) => {
              switch (item.sign) {
                case "usable":
                  item.name = item.name + `(${data.can_use_count})`;
                  break;
                case "disabled":
                  item.name = item.name + `(${data.not_can_use_count})`;
                  break;
              }
            });
            resolve(data);
          })
          .catch((errMsg) => reject(errMsg));
      });
    },

    // 更改优惠券
    changeCouponUse(coupon, value) {
      this.couponsInfo.can_use.forEach((item) => (item.checked = false));
      coupon && (coupon.checked = true);
    },

    // 使用优惠券
    useCoupon() {
      this.showCoupons = false;
      const coupon = this.couponsInfo.can_use.find((item) => item.checked);
      this.couponsActive = coupon || {};
      this.$set(this.orderFrom, "coupon_list_id", coupon?.id);
      this.initOrderData();
    },

    // 打开优惠券弹窗
    openCouponsPopup() {
      if (Object.keys(this.couponsActive).length) {
        const coupon = this.couponsInfo.can_use.find(
          (item) => item.id === this.couponsActive.id
        );
        coupon.checked = true;
      }
    },

    // 关闭优惠券弹窗
    closeCouponsPopup() {
      if (!Object.keys(this.couponsActive).length) {
        this.couponsInfo.can_use.forEach((item) => (item.checked = false));
      }
    },
    /** E 优惠券 **/

    // 更改配送方式
    async changeDeliveryType(index) {
      this.deliveryTypeTabsIndex = index;
      this.orderFrom.delivery_type = this.deliveryActive["id"];
      try {
        const { selffetch_info } = await this.initOrderData();
        if (selffetch_info.selffetch_shop_id) {
          this.orderFrom.selffetch_shop_id = selffetch_info.selffetch_shop_id;
          this.selffetchStoreInfo = selffetch_info.selffetch_shop;
        }
      } catch (error) {
        console.log(error);
      }
    },

    // 点击地址选择
    onAddressSelect() {
      // 监听全局selectaddress事件
      uni.$once("changeAddress", (id = this.orderFrom.address_id) => {
        id && (this.orderFrom.address_id = id);
        this.initOrderData();
      });

      this.$Router.push({
        path: "/pages/address/address",
        query: {
          type: true,
        },
      });
    },

    // 点击店铺选择
    onStoreSelect() {
      // 监听全局selectaddress事件
      uni.$once("changeStore", (store) => {
        this.selffetchStoreInfo = store;
        this.orderFrom.selffetch_shop_id = store.id;
        this.initOrderData();
      });

      this.$Router.push({
        path: "/bundle/pages/store_list/store_list",
      });
    },
    // 校验表单数据
    checkOrder() {
      const from = {
        ...this.orderFrom,
      };

      // 根据物流方式过滤
      switch (from.delivery_type) {
        case 1:
          if (!from.address_id) {
            throw new Error("请选择送货地址");
          }
          break;
        case 2:
          if (
            !(
              from.selffetch_shop_id &&
              from.contact &&
              from.mobile.length == 11
            )
          ) {
            throw new Error("请完善自提点信息");
          }
          break;
      }
    },
    // 点击提交订单
    onSubmitOrder() {
      // 可购买商品为0时直接返回
      if (this.goodsLength == 0) {
        return;
      }
      if (
        this.orderFrom.delivery_type == 4 &&
        !this.orderFrom.address_id &&
        this.deliveryActive["sign"] == "express" &&
        this.orderInfo.is_address != 0
      ) {
        return this.$toast({
          title: "请选择收货地址",
        });
      }
      try {
        this.checkOrder();
      } catch (error) {
        return this.$toast({
          title: error.message,
        });
      }
      uni.showModal({
        title: "温馨提示",
        content: "是否确认下单?",
        confirmColor: this.themeColor,
        success: async ({ confirm }) => {
          try {
            if (!confirm) throw "订单取消";

            // 微信订阅消息
            try {
              // #ifdef MP-WEIXIN
              await this.handleSubscribeMP_WEIXIN();
              // #endif
            } catch (errMsg) {
              console.log("SUBSCRIBE_ERROR_MSG:", errMsg);
            }
            // 下单处理
            const res = await this.handlePlaceOrder();
            console.log(res);
          } catch (errMsg) {
            console.log("ORDER_ERROR_MSG:", errMsg);
            if (errMsg == "提交的商品已不能购买，请重新选择商品") {
              this.showModal = true;
            } else if (errMsg == "自提门店不能为空") {
              this.$toast({
                title: "请选择自提门店",
              });
            }
          }
        },
      });
    },

    // 处理：微信订阅消息
    handleSubscribeMP_WEIXIN() {
      return new Promise((resolve, reject) => {
        apiSubscribe()
          .then((data) => {
            if (!data.length) reject("暂无可订阅信息");
            uni.requestSubscribeMessage({
              tmplIds: data,
              success(res) {
                resolve(res);
              },
              fail(err) {
                reject("订阅失败");
              },
            });
          })
          .catch(() => reject("订阅获取失败"));
      });
    },

    // 下单处理
    handlePlaceOrder() {
      return new Promise((resolve, reject) => {
        apiOrderPlace({
          ...this.orderFormParams,
          ...this.pageQueryPayload,
          ...this.orderExtendParams,
          action: "buy",
        })
          .then((data) => {
            // 监听全局duringPayment事件
            // uni.$once('duringPayment', (data) => {
            //     this.handlePayResult(data)
            // })
            return data;
          })
          .then(({ type, order_id }) => {
            // 去支付
            this.$Router.replace({
              path: `/pages/payment/payment`,
              query: {
                from: type,
                order_id,
              },
            });
            resolve(order_id);
          })
          .catch((errMsg) => reject(errMsg));
      });
    },

    // 支付结果处理
    // handlePayResult({ result, order_id }) {
    //     switch (result) {
    //         case PaymentStatusEnum['SUCCESS']:
    //         case PaymentStatusEnum['FAIL']:
    //             setTimeout(() => {
    //                 this.$Router.replace({
    //                     path: '/pages/payment_result/payment_result',
    //                     query: { order_id, from: 'order' }
    //                 })
    //             }, 1000)
    //             break
    //         case PaymentStatusEnum['CLOSE']:
    //             this.$Router.push({
    //                 path: '/pages/payment_result/payment_result',
    //                 query: { order_id, from: 'order' }
    //             })
    //             break
    //     }
    // },
    //不可购买弹框
    handledisBuy() {
      this.$Router.replaceAll({
        path: "/pages/shop_cart/shop_cart",
      });
    },
  },

  async onLoad() {
    const options = this.$Route.query;
    console.log(options);
    // 初始化页面
    try {
      // 初始化页面参数
      this.from = options.from;
      this.pageQueryPayload = options.payload;
      this.orderFrom.address_id = options.address_id;
      // 初始化配送方式
      if (options.from == 4) {
        this.orderFrom.delivery_type = 4;
      } else {
        await this.initDeliveryType();
      }
      // 初始化优惠券数据(营销商品不可使用)
      !this.isMarketing && (await this.initCouponGoods());
      // 默认选择可用优惠券
      this.$set(
        this.orderFrom,
        "coupon_list_id",
        this.couponsInfo?.can_use[0]?.id
      );
      // 初始化页面数据
      await this.initOrderData();

      this.pageStatus = PageStatusEnum["NORMAL"];
    } catch (errMsg) {
      this.pageErrorMsg = errMsg;
      this.pageStatus = PageStatusEnum["ERROR"];
      console.log("页面数据初始化失败", errMsg);
    }
  },

  onUnload() {
    uni.$off(["changeStore", "changeAddress"]);
  },
};
</script>

<style lang="scss" scoped>
// Page style
.goods-order {
  padding: 0 20rpx calc(100rpx + 20rpx + constant(safe-area-inset-bottom)) 20rpx;
  padding: 0 20rpx calc(100rpx + 20rpx + env(safe-area-inset-bottom)) 20rpx;

  // 卡片
  .contain {
    margin-top: 20rpx;
    border-radius: 7px;
    background-color: #ffffff;
    overflow: hidden;
  }

  // 单元格
  .cell {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: $-font-size-nr;
    color: $-color-black;

    &-label {
      width: 130rpx;
    }

    &-content {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: flex-end;
    }
  }

  // 初始化复选框的外边距
  .u-checkbox {
    ::v-deep .u-checkbox__label {
      margin: 0;
    }
  }
}

// 配送方式
.delivery {
  &-tabs {
    border-bottom: $-solid-border;
  }

  &-contain {
    // border-top: $-solid-border;
  }
}

// 门店自提表单
.store-from {
  padding-left: 20rpx;
  font-size: $-font-size-nr;

  &-item {
    display: flex;
    align-items: center;
    height: 100rpx;
    padding-right: 20rpx;

    &:nth-child(n + 2) {
      border-top: $-dashed-border;
    }
  }
}

// 商品
.goods {
  &-item {
    position: relative;

    &:nth-child(n + 2)::before {
      position: absolute;
      left: 0;
      right: 0;
      top: 0;
      display: block;
      content: "";
      border-top: $-solid-border;
      margin-left: 20rpx;
    }

    &-widget {
      padding: 0 20rpx 0 calc((20rpx * 2) + 160rpx);

      .delivery-tag {
        display: inline-block;
        padding: 4rpx 15rpx;
        margin-bottom: 20rpx;
        border-radius: 60px;
        font-size: $-font-size-xxs;
        background-color: #f4f4f4;
        color: $-color-muted;
      }
    }
  }

  // 会员价
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
}

// 留言
.remark {
  height: 100rpx;
  padding: 0 20rpx;
}

// 优惠
.discounts {
  padding-left: 20rpx;

  &-item {
    height: 100rpx;
    padding-right: 20rpx;

    &:nth-child(n + 2) {
      border-top: $-dashed-border;
    }
  }
}

// 订单清算
.clearing {
  padding: 20rpx;

  &-item {
    height: 70rpx;
  }
}

// 订单功能区
.operational {
  box-sizing: border-box;
  position: fixed;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 100;
  height: calc(100rpx + constant(safe-area-inset-bottom));
  height: calc(100rpx + env(safe-area-inset-bottom));
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20rpx constant(safe-area-inset-bottom) 20rpx;
  padding: 0 20rpx env(safe-area-inset-bottom) 20rpx;
  border-top: $-solid-border;
  background-color: #ffffff;

  .order-amount {
    font-size: &-font-size-nr;
    color: $-color-normal;
  }

  &-button {
    padding: 16rpx 45rpx;
    border-radius: 60px;
    font-size: $-font-size-md;
    @include background_linear(); color: #ffffff;
  }
}

.goods-disable {
  position: fixed;
  left: 0;
  right: 0;
  bottom: calc(100rpx + constant(safe-area-inset-bottom));
  bottom: calc(100rpx + env(safe-area-inset-bottom));
  z-index: 100;
  background-color: #ffffff;
  height: 86rpx;
  padding: 10rpx 20rpx;
  display: flex;
  align-items: center;
  justify-content: space-between;

  .text {
    @include font_color();
  }

  .image {
    height: 50rpx;
    width: 50rpx;
  }
}

// 优惠券【弹窗】
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
    &__tabs {
      border-top: $-solid-border;
    }

    &__contain {
      // margin-top: 20rpx;
      padding: 0 20rpx;
    }

    .coupons-item {
      margin-top: 20rpx;
    }
  }

  &-footer {
    display: flex;
    align-items: center;
    padding: 0 20rpx;
    height: 100rpx;
    background-color: #ffffff;

    &-button {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 82rpx;
      border-radius: 60px;
      font-size: $-font-size-lg;
      @include background_color();
      color: #ffffff;
    }
  }
}

.red {
  color: red;
}

.modal {
  padding: 50rpx 0;

  &-text-title {
    text-align: center;
    font-size: large;
    font-weight: 500;
  }

  &-text-content {
    text-align: center;
  }
}

.modal-btn {
  @include background_color();
  color: #fff;
  width: 80%;
  margin: 0 auto;
}

.skuline {
  -webkit-line-clamp: 1;
  overflow: hidden;
  word-break: break-all;
  text-overflow: ellipsis;
  display: -webkit-box; // 弹性伸缩盒
  -webkit-box-orient: vertical; // 设置伸缩盒子元素排列方式
}
</style>
