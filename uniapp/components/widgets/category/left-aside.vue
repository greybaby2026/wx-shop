<template>
	<view :class="themeName" style="height: 100%">
		<scroll-view scroll-y="true" class="aside bg-white" scroll-with-animation="true" v-if="lists.length">
			<template v-for="(item, index) in lists">
				<view  :class="'item-one sm ' + (index == selectIndex ? 'active' : '')"  :key="index"
					@click="changeActive(index)">
					<text class="name p-l-10">{{ item.name }}</text>
					<view v-if="index == selectIndex" class="active-line"></view>
				</view>
			</template>
		</scroll-view>
	</view>
</template>

<script>
	import category from './category.js'
	export default {
		mixins:[category],
		data() {
			return {

			}
		},
		methods: {
			changeActive(index) {
				this.parent.selectIndex = index
			}
		},
	}
</script>

<style lang="scss">
	.aside {
		width: 180rpx;
		flex: none;
		height: 100%;

		.item-one {
			position: relative;
			text-align: center;
			padding: 26rpx 10rpx;

			&.active {
				font-size: 26rpx;
				font-weight: bold;
				@include font_color()
			}

			.active-line {
				position: absolute;
				width: 6rpx;
				height: 30rpx;
				left: 4rpx;
				top: 50%;
				transform: translateY(-50%);
				@include background_color()
			}
		}
	}
</style>
