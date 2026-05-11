<template>
	<view 
	  :class="['address-card', {'address-card--empty': !hasContent}]" 
	  :style="{'border-radius': borderRadius, 'background-color': backgroundColor}"
	>
		<!-- Sign -->
		<u-icon name="map" size="48" />
		
		<!-- Content -->
		<view class="content">
			<!-- 地址信息 -->
			<view v-if="hasContent">
				<view class="bold">
					<slot name="header"></slot>
				</view>
				<view class="m-t-10 lighter">
					<slot name="main"></slot>
				</view>
				<view class="m-t-10 xs muted">
					<slot name="footer"></slot>
				</view>
			</view>
			<!-- 不存在地址 -->
			<view v-else>{{ placeholder }}</view>
		</view>
		
		<!-- Action Sign -->
		<u-icon v-if="action" name="arrow-right" size="30" />
	</view>
</template>

<script>
	
	/**
	 * @description 地址卡片
	 * @property {Boolean} name 名称
	 * @property {Boolean} action suffix图标 (默认值：true)
	 * @property {Boolean} hasContent 是否存在内容 (默认值：false)
	 * @property {String} placeholder 提示文字 (默认值：设置收货地址)
	 * @property {String} borderRadius 圆角
	 * @property {String} backgroundColor 背景色 (默认值：#FFFFFF)
	 * @example <address-card :has-content="isEmpty" :action="true" />
	 */
	export default {
		name: 'AddressCard',
		
		props: {			
			// suffix图标
			action: {
				type: Boolean,
				default: false
			},
			
			// 是否存在内容
			hasContent: {
				type: Boolean,
				default: false
			},
			
			// 提示文字
			placeholder: {
				type: String,
				default: '设置收货地址'
			},
			
			// 圆角
			borderRadius: {
				type: String,
				default: 'unset',
			},
			
			// 背景色
			backgroundColor: {
				type: String,
				default: '#FFFFFF'
			}
		},
	}
</script>

<style lang="scss" scoped>
	.address-card {
		display: flex;
		align-items: flex-start;
		min-height: 164rpx;
		padding: 20rpx;
		
		&--empty {
			align-items: center;
		}
	}
	
	
	
	.content {
		flex: 1;
		display: flex;
		flex-direction: column;
		margin: 0 20rpx;
		font-size: $-font-size-md;
	}
</style>
