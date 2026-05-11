<template>
	<view class="verification-list">

		<!-- Tabs -->
		<u-tabs :list="tabsList" :is-scroll="false" :height="80" :bar-style="{ top: '100%' }" :current="tabsIndex"
			@change="changeCurrentTab" active-color="#3868f9" />

		<!-- Order Lists -->
		<block v-for="(tabItem, tabIndex) in tabsList" :key="tabIndex">
			<mescroll-uni v-if="tabIndex == tabsIndex" top="80rpx" ref="mescrollRef" @init="mescrollInit" :up="{
				noMoreSize: 5,
				empty: {
					icon: '/static/images/empty/order.png',
					tip: '暂无订单~',
					fixed: true
				}
			}" @up="upCallback($event)" @down="downCallback">
				<view class="order-contain">
					<view class="order" v-for="orderItem in tabsList[tabIndex].list" :key="orderItem.id">
						<!-- Order Header -->
						<view class="order-header">
							<view class="flex user">
								<view>提货人: {{ orderItem.consignee }}，</view>
								<view>{{ orderItem.mobile }}</view>
							</view>
							<view class="order-status">
								<view class="order-status--primary" v-if="orderItem.verification_status == 0">待核销</view>
								<view class="order-status--muted" v-if="orderItem.verification_status == 1">已核销</view>
							</view>
						</view>

						<!-- Order Main -->
						<view class="order-main">

							<view class="goods" v-for="(goodsItem, goodsIndex) in orderItem.order_goods">
								<view class="goods-wrap">
									<view class="image">
										<u-image :src="goodsItem.goods_image" width="160" height="160"></u-image>
									</view>

									<view class="m-l-16 line-1">
										<!-- 商品名称 -->
										<view class="goods-name line-1">{{ goodsItem.goods_name }}</view>
										<!-- 规格数量 -->
										<view class="muted flex row-between xs m-t-20">
											<view>{{ goodsItem.spec_value_str }}</view>
											<view>x {{ goodsItem.goods_num }}</view>
										</view>
										<view class="text-right m-t-20 sm" style="color: #F2A626">
											{{ goodsItem.after_sale_status_desc }}
										</view>
									</view>
								</view>
								
							</view>
						</view>
						<view class="button" v-if="!orderItem.verification_status">
							<button @click="toVerification(orderItem.id)">核销订单</button>
						</view>
					</view>
				</view>
			</mescroll-uni>
		</block>
		<modal height="200rpx" v-model="showModal" @confirm="confirmVer">
            <view class="black nr" style="height: 200rpx;">
                确定核销该订单？
            </view>
        </modal>
		
		<modal height="100rpx" v-model="verificationDialog" @confirm="confirmVer(1)">
			<view class="black nr" style="height: 200rpx;">
				{{ verifyTips }}
			</view>
		</modal>
</view>
</template>


<script>
import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins.js";
import { apiVerificationOrderList,apiVerificationOrderConfirm } from '@/api/order'

export default {
	name: 'OrderList',
	mixins: [MescrollMixin],

	data() {
		return {
			// Tabs列表
			tabsList: [{
				sign: 0,
				name: '待核销',
				list: [],
			}, {
				sign: 1,
				name: '已核销',
				list: [],
			}],
			verifyTips: '',
			verificationDialog: false,
			tabsIndex: 0,			// Tabs索引
			showModal:false,
			// 订单状态
			orderStatus: {
				0: {
					name: '待核销',
					style: 'primary',
				},
				1: {
					name: '已核销',
					style: 'muted',
				}
			},

			code: '',						// 核销码
			showInputCode: false,			// 显示(输入核销码)：是 | 否
		}
	},

	computed: {
		// 当前Tab项
		currentTab() {
			return this.tabsList[this.tabsIndex]
		}
	},

	methods: {
		// 更改当前Tab页
		changeCurrentTab(index) {
			this.tabsIndex = index
		},
		refreshOrderData() {
			this.$nextTick(() => {
				this.$refs.mescrollRef[this.tabsIndex].mescroll?.resetUpScroll()
			})
		},

		// 上拉加载更多
		upCallback({ num, size }) {
			apiVerificationOrderList({
				type: this.tabsIndex,
				page_no: num,
				page_size: size,
			}).then(({ lists, page_size, count }) => {
				// 如果是第一页或是搜索时需手动置空列表
				if (num == 1) this.tabsList[this.tabsIndex].list = []
				this.tabsList[this.tabsIndex].list = [...this.tabsList[this.tabsIndex].list, ...lists]
				this.$refs.mescrollRef[0].mescroll.endBySize(page_size, count)
			}).catch(err => {
				console.log(err)
				this.$refs.mescrollRef[0].mescroll.endErr()
			})
		},

		//核销订单
		toVerification(id){
			this.showModal = true
			this.code = id
		},
		//确定核销
		async confirmVer(isConfirm = 0){
			const res = await apiVerificationOrderConfirm({id: this.code, confirm: isConfirm})
			if (res.code === 10) {
				this.verifyTips = res.msg
				this.verificationDialog = true
				return
			}
			this.refreshOrderData()
		}
	}
}
</script>


<style lang="scss" scoped>
.verification-list {
	display: flex;
	flex-direction: column;
	// max-height: 100vh;
	// overflow: hidden;
	padding-bottom: 200rpx;
}

.goods {
	width: 100%;
	padding: 20rpx;

	&-wrap {
		width: 100%;
		display: flex;

		.goods-name {
			color: #101010;
			font-size: $-font-size-nr;
		}

		.goods-price {
			color: #FF0000;
			font-size: $-font-size-nr;
		}

		>view {
			width: 100%;
		}

		.image {
			flex: 0;
		}
	}


}

.order-contain {
	padding: 10rpx 20rpx;
	.button {
		display: flex;
		justify-content: end;
		button {
			width: 200rpx;
			background-color: $-color-primary;
			color: #FFFFFF;
			height: 70rpx;
			border-radius: 70rpx;
		}
	}
	.order {
		padding: 0 20rpx 20rpx 20rpx ;
		margin-top: 20rpx;
		border-radius: 5px;
		background-color: #FFFFFF;

		&-header {
			display: flex;
			height: 80rpx;
			align-items: center;
			justify-content: space-between;
			padding-right: 20rpx;
			border-bottom: $-dashed-border;

			.user {
				font-size: 26rpx
			}

			.order-status {
				margin-left: auto;
				font-size: $-font-size-sm;

				&--primary {
					color: $-color-primary;
				}

				&--muted {
					color: $-color-muted;
				}
			}
		}

		&-main {
			&__spec {
				display: flex;
				justify-content: space-between;
				height: 100%;
			}
		}
	}
}

.operation {
	position: fixed;
	z-index: 99;
	left: 0;
	right: 0;
	bottom: 0;
	display: flex;
	justify-content: space-between;
	padding: 0 20rpx calc(20rpx + constant(safe-area-inset-bottom)) 20rpx;
	padding: 0 20rpx calc(20rpx + env(safe-area-inset-bottom)) 20rpx;

	&-button {
		flex: 1;
		display: flex;
		align-items: center;
		justify-content: center;
		height: 82rpx;
		border-radius: 60px;
		font-size: $-font-size-nr;
		background-color: $-color-primary;
		color: #FFFFFF;

		&:nth-child(n+2) {
			margin-left: 20rpx;
		}
	}
}
</style>
