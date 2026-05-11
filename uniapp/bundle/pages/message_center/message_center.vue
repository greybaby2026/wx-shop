<!-- 消息中心 -->
<template>
	<view class="message-center-centent">
		<navigator class="message-item flex" v-for="(item, index) in messageList" :key="index"
			hover-class="none" :url="'/bundle/pages/notice/notice?type=' + item.type">
			<u-image width="76rpx" height="76rpx" border-radius="50%" :src="item.img"></u-image>
			<view class="item-info m-l-20 flex-col row-center">
				<view class="item-title">
					{{item.title}}
				</view>
				<view class="item-desc line-1 muted">
					{{item.content}}
				</view>
			</view>
		</navigator>
        
        <loading-view v-if="loading"></loading-view>
	</view>
</template>

<script>
    import {apiNoticeGet} from "@/api/store.js"
	export default {
		data() {
			return {
                loading: true,
				messageList: [{
					type: '系统通知',
					img: '../../static/images/msgCenter_icon_system.png',
					title: 'hszz',
					content: 'hszz',
				}, {
					type: 'earning',
					img: '',
					title: 'hszz',
					content: 'hszz',
				}]
			};
		},
        
        methods: {
            getNoticeData() {
                apiNoticeGet().then(res => {
                    this.messageList = res;
                    
                    setTimeout(() => {
                        this.loading = false
                    }, 500)
                })
            }
        },
        
        onShow() {
            this.getNoticeData()
        }
	}
</script>

<style lang="scss">
	.message-center-centent {
		margin-top: 10rpx;
		padding: 0 20rpx;
		background-color: white;

		.message-item {
			padding: 32rpx 0;

			&:not(:last-of-type) {
				border-bottom: $-solid-border;
			}

			.item-info {
				font-size: 32rpx;
				flex: 1;
				overflow: hidden;

				.item-title {
					font-size: 32rpx;
				}

				.item-desc {
					font-size: 26rpx;
				}
			}
		}
	}
</style>
