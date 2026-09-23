<template>
	<view class="user" :style="[pageStyle, themeCssVars]" :class="themeName">
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
		<!-- 门店核销入口(仅门店核销员可见) -->
		<view v-if="isVerifier" class="verification-entry-wrap">
			<view class="verification-entry flex row-between col-center" @click="goVerification">
				<view class="flex-col">
					<text class="bold">门店核销</text>
					<text class="entry-tips">{{ verifierStoreName || '当前门店' }} · 扫描或输入提货码核销订单</text>
				</view>
				<u-icon name="arrow-right" color="#999999" size="32"></u-icon>
			</view>
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
	import {apiVerificationIsVerifier} from '@/api/order'
	import notificationPopup from '@/components/notification-popup/notification-popup.vue'
	export default {
		components: { notificationPopup },
		data() {
			return {
				styles: {},
				pagesData: [],
				percent: 0,
				isVerifier: false, //是否为门店核销员
				verifierStoreName: '' //核销员所属门店名称
			}
		},
		methods: {
			...mapActions(['getUser']),
			// 查询当前用户是否为门店核销员(决定核销入口显隐)
			getVerifierAuth() {
				if (!this.$store.getters.token) {
					this.isVerifier = false
					this.verifierStoreName = ''
					return
				}
				apiVerificationIsVerifier()
					.then((res) => {
						this.isVerifier = !!(res && res.is_verifier)
						this.verifierStoreName = (res && res.store_name) || ''
					})
					.catch(() => {
						this.isVerifier = false
					})
			},
			// 进入门店核销
			goVerification() {
				this.$Router.push('/bundle/pages/verification_list/verification_list')
			},
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
			this.getVerifierAuth()
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
.verification-entry-wrap {
	padding: 20rpx 24rpx;
}

.verification-entry {
	background-color: #ffffff;
	border-radius: 16rpx;
	padding: 28rpx 24rpx;
}

.verification-entry .entry-tips {
	margin-top: 10rpx;
	font-size: 24rpx;
	color: #999999;
}
</style>
