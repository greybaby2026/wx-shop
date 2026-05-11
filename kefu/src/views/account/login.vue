<template>
  <div class="login">
    <div class="main">
      <div class="login-container">
        <div class="title">客服登录</div>
        <div class="form m-t-40">
          <el-form :model="accountObj" :rules="rules" ref="form">
            <el-form-item prop="account">
              <el-input
                placeholder="请输入账号"
                v-model="accountObj.account"
                @keyup.enter.native="$refs.inputPwd.focus()"
              >
                <i slot="prefix" class="el-input__icon el-icon-s-custom"></i>
              </el-input>
            </el-form-item>
            <el-form-item prop="password">
              <el-input
                ref="inputPwd"
                placeholder="请输入密码"
                v-model="accountObj.password"
                show-password
                @keyup.enter.native="handleLogin"
              >
                <i
                  slot="prefix"
                  class="el-input__icon el-icon-s-cooperation"
                ></i>
              </el-input>
            </el-form-item>
            <div class="m-b-10 p-t-10">
              <el-checkbox
                v-model="rememberAccount"
                label="记住账号"
              ></el-checkbox>
            </div>
            <el-button
              style="width: 100%"
              type="primary"
              :loading="loadingLogin"
              @click="handleLogin"
              >登录</el-button
            >
          </el-form>
        </div>
      </div>
    </div>
    <div class="footer">{{ copyright }}</div>
  </div>
</template>

<script>
import cache from "@/utils/cache";
import { apiLogin } from "@/api/app";
import { mapGetters } from "vuex";
export default {
  data() {
    return {
      rememberAccount: false,
      loadingLogin: false,
      accountObj: {
        account: "",
        password: "",
      },
      rules: {
        account: [
          {
            required: true,
            message: "请输入账号",
            trigger: ["blur"],
          },
        ],
        password: [
          {
            required: true,
            message: "请输入密码",
            trigger: ["blur"],
          },
        ],
      },
    };
  },
  methods: {
    handleLogin() {
      this.$refs.form.validate((valid) => {
        if (!valid) return;
        cache.set("remember_account", {
          remember: this.rememberAccount,
          account: this.accountObj.account,
        });
        this.login();
      });
    },
    //登录
    login() {
      this.loadingLogin = true;
      const { account, password } = this.accountObj;

      this.$store
        .dispatch("login", {
          account,
          password,
        })
        .then((data) => {
          const {
            query: { redirect },
          } = this.$route;
          const path = typeof redirect === "string" ? redirect : "/";
          this.$router.replace(path);
        })
        .catch(() => {
          this.loadingLogin = false;
        });
    },
  },
  computed: {
    ...mapGetters(["copyright"]),
  },
  created() {
    const value = cache.get("remember_account");
    if (value.remember) {
      this.rememberAccount = value.remember;
      this.accountObj.account = value.account;
    }
  },
};
</script>

<style lang="scss" scope>
.login {
  display: flex;
  flex-direction: column;
  height: 100vh;
  .main {
    flex: 1;
    background-image: url(../../assets/images/login_bg.png);
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    display: flex;
    justify-content: center;
    align-items: center;
    .login-container {
      width: 400px;
      border-radius: 10px;
      background: #fff;
      box-shadow: 0px 0px 10px rgba(64, 115, 250, 0.08);
      padding: 30px 40px 80px;
      .title {
        text-align: center;
        font-size: 24px;
        font-weight: 500;
      }
    }
  }
  .footer {
    font-size: 12px;
    color: #9999;
    text-align: center;
    padding: 20px 0;
  }
}
</style>
