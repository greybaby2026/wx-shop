<template>
	<view class="month-bill" :class="themeName">
		<mescroll-body ref="mescrollRef" @init="mescrollInit" @down="downCallback" @up="upCallback"
			:up="upOption">
			<view v-for="(item, index) in orderList" :key="index">
				<view class="bill-time flex sm">
					{{item.date}}
				</view>
				<view class="show-panel flex">
					<view class="panel-item flex-col col-center">
						<price :content="item.total_money" mainSize="36rpx" minorSize="26rpx"/>
						<view class="lighter label m-t-10">预估收入</view>
					</view>
					<view class="panel-item flex-col col-center">
						<view class="xxl">{{item.order_goods_num}}</view>
						<view class="lighter label m-t-10">成交笔数</view>
					</view>
					<view class="panel-item flex-col col-center">
						<router-link :to="{path: '/bundle/pages/monthly_bill_detail/monthly_bill_detail', query: {year: item.year, month: item.month}}">
							<view class="flex lighter">
								查看详情
								<u-icon name="arrow-right" size="28rpx" color="#666666" />
							</view>
						</router-link>
					</view>
				</view>
			</view>
		</mescroll-body>
	</view>
</template>

<script>
	import {
		apiDistributionMonthBill
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

		onLoad: function(options) {
			
		},

		methods: {
			upCallback(page) {
				let pageNum = page.num; // 页码, 默认从1开始
				let pageSize = page.size; // 页长, 默认每页10条
				apiDistributionMonthBill({
					page_size: pageSize,
					page_no: pageNum,
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
	.month-bill {
		.bill-time {
			padding: 20rpx;
			line-height: 34rpx;
		}

		.show-panel {
			background-color: white;
			padding: 36rpx 0 26rpx;
		}

		.show-panel {
			.panel-item {
				flex: 1;
				line-height: 34rpx;
			}
		}
	}
</style>
