<template>
	<view class="monthly-bill-detail" :class="themeName">
		<mescroll-body ref="mescrollRef" @init="mescrollInit" @down="downCallback" @up="upCallback" :up="upOption">
			<view class="order-container">
				<view v-for="(item, index) in orderList" :key="index" class="order-item bg-white m-t-20">
					<view class="order-header flex row-between">
						<view>订单编号：{{item.sn}}</view>
					</view>
					<view class="order-content flex">
						<view>
							<u-image width="180rpx" height="180rpx" border-radius="6px" :src="item.image" />
						</view>
						<view class="order-goods-info flex-1 m-l-20">
							<view class="name sm line-2">{{item.goods_name}}</view>
							<view class="flex row-between m-t-6">
								<view class="xs muted">
									<text class="m-r-10">
										数量
									</text>
									<text class="normal">{{item.goods_num}}</text>
								</view>
								<view class="xs">
									<text class="muted m-r-10">付款金额</text>
									<price :content="item.total_pay_price" color="#333333" mainSize="28rpx" minorSize="28rpx" />
								</view>
							</view>
							<view class="pre-income muted sm m-t-10">
								<text class="m-r-10">预估收益</text>
								<price :content="item.earnings" mainSize="28rpx" minorSize="28rpx" />
							</view>
						</view>
					</view>
					<view class="order-footer flex row-between">
						<view class="time muted">{{item.create_time}}</view>
						<view class="static sm" :style="{color: item.status == 1 ? '#F95F2F' : item.status == 2 ? '#00c735' : '#999999'}">
							{{item.status_desc}}
						</view>
					</view>
				</view>
			</view>
		</mescroll-body>
	</view>
</template>

<script>
	// +----------------------------------------------------------------------
	// | LikeShop100%开源免费商用电商系统
	// +----------------------------------------------------------------------
	// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
	// | 开源版本可自由商用，可去除界面版权logo
	// | 商业版本务必购买商业授权，以免引起法律纠纷
	// | 禁止对系统程序代码以任何目的，任何形式的再发布
	// | Gitee下载：https://gitee.com/likemarket/likeshopv2
	// | 访问官网：https://www.likemarket.net
	// | 访问社区：https://home.likemarket.net
	// | 访问手册：http://doc.likemarket.net
	// | 微信公众号：好象科技
	// | 好象科技开发团队 版权所有 拥有最终解释权
	// +----------------------------------------------------------------------
	// | Author: LikeShopTeam
	// +----------------------------------------------------------------------
	import {
		apiDistributionMonthDetail
	} from "@/api/user";
	import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins";

	export default {
		mixins: [MescrollMixin],
		data() {
			return {
				orderList: [],
				upOption: {
					empty: {
						icon: '/static/images/empty/order.png',
						tip: '暂无数据~', // 提示
						
					}
				},
			};
		},

		onLoad() {
			const {
				month,
				year
			} = this.$Route.query
			this.year = year;
			this.month = Number(month)
			uni.setNavigationBarTitle({
				title: this.month + '月账单明细'
			})
		},


		methods: {
			upCallback(page) {
				let pageNum = page.num; // 页码, 默认从1开始
				let pageSize = page.size; // 页长, 默认每页10条
				let {
					year,
					month
				} = this;
				apiDistributionMonthDetail({
					page_size: pageSize,
					page_no: pageNum,
					year: year,
					month: month
				}).then((data) => {
					if (page.num == 1) this.orderList = [];
					const curPageData = data.lists;
					const curPageLen = curPageData.length;
					this.orderList = this.orderList.concat(curPageData);
					this.mescroll.endSuccess(curPageLen, data.count);
				}).catch(() => {
					this.mescroll.endErr()
				})
			},

		}
	};
</script>
<style lang="scss">
	.monthly-bill-detail {
		.order-container {
			padding: 0 20rpx;

			.order-item {
				border-radius: 14rpx;

				.order-header {
					padding: 20rpx 30rpx;
					border-bottom: $-solid-border;

					.guide-shop-btn {
						background: linear-gradient(80deg, #F95F2F 0%, #FF2C3C 100%);
						border-radius: 4rpx;
						width: 134rpx;
						height: 42rpx;
					}
				}

				.order-content {
					padding: 20rpx 30rpx 20rpx 20rpx;
					border-bottom: $-solid-border;
				}

				.order-footer {
					padding: 20rpx 30rpx 20rpx 20rpx;
				}
			}
		}
	}
</style>
