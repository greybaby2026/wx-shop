<template>
	<view class="emoji">
		<view class="emoji-lists">
			<view class="emoji-item" v-for="(item, index) in emoji" @tap="handleClick(item)" :key="index">
				<text class="em" :class="item"></text>
			</view>
		</view>
	</view>
</template>

<script>
	import emoji from '@/plugin/emoji-awesome/emoji'
	export default {
		name:"emoji",
		data() {
			return {
				emoji
			};
		},
		methods: {
			handleClick(emoji) {
				this.$emit('input', `[${emoji}]`)
			}
		}
	}
</script>

<style lang="scss"> 
.emoji-lists {
	display: flex;
	flex-wrap: wrap;
	padding: 20rpx;
	.emoji-item {
		width: 12.5%;
		text-align: center;
		padding: 10rpx 0;
		transform: scale(1.2);
	}
}
</style>
