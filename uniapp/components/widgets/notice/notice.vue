<template>
	<widget-root :styles="styles" v-if="content.data.length">
		<router-link to="/bundle/pages/store_notice/store_notice">
			<view class="notice" :style="{
			        'background-color': styles.bg_color, 
			        'border-radius': `${$px2rpx(styles.border_radius)}rpx`
			    }">
				<view class="flex">
					<image class="flex-none" style="height: 34rpx;width: auto;"
						:src="content.icon_type == 1 ? require('@/static/images/notice_icon.png') : $getImageUri(content.icon)"
						mode="heightFix"></image>
					<view class="left-line" :style="{'background-color': styles.line_color}"></view>
					<view class="flex-1">
						<swiper autoplay="true" style="height: 76rpx; flex: 1" vertical="true" circular="true"
							:interval="3000">
							<swiper-item v-for="(item, index) in content.data" :key="index" class="flex">
								<view class="flex-none">
									<u-tag v-if="content.show_tag && index == 0" shape="circle" text="最新" size="mini"
										:color="themeColor" :border-color="themeColor" mode="plain" />
								</view>
								<view class="text-swiper m-l-10 line-1" :style="{'color': styles.color}">{{item.name }}
								</view>
							</swiper-item>
						</swiper>
					</view>
					<u-icon name="arrow-right" size="28"></u-icon>
				</view>
				<view class="shade"></view>
			</view>
		</router-link>
	</widget-root>
</template>

<script type="text/javascript">
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

			}
		},
		methods: {

		},
		computed: {

		}
	};
</script>

<style lang="scss" scoped>
	.notice {
		padding: 0 20rpx;
		position: relative;

		.left-line {
			height: 28rpx;
			width: 1px;
			background-color: #e5e5e5;
			margin: 0 20rpx;
		}

		.shade {
			position: absolute;
			left: 0;
			top: 0;
			width: 100%;
			height: 100%;
			z-index: 100;
		}

		.new-tag {
			color: #ff2c3c;
			padding: 0 10rpx;
			margin-right: 10rpx;
			font-size: 22rpx;
			border-radius: 14rpx;
			background: transparent;
			border: 1px solid #ff2c3c;
		}
	}
</style>
