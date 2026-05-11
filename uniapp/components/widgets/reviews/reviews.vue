<template>
  <view class="reviews" :class="themeName">
    <router-link
      :to="`/bundle/pages/goods_comment_he/goods_comment_he?id=${goodsId}`"
    >
      <view class="flex row-between reviews-header">
        <view>
          <text class="nr">评价</text>
        </view>
        <view class="muted">
          <u-icon name="arrow-right" />
        </view>
      </view>
    </router-link>
    <view class="reviews-content">
      <template v-if="comment.comment">
        <view class="flex">
          <u-image
            width="70"
            height="70"
            mode="scaleToFill"
            border-radius="50%"
            :src="comment.avatar"
          />
          <text class="m-l-20">{{ comment.nickname }}</text>
        </view>
        <view class="flex">
          <view class="m-t-16 line-3 m-r-10">{{ comment.comment }}</view>
          <view class="reviews-images" v-if="comment.comment_image.length">
            <u-image
              v-show="index == 0"
              v-for="(item, index) in comment.comment_image"
              :key="item"
              :src="item"
              width="120"
              height="120"
              mode="scaleToFill"
              border-radius="10"
              @tap="showCommentImage(index)"
            />
            <view class="num-tip" v-if="comment.comment_image.length > 1">{{
              comment.comment_image.length
            }}</view>
          </view>
        </view>

        <view class="muted xs m-t-10 skuline">
          <text>{{ comment.spec_value_str }}</text>
        </view>
      </template>
      <view v-else class="muted flex row-center xs"> 暂无评价~ </view>
    </view>
  </view>
</template>

<script type="text/javascript">
export default {
  props: {
    content: {
      type: [Object, Array],
    },
    styles: {
      type: [Object, Array],
    },
    comment: {
      type: [Object, Array],
      default: () => ({}),
    },
    goodsId: {
      type: [Number, String],
    },
  },
  data() {
    return {};
  },
  methods: {
    // 查看评价图片
    showCommentImage(index) {
      // 预览图片
      uni.previewImage({
        urls: this.comment.comment_image,
        current: index,
      });
    },
  },
  computed: {},
};
</script>

<style lang="scss" scoped>
.reviews {
  margin: 30rpx 20rpx 30rpx 20rpx;
  border-radius: 30rpx;
  background-color: #ffffff;

  &-header {
    padding: 24rpx;
  }

  &-content {
    padding: 24rpx;
    border-top: 1px solid $-color-border;

    .evaluate-images {
      display: flex;
      flex-wrap: wrap;
    }
  }

  &-rate {
    @include font_color();
  }
  .reviews-images {
    position: relative;
    margin-left: auto;
    .num-tip {
      bottom: 8rpx;
      right: 8rpx;
      position: absolute;
      background-color: gray;
      color: white;
      border-radius: 25rpx;
      width: 25rpx;
      height: 25rpx;
      text-align: center;
      font-size: 18rpx;
      line-height: 25rpx;
    }
  }
}
</style>
