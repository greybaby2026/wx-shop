<template>
	<view class="h5-pay-query">
		<!-- 页面状态 -->
		<page-status :status="pageStatus">
			<template #loading>
				<view class="flex-col col-center">
					<u-icon class="search-icon" name="search" size="48" :color="themeColor" />
					<text class="m-t-40">支付查询中...</text>
				</view>
			</template>

			<template #error>
				<u-empty :text="pageErrorMsg" src="/static/images/empty/order.png" :icon-size="280" />
			</template>
		</page-status>
	</view>
</template>


<script>
	import { apiPayStatus } from '@/api/app'
	import { PageStatusEnum, PayWayEnum } from '@/utils/enum'

	export default {
		name: 'H5PayQuery',

		data() {
			return {
				// 页面状态
				pageStatus: PageStatusEnum['LOADING'],
				pageErrorMsg: '',
			}
		},

		onShow() {
			const { pay_way } = this.$Route.query
			
			setTimeout(() => {
				switch(pay_way * 1) {
					case PayWayEnum['WECHAT']: 
						window.history.go(-2)
						break
					case PayWayEnum['ALIPAY']: 
						window.history.go(-1)
						break
				}
			}, 0.8 * 1000)
		}
	}
</script>


<style lang="scss" scoped>
	.h5-pay-query {
		.search-icon {
			//调用动画
			transition: 1s;
			animation: shakeX 5s linear infinite;
		}

		//添加旋转的动画
		@keyframes shakeX {
			0%,
			to {
				-webkit-transform: translateZ(0);
				transform: translateZ(0)
			}

			10%,
			30%,
			50%,
			70%,
			90% {
				-webkit-transform: translate3d(-10px, 0, 0);
				transform: translate3d(-10px, 0, 0)
			}

			20%,
			40%,
			60%,
			80% {
				-webkit-transform: translate3d(10px, 0, 0);
				transform: translate3d(10px, 0, 0)
			}
		}
	}
</style>
