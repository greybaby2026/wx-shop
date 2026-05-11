<template>
    <view class="container">
        <!--经营概况-->
        <view class="manage">
            <view class="title">经营概况</view>
            <view class="card flex flex-wrap col-top">
                <view class="item">
                    <view class="xs muted">累计营业额</view>
                    <view class="number m-t-10">{{ finance.order_sum }}</view>
                </view>
				<view class="item">
				    <view class="xs muted">累计成交订单数</view>
				    <view class="number m-t-10">{{ finance.order_num }}</view>
				</view>
				<view class="item">
				    <view class="xs muted">累计售后退款金额</view>
				    <view class="number m-t-10">{{finance.after_sale_sum}}</view>
				</view>
            </view>
        </view>
		<view class="manage">
		    <view class="title">用户概况</view>
		    <view class="card flex flex-wrap col-top">
		        <view class="item">
		            <view class="xs muted">用户总资产</view>
		            <view class="number m-t-10">{{ finance.user_total_assets }}</view>
		        </view>
				<view class="item">
				    <view class="xs muted">用户可用余额</view>
				    <view class="number m-t-10">{{ finance.user_money_sum }}</view>
				</view>
				<view class="item">
				    <view class="xs muted">用户可提现金额</view>
				    <view class="number m-t-10">{{ finance.user_earnings_sum }}</view>
				</view>
		    </view>
		</view>
		<view class="manage">
		    <view class="title">分销概况</view>
		    <view class="card flex flex-wrap col-top">
		        <view class="item">
		            <view class="xs muted">今日入账佣金</view>
		            <view class="number m-t-10">{{ finance.distribution_data.today_rebated_commission }}</view>
		        </view>
				<view class="item">
				    <view class="xs muted">待结算佣金</view>
				    <view class="number m-t-10">{{ finance.distribution_data.cumulative_unrefunded_commission }}</view>
				</view>
				<view class="item">
				    <view class="xs muted">累计已入账佣金</view>
				    <view class="number m-t-10">{{ finance.distribution_data.cumulative_rebated_commission }}</view>
				</view>
				<view class="item">
				    <view class="xs muted">今日新增待结算佣金</view>
				    <view class="number m-t-10">{{ finance.distribution_data.today_unrefunded_commission }}</view>
				</view>
		    </view>
		</view>
    </view>
</template>

<script>
	import { userFinance } from '@/api/user.js'
export default {
    name: 'finance',
    data() {
        return {
			finance: {
				distribution_data: {}
			}
        }
    },
	methods: {
		async getUserFinance() {
			const data = await userFinance()
			this.finance = data
		}
	},
	onLoad() {
		this.getUserFinance()
	}
}
</script>

<style lang="scss" scoped>
.container {
    padding: 28rpx 20rpx 0rpx 20rpx;

    .manage {}
}

.title {
    font-size: 32rpx;
    font-weight: 500;
	margin-bottom: 28rpx;
}

.card {
    background: white;
    border-radius: 14rpx;
	padding: 30rpx 0 10rpx;
	margin-bottom: 48rpx;
    .item {
        display: flex;
        flex-direction: column;
		width: 50%;
		margin-bottom: 30rpx;
		padding:  0 20rpx;
		.number {
			font-size: 50rpx;
			word-wrap: break-word;
			width: 100%;
		}
    }
}
</style>