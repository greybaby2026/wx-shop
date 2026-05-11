<template>
  <view
    class="widget-root"
    :style="{
      padding: `${$px2rpx(styles.padding_top)}rpx ${$px2rpx(
        styles.padding_horizontal
      )}rpx ${$px2rpx(styles.padding_bottom)}rpx`,
      'background-color': styles.root_bg_color,
      'border-radius': `${styles.radius}rpx`,
    }"
  >
    <slot></slot>
  </view>
</template>

<script>
export default {
  props: {
    styles: {
      type: [Object, Array],
    },
  },
};
</script>

<style></style>
