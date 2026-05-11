<template>
	<widget-root :styles="styles">
		<view class="title" :style="{
			'background-color': styles.bg_color,
			'border-radius': `${$px2rpx(styles.border_radius_top)}rpx ${$px2rpx(styles.border_radius_top)}rpx ${$px2rpx(styles.border_radius_bottom)}rpx ${$px2rpx(styles.border_radius_bottom)}rpx`
			}">
			<view class="title-style1 flex-col col-center" v-if="content.style == 1">
				<view class="title-content" :style="{
			                    'color': styles.title_color,
			                    'font-size': `${$px2rpx(styles.title_font_size)}rpx`
			                }">
					{{content.title}}
				</view>
				<view v-if="!content.hidden_subtitle" class="title-subtitle" :style="{
			                    'color': styles.subtitle_color,
			                    'font-size': `${$px2rpx(styles.subtitle_font_size)}rpx`
			                }">
					{{content.subtitle}}
				</view>
			</view>
			<view class="title-style2 flex" v-if="content.style == 2">
				<view class="title-content m-r-10" :style="{
			                    'color': styles.title_color,
			                    'font-size': `${$px2rpx(styles.title_font_size)}rpx`
			                }">
					{{content.title}}
				</view>
				<view class="flex-1">
					<view v-if="!content.hidden_subtitle" class="title-subtitle line-1" :style="{
					                'color': styles.subtitle_color,
					                'font-size': `${$px2rpx(styles.subtitle_font_size)}rpx`
					            }">
						{{content.subtitle}}
					</view>
				</view>
				<view class="title-more" @tap="navigate" v-if="content.show_more" :style="{
					                'color': styles.more_color,
					            }">
					{{content.more_title}}
					<u-icon name="arrow-right"></u-icon>
				</view>
			</view>
		</view>
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
			navigate() {
				const { path, params} = this.content.link
				if(!path) return
				this.$Router.push({
					path: path,
					query: params
				})
			}
		},
		computed: {

		}
	};
</script>

<style lang="scss" scoped>
	.title {
		padding: 24rpx 30rpx;

		.title-content {
			font-size: 32rpx;
			font-weight: 500;
		}

		.title-subtitle {
			font-size: 24rpx;
		}

		.title-style1 {
			text-align: center;

			.title-content {
				display: inline-flex;
				align-items: center;

				&::before,
				&::after {
					content: '';
					display: inline-block;
					width: 98rpx;
					height: 2rpx;
					margin: 0 20rpx;
					background: #dcdfe6;
				}
			}
		}

		.title-more {
			font-size: 24rpx;
		}
	}
</style>
