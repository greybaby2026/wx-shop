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
        title="登录"
        :title-bold="true"
        :is-fixed="false"
        :border-bottom="false"
        :background="{ background: 'rgba(256,256, 256,0)' }"
      ></u-navbar>
    </u-sticky>
    <!-- #endif -->

    <view class="login">
      <view class="login-box">
        <image class="logo" :src="appConfig.logo" mode="heightFix"></image>
        <block v-if="!phoneLogin">
          <!-- 一键登录Start -->
          <!-- #ifdef MP-WEIXIN -->
          <button
            class="login-btn white login-btn-user"
            v-if="isMnpWxAuth"
            @tap="mnpLogin"
          >
            用户一键登录
          </button>
          <!-- #endif -->
          <!-- #ifdef H5 -->
          <button
            class="login-btn white login-btn-user"
            v-if="isOaWxAuth && isWeixin"
            @tap="getCodeUrl"
          >
            用户一键登录
          </button>
          <!-- #endif -->
          <!-- #ifdef APP-PLUS -->
          <button
            class="login-btn white login-btn-user"
            v-if="isAPPWxAuth"
            @tap="appLogin"
          >
            用户一键登录
          </button>
          <!-- #endif -->
          <!-- 字节登录 -->
          <!-- #ifdef MP-TOUTIAO -->
          <button
            class="login-btn white login-btn-user"
            v-if="isToutiaoAuth"
            @tap="ttLogin"
          >
            用户一键登录
          </button>
          <!-- #endif  -->
          <!-- 一键登录End -->
          <button
            class="login-btn white login-btn-mobile"
            @click="phoneLogin = !phoneLogin"
          >
            手机号登录
          </button>
        </block>
      </view>
      <block v-if="phoneLogin">
        <view class="input m-t-40">
          <u-field
            label-width="0"
            v-model="account"
            placeholder="请输入手机号"
            :border-bottom="false"
            :style="{ width: '100%' }"
            type="number"
          />
        </view>
        <view class="input m-t-40" v-if="loginType == 1">
          <u-field
            label-width="0"
            v-model="password"
            type="password"
            :password-icon="false"
            placeholder="请输入密码"
            :border-bottom="false"
          >
            <view class="forget-btn p-l-20" slot="right">
              <router-link to="/pages/forget_pwd/forget_pwd">
                忘记密码?
              </router-link>
            </view>
          </u-field>
        </view>
        <view class="input m-t-40" v-if="loginType == 2">
          <u-field
            label-width="0"
            v-model="code"
            placeholder="请输入验证码"
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
                    disabled: codeTips == '获取验证码' && account.length !== 11,
                  }"
                  >{{ codeTips }}</view
                >
              </view>
            </view>
          </u-field>
        </view>
      </block>
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
              <view class="agreement"
                >《{{ AgreemenName || "服务协议" }}》</view
              >
            </router-link>
            和
            <router-link to="/bundle/pages/server_explan/server_explan?type=2">
              <view class="agreement"
                >《{{ AgreemenUserName || "隐私协议" }}》</view
              >
            </router-link>
          </view>
        </u-checkbox>
      </view>
      <!-- @click="showModel = true" -->
      <block v-if="phoneLogin">
        <button
          class="login-btn white login-btn-user"
          @tap="loginFun"
          :class="{ disabled: isDisabled }"
        >
          登录
        </button>
        <view class="m-t-40 flex row-between">
          <view v-show="appConfig.login_way.length != 1">
            <span> 已有账号，使用</span>
            <span
              class="agreement"
              @tap="changeLoginType"
              v-if="hasLoginWay(2) && loginType == 2"
              >密码登录</span
            >
            <span
              class="agreement"
              @tap="changeLoginType"
              v-if="hasLoginWay(1) && loginType == 1"
              >验证码登录</span
            >
          </view>
          <view
            :style="{
              'margin-left': 'auto',
            }"
          >
            <router-link to="/pages/register/register" v-if="isPhoneRegister"
              >注册账号
            </router-link>
          </view>
        </view>
      </block>
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
            <view class="agreement">《{{ AgreemenName || "服务协议" }}》</view>
          </router-link>
          和
          <router-link to="/bundle/pages/server_explan/server_explan?type=2">
            <view class="agreement"
              >《{{ AgreemenUserName || "隐私协议" }}》</view
            >
          </router-link>
        </view>
      </view>
    </u-modal>
    <!-- #ifdef MP-WEIXIN -->
    <mplogin-popup
      v-model="showLoginPop"
      :logo="appConfig.logo"
      :title="appConfig.shop_name"
      :login-data="loginData"
      @close="closePopup"
      @update="handleSubmitInfo"
    />
    <!--  #endif -->
  </view>
</template>

<script>
import { mapMutations, mapActions, mapGetters } from "vuex";
import {
  apiAuthLogin,
  apiAccountLogin,
  apiLoginCaptcha,
  apiToutiaoAuthLogin,
  apiAppWxLogin,
  apiUpdateUser,
  apiCheckMobile,
} from "@/api/app";
import wechath5 from "@/utils/wechath5";
import { isWeixinClient, currentPage, trottle } from "@/utils/tools";
import Cache from "@/utils/cache";
import { BACK_URL, INVITE_CODE } from "@/config/cachekey";
import { getCode, getUserProfile } from "@/utils/login";
import { apiPolicyAgreement } from "@/api/app";

const loginType = {
  ACCOUNT: 1,
  SMS_CODE: 2,
};
export default {
  data() {
    return {
      password: "",
      account: "",
      code: "",
      loginType: "",
      isWeixin: "",
      codeTips: "",
      showLoginPop: false,
      loginData: {},
      isAgree: false,
      showModel: false,
      phoneLogin: false,
      AgreemenName: "",
      AgreemenUserName: "",
    };
  },

  methods: {
    ...mapMutations(["login"]),
    ...mapActions(["getUser"]),
    // 获取服务协议
    getPolicyAgreement() {
      apiPolicyAgreement({
        type: 1,
      }).then((res) => {
        this.AgreemenName = res.name;
      });
      apiPolicyAgreement({
        type: 2,
      }).then((res) => {
        this.AgreemenUserName = res.name;
      });
    },
    // 小程序微信登录
    async mnpLogin() {
      if (!this.isAgree) {
        this.showModel = true;

        return;
      }
      const {
        userInfo: { avatarUrl, nickName, gender },
      } = await getUserProfile();
      uni.showLoading({
        title: "登录中...",
        mask: true,
      });
      const wxCode = await getCode();
      const data = await apiAuthLogin({
        code: wxCode,
        nickname: nickName,
        headimgurl: avatarUrl,
      });
      if (data.is_new_user) {
        uni.hideLoading();
        this.showLoginPop = true;
        this.loginData = data;
      } else {
        this.loginHandle(data);
      }
      // this.loginHandle(data)
    },
    getCodeUrl() {
      if (!this.isAgree) {
        // this.$toast({ title: "请阅读并同意《服务协议》《隐私协议》" });
        this.showModel = true;

        return;
      }
      wechath5.getWxUrl();
    },
    // 公众号微信登录
    async oaLogin(code) {
      if (!code) return;
      const data = await wechath5.authLogin(code);
      this.loginHandle(data);
    },
    oaAutoLogin() {
      // H5微信登录
      const { code } = this.$Route.query;
      this.isWeixin = isWeixinClient();
      if (this.isLogin) {
        // 已经登录 => 首页
        uni.switchTab({
          url: "/pages/index/index",
        });
        return;
      }
      if (code) {
        // 带有code => 登录
        return this.oaLogin(code);
      }
      if (this.isWeixin && this.isOaWxAutoLogin) {
        // 开启自动授权登录
        // this.getCodeUrl();
        wechath5.getWxUrl();
      }
    },
    // app微信登录
    async appLogin() {
      if (!this.isAgree) {
        // this.$toast({ title: "请阅读并同意《服务协议》《隐私协议》" });
        this.showModel = true;

        return;
      }
      uni.login({
        provider: "weixin",
        success: (res) => {
          uni.showLoading({
            title: "登录中...",
            mask: true,
          });
          const { openid, access_token } = res.authResult;
          apiAppWxLogin({
            openid,
            access_token,
          })
            .then((res) => {
              this.loginHandle(res);
            })
            .finally((res) => {
              uni.hideLoading();
            });
        },
      });
    },
    // 账号登录
    async loginFun() {
      const { account, password, telephone, code, loginType } = this;
      if (!account) {
        this.$toast({
          title: "请输入手机号",
        });
        return;
      }

      if (this.loginType == 1 && !password) {
        this.$toast({
          title: "请输入密码",
        });
        return;
      }
      if (this.loginType == 2 && !code) {
        this.$toast({
          title: "请输入验证码",
        });
        return;
      }
      if (!this.isAgree) {
        this.showModel = true;
        return;
      }

      apiAccountLogin({
        scene: loginType,
        account,
        password: loginType == 1 ? password : "",
        code: loginType == 2 ? code : "",
      }).then((res) => {
        this.loginHandle(res);
      });
    },
    // 登录结果处理
    async loginHandle(data) {
      this.login(data);
      this.getUser();
      // 更新购物车
      this.$store.dispatch("getCartNum");
      uni.hideLoading();
      if (this.isBindMobile && !data.mobile) {
        return this.$Router.replace("/pages/bind_mobile/bind_mobile");
      }
      this.goBack();
    },
    goBack() {
      if (getCurrentPages().length > 1) {
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
      Cache.remove(BACK_URL);
    },
    changeLoginType() {
      if (this.loginType == loginType.ACCOUNT) {
        this.loginType = loginType.SMS_CODE;
      } else if (this.loginType == loginType.SMS_CODE) {
        this.loginType = loginType.ACCOUNT;
      }
    },
    // 发送验证码
    async sendSMS() {
      if (!this.$refs.uCode.canGetCode) return;
      if (!this.account) {
        this.$toast({
          title: "请输入手机号",
        });
        return;
      }
      try {
        /**
         * @descriptionp 检测手机号是否已经注册
         */
        const { has } = await apiCheckMobile({ mobile: this.account });
        if (has === 0) {
          return this.$toast({
            title: "手机号未注册",
          });
        }
        apiLoginCaptcha({
          mobile: this.account,
        })
          .then((res) => {
            this.$refs.uCode.start();
          })
          .catch((err) => {
            console.log(err);
          });
      } catch (error) {
        console.log(error);
      }
    },

    codeChange(tip) {
      this.codeTips = tip;
    },
    // 头条小程序登录
    async ttLogin() {
      const code = await getCode();
      const [err, res] = await uni.getUserInfo();
      if (!res) {
        this.openSetting();
        return;
      }
      const { avatarUrl, nickName } = res.userInfo;
      uni.showLoading({
        title: "登录中...",
      });

      apiToutiaoAuthLogin({
        code,
        avatarUrl,
        nickName,
      })
        .then((res) => {
          this.loginHandle(res);
        })
        .catch((res) => {
          console.log(res);
          uni.hideLoading();
        });
    },
    openSetting() {
      uni.showModal({
        title: "温馨提示",
        content: "您已拒绝授权，是否前往设置打开个人信息授权？",
        confirmColor: this.themeColor,
        success(res) {
          if (res.confirm) {
            uni.openSetting();
          }
        },
      });
    },
    async handleSubmitInfo(e) {
      const loginData = this.loginData;
      const res = await apiUpdateUser(e, loginData.token);
      this.loginHandle(loginData);
    },
    closePopup() {
      this.showLoginPop = false;
    },
  },
  computed: {
    // 开启手机号注册
    isPhoneRegister() {
      const { register_way } = this.appConfig;
      if (!register_way) {
        return false;
      }
      return register_way.some((item) => item == 1);
    },
    // 开启强制绑定手机号
    isBindMobile() {
      const { coerce_mobile } = this.appConfig;
      return coerce_mobile;
    },
    // 登录方式数组
    loginWay() {
      const { login_way } = this.appConfig;
      if (!login_way) return false;
      this.loginType = login_way[0] || "";
      return login_way;
    },
    // 是否包含对应的登录方式
    hasLoginWay() {
      return (type) => {
        return this.loginWay && this.loginWay.some((item) => item == type);
      };
    },
    // 开启小程序微信授权
    isMnpWxAuth() {
      const { mnp_wechat_auth } = this.appConfig;
      return mnp_wechat_auth;
    },

    // 开启小程序微信授权
    isOaWxAuth() {
      const { h5_wechat_auth } = this.appConfig;
      return h5_wechat_auth;
    },
    // 开启app微信授权
    isAPPWxAuth() {
      const { app_wechat_auth } = this.appConfig;
      return app_wechat_auth;
    },
    // 开启公众号微信自动登录
    isOaWxAutoLogin() {
      const { h5_auto_wechat_auth } = this.appConfig;
      return h5_auto_wechat_auth;
    },
    // 开启小程序头条登录
    isToutiaoAuth() {
      const { toutiao_auth } = this.appConfig;
      return toutiao_auth;
    },
    //登录按钮禁用样式
    isDisabled() {
      if (this.account.length == 11 && this.password && this.loginType == 1) {
        return false;
      } else if (
        this.account.length == 11 &&
        this.code &&
        this.loginType == 2
      ) {
        return false;
      } else {
        return true;
      }
    },
  },
  onLoad() {
    this.getPolicyAgreement();
    this.mnpLogin = trottle(this.mnpLogin);
    this.appLogin = trottle(this.appLogin);
    this.getCodeUrl = trottle(this.getCodeUrl);
    this.ttLogin = trottle(this.ttLogin);
    // #ifdef H5
    this.oaAutoLogin();
    // #endif
  },
  onUnload() {},
};
</script>
<style lang="scss">
page {
  background-color: #fff;
  height: 100%;
  padding-bottom: 0;
}

.login {
  padding: 60rpx;

  padding-top: 80rpx;
  &-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
  .logo {
    height: 260rpx;
    width: 260rpx;
    border-radius: 20rpx;
    margin-bottom: 40rpx;
  }
  .login-btn {
    margin-top: 40rpx;
    width: 100%;
    height: 100rpx;
    line-height: 100rpx;
    font-size: 32rpx;
    border-radius: 12rpx;
    &-user {
      @include background_color();
    }
    &-mobile {
      background: white;
      color: black;
      border: 1px solid #bbb;
    }
  }
  .input {
    border: 1px solid #d7d7d7;
    height: 100rpx;
    border-radius: 12rpx;
    display: flex;
    align-items: center;
  }
  .forget-btn {
    border-left: $-solid-border;
  }
  .sms-btn {
    text-align: center;
    border-left: $-solid-border;
    @include font_color();
  }
}
.comfirm-box {
  text-align: center;
  padding: 60rpx 0 70rpx 0;
}
.agreement {
  @include font_color;
}
.disabled {
  opacity: 0.5;
}
</style>
