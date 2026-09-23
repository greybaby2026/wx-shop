<template>
	<view class="detail">
		<!-- 账单概览 -->
		<view class="card">
			<view class="card-header">
				<text class="sn">{{ bill.sn }}</text>
				<text class="status" :class="'status--' + bill.status">{{ bill.status_desc }}</text>
			</view>

			<view class="amount-line">
				<text class="role-tag" :class="bill.role == 'receive' ? 'tag--receive' : 'tag--pay'">
					{{ bill.role_desc }}
				</text>
				<text class="amount">¥{{ bill.amount }}</text>
			</view>

			<view class="row"><text class="label">账期类型</text><text>{{ bill.period_type_desc }}账期</text></view>
			<view class="row"><text class="label">账期区间</text><text>{{ bill.start_date }} ~ {{ bill.end_date }}</text></view>
			<view class="row"><text class="label">付款门店</text><text>{{ bill.pay_store_name || '-' }}</text></view>
			<view class="row"><text class="label">收款门店</text><text>{{ bill.receive_store_name || '-' }}</text></view>
			<view class="row"><text class="label">核销笔数</text><text>{{ bill.order_count }} 笔</text></view>
			<view class="row"><text class="label">生成时间</text><text>{{ bill.create_time || '-' }}</text></view>
			<view class="row"><text class="label">确认时间</text><text>{{ bill.confirm_time || '-' }}</text></view>
			<view class="row"><text class="label">付款时间</text><text>{{ bill.pay_time || '-' }}</text></view>

			<view class="tip">
				结算账单由平台后台「门店结算中心」生成并线下付款，门店端仅作查看，金额与后台完全一致。
			</view>
		</view>

		<!-- 商品行明细 -->
		<view class="section-title">核销明细（{{ details.length }} 笔）</view>

		<view class="card" v-for="item in details" :key="item.id">
			<view class="card-header">
				<text class="sn">订单 {{ item.order_sn }}</text>
				<text class="source-tag" :class="{ 'tag--danger': item.cost_source == 3 }">
					{{ item.cost_source_desc }}
				</text>
			</view>

			<view class="goods" v-for="(goods, gi) in item.detail" :key="gi">
				<view class="goods-name line-1">{{ goods.goods_name }}</view>
				<view class="goods-fee">
					¥{{ goods.supply_price }} × {{ goods.goods_num }} = ¥{{ goods.amount }}
				</view>
			</view>

			<view class="row"><text class="label">核销时间</text><text>{{ item.create_time || '-' }}</text></view>

			<view class="bill-footer">
				<text class="muted xs">本单结算金额</text>
				<text class="amount sm">¥{{ item.amount }}</text>
			</view>
		</view>

		<view class="empty" v-if="!details.length">暂无明细</view>
	</view>
</template>

<script>
import { apiSettlementDetail } from "@/api/settlement";

export default {
	name: "SettlementDetail",
	data() {
		return {
			id: 0,
			bill: {},
			details: [],
		};
	},
	onLoad(options) {
		this.id = options.id || 0;
		this.getDetail();
	},
	methods: {
		async getDetail() {
			if (!this.id) return;
			// 失败提示由 request 拦截器统一 toast, 此处仅需终止流程
			const data = await apiSettlementDetail({ id: this.id }).catch(() => null);
			if (!data) return;
			this.bill = data.bill || {};
			this.details = data.details || [];
		},
	},
};
</script>

<style lang="scss" scoped>
.detail {
	padding: 20rpx;
}

.card {
	background: #ffffff;
	border-radius: 14rpx;
	padding: 24rpx 20rpx;
	margin-bottom: 20rpx;

	.card-header {
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

		.source-tag {
			font-size: 24rpx;
			color: #999999;
		}
	}

	.amount-line {
		display: flex;
		align-items: center;
		justify-content: space-between;
		margin-top: 20rpx;

		.role-tag {
			font-size: 22rpx;
			padding: 4rpx 16rpx;
			border-radius: 8rpx;
		}

		.amount {
			font-size: 40rpx;
			font-weight: 500;
			color: #333333;
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

	.tip {
		margin-top: 24rpx;
		padding: 16rpx;
		background: #f7f8fa;
		border-radius: 10rpx;
		font-size: 24rpx;
		line-height: 34rpx;
		color: #999999;
	}

	.goods {
		margin-top: 20rpx;

		.goods-name {
			font-size: 28rpx;
			color: #333333;
		}

		.goods-fee {
			margin-top: 8rpx;
			font-size: 24rpx;
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

		.amount {
			font-size: 32rpx;
			font-weight: 500;
			color: #333333;
		}
	}
}

.section-title {
	font-size: 28rpx;
	font-weight: 500;
	color: #333333;
	margin: 10rpx 0 20rpx;
}

.empty {
	text-align: center;
	font-size: 26rpx;
	color: #999999;
	padding: 60rpx 0;
}

.tag--receive {
	color: #3868f9;
	background: rgba(56, 104, 249, 0.1);
}

.tag--pay {
	color: #f2a626;
	background: rgba(242, 166, 38, 0.12);
}

.tag--danger {
	color: #ff4d4f;
}
</style>
