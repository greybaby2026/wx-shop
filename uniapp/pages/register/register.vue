<template>
  <view
    class="pages"
    :class="themeName"
    :style="
      'background-image: url(' +
      $getImageUri('resource/image/shopapi/default/login_bg.png') +
      ')'
    "
  >
    <!-- #ifndef  H5 -->

    <u-sticky offset-top="0" h5-nav-height="0" bg-color="transparent">
      <u-navbar
        :is-back="true"
        title="注册"
        :title-bold="true"
        :is-fixed="false"
        :border-bottom="false"
        :background="{ background: 'rgba(256,256, 256,0)' }"
      ></u-navbar>
    </u-sticky>
    <!-- #endif -->
    <view class="register">
      <view class="register-text">注册新账号</view>
      <view class="input m-t-40">
        <u-field
          label-width="0"
          v-model="register.mobile"
          placeholder="输入手机号"
          :border-bottom="false"
          :style="{ width: '100%' }"
          type="number"
        />
      </view>
      <view class="input m-t-40" v-if="isRegisterCode">
        <u-field
          label-width="0"
          v-model="register.code"
          placeholder="输入验证码"
          :border-bottom="false"
          type="number"
        >
          <view slot="right">
            <view class="sms-btn p-l-20" @tap="sendSMS">
              <!-- 获取验证码 -->
              <u-verification-code
                unique-key="login"
                ref="uCode"
                @change="codeChange"
              >
              </u-verification-code>
              <view
                class="xs"
                :class="{
                  disabled:
                    codeTips == '获取验证码' && register.mobile.length !== 11,
                }"
                >{{ codeTips }}</view
              >
            </view>
          </view>
        </u-field>
      </view>
      <view class="input m-t-40">
        <u-field
          label-width="0"
          v-model="register.password"
          :password="!pwdShow"
          type="text"
          :password-icon="false"
          placeholder="输入密码"
          :border-bottom="false"
          :style="{ width: '100%' }"
        >
          <view slot="right">
            <u-icon
              name="eye"
              @click="pwdShow = !pwdShow"
              v-show="!pwdShow"
              size="36rpx"
            >
            </u-icon>
            <u-icon
              name="eye-off"
              @click="pwdShow = !pwdShow"
              v-show="pwdShow"
              size="36rpx"
            ></u-icon>
          </view>
        </u-field>
      </view>
      <view class="input m-t-40">
        <u-field
          label-width="0"
          v-model="register.password_confirm"
          :password="!comfirmPwdShow"
          type="text"
          :password-icon="false"
          placeholder="请再次输入密码"
          :border-bottom="false"
          :style="{ width: '100%' }"
        >
          <view slot="right">
            <u-icon
              name="eye"
              @click="comfirmPwdShow = !comfirmPwdShow"
              v-show="!comfirmPwdShow"
              size="36rpx"
            >
            </u-icon>
            <u-icon
              name="eye-off"
              @click="comfirmPwdShow = !comfirmPwdShow"
              v-show="comfirmPwdShow"
              size="36rpx"
            ></u-icon>
          </view>
        </u-field>
      </view>
      <view class="m-t-40">
        <u-checkbox
          v-model="isAgree"
          :active-color="themeColor"
          shape="circle"
          :label-disabled="true"
        >
          <view class="sm flex">
            已阅读并同意
            <router-link
              data-theme=""
              to="/bundle/pages/server_explan/server_explan?type=1"
            >
              <view class="agreement">《服务协议》</view>
            </router-link>
            和
            <router-link to="/bundle/pages/server_explan/server_explan?type=2">
              <view class="agreement">《隐私协议》</view>
            </router-link>
          </view>
        </u-checkbox>
      </view>
      <button
        class="btn m-t-40 white"
        :class="{ disabled: isDisabled }"
        @tap="registerFun"
      >
        注册
      </button>
    </view>
    <u-modal
      :value="showModel"
      show-cancel-button
      :show-title="false"
      @confirm="(isAgree = true), (showModel = false)"
      @cancel="showModel = false"
      :confirm-color="themeColor"
    >
      <view class="comfirm-box">
        <view> 请先阅读并同意 </view>
        <view class="flex row-center">
          <router-link
            data-theme=""
            to="/bundle/pages/server_explan/server_explan?type=1"
          >
            <view class="agreement">《服务协议》</view>
          </router-link>
          和
          <router-link to="/bundle/pages/server_explan/server_explan?type=2">
            <view class="agreement">《隐私协议》</view>
          </router-link>
        </view>
      </view>
    </u-modal>
  </view>
</template>

<script>
import {
  apiRegisterCaptcha,
  apiAccountRegister,
  apiCheckMobile,
} from "@/api/app";
import { mapGetters } from "vuex";
import { trottle } from "@/utils/tools";
export default {
  name: "register",
  data() {
    return {
      register: {
        mobile: "",
        code: "",
        password: "",
        password_confirm: "",
      },
      codeTips: "",
      isAgree: false, // 是否同意协议
      pwdShow: false,
      comfirmPwdShow: false,
      showModel: false,
    };
  },
  methods: {
    codeChange(tip) {
      this.codeTips = tip;
    },
    registerFun() {
      let { mobile, password, code, password_confirm } = this.register;
      if (!mobile) {
        this.$toast({
          title: "请输入手机号",
        });
        return;
      }
      if (!password) {
        this.$toast({
          title: "请输入密码",
        });
        return;
      }
      if (!password) {
        this.$toast({
          title: "请再次输入密码",
        });
        return;
      }
      if (password != password_confirm) {
        this.$toast({
          title: "两次密码输入不一致",
        });
        return;
      }
      if (!this.isAgree) {
        // this.$toast({ title: "请阅读并同意《服务协议》《隐私协议》" });
        this.showModel = true;
        return;
      }
      apiAccountRegister(this.register).then((res) => {
        setTimeout(() => {
          this.$Router.back(1);
        }, 1500);
      });
    },
    async sendSMS() {
      if (!this.$refs.uCode.canGetCode) return;
      if (!this.register.mobile) {
        this.$toast({
          title: "请输入手机号",
        });
        return;
      }
      /**
       * @descriptionp 检测手机号是否已经注册
       */
      const { has } = await apiCheckMobile({ mobile: this.register.mobile });
      if (has === 1) {
        return this.$toast({
          title: "手机号已被注册",
        });
      }
      apiRegisterCaptcha({
        mobile: this.register.mobile,
      })
        .then((res) => {
          this.$refs.uCode.start();
        })
        .catch((err) => {
          console.log(err);
        });
    },
  },
  computed: {
    isRegisterCode() {
      const { is_mobile_register_code } = this.appConfig;
      return is_mobile_register_code;
    },
    isDisabled() {
      //TODO
      if (this.isRegisterCode) {
        if (
          this.register.mobile.length == 11 &&
          this.register.code &&
          this.register.password &&
          this.register.password_confirm
        ) {
          return false;
        } else {
          return true;
        }
      } else {
        if (
          this.register.mobile.length == 11 &&
          this.register.password &&
          this.register.password_confirm
        ) {
          return false;
        } else {
          return true;
        }
      }
    },
  },
  onLoad() {
    this.registerFun = trottle(this.registerFun);
  },
};
</script>

<style lang="scss">
page {
  background-color: white;
}

.register {
  padding: 60rpx;
  &-text {
    font-size: 38rpx;
  }
  .input {
    border: 1px solid #d7d7d7;
    height: 100rpx;
    border-radius: 12rpx;
    display: flex;
    align-items: center;
  }

  .sms-btn {
    text-align: center;
    @include font_color();
    border-left: $-solid-border;
  }

  .agreement {
    @include font_color;
  }
  .btn {
    margin-top: 40rpx;
    width: 100%;
    height: 100rpx;
    line-height: 100rpx;
    font-size: 32rpx;
    border-radius: 12rpx;
    @include background_color();
  }
  .disabled {
    opacity: 0.5;
  }
}
.comfirm-box {
  text-align: center;
  padding: 60rpx 0 70rpx 0;
}
</style>
