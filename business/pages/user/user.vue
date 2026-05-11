<template>
	<view class="user">
		<u-sticky offset-top="0" h5-nav-height="0" bg-color="transparent">
			<u-navbar :is-back="false" title="个人中心" :title-bold="true" :is-fixed="false" :border-bottom="false"
				:background="{ background: 'rgba(256,256, 256,' + 1 + ')' }" :title-color="1 > 0.5 ? '#000' : '#fff'">
			</u-navbar>
		</u-sticky>
		<view class="flex-1" style="min-height: 0">
			<mescroll-body ref="mescrollRef" height="100%" @init="mescrollInit" @down="downCallback" @up="upCallback"
				:up="{ use: false }">
				<view class="user-info">
					<!-- Header -->
					<view class="header">
						<!-- <router-link to="/bundle/pages/shop_setting/shop_setting"> -->
						<view class="info flex col-top">
							<view class="flex flex-1">
								<image class="logo m-r-20 flex-none"
									:src="isLogin ? info.logo : '/static/images/icon_portrait'" />
								<template v-if="isLogin">
									<view class="name">
										<view class="lg white bold line-2">{{ info.name }}</view>
										<!-- switch -->
										<!-- <view class="m-t-14">
																																								            <b-switch @change="isRunFunc" size="36" v-model="info.is_run"></b-switch>
																																								        </view> -->
										<!--账号-->
										<view class="account">
											<view>当前账号：{{ user_info.name }}</view>
										</view>
									</view>
								</template>
								<template v-if="!isLogin">
									<view class="xl">点击登录</view>
								</template>
							</view>
							<view>
								<router-link to="/bundle/pages/user_profile/user_profile">
									<image class="user-setting" src="/static/images/icon_my_setting.png" />
								</router-link>
							</view>
						</view>
						<!-- </router-link> -->
					</view>

					<!-- Section  -->
					<view class="section">
						<!-- Wallet -->
						<!-- <router-link to="/bundle/pages/user_wallet/user_wallet">
									<view class="wallet bg-white flex row-between">
										<view class="normal">
											<view class="xs">可提现金额</view>
											<view class="wallet-price m-t-20">{{
												info.wallet || 0
											}}</view>
										</view>
										<view class="wallet-btn br60 flex row-center white sm">
											去提现
										</view>
									</view>
								</router-link> -->

						<!-- Nav -->
						<view class="nav bg-white m-t-20">
							<view class="title md bold normal">功能服务</view>

							<view class="nav-item flex flex-wrap">
								<button v-for="(item, index) in menuList" :key="index"
									:open-type="item.link_type == 2 ? 'contact' : ''"
									class="item flex-col col-center m-b-20" style="width: 25%" @tap="menuJump(item)"
									hover-class="none">
									<image class="nav-icon" :src="item.image"></image>
									<view class="sm nav-name">{{ item.name }}</view>
								</button>
							</view>
						</view>

						<!-- Logout -->
						<view class="logout-btn nd bold lighter bg-white flex row-center" @click="logout">
							退出登录
						</view>

						<!-- Edition -->
						<view class="m-20 muted xs flex row-center">版本号v3.12</view>
					</view>
				</view>

				<u-toast ref="uToast" />
			</mescroll-body>
		</view>
</view>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import { apiLogout } from "@/api/user";
import { apiSetShopInfo } from "@/api/store";
import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins";
export default {
	mixins: [MescrollMixin],
	data() {
		return {
			info: {},
			user_info: {},
			menuList: [
				{
					name: "核销订单",
					link: "/pages/verification_order/verification_order",
					image: "/static/images/verification.png",
				},
				{
					name: "商品管理",
					link: "/pages/goods_manage/goods_manage",
					image: "/static/images/goods_management.png",
				},
				{
					name: "订单管理",
					link: "/pages/order_manage/order_manage",
					image: "/static/images/order_management.png",
				},
				{
					name: "财务概况",
					link: "/pages/finance/finance",
					image: "/static/images/financial_overview.png",
				},
				{
					name: "数据统计",
					link: "/bundle/pages/shop_data/shop_data",
					image: "/static/images/data_statistics.png",
				},
				{
					name: "店铺设置",
					link: "/bundle/pages/shop_setting/shop_setting",
					image: "/static/images/store_setting.png",
				},
				{
					name: "个人设置",
					link: "/bundle/pages/user_profile/user_profile",
					image: "/static/images/personal_setting.png",
				},
			],
		};
	},
	methods: {
		...mapActions(["getUser"]),
		async downCallback() {
			let res = await this.getUser();
			let user_data = JSON.parse(
				JSON.stringify(this.$store.state.app.user_info)
			);
			this.info = JSON.parse(JSON.stringify(res));
			for (let key in user_data) {
				this.user_info[key] = user_data[key];
			}
			this.mescroll.endSuccess(0, false);
		},

		menuJump(item) {
			if (item.name == "商品管理" || item.name == '订单管理') {
				uni.reLaunch({
					url: item.link,
				});
			} else {
				this.$Router.push(item.link);
			}
		},

		logout() {
			//  退出登录
			apiLogout().then((res) => {
				this.$store.commit("logout");
				this.$toast({
					title: "退出成功",
				});
				setTimeout(() => {
					uni.reLaunch({
						url: "/pages/login/login",
					});
				}, 500);
			});
		},

		async isRunFunc(event) {
			console.log(event);
			const res = await apiSetShopInfo({
				is_run: event.value ? 1 : 0,
			});
			this.$refs.uToast.show({
				title: "设置成功",
				type: "success",
			});
		},
	},

	onShow() {
		this.getUser().then((res) => {
			this.info = JSON.parse(JSON.stringify(res));
		});
	},
};
</script>

<style lang="scss">
page {
	height: 100%;
	padding: 0;
}

.user {
	height: 100%;
	display: flex;
	flex-direction: column;

	&-info {
		position: relative;

		.header {
			padding: 40rpx 20rpx 150rpx 20rpx;
			background-color: $-color-primary;

			// background-image: url(../../static/images/my_topbg.png);
			// background-size: 100% 300rpx;
			// background-repeat: no-repeat;
			.logo {
				height: 100rpx;
				width: 100rpx;
				border-radius: 50%;
				overflow: hidden;
			}

			.name {
				text-align: left;
				margin-bottom: 5rpx;

				.account {
					color: white;
					font-size: small;
					margin-top: 10rpx;
				}
			}

			.user-setting {
				width: 58rpx;
				height: 58rpx;
			}
		}

		.section {
			padding: 0 20rpx;
			width: 100%;
			margin-top: -80rpx;

			.wallet {
				padding: 50rpx 40rpx;
				border-radius: 14rpx;

				&-price {
					font-size: 60rpx;
				}

				&-btn {
					width: 160rpx;
					height: 60rpx;
					background-color: $-color-primary;
				}
			}

			// 导航
			.nav {
				border-radius: 14rpx;

				.title {
					padding: 24rpx 0;
					margin: 0 30rpx;
					border-bottom: $-solid-border;
				}

				&-item {
					padding: 26rpx 0 0;

					.item {
						width: 25%;
						border-radius: 0;
					}

					.nav-icon {
						width: 56rpx;
						height: 56rpx;
						margin-top: 16rpx;
					}

					.nav-name {
						width: 80%;
						height: 40rpx;
						margin: 0 24rpx;
						margin-bottom: 24rpx;
						text-align: center;
					}
				}
			}

			.logout-btn {
				height: 88rpx;
				border-radius: 100rpx;
				margin: 40rpx 24rpx;
			}
		}
	}
}
</style>
