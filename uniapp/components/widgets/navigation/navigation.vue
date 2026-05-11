<template>
	<widget-root :styles="styles">
		<div class="navigation" :style="{
		                'background-color': styles.bg_color,
		                'border-radius': `${$px2rpx(styles.border_radius_top)}rpx ${$px2rpx(styles.border_radius_top)}rpx ${$px2rpx(styles.border_radius_bottom)}rpx ${$px2rpx(styles.border_radius_bottom)}rpx`
		            }">
			<template v-if="styles.nav_style==1">
				<div class="navigation-content flex flex-wrap p-t-32">
					<div v-for="(item, index) in navList" :key="index"
						:style="{width: `${(100/styles.nav_line_num)}%`}">
						<item :item="item" :styles="styles" :content="content" />
					</div>
				</div>
			</template>
			<template v-if="styles.nav_style==2">
				<swiper :style="{height: swiperHeight}" class="navigation-swiper" @change="swiperChange">
					<swiper-item v-for="(s, i) in pageSize" :key="i">
						<div class="navigation-content flex flex-wrap p-t-32">
							<div v-for="(item, index) in getPageList(i)" :key="index"
								:style="{width: `${(100/styles.nav_line_num)}%`}">
								<item :item="item" :styles="styles" :content="content" />
							</div>
						</div>
					</swiper-item>
				</swiper>
				<div class="navigation-indicator">
					<indicator :current="current" :length="pageSize" :type="styles.indicator_style" align="center"
						:color="styles.indicator_color" :bottom="15" />
				</div>
			</template>
		</div>
	</widget-root>
</template>

<script type="text/javascript">
	import item from './item.vue'
	export default {
		components: {
			item
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
				current: 0
			}
		},
		methods: {
			swiperChange({
				detail
			}) {
				this.current = detail.current
			}
		},
		computed: {
			pageSize() {
				const {
					content: {
						data
					},
					styles: {
						nav_line,
						nav_line_num
					}
				} = this

				return Math.ceil(data.length / (nav_line * nav_line_num))
			},
			swiperHeight() {
				const {
					content: {
						style
					},
					styles: {
						nav_line,
						nav_line_num
					},
					pageSize,
					navList
				} = this
				// 每行行高
				const lineHeight = style == 1 ? 162 : 66
				// 边距
				const padding = 58
				let height = 0
				
				if(pageSize > 1) {
					height = lineHeight * nav_line + padding
				}else {
					height = lineHeight * Math.ceil(navList.length/nav_line_num) + padding
				}
				return `${height}rpx`
			},
			getPageList() {
				const {
					styles: {
						nav_line,
						nav_line_num
					}
				} = this
				return (index) => {
					const len = nav_line * nav_line_num
					return this.navList.slice(index * len, (index + 1) * len)
				}
			},
			navList() {
				const {
					content: {
						data
					},
					styles: {
						nav_style,
						nav_line,
						nav_line_num
					}
				} = this
				if (!Array.isArray(data)) return []
				return nav_style == 1 ? data.slice(0, nav_line * nav_line_num) : data
			}
		}
	};
</script>
<style lang="scss" scoped>
	.navigation {
		position: relative;
		.navigation-swiper {
			position: relative;
			z-index: 99;
		}
	}
</style>
