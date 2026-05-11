<template>
	<view class="indicator" :style="{bottom: `${bottom}rpx`}" v-if="length > 1">
		<view v-if="type==1||type==2" class="indicator-content" :class="{
	                fillet:type==1,
	                circle:type==2,
	            }" :style="{
	                'text-align':align
	            }">
			<text v-for="(item,index) in length" :key="index" class="indicator-item"
				:style="[current==index ? {'background-color':color }: {}]"></text>
		</view>
		<view v-if="type==3" :style="{
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
