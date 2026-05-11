<template>
	<view :class="themeName">
		<view class="integral-goods-details">
			<navbar title="积分兑换详情" :background="{background: `rgba(256,256,256,${percent})`}"
				:titleColor="`rgba(0,0,0,${percent})`" :immersive="true"></navbar>
			<!-- 商品图片 -->
			<view class="goods-image" @tap="previewImage(goodsInfo.image)">
				<u-image :src="goodsInfo.image" width="100%" height="750rpx"></u-image>
			</view>
			<!-- 商品信息 -->
			<view class="goods-info bg-white">
				<view class="info-header flex">
					<view class="price flex flex-1">
						<view class="m-r-20">
							<price :content="goodsInfo.need_integral" mainSize="38rpx" minorSize="24rpx"
								:color="themeColor" :prefix="null" suffix="积分" />
							<price v-if="goodsInfo.exchange_way === 2" :content="goodsInfo.need_money" mainSize="38rpx"
								minorSize="24rpx" :color="themeColor" prefix="+" suffix="元" />
						</view>
						<view class="muted xs" v-if="goodsInfo.market_price > 0">
							市场价
							<price :content="goodsInfo.market_price" mainSize="24rpx" minorSize="24rpx" />
						</view>
					</view>
					<view class="muted xs">
						已兑换：{{ goodsInfo.sales }}
					</view>
				</view>
				<view class="name lg bold">{{ goodsInfo.name }}</view>
			</view>
			<!-- 商品规格选择 -->
			<view class="goods-spec flex bg-white m-t-20" @tap="showSpec = true">
				<view class="label muted flex-none">已选</view>
				<view class="line-1 m-r-20 flex-1">数量x{{ count }}</view>
				<u-icon name="arrow-right" color="#999"></u-icon>
			</view>
			<!-- 商品详情 -->
			<view class="goods-details m-t-20 bg-white">
				<view class="details-title lg">商品详情</view>
				<view class="details-content">
					<u-parse :html="goodsInfo.content" :lazy-load="true" :show-with-animation="true"></u-parse>
				</view>
			</view>
			<!-- 底部按钮 -->
			<view class="goods-footer bg-white flex">
				<button class="right-btn flex-1 br60" @tap="toSettlement">
					立即兑换
				</button>
			</view>
			<!-- 规格弹窗 -->
			<view class="spec-popup">
				<u-popup v-model="showSpec" mode="bottom" border-radius="14" :closeable="true"
					:safe-area-inset-bottom="true">
					<view class="bg-white spec-contain">
						<view class="spec-header flex">
							<u-image width="160rpx" height="160rpx" class="m-r-20" border-radius="10rpx"
								@click="previewImage(goodsInfo.image)" :src="goodsInfo.image"></u-image>
							<view class="goods-info">
								<view class="flex">
									<price :content="goodsInfo.need_integral" mainSize="38rpx" minorSize="24rpx"
										:color="themeColor" :prefix="null" suffix="积分" />
									<price v-if="goodsInfo.exchange_way === 2" :content="goodsInfo.need_money"
										mainSize="38rpx" minorSize="24rpx" :color="themeColor" prefix="+" suffix="元" />
								</view>
								<view class="sm">
									库存：{{goodsInfo.stock}}件
								</view>
							</view>
						</view>
						<view class="spec-main" style="height: 400rpx;">
							<view class="good-num flex row-between m-l-20 m-r-20">
								<view class="label">数量</view>
								<u-number-box v-model="count" :min="1" :max="goodsInfo.stock">
								</u-number-box>
							</view>
						</view>
						<view class="spec-footer">

							<button hover-class="none" class="right-btn flex-1 br60" @tap="toSettlement">
								立即兑换
							</button>
						</view>
					</view>
				</u-popup>
			</view>
			<!-- 页面状态 -->
			<page-status :status="pageStatus">
				<template #error>
					<u-empty :text="pageErrorMsg" src="/static/images/empty/shop.png" :icon-size="280"></u-empty>
				</template>
			</page-status>
			<u-back-top :scroll-top="scrollTop" :top="1000"
				:customStyle="{ backgroundColor: '#FFF', color: '#000', boxShadow: '0px 3px 6px rgba(0, 0, 0, 0.1)'}"></u-back-top>
		</view>
	</view>
</template>

<script>
	import {
		apiIntegralGoodsDetail
	} from '@/api/integral_mall'
	import {
		PageStatusEnum
	} from '@/utils/enum'
	export default {
		data() {
			return {
				pageStatus: PageStatusEnum['LOADING'],
				pageErrorMsg: '', // 页面异常信息
				goodsInfo: {},
				count: 1,
				showSpec: false,
				scrollTop: 0,
				percent: 0,
			}
		},
		methods: {
			// 图片预览
			previewImage(current) {
				uni.previewImage({
					current,
					urls: [current] // 需要预览的图片http链接列表
				});
			},
			// 前往结算页面
			toSettlement() {
				this.$Router.push({
					path: '/bundle/pages/integral_settlement/integral_settlement',
					query: {
						id: this.goods_id,
						num: this.count
					}
				})
			},
			// 初始化商品详情数据
			initGoodsDetail() {
				return new Promise((resolve, reject) => {
					apiIntegralGoodsDetail({
						id: this.goods_id
					}).then(data => {
						this.goodsInfo = data
						return data
					}).then(data => {
						// #ifdef H5
						// 设置分享
						this.$store.dispatch('setWxShare', {
							shareImage: data?.image,
							shareDesc: data?.name
						})
						// #endif
						resolve(data)
					}).catch(err => {
						reject(err)
					})
				})
			},

		},
		onPageScroll(e) {
			const top = uni.upx2px(100)
			const {
				scrollTop
			} = e
			this.percent = scrollTop / top > 1 ? 1 : scrollTop / top
			this.scrollTop = scrollTop
		},
		async onShow() {
			const options = this.$Route.query
			this.goods_id = options.id

			try {
				if (!this.goods_id) throw new Error('该商品不存在')
				// 商品详情数据
				await this.initGoodsDetail()
				this.pageStatus = PageStatusEnum['NORMAL']
			} catch (err) {
				console.log(err)
				this.pageErrorMsg = err.message
				this.pageStatus = PageStatusEnum['ERROR']
			}
		},
		onShareAppMessage() {
			const {
				goodsInfo
			} = this;
			return {
				title: goodsInfo.name,
				imageUrl: goodsInfo.image,
				path: '/bundle/pages/integral_goods_details/integral_goods_details?id=' + this.goods_id + "&invite_code=" +
					this.$store.getters.inviteCode
			};
		},
		//分享朋友圈
		onShareTimeline() {
			const {
				goodsInfo
			} = this;
			return {
				title: goodsInfo.name,
				query: `id=${this.goods_id}&invite_code=${this.$store.getters.inviteCode}`, // 分享参数，注意这里使用query而不是path
				imageUrl: goodsInfo.image

			}
		},

	}
</script>

<style lang="scss">
	.integral-goods-details {
		padding-bottom: calc(120rpx + env(safe-area-inset-bottom));

		.goods-info {
			position: relative;

			.info-header {
				padding: 20rpx 24rpx 0;

				.price {
					align-items: baseline;
				}
			}

			.name {
				padding: 20rpx 24rpx;
				flex: 1;
			}
		}

		.goods-details {

			// overflow: hidden;
			.details-title {
				line-height: 88rpx;
				text-align: center;
			}

			&>.details-content {
				padding: 0 20rpx 20rpx;

				::v-deep image {
					vertical-align: top;
				}

				// #ifdef H5
				::v-deep img {
					vertical-align: top;
					display: inline;
				}

				// #endif
				// #ifdef MP-WEIXIN || APP-PLUS
				::v-deep ._img {
					vertical-align: top;
					display: inline;
				}

				// #endif
			}
		}

		.goods-spec {
			padding: 24rpx 24rpx;

			.label {
				width: 100rpx;
			}
		}

		.goods-footer {
			height: 100rpx;
			position: fixed;
			bottom: 0;
			left: 0;
			right: 0;
			box-sizing: content-box;
			padding: 0 24rpx;
			padding-bottom: env(safe-area-inset-bottom);
		}

		.spec-popup {
			.spec-contain {
				overflow: hidden;
				position: relative;

				.spec-header {
					padding: 30rpx;
					padding-right: 70rpx;
					align-items: flex-start;
					border: $-solid-border;
				}

				.spec-main,
				.spec-footer {
					padding: 24rpx;
				}
			}

		}

		.right-btn {
			height: 80rpx;
			line-height: 80rpx;
			@include background_color();
			color: #fff;
		}
	}
</style>