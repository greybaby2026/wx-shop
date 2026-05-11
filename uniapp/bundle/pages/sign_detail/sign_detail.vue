<template>
	<view class="sign-detail" :class="themeName">
		<mescroll-contain  top="20rpx" ref="integral" :up-option="{
		  	noMoreSize: 10,
			page: {
			  num : 0 ,
			  size : 15,
			  time : null
			},
		  	empty: {
		  		icon: '/static/images/empty/order.png',
		  		tip: '暂无积分明细~',
		  		fixed: false
		  	}
		  }" @upcallback="upCallback">
			<view class="">
				<view class="item flex row-between bg-white" v-for="(item, index) in integralList" :key="index">
					<view class="">
						<view class="nr">
							{{item.type_desc}}
						</view>
						<view class="xs muted m-t-10">
							{{item.create_time}}
						</view>
					</view>
					<view :class="'lg ' +(item.action == 2 ? 'amount' : '')">
						{{item.change_amount_desc}}
					</view>
				</view>
			</view>

		</mescroll-contain>
	</view>
</template>

<script>
	import {
		apiAccountLogLists
	} from "@/api/user.js";

	let mescroll = null

	export default {
		data() {
			return {
				integralList: [],
			};
		},

		mounted() {
			mescroll = this.$refs['integral']
		},

		methods: {
			// 上拉加载更多
			upCallback({
				num,
				size
			}) {
				apiAccountLogLists({
					page_no: num,
					page_size: size,
					type: 'integral', // 明细类型 integral - 积分明细
				}).then(({
					lists,
					page_size,
					count
				}) => {
					if (num === 1) this.integralList = []
					this.integralList = [...this.integralList, ...lists]
					// mescroll.endBySize(page_size, count)
					mescroll.endSuccess(lists.length, count)
				}).catch(err => {
					mescroll.endBySize()
				})
			}
		},
	}
</script>

<style lang="scss">
	.item {
		padding: 18rpx 30rpx;
		position: relative;
	}
	
	.amount {
		@include font_color()
	}
</style>
