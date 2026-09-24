<template>
	<view class="indicator" :style="{bottom: `${bottom}rpx`}" v-if="length > 1">
		<view v-if="safeType==1||safeType==2" class="indicator-content" :class="{
	                fillet:safeType==1,
	                circle:safeType==2,
	            }" :style="{
	                'text-align':align
	            }">
			<text v-for="(item,index) in length" :key="index" class="indicator-item"
				:style="[current==index ? {'background-color':color }: {}]"></text>
		</view>
		<view v-if="safeType==3" :style="{
	                'text-align':align,
	            }">
			<text class="indicator-number">{{current + 1}}/{{length}}</text>
		</view>
	</view>
</template>

<script>
	export default {
		props: {
			current: {
				type: Number,
				default: 0
			},
			length: {
				type: Number,
				default: 0
			},
			type: {
				type: Number,
				default: 1
			},
			align: {
				type: String,
				default: 'left'
			},
			color: {
				type: String,
				default: '#FF2C3C'
			},
			bottom: {
				type: Number,
				default: 15
			}
			},
			computed: {
			// 兜底：type 越界（非 1/2/3，如后端配置 0 或 4）时按「圆点」样式渲染，
			// 否则整块指示器会渲染为空白，用户看不到当前页位置
			safeType() {
				const type = Number(this.type);
				return [1, 2, 3].includes(type) ? type : 2;
			}
			}
			};
</script>

<style lang="scss" scoped>
	.indicator {
		position: absolute;
		bottom: 15rpx;
		z-index: 2;
		width: 100%;
		padding: 0 40rpx;
		box-sizing: border-box;

		.indicator-item {
			display: inline-block;
			background: rgba(0, 0, 0, 0.3);

			&:not(:last-of-type) {
				margin-right: 10rpx;
			}
		}

		.fillet {
			.indicator-item {
				width: 24rpx;
				height: 4rpx;
			}
		}

		.circle {
			.indicator-item {
				width: 12rpx;
				height: 12rpx;
				border-radius: 50%;
			}
		}

		.indicator-number {
			display: inline-block;
			min-width: 36rpx;
			height: 36rpx;
			padding: 0 12rpx;
			color: #fff;
			line-height: 36rpx;
			background: rgba(0, 0, 0, 0.3);
			border-radius: 36rpx;
			font-size: 24rpx;
		}
	}
</style>
