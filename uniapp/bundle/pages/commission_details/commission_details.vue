

<template>
	<view class="commission-details" :class="themeName">
		<mescroll-body ref="mescrollRef" @init="mescrollInit" @up="upCallback" :up="upOption" :down="downOption" @down="downCallback">
			<view class="p-t-20" >
				<view class="bg-white" v-for="(item, index) in lists" :key="index" >
					<record-cell :remark="item.type_desc" :date="item.create_time" :money="item.change_amount_desc" :type="item.action" />
				</view>
			</view>
		</mescroll-body>
	</view>
</template>

<script>
	import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins.js";
	import recordCell from '@/bundle/components/record-cell/record-cell.vue'
	import {
	    userBill
	} from "@/api/user";
	export default {
		components: {
			recordCell
		},
		mixins: [MescrollMixin], // 使用mixin
		data() {
			return {
				upOption: {
					empty: {
						icon: '/static/images/empty/order.png',
						tip: '暂无记录~', // 提示
					}
				},
				lists: [], // 列表数据--全部
			};
		},

		methods: {
		

			// 上拉加载 
			upCallback(page) {
				const pageNum = page.num; // 页码, 默认从1开始
				const pageSize = page.size; // 页长, 默认每页10条
				userBill({
					page_size: pageSize,
					page_no: pageNum,
					type: 'bw'
				}).then((data) => {
					if (page.num == 1) this.lists = [];
					const curPageData = data.lists;
					const curPageLen = curPageData.length;
					this.lists = this.lists.concat(curPageData);
					this.mescroll.endSuccess(curPageLen, data.count);
				}).catch(() => {
					this.mescroll.endErr()
				})

			}
		},
	}
</script>

<style scoped>
	
</style>