<template>
	<view class="style-six">
		<left-aside />
		<view class="right-warp">
			<scroll-view style="height: 100%" scroll-y="true" scroll-with-animation="true">
				<view v-if="banner">
					<w-banner :content="banner.content" :styles="banner.styles" />
				</view>
				<view class="m-20 bg-white" style="border-radius: 10rpx;" v-if="twoLists.length">
					<img-list :lists="twoLists" :line-num="3" />
				</view>
			</scroll-view>
		</view>
	</view>
</template>

<script type="text/javascript">
	import leftAside from './left-aside.vue'
	import imgList from './img-list.vue'
	import category from './category.js'
	export default {
		mixins: [category],
		components: {
			leftAside,
			imgList
		},
		props: {


		},
		data() {
			return {

			}
		},
		methods: {

		},
		computed: {
			twoLists() {
				const {
					lists,
					selectIndex
				} = this
				return lists[selectIndex] ? lists[selectIndex].sons || [] : []
			},
		}
	};
</script>
<style lang="scss" scoped>
	.style-six {
		flex: 1;
		min-width: 0;
		min-height: 0;
		/* 需给flex:1的元素加上最小高,否则内容超过会溢出容器 (如:小程序Android真机) */
		display: flex;
		height: 100%;
		width: 750rpx;
		// 右边
		.right-warp {
			flex: 1;
			min-width: 0;
		}
	}
</style>
