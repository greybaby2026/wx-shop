<template>
	<view :class="themeName">
		<u-tabs :list="tabsList" :is-scroll="false" :height="80" :active-color="themeColor" :bar-style="{top: '100%'}"
			:current="tabsIndex" @change="changeCurrentTab" />

		<mescroll-body ref="mescrollRef" @init="mescrollInit" @up="upCallback" :up="upOption" :down="downOption"
			@down="downCallback">
			<view class="goods-list">
				<view v-for="(item, index) in bargainGoods" :key="index">
					<view class="goods-item flex-col" @click="tobargainProgeess(item.initiate_id)">
						<view class="flex start-time row-between">
							<view class="">
								发起时间：{{ item. create_time}}
							</view>
							<view :class="item.status != 3 ? 'desc' : 'desc-fail'">
								{{ item.status_desc}}
							</view>
						</view>
						<view class="flex bg-white centent">
							<u-image width="180rpx" height="180rpx" border-radius="10rpx" :src="item.goods_image" />
							<view class="goods-info m-l-20 flex-col">
								<view style="width: 490rpx" class="goods-name line-2 m-b-10">{{ item.goods_name }}</view>
								<view class="info-footer flex row-between m-t-5">
									<view class="flex">
										<price :content="item.current_price" prefix="已砍至￥" :color="themeColor" mainSize="34rpx"
											minorSize="26rpx" />
										<price class="m-l-20" :content="item.origin_price" prefix="原价￥" color="#999999"
											mainSize="24rpx" minorSize="24rpx" />
									</view>
								</view>
								<view class="condition">
									{{ item.tips }}
								</view>
							</view>
						</view>
						<view class="footer flex row-right" v-if="item.status != 3">
							<view v-if="item.status == 1" class="btn br60 white">继续砍价</view>
							<view v-if="item.status == 2" class="btn br60 white">砍价成功</view>
						</view>
					</view>
				</view>
			</view>
		</mescroll-body>

	</view>
</template>

<script>
	import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins.js";
	import {
		apiBargainRecord
	} from "@/api/bargain.js";
	export default {
		mixins: [MescrollMixin], // 使用mixin
		data() {
			return {
				// Tabs列表
				tabsList: [{
					name: '全部',
				}, {
					name: '砍价中',
				}, {
					name: '砍价成功',
				}, {
					name: '砍价失败',
				}],
				tabsIndex: 0, // tab索引
				status: ' ', // 砍价状态，不传或为空字符串代表全部， 1-砍价中 2-砍价成功 3-砍价失败

				bargainGoods: [], // 记录列表
				
				upOption: {
					textColor: '#fff',
					empty: {
						tip: '暂无砍价记录~', // 提示
						icon: '/static/images/empty/order.png',
						top: '200rpx',
						textColor: '#fff',
					}
				},
				downOption: {
					textColor: '#fff',
				},
			};
		},

		watch: {
			tabsIndex(value) {
				if (value == ' ') {
					this.status = ' '
				}else {
					this.status = value
				}
				
				this.mescroll.resetUpScroll()
			},
		},

		methods: {
			// 跳转砍价进度
			tobargainProgeess(id) {
				uni.navigateTo({
					url: '/bundle/pages/bargain_progress/bargain_progress?isShare=1&initiate_id=' + id, 
				})
			},
			
			// 更改当前Tab页
			changeCurrentTab(index) {
				this.tabsIndex = index
				console.log(index)
			},

			// 上拉加载
			upCallback(page) {
				apiBargainRecord({
					page_size: page.size,
					page_no: page.num,
					status: this.status,
				}).then(({
					lists,
					count
				}) => {
					if (page.num == 1) this.bargainGoods = [];
					this.bargainGoods.push(...lists);
					this.mescroll.endSuccess(lists.length, count);
				}).catch(() => {
					this.mescroll.endErr()
				})
			}
		}
	}
</script>

<style lang="scss">
	.goods-list {
		margin-top: 20rpx;
	}

	.goods-item {
		background-color: #FFFFFF;
		padding: 0rpx 24rpx;
		border-radius: 20rpx;
		margin-bottom: 20rpx;

		.start-time {
			font-family: "PingFang SC";
			font-weight: normal;
			font-size: 24rpx;
			color: #999;
			margin-bottom: 30rpx;
			padding: 24rpx 0;
			border-bottom: 1rpx solid #E5E5E5;
			
			.desc {
				@include font_color();
			}
			
			.desc-fail {
				color: #999;
			}
		}
		
		.centent {
			padding-bottom: 30rpx;
			.goods-info {
				// flex: 1;
				width: 450rpx;
				// margin-bottom: 30rpx;
				height: 180rpx;
				box-sizing: border-box;
			
				.goods-name {
					font-family: "PingFang SC";
					font-weight: normal;
					font-size: 28rpx;
					line-height: 40rpx;
					text-align: left;
					color: #333;
				}
			
				.condition {
					font-family: "PingFang SC";
					font-weight: normal;
					font-size: 22rpx;
					text-align: left;
					color: #ffa200;
				}
			
				.sale-info {
					padding: 4rpx 16rpx;
				}
			}
		}
		
		.footer {
			padding: 19rpx 0;
			border-top: 1rpx solid #E5E5E5;
			
			.btn {
				@include background_linear();
				padding: 13rpx 45rpx;
				font-family: "PingFang SC";
				font-weight: normal;
				font-size: 28rpx;
				line-height: 42rpx;
				
				width: 200rpx;
				height: 62rpx;
				border-radius: 31rpx;
			}
		}
	}
</style>
