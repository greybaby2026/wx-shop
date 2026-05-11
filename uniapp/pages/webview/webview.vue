<template>
	<view class="page-body">
		<web-view :src="url"></web-view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				url: ''
			};
		},
		onLoad: function(options) {
			this.url = decodeURIComponent(this.$Route.query.url)
		},
		methods: {}
	};
</script>
<style>

</style>
