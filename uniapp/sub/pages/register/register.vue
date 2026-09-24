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
      <!-- 所属门店（必选）：门店为加盟店，注册即绑定归属门店（首绑终身） -->
      <view class="m-t-30 store-cell flex row-between" @tap="chooseStore" v-if="storeList.length">
        <view class="sm">所属门店</view>
        <view class="flex row-center">
          <text class="sm" :class="storeName ? 'black' : 'muted'">
            {{ storeName || '请选择门店' }}
          </text>
          <u-icon class="m-l-10" name="arrow-right" size="26rpx" color="#999"></u-icon>
        </view>
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
import { apiSelffetchStore } from "@/api/store";
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
      // 所属门店（注册即绑定归属门店，首绑终身）
      storeList: [],
      storeId: "",
      storeName: "",
    };
  },
  onLoad() {
    // 拉取门店列表（该接口已开放免登录，供注册前选店）
    this.getStoreList();
    // 监听「选择门店」页回传的选择结果
    uni.$on("storeSelected", this.onStoreSelected);
  },
  onUnload() {
    uni.$off("storeSelected", this.onStoreSelected);
  },
  methods: {
    codeChange(tip) {
      this.codeTips = tip;
    },
    // 门店列表
    getStoreList() {
      apiSelffetchStore({ page_no: 1, page_size: 100 })
        .then((res) => {
          this.storeList = (res && res.lists) || [];
        })
        .catch(() => {
          this.storeList = [];
        });
    },
    // 打开「选择门店」页（选择模式，不绑定）
    chooseStore() {
      this.$Router.push({
        path: "/bundle/pages/store_bind/store_bind",
        query: { mode: "select" },
      });
    },
    onStoreSelected(item) {
      if (!item) return;
      this.storeId = item.id;
      this.storeName = item.name;
    },
    registerFun() {
      let { mobile, password, code, password_confirm } = this.register;
      // 门店为加盟店：注册必须选择所属门店（无可用门店时不拦截，避免无法注册）
      if (this.storeList.length && !this.storeId) {
        this.$toast({ title: "请选择所属门店" });
        return;
      }
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
      const params = { ...this.register };
      // 注册即绑定归属门店（首绑终身；服务端会校验门店有效性）
      if (this.storeId) params.store_id = this.storeId;
      apiAccountRegister(params).then((res) => {
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

/* 所属门店选择（注册即绑定归属门店） */
.store-cell {
  padding: 24rpx 0;
  border-bottom: 1px solid #f2f2f2;
}
</style>
