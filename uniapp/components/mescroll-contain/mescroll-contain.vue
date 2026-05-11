<template>
	<!-- Scroll列表 -->
	<mescroll-uni ref="mescrollRef" :top="top" :height="height" :up="upOption" :down="downOption"
		:bottom="bottom" @init="mescrollInit" @down="downCallback" @up="upCallback">
		<slot></slot>
	</mescroll-uni>
</template>


<script>
	import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins'
	export default {
		name: 'MescrollContain',
		mixins: [MescrollMixin],

		props: {
			height: {
				type: String,
			},

			top: {
				type: String,
			},

			bottom: {
				type: String,
			},


			upOption: {
				type: Object,
				default: () => ({})
			},

			downOption: {
				type: Object,
				default: () => ({})
			},
		},
		methods: {
			//下拉刷新的回调
			downCallback() {
				this.mescroll && this.mescroll.resetUpScroll(); // 重置列表为第一页 (自动执行 page.num=1, 再触发upCallback方法 )
			},
			// 上拉加载更多
			upCallback(page) {
				const pageNum = page.num
				const pageSize = page.size

				this.$emit('upcallback', {
					num: pageNum,
					size: pageSize
				})
			},

			// 请求失败,隐藏加载状态
			endErr() {
				this.mescroll.endErr()
			},

			// 是否有下一页
			endBySize(page_size, count) {
				this.mescroll.endBySize(page_size, count)
			},

			// 是否有下一页
			endSuccess(len, count) {
				this.mescroll.endBySize(len, count)
			},
		}
	}
</script>
