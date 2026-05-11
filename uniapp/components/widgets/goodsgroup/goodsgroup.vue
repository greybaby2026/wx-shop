<template>
	<widget-root :styles="styles">
		<goods :content="content" :styles="styles" />
	</widget-root>
</template>

<script type="text/javascript">
	import goods from './goods.vue'
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
			return {}
		},
		methods: {
			navigate(item) {
				this.$Router.push({
					path: '/pages/goods_detail/goods_detail',
					query: {
						id: item.id
					}
				})
			}
		},
		computed: {

			goods() {
				const {
					data
				} = this.content
				return data.length ? data : []
			},
			btnStyle() {
				const {
					btn_bg_color,
					btn_color,
					btn_border_radius,
					btn_border_color
				} =
				this.styles
				const {
					btn_bg_type
				} = this.content
				const style = {
					"background-color": btn_bg_color,
					color: btn_color,
					'border-radius': `${this.$px2rpx(btn_border_radius)}rpx`,
					'border-color': btn_border_color,
				}
				if (btn_bg_type == 2) delete style['background-color']
				return style
			}
		}
	};
</script>
<style lang="scss" scoped>

</style>
