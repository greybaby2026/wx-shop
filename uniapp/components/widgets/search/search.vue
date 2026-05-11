<template>
	<router-link to="/pages/goods_search/goods_search">
		<widget-root :styles="styles">
			<div class="search">
				<div class="search-content" :style="{
			            'background-color': styles.bg_color, 
			            'text-align': styles.text_align, 
			            'border-radius': `${$px2rpx(styles.border_radius)}rpx`
			        }">
					<u-icon name="search" :color="styles.icon_color"></u-icon>
					<text class="search-text" :style="{color: styles.color}">{{ content.text }}</text>
				</div>
			</div>
		</widget-root>
	</router-link>
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

		}
	};
</script>

<style lang="scss" scoped>
	.search {
		box-sizing: border-box;
		width: 100%;

		.search-content {
			height: 64rpx;
			line-height: 64rpx;
			padding: 0 24px;

			.search-text {
				margin-left: 8px;
			}
		}
	}
</style>
