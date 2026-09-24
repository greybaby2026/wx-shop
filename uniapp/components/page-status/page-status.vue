<template>
	<view class="page-status" v-if="status !== PageStatusEnum['NORMAL']">
		<!-- Loading -->
		<template v-if="status === PageStatusEnum['LOADING']">
			<slot name="loading">
				<u-loading :size="60" mode="flower" />
			</slot>
		</template>
		<!-- Error -->
		<template v-if="status === PageStatusEnum['ERROR']">
			<!-- 默认内容：使用方未提供 #error 插槽时给出明确提示，避免「纯白全屏」 -->
			<slot name="error">
				<u-empty text="加载失败，请稍后重试" src="/static/images/empty/shop.png" :icon-size="280" />
			</slot>
		</template>
		<!-- Empty -->
		<template v-if="status === PageStatusEnum['EMPTY']">
			<!-- 默认内容：使用方未提供 #empty 插槽时给出明确提示，避免「纯白全屏」 -->
			<slot name="empty">
				<u-empty text="暂无数据" src="/static/images/empty/order.png" :icon-size="280" />
			</slot>
		</template>
	</view>
</template>


<script>
	import { PageStatusEnum } from '@/utils/enum'
	
	export default {
		name: 'PageStatus',
		
		props: {
			status: {
				type: String,
				default: PageStatusEnum['LOADING']
			},
		},
		
		computed: {
			PageStatusEnum: () => PageStatusEnum,
		}
	}
</script>


<style>
	.page-status {
		position: fixed;
		top: 0;
		left: 0;
		width: 100vw;
		height: 100vh;
		z-index: 900;
		display: flex;
		justify-content: center;
		align-items: center;
		background-color: #FFFFFF;
	}
</style>
