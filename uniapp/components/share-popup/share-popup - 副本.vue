<template>
    <view :class="themeName">
        <u-popup
            class="share-popup"
            v-model="showshare"
            mode="bottom"
            border-radius="14"
            :closeable="true"
            :safe-area-inset-bottom="true"
        >
            <view class="flex row-center md bold m-t-30 m-b-30">分享至</view>
            <view class="flex row-around share-tab">
                <view class="flex-col col--center" @tap="getPoster">
                    <image
                        mode="widthFix"
                        class="share-icon"
                        src="/static/images/icon_generate_poster.png"
                    ></image>
                    <view class="m-t-10">生成海报</view>
                </view>
                <!-- #ifdef MP-WEIXIN-->
                <button open-type="share" class="flex-col col--center" hover-class="none">
                    <image class="share-icon" src="/static/images/icon_wechat.png"></image>
                    <view class="m-t-10">微信好友</view>
                </button>
                <!-- #endif -->
                <!-- #ifdef H5 || APP-PLUS -->
                <view oclass="flex-col col--center" @tap="shareWx">
                    <image class="share-icon" src="/static/images/icon_wechat.png"></image>
                    <view class="m-t-10">微信好友</view>
                </view>
                <!-- #endif -->
            </view>
            <view class="flex row-center bg-body cancel xl" @tap="showshare = false">取消</view>
        </u-popup>
        <u-popup
            class="share-poster"
            v-model="showPoster"
            mode="center"
            :closeable="true"
            :safe-area-inset-bottom="true"
        >
            <!-- #ifndef H5 -->
            <image style="width: 640rpx" mode="widthFix" :src="poster"></image>
            <!-- #endif -->
            <!-- #ifdef H5 -->
            <img style="width: 640rpx" :src="poster" />
            <!-- #endif -->
            <button class="flex row-center save-btn" size="lg" @tap="savePoster">
                <!-- #ifndef H5 -->
                保存图片到相册
                <!-- #endif -->
                <!-- #ifdef H5 -->
                长按保存图片到相册
                <!-- #endif -->
            </button>
        </u-popup>
        <!-- #ifdef H5 -->
        <u-popup class="share-tips" v-model="showTips" mode="top">
            <view style="overflow: hidden">
                <image src="/static/images/share_arrow.png" class="share-arrow" />
                <view class="white" style="text-align: center; margin-top: 280rpx">
                    <view class="bold lg">立即分享给好友吧</view>
                    <view class="sm m-t-10">点击屏幕右上角将本页面分享给好友</view>
                </view>
            </view>
        </u-popup>
        <!-- #endif -->

        <poster
            ref="poster"
            :options="options"
            :qrcode="mnpQrcode"
            :link="getLink"
            @success="handleSuccess"
            @fail="handleFail"
            :poster_img="poster_img"
        />
    </view>
</template>

<script>
import { apiGoodsPosterConfig, apiMnpQrCode } from '@/api/app'
import { baseURL, basePath } from '@/config/app'
import poster from './poster.vue'
export default {
    components: {
        poster
    },
    props: {
        value: {
            type: Boolean
        },
        shareId: {
            type: [String, Number],
            default: ''
        },
        pagePath: {
            type: String,
            default: ''
        },
        config: {
            type: Object,
            default: () => ({})
        },
        type: Number,
        bargain: {
            type: Object,
            default: () => ({})
        },
        poster_img: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            poster: '',
            showPoster: false,
            showTips: false,
            mnpQrcode: '',
            options: {}
        }
    },
    computed: {
        getLink() {
            return `${baseURL}${basePath}/${this.pagePath}?id=${this.shareId}&invite_code=${this.userInfo.code}`
        },
        showshare: {
            get() {
                return this.value
            },
            set(val) {
                this.$emit('input', val)
            }
        }
    },
    methods: {
        async getConfig() {
            const parmas =
                this.type == 4
                    ? {
                          type: this.type,
                          goods_id: this.bargain.goodsId,
                          activity_id: this.bargain.activityId
                      }
                    : {
                          goods_id: this.shareId,
                          type: this.type,
                          activity_id: this.shareId
                      }
            const data = await apiGoodsPosterConfig(parmas)
            this.options = data
        },
        async getPoster() {
            if (!this.isLogin) return this.$Router.push('/pages/login/login')
            if (this.poster) {
                this.showPoster = true
                this.showshare = false
                return
            }
            uni.showLoading({
                title: '正在生成中'
            })
            await Promise.all([
                this.getConfig(),
                // #ifdef MP-WEIXIN
                this.getMnpQrcode()
                // #endif
            ])
            setTimeout(() => {
                this.drawCanvas()
            }, 100)
        },
        // 获取商品页面二维码
        getMnpQrcode() {
            return new Promise((resolve, reject) => {
                apiMnpQrCode({
                    id: this.shareId,
                    page: this.pagePath
                })
                    .then((res) => {
                        this.mnpQrcode = res.qr_code
                        resolve(res)
                    })
                    .catch(() => {
                        reject()
                    })
            })
        },
        drawCanvas() {
            this.$refs.poster.drawCanvas()
        },
        handleSuccess(val) {
            this.poster = val
            uni.hideLoading()
            this.showPoster = true
            this.showshare = false
        },
        handleFail() {
            uni.hideLoading({
                success: () => {
                    this.$toast({
                        title: '生成失败'
                    })
                }
            })
        },
        shareWx() {
            // #ifdef H5
            this.showTips = true
            this.showshare = false
            // #endif
            // #ifdef APP-PLUS
            uni.share({
                provider: 'weixin',
                scene: 'WXSceneSession',
                type: 0,
                href: this.getLink,
                title: this.config.name,
                summary: '',
                imageUrl: this.config.image,
                success: (res) => {
                    console.log('分享成功')
                },
                fail: (err) => {
                    this.$toast({
                        title: err.errMsg
                    })
                }
            })
            // #endif
        },

        async savePoster() {
            // #ifndef H5
            uni.saveImageToPhotosAlbum({
                filePath: this.poster,
                success: (res) => {
                    this.showPoster = false
                    this.$toast({
                        title: '保存成功',
                        icon: 'success'
                    })
                },
                fail: (err) => {
                    this.$toast({
                        title: '保存失败'
                    })
                    console.log(err)
                }
            })
            // #endif
            // #ifdef H5
            this.$toast({
                title: '请长按图片保存'
            })
            // #endif
        }
    }
}
</script>

<style lang="scss">
.share-popup {
    .share-tab {
        margin: 40rpx 0 140rpx;

        .share-icon {
            width: 100rpx;
            height: 100rpx;
        }
    }

    .cancel {
        height: 98rpx;
    }
}

.share-poster {
    .share-img {
        width: 640rpx;
        border-radius: 12rpx;
    }

    .save-btn {
        @include background_color();
        color: #fff;
        margin-top: 20rpx;
    }
}

.share-tips .share-arrow {
    width: 140rpx;
    height: 250rpx;
    float: right;
    margin: 15rpx 31rpx 0 0;
}
</style>
