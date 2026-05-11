<template>
	<view class="integral-details">
		<mescroll-body ref="mescrollRef" :up="{
		  	noMoreSize: 10,
		  	empty: {
		  		icon: '/static/images/order_null.png',
		  		tip: '暂无积分明细~',
		  		fixed: false
		  	}
		  }" @init="mescrollInit" @down="downCallback" @up="upCallback">
			<view class="integral-lists">
				<view class="item flex row-between bg-white" v-for="(item, index) in integralList" :key="index">
					<view class="">
						<view class="nr">
							{{item.source_type}}
						</view>
						<view class="xs muted m-t-10">
							{{item.create_time}}
						</view>
					</view>
					<view :class="'lg ' +(item.change_type == 1 ? 'primary' : '')">
						{{item.change_amount_format}}
					</view>
				</view>
			</view>
		</mescroll-body>
	</view>
</template>

<script>
	import {
		getAccountLog
	} from "@/api/user";

	import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins.js";
	export default {
		mixins: [MescrollMixin], // 使用mixin
		data() {
			return {
				integralList: [],
			};
		},


		methods: {
			// 上拉加载更多
			upCallback({
				num,
				size
			}) {
				getAccountLog({
					page_no: num,
					page_size: size,
					source: 2, //积分明细
				}).then(({
					data,
					code
				}) => {
					if(code != 1) return this.mescroll.endErr()
					if (num === 1) this.integralList = []
					this.integralList = [...this.integralList, ...data.list]
					this.mescroll.endSuccess(data.list.length, !!data.more)
				}).catch(err => {
					this.mescroll.endErr()
				})
			}
		},
	}
</script>

<style lang="scss">
	.integral-lists {
		padding-top: 20rpx;
		.item {
			padding: 18rpx 30rpx;
			position: relative;
			&:not(:last-of-type) {
				border-bottom: $-solid-border;
			}
		}
	}
	
</style>
