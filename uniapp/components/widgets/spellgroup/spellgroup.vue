<template>
	<widget-root :styles="styles" v-if="content.data.length">
		<view class="spellgroup" :style="{
	                'background-color': styles.content_bg_color,
	                'border-radius': `${$px2rpx(styles.border_radius_top)}rpx ${$px2rpx(styles.border_radius_top)}rpx ${$px2rpx(styles.border_radius_bottom)}rpx ${$px2rpx(styles.border_radius_bottom)}rpx`,
	            }">
			<view class="spellgroup-header flex" :style="[headerStyle]">
				<view class="flex-1 flex">
					<image mode="heightFix" v-if="content.header_icon_image" class="flex-none m-r-6"
						style="height: 22px" :src="$getImageUri(content.header_icon_image)" />
					<view class="line-1" :style="{
	                            'max-width': '400rpx',
	                            color: styles.header_title_color,
	                            'font-size': `${$px2rpx(styles.header_title_size)}rpx`,
	                        }">
						{{ content.header_title }}
					</view>
				</view>
				<view class="more flex-none xs" v-if="content.show_haeder_more" :style="{
					            color: styles.header_more_color,
					        }">

					<router-link to="/bundle/pages/goods_team/goods_team">
						{{ content.header_more_text }}
						<u-icon name="arrow-right" size="26"></u-icon>
					</router-link>
				</view>
			</view>
			<view class="spellgroup-goods" :style="{ margin: `-${$px2rpx(styles.margin / 2)}rpx`, padding: '20rpx' }">
				<view class="goods-lists" :class="{
	                        lists: content.style == 1,
	                        swiper: content.style == 2,
	                    }">
					<view class="goods-wrap" v-for="(item, index) in content.data" :key="index">
						
						<router-link :to="{
							path: '/bundle/pages/goods_team_detail/goods_team_detail',
							query: { id: item.id }
						}">
							<view class="goods-item" :style="{
									'background-color': styles.goods_bg_color,
									margin: `${$px2rpx(styles.margin / 2)}rpx`,
									'border-radius': `${$px2rpx(styles.goods_border_radius)}rpx`,
								}">
								<view class="goods-image">
									<view class="u-image">
										<u-image :src="item.image" width="100%" height="100%" />
									</view>
								</view>
								<view class="goods-info p-10">
									<view v-if="content.show_title" class="m-b-10 name" :style="{
												color: styles.title_color,
											}">
										<view :class="
													content.style == 2
														? 'line-1'
														: 'line-2'
												">
											{{ item.name || "这里是商品标题" }}
										</view>
									</view>
									<view class="flex">
										<view v-if="content.show_group_num" class="spell-num xxs m-r-6" :style="{
											color: styles.group_num_color
										}">{{ item.people_num }}人团</view>
										<view class="muted xs" :style="{
											color: styles.sell_color,
										}" v-if="
											content.style == 1 && content.show_sell
										">
											已拼{{item.activity_sales}}件
										</view>
									</view>

									<view class="flex price-btn flex-wrap">
										<view class="flex-1 flex col-baseline">
											<view v-if="content.show_price" class="price m-r-10">
												<price :content="item.activity_price" :color="styles.price_color"
													:font-weight="500" mainSize="34rpx" minorSize="26rpx" />
											</view>
											<view v-if="content.show_scribing_price">
												<price :content="item.sell_price" color="#999999" mainSize="26rpx"
													minorSize="26rpx" :line-through="true" />
											</view>
										</view>
										<view class="buy-btn xs" v-if="
													content.style == 1 &&
													content.show_btn
												" :style="{
													'background-color':
														styles.btn_bg_color,
													color: styles.btn_color,
												}">
											{{ content.btn_text }}
										</view>
									</view>
								</view>
							</view>
						</router-link>
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
	.spellgroup {
		overflow: hidden;

		.spellgroup-header {
			color: #fff;
			height: 100rpx;
			background-color: #ff624b;
			background-repeat: no-repeat;
			background-size: 100% auto;
			padding: 0 10rpx 0 20rpx;
		}

		.spellgroup-goods {

			.goods-lists {
				overflow: hidden;

				&.swiper {
					overflow-x: auto;
					overflow-y: hidden;
					display: flex;

					.goods-wrap {
						flex: 0 0 36%;
						width: 36%;
					}
				}

				&.lists {
					.goods-wrap {
						.goods-item {
							flex-direction: row;

							.goods-image {
								width: 240rpx;
								padding-top: 240rpx;
							}

							.goods-info {
								position: relative;
								padding: 20rpx;

								.name {
									line-height: 36rpx;
									height: 72rpx;
								}

								.price-btn {
									position: absolute;
									right: 20rpx;
									left: 20rpx;
									bottom: 20rpx;
								}
							}
						}
					}
				}

				.goods-wrap {
					overflow: hidden;

					.goods-item {
						overflow: hidden;
						display: flex;
						flex-direction: column;

						.goods-image {
							position: relative;
							width: 100%;
							padding-top: 100%;
							box-sizing: border-box;

							.u-image {
								position: absolute;
								top: 0;
								right: 0;
								bottom: 0;
								left: 0;
								width: 100%;
							}
						}

						.goods-info {
							flex: 1;

							.name {
								color: #333333;
							}

							.price {
								color: #ff2c3c;
							}

							.spell-num {
								color: #ff2c3c;
								border: 1px solid currentColor;
								border-radius: 3px;
								line-height: 14px;
								padding: 1px 8px;
							}

							.buy-btn {
								padding: 12rpx 20rpx;
								color: #ffffff;
								background-color: #ff2c3c;
								border-radius: 60rpx;
							}
						}
					}
				}
			}
		}
	}
</style>
