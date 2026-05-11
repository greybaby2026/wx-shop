<template>
	<view class="sort-icon">
		<!-- Up -->
		<u-icon 
		  name="arrow-up-fill" 
		  :size="size" 
		  :color="status === 'desc' ? activeColor : color"
		  :customStyle="{transform: 'scale(0.55) translateY(5px)'}"
		   
		/>
		<!-- Down -->
		<u-icon 
		  name="arrow-down-fill" 
		  :size="size" 
		  :color="status === 'asc' ? activeColor : color" 
		  :customStyle="{transform: 'scale(0.55)  translateY(-5px)'}"
		/>
	</view>
</template>


<script>
	/**
	 * @description 排序升降序图标
	 * @property {String} status 升降序状态 [desc | asc]
	 * @property {Number|String} size 图标大小 (默认: 28)
	 * @property {String} color 图标颜色 (默认: #999999)
	 * @property {String} active-color 激活状态图标颜色 (默认: #101010)
	 * @example <sort-icon :status="search.price" :active-color="themeColor" />
	 */
	
	export default {
		name: 'SortIcon',
		
		props: {
			// 排序状态: desc | asc 
			status: {
				type: String,
				default: ''
			},
			
			// 图标大小
			size: {
				type: [Number, String],
				default: 28
			},
			
			// 图标颜色
			color: {
				type: String,
				default: '#999999'
			},
			
			// 激活状态图标颜色
			activeColor: {
				type: String,
				default: '#101010'
			},
		}
	}
</script>


<style lang="scss" scoped>
	.sort-icon {
		display: flex;
		flex-direction: column;
		align-items: center;
	}
</style>
