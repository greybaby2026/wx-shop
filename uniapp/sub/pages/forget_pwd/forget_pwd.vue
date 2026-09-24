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
                title="忘记密码"
                :title-bold="true"
                :is-fixed="false"
                :border-bottom="false"
                :background="{ background: 'rgba(256,256, 256,0)' }"
            ></u-navbar>
        </u-sticky>
        <!-- #endif -->
        <view class="forget-pwd">
            <view class="forget-pwd-text">忘记登录密码</view>
            <view class="input m-t-40">
                <u-field
                    label-width="0"
                    v-model="ferget.mobile"
                    placeholder="输入手机号"
                    :border-bottom="false"
                    :style="{ width: '100%' }"
                    type="number"
                />
            </view>
            <view class="input m-t-40">
                <u-field
                    label-width="0"
                    v-model="ferget.code"
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
                                        codeTips == '获取验证码' && ferget.mobile.length !== 11
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
                    v-model="ferget.password"
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
            <button
                class="forget-pwd-btn m-t-40 white"
                :class="{ disabled: isDisabled }"
                @tap="fergetFun"
            >
                立即重置密码
            </button>
        </view>
    </view>
</template>

<script>
import { apiResetPasswordCaptcha, apiResetPassword, apiCheckMobile } from '@/api/app'
import { mapGetters } from 'vuex'

export default {
    data() {
        return {
            ferget: {
                mobile: '',
                code: '',
                password: ''
            },
            codeTips: '',
            pwdShow: false
        }
    },
    methods: {
        codeChange(tip) {
            this.codeTips = tip
        },
        fergetFun() {
            let { mobile, password, code, password_confirm } = this.ferget
            if (!mobile) {
                this.$toast({
                    title: '请输入手机号'
                })
                return
            }
            if (!code) {
                this.$toast({
                    title: '请输入验证码'
                })
                return
            }
            if (!password) {
                this.$toast({
                    title: '请输入密码'
                })
                return
            }
            // if (!password) {
            //   this.$toast({
            //     title: "请再次输入密码",
            //   });
            //   return;
            // }
            apiResetPassword({
                mobile,
                password,
                code
            }).then((res) => {
                setTimeout(() => {
                    this.$Router.back(1)
                }, 1500)
            })
        },
        async sendSMS() {
            if (!this.$refs.uCode.canGetCode) return
            if (!this.ferget.mobile) {
                this.$toast({
                    title: '请输入手机号'
                })
                return
            }
            /**
             * @descriptionp 检测手机号是否已经注册
             */
            const { has } = await apiCheckMobile({ mobile: this.ferget.mobile })
            if (has === 0) {
                return this.$toast({
                    title: '手机号未注册'
                })
            }
            apiResetPasswordCaptcha({
                mobile: this.ferget.mobile
            })
                .then((res) => {
                    this.$refs.uCode.start()
                })
                .catch((err) => {
                    console.log(err)
                })
        }
    },
    computed: {
        ...mapGetters(['appConfig']),
        isDisabled() {
            if (this.ferget.mobile.length == 11 && this.ferget.code && this.ferget.password) {
                return false
            } else {
                return true
            }
        }
    }
}
</script>

<style lang="scss">
page {
    background-color: white;
}

.forget-pwd {
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
    &-btn {
        margin-top: 40rpx;
        width: 100%;
        height: 100rpx;
        line-height: 100rpx;
        font-size: 32rpx;
        border-radius: 12rpx;
        @include background_color();
    }
    .sms-btn {
        text-align: center;
        @include font_color();
        border-left: $-solid-border;
    }
    .disabled {
        opacity: 0.5;
    }
}
</style>
