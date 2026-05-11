<template>
    <div class="forget-pwd-container flex-col row-center col-center">
        <div class="forget-pwd-box flex-col col-center bg-white">
            <div class="forget-pwd-title">忘记密码</div>
            <el-form class="form-box flex-col">
                <div class="forget-form-item">
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
                <div class="forget-form-item flex">
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
                <div class="forget-form-item">
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
                <div class="forget-form-item">
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
                <div class="flex-col" style="margin-top: 46px">
                    <el-button type="primary" @click="forgetFun"
                        >确定</el-button
                    >
                </div>
            </el-form>
        </div>
    </div>
</template>

<script>
import { SMSType, client } from '@/utils/type'
import CountDown from '~/components/countDown'
import headerMixins from "@/mixins/header"
export default {
    mixins: [headerMixins],
    layout: 'default-layout',
    components: {
        CountDown,
    },
    data() {
        return {
            telephone: '',
            smsCode: '',
            canSend: true,
            password: '',
            againPwd: '',
        }
    },
    methods: {
        async forgetFun() {
            if (!this.telephone) {
                this.$message({
                    message: '请输入手机号码',
                    type: 'error',
                })
                return
            } else if (!this.smsCode) {
                this.$message({
                    message: '请输入验证码',
                    type: 'error',
                })
                return
            } else if (!this.password) {
                this.$message({
                    message: '请输入密码信息',
                    type: 'error',
                })
                return
            } else if (this.password != this.againPwd) {
                this.$message({
                    message: '两次输入密码不一致',
                    type: 'error',
                })
                return
            }
            let res = await this.$post('user/resetPassword', {
                mobile: this.telephone,
                code: this.smsCode,
                password: this.password,
                // repassword: this.againPwd,
                // client: client,
            })
            if (res.code == 1) {
                this.$message({
                    message: '修改成功',
                    type: 'success',
                })
                let time = setTimeout(() => {
                    this.$router.replace('/account/login')
                    clearTimeout(time)
                }, 1000)
            }
        },
        async sendSMSCode() {
            if (!this.telephone) {
                this.$message({
                    message: '请输入手机号码',
                    type: 'error',
                })
                return
            }
            let res = await this.$post('user/resetPasswordCaptcha', {
                mobile: this.telephone,
                // key: SMSType.FINDPWD,
            })
            if (res.code == 1) {
                this.canSend = false
                this.$message({
                    message: '发送成功',
                    type: 'success',
                })
            }
        },
    },
}
</script>

<style lang="scss" scoped>
.forget-pwd-container {
    flex: 1;
    .forget-pwd-box {
        padding-top: 40px;
        padding-bottom: 55px;
        width: 880px;
        border: 1px solid #e5e5e5;
        .forget-pwd-title {
            font-size: 24px;
        }
        .form-box {
            .forget-form-item {
                margin-top: 24px;
            }
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
</style>
