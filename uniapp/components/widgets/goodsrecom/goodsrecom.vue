<template>
  <view
    :style="{
      'margin-top': `${$px2rpx(styles.margin_top)}rpx`,
      'margin-right': '20rpx',
      'margin-left': '20rpx',
    }"
    v-if="content.data.length"
  >
    <widget-root :styles="{ ...styles, radius: 30 }">
      <view class="title">
        <view
          class="title-content"
          :style="{
            color: styles.header_title_color,
            'font-size': `${$px2rpx(styles.header_title_size)}rpx`,
          }"
          >{{ content.header_title }}</view
        >
      </view>
      <goods :content="content" :styles="styles" />
    </widget-root>
  </view>
</template>

<script type="text/javascript">
import goods from "../goodsgroup/goods.vue";
export default {
  components: {
    goods,
  },
  props: {
    content: {
      type: [Object, Array],
    },
    styles: {
      type: [Object, Array],
    },
  },
  data() {
    return {};
  },
  methods: {},
  computed: {},
};
</script>
<style lang="scss" scoped>
.title {
  padding: 30rpx 0;
  text-align: center;

  .title-content {
    display: inline-flex;
    align-items: center;
    font-weight: bold;

    &::before,
    &::after {
      content: "";
      display: inline-block;
      width: 98rpx;
      height: 1px;
      margin: 0 20rpx;
      background: #dcdfe6;
    }
  }
}
</style>
