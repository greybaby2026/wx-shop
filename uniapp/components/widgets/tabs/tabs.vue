<template>
	<view class="tabs" :style="{'background-color': styles.root_bg_color}">
		<widget-root :styles="styles">
			<scroll-view class="tab-scroll" scroll-with-animation :scroll-x="true" :scroll-into-view="'tab-item-' + (active - 2)" :style="{
			             'background-color': styles.bg_color,
			              'border-radius': `${$px2rpx(styles.border_radius_top)}rpx ${$px2rpx(styles.border_radius_top)}rpx ${$px2rpx(styles.border_radius_bottom)}rpx ${$px2rpx(styles.border_radius_bottom)}rpx`
			         }">
				<view class="tabs-header flex" :style="{
			              'border-radius': `${$px2rpx(styles.border_radius_top)}rpx ${$px2rpx(styles.border_radius_top)}rpx ${$px2rpx(styles.border_radius_bottom)}rpx ${$px2rpx(styles.border_radius_bottom)}rpx`
			         }">
					<view class="tabs-header-item" :id="'tab-item-' + index" v-for="(item,index) in content.data"
						:key="index" :style="[itemStyle(index)]" @tap="changeActive(index)">
						<view>{{item.name}}</view>
						<view v-if="content.show_line == 1 && active == index" class="tabs-line"
							:style="{background: styles.line_color}"></view>
					</view>
				</view>
			</scroll-view>
		</widget-root>
		<view class="tabs-content">
			<view class="tabs-content-item" v-for="(item,index) in content.data" :key="index">
				<view v-if="showLists[index]" v-show="active == index">
					<goods :styles="item" :content="item" />
				</view>
			</view>
		</view>
	</view>
</template>

<script type="text/javascript">
	import goods from '../goodsgroup/goodsgroup.vue'
	export default {
		components: {
			goods
		},
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
				active: 0,
				showLists: []
			}
		},
		methods: {
			changeActive(index) {
				this.active = index
				this.showLists[index] = true
			}
		},
		computed: {
			itemStyle() {
				return (index) => {
					const {
						styles: {
							padding,
							color,
							active_color,
							active_bg_color
						},
						content: {
							has_active_bg
						},
						active,
					} = this
					const style = {
						padding: `0 ${this.$px2rpx(padding)}rpx`,
						color: color,
					}
					if (active == index) {
						style['background-color'] = has_active_bg ? active_bg_color : ''
						style.color = active_color
						style['font-weight'] = 500
					}
					return style
				}
			}
		},
		watch: {
			'content.data': {
				handler(val) {
					this.showLists = val.map((item, index) => {
						if(index == 0) {
							return true
						}
						return false
					})
				},
				immediate: true
			}
		}
	};
</script>
<style lang="scss" scoped>
	.tabs {
		.tabs-header {
			display: inline-flex;
			overflow: hidden;
			.tabs-header-item {
				padding: 0 20rpx;
				text-align: center;
				font-size: 30rpx;
				height: 88rpx;
				line-height: 88rpx;
				position: relative;
				white-space: nowrap;
				color: #ff2c3c;

				.tabs-line {
					position: absolute;
					left: 20%;
					right: 20%;
					bottom: 0;
					height: 4rpx;
					background: #ff2c3c;
					z-index: 1;
				}
			}
		}
	}
</style>
