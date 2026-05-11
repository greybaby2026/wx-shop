<template>
	<widget-root :styles="styles">
		<div class="blank" :style="{
                            'height': `${styles.height}px`,
                            'background-color': styles.bg_color,
                        }">

		</div>
	</widget-root>
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
	.blank {
		width: 100%;
		height: 10px;
	}
</style>
