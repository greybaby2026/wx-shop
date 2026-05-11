<template>
	<view :class="themeName">
		
		<view class="section-card">
			<!-- Section -->
			<view class="flex row-between m-b-20 t-border-b">
				<!-- Text -->
				<view class="flex" style="max-width: 410rpx;">
					<!-- 置顶tips -->
					<view class="tips" v-if="is_tips">
						置顶
					</view>
					<!-- Title -->
					<view class="nr bold line-1">{{name}}</view>
				</view>
				<!-- Time -->
				<view class="xs muted">{{create_time}}</view>
			</view>

			<view class="flex row-between m-t-20 m-b-20">
				<!-- Article -->
				<view class="sm lighter line-2">{{desc}}</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name: 'SectionCard',
		props: {
			// 标题
			name: {
				type: String,
				default: ''
			},
			// 简介
			desc: {
				type: String,
				default: ''
			},
			// 图片
			image: {
				type: String,
				default: ''
			},
			// 创建时间
			create_time: {
				type: String,
				default: ''
			},
			// views
			views: {
				type: String,
				default: ''
			},

			is_tips: {
				type: Boolean,
				default: false
			},
		}
	}
</script>


<style lang="scss" scoped>
	.section-card {
		margin: 20rpx 20rpx 0;
		padding: 20rpx;
		border-radius: 14rpx;
		background-color: #FFFFFF;

		.tips {
			color: #FFFFFF;
			font-size: $-font-size-xxs;
			min-width: 68rpx;
			height: 36rpx;
			border-radius: 4rpx;
			@include background_linear(90deg, 0%, 100%);
			text-align: center;
			margin-right: 10rpx;
		}
		
		.t-border-b {
			border-bottom: 1rpx solid #e5e5e5;
			padding-bottom: 20rpx;
		}
	}
</style>
