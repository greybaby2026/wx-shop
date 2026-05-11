<!-- 会员中心 -->
<template>
	<view>
		<view class="user-vip">
			<!-- 顶部会员信息 -->
			<view class="header flex col-top">
				<u-image class="m-l-20" width="110rpx" height="110rpx" shape="circle" :src="user.avatar"></u-image>
				<view class="m-l-20">
					<view class="user-text white xxl">
						{{user.nickname || '无'}}
					</view>
					<view class="user-level white xs">
						当前等级：{{user.level_name}}
					</view>
				</view>
			</view>

			<view class="content">
				<!-- 会员轮播卡片 -->
				<view class="vip-swiper-container">
					<swiper class="swiper" style="height: 320rpx" previous-margin="60rpx" next-margin="60rpx"
						display-multiple-items="1" :current="currentIndex" @change="bindchange">
						<swiper-item v-for="(item, index) in levelList" :key="index">
							<view class="vip-card-item"
								:style="'background-image: url(' + item.background_image + ');'">
								<!-- 卡片顶部标识 -->
								<view class="flex row-between col-top flex-1">
									<view>
										<!-- <view v-if="user.level > item.rank" class="flex grade white sm">已解锁
										</view>
										<view v-if="user.level < item.rank" class="flex grade white sm">未解锁
										</view>
										<view v-if="user.level == item.rank" class="flex grade white sm">当前等级
										</view> -->
										<view v-if="item.status == 1" class="flex grade white sm">当前等级
										</view>
									</view>
									<u-image width="120rpx" height="100rpx" :src="item.image"></u-image>
								</view>
								<view class="flex row-between vip-name white">
									<view class="bold">{{item.name}}</view>
								</view>
								<view class="flex row-between white vip-centent">
									<view v-if="index+1 >= levelList.length" class="sm">
										{{user.rank > item.rank ? '当前高于该等级': '下一等级：-' }}
									</view>
									<view v-else class="sm">
										{{user.rank > item.rank ? '当前高于该等级': '下一等级：'+ levelList[index+1].name}}
									</view>
									<view class="flex" v-if="item.rank != 1">
										<view class="sm m-r-10">{{item.discount}}</view>
										<u-image width="46rpx" height="30rpx" src="@/bundle/static/images/vip/icon_zhekou.png">
										</u-image>
									</view>
								</view>
							</view>
						</swiper-item>
					</swiper>
				</view>

				<!-- 升级条件 -->
				<view class="vip-grade-rule" v-if="condition.length">

					<view class="title flex">
						<view class="xl bold">等级升级条件</view>
						<view class="xs muted m-l-20">{{condition_tips}}</view>
					</view>
					<view class="checkbox-item" v-for="(item,index) in condition" :key="index" :class="index <= condition.length-1?'checkbox-item-border':''">
						<view class="flex">
							<image src="@/bundle/static/images/vip/icon_gou.png" mode=""
								style="height: 42rpx; width: 42rpx;"></image>
							<view class="m-l-20">{{item}}</view>
						</view>
					</view>
				</view>
			</view>
		</view>


		<loading-view v-if="!user.nickname"></loading-view>
	</view>
</template>

<script>
	import {
		apiUserlevel
	} from "@/api/user";
	export default {
		data() {
			return {
				// 用户信息
				user: {},
				// 轮播id
				currentIndex: 0,
				// 等级列表
				levelList: [],
				// 条件列表
				condition: [],
				// 条件描述
				condition_tips: '',

			}
		},
		methods: {
			// 等级卡片切换时等级升级条件改变
			bindchange(e) {
				let {
					current
				} = e.detail;
				let currentLevel = this.levelList[current];
				this.currentIndex = current
				this.condition = currentLevel.condition
				this.condition_tips = currentLevel.condition_tips
			},

			// 获取会员等级信息
			getUserlevel() {
				apiUserlevel()
					.then(res => {
						console.log('res', res)
						this.user = res.user
						this.levelList = res.level_list

						// 首次进入，根据用户等级 匹配对应 等级卡片块
						this.levelList.forEach((item, index) => {
							if (item.id == this.user.level) {
								this.currentIndex = index
								this.condition = item.condition
								this.condition_tips = item.condition_tips
							}
						})
					})
					.catch(res => {
						console.log('error', res)
					})
			},
		},

		created() {
			this.getUserlevel()
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #fff;

		.user-vip {
			.header {
				width: 750rpx;
				height: 412rpx;
				//background: linear-gradient(#333 0%, #333 100%);

				background-image: url('../../static/images/vip/vip_grade_bg.png');
				padding-top: 30rpx;
				background-size: 100% 100%;
				height: 382rpx;


				// padding-left: 30rpx;

				.user-level {
					border: 1px solid white;
					border-radius: 100rpx;
					padding: 4rpx 20rpx;
					line-height: 30rpx;
				}

				.user-text {
					line-height: 50rpx;
					font-weight: bold;
				}
			}



			.content {

				margin-top: -200rpx;

				.vip-card-item {
					border-radius: 20rpx;
					//background-color: #71D5A1;
					height: 320rpx;
					width: 600rpx;
					position: relative;
					background-size: 100% 100%;

					.grade {
						line-height: 36rpx;
						background-color: rgba(0, 0, 0, 0.3);
						border-bottom-right-radius: 20rpx;
						border-top-left-radius: 20rpx;
						height: 50rpx;
						padding: 0 28rpx;
					}

					// .user-grade {
					// 	line-height: 36rpx;
					// 	margin-left: 30rpx;
					// }

					// .grade-icon {
					// 	width: 120rpx;
					// 	height: 100rpx;
					// }

					.vip-name {
						padding: 20rpx 30rpx 14rpx 30rpx;
						font-size: 46rpx;
						text-align: center;
						align-items: flex-end;
						//margin-bottom: 30rpx;
					}

					.vip-centent {
						padding: 0 30rpx;
						// font-size: 46rpx;
						// text-align: center;
						// align-items: flex-end;
						// margin-bottom: 30rpx;
					}
				}

				.vip-grade-rule {
					margin: 30rpx 40rpx;

					.title {
						height: 100rpx;
						background: #fff;
						padding: 29rpx 0 31rpx 0;
					}

					.checkbox-item {
						height: 100rpx;
						background: #fff;
						padding: 29rpx 0 31rpx 0;

						font-family: "PingFang SC";
						font-weight: normal;
						font-size: 28rpx;
						letter-spacing: 0.02rpx;
						text-align: left;
						color: #333;
					}

					.checkbox-item-border {
						border-bottom: 1rpx solid $-color-border;
					}
				}
			}
		}
	}
</style>
