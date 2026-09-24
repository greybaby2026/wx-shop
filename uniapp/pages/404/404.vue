<template>
  <!-- 404 页：由 router.js 的 catch-all 重定向进入（未匹配到的路径） -->
  <view class="not-found">
    <image class="not-found__img" src="/static/images/empty/shop.png" mode="aspectFit" />
    <view class="not-found__title">页面不存在</view>
    <view class="not-found__desc">您访问的页面可能已被移除，或链接有误</view>
    <view class="not-found__btn white" @tap="goHome">返回首页</view>
  </view>
</template>

<script>
export default {
  name: 'NotFound',
  methods: {
    goHome() {
      // 用 replaceAll 清空页面栈，避免用户返回时又回到 404
      this.$Router.replaceAll({ path: '/pages/index/index' })
    }
  }
}
</script>

<style lang="scss" scoped>
.not-found {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  padding: 0 80rpx;
  background-color: #ffffff;

  &__img {
    width: 280rpx;
    height: 280rpx;
  }

  &__title {
    margin-top: 40rpx;
    font-size: 34rpx;
    color: #222222;
  }

  &__desc {
    margin-top: 16rpx;
    font-size: 26rpx;
    line-height: 40rpx;
    color: #999999;
    text-align: center;
  }

  &__btn {
    margin-top: 60rpx;
    width: 320rpx;
    height: 80rpx;
    line-height: 80rpx;
    font-size: 30rpx;
    text-align: center;
    border-radius: 40rpx;
    @include background_color();
  }
}
</style>
