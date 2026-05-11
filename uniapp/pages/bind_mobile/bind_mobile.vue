<template>
  <view class="pages" :class="themeName">
    <view class="bind-mobile">
      <!-- #ifndef MP-WEIXIN -->
      <view>
        <view class="input-wrap">
          <view class="input">
            <u-field
              label-width="160"
              label="手机号"
              v-model="mobile"
              placeholder="请输入手机号"
              :input-border="false"
            />
          </view>
          <view class="input">
            <u-field
              label-width="160"
              label="短信验证码"
              v-model="code"
              placeholder="请输入验证码"
              :input-border="false"
            >
              <view slot="right">
                <view class="sms-btn" @tap="sendSMS">
                  <!-- 获取验证码 -->
                  <u-verification-code
                    unique-key="login"
                    ref="uCode"
                    @change="codeChange"
                  >
                  </u-verification-code>
                  <view class="xs">{{ codeTips }}</view>
                </view>
              </view>
            </u-field>
          </view>
        </view>
        <button class="confirm-btn white br60" size="lg" @tap="bindMobile">
          确定
        </button>
      </view>
      <!-- #endif -->
      <!-- #ifdef MP-WEIXIN -->
      <view class="mnp-bind">
        <view class="avatar">
          <open-data type="userAvatarUrl"></open-data>
        </view>
        <view class="mt20 bold">授权手机号</view>
        <button
          open-type="getPhoneNumber"
          class="bind-btn white br60"
          size="lg"
          @getphonenumber="getPhoneNumber"
        >
          一键绑定手机号
        </button>
      </view>
      <!-- #endif -->
    </view>
  </view>
</template>

<script>
import {
  apiMnpBindMobile,
  apiBindMobileCaptcha,
  apiBindMobile,
} from "@/api/user";
import { mapGetters, mapMutations } from "vuex";
import { getCode } from "@/utils/login";
import Cache from "@/utils/cache";
import { BACK_URL } from "@/config/cachekey";
export default {
  data() {
    return {
      mobile: "",
      code: "",
      codeTips: "",
      wxCode: "",
      isBind: false,
    };
  },
  methods: {
    ...mapMutations(["logout"]),
    codeChange(tip) {
      this.codeTips = tip;
    },
    bindMobile() {
      let { mobile, code } = this;
      if (!mobile) {
        this.$toast({
          title: "请输入手机号",
        });
        return;
      }
      if (!code) {
        this.$toast({
          title: "请输入验证码",
        });
        return;
      }
      apiBindMobile({
        mobile,
        code,
      }).then((res) => {
        this.bindSuccess();
      });
    },
    sendSMS() {
      if (!this.$refs.uCode.canGetCode) return;
      if (!this.mobile) {
        this.$toast({
          title: "请输入手机号",
        });
        return;
      }
      apiBindMobileCaptcha({
        mobile: this.mobile,
      }).then((res) => {
        this.$refs.uCode.start();
      });
    },
    // #ifdef MP-WEIXIN
    async getPhoneNumber(e) {
      const { code, errMsg } = e.detail;
      if (errMsg !== "getPhoneNumber:ok")
        return this.$toast({
          title: "绑定失败",
        });
      const data = await apiMnpBindMobile({ code });
      this.bindSuccess();
    },
    // #endif
    bindSuccess() {
      this.isBind = true;
      const { type } = this.$Route.query;
      setTimeout(() => {
        const pageLen = getCurrentPages().length;
        if (pageLen > 1) {
          this.$Router.back(1, {
            success: () => {
              const { onLoad, options } = currentPage();
              // 刷新上一个页面
              onLoad && onLoad(options);
            },
          });
        } else if (Cache.get(BACK_URL)) {
          this.$Router.replace(Cache.get(BACK_URL));
        } else {
          this.$Router.replaceAll("/pages/index/index");
        }
      }, 1500);
    },
  },
  computed: {
    ...mapGetters(["appConfig"]),
  },
  onUnload() {
    if (!this.isBind) {
      this.logout();
    }
  },
};
</script>

<style lang="scss">
page {
  background: #ffffff;
  padding: 0;
}

.bind-mobile {
  padding: 20rpx 30rpx;
  text-align: center;

  .avatar {
    margin: 200rpx 0 60rpx;
    display: inline-block;
    width: 120rpx;
    height: 120rpx;
    border-radius: 50%;
    border: 1px solid #eee;
    overflow: hidden;
  }

  .input-wrap {
    .input {
      padding-top: 20rpx;
    }
  }

  .sms-btn {
    text-align: center;
    border: 1px solid;
    width: 176rpx;
    height: 60rpx;
    line-height: 60rpx;
    border-radius: 60rpx;
    @include font_color();
    @include border_color();
  }

  .confirm-btn {
    margin-top: 80rpx;
    @include background_color();
  }

  .bind-btn {
    margin-top: 80rpx;
    background: #09bb07;
  }
}
</style>
