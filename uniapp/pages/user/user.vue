<template>
	<view class="user" :style="[pageStyle]" :class="themeName">
		<view v-for="(item, index) in pagesData" :key="index">
			<template v-if="item.name=='userinfo'">
				<w-userinfo v-show="item.show" :content="item.content" :styles="item.styles" :percent="percent" :title="styles.title" />
			</template>
			<template v-if="item.name=='userorder'">
				<w-userorder v-show="item.show" :content="item.content" :styles="item.styles" />
			</template>
			<template v-if="item.name=='userserve'">
				<w-userserve v-show="item.show" :content="item.content" :styles="item.styles" />
			</template>
			<template v-if="item.name=='goodsrecom'">
				<w-goodsrecom v-show="item.show" :content="item.content" :styles="item.styles" />
			</template>
		</view>
		<tabbar />
		<notification-popup />
	</view>
</template>

<script>
	import {
		mapGetters,
		mapActions
	} from 'vuex'
	import {apiGetPage} from '@/api/store'
	import notificationPopup from '@/components/notification-popup/notification-popup.vue'
	export default {
		components: { notificationPopup },
		data() {
			return {
				styles: {},
				pagesData: [],
				percent: 0
			}
		},
		methods: {
			...mapActions(['getUser']),
			getPage() {
				apiGetPage({
					type: 3
				}).then(res => {
					const {
						common: {
							title
						},
						common,
						content
					} = res
					uni.setNavigationBarTitle({
						title
					});
					this.styles = common
					this.pagesData = content
				})
			}
		},
		computed: {
			pageStyle() {
				const {
					background_color,
					background_image,
					background_type,
				} = this.styles
				if (background_type == 0 || !background_image || !background_color) {
					return
				} 
				const style = background_type == 1 ? {
					'background-color': background_color
				}: {
					'background-image': `url(${background_image})`
				}
				return style
			}
		},
		onLoad() {
			this.getPage()
		},
		onShow() {
			this.getUser()
		},
		onPageScroll(e) {
			const top = uni.upx2px(100)
			const {
				scrollTop
			} = e
			this.percent = scrollTop / top > 1 ? 1 : scrollTop / top
		},
		onPullDownRefresh() {
			this.getPage()
			this.getUser().finally(() => {
				uni.stopPullDownRefresh();
			})
		},
	}
</script>

<style>

</style>
