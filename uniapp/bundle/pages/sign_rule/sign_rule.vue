<template>
	<view class="m-t-20 m-l-20 m-r-20">
		<text class="nr sign-rule">{{rules}}</text>
	</view>
</template>

<script>
	import { apiSignGetRemark } from "@/api/user.js";
	
	export default {
		data() {
			return {
				rules: '',
			};
		},
		
		methods: {
			signGetRemark() {
				apiSignGetRemark()
				.then(res => {
					this.rules = res.remark
				})
			}
		},
		
		onLoad() {
			this.signGetRemark()
		}
	}
</script>

<style lang="scss">
	.sign-rule {
		line-height: 36rpx;
	}
</style>
