<template>
	<view class="verification-detail">
		<view class="order-contain">
			<view class="order">
				<!-- Order Header -->
				<view class="order-header">		
					<view class="order-sn">联系人: {{ orderInfo.contact }}</view>
					<view :class="['order-status', 'order-status--primary']">{{ orderInfo.verification_status_desc }}</view>
				</view>
				
				<!-- 取货/归属门店 -->
				<view class="order-shop" v-if="orderInfo.selffetch_shop_name || orderInfo.belong_store_name">
					<view class="xs muted">取货门店：{{ orderInfo.selffetch_shop_name || '-' }}</view>
					<view class="xs muted m-t-6">归属门店：{{ orderInfo.belong_store_name || '未绑定' }}</view>
					<view class="xs cross-tip m-t-6" v-if="orderInfo.is_cross_store">跨店订单：与归属门店不一致，请确认后再核销</view>
				</view>

				<!-- Order Main -->
				<view class="order-main">
					<view class="goods" v-for="(goodsItem, goodsIndex) in orderInfo.order_goods">
						<view class="goods-wrap flex" @click="toDetail(goodsItem.id)" >
							<view class="image">
								<u-image :src="goodsItem.image" width="160" height="160"></u-image>
							</view>
						    
						    <view class="m-l-16 line-1">
						        <!-- 商品名称 -->
						        <view class="goods-name line-1 m-t-10">{{goodsItem.goods_name}}</view>
						        <!-- 规格数量 -->
						        <view class="muted flex row-between xs m-t-10">
						            <view>{{goodsItem.spec_value}}</view>
						            <view>x {{goodsItem.goods_num}}</view>
						        </view>
						    </view>
						</view>
					</view>
				</view>
			</view>
			
			<view
				class="operation operation--primary"
				:class="{ 'operation--submitting': submitting }"
				@click="openVerificationModal"
			>{{ submitting ? '核销中...' : '已提货' }}</view>
			<view class="operation operation--normal" @click="goVerificationList">返回核销列表</view>
		</view>
		
		<!-- 二次确认核销 -->
		<u-modal 
		  ref="uModalInput" 
		  v-model="showVerificationModal" 
		  show-cancel-button 
		  confirm-text="确定" 
		  title="确认核销" 
		  content="是否确认核销？"
		  @confirm="handleVerificationConfirm" 
		/>
		
		<!-- 页面状态 -->
		<page-status :status="pageStatus">
			<view slot="error" class="flex-col column-center">
				<u-empty text="订单异常" src="/static/images/empty/order.png" :icon-size="280"></u-empty>
				<view class="operation operation--primary" @click="goVerificationList">返回核销列表</view>
			</view>
		</page-status>
	</view>
</template>


<script>
	import { apiVerificationOrderDetail, apiVerificationOrderConfirm } from '@/api/order'
	import { PageStatusEnum } from '@/utils/enum'
	
	export default {
		name: 'VerificationDetail',
		
		data() {
			return {
				code: '',							// 核销码
				orderInfo: {},						// 订单信息
				pageStatus: PageStatusEnum['LOADING'],
				showVerificationModal: false,		// 显示(核销)：是|否
				submitting: false,					// 核销请求中(防重复提交)
			}
		},
		
		methods: {
			// 初始化订单数据
			initOrderData() {
				return new Promise((resolve, reject) => {
					apiVerificationOrderDetail({ 
						pickup_code: this.code,
					}).then(data => {
						this.orderInfo = data
						resolve(data)
					}).catch(err => {
						reject(err.message)
					})
				})
			},
			
			// 打开核销确认弹窗(核销请求中不可再次打开)
			openVerificationModal() {
				if (this.submitting) return
				this.showVerificationModal = true
			},
			
			// 确认核销订单
			handleVerificationConfirm() {
				// 防重复核销:请求未返回前直接拦截
				if (this.submitting) return
				this.submitting = true
				apiVerificationOrderConfirm({
					id: this.orderInfo.id
				}).then(data => {
					// this.initOrderData()
					setTimeout(() => {
						this.$Router.back()
					}, 0.5 * 1000)
				}).catch(err => {
					this.submitting = false
					this.$toast({
						title: typeof err === 'string' && err ? err : '核销失败，请重试'
					})
				})
			},
			
			// 返回核销列表
			goVerificationList() {
				this.$Router.back()
			}
		},
		
		async onLoad() {
			const options = this.$Route.query
			
			try {
				console.log(!options.code)
				if (!options.code) throw new Error('订单异常')
				this.code = options.code
				await this.initOrderData()
				console.log("HELLOW")
				this.pageStatus = PageStatusEnum['NORMAL']
			} catch(err) {
				console.log(err)
				// setTimeout(() => {
				// 	this.$Router.back()
				// }, 0.5 * 1000)
				this.pageStatus = PageStatusEnum['ERROR']
			}
		}
		
	}
</script>


<style lang="scss" scoped>
	.verification-detail {
		padding: 0 20rpx;
	}
	
	.order {
		padding-left: 20rpx;
		margin-top: 20rpx;
		border-radius: 5px;
		background-color: #FFFFFF;

		&-shop {
			padding: 12rpx 20rpx 0;

			.cross-tip {
				color: #f2a626;
			}
		}

		&-header {
			display: flex;
			height: 80rpx;
			align-items: center;
			padding-right: 20rpx;
			border-bottom: $-dashed-border;
			
			.order-sn {
				font-size: $-font-size-nr;
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
			.goods {
				width: 100%;
			    padding: 20rpx;
			    margin-bottom: 20rpx;
			    &-wrap {
			        width: 100%;
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
		}
	}
	
	.operation {
		display: flex;
		align-items: center;
		justify-content: center;
		height: 80rpx;
		margin-top: 20rpx;
		border-radius: 60px;
		
		&--primary {
			background-color: $-color-primary;
			color: #FFFFFF;
		}
		
		&--normal {
			background-color: #FFFFFF;
			color: $-color-normal;
		}
		
		&--submitting {
			opacity: 0.6;
		}
	}
</style>