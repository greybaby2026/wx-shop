<template>
	<widget-root :styles="styles">
		<div class="video" :style="[{
					'border-radius': `${$px2rpx(styles.border_radius_top)}rpx ${$px2rpx(styles.border_radius_top)}rpx ${$px2rpx(styles.border_radius_bottom)}rpx ${$px2rpx(styles.border_radius_bottom)}rpx`,
				}, ]" :class="{
                style1: content.proportion == 1,
                style2: content.proportion == 2,
                style3: content.proportion == 3,
            }">
			<image v-if="showPoster" class="poster" mode="aspectFill" :src="$getImageUri(content.poster)"></image>
			<image v-if="showPoster" class="play-icon" src="/static/images/icon_play.png" mode="widthFix" @tap="paly" />
			<video :id="videoId" class="video-container" :src="getVideoUrl" @pause="handlePause" @play="handlePaly">
			</video>
		</div>
	</widget-root>
</template>

<script type="text/javascript">
	import { getNonDuplicateID } from '@/utils/tools'
	export default {
		props: {
			content: {
				type: [Object, Array]
			},
			styles: {
				type: [Object, Array]
			},
		},
		data() {
			return {
				videoId: '',
				showPoster: true
			}
		},
		methods: {
			paly() {
				this.videoContext.play()
			},
			handlePause() {
				this.showPoster = true
			},
			handlePaly() {
				this.showPoster = false
			}
		},
		computed: {
			videoStyle() {
				const {
					content
				} = this
				return content.poster ? {
					'background-image': `url(${this.$getImageUri(content.poster)})`
				} : {}
			},
			getVideoUrl() {
				const {
					content: {
						url,
						video_type
					}
				} = this
				return video_type == 1 ? this.$getImageUri(url) : url
			}
		},
		created() {
			this.videoId = getNonDuplicateID();
		},
		mounted() {
			this.videoContext = uni.createVideoContext(this.videoId, this)
		},
		
	};
</script>

<style lang="scss" scoped>
	.video {
		position: relative;

		// background-repeat: no-repeat;
		// background-size: cover;
		// background-position: 50%;
		// background-image: url(../../../../assets/images/default_video.png);
		// position: relative;
		// &::after {
		//     content: "";
		//     position: absolute;
		//     top: 0;
		//     left: 0;
		//     right: 0;
		//     bottom: 0;
		//     background: url(../../../../assets/images/icon_play.png);
		//     background-position: 50%;
		//     background-repeat: no-repeat;
		//     background-size: 60px;
		//     z-index: 2;
		// }
		&.style1 {
			height: 422rpx;
		}

		&.style2 {
			height: 572px;
		}

		&.style3 {
			height: 750px;
		}

		.poster, .video-container, {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
		}
		
		.poster {
			z-index: 999;
		}

		.play-icon {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			width: 100rpx;
			height: 100rpx;
			z-index: 999;
		}
	}
</style>
