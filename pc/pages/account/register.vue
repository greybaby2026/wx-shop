<template>
    <div class="register-container flex-col row-center col-center">
        <div class="register-box flex-col col-center bg-white">
            <div class="register-title">注册账号</div>
            <el-form class="form-box flex-col">
                <div class="register-form-item">
                    <el-input
                        class="form-input"
                        v-model="telephone"
                        placeholder="请输入手机号码"
                    >
                        <i
                            class="el-icon-user"
                            style="font-size: 18px"
                            slot="prepend"
                        />
                    </el-input>
                </div>
                <div class="register-form-item flex" v-if="registerSetting">
                    <el-input
                        v-model="smsCode"
                        class="form-input"
                        placeholder="短信验证码"
                        style="width: 264px"
                    >
                        <i
                            class="el-icon-lock"
                            style="font-size: 18px"
                            slot="prepend"
                        />
                    </el-input>
                    <el-button class="sms-btn" @click="sendSMSCode">
                        <div v-if="canSend">获取验证码</div>
                        <count-down
                            v-else
                            :time="60"
                            format="ss秒"
                            autoStart
                            @finish="canSend = true"
                        />
                    </el-button>
                </div>
                <div class="register-form-item">
                    <el-input
                        v-model="password"
                        placeholder="请输入密码 (数字与字母自由组合)"
                        show-password
                    >
                        <i
                            class="el-icon-more-outline"
                            style="font-size: 18px"
                            slot="prepend"
                        />
                    </el-input>
                </div>
                <div class="register-form-item">
                    <el-input
                        v-model="againPwd"
                        placeholder="再次输入密码"
                        show-password
                    >
                        <i
                            class="el-icon-key"
                            style="font-size: 18px"
                            slot="prepend"
                        />
                    </el-input>
                </div>
                <div class="m-t-20">
                    <el-checkbox v-model="isAgree">
                        <span class="black">已阅读并同意</span>
                        <nuxt-link
                            @click.stop
                            class="primary"
                            to="/server_explan?type=1"
                            target="_blank"
                        >
                            《服务协议》、
                        </nuxt-link>
                        <nuxt-link
                            @click.stop
                            class="primary"
                            to="/server_explan?type=2"
                            target="_blank"
                        >
                            《隐私政策》
                        </nuxt-link>
                    </el-checkbox>
                </div>

                <div class="m-t-20 flex-col">
                    <el-button type="primary" @click="registerFun">
                        立即注册
                    </el-button>
                </div>
                <div class="flex row-center" style="margin-top: 36px">
                    <nuxt-link to="/account/login">已有账号，去登录</nuxt-link>
                </div>
            </el-form>
        </div>
    </div>
</template>

<script>
import { client, SMSType } from "@/utils/type";
import CountDown from "@/components/countDown";
import headerMixins from "@/mixins/header";
export default {
    mixins: [headerMixins],
    layout: "default-layout",
    components: {
        CountDown,
    },
    data() {
        return {
            telephone: "",
            smsCode: "",
            password: "",
            againPwd: "",
            canSend: true,
            isAgree: false,
        };
    },
    computed: {
        registerSetting() {
            return this.$store.state.publicData.shop.is_mobile_register_code;
        },
    },
    methods: {
        // 注册发送验证码
        async sendSMSCode() {
            if (!this.canSend) {
                return;
            }
            let res = await this.$post("register/captcha", {
                mobile: this.telephone,
                key: SMSType.REGISTER,
            });
            if (res.code == 1) {
                this.$message({
                    message: res.msg,
                    type: "success",
                });
                this.canSend = false;
            }
        },
        // 注册
        async registerFun() {
            if (!this.isAgree) {
                this.$message({
                    message: "请勾选已阅读并同意《服务协议》、《政策协议》",
                    type: "error",
                });
                return;
            }
            if (!this.telephone) {
                this.$message({
                    message: "请输入手机号",
                    type: "error",
                });
                return;
            }
            if (this.registerSetting && !this.smsCode) {
                this.$message({
                    message: "请输入短信验证码",
                    type: "error",
                });
                return;
            }
            if (!this.password) {
                this.$message({
                    message: "请输入密码",
                    type: "error",
                });
                return;
            }
            if (this.password != this.againPwd) {
                this.$message({
                    message: "两次密码输入不一致",
                    type: "error",
                });
                return;
            }
            let res = await this.$post("register/register", {
                mobile: this.telephone,
                password: this.password,
                password_confirm: this.againPwd,
                code: this.smsCode,
                register_source: client,
            });
            if (res.code == 1) {
                // this.$message({
                //     message: '注册成功',
                //     type: 'success',
                // })
                this.$router.replace("/account/login");
            }
        },
    },
};
</script>

<style lang="scss" scoped>
.register-container {
    flex: 1;
    .register-box {
        padding-top: 40px;
        padding-bottom: 55px;
        width: 880px;
        border: 1px solid #e5e5e5;
        .register-title {
            font-size: 24px;
        }
        .form-box {
            .register-form-item {
                margin-top: 24px;
                .form-input {
                    width: 400px;
                }
                .verify-code-img {
                    width: 100px;
                    height: 40px;
                    margin-left: 26px;
                    background-color: red;
                }
                .sms-btn {
                    margin-left: 16px;
                    height: 40px;
                    width: 120px;
                }
            }
        }
    }
}
</style>
