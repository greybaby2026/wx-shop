<template>
  <view class="goods-collects" :class="themeName">
    <mescroll-contain
      top="20rpx"
      ref="collect"
      :up-option="upOpt"
      @upcallback="upCallback"
    >
      <view>
        <view
          class="collect"
          v-for="(item, index) in collectList"
          :key="item.goods_id"
        >
          <u-swipe-action
            :show="item.show"
            :index="index"
            :key="item.goods_id"
            @click="swipeClick"
            @open="swipeOpen"
            :options="swipeOptions"
            style="width: 100%"
          >
            <goods-card
              shape="rectangle"
              :name="item.name"
              :image="item.image"
              :contain-style="{ 'border-raius': 0, height: '200rpx' }"
              :image-style="{ width: '160rpx', height: '160rpx' }"
            >
              <text class="muted xs skuline">{{ item.spec_value_str }}</text>
              <view class="m-t-10 flex row-between">
                <price
                  :content="item.min_price"
                  mainSize="32rpx"
                  minorSize="24rpx"
                  :color="themeColor"
                />
                <template v-if="item.status">
                  <view
                    class="collect-button collect-button--normal"
                    @click="goGoodsDetail(item.goods_id)"
                    >去购买</view
                  >
                </template>
                <template v-else>
                  <view class="collect-button collect-button--muted"
                    >已失效</view
                  >
                </template>
              </view>
            </goods-card>
          </u-swipe-action>
        </view>
      </view>
    </mescroll-contain>
  </view>
</template>

<script>
import { apiGoodsCollect, apiGoodsCollectList } from "@/api/goods";

let mescroll = null;

export default {
  name: "GoodsCollects",

  data() {
    return {
      collectList: [], // 收藏列表
      swipeOptions: [
        {
          text: "删除",
          style: {
            backgroundColor: "#FF2C3C",
          },
        },
      ],
      upOpt: {
        noMoreSize: 10,
        empty: {
          icon: "/static/images/empty/collect.png",
          tip: "暂无收藏~",
          fixed: true,
        },
      },
    };
  },

  mounted() {
    mescroll = this.$refs["collect"];
  },

  methods: {
    // 上拉加载更多
    upCallback({ num, size }) {
      apiGoodsCollectList({
        page_no: num,
        page_size: size,
      })
        .then(({ lists, page_size, count }) => {
          lists.forEach((item) => (item.show = false));
          if (num === 1) this.collectList = [];
          this.collectList = [...this.collectList, ...lists];
          // mescroll.endBySize(page_size, count)
          mescroll.endSuccess(lists.length, count);
        })
        .catch((err) => {
          mescroll.endBySize();
        });
    },

    // 去商品详情
    goGoodsDetail(goods_id) {
      this.$Router.push({
        path: "/pages/goods_detail/goods_detail",
        query: { id: goods_id },
      });
    },

    // 点击滑动操作Options
    swipeClick(collectIndex, optionIndex) {
      if (optionIndex == 0) {
        const collectItem = this.collectList[collectIndex];
        collectItem.show = false;

        this.handleCollectDel(collectItem.goods_id);
      }
    },

    // 关闭其他Options
    swipeOpen(index) {
      this.collectList[index].show = true;
      this.collectList.map((val, idx) => {
        if (index != idx) this.collectList[idx].show = false;
      });
    },

    // 取消收藏
    handleCollectDel(id) {
      apiGoodsCollect({
        goods_id: id,
        is_collect: 0,
      })
        .then(() => {
          mescroll.downCallback();
        })
        .catch((err) => {
          console.log(err);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.collect {
  position: relative;

  &:nth-child(n + 2)::after {
    position: absolute;
    top: 0;
    right: 0;
    display: block;
    content: "";
    width: calc(100% - 160rpx - 20rpx * 2);
    border-top: $-solid-border;
  }

  &-button {
    box-sizing: border-box;
    padding: 8rpx 30rpx;
    font-size: $-font-size-sm;
    border-radius: 60px;

    &--normal {
      border-width: 1px;
      border-style: solid;
      @include font_color();
      @include border_color();
    }

    &--muted {
      color: $-color-lighter;
      background-color: #f2f2f2;
    }
  }
}
</style>
