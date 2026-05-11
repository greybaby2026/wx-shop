<template>
  <view class="goods-comment-user" :class="themeName">
    <view class="header" v-if="hasComment">
      <!-- Title -->
      <view class="title">
        <text>商品好评率</text>
        <text :style="{ color: themeColor }">{{ goodsPercent }}</text>
      </view>

      <!-- Navigation -->
      <view class="nav">
        <block v-for="(item, index) in goodsCategory" :key="item.id">
          <view
            :class="[
              'nav-item',
              { 'nav-item--active': item.id === goodsCategoryCurrent.id },
            ]"
            @tap="changeCategory(item)"
          >
            <text>{{ item.name }}</text>
            <text>{{ "(" + item.count + ")" }}</text>
          </view>
        </block>
      </view>
    </view>

    <!-- Main -->
    <view class="main" v-show="hasComment">
      <mescroll-contain
        height="80%"
        :up-option="{ auto: false }"
        :down-option="{ auto: false }"
        @upcallback="upCallback"
        ref="mescroll"
      >
        <view class="contain">
          <view
            class="evaluate nr"
            v-for="item in goodsCommentList"
            :key="item.id"
          >
            <!-- 用户信息 -->
            <view class="flex">
              <u-image
                width="70"
                height="70"
                mode="scaleToFill"
                border-radius="50%"
                :src="item.avatar"
              />
              <text class="m-l-20 m-r-20">{{ item.nickname }}</text>
              <u-rate
                :current="item.goods_comment"
                :active-color="themeColor"
                :disabled="true"
              />
            </view>
            <!-- 订单信息 -->
            <view class="muted xs m-t-10">
              <text>{{ item.create_time }}</text>
              <text class="m-l-20 skuline">{{ item.spec_value_str }}</text>
            </view>
            <!-- 评价文本 -->
            <view class="m-t-16">{{ item.comment }}</view>
            <!-- 评价图片 -->
            <view class="evaluate-images" v-if="item.image">
              <u-image
                class="m-r-16 m-t-16"
                v-for="(imageItem, imageIndex) in item.image"
                :key="imageItem"
                :src="imageItem"
                width="200"
                height="200"
                mode="scaleToFill"
                @tap="showCommentImage(item.image, imageIndex)"
              />
            </view>
            <!-- 商家回复 -->
            <view class="evaluate-reply" v-if="item.reply">
              <text class="m-r-20 muted">商家回复:</text>
              <text>{{ item.reply }}</text>
            </view>
          </view>
        </view>
      </mescroll-contain>
    </view>
    <u-empty
      :show="!hasComment"
      text="暂无评价"
      src="/static/images/empty/evaluate.png"
      :icon-size="280"
      margin-top="280"
    ></u-empty>
  </view>
</template>

<script>
import { apiGoodsCommentCategory, apiGoodsCommentList } from "@/api/goods";

let mescroll = null;

export default {
  name: "GoodsCommentHe",

  data() {
    return {
      goods_id: "", // 商品ID
      goodsPercent: "", // 好评率
      goodsCategory: [], // 评论分类
      goodsCategoryCurrent: {}, // 分类当前
      goodsCommentList: [], // 评价列表
      hasComment: false, // 是否有评价数据
    };
  },

  methods: {
    // 初始化评价分类
    initCommentCategory() {
      return new Promise((resolve, reject) => {
        apiGoodsCommentCategory({
          goods_id: this.goods_id,
        })
          .then((data) => {
            this.goodsPercent = data.percent ?? "0%";
            this.goodsCategory =
              data.comment.filter((item) => item.count) ?? [];
            this.goodsCategoryCurrent = this.goodsCategory[0];
            resolve(data);
          })
          .catch((err) => {
            reject(err);
          });
      });
    },

    // 上拉加载更多
    upCallback({ num, size }) {
      apiGoodsCommentList({
        id: this.goodsCategoryCurrent.id,
        goods_id: this.goods_id,
        page_no: num,
        page_size: size,
      })
        .then(({ lists, page_size, count }) => {
          if (num === 1) this.goodsCommentList = [];
          this.goodsCommentList = [...this.goodsCommentList, ...lists];

          mescroll.endBySize(page_size, count);
        })
        .catch((err) => {
          mescroll.endBySize();
        });
    },

    // 更改分类
    changeCategory(item) {
      this.goodsCategoryCurrent = item;
      mescroll.downCallback();
    },

    // 查看评价图片
    showCommentImage(list, index) {
      // 预览图片
      uni.previewImage({
        urls: list,
        current: index,
      });
    },
  },

  async onLoad() {
    const options = this.$Route.query;

    try {
      if (!options.id) throw new Error();
      this.goods_id = options.id;
      await this.initCommentCategory().then(() => {
        this.hasComment = !!this.goodsCategory.length ? true : false;
        if (this.hasComment) {
          this.$nextTick(() => {
            mescroll.downCallback();
          });
        }
      });
    } catch (err) {
      console.log(err);
      this.$toast({ title: "页面异常" });
      setTimeout(() => {
        this.$Router.back();
      }, 1.2 * 1000);
    }
  },

  mounted() {
    mescroll = this.$refs["mescroll"];
  },
};
</script>

<style lang="scss" scoped>
.goods-comment-user {
  display: flex;
  flex-direction: column;
  max-height: 100vh;
  overflow: hidden;
  // padding: 20rpx 0;
}

.header {
  background-color: #ffffff;

  .title {
    display: flex;
    align-items: center;
    height: 80rpx;
    padding: 0 20rpx;
    border-bottom: $-solid-border;
    font-size: $-font-size-xs;
    color: $-color-lighter;
  }

  .nav {
    display: flex;
    flex-wrap: wrap;
    padding: 20rpx;
    font-size: $-font-size-xs;

    &-item {
      padding: 8rpx 30rpx;
      border-radius: 60px;
      margin: 10rpx 10rpx 0 0;
      background-color: #f4f4f4;

      &--active {
        @include background_color();
        color: #ffffff;
      }
    }
  }
}

.main {
  flex: 1;
  margin-top: 20rpx;
  overflow: hidden;

  .evaluate {
    padding: 24rpx;
    background-color: #ffffff;

    &:nth-child(n + 2) {
      border-top: $-solid-border;
    }

    &-images {
      display: flex;
      flex-wrap: wrap;
    }

    &-reply {
      position: relative;
      padding: 30rpx 20rpx;
      margin-top: 30rpx;
      border-radius: 8px;
      font-size: $-font-size-nr;
      background-color: #f6f6f6;
      word-break: break-all;
      &::after {
        position: absolute;
        top: calc(-20rpx * 2);
        left: 40rpx;
        display: block;
        content: "";
        width: 0;
        height: 0;
        border-top: 20rpx solid transparent;
        border-right: 20rpx solid transparent;
        border-left: 20rpx solid transparent;
        border-bottom: 20rpx solid #f6f6f6;
      }
    }
  }
}
</style>
