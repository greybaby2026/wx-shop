<template>
    <view>
        <l-painter
            ref="painter"
            :css="{
                width: '600rpx',
                height: '960rpx',
                'border-radius': '20rpx',
                overflow: 'hidden'
            }"
            custom-style="position: fixed; left: 200%"
        >
            <l-painter-image
                :src="getBgImgSrc"
                :css="{
                    position: 'absolute',
                    width: '600rpx',
                }"
            />
            <l-painter-view
				v-if="showPosterCon"
                :css="{
                    position: 'absolute',
                    bottom: 0,
                    'background-color': `rgba(255, 255, 255, ${getConfig.style.opacity / 100})`,
                    width: '100%',
                    height: '290rpx',
                    'border-radius': '20rpx'
                }"
            >
                <l-painter-view
                    :css="{
                        position: 'absolute',
                        top: '30rpx',
                        left: '30rpx',
                        display: 'flex',
                        'align-items': 'center',
                    }"
                >
                    <l-painter-image
                        v-if="getConfig.show.user_avtar"
                        :src="options.avatar"
                        :css="{
                            width: '80rpx',
                            height: '80rpx',
                            'border-radius': '50%',
                        }"
                    />
                    <l-painter-text
                        v-if="getConfig.show.user_name"
                        :text="options.nickname"
                        :css="{
                            'margin-left': '20rpx',
                            color: getConfig.style.user_name,
                            fontSize: '34rpx',
                            'line-clamp': 1,
                            'font-weight': 'bold',
                            width: '220rpx',
                        }"
                    />
                </l-painter-view>
                <l-painter-text
                    v-if="getConfig.show.slogan"
                    :text="getConfig.slogan"
                    :css="{
                        position: 'absolute',
                        color: getConfig.style.slogan_text,
                        top: '140rpx',
                        left: '30rpx',
                        fontSize: '28rpx',
                    }"
                />
                <l-painter-text
                    v-if="getConfig.show.slogan_code"
                    :text="`邀请码：${options.code}`"
                    :css="{
                        position: 'absolute',
                        color: getConfig.style.slogan_code,
                        top: '200rpx',
                        left: '30rpx',
                        fontSize: '28rpx',
                    }"
                />
                <l-painter-view
                    :css="{
                        position: 'absolute',
                        top: '45rpx',
                        left: '360rpx',
                    }"
                    v-if="getConfig.show.qrcode"
                >
                    <l-painter-view css="padding-left: 30rpx;">
                        <!-- #ifdef H5 || APP-PLUS -->
                        <l-painter-qrcode
                            css="width: 180rpx; height: 180rpx;"
                            :text="options.link"
                        >
                        </l-painter-qrcode>
                        <!--  #endif -->
                        <!-- #ifdef MP -->
                        <l-painter-image
                            :src="options.qr_code"
                            css="width: 180rpx; height: 180rpx;"
                        />
                        <!--  #endif -->
                    </l-painter-view>
                </l-painter-view>
            </l-painter-view>
        </l-painter>
    </view>
</template>

<script>
export default {
    name: "share-poster",
    props: {
        options: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        return {};
    },
    computed: {
        getConfig() {
            return (
                this.options.config || {
                    show: {},
                    style: {},
                }
            );
        },
        getBgImgSrc() {
            const { background_url, background_type } = this.getConfig;
            return background_type == 1
                ? require("@/bundle/static/images/invitation_poster.png")
                : background_url;
        },
        showPosterCon() {
            const { show } = this.getConfig
			console.log(show)
            return Object.keys(show).some((key) => show[key]);
        },
    },
    methods: {
        drawCanvas() {
            this.$refs.painter.canvasToTempFilePathSync({
                fileType: "png",
                // 如果返回的是base64是无法使用 saveImageToPhotosAlbum，需要设置 pathType为url
                pathType: "url",
                quality: 1,
                success: (res) => {
                    this.$emit("success", res.tempFilePath);
                },
            });
        },
    },
};
</script>

<style></style>
