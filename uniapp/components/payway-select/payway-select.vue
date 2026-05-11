<template>
	<view class="payway-select">
		<!-- Title -->
		<view class="payway-select-title">支付方式</view>
		<!-- Payway -->
		<u-radio-group v-model="payway">
			<view class="payway">
				<view 
				  class="payway-item"
				  v-for="(item, index) in paywayList"
				  :key="index"
				  @click="changePayway(index)"
				>
					<u-image 
					  :src="item.image" 
					  width="48" 
					  height="48" 
					  mode="scaleToFill" 
					/>
					<view class="payway-item-content">
						<text class="payway-item-content-name">{{ item.name }}</text>
						<text class="payway-item-content-tips">{{ item.label }}</text>
					</view>
					<u-radio shape="circle" :name="index" :active-color="themeColor" />
				</view>
			</view>
		</u-radio-group>
	</view>
</template>


<script>
	export default {
		name: 'PaywaySelect',
		
		data() {
			return {
				payway: '',
				paywayList: [
					{
						name: '微信支付',
						label: '微信快捷支付',
						image: 'https://img2.baidu.com/it/u=700829083,2195114188&fm=26&fmt=auto&gp=0.jpg'
					},
					{
						name: '微信支付',
						label: '微信快捷支付',
						image: 'https://img2.baidu.com/it/u=700829083,2195114188&fm=26&fmt=auto&gp=0.jpg'
					},
					{
						name: '微信支付',
						label: '微信快捷支付',
						image: 'https://img2.baidu.com/it/u=700829083,2195114188&fm=26&fmt=auto&gp=0.jpg'
					}
				]
			}
		},
		
		methods: {
			// 更改支付方式
			changePayway(value) {
				this.$set(this, 'payway', value)
				this.$emit('change', value)
			},
		}
	}
</script>


<style lang="scss" scoped> 
	.payway-select {
		padding: 0 20rpx;
		border-radius: 7px;
		background-color: #FFFFFF;
		
		&-title {
			height: 70rpx;
			line-height: 70rpx;
			font-size: $-font-size-nr;
			color: $-color-lighter;
		}
	}
	
	.payway {
		width: 100%;
		
		&-item {
			display: flex;
			align-items: center;
			height: 120rpx;
			&:nth-child(n+2) {
				border-top: $-dashed-border;
			}
			
			&-content {
				flex: 1;
				display: flex;
				flex-direction: column;
				margin-left: 16rpx;
				
				&-name {
					font-size: $-font-size-sm;
					color: $-color-black;
				}
				
				&-tips {
					font-size: $-font-size-xs;
					color: $-color-muted;
				}
			}
		}
	}
</style>
