<template>
    <view class="pages" :class="themeName">
        <view class="invite-fans flex-col col-center">
            <image :src="imgPath" mode="widthFix" class="poster"></image>
            <invite-poster ref="poster" :options="options" @success="handleSuccess" />
            <view class="bg-white footer flex-1">
                <view class="m-b-40">
                    <view class="m-b-10 sm lighter">我的邀请码</view>
                    <view class="flex row-between">
                        <view class="font-size-44">{{ options.code }}</view>
                        <view class="sm m-r-30 copy-btn" @tap="$copy(options.code)">点击复制</view>
                    </view>
                </view>
                <!-- #ifndef H5  -->
                <button class="save-btn br60" size="lg" @tap="saveImageToAlbum">保存到相册</button>
                <!-- #endif -->
                <!-- #ifdef H5 -->
                <button class="save-btn br60" size="lg">长按保存到相册</button>
                <!-- #endif -->
            </view>
        </view>
        <loading-view v-show="loading"></loading-view>
    </view>
</template>

<script>
import { apiMnpQrCode } from '@/api/app'
import { baseURL, basePath } from '@/config/app'
import { apiInvitePosterConfig } from '@/api/app'
import invitePoster from '@/bundle/components/invite-poster/invite-poster.vue'
import { mapActions, mapGetters } from 'vuex'

export default {
    components: {
        invitePoster
    },
    data() {
        return {
            imgPath: '',
            loading: true,
            options: {}
        }
    },

    async onLoad() {
        this.getPoster()
    },

    computed: {
        ...mapGetters(['userInfo', 'appConfig', 'shareConfig'])
    },

    methods: {
        async getPoster() {
            try {
                const res = await Promise.all([
                    apiInvitePosterConfig(),
                    // #ifdef MP-WEIXIN
                    apiMnpQrCode()
                    // #endif
                ])
                this.options = res[0]
                // #ifdef MP-WEIXIN
                this.options.qr_code = res[1]?.qr_code
                // #endif
                this.options.link = `${baseURL}${basePath}?invite_code=${this.options.code}`
                this.$refs.poster.drawCanvas()
            } catch (e) {
                this.$toast({
                    title: e
                })
            }
        },
        saveImageToAlbum() {
            // #ifndef H5
            uni.saveImageToPhotosAlbum({
                filePath: this.imgPath,
                success: (res) => {
                    this.$toast({
                        title: '保存成功'
                    })
                },
                fail: (err) => {
                    this.$toast({
                        title: '保存失败'
                    })
                }
            })
            // #endif
            // #ifdef H5
            this.$toast({
                title: '请长按图片保存'
            })
            // #endif
        },
        handleSuccess(val) {
            this.imgPath = val
            this.loading = false
        }
    }
}
</script>
<style lang="scss">
page {
    padding: 0;
}

.invite-fans {
    min-height: 100vh;
    overflow: hidden;

    .poster {
        width: 600rpx;
        margin: 40rpx 0;
    }

    .footer {
        padding: 30rpx;
        width: 100%;
    }

    .copy-btn {
        @include font_color();
    }

    .save-btn {
        color: #fff;
        @include background_color();
    }
}
</style>
