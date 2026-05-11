<template>
  <view class="lazy-component">
    <view class="lazy-contain">
      <view v-if="isInit">
        <slot></slot>
      </view>
      <view v-else>
        <slot name="skeleton"></slot>
      </view>
    </view>
  </view>
</template>

<script>
export default {
  name: "lazy-component",
  props: {
    // 参照物
    viewport: {
      type: String,
      default: () => null,
    },
    // 阀值
    threshold: {
      type: Number,
      default: 100,
    },
    // 位置
    position: {
      type: String,
      default: "bottom",
    },
  },
  data() {
    return {
      isInit: false,
    };
  },
  mounted() {
    // 添加延时器，防止APP端Error: Not Found：Page[1]
    this.timer = setTimeout(() => {
      this.initObserver();
    }, 50);
  },
  methods: {
    initObserver() {
      try {
        this.observer = uni.createIntersectionObserver(this, {
          threshold: [0, Number.MIN_VALUE, 0.01],
        });
        const options = {
          [this.position]: this.threshold,
        };
        if (this.viewport) {
          this.observer.relativeTo(this.viewport, options);
        } else {
          this.observer.relativeToViewport(options);
        }
        this.observer.observe(".lazy-contain", (res) => {
          if (res.intersectionRatio > 0) {
            this.isInit = true;
            this.closeObserver();
          }
        });
      } catch (e) {
        this.isInit = true;
      }
    },
    closeObserver() {
      this.observer && this.observer.disconnect();
    },
  },
  beforeDestroy() {
    clearTimeout(this.timer);
    this.closeObserver();
  },
};
</script>

<style lang="scss"></style>
