<template>
  <view class="container">
    <u-sticky offset-top="0" h5-nav-height="0" bg-color="transparent">
      <u-navbar
        :is-back="true"
        :back-icon-color="'#fff'"
        :title="' '"
        :title-bold="true"
        :is-fixed="false"
        :border-bottom="false"
        :background="{ background: 'rgba(256,256, 256,0)' }"
        :title-color="'#fff'"
      ></u-navbar>
    </u-sticky>
    <image
      class="container-bg"
      mode="widthFix"
      :src="activity.background_image"
    ></image>
    <image
      class="top-img"
      v-if="activity.top_image"
      mode="widthFix"
      :src="activity.top_image"
    >
    </image>
    <image
      class="bottom-img"
      mode="widthFix"
      :src="
        $getImageUri(
          '/resource/image/adminapi/default/luck_draw_default_bg_bottom.png'
        )
      "
    >
    </image>
    <!-- Header -->
    <view style="position: relative">
      <!-- Section -->
      <view class="section">
        <view class="box-wrap">
          <!-- 中奖名单公示 -->
          <view class="notice flex" v-if="activity.show_winning_list != 0">
            <image
              class="m-l-18"
              src="../../static/images/choujinag_notice.png"
            ></image>
            <view style="width: 90%">
              <u-notice-bar
                bg-color="none"
                :volume-icon="false"
                mode="horizontal"
                :list="list"
              >
              </u-notice-bar>
            </view>
          </view>

          <!-- 转盘 -->
          <view class="turntable" v-if="activity.length != 0">
            <image
              class="turntable-container-bg"
              :src="activity.container_image"
            >
            </image>
            <view class="turntable-container">
              <my-turntable
                :ids="id"
                @finish="finish"
                :params="activity"
              ></my-turntable>
            </view>
          </view>
          <!-- 抽奖信息 -->
          <!-- <view class="message flex row-between nr" v-if="activity.length != 0">
            <view v-if="activity.frequency_type == 0">剩余次数:不限制</view>
            <view v-if="activity.frequency_type == 1"
              >剩余次数:{{ activity.surplus_draw_num }}</view
            >
            <template v-if="activity.show_winning_list">
              <view
                class="flex"
                @click="goTo('/bundle/pages/win_prize_code/win_prize_code?id=')"
              >
                <image
                  class="m-r-10"
                  src="../../static/images/choujiang_name_list.png"
                ></image>
                中奖名单
              </view>
            </template>
          </view> -->

          <!-- 次数提示 -->
          <!-- <view class="num-tips nr" v-if="activity.length != 0">
                    {{ activity.limit || '-' }}
                </view> -->

          <!-- 活动规则 -->
          <!-- <view class="rule" v-if="activity.length != 0">
                    <view class="lg bold">活动规则</view>
                    <text class="sm" style="color: #fcd7d2">{{ activity.rule || '-' }} </text>
                </view> -->

          <!-- <view
                    class="flex-1 flex row-center col-center"
                    v-if="activity.length == 0"
                    style="color: #fcd7d2; height: 61vh"
                >
                    活动已失效
                </view> -->
        </view>
      </view>
      <!-- <view class="section m-t-16">
        <view class="box-wrap prize-contain" style="padding: 0">
          <image
            :src="$getImageUri('/resource/image/shopapi/default/prize.png')"
            class="prize"
          ></image>
          <scroll-view
            :scroll-y="true"
            style="height: 700rpx; touch-action: none; margin-top: 80rpx"
            class=""
          >
            <view
              class="prize-item flex"
              v-for="item in activity.prizes_lists"
              :key="item.name"
            >
              <view>
                <image :src="item.image" class="img"></image>
              </view>

              <view
                class="m-l-10 flex-col row-around"
                style="height: 150rpx; width: 100%"
              >
                <view class="flex">
                  <view> {{ item.name }}-{{ item.type_desc }} </view>
                  <view style="margin-left: auto; flex-shrink: 0">
                    共{{ item.num }}个
                  </view>
                </view>
                <view> {{ item.type_value_desc }} </view>
              </view>
            </view>
          </scroll-view>
        </view>
      </view> -->
    </view>
    <view class="ctrl-box" v-if="activity.length !== 0">
      <view class="ctrl-items" @click="showRules = true">
        <image
          class="ctrl-items-icon"
          :src="
            $getImageUri(
              '/resource/image/shopapi/default/luck_draw_btn_icon_reward.png'
            )
          "
        ></image
        >抽奖规则
      </view>
      <view
        class="ctrl-items"
        @click="goTo('/bundle/pages/luck_draw_code/luck_draw_code?id=')"
      >
        <image
          class="ctrl-items-icon"
          :src="
            $getImageUri(
              '/resource/image/shopapi/default/luck_draw_btn_icon_gift.png'
            )
          "
        ></image>
        我的奖品
      </view>
    </view>
    <!-- Footer -->
    <!-- <view class="footer"> 本活动的所有奖品，均由商城提供 </view> -->
    <u-popup
      v-model="showRules"
      mode="bottom"
      safe-area-inset-bottom
      border-radius="14"
    >
      <view class="p-20">
        <view style="text-align: center" class="p-20"> 活动规则 </view>
        <scroll-view style="height: 300px" scroll-y="true">
          {{ activity.describe || "-" }}
        </scroll-view>
      </view>
    </u-popup>
    <!-- Popup -->
    <u-popup v-model="showResult" mode="center">
      <view class="result-popup flex-col">
        <view class="result-container flex-col row-around col-center p-20">
          <view
            class="no-reward-box"
            style="color: #333333; font-weight: 500"
            v-if="result.type == 0"
          >
            <image
              class="no-reward-box-goods-img"
              src="https://php-b2cplus.yixiangonline.com/uploads/images/20250730/202507300023548c8475901.png"
            ></image>
            <view class="no-reward-box-title"> 差一点就中奖了 </view>
          </view>
          <view class="reward-box" v-else>
            <view
              class="reward-box-title"
              style="color: #fe6129; font-weight: 500"
            >
              恭喜你获得
            </view>
            <view class="reward-box-goods">
              <image
                class="reward-box-goods-img"
                :src="
                  $getImageUri(
                    '/resource/image/shopapi/default/luck_draw_reward_icon.png'
                  )
                "
              ></image>
              <view class="reward-box-goods-text">
                <view class="reward-box-goods-text-1">
                  {{ result.name }}
                </view>
              </view>
            </view>
            <view
              class="btn"
              @click="
                result.type == 0
                  ? (showResult = false)
                  : goTo('/bundle/pages/luck_draw_code/luck_draw_code?id=')
              "
            >
              开心收下
            </view>
          </view>
          <view style="margin-top: 20rpx" @click="showResult = false">
            <u-icon name="close-circle" size="62" color="#fff"></u-icon>
          </view>
        </view>
      </view>
    </u-popup>

    <!-- 页面状态 -->
    <page-status :status="pageStatus">
      <template #error>
        <u-empty
          :text="pageErrorMsg"
          src="/static/images/empty/order.png"
          :icon-size="280"
        />
      </template>
    </page-status>
  </view>
</template>

<script>
import {
  apiLuckyDrawActivity,
  apiLuckyDrawWinningList,
} from "@/api/luck_draw.js";
import wechath5 from "@/utils/wechath5";
import myTurntable from "@/bundle/components/my-turntable/my-turntable.vue";
export default {
  components: {
    myTurntable,
  },
  data() {
    return {
      id: "",
      // 中奖名单的轮播
      list: [],
      // 活动的信息
      activity: {
        background_image: "",
      },
      // 抽奖结果
      resultText: "",
      // 弹窗控制
      showResult: false,
      // 页面的状态
      pageStatus: "normal",
      pageErrorMsg: "",
      showRules: false,
      result: {
        name: "",
        image: "",
        type: 0,
      },
    };
  },

  onLoad() {
    try {
      const id = this.$Route.query.id;
      this.id = id;
      console.log(id);
    } catch (e) {
      console.log(e);
      //TODO handle the exception
    }
  },

  onShow() {
    // 活动信息
    this.getLuckyDrawActivityFunc();
    // 中奖名单
    this.getLuckyDrawListFunc();
  },
  methods: {
    // 活动信息
    async getLuckyDrawActivityFunc() {
      apiLuckyDrawActivity({
        id: this.id,
      })
        .then((res) => {
          this.activity = res;
          // #ifdef H5
          const href = window.location.origin;
          const pathname = window.location.pathname;
          console.log("分享信息", {
            shareTitle: this.activity.name,
            shareImage: this.activity.share_image,
            shareLink: `${href}${pathname}?id=${this.id}&invite_code=${this.$Route.query.name}`,
            shareDesc: this.activity.share_describe || "",
          });
          wechath5.share({
            shareTitle: this.activity.name,
            shareImage: this.activity.share_image,
            shareLink: `${href}${pathname}?id=${this.id}&invite_code=${this.$Route.query.name}`,
            shareDesc: this.activity.share_describe || "",
          });
          //#endif
        })
        .catch((err) => {
          console.log(err);
          this.pageErrorMsg = err == "请求参数缺token" ? "请先登录" : err;
          this.pageStatus = "error";
        });
    },

    // 中奖名单
    async getLuckyDrawListFunc() {
      const res = await apiLuckyDrawWinningList({
        id: this.id,
        page_no: 1,
        page_size: 10,
      });
      this.list = res.lists.map((item) => item.title);
    },

    // 确认收下
    finish(e) {
      this.showResult = true;
      console.log(e);
      this.resultText = e.detail;
      this.activity.prizes.forEach((item) => {
        if (item.location == e.location) {
          this.result.name = item.name;
          this.result.image = item.image;
          this.result.type = item.type;
        }
      });
    },

    goTo(url, id) {
      uni.navigateTo({
        url: url + this.id,
      });
    },

    onShareAppMessage() {
      return {
        title: this.activity.name,
        path: `/bundle/pages/luck_draw/luck_draw?id=${this.id}&invite_code=${this.$Route.query.name}`,
        imageUrl: this.activity.share_image,
      };
    },

    //分享朋友圈
    onShareTimeline() {
      return {
        title: this.activity.name,
        path: `/bundle/pages/luck_draw/luck_draw?id=${this.id}&invite_code=${this.$Route.query.name}`,
        imageUrl: this.activity.share_image,
      };
    },
  },
};
</script>

<style lang="scss">
page {
  background-color: #f3f3f3;
  padding-bottom: 0;
}

.container {
  width: 100%;
  // padding-top: 300rpx;
  // padding-bottom: 50rpx;

  background-size: 100% auto !important;
  background-repeat: no-repeat;
  position: relative;
  min-height: 100vh;
  overflow: hidden;

  .container-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    max-height: 100%;
    z-index: 0;
  }

  .header {
    .choujiang-btn1 {
      padding-right: 20rpx;
    }
    .icon-luck-draw-btn {
      width: 120rpx;
      height: 120rpx;
    }
  }
  .top-img {
    position: relative;
    width: 100%;
    max-height: 500rpx;
    z-index: 1;
  }
  .bottom-img {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    z-index: 0;
  }
  .rule {
    position: absolute;
    top: 77%;
    right: 0rpx;
  }
  .prize {
    position: absolute;
    top: 85%;
    right: 0rpx;
  }

  .section {
    // padding: 0 30rpx;

    .box-wrap {
      // width: 690rpx;
      padding: 0 28rpx;
      border-radius: 30rpx;
      // background: #ed3720;
      // border: 12rpx solid #fe6847;

      // 中奖名单公示
      .notice {
        margin: 0 24rpx;
        height: 58rpx;
        padding: 12rpx 0;
        border-radius: 29rpx;
        background: #d30c16;
        border: 2rpx solid #edb17d;
        margin-bottom: 24rpx;

        image {
          width: 32rpx;
          height: 32rpx;
        }
      }

      // 中奖信息奖品等
      .message {
        margin: 30rpx 10rpx 0 10rpx;
        color: #fef0b5;

        image {
          width: 28rpx;
          height: 30rpx;
        }
      }

      // 转盘
      .turntable {
        // width: 356px;
        // height: 356px;
        overflow: hidden;
        border-radius: 20rpx;
        padding: 64rpx;
        position: relative;
        margin: 0 auto;

        background-size: 100% auto !important;
        .turntable-container-bg {
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
        }
        .turntable-container {
          position: relative;
          z-index: 1;
          margin-left: 10rpx;
        }
      }

      // 每日抽奖次数提示
      .num-tips {
        color: #fbccc7;
        margin: 16rpx 0;
        text-align: center;
      }

      // 活动规则
      .rule {
        color: #fcd7d2;
        font-size: $-font-size-sm;
        padding: 30rpx 16rpx 30rpx 28rpx;
        border-radius: 20rpx;
        background: #d30c16;
      }
    }
  }

  // Footer
  .footer {
    color: #fff2d9;
    padding: 30rpx 0;
    text-align: center;
    font-size: $-font-size-xs;
  }

  // 弹窗
  .result-popup {
    text-align: center;

    border-radius: 20rpx;
    .result-container {
      width: 720rpx;
      height: 800rpx;
      background: url(https://php-b2cplus.yixiangonline.com/uploads/images/20250729/20250729233300ab0a67341.png)
        no-repeat;
      background-size: 100% auto;
      border-radius: 20rpx;
      .img {
        width: 150rpx;
        height: 150rpx;
        border-radius: 20rpx;
      }
      .btn {
        color: white;
        background-color: red;
        border-radius: 50rpx;
        width: 336rpx;
        padding: 20rpx 0;
        margin: 70rpx auto 0;
        border: 4rpx solid #fff;
      }
    }
    .reward-box {
      width: 100%;
      padding-top: 320rpx;
      .reward-box-goods {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20rpx 100rpx;
        overflow: hidden;
        .reward-box-goods-img {
          width: 116rpx;
          height: 116rpx;
          margin-right: 36rpx;
        }
        .reward-box-goods-text {
          font-weight: bold;
          flex: 1;
          overflow: hidden;
        }
        .reward-box-goods-text-1 {
          font-weight: bold;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
        }
      }
    }
    .no-reward-box {
      width: 100%;
      padding-top: 280rpx;
      .no-reward-box-goods-img {
        width: 116rpx;
        height: 116rpx;
        margin-bottom: 36rpx;
      }
    }
  }

  // 按钮
  .get-btn {
    width: 320rpx;
    height: 70rpx;
    color: #7b3200;
    background: linear-gradient(180deg, #fef0b0 0%, #ffa92e 100%);
  }
  .prize-contain {
    position: relative;
    .prize {
      position: absolute;
      width: 250rpx;
      height: 92rpx;
      top: -15rpx;
      left: 50%;
      margin-left: -125rpx;
    }
    .prize-item {
      // border: 12rpx solid #fe6847;
      // background-color: white;
      margin-top: 10rpx;
      padding: 20rpx;
      .img {
        width: 120rpx;
        height: 120rpx;
        border-radius: 20rpx;
      }
      border-bottom: 1rpx solid #f7f7f72e;
      color: white;
    }
  }
  .ctrl-box {
    display: flex;
    justify-content: center;
    align-items: center;
    color: #fff;
    margin-top: 36rpx;
    position: relative;
    z-index: 0;
    padding-bottom: 100rpx;
    .ctrl-items {
      display: flex;
      width: 128px;
      height: 34px;
      padding: 6px 5px;
      justify-content: center;
      align-items: center;
      gap: 2px;
      border-radius: 16px;
      border: 1px solid #ffffff66;
      background: #ffffff4d;
      box-shadow: 0 2px 6px 0 #c4000366;
      backdrop-filter: blur(10px);
      margin: 0 36rpx;
      > .ctrl-items-icon {
        width: 20px;
        height: 20px;
        margin-right: 4px;
      }
    }
  }
}
</style>
