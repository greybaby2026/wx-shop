<template>
	<scroll-view style="height: 100%" scroll-y="true" scroll-with-animation="true">
		<view class="one-lists">
			<template v-for="(item, index) in lists">
				<view class="item-one" v-if="selectIndex == index" :key="index">
					<view class="two-lists">
						<template v-if="item.sons">
							<view class="item-two" v-for="(sitem, sindex) in item.sons" :key="sindex">
								<router-link :to="{path: '/pages/goods_search/goods_search', query: {
										id: sitem.id,
										name: sitem.name
									}}">
									<view class="title flex row-between">
										<text class="name bold sm">{{sitem.name}}</text>
										<u-icon name="arrow-right"></u-icon>
									</view>
								</router-link>
								<template v-if="sitem.sons">
									<slot :lists="sitem.sons" :na="sitem.name"></slot>
								</template>
							</view>
						</template>
					</view>
				</view>
			</template>
		</view>
	</scroll-view>
</template>

<script>
	export default {
		inject: ['self'],
		props: {},
		data() {
			return {

			}
		},
		methods: {
			
		},
		computed: {
			lists() {
				return this.self.lists
			},
			selectIndex() {
				console.log(this.self.selectIndex)
				return this.self.selectIndex
			}
		}
	}
</script>

<style lang="scss" scoped>
	.one-lists {
		padding: 20rpx;

		.item-one {
			.item-two {
				&:not(:last-of-type) {
					margin-bottom: 20rpx;
				}

				.title {
					background: #fff;
					padding: 20rpx;
					border-radius: 10rpx;
				}
			}
		}
	}
</style>
