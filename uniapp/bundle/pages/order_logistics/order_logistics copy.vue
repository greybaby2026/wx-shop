<template>
  <view class="order-logistics u-skeleton" :class="themeName">
    <view class="express m-t-20">
      <!-- 商品图片 -->
      <view class="express-goods" :data-count="order.goods_count || '0'">
        <u-image
          width="100%"
          height="100%"
          mode="scaleToFill"
          :src="order.goods_image"
        />
      </view>
      <!-- 快递信息 -->
      <view class="express-info">
        <text class="md">{{ getOrderStatusToName(order.order_status) }}</text>
        <text class="sm m-t-10"
          >物流公司：{{ order.express_name || "--" }}</text
        >
        <view class="sm m-t-10 flex">
          <text>快递单号：{{ order.invoice_no || "--" }}</text>
          <template v-if="order.invoice_no && order.send_type === 1">
            <view
              class="m-l-30"
              :style="{ color: themeColor }"
              @click.stop="$copy(order.invoice_no)"
              >复制</view
            >
          </template>
        </view>
      </view>
    </view>

    <view class="logistics m-t-20">
      <u-time-line>
        <!-- 收货地址 -->
        <u-time-line-item>
          <template v-slot:node>
            <view class="node-icon node-icon--active">
              <text class="white">收</text>
            </view>
          </template>
          <template v-slot:content>
            <view class="node-title">
              <text>{{ take.contact }}</text>
              <text class="m-l-20">{{ take.mobile }}</text>
            </view>
            <view class="node-desc">{{ take.address }}</view>
          </template>
        </u-time-line-item>

        <!-- 交易完成 -->
        <u-time-line-item v-if="order.order_status === 3">
          <template v-slot:node>
            <view class="node-icon node-icon--active">
              <u-icon name="checkmark" color="#fff" />
            </view>
          </template>
          <template v-slot:content>
            <view class="node-title">{{ finish.title }}</view>
            <view class="node-desc">{{ finish.tips }}</view>
            <view class="node-time">{{ finish.time }}</view>
          </template>
        </u-time-line-item>

        <!-- S 运输 -->
        <u-time-line-item v-if="delivery.new_trace">
          <template v-slot:node>
            <view class="node-icon node-icon--active">
              <u-icon name="car-fill" color="#fff" />
            </view>
          </template>
          <template v-slot:content>
            <view class="node-title">{{ delivery.title }}</view>
            <view class="node-desc">{{ delivery.new_trace[1] }}</view>
            <view class="node-time">{{ delivery.new_trace[0] }}</view>
          </template>
        </u-time-line-item>
        <block v-for="(item, index) in delivery.traces" :key="index">
          <u-time-line-item>
            <template v-slot:content>
              <view class="node-desc">{{ item[1] }}</view>
              <view class="node-time">{{ item[0] }}</view>
            </template>
          </u-time-line-item>
        </block>
        <!-- E 运输 -->

        <!-- 发货 -->
        <u-time-line-item v-if="order.order_status === 2">
          <template v-slot:node>
            <view class="node-icon node-icon--active">
              <u-icon name="bell-fill" color="#fff" />
            </view>
          </template>
          <template v-slot:content>
            <view class="node-title">{{ shipment.title }}</view>
            <view class="node-desc">{{ shipment.tips }}</view>
            <view class="node-time">{{ shipment.time }}</view>
          </template>
        </u-time-line-item>

        <!-- 支付 -->
        <u-time-line-item v-if="pay">
          <template v-slot:node>
            <view class="node-icon node-icon--active">
              <u-icon name="red-packet-fill" color="#fff" />
            </view>
          </template>
          <template v-slot:content>
            <view class="node-title">{{ pay.title }}</view>
            <view class="node-desc">{{ pay.tips }}</view>
            <view class="node-time">{{ pay.time }}</view>
          </template>
        </u-time-line-item>

        <!-- 下单 -->
        <u-time-line-item>
          <template v-slot:node>
            <view class="node-icon node-icon--active">
              <u-icon name="file-text-fill" color="#fff" />
            </view>
          </template>
          <template v-slot:content>
            <view class="node-title">{{ buy.title }}</view>
            <view class="node-desc">{{ buy.tips }}</view>
            <view class="node-time">{{ buy.time }}</view>
          </template>
        </u-time-line-item>
      </u-time-line>
    </view>
    <u-skeleton
      :loading="loadingSkeleton"
      :animation="true"
      bgColor="#FFFFFF"
    />
  </view>
</template>

<script>
import { apiOrderLogistics } from "@/api/order";
import { apiIntegralOrderTraces } from "@/api/integral_mall";
export default {
  name: "OrderLogistics",

  data() {
    return {
      order_id: "", // 订单ID
      order: {}, // 订单信息
      take: {}, // 收货人信息
      finish: {}, // 交易完成信息
      delivery: {}, // 物流信息
      shipment: {}, // 发货信息
      pay: {}, // 支付信息
      buy: {}, // 下单信息
      loadingSkeleton: true,
    };
  },

  methods: {
    // 初始化物流数据
    initLogisticsData() {
      const api =
        this.type == "integral"
          ? apiIntegralOrderTraces({
              id: this.order_id,
            })
          : apiOrderLogistics({
              id: this.order_id,
            });
      api
        .then((data) => {
          this.order = data.order;
          this.take = data.take;
          this.finish = data.finish;
          this.shipment = data.shipment;
          this.pay = data.pay;
          this.buy = data.buy;
          return data;
        })
        .then((data) => {
          const traces = data.delivery?.traces;
          // 【运输中】数据结构格式化
          if (traces.length) {
            const newTrace = traces.shift();
            data.delivery = {
              ...data.delivery,
              new_trace: newTrace,
            };
          }
          this.delivery = data.delivery;
          this.loadingSkeleton = false;
        });
    },

    // 获取订单状态名称
    getOrderStatusToName(status) {
      switch (status) {
        case 0:
          return "已下单";
        case 1:
          return "待发货";
        case 2:
          return "待收货";
        case 3:
          return "交易完成";
        case 4:
          return "已关闭";
      }
    },
  },

  async onLoad() {
    const options = this.$Route.query;

    try {
      if (!options.order_id) throw new Error("物流异常");
      this.order_id = options.order_id;
      this.type = options.type;
      await this.initLogisticsData();
    } catch (err) {
      console.log(err);
      setTimeout(() => {
        this.$Router.back();
      }, 0.5 * 1000);
    }
  },
};
</script>

<style lang="scss" scoped>
.order-logistics {
  // padding: 0 20rpx;
}

.express {
  display: flex;
  align-items: center;
  height: 200rpx;
  padding: 20rpx 24rpx;
  background-color: #ffffff;

  &-goods {
    position: relative;
    width: 160rpx;
    height: 160rpx;

    &::before {
      z-index: 9;
      position: absolute;
      left: 0;
      right: 0;
      bottom: 0;
      display: block;
      content: "共" attr(data-count) "件商品";
      padding: 5rpx 20rpx;
      font-size: $-font-size-xs;
      text-align: center;
      border-radius: 60px;
      background-color: rgba(0, 0, 0, 0.6);
      color: #ffffff;
    }
  }

  &-info {
    display: flex;
    flex-direction: column;
    margin-left: 24rpx;
  }
}

.logistics {
  padding: 30rpx 24rpx 30rpx calc(24rpx + 40rpx / 2);
  background-color: #ffffff;

  .node-icon {
    width: 40rpx;
    height: 40rpx;
    border-radius: 100rpx;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #d0d0d0;

    &--active {
      @include background_linear();
    }
  }

  .node-title {
    font-weight: 500;
    font-size: $-font-size-nr;
    color: $-color-black;
  }

  .node-desc {
    margin-bottom: 6rpx;
    font-size: $-font-size-sm;
    color: $-color-lighter;
  }

  .node-time {
    color: $-color-muted;
    font-size: $-font-size-sm;
  }
}
</style>
