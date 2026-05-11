<template>
  <view class="m-t-6 flex flex-wrap row-center truntable-box">
    <view
      v-for="(item, index) in params.prizes"
      :key="index"
      class="lattice nr"
      :class="{
        active: activeIndex == index,
        start: item.flag && index == 4,
        end: !item.flag && index == 4,
      }"
      @click="startLuckDraw(index)"
    >
      <image
        v-if="!(item.flag && index == 4) && params.prize_base_image"
        class="lattice-bg"
        :src="params.prize_base_image"
      ></image>
      <image
        class="start-image"
        v-if="index == 4"
        :src="params.start_button_image || ''"
      ></image>
      <image v-if="index != 4" :src="item.image || ''"></image>
      <view v-if="index != 4" style="font-size: 22rpx" class="line-1">{{
        item.name || item.tips
      }}</view>
    </view>
    <u-popup
      v-model="showNeedPoints"
      :maskCloseAble="false"
      mode="center"
      safe-area-inset-bottom
      border-radius="14"
    >
      <view class="need-points">
        <view class="need-points-title"> 确认抽奖 </view>
        <view class="need-points-content">
          确认消耗
          <Text class="need-points-num">{{ params.need_integral || 0 }}</Text>
          积分抽奖？
        </view>
        <view class="need-points-btns flex row-around">
          <view class="need-points-btn" @click="handelCancel">取消</view>
          <view class="need-points-btn confirm" @click="handleStartLuckDraw"
            >确认</view
          >
        </view>
      </view>
    </u-popup>
  </view>
</template>

<script>
import { apiLuckyDrawStart } from "@/api/luck_draw.js";
const ORDER = [1, 2, 3, 8, "", 4, 7, 6, 5];
export default {
  props: {
    ids: {
      type: Number,
    },
    params: {
      type: Object || Array,
      default: {},
    },
    // 最少转动多少个
    circleTimes: {
      type: Number,
      default: 30,
    },
  },

  data() {
    return {
      isRunLuckDraw: false, //是否抽奖进行中
      orderIndex: -1, //中奖的索引
      activeIndex: -1, //当前显示在页面上的显示索引
      currentIndex: 1, //显示到第几个
      speed: 200, //抽奖的转动间隔
      showNeedPoints: false,

      result: "", //提示结果
      location: "",
    };
  },

  methods: {
    async startLuckDraw(index) {
      // 上锁，在抽奖期间不允许点击
      if (this.isRunLuckDraw) return;
      this.isRunLuckDraw = true;
      if (index == 4) {
        this.showNeedPoints = true;
      }
    },

    handelCancel() {
      this.showNeedPoints = false;
      this.isRunLuckDraw = false;
    },

    async handleStartLuckDraw() {
      try {
        const res = await apiLuckyDrawStart({ id: this.ids });
        if (res) {
          this.result = res.tips;
          this.location = res.location;
          this.orderIndex = res.location;
          this.showNeedPoints = false;
          this.start();
        }
        this.$parent.getLuckyDrawActivityFunc();
      } catch (error) {
        this.isRunLuckDraw = false;
      }
    },

    // 开始抽奖
    start() {
      let index = this.currentIndex % 9;
      if (index == 0) {
        index = 1;
        this.currentIndex += 1;
      }
      this.currentIndex += 1;

      // 中奖的索引
      const orderIndex = ORDER.findIndex((item) => item == this.orderIndex);
      this.activeIndex = ORDER.findIndex((item) => item == index);

      if (
        this.currentIndex > this.circleTimes + 8 &&
        orderIndex == this.activeIndex
      ) {
        if (this.luckDrawTimer) {
          clearTimeout(this.luckDrawTimer);
          setTimeout(this.reset, 1500);
          setTimeout(this.stopCallbackFun, 500);
        }
      } else {
        if (this.currentIndex < this.circleTimes) {
          this.speed -= 10;
        } else {
          this.speed += 60;
        }
        if (this.speed < 50) {
          this.speed = 50;
        }
        this.luckDrawTimer = setTimeout(this.start.bind(this), this.speed);
      }
    },

    // 重设转盘
    reset() {
      this.isRunLuckDraw = false;
      this.currentIndex = -1;
      this.speed = 100;
      this.activeIndex = -1;
    },

    // 转盘结束
    stopCallbackFun() {
      this.$emit("finish", {
        detail: this.result,
        location: this.location,
      });
    },
  },
};
</script>

<style lang="scss" scoped>
.truntable-box {
  margin: 10rpx 0 10rpx -10rpx;
}
.lattice {
  color: #333;
  padding: 20rpx;
  width: 172rpx;
  height: 172rpx;
  border-radius: 20rpx;
  position: relative;
  //   background: url(../../static/images/choujiang_block.png) no-repeat;
  //   background-size: 171rpx 171rpx;
  margin-right: 16rpx;
  margin-bottom: 16rpx;
  text-align: center;
  .lattice-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 171rpx;
    height: 171rpx;
  }
  image {
    width: 96rpx;
    height: 96rpx;
    position: relative;
    z-index: 1;
  }
  .line-1 {
    position: relative;
    z-index: 1;
    font-weight: 500;
  }
}
.lattice:nth-child(n + 7) {
  margin-bottom: 0;
}

.lattice:nth-child(3n) {
  margin-right: 0;
}

.lattice:nth-child(5) {
  view {
    margin-top: 12rpx !important;
    font-size: $-font-size-xs;
  }
}
// 5
.start,
.end {
  position: relative;
  overflow: hidden;
  .start-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 171rpx;
    height: 171rpx;
    z-index: 1;
  }
  //   color: #ed3720;
  //   background: url(../../static/images/choujiang_button.png) no-repeat;
  //   background-size: 171rpx 171rpx;
}
// 5
.end {
  color: #666666;
  background: url(../../static/images/choujiang_button_end.png) no-repeat;
  background-size: 171rpx 171rpx;
}

// 当前的
.active {
  opacity: 0.6;
}
.need-points {
  background: #fff;
  border-radius: 12rpx;
  width: 500rpx;
  padding: 48rpx 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  .need-points-title {
    font-size: 36rpx;
    color: #333;
    font-weight: 500;
    text-align: center;
    margin-top: 30rpx;
  }
  .need-points-content {
    font-size: 28rpx;
    color: #333;
    text-align: center;
    margin-top: 30rpx;
    .need-points-num {
      color: #ed3720;
      font-weight: 500;
      margin: 0 12rpx;
    }
  }
  .need-points-btns {
    width: 100%;
    display: flex;
    justify-content: space-around;
    margin-top: 48rpx;
    .need-points-btn {
      width: 200rpx;
      height: 60rpx;
      border: 4rpx solid #f3f3f3;
      color: #333;
      text-align: center;
      line-height: 60rpx;
      border-radius: 30rpx;
      font-size: 28rpx;
    }
    .confirm {
      background-color: rgb(91, 150, 91);
      border: 2rpx solid rgb(91, 150, 91);
      color: #fff;
    }
  }
}
</style>
