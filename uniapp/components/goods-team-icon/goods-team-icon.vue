<template>
	<view :class="themeName" style="display: inline-block;">
		<view :class="['goods-team-icon', style[theme]]">
			<u-icon class="goods-team-icon__icon" name="account" size="28" />
			<text class="goods-team-icon__num">{{ count }}</text>
		</view>
	</view>
</template>

<script>
	export default {
		name: 'GoodsTeamIcon',
		
		props: {
			// 主题: primary | normal
			theme: {
				type: String,
				default: 'primary',
			},
			
			// 拼团人数
			count: {
				type: Number | String,
				required: true,
			}
		},
		
		data() {
			return {
				style: {
					'primary': 'goods-team-icon--primary',
					'normal': 'goods-team-icon--normal'
				}
			}
		},
	}
</script>

<style lang="scss" scoped>
	.goods-team-icon {
		display: flex;
		height: 34rpx;
		border-width: 1px;
		border-style: solid;
		border-radius: 2px;
		
		&__icon {
			display: flex;
			align-items: center;
			justify-content: center;
			width: 42rpx;
		}
		
		&__num {
			padding: 0 10rpx;
			font-size: $-font-size-xxs;
			
			&::after {
				content: "人团";
			}
		}
		
		&--primary {
			@include border_color();
			
			.goods-team-icon {
				&__icon {
					@include background_color();
					color: #FFFFFF;
				}
				
				&__num {
					@include font_color();
				}				
			}
		}
		
		&--normal {
			border-color: #FFFFFF;
			
			.goods-team-icon {
				&__icon {
					@include font_color();
					background-color: #FFFFFF;
				}
				
				&__num {
					color: #FFFFFF;
				}				
			}
		}
	}
</style>
