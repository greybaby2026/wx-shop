<template>
	<view class="swiper-wrap">
		<swiper class="swiper" ref="swiper" :autoplay="autoplay" :circular="circular" :interval="interval"
			:duration="duration" @change="changeSwiper">
			<block v-for="(item, index) in urls" :key="index">
				<swiper-item @tap="previewSwiperItem(index)">
					<view v-if="item['type'] === 'video'" class="video-wrap">
						<j-video width="750rpx" height="750rpx" :url="item.url" :poster="videoCover" />
					</view>

					<u-image width="750" height="750" :src="item.uri" mode="scaleToFill" />
				</swiper-item>
			</block>
		</swiper>

		<view class="dots black sm bg-white br60">{{ currentSwiperIndex + 1 }}/{{ urls.length || 0 }}</view>
	</view>
</template>

<script>
export default {
	data() {
		return {
			currentSwiperIndex: 0,
			showPlay: true,
			showControls: false,
			autoplay: true,
			videoContext: null,
		}
	},


	props: {
		// 图片链接列表
		images: {
			type: Array,
			default: () => [],
		},

		// 视频链接
		video: {
			type: [String, Boolean],
			default: false,
		},

		// 视频封面
		videoCover: {
			type: [String, Boolean],
			default: false
		},

		// 衔接滑动，即播放到末尾后重新回到开头
		circular: {
			type: Boolean,
			default: true,
		},

		// 自动切换时间间隔
		interval: {
			type: Number,
			default: 3 * 1000,
		},

		// 滑动动画时长
		duration: {
			type: Number,
			default: 500,
		},
	},


	computed: {
		urls() {
			const images = this.images.map(item => ({ uri: item, type: 'image' }))

			return this.video
				? [{ uri: this.video, type: 'video' }, ...images]
				: images
		}
	},


	mounted() {
		this.videoContext = uni.createVideoContext('video', this)
	},


	methods: {
		// 切换Swiper项
		changeSwiper(e) {
			this.currentSwiperIndex = e.detail.current
		},

		// 视频异常
		videoErrorCallback(err) {
			throw new Error(err)
		},

		// 查看Swiper项
		previewSwiperItem(current) {
			const type = this.urls[current].type

			// #ifdef MP-WEIXIN
			wx.previewMedia({
				current,
				sources: this.urls,
			})
			//#endif

			// #ifdef H5 || APP-PLUS
			const index = this.video ? current - 1 : current
			type === "video"
				? this.videoContext.requestFullScreen()
				: uni.previewImage({ index, urls: this.images })
			//#endif
		},

		// 视频播放
		playVideo() {
			// this.videoContext.play()
		},

		// 当视频进入和退出全屏时触发
		fullscreenchange(e) {
			const { fullScreen } = e.detail
			this.showPlay = !fullScreen
			this.showControls = !!fullScreen
			console.log(fullScreen)
			!!fullScreen && this.videoContext.play()
			!fullScreen && this.videoContext.pause()
		}

	},


}
</script>

<style scoped>
.swiper-wrap {
	width: 100%;
	height: 750rpx;
	position: relative;
}

.swiper-wrap .swiper {
	width: 100%;
	height: 100%;
	position: relative;
}

.swiper-wrap .swiper .slide-image {
	width: 100%;
	height: 100%;
}

.swiper-wrap .dots {
	position: absolute;
	right: 24rpx;
	bottom: 24rpx;
	display: flex;
	height: 34rpx;
	padding: 0 15rpx;
}

.swiper-wrap .video-wrap {
	width: 100%;
	height: 100%;
	position: relative;
	overflow: hidden;
}

.swiper-wrap .video {
	width: 100%;
	height: 100%;
}

.swiper-wrap .icon-play {
	width: 90rpx;
	height: 90rpx;
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	z-index: 999;
}
</style>
