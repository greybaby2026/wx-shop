<template>
    <view :class="themeName">
        <!-- 头部修改头像 -->
        <view class="header bg-white p-t-30">
            <button
                class="flex flex-col row-center"
                hover-class="none"
                @chooseavatar="onChooseAvatar"
                open-type="chooseAvatar"
                @click="onChooseAvatar"
            >
                <image
                    :src="
                        userInfos.avatar != ''
                            ? userInfos.avatar
                            : '../../static/images/icon_wechat.png'
                    "
                ></image>
                <view class="muted xs">点击修改头像</view>
            </button>
        </view>
        <view class="bg-white p-t-30">
            <!-- ID -->
            <view class="item nr flex row-between">
                <view class="label">ID</view>
                <view class="content">{{ userInfos.sn }}</view>
            </view>
        </view>

        <!-- 昵称 -->
        <view
            class="item nr flex row-between"
            @click=";(showNickName = true), (newNickname = userInfos.nickname)"
        >
            <view class="label">昵称</view>
            <view class="content">{{ userInfos.nickname }}</view>
            <u-icon name="arrow-right"></u-icon>
        </view>

        <!-- 性别 -->
        <view class="item nr flex row-between" @click="changeSex">
            <view class="label">性别</view>
            <view class="content">{{ userInfos.sex }}</view>
            <u-icon name="arrow-right"></u-icon>
        </view>

        <!-- 手机号 -->
        <view class="item nr flex row-between">
            <view class="label">手机号</view>
            <view class="content">{{
                userInfos.mobile == '' ? '未绑定手机号' : userInfos.mobile
            }}</view>

            <!-- #ifdef MP-WEIXIN -->
            <button
                class="br60 bind nr flex row-center"
                open-type="getPhoneNumber"
                @getphonenumber="getPhoneNumber"
            >
                {{ userInfos.mobile == '' ? '绑定手机号' : '更换手机号' }}
            </button>
            <!-- #endif -->
            <!-- #ifndef MP-WEIXIN -->
            <button
                class="br60 bind nr flex row-center"
                @click="goPage('/bundle/pages/pay_pwd/pay_pwd', userInfos.mobile == '' ? 4 : 5)"
            >
                {{ userInfos.mobile == '' ? '绑定手机号' : '更换手机号' }}
            </button>
            <!-- #endif -->
        </view>
        <!-- 注册时间 -->
        <view class="item nr flex row-between">
            <view class="label">注册时间</view>
            <view class="content">{{ userInfos.create_time }}</view>
        </view>
        <view class="p-b-50">
            <button
                class="btn-cancel m-t-30 md br60 flex row-center white"
                style="height: 88rpx"
                @click="goPage('/bundle/pages/cancel/cancel')"
            >
                注销账号
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
import { chooseImage, uploadFile, trottle } from '@/utils/tools.js'

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
            showNickName: false, //显示昵称输入框
            fieldType: FieldType.NONE, //用户信息的枚举
            newNickname: '', //用户昵称
            showPicker: false, //显示性别选择
            sexList: ['男', '女'] //选择性别数据
        }
    },
    methods: {
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
        // 获取用户信息
        getUserInfoFun() {
            apiGetUserInfo().then((res) => {
                this.userInfos = res
            })
        },
        goPage(url, type) {
            this.$Router.push({
                path: url,
                query: {
                    type: type
                }
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
            this.fieldType = FieldType.AVATAR
            // #ifndef MP-WEIXIN
            // 此为uView的跳转方法，详见"文档-JS"部分，也可以用uni的uni.navigateTo
            this.$Router.push({
                // 关于此路径，请见下方"注意事项"
                path: '/components/uview-ui/components/u-avatar-cropper/u-avatar-cropper',
                // 内部已设置以下默认参数值，可不传这些参数
                query: {
                    // 输出图片宽度，高等于宽，单位px
                    destWidth: 300,
                    // 裁剪框宽度，高等于宽，单位px
                    rectWidth: 200,
                    // 输出的图片类型，如果'png'类型发现裁剪的图片太大，改成"jpg"即可
                    fileType: 'jpg'
                }
            })
            // #endif
            // #ifdef MP-WEIXIN
            if (e.detail.avatarUrl) {
                this.uploadImage(e.detail.avatarUrl)
            }
            // #endif
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
    onShow() {
        this.getUserInfoFun()
    },
    async onLoad() {
        // 监听从裁剪页发布的事件，获得裁剪结果
        uni.$on('uAvatarCropper', (path) => {
            this.uploadImage(path)
        })
    }
}
</script>

<style lang="scss">
.header {
    padding: 30rpx 20rpx;
    margin-top: 20rpx;
    width: 100%;
    // height: 240rpx;
    border-radius: 14rpx;
    image {
        width: 120rpx;
        height: 120rpx;
        border-radius: 50%;
    }
    button {
        padding: 0;
    }
}
.item {
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
.btn-cancel {
    height: 70rpx;
    margin: 60rpx 50rpx 0;
    // @include background_color();
    color: #666666;
    background-color: white;
}
.btn {
    height: 70rpx;
    margin: 60rpx 50rpx 0;
    @include background_color();
}
</style>
