<template>
    <view :class="themeName">
        <view class="service">
            <!-- Contain -->
            <view class="service-contain">
                <view class="header-image">
                    <u-image
                        :src="$getImageUri('/resource/image/shopapi/default/service.png')"
                        width="100rpx"
                        height="100rpx"
                        shape="circle"
                        style="margin-top: -50rpx"
                        class="circle"
                    ></u-image>
                </view>

                <!-- type==1 二维码客服 -->
                <view
                    class="flex lg m-t-40 row-center"
                    v-if="ServiceConfig.remarks && ServiceConfig.way == 1"
                >
                    {{ ServiceConfig.remarks }}
                </view>

                <view class="flex code row-center" v-if="ServiceConfig.way == 1">
                    <u-image
                        :src="ServiceConfig.qr_code"
                        width="320rpx"
                        height="320rpx"
                        border-radius="20"
                    ></u-image>
                </view>

                <view
                    class="m-t-20 m-b-20 flex row-center xs muted"
                    v-if="ServiceConfig.phone && ServiceConfig.way == 1"
                >
                    客服电话：{{ ServiceConfig.phone }}
                </view>
                <view
                    class="flex row-center xs m-t-20 muted m-b-20"
                    v-if="ServiceConfig.business_time && ServiceConfig.way == 1"
                >
                    服务时间: {{ ServiceConfig.business_time }}
                </view>
                <!-- logo -->

                <template v-if="ServiceConfig.way != 1">
                    <view class="flex lg row-center m-t-40">
                        {{ shop_name }}
                    </view>
                    <view class="flex code row-center">
                        <u-image
                            :src="logo"
                            width="200rpx"
                            height="200rpx"
                            border-radius="20"
                        ></u-image>
                    </view>
                </template>

                <!-- type==2 电话客服 -->

                <view
                    class="m-t-40 flex row-center"
                    v-if="ServiceConfig.phone && ServiceConfig.way == 2"
                >
                    <view style="text-align: center" @click="goService">
                        <view> 拨打客服热线 </view>
                        <view style="text-decoration: underline">
                            {{ ServiceConfig.phone }}
                        </view>
                    </view>

                    <!-- <view class="br60 m-l-10 phone muted">拨打</view> -->
                </view>
            </view>
            <view class="p-t-20" v-if="ServiceConfig.way == 1" @click="saveCode">
                <view class="br20 copy-btn flex row-center white lg">
                    <text class="m-l-20">保存二维码</text>
                </view>
            </view>
            <view class="p-t-20" v-if="ServiceConfig.way == 2" @click="goService">
                <view class="br20 copy-btn flex row-center white lg">
                    <text class="m-l-20">拨打电话</text>
                </view>
            </view>
            <!-- type==3 系统客服 -->

            <view class="p-t-20" v-if="ServiceConfig.way == 3" @click="goService">
                <view class="copy-btn br20 flex row-center white lg">
                    <text class="m-l-20">联系系统客服</text>
                </view>
            </view>

            <!-- type==4 企业微信客服 -->
            <view class="p-t-20" v-if="ServiceConfig.way == 4" @click="goService">
                <view class="copy-btn flex row-center white lg br20">
                    <text class="m-l-20">联系企业微信客服</text>
                </view>
            </view>
            <!-- type==5 微信小程序客服 -->

            <view class="p-t-20" v-if="ServiceConfig.way == 5">
                <!-- #ifdef MP-WEIXIN -->
                <button
                    open-type="contact"
                    class="copy-btn icon-item white lg br20"
                    style="line-height: 88rpx"
                    v-if="ServiceConfig.way == 5"
                >
                    <text style="line-height: 32rpx">联系微信小程序客服</text>
                </button>
                <!-- #endif -->
            </view>
        </view>

        <u-modal
            :content="content"
            v-model="showPhoneCall"
            show-cancel-button
            confirm-text="呼叫"
            :confirm-color="themeColor"
            @confirm="onCall"
        >
        </u-modal>

        <loading-view v-if="loading"></loading-view>
    </view>
</template>

<script>
import { copy } from '@/utils/tools.js'
import { apiServiceConfig } from '@/api/store.js'
import { mapGetters, mapState, mapActions } from 'vuex'
import ServiceMixin from '@/mixins/service'

export default {
    name: 'Service',
    mixins: [ServiceMixin],

    data() {
        return {
            serviceData: {},
            showPhoneCall: false,
            content: '即将打电话给',
            loading: true,
            logo: '',
            shop_name: ''
        }
    },

    methods: {
        ...mapActions(['getsetserviceConfig']),

        getServiceConfigDataFun() {
            apiServiceConfig()
                .then((res) => {
                    this.serviceData = res
                    //#ifndef H5
                    this.downloadCode()
                    //#endif
                    setTimeout(() => {
                        this.loading = false
                    }, 100)
                })
                .catch((err) => {
                    this.loading = false
                })
        },

        // 下载图片
        downloadCode() {
            uni.downloadFile({
                url: this.ServiceConfig.qr_code, //仅为示例，并非真实的资源
                success: (res) => {
                    if (res.statusCode == 200) {
                        this.tempFilePath = res.tempFilePath
                    }
                }
            })
        },

        saveCode() {
            //#ifdef H5
            this.$toast({
                title: '长按图片保存'
            })
            //#endif
            //#ifndef H5
            uni.saveImageToPhotosAlbum({
                filePath: this.tempFilePath,
                success: (res) => {
                    this.$toast({
                        title: '保存成功',
                        icon: 'success'
                    })
                },
                fail: (err) => {
                    this.$toast({
                        title: '保存失败'
                    })
                }
            })
            //#endif
        },

        // 显示是否拨打电话弹窗
        showTelTips() {
            this.showPhoneCall = true
            this.content = '即将打电话给' + this.ServiceConfig.phone
        },

        //打电话
        onCall() {
            uni.makePhoneCall({
                phoneNumber: this.ServiceConfig.phone.toString()
            })
        }
    },

    computed: {
        ...mapGetters(['isLogin', 'ServiceConfig', 'appConfig'])
    },

    async onLoad() {
        const { shop_name, logo } = this.appConfig
        this.shop_name = shop_name
        this.logo = logo
        this.getServiceConfigDataFun()
        // 获取客服配置
        await this.getsetserviceConfig()
    }
}
</script>

<style lang="scss" scoped>
.header-image {
    display: flex;
    justify-content: center;
    .circle {
        border-radius: 50%;
        border: 6rpx solid #ffffff;
    }
}
.service {
    width: 100vw;
    height: 100vh;
    background-image: url(../../static/images/service_bg.jpg);
    background-size: 100% auto;
    overflow: hidden;

    &-contain {
        width: 620rpx;
        // height: 750rpx;
        margin: 140rpx auto;
        margin-bottom: 40rpx;
        padding-bottom: 80rpx;
        border-radius: 10px;
        background-color: #ffffff;

        .code {
            border-radius: 20rpx;
            padding-top: 60rpx;
        }

        .phone {
            padding: 0 20rpx;
            text-decoration: underline;
        }
    }
}
.copy-btn {
    margin: 0 80rpx;
    height: 88rpx;
    // @include background_linear(90deg, 0, 100%);
    background: #000722;
}
</style>
