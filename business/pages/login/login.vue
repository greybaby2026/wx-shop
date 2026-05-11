<template>
    <view class="pages">
        <view class="login">
            <view class="logo">
                <view class="bold">{{ this.appConfig.name }}</view>
            </view>
            <view class="acount-login">
                <view class="input">
                    <u-field label-width="0" clear-size="38" icon="account" v-model="account" placeholder="请输入账号"
                        :input-border="false" />
                </view>
                <view class="input">
                    <u-field label-width="0" icon="lock-open" @right-icon-click="showPassword"
                        :right-icon="isPassword ? 'eye' : 'eye-fill'" :password="isPassword" v-model="password"
                        placeholder="请输入密码" :input-border="false" />
                </view>

                <button class="login-btn white" size="lg" @tap="loginFun">登录</button>
            </view>

        </view>
    </view>
</template>

<script>
import {
    mapMutations,
    mapActions,
    mapGetters
} from 'vuex'
import {
	CONFIG
} from '@/config/cachekey';
import {
    apiAuthLogin,
    apiAccountLogin,
    apiLoginCaptcha
} from '@/api/app';
import wechath5 from '@/utils/wechath5'
import {
    isWeixinClient,
    currentPage,
    client,
    trottle
} from '@/utils/tools'
import Cache from "@/utils/cache"
import {
    BACK_URL,
    INVITE_CODE
} from '@/config/cachekey'
import {
    getWxCode,
    getUserProfile
} from '@/utils/login'
import store from 'store'
export default {
    data() {
        return {
            password: '',
            account: '',
            // checked: false,
            isPassword: true,
            client: store.getters.client, //1-微信小程序 2-微信公众号 3-安卓app 4-苹果app 5-pc端 6-h5
            appConfig:''
        };
    },

    methods: {
        ...mapMutations(['login']),
        ...mapActions(['getUser']),

        // 账号登录
        async loginFun() {
            const {
                account,
                password,
                // checked
            } = this
            // if (!checked) return this.$toast({
            //     title: '请阅读并同意隐私协议'
            // })

            if (!account) return this.$toast({
                title: '请输入账号'
            })

            if (!password) return this.$toast({
                title: '请输入密码'
            })

            apiAccountLogin({
                account,
                client: this.client,
                password: password,
                terminal: 3
            }).then(res => {
                this.loginHandle(res)
            })
        },
        // 登录结果处理
        async loginHandle(data) {
            this.login(data)
            this.getUser()
            uni.hideLoading()
            uni.reLaunch({
                url: '/pages/index/index'
            })
        },

        showPassword() {
            this.isPassword = !this.isPassword
        }
    },
    onLoad() {
        const account = Cache.get('account')
        setTimeout(() => {
            this.appConfig = this.$store.state.app.config
            console.log(this.appConfig.name)
        }, 300);
        if (account) this.account = account

        this.getCodeUrl = trottle(this.getCodeUrl)
        // #ifdef H5
        // this.oaAutoLogin()
        // #endif
    },
    onUnload() {

    },
};
</script>
<style lang="scss">
page {
    background-color: #fff;

    padding: 0;
}

.login {
    height: 100vh;
    padding-top: 80rpx;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;

    .logo {
        height: 126rpx;
        text-align: center;

        view:first-child {
            font-size: 52rpx;
        }

        // view:last-child {
        //     font-size: 36rpx;
        // }
    }


    .acount-login,
    .other-login {
        margin-top: 63rpx;
        width: calc(100% - 80rpx);

        .input {
            padding-top: 20rpx;
        }

        .login-btn {
            border-radius: 8rpx;
            margin: 30rpx 0 50rpx;
            background-color: $-color-primary;
            border-radius: 40rpx;
        }
    }

    .agreement {
        color: $-color-primary;
    }
}
</style>
