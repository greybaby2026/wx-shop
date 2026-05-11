<template>
	<view class="live-broadcast">
		<!-- #ifdef MP-WEIXIN -->
		<mescroll-body ref="mescrollRef" @init="mescrollInit" @up="upCallback" :up="upOption" :down="downOption"
			@down="downCallback">
			<view class="broadcast-list">
				<view class="broadcast-item" @tap="navigateTo(item)" v-for="(item, index) in LBLists" :key="index">
					<view class="img-wrap">
						<u-image width="100%" height="100%" :src="item.cover_img"></u-image>
						<view class="tag xxs" :class="{
						active: item.status == 101
					}">
							<image v-if="item.status == 101" class="live-img" src="@/static/images/live.gif" alt="" />
							<text v-else class="circel"></text>
							<text class="m-l-10">{{ item.live_status }}</text>
						</view>
						<view class="tips line1">{{ item.name }}</view>
					</view>
					<view class="info">
						<view class="md">
							主播：{{ item.anchor_name }}
							<text class="xs">｜直播商品：{{item.goods}}件</text>
						</view>
						<view class="muted xs mt20">直播时间：{{item.start_time}} 至 {{item.end_time}}</view>
					</view>
				</view>
			</view>
		</mescroll-body>
		<!-- #endif -->


		<!-- #ifndef MP-WEIXIN-->
		<view class="data-null flex-col col-center " style="padding-top: 400rpx;">
			<image class="img-null" src="/static/images/order_null.png"></image>
			<text class="nr muted">非小程序端暂不支持直播~</text>
		</view>
		<!-- #endif -->
	</view>
</template>

<script>
	import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins.js";
	import {
		apiLiveRoomLists
	} from "@/api/live_broadcast.js";
	export default {
		mixins: [MescrollMixin], // 使用mixin
		data() {
			return {
				LBLists: [],

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
					// textColor: '#fff',
				},
			};
		},

		methods: {
			navigateTo(item) {
				console.log('item ', item)
				const roomId = [item.room_id];
				console.log('roomId', roomId)
				wx.navigateTo({
					url: `plugin-private://wx2b03c6e691cd7370/pages/live-player-plugin?room_id=${roomId}`
				});
			},

			// 上拉加载
			upCallback(page) {
				apiLiveRoomLists({
					page_size: page.size,
					page_no: page.num,
				}).then(({
					lists,
					count
				}) => {
					if (page.num == 1) this.LBLists = [];
					this.LBLists.push(...lists);
					this.mescroll.endSuccess(lists.length, count);
				}).catch(() => {
					this.mescroll.endErr()
				})
			},

		},
	}
</script>

<style lang="scss">
	.live-broadcast {
		.broadcast-list {
			padding: 20rpx 20rpx 0;
			overflow: hidden;

			.broadcast-item {
				border-radius: 12rpx;
				overflow: hidden;
				background-color: #fff;

				&:not(:last-of-type) {
					margin-bottom: 20rpx;
				}

				.img-wrap {
					height: 300rpx;
					position: relative;
					color: #fff;

					.tag {
						position: absolute;
						top: 0;
						border-radius: 0 60rpx 60rpx 0;
						background: #ccc;
						padding: 8rpx 20rpx;

						.live-img {
							width: 30rpx;
							height: 30rpx;
						}

						&.active {
							background: linear-gradient(#ff2c3c 0%, #f95f2f 100%);
						}

						.circel {
							display: inline-block;
							width: 14rpx;
							height: 14rpx;
							background: #fff;
							border-radius: 50%;
						}
					}

					.tips {
						position: absolute;
						background: linear-gradient(rgba(255, 255, 255, 0) 0%, #808080 100%);
						bottom: 0;
						width: 100%;
						font-weight: 500;
						font-size: 30rpx;
						padding: 42rpx 20rpx 16rpx;
					}
				}

				.info {
					padding: 20rpx;
				}
			}
		}
	}
</style>
