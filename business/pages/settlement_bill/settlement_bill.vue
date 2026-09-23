<template>
	<view class="settlement">
		<!-- Tabs -->
		<u-tabs :list="tabsList" :is-scroll="false" :height="80" :bar-style="{ top: '100%' }" :current="tabsIndex"
			@change="changeCurrentTab" active-color="#3868f9" />

		<mescroll-uni top="80rpx" ref="mescrollRef" @init="mescrollInit" :up="{
			noMoreSize: 5,
			empty: {
				icon: '/static/images/empty/money.png',
				tip: '暂无结算账单',
				fixed: true
			}
		}" @up="upCallback" @down="downCallback">
			<view class="content">
				<!-- 汇总: 本店应收 / 应付 / 待确认账单数 -->
				<view class="summary">
					<view class="summary-item">
						<view class="label">应收合计</view>
						<view class="value receive">¥{{ stat.receive_total }}</view>
					</view>
					<view class="summary-item">
						<view class="label">应付合计</view>
						<view class="value pay">¥{{ stat.pay_total }}</view>
					</view>
					<view class="summary-item">
						<view class="label">待确认账单</view>
						<view class="value">{{ stat.wait_confirm_count }}</view>
					</view>
				</view>

				<!-- 平台账号提示 -->
				<view class="platform-tip" v-if="isPlatformAccount">
					当前为平台账号，不参与门店结算，因此没有账单数据。如需查看门店账单，请让管理员在后台为该账号设置「所属门店」。
				</view>

				<!-- 账单列表 -->
				<view class="bill" v-for="item in list" :key="item.id" @click="toDetail(item.id)">
					<view class="bill-header">
						<view class="sn">{{ item.sn }}</view>
						<view class="status" :class="'status--' + item.status">{{ item.status_desc }}</view>
					</view>

					<view class="row">
						<text class="label">账期</text>
						<text>{{ item.period_type_desc }}账期 {{ item.start_date }} ~ {{ item.end_date }}</text>
					</view>
					<view class="row">
						<text class="label">{{ item.role == 'pay' ? '收款方' : '付款方' }}</text>
						<text>{{ item.other_store_name || '-' }}</text>
					</view>
					<view class="row">
						<text class="label">核销笔数</text>
						<text>{{ item.order_count }} 笔</text>
					</view>

					<view class="bill-footer">
						<text class="role-tag" :class="item.role == 'receive' ? 'tag--receive' : 'tag--pay'">
							{{ item.role_desc }}
						</text>
						<text class="amount">¥{{ item.amount }}</text>
					</view>
				</view>
			</view>
		</mescroll-uni>
	</view>
</template>

<script>
import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins.js";
import { apiSettlementList } from "@/api/settlement";

export default {
	name: "SettlementBill",
	mixins: [MescrollMixin],
	data() {
		return {
			// 账单状态: 0待确认 1已确认 2已付款 (sign 为空时后端不传该条件)
			tabsList: [
				{ name: "全部", sign: "" },
				{ name: "待确认", sign: 0 },
				{ name: "已确认", sign: 1 },
				{ name: "已付款", sign: 2 },
			],
			tabsIndex: 0,
			list: [],
			stat: {
				receive_total: "0.00",
				pay_total: "0.00",
				wait_confirm_count: 0,
			},
			isPlatformAccount: false,
		};
	},
	methods: {
		// 切换状态Tab: 清空并重新加载
		changeCurrentTab(index) {
			if (index === this.tabsIndex) return;
			this.tabsIndex = index;
			this.list = [];
			this.$nextTick(() => {
				this.mescroll && this.mescroll.resetUpScroll();
			});
		},

		upCallback({ num, size }) {
			apiSettlementList({
				status: this.tabsList[this.tabsIndex].sign,
				page_no: num,
				page_size: size,
			})
				.then(({ lists, count, page_size, stat, is_platform_account }) => {
					if (num == 1) this.list = [];
					this.list = [...this.list, ...lists];
					if (stat) this.stat = stat;
					this.isPlatformAccount = !!is_platform_account;
					this.mescroll.endBySize(page_size, count);
				})
				.catch(() => {
					this.mescroll.endErr();
				});
		},

		toDetail(id) {
			this.$Router.push({
				path: "/pages/settlement_detail/settlement_detail",
				query: { id },
			});
		},
	},
};
</script>

<style lang="scss" scoped>
.settlement {
	display: flex;
	flex-direction: column;
}

.content {
	padding: 20rpx;
}

.summary {
	display: flex;
	background: #ffffff;
	border-radius: 14rpx;
	padding: 30rpx 0;
	margin-bottom: 20rpx;

	.summary-item {
		flex: 1;
		display: flex;
		flex-direction: column;
		align-items: center;

		.label {
			font-size: 24rpx;
			color: #999999;
		}

		.value {
			margin-top: 12rpx;
			font-size: 34rpx;
			font-weight: 500;
			color: #333333;

			&.receive {
				color: #3868f9;
			}

			&.pay {
				color: #f2a626;
			}
		}
	}
}

.platform-tip {
	background: #fff8e6;
	color: #b8860b;
	font-size: 24rpx;
	line-height: 36rpx;
	padding: 20rpx;
	border-radius: 14rpx;
	margin-bottom: 20rpx;
}

.bill {
	background: #ffffff;
	border-radius: 14rpx;
	padding: 24rpx 20rpx;
	margin-bottom: 20rpx;

	.bill-header {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding-bottom: 16rpx;
		border-bottom: 1rpx dashed #eeeeee;

		.sn {
			font-size: 28rpx;
			font-weight: 500;
			color: #333333;
		}

		.status {
			font-size: 26rpx;

			&--0 {
				color: #f2a626;
			}

			&--1 {
				color: #3868f9;
			}

			&--2 {
				color: #999999;
			}
		}
	}

	.row {
		display: flex;
		font-size: 26rpx;
		color: #333333;
		margin-top: 16rpx;

		.label {
			width: 150rpx;
			color: #999999;
		}
	}

	.bill-footer {
		display: flex;
		align-items: center;
		justify-content: space-between;
		margin-top: 20rpx;
		padding-top: 16rpx;
		border-top: 1rpx solid #f5f5f5;

		.role-tag {
			font-size: 22rpx;
			padding: 4rpx 16rpx;
			border-radius: 8rpx;
		}

		.amount {
			font-size: 32rpx;
			font-weight: 500;
			color: #333333;
		}
	}
}

.tag--receive {
	color: #3868f9;
	background: rgba(56, 104, 249, 0.1);
}

.tag--pay {
	color: #f2a626;
	background: rgba(242, 166, 38, 0.12);
}
</style>
