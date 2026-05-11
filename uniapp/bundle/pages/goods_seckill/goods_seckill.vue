<template>
  <view class="pages" :class="themeName">
    <view class="goods-seckill" :class="{ 'bg-size': isEmpty }">
      <mescroll-body
        ref="mescrollRef"
        @init="mescrollInit"
        @up="upCallback"
        :up="upOption"
        :down="downOption"
        @down="downCallback"
      >
        <view>
          <view class="header">
            <u-divider
              color="#ffffff"
              bg-color="transpartent"
              half-width="100"
              border-color="#ffffff"
            >
              <view class="flex">
                <image
                  class="icon-image"
                  src="/bundle/static/images/icon_seckill.png"
                ></image>
                <text class="xl m-l-6 bold">秒杀商品</text>
              </view>
            </u-divider>
          </view>
          <view class="goods-list" v-if="seckillGoods.length">
            <router-link
              v-for="(item, index) in seckillGoods"
              :key="index"
              :to="{
                path: '/bundle/pages/seckill_detail/seckill_detail',
                query: { id: item.id },
              }"
            >
              <view class="goods-item flex bg-white">
                <u-image
                  width="210rpx"
                  height="210rpx"
                  border-radius="10rpx"
                  :src="item.image"
                />
                <view class="goods-info m-l-20">
                  <view class="goods-name line-2 m-b-20">{{ item.name }}</view>
                  <text class="sale-info xs br60">
                    已抢{{ item.sales_volume + item.virtual_sales_num }}件
                  </text>
                  <view class="info-footer flex row-between m-t-5">
                    <view class="price">
                      <price
                        :content="item.min_seckill_price"
                        :color="themeColor"
                        :font-weight="500"
                        mainSize="34rpx"
                        minorSize="26rpx"
                      />
                      <price
                        class="m-l-14"
                        :content="item.original_price"
                        :line-through="true"
                        color="#999"
                        mainSize="26rpx"
                        minorSize="26rpx"
                      />
                    </view>
                    <button class="br60 white btn" size="sm">立即抢购</button>
                  </view>
                </view>
              </view>
            </router-link>
          </view>
        </view>
      </mescroll-body>
    </view>
  </view>
</template>

<script>
import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins.js";
import { apiSeckillLists } from "@/api/activity.js";
import { mapGetters, mapState } from "vuex";

export default {
  mixins: [MescrollMixin], // 使用mixin
  data() {
    return {
      upOption: {
        textColor: "#fff",
        empty: {
          tip: "活动暂未开始~", // 提示
          icon: "",
          top: "40vh",
          textColor: "#fff",
        },
      },
      downOption: {
        textColor: "#fff",
      },
      isEmpty: false,
      seckillGoods: [], // 列表数据--全部
    };
  },
  onShareAppMessage() {
    const { code } = this.userInfo;

    const share = {
      path: `/bundle/pages/goods_seckill/goods_seckill?invite_code=${code}`,
    };
    return share;
  },
  computed: {
    ...mapGetters(["userInfo"]),
  },
  methods: {
    // 上拉加载
    upCallback(page) {
      apiSeckillLists({
        page_size: page.size,
        page_no: page.num,
      })
        .then(({ lists, count }) => {
          if (page.num == 1) this.seckillGoods = [];

          this.seckillGoods.push(...lists);
          if (lists.length == 0) {
            this.isEmpty = true;
          }
          this.mescroll.endSuccess(lists.length, count);
        })
        .catch(() => {
          this.mescroll.endErr();
        });
    },
  },
};
</script>

<style lang="scss" scoped>
page {
  padding: 0;
}

.goods-seckill {
  @include background_linear();
  background-repeat: no-repeat;

  .header {
    padding: 26rpx 0;

    .icon-image {
      width: 40rpx;
      height: 40rpx;
    }
  }

  ::v-deep.mescroll-empty .empty-tip {
    color: #ffffff;
  }

  .goods-list {
    overflow: hidden;
    padding: 0 20rpx 20rpx;

    .goods-item {
      padding: 20rpx;
      margin-top: 20rpx;
      border-radius: 10rpx;

      .goods-info {
        flex: 1;
        width: 470rpx;

        .sale-info {
          padding: 4rpx 16rpx;
          @include background_color(0.2);
          @include font_color();
        }

        .info-footer {
          .btn {
            @include background_linear();
          }
        }
      }
    }
  }
}
</style>
