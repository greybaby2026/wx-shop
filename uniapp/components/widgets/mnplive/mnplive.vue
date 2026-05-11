<template>
	<widget-root :styles="styles" v-if="content.data.length">
		<view class="mnplive" :style="{
		                'background-color': styles.content_bg_color,
		                'border-radius': `${$px2rpx(styles.border_radius_top)}rpx ${$px2rpx(styles.border_radius_top)}rpx ${$px2rpx(styles.border_radius_bottom)}rpx ${$px2rpx(styles.border_radius_bottom)}rpx`,
		            }">
			<view class="mnplive-header flex" :style="[headerStyle]">
				<view class="flex-1 flex">
					<image mode="heightFix" v-if="content.header_icon_image" class="flex-none m-r-10"
						style="height: 22px;" :src="$getImageUri(content.header_icon_image)" />
					<view class="line-1" :style="{
		                            'min-width': 0,
		                            color: styles.header_title_color,
		                            'font-size': `${$px2rpx(styles.header_title_size)}rpx`,
		                        }">
						{{ content.header_title }}
					</view>
				</view>
				<view class="more flex-none xs" v-if="content.show_haeder_more" :style="{
		                        color: styles.header_more_color,
		                    }">
					<router-link to="/bundle/pages/live_broadcast/live_broadcast">
						{{ content.header_more_text }}
						<u-icon name="arrow-right" size="26"></u-icon>
					</router-link>
				</view>
			</view>
			<view class="mnplive-wrap" :style="{ margin: `-${$px2rpx(styles.margin / 2)}rpx` }">
				<view class="mnplive-lists" :class="{
		                        larger: content.style == 1,
		                        perline: content.style == 2,
		                        lists: content.style == 3,
		                    }">
					<view class="mnplive-item-wrap" v-for="(item, index) in content.data" :key="index" @tap="navigateTo(item)">
						<view class="mnplive-item" :style="{
		                                'background-color': styles.bg_color,
		                                margin: `${$px2rpx(styles.margin / 2)}rpx`,
		                                'border-radius': `${$px2rpx(styles.goods_border_radius)}rpx`,
		                            }">
							<view class="item-image">
								<view class="u-image">
									<u-image :src="item.cover_img" width="100%" height="100%" />
								</view>
								<view class="status flex xs" :class="{
									active: item.status == 101
								}">
									<image v-if="item.status == 101" class="live-img" src="@/static/images/live.gif" alt="" />
									<text v-else class="circel"></text>
									<text class="m-l-20">{{ item.live_status }}</text>
								</view>
								<view class="name">{{item.name}}</view>
								
							</view>
							<view class="info">
								<view>
									<text class="lg bold">主播：{{item.anchor_name}}</text>
									<text v-if="content.style == 1" class="sm"> ｜ 直播商品：{{item.goods}}件 </text>
									<view v-else class="sm m-t-10">直播商品：{{item.goods}}件 </view>
								</view>
								<view class="xs muted m-t-10">
									开播时间：{{item.start_time}}
								</view>
							</view>
						</view>
					</view>
				</view>
			</view>
		</view>
	</widget-root>
</template>

<script type="text/javascript">
	export default {
		props: {
			content: {
				type: [Object, Array]
			},
			styles: {
				type: [Object, Array]
			},
		},
		data() {
			return {

			}
		},
		methods: {
			navigateTo(item) {
				const roomId = [item.room_id];
				wx.navigateTo({
					url: `plugin-private://wx2b03c6e691cd7370/pages/live-player-plugin?room_id=${roomId}`
				});
			},
		},
		computed: {
			headerStyle() {
				const {
					header_bg_type,
					header_bg_image
				} = this.content
				const {
					header_bg_color
				} = this.styles
				return header_bg_type == 1 ? {
					'background-color': header_bg_color
				} : {
					'background-image': `url(${this.$getImageUri(header_bg_image)})`
				}
			}
		}
	};
</script>

<style lang="scss" scoped>
.mnplive {
    overflow: hidden;
    .mnplive-header {
        height: 100rpx;
        background-repeat: no-repeat;
        background-size: 100% auto;
        padding: 0 10rpx 0 20rpx;
    }
    .mnplive-wrap {
        overflow: hidden;
        .mnplive-lists {
            overflow: hidden;
            .mnplive-item-wrap {
                .mnplive-item {
					
					overflow: hidden;
                    .item-image {
                        height: 0;
                        position: relative;
						.u-image {
							position: absolute;
							top: 0;
							right: 0;
							bottom: 0;
							left: 0;
							width: 100%;
						}
                        .status {
                            color: #fff;
                            position: absolute;
                            top: 0;
                            border-radius: 0 60rpx 60rpx 0;
                            background: #ccc;
                            padding: 10rpx 20rpx;
							.circel {
								display: inline-block;
								width: 14rpx;
								height: 14rpx;
								background: #fff;
								border-radius: 50%;
							}
							&.active {
								background: linear-gradient(
								    #ff2c3c 0%,
								    #f95f2f 100%
								);
							}
                            z-index: 1;
                            .live-img {
                                width: 30rpx;
                                height: 30rpx;
                            }
                        }
                        .name {
                            position: absolute;
                            background: linear-gradient(
                                rgba(255, 255, 255, 0) 0%,
                                #808080 100%
                            );
                            bottom: 0;
                            width: 100%;
                            font-weight: 500;
                            font-size: 30rpx;
                            padding: 16rpx 20rpx;
                            z-index: 1;
                            color: #fff;
                        }
                        
                    }
                    .info {
                        padding: 20rpx;
                    }
                }
            }
            &.larger {
                .item-image {
                    padding-top: 42%;
                }
            }
            &.perline {
                display: flex;
                flex-wrap: wrap;
                .mnplive-item-wrap {
                    width: 50%;
                    .mnplive-item {
                        .item-image {
                            padding-top: 100%;
                        }
                    }
                }
            }
            &.lists {
                .mnplive-item-wrap {
                  
                    .mnplive-item {
                        display: flex;
                        .item-image {
                            flex: none;
                            width: 288rpx;
                            padding-top: 288rpx;
                        }
                    }
                }
            }
        }
    }
}
</style>
