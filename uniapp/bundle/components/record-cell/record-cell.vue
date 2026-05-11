<!-- 账户明细 单元格 -->
<template>
    <view class="record-cell flex row-between" :class="themeName">
        <!-- Left -->
        <view>
            <view class="remark md">{{ remark }}</view>
            <view class="time m-t-10 muted sm">{{ date }}</view>
        </view>
        
        <!-- Right -->
        <view class="black">
            <view class="money lg" :class="{'money-add': type == 2}">{{ money }}</view>
        </view>
    </view>
</template>

<script>
    export default {
        name: 'record-cell',
        
        props: {
            // 备注
            remark: {
                type: String,
                required: true
            },
            // 时间
            date: {
                type: String,
                required: true
            },
            // 金额
            money: {
                type: String | Number,
                required: true
            },
			type: {
				 type: String | Number,
			}
        },
        
        methods: {
          
        }
    }
</script>

<style lang="scss" scoped>
.record-cell {
	padding: 16rpx 30rpx;
	border-bottom: $-solid-border;
	.money-add {
		@include font_color();
	}
}
</style>
