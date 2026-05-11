<template>
	<mescroll-uni ref="mescrollRef" top="80rpx" @init="mescrollInit" @down="downCallback" @up="upCallback"
		:down="downOption" :up="upOption">
		<view class="spread-order">
			<view class="order-lists">
				<view v-for="(item, index) in orderList" :key="item.sn" class="order-item bg-white m-t-20">
					<view class="order-header flex row-between">
						<view>订单编号：{{item.sn}}</view>
						<view class="static sm"
							:style="{color: item.status == 1 ? '#F95F2F' : item.status == 2 ? '#00c735' : '#999999'}">
							{{item.status_desc}}
						</view>
					</view>
					<view class="order-content flex">
						<view>
							<u-image width="180rpx" height="180rpx" border-radius="6px" :src="item.image" />
						</view>
						<view class="order-goods-info m-l-20">
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
									<price :content="item.total_pay_price" color="#333333" mainSize="28rpx" minorSize="28rpx"/>
								</view>
							</view>
							<view class="pre-income muted sm m-t-10">
								<text class="m-r-10">预估收益</text>
								<price :content="item.earnings" mainSize="28rpx" minorSize="28rpx"/>
							</view>
						</view>
					</view>
					<view class="order-footer flex row-between">
						<view class="time muted sm">{{item.create_time}}</view>
					</view>
				</view>
			</view>
		</view>
	</mescroll-uni>
</template>

<script>
	import {
		DistributionOrderEnum
	} from "@/utils/enum";
	import {
		apiDistributionOrder
	} from "@/api/user";
	import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins";
	import MescrollMoreItemMixin from "@/components/mescroll-uni/mixins/mescroll-more-item";
	export default {
		mixins: [MescrollMixin, MescrollMoreItemMixin],
		data() {
			return {
				orderList: [],
				downOption: {
					auto: false // 不自动加载 (mixin已处理第一个tab触发downCallback)
				},
				upOption: {
					auto: false, // 不自动加载
					noMoreSize: 4, //如果列表已无数据,可设置列表的总数量要大于半页才显示无更多数据;避免列表数据过少(比如只有一条数据),显示无更多数据会不好看; 默认5
					empty: {
						icon: '/static/images/empty/order.png',
						tip: '暂无分销订单~', // 提示
						fixed: true
					}
				},
				confirmDialog: false
			};
		},

		props: {
			type: {
				type: Number | String,
				default: DistributionOrderEnum['ALL']
			}
		},

		methods: {
			upCallback(page) {
				let pageNum = page.num; // 页码, 默认从1开始
				let pageSize = page.size; // 页长, 默认每页10条
				let {
					type,
				} = this;
				apiDistributionOrder({
					page_size: pageSize,
					page_no: pageNum,
					status: type
				}).then((data) => {
					if (page.num == 1) this.orderList = [];
					const curPageData = data.lists;
					const curPageLen = curPageData.length;
					this.orderList = this.orderList.concat(curPageData);
					this.mescroll.endSuccess(curPageLen, data.count);
				})
			},

		}
	};
</script>
<style lang="scss">
	.spread-order {
		.order-lists {
			padding: 0 20rpx;

			.order-item {
				border-radius: 14rpx;

				.order-header {
					padding: 20rpx 30rpx;
					border-bottom: $-solid-border;

					.status {
						color: #F95F2F;
					}
				}

				.order-content {
					padding: 20rpx 30rpx 20rpx 20rpx;
					border-bottom: $-solid-border;

					.order-goods-info {
						text-align: left;
						flex: 1;

						.name {
							line-height: 36rpx;
						}

						.pre-income {
							margin-top: 8rpx;
						}
					}
				}

				.order-footer {
					padding: 20rpx 30rpx 20rpx 20rpx;
				}
			}
		}
	}
</style>
