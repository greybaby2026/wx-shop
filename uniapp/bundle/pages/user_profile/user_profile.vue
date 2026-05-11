<template>
    <view :class="themeName">
        <!-- 头部修改头像 -->
        <view class="header bg-white p-t-30" @click="onChooseAvatar">
            <button class="flex" hover-class="none">
                <!-- @chooseavatar="onChooseAvatar open-type="chooseAvatar""
                @click="onChooseAvatar" -->
                <image
                    :src="
                        userInfos.avatar != ''
                            ? userInfos.avatar
                            : '../../static/images/icon_wechat.png'
                    "
                ></image>
            </button>
            <view class="m-l-40">
                <view class="nickname lg">
                    {{ userInfos.nickname }}
                </view>
                <view class="muted m-t-20"> ID：{{ userInfos.sn }} </view>
            </view>
            <view style="margin-left: auto">
                <u-icon name="arrow-right"></u-icon>
            </view>
        </view>

        <!-- 交易密码 -->
        <view class="item nr flex row-between" @click="showPayPwd">
            <view class="label">转账密码</view>
            <u-icon name="arrow-right"></u-icon>
        </view>

        <!-- 登录密码 -->
        <view class="item nr flex row-between" @click="showLogin">
            <view class="label">登录密码</view>
            <u-icon name="arrow-right"></u-icon>
        </view>

        <view v-if="userInfo.is_auth === 0" class="item nr flex row-between">
            <view class="label">微信授权</view>
            <button class="br60 bind nr flex row-center" @tap="bindWechat">授权</button>
        </view>

        <!-- 服务协议 -->
        <view
            class="item nr flex row-between"
            style="margin-top: 24rpx"
            @click="goPage('/bundle/pages/server_explan/server_explan', 1)"
        >
            <view class="label">服务协议</view>
            <view class="content"></view>
            <u-icon name="arrow-right"></u-icon>
        </view>
        <!-- 隐私政策 -->
        <view
            class="item nr flex row-between"
            @click="goPage('/bundle/pages/server_explan/server_explan', 2)"
        >
            <view class="label">隐私政策</view>
            <view class="content"></view>
            <u-icon name="arrow-right"></u-icon>
        </view>

        <!-- 关于我们 -->
        <view class="item nr flex row-between">
            <view class="label">关于我们</view>
            <view class="content flex row-right m-r-24">v{{ userInfos.version }}</view>
        </view>

        <view class="p-b-50">
            <button
                class="btn m-t-30 md br60 flex row-center white"
                style="height: 88rpx"
                @click="logOut"
            >
                退出登录
            </button>
        </view>

        <!-- 昵称修改组件 -->
        <u-popup
            v-model="showNickName"
            :closeable="true"
            :maskCloseAble="false"
            mode="center"
            border-radius="14"
        >
            <view class="p-24 bg-white" style="width: 70vw">
                <view class="m-b-30 xl text-center">修改用户名</view>
                <form @submit="changeNameConfirm">
                    <u-form-item label="新昵称" :labelWidth="120">
                        <input
                            style="height: 60rpx"
                            class="nr"
                            :value="userInfos.nickname"
                            name="nickname"
                            type="nickname"
                            placeholder="请输入新的昵称"
                        />
                    </u-form-item>
                    <button class="btn white flex br60 row-center" form-type="submit">确定</button>
                </form>
            </view>
        </u-popup>

        <!-- 性别修改组件 -->
        <u-picker
            mode="selector"
            v-model="showPicker"
            confirm-color="#101010"
            :default-selector="[0]"
            :range="sexList"
            @confirm="changeSexConfirm"
        />

        <!-- 登录密码组件 -->
        <u-action-sheet
            :list="loginList"
            @click="actionSheetLogin"
            v-model="showTipsLogin"
        ></u-action-sheet>
        <!-- 交易密码组件 -->
        <u-action-sheet
            :list="payList"
            @click="actionSheetPay"
            v-model="showTipsPwd"
        ></u-action-sheet>
    </view>
</template>

<script>
import {
    apiLogout, //退出登录
    apiSetUserInfo, //设置用户信息
    apiGetUserInfo, //获取用户信息
    apiMnpBindMobile, //获取微信手机号码
    hasPayPassword, //判断是否有交易密码
    apiBindMobileCaptcha, //发送验证码绑定手机号
    apiBindMobile, //绑定手机号
    apiHasPassword,
    apiMnpAuthLogin,
    apiOaAuthLogin
} from '@/api/user'
import { SMSEnum, ClientEnum } from '@/utils/enum'
import { chooseImage, uploadFile, trottle } from '@/utils/tools.js'
import { mapActions, mapGetters, mapMutations } from 'vuex'
import { getCode, getUserProfile } from '@/utils/login'
import Cache from '@/utils/cache.js'
import wechath5 from '@/utils/wechath5'
const FieldType = {
    NONE: '',
    SEX: 'sex',
    NICKNAME: 'nickname',
    AVATAR: 'avatar',
    MOBILE: 'mobile'
}
export default {
    data() {
        return {
            userInfos: {}, //用户信息
            fieldType: FieldType.NONE, //用户信息的枚举
            smsType: SMSEnum['FINDPWD'], //短信的枚举
            showNickName: false, //显示昵称输入框
            newNickname: '', //用户昵称
            mobile: '', //手机号码 其他端
            showPicker: false, //显示性别选择
            sexList: ['男', '女'], //选择性别数据

            payList: [
                {
                    text: '设置密码'
                },
                {
                    text: '忘记密码'
                }
            ], //交易密码选择

            has_pay_password: false, //是否设置了交易密码

            showTipsPwd: false, //显示交易密码选择
            showTipsLogin: false, //显示登录密码选择
            has_password: false, //是否设置了登录密码
            loginList: [
                {
                    text: '设置密码'
                },
                {
                    text: '忘记密码'
                }
            ] //登录密码选择
        }
    },
    computed: {
        ...mapGetters(['client'])
    },
    methods: {
        ...mapActions(['getUser']),
        ...mapMutations(['logout']),

        // 获取用户信息
        getUserInfoFun() {
            apiGetUserInfo().then((res) => {
                this.userInfos = res
            })
        },

        // 修改用户信息
        async setUserInfoFun(value) {
            await apiSetUserInfo({
                field: this.fieldType,
                value: value
            })
            this.getUserInfoFun()
        },

        // 上传图片
        onChooseAvatar(e) {
            // this.fieldType = FieldType.AVATAR
            // // #ifndef MP-WEIXIN
            // // 此为uView的跳转方法，详见"文档-JS"部分，也可以用uni的uni.navigateTo
            // this.$Router.push({
            //     // 关于此路径，请见下方"注意事项"
            //     path: '/components/uview-ui/components/u-avatar-cropper/u-avatar-cropper',
            //     // 内部已设置以下默认参数值，可不传这些参数
            //     query: {
            //         // 输出图片宽度，高等于宽，单位px
            //         destWidth: 300,
            //         // 裁剪框宽度，高等于宽，单位px
            //         rectWidth: 200,
            //         // 输出的图片类型，如果'png'类型发现裁剪的图片太大，改成"jpg"即可
            //         fileType: 'jpg'
            //     }
            // })
            // // #endif
            // // #ifdef MP-WEIXIN
            // if (e.detail.avatarUrl) {
            //     this.uploadImage(e.detail.avatarUrl)
            // }
            // #endif
            this.$Router.push({
                path: '/bundle/pages/user_set/user_set'
            })
        },
        // 修改用户昵称
        async changeNameConfirm(e) {
            this.fieldType = FieldType.NICKNAME
            this.newNickname = e.detail.value.nickname
            if (!this.newNickname)
                return this.$toast({
                    title: '请输入新的昵称'
                })
            await this.setUserInfoFun(this.newNickname)
            this.showNickName = false
        },
        // 显示修改用户性别弹窗
        changeSex() {
            this.showPicker = true
            this.fieldType = FieldType.SEX
        },

        // 修改用户性别
        changeSexConfirm(value) {
            this.setUserInfoFun(value[0] + 1)
            this.showPicker = false
        },

        // 绑定｜｜修改用户手机号
        async getPhoneNumber(e) {
            const { code, errMsg } = e.detail
            if (errMsg !== 'getPhoneNumber:ok')
                return this.$toast({
                    title: '绑定失败'
                })
            apiMnpBindMobile({
                code
            }).then((res) => {
                this.getUserInfoFun()
            })
        },

        // 判断是否有设置交易密码
        getHasPayPassword() {
            hasPayPassword().then((res) => {
                this.has_pay_password = res.has_pay_password
                if (res.has_pay_password == false) {
                    this.payList = [
                        {
                            text: '设置密码'
                        }
                    ]
                } else {
                    this.payList = [
                        {
                            text: '修改密码'
                        },
                        {
                            text: '忘记密码'
                        }
                    ]
                }
            })
        },

        // 显示交易密码选择
        showPayPwd() {
            // if (this.userInfos.mobile == '')
            //     return this.$toast({
            //         title: '请先绑定手机号码！'
            //     })
            this.showTipsPwd = true
            console.log(this.showTipsPwd)
        },

        // 选择的交易密码
        actionSheetPay(event) {
            if (!this.has_pay_password) {
                // 设置密码
                this.goPage('/bundle/pages/pay_pwd/pay_pwd', 1)
            } else {
                if (event === 0) {
                    // 修改密码
                    this.goPage('/bundle/pages/pay_pwd/pay_pwd', 2)
                } else if (event === 1) {
                    // 忘记密码
                    this.goPage('/bundle/pages/pay_pwd/pay_pwd', 3)
                }
            }
        },

        // 判断是否有设置登录密码
        getHasLoginPassword() {
            apiHasPassword().then((res) => {
                this.has_password = res.has_password
                if (res.has_password == false) {
                    this.loginList = [
                        {
                            text: '设置密码'
                        }
                    ]
                } else {
                    this.loginList = [
                        {
                            text: '修改密码'
                        },
                        {
                            text: '忘记密码'
                        }
                    ]
                }
            })
        },

        // 显示登录密码选择
        showLogin() {
            // if (this.userInfos.mobile == '')
            //     return this.$toast({
            //         title: '请先绑定手机号码！'
            //     })
            this.showTipsLogin = true
        },
        // 选择的登录密码
        actionSheetLogin(event) {
            console.log(this.has_password)
            if (!this.has_password) {
                // 设置密码
                this.goPage('/bundle/pages/pay_pwd/pay_pwd', 6)
            } else {
                if (event === 0) {
                    // 修改密码
                    this.goPage('/bundle/pages/pay_pwd/pay_pwd', 7)
                } else if (event === 1) {
                    // 忘记密码
                    this.goPage('/bundle/pages/pay_pwd/pay_pwd', 8)
                }
            }
        },

        /** URL: 页面路径 - TYPE: 状态Enum **/
        goPage(url, type) {
            this.$Router.push({
                path: url,
                query: {
                    type: type
                }
            })
        },

        // 退出登录
        logOut() {
            uni.showModal({
                content: '是否退出登录？',
                confirmColor: this.themeColor,
                success: ({ cancel }) => {
                    if (cancel) return
                    apiLogout().then((res) => {
                        Cache.remove('TOKEN')
                        Cache.remove('USER_INFO')
                        this.logout()

                        uni.redirectTo({
                            url: '/pages/login/login'
                        })
                    })
                }
            })
        },
        //绑定微信
        bindWechat() {
            switch (this.client) {
                case ClientEnum['MP_WEIXIN']:
                    this.mnpAuth()
                    break
                case ClientEnum['OA_WEIXIN']:
                    this.oaAuth()
                    break
            }
        },
        async mnpAuth() {
            const code = await getCode()
            await apiMnpAuthLogin({
                code
            })
            await this.getUser()
            const { form } = this.$Route.query
            if (form === 'payment') {
                this.$Router.back()
            }
        },
        oaAuth() {
            wechath5.getWxUrl()
        },
        uploadImage(path) {
            uni.showLoading({
                title: '正在上传中...',
                mask: true
            })
            uploadFile(path)
                .then((res) => {
                    uni.hideLoading()
                    this.setUserInfoFun(res.uri)
                })
                .catch(() => {
                    uni.hideLoading()
                    this.$toast({
                        title: '上传失败'
                    })
                })
        }
    },

    async onLoad() {
        // #ifdef H5
        let { code, form } = this.$Route.query
        if (code) {
            if (Array.isArray(code)) code = code.pop()
            await apiOaAuthLogin({
                code
            })
            await this.getUser()
            if (form === 'payment') {
                this.$Router.replace({
                    path: '/bundle/pages/user_order/user_order',
                    query: {
                        mode: 'pay'
                    }
                })
            }
        }
        // #endif

        // 监听从裁剪页发布的事件，获得裁剪结果
        uni.$on('uAvatarCropper', (path) => {
            this.uploadImage(path)
        })
        this.bindWechat = trottle(this.bindWechat)
    },
    onUnload() {
        uni.$off('uAvatarCropper')
    },
    onShow() {
        this.getUserInfoFun()
        this.getHasPayPassword()
        this.getHasLoginPassword()
    }
}
</script>

<style lang="scss" scoped>
.header {
    padding: 30rpx 20rpx;
    margin: 20rpx 0;
    width: 100%;
    // height: 240rpx;
    border-radius: 14rpx;
    display: flex;
    align-items: center;
    image {
        width: 120rpx;
        height: 120rpx;
        border-radius: 50%;
    }
    button {
        padding: 0;
    }
    .nickname {
    }
}

.item {
    margin-top: 2rpx;
    padding: 30rpx 20rpx;
    border-radius: 14rpx;
    background-color: #ffffff;

    .label {
        width: 150rpx;
    }

    .content {
        flex: 1;
        width: 80%;
    }

    .bind {
        height: 56rpx;
        border-width: 1rpx;
        border-style: solid;
        @include font_color();
        @include border_color();
    }
}

.btn {
    height: 70rpx;
    margin: 60rpx 50rpx 0;
    @include background_color();
}
</style>
