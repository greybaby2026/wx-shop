<template>
    <view>
        <l-painter
            ref="painter"
            :css="{
                width: '640rpx',
                height: '960rpx',
                'border-radius': '10rpx',
                background: '#fff',
                overflow: 'hidden'
            }"
            @fail="handleFail"
            custom-style="position: fixed; left: 200%"
        >
            <l-painter-image
                v-if="getConfig.background_type == 2 && getConfig.id != 3"
                :src="getConfig.background_url"
                :css="{
                    position: 'absolute',
                    width: '640rpx'
                }"
            />
            <l-painter-view
                v-if="getConfig.background_type"
                :css="{
                    position: 'absolute'
                }"
            >
                <!-- getConfig.id == 3? options.goods.poster? options.goods.poster: options.goods.image: options.goods.image -->
                <l-painter-image
                    v-if="getConfig.show.goods_img || getConfig.id == 3"
                    :src="poster_img ? poster_img : options.goods.image"
                    :css="{
                        position: 'absolute',
                        top: getConfig.id == 1 ? '120rpx' : 0,
                        left: getConfig.id == 1 ? '40rpx' : 0,
                        'object-fit': 'cover',
                        'object-position': 'center',
                        width: getConfig.id == 1 ? '560rpx' : '640rpx',
                        height:
                            getConfig.id == 1 ? '560rpx' : getConfig.id == 2 ? '640rpx' : '960rpx'
                    }"
                />
                <l-painter-view
                    :css="{
                        position: 'absolute',
                        left: '40rpx',
                        top: '20rpx',
                        display: 'flex',
                        'align-items': 'center'
                    }"
                >
                    <l-painter-image
                        v-if="getConfig.show.user_avtar"
                        :src="options.avatar"
                        :css="{
                            width: '72rpx',
                            height: '72rpx',
                            'border-radius': '50%'
                        }"
                    />
                    <l-painter-text
                        v-if="getConfig.show.user_name"
                        :text="`来自${options.nickname}的分享`"
                        :css="{
                            color: getConfig.style.user_name,
                            'line-clamp': 1,
                            'margin-left': '18rpx',
                            width: '400rpx'
                        }"
                    />
                </l-painter-view>

                <l-painter-view
                    :css="{
                        position: 'absolute',
                        top: getConfig.id == 1 ? '700rpx' : '770rpx',
                        left: getConfig.qrcode_align == 2 ? '40rpx' : '230rpx',
                        'vertical-align': 'bottom',
                        lineHeight: '1.4em'
                    }"
                >
                    <l-painter-view
                        v-if="getConfig.show.goods_sale_price && getConfig.id != 3"
                        :css="{
                            color: getConfig.style.goods_sale_price,
                            display: 'inline-block'
                        }"
                    >
                        <l-painter-text
                            text="￥"
                            :css="{
                                fontSize: '28rpx'
                            }"
                        />
                        <l-painter-text
                            v-if="price.prev"
                            :text="price.prev"
                            :css="{
                                fontSize: '42rpx'
                            }"
                        />
                        <l-painter-text
                            v-if="price.next"
                            :text="price.next"
                            :css="{
                                fontSize: '28rpx'
                            }"
                        />
                    </l-painter-view>
                    <l-painter-text
                        v-if="getConfig.show.goods_origin_price && getConfig.id != 3"
                        :text="marketPrice"
                        :css="{
                            fontSize: '28rpx',
                            'vertical-align': 'bottom',
                            'padding-left': '10rpx',
                            'text-decoration': 'line-through',
                            color: getConfig.style.goods_origin_price
                        }"
                    />
                </l-painter-view>
                <l-painter-text
                    v-if="getConfig.show.goods_name && getConfig.id != 3"
                    :css="{
                        position: 'absolute',
                        top: getConfig.id == 1 ? '780rpx' : '660rpx',
                        left: getConfig.qrcode_align == 2 || getConfig.id == 2 ? '40rpx' : '230rpx',
                        'line-clamp': 2,
                        lineHeight: '1.5em',
                        fontSize: '30rpx',
                        width: getConfig.id == 1 ? '380rpx' : '560rpx',
                        color: getConfig.style.goods_name
                    }"
                    :text="getGoods.name"
                >
                </l-painter-text>
                <l-painter-view
                    v-if="getConfig.show.qrcode"
                    :css="{
                        position: 'absolute',
                        top: getConfig.id == 1 || getConfig.id == 3 ? '730rpx' : '770rpx',
                        left: getConfig.qrcode_align == 2 ? '440rpx' : '40rpx'
                    }"
                >
                    <!-- #ifdef H5 || APP-PLUS -->
                    <l-painter-qrcode
                        css="width: 150rpx; height: 150rpx;background-color: white;padding:10rpx"
                        :text="link"
                    >
                    </l-painter-qrcode>
                    <!--  #endif -->
                    <!-- #ifdef MP -->
                    <l-painter-image :src="qrcode" css="width: 160rpx; height: 160rpx;" />
                    <!--  #endif -->
                </l-painter-view>
                <l-painter-text
                    v-if="getConfig.show.qrcode_title"
                    text="长按识别二维码"
                    :css="{
                        position: 'absolute',
                        top: getConfig.id == 1 || getConfig.id == 3 ? '900rpx' : '870rpx',
                        left:
                            getConfig.id == 1 || getConfig.id == 3
                                ? getConfig.qrcode_align == 1
                                    ? '40rpx'
                                    : '440rpx'
                                : getConfig.qrcode_align == 1
                                ? '230rpx'
                                : '40rpx',
                        fontSize: '24rpx',
                        color: getConfig.style.qrcode_title
                    }"
                />
            </l-painter-view>
        </l-painter>
    </view>
</template>

<script>
export default {
    name: 'share-poster',
    props: {
        options: {
            type: Object,
            default: () => ({})
        },
        qrcode: {
            type: [String],
            default: ''
        },
        link: {
            type: String,
            default: ''
        },
        type: {
            type: String,
            default: 'goods'
        },
        poster_img: {
            type: String,
            default: ''
        }
    },
    data() {
        return {}
    },
    computed: {
        price() {
            let { min_price } = this.getGoods
            if (min_price == undefined) return {}
            let price = String(parseFloat(min_price)).split('.')
            return {
                prev: price[0],
                next: price[1] ? `.${price[1]}` : ''
            }
        },
        marketPrice() {
            return `￥${parseFloat(this.getGoods.min_lineation_price)}`
        },
        getConfig() {
            return (
                this.options.config || {
                    show: {},
                    style: {}
                }
            )
        },
        getGoods() {
            return this.options.goods || {}
        }
    },
    methods: {
        handleFail() {
            this.$emit('fail')
        },
        drawCanvas() {
            // 生成图片
            this.$refs.painter.canvasToTempFilePathSync({
                fileType: 'png',
                // 如果返回的是base64是无法使用 saveImageToPhotosAlbum，需要设置 pathType为url
                pathType: 'url',
                quality: 1,
                success: (res) => {
                    this.$emit('success', res.tempFilePath)
                }
            })
        }
    }
}
</script>

<style></style>
