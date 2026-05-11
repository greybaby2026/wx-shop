<template>
	<view :class="themeName">
		<!-- #ifndef H5 -->
		<navbar title="砍价进度"></navbar>
		<!-- #endif -->

		<view class="bargain-progress">
			<!-- 顶部 -->
			<view class="header">
				<view class="" v-if="status != 4">
					<view class="white" style="font-size: 36rpx; margin-bottom: 11rpx" v-if="status == 0">
						您正在发起砍价
					</view>
					<view class="white" style="font-size: 36rpx; margin-bottom: 11rpx" v-if="status == 1">
						砍价中
					</view>
					<view class="white" style="font-size: 36rpx; margin-bottom: 11rpx" v-if="status == 2">
						砍价成功
					</view>
					<view class="white" style="font-size: 36rpx; margin-bottom: 11rpx" v-if="status == 3">
						砍价失败
					</view>
					<view class="white" style="font-size: 22rpx; margin-bottom: 30rpx" v-if="status == 0">
						邀请好友帮忙砍价 —— 砍至{{ bargainData.min_price || '' }}元即可发货
					</view>
					<view class="white" style="font-size: 22rpx; margin-bottom: 30rpx"
						v-if="status == 1 || status == 2 || status == 3">
						邀请好友帮忙砍价 —— 砍至{{ bargainData.floor_price }}元即可发货
					</view>
				</view>

				<view class="flex-col" v-if="status == 4">
					<view class="flex m-d-30">
						<u-image width="80rpx" height="80rpx" shape="circle" :src="bargainData.initiate_user.avatar" />
						<view class="flex-col m-l-20">
							<view class="white" style="font-size: 28rpx">
								来自{{ bargainData.initiate_user.nickname }}的分享
							</view>
							<view class="white" style="font-size: 22rpx">
								谢谢你的助力，动动手指帮我砍一刀
							</view>
						</view>
					</view>
				</view>
				<!-- 商品详情 -->
				<view class="goods-item flex bg-white m-t-30">
					<u-image width="180rpx" height="180rpx" border-radius="10rpx" :src="bargainData.goods_image" />
					<view class="goods-info m-l-15 flex-1">
						<view class="goods-name line-2 m-b-10">{{
                            bargainData.goods_name || ''
                        }}</view>
						<view class="info-footer flex row-between m-t-5" v-if="status == 0">
							<view class="flex-col">
								<price :content="bargainData.bargain_min_price" prefix="最低可砍至￥" :color="themeColor"
									mainSize="34rpx" minorSize="24rpx" />
								<price :content="bargainData.goods_max_price" prefix="原价￥" color="#999999"
									mainSize="24rpx" minorSize="24rpx" />
							</view>
						</view>

						<view class="info-footer flex row-between m-t-5 flex-1"
							v-if="status == 1 || status == 2 || status == 3 || status == 4">
							<view class="flex-col flex-1">
								<view class="" style="color: #999999; font-size: 24rpx">
									{{ bargainData.item_spec_value_str }}
								</view>
								<view class="flex row-between flex-1">
									<view class="">
										<price :content="bargainData.item_sell_price" prefix="原价￥" color="#999999"
											mainSize="24rpx" minorSize="24rpx" />
									</view>
									<view class="flex" v-if="status == 1">
										<!-- <u-count-down 
										  :timestamp="bargainData.end_time - nowTime"
										  color="#FFFFFF"
										  :bg-color="themeColor"
										  :separator-color="themeColor"
										/> -->
										<view class="flex">
											<u-count-down :timestamp="bargainData.end_time - nowTime" height="36"
												separator="zh" :show-hours="false" :show-minutes="false"
												:show-seconds="false" :font-size="24" :separator-size="22" />
											<u-count-down :timestamp="
                                                    (bargainData.end_time - nowTime) %
                                                    (60 * 60 * 24)
                                                " color="#FFFFFF" height="36" separator="colon" :show-days="false"
												:font-size="24" :separator-size="22" :bg-color="bgColor || themeColor"
												:separator-color="separatorColor || themeColor" />
										</view>
										<!-- <count-down :time-end="bargainData.end_time" /> -->
										<view class="muted xs m-l-5"> 后结束帮砍 </view>
									</view>
								</view>
							</view>
						</view>
					</view>
				</view>
			</view>
			<!-- 请选择商品规格 未砍价时才有 -->
			<view class="content" v-if="status == 0">
				<view class="title"> 请选择商品规格 </view>

				<view class="choose-content flex row-between">
					<view class="flex">
						<view class="muted sm flex-none">已选：</view>
						<view class="md normal">
							{{ specValueStr || '' }}
						</view>
					</view>
					<!-- <u-icon name="arrow-right" size="28" color="#999" /> -->
				</view>

				<!-- 规格选择Popup -->
				<goods-spec :defaultInfo="{
                        image: bargainData.image,
                        price: bargainData.goods_max_price,
                        stock: bargainData.total_stock,
                        bargain_price: bargainData.min_price
                    }" :stock_show="bargainData.stock_show" v-model="showGoodsSpec" :default-select="true"
					:is-bargain="true" :goods="bargainData.goods_id" :buttons="specButtonsList"
					@change="changeGoodsSpec" @confirm="handleConfirm" @closer="showGoodsSpec = false"
					:spec-list="bargainData.spec_value" :spec-map="bargainData.spec_value_list" />

				<view class="lunch-btn white br60 flex row-center" @click="onSelect">发起砍价</view>
			</view>

			<!-- 砍价进度 发起砍价后有/查看砍价进度 -->
			<view class="progressing" v-if="status == 1 || status == 2 || status == 3">
				<view class="progressing-heared">
					已砍<text class="progressing-text">{{ bargainData.help_total }}</text>元，还差<text
						class="progressing-text">{{ bargainData.help_diff }}</text>元
				</view>
				<view class="progress-container row-center flex">
					<view class="progress m-r-16">
						<view class="progress-bar" :style="{ width: bargainData.precent + '%' }" />
					</view>
					<price :content="bargainData.floor_price" prefix="￥" mainSize="24rpx" minorSize="24rpx"
						:color="themeColor" />
				</view>
				<!-- 是否成功文字描述 -->
				<view class="m-b-35 flex row-center">
					<view class="" v-if="status == 2"> 恭喜您，砍价成功 </view>
					<view class="" v-if="status == 3"> 非常遗憾，砍价失败 </view>
				</view>
				<!-- 按钮 -->
				<view class="p-btn">
					<view class="" v-if="status == 1">
						<view class="flex flex-1" v-if="bargainData.btns.buy_now_btn">
							<view class="flex row-center m-r-16 flex-1 btn-event" @click="goToConfirm('random')">
								<view class="lunch-btn-event white br60 flex row-center flex-1">
								</view>
								<view class="lunch-btn-font">
									¥{{ bargainData.current_price }} 直接购买
								</view>
							</view>
							<view class="lunch-btn white br60 flex row-center flex-1" @click="shareToBargain">邀请好友帮砍价
							</view>
						</view>
						<view class="lunch-btn white br60 flex row-center" @click="shareToBargain" v-else>邀请好友帮砍价</view>
					</view>
					<view class="" v-if="status == 2">
						<view class="lunch-btn white br60 flex row-center" @click="goToConfirm('floor')"
							v-if="bargainData.btns.buy_allow_btn">立即购买</view>
						<view class="lunch-btn white br60 flex row-center" @click="goToOrder" v-else>查看订单</view>
					</view>
					<view class="" v-if="status == 3">
						<view class="lunch-btn-fail white br60 flex row-center">砍价失败</view>
					</view>
				</view>

				<!-- 砍价记录 -->
				<view class="bargain-code-content">
					<view class="bargain-code-title flex row-between">
						<view class="md normal bold"> 砍价记录 </view>
						<view class="muted xs"> 已有{{ bargainData.help_num }}人帮砍 </view>
					</view>
					<view class="code-content">
						<view class="user-item row-between flex" v-for="(item, index) in bargainData.help_record"
							:key="item.id">
							<view class="user-content flex">
								<u-image width="80rpx" height="80rpx" shape="circle" :src="item.avatar" />
								<view class="m-l-20">
									<view class="sm normal">
										{{ item.nickname }}
									</view>
									<view class="muted xxs"> 小刀一砍，惊喜满满 </view>
								</view>
							</view>
							<view class="bargain-price xs"> ¥{{ item.reduce_amount || 0 }} </view>
						</view>
					</view>
				</view>
			</view>

			<!-- 分享进入 -->
			<view class="shareDetails" v-if="status == 4">
				<!-- 按钮 -->
				<view class="s-btn">
					<!-- <view class="lunch-btn white br60 flex row-center"
						@click="onBargain">帮忙砍一刀</view> -->
					<view class="tips m-b-30" v-if="bargainData.tips">
						{{ bargainData.tips }}
					</view>
					<view class="lunch-btn white br60 flex row-center" v-if="bargainData.btns.help_btn"
						@click="onBargain">帮忙砍一刀</view>
					<view class="lunch-btn-fail br60 flex row-center" v-if="bargainData.btns.initiate_btn"
						@click="onGoodsBargain">我也要砍价</view>
				</view>

				<!-- 砍价记录 -->
				<view class="bargain-code-content">
					<view class="bargain-code-title flex row-between">
						<view class="md normal bold"> 砍价记录 </view>
						<view class="muted xs">
							已有{{ bargainData.help_record.length }}人帮砍
						</view>
					</view>
					<view class="code-content">
						<view class="user-item row-between flex" v-for="(item, index) in bargainData.help_record"
							:key="item.id">
							<view class="user-content flex">
								<u-image width="80rpx" height="80rpx" shape="circle" :src="item.avatar" />
								<view class="m-l-20">
									<view class="sm normal">
										{{ item.nickname }}
									</view>
									<view class="muted xxs"> 小刀一砍，惊喜满满 </view>
								</view>
							</view>
							<view class="bargain-price xs"> ¥{{ item.reduce_amount || 0 }} </view>
						</view>
					</view>
				</view>
			</view>

			<!-- 砍价成功弹窗/帮砍成功弹窗 -->
			<u-popup v-model="showBargainPop" mode="center">
				<view class="bargain-pop-container">
					<view class="md normal bold flex row-center" style="padding-top: 64rpx">
						恭喜您成功砍下<text class="lg" style="color: #ff2c3c">{{
                            reduceAmount.toFixed(2) || 0
                        }}</text>元
					</view>
					<view class="bold md flex row-center" style="margin-top: 38rpx" v-if="status != 4">
						还差<text class="m-l-20 differ-price" style="color: #ff2c3c">{{ diffPrice }}<text
								style="font-size: 40rpx">元</text></text>
					</view>
					<view class="bold xxl flex row-center" style="margin-top: 38rpx; color: #ff2c3c" v-if="status == 4">
						已为好友砍价成功
					</view>
					<view class="progress-container row-center flex">
						<view class="progress">
							<view class="progress-bar" :style="{ width: precent + '%' }" />
						</view>
					</view>
					<view class="row-center flex">
						<view class="invite-btn row-center md flex" @click="shareToBargain" v-if="status != 4">
							邀请好友帮砍
						</view>
						<view class="invite-btn row-center md flex" @click="closeBargainPop" v-if="status == 4">
							确定
						</view>
					</view>
				</view>
				<view class="close-icon flex row-center" @click="closeBargainPop" mode="bottom">
					<u-icon name="close-circle" size="68rpx" color="#fff"></u-icon>
				</view>
			</u-popup>

			<!-- 分享弹窗 -->
			<share-popup v-model="showSharePop" :share-id="initiateId"
				pagePath="bundle/pages/bargain_progress/bargain_progress" :type="4" :bargain="{
                    goodsId: bargainData.goods_id,
                    activityId: bargainData.activity_id
                }" :config="{
                    image: bargainData.goods_image,
                    name: bargainData.goods_name
                }" />
		</view>

		<!-- 加载动画 -->
		<page-status :status="pageStatus">
			<template #error>
				<u-empty text="加载异常"></u-empty>
			</template>
		</page-status>
	</view>
</template>

<script>
	import {
		apiBargainDetail,
		apiBargainInitiate,
		apiBargainProgress,
		apiBargainShareDetail,
		apiBargainHelp
	} from '@/api/bargain.js'
	import {
		OrderTypeEnum
	} from '@/utils/enum'
	import {
		strToParams
	} from '@/utils/tools.js'
	import {
		PageStatusEnum
	} from '@/utils/enum'
	import {
		apiUserCentre
	} from '@/api/user'
	import wechath5 from '@/utils/wechath5'

	export default {
		data() {
			return {
				// 页面加载状态
				pageStatus: PageStatusEnum['LOADING'],

				activityId: 0, // 砍价活动id
				goodsId: 0, // 商品id

				// 砍价未开始数据
				bargainData: {
					help_record: []
				},

				showGoodsSpec: false, // 商品规格: 显示 | 隐藏
				specButtonsList: [], // 弹窗自定义按钮
				goodsSpecOptions: {}, // 已选商品规格

				// 规格按钮组
				specButtonsGroups: {
					confirm: {
						event: 'confirm',
						text: '确认'
					}
				},
				// 发起砍价放回数据
				reduceAmount: 0, // 自己的帮砍金额
				diffPrice: 0, // 还差多少到底价的金额
				needHelp: 0, // 总共需要砍的价格
				currentPrice: 0, // 当前价格
				precent: 0, // 砍价进度条百分比
				initiateId: 0, // 砍价id

				showBargainPop: false, // 砍价成功弹窗
				showSharePop: false, // 分享弹窗

				// 分享信息
				status: 0, // 页面状态 0-未发起（发起砍价 1-砍价中 2-砍价成功 3-砍价失败 4-分享进入（分享/海报）
				nowTime: 0 // 当前时间
			}
		},

		computed: {
			// 已选商品规格
			specValueStr() {
				return this.goodsSpecOptions?.spec?.spec_value_str ?? ''
			}
		},

		methods: {
			// 发起砍价页面数据
			bargainDetail() {
				apiBargainDetail({
						activity_id: this.activityId,
						goods_id: this.goodsId
					})
					.then((res) => {
						console.log('res', res)
						this.bargainData = res

						this.pageStatus = PageStatusEnum['NORMAL']
					})
					.catch((err) => {
						console.log(err, '1')
						this.pageStatus = PageStatusEnum['ERROR']
					})
			},

			// 点击选择规格
			onSelect() {
				this.showGoodsSpec = true
				this.specButtonsList = [this.specButtonsGroups.confirm]
			},
			// 更改规格
			changeGoodsSpec(options) {
				this.goodsSpecOptions = options
				console.log(options)
			},
			// 确定->发起砍价
			handleConfirm({
				spec,
				number
			}) {
				if (!this.isLogin) return this.$Router.push('/pages/login/login')
				apiBargainInitiate({
					activity_id: this.activityId,
					item_id: spec.id,
					goods_num: number
				}).then((res) => {
					console.log('发起砍价', res)
					this.reduceAmount = res.reduce_amount
					this.diffPrice = res.diff_price
					this.initiateId = res.initiate_id
					this.needHelp = res.need_help

					this.bargainProgress()

					// 自己不能帮砍，不显示弹窗
					if (!res.reduce_amount && !res.need_help) {
						return
					}

					// 砍价进度条数据
					this.precent = (res.reduce_amount / res.need_help).toFixed(2) * 100
					this.showBargainPop = true
				})
			},

			// 砍价进度
			bargainProgress() {
				apiBargainProgress({
						initiate_id: this.initiateId
					})
					.then((res) => {
						this.bargainData = res
						this.status = res.status
						// 砍价进度条数据
						this.bargainData.precent =
							(res.help_total / (res.help_total + res.help_diff)).toFixed(2) * 100

						// throw 'abcd'
						this.pageStatus = PageStatusEnum['NORMAL']
					})
					.catch((err) => {
						console.log(err, '2')
						this.pageStatus = PageStatusEnum['ERROR']
					})
			},

			// 关闭砍价弹窗
			closeBargainPop() {
				this.showBargainPop = false
			},

			// 邀请好友
			shareToBargain() {
				// this.showBargainPop = false
				// if (this.isHelpKnife) {
				// 	return;
				// }
				// #ifdef H5
				const href = window.location.origin
				const pathname = window.location.pathname

				wechath5.share({
					shareLink: `${href}${pathname}?initiate_id=${this.initiateId}&isShare=2&invite_code=${this.userInfo.code}`
				})
				//#endif
				this.showSharePop = true
			},

			// 监听分享
			// #ifdef MP-WEIXIN
			onShareAppMessage() {
				return {
					path: 'bundle/pages/bargain_progress/bargain_progress?initiate_id=' +
						this.initiateId +
						'&isShare=2' +
						'&invite_code=' +
						this.userInfo.code,
					title: '我正在参与砍价，还差一步',
					imageUrl: this.bargainData.goods_image
				}
			},
			//分享朋友圈
			onShareTimeline() {
				return {
					title: '我正在参与砍价，还差一步',
					query: `initiate_id=${this.initiateId}&isShare=2&invite_code=${this.userInfo.code}`, // 分享参数，注意这里使用query而不是path
			     	imageUrl: this.bargainData.image
				}
			},
			// #endif

			// 分享详情内容
			bargainShareDetail() {
				return new Promise((reslove, reject) => {
					apiBargainShareDetail({
							initiate_id: this.initiateId
						})
						.then((res) => {
							console.log(res, 'bargainShareDetail')
							this.bargainData = res
							reslove(res)
							this.pageStatus = PageStatusEnum['NORMAL']
						})
						.catch((errMsg) => {
							console.log(errMsg)
							//TODO 处理未登录路由不执行next方法
							// if (errMsg == "登录超时，请重新登录" || "请求参数缺token") {
							this.$Router.replace({
								path: '/pages/login/login'
							})
							// }
							reject(errMsg)
							// this.pageStatus = PageStatusEnum["ERROR"];
						})
				})
			},
			// 去订单详情
			goToOrder() {
				this.$Router.push({
					path: '/pages/order_detail/order_detail',
					query: {
						order_id: this.bargainData.order_id
					}
				})
			},
			// 去结算
			goToConfirm(type) {
				this.$Router.replace({
					path: '/pages/goods_order/goods_order',
					query: {
						from: OrderTypeEnum['BARGAIN'],
						payload: {
							initiate_id: this.initiateId,
							buy_condition: type // 购买类型 random-任意金额购买 floor-底价购买
						}
					}
				})
			},

			// 好友砍价
			onBargain() {
				apiBargainHelp({
					initiate_id: this.initiateId
				}).then((res) => {
					console.log('发起砍价', res)
					this.reduceAmount = res.reduce_amount
					this.currentPrice = res.current_price
					this.initiateId = res.initiate_id

					// 砍价进度条数据
					this.precent = ((res.need_help - res.diff_price) / res.need_help).toFixed(2) * 100

					this.$nextTick(function() {
						this.showBargainPop = true
					})
					this.bargainShareDetail()
				})
			},

			// 我也要砍价-》跳转到砍价列表页面
			onGoodsBargain() {
				uni.navigateTo({
					url: '/bundle/pages/goods_bargain/goods_bargain'
				})
			}
		},

		onLoad() {
			// 获取当前时间
			let ndate = new Date()
			this.nowTime = Date.parse(ndate) / 1000
			console.log('nowTime', this.nowTime)

			const options = this.$Route.query
			console.log(options, '------')
			const scene = options['scene'] // 微信二维码
			const id = options?.id // h5二维码
			console.log('scene', scene)

			// 扫码进入
			if (scene || id) {
				scene
					?
					(this.initiateId = strToParams(decodeURIComponent(scene))['id']) :
					(this.initiateId = id)
				console.log('initiateId', this.initiateId)

				this.status = 4
				this.bargainShareDetail().then((data) => {
					// console.log('data', data)
					// console.log('this.bargainData.user_id', data.user_id)
					// 获取用户信息
					if (this.bargainData.user_id == this.userInfo.id) {
						this.bargainProgress()
					}
				})
			} else {
				// 非扫码进入
				// 发起砍价和分享需要用到
				if (options.goods_id) this.goodsId = options.goods_id

				// 发起砍价->发起详情
				if (options.activity_id) {
					this.activityId = options.activity_id
					setTimeout(() => {
						this.bargainDetail()
					}, 50)
					return
				}

				// options.isShare=1即是砍价记录进来  options.isShare=2是微信小程序分享进入
				if (options.isShare == 2) {
					this.status = 4
					this.initiateId = options.initiate_id
					this.bargainShareDetail().then((data) => {
						// 本人进入分享请求砍价进度信息
						console.log(this.bargainData, '======')

						if (this.bargainData.user_id == this.userInfo.id) {
							this.bargainProgress()
						}
					})
				} else if (options.isShare == 1) {
					this.initiateId = options.initiate_id
					this.bargainProgress()
				}
			}
		},

		onPullDownRefresh() {
			if (this.bargainData.user_id == this.userInfo.id) {
				this.bargainProgress()
			} else {
				this.bargainShareDetail()
			}
			setTimeout(() => {
				uni.stopPullDownRefresh()
			}, 500)
		}
	}
</script>

<style lang="scss" scoped>
	.bargain-progress {
		.header {
			padding: 0 24rpx;
			@include background_linear();
			height: 330rpx;
			background-size: 100% 100%;
			padding-top: 30rpx;

			.goods-item {
				padding: 30rpx 20rpx;
				border-radius: 20rpx;
			}
		}

		.content {
			margin-top: 88rpx;
			padding: 0 24rpx;

			.title {
				font-family: 'PingFang SC';
				font-weight: 500;
				font-size: 34rpx;
				text-align: left;
				color: #333;
				margin-bottom: 30rpx;
			}

			.choose-content {
				padding: 30rpx 20rpx;
				background-color: #ffffff;
			}

			.lunch-btn {
				@include background_color(); margin-top: 40rpx;
				width: 702rpx;
				height: 84rpx;
				border-radius: 42rpx;
				// background: #ff2c3c;
				// box-shadow: 0rpx 3rpx 10rpx rgba(235, 36, 51, 0.5);
			}
		}

		.bargain-pop-container {
			width: 559rpx;
			height: 580rpx;
			background-image: url(../../static/images/bg_kanjia.png);
			background-size: 100% 100%;

			.differ-price {
				font-size: 58rpx;
				line-height: 38rpx;
			}

			.progress-container {
				margin-top: 26rpx;

				.progress {
					width: 409rpx;
					height: 20rpx;
					// background-color: #FD498F1A;
					background-color: #f6f6f6;
					border-radius: 50rpx;

					.progress-bar {
						height: 100%;
						width: 0%;
						@include background_linear();
						// background: linear-gradient(90deg, #FA444D 0%, #FD498F 100%);
						border-radius: 50rpx;
						transition: width 0.5s ease;
					}
				}
			}

			.invite-btn {
				background-image: url(../../static/images/coupon_button.png);
				background-size: 100% 100%;
				height: 106rpx;
				color: #7b3200;
				font-weight: 500;
				margin-top: 180rpx;
				width: 478rpx;
			}
		}

		.close-icon {
			margin-top: 26rpx;
		}

		.share-pop-container {
			height: 500rpx;
			background-color: $-color-white;
		}

		.progressing {
			margin: 88rpx 24rpx 0 24rpx;
			padding: 24rpx;
			background-color: #ffffff;

			.progressing-heared {
				margin-bottom: 33rpx;

				.progressing-text {
					@include font_color();
				}
			}

			.progress-container {
				margin-bottom: 35rpx;

				.progress {
					width: 568rpx;
					height: 20rpx;
					background-color: #f6f6f6;
					border-radius: 50rpx;

					.progress-bar {
						height: 100%;
						width: 0%;
						// background: linear-gradient(90deg, #FA444D 0%, #FD498F 100%);
						@include background_linear();
						border-radius: 50rpx;
						transition: width 0.5s ease;
					}
				}
			}

			.p-btn {
				.lunch-btn {
					margin-bottom: 60rpx;
					// width: 702rpx;
					height: 84rpx;
					border-radius: 42rpx;
					@include background_color() // background: #ff2c3c;
					// box-shadow: 0rpx 3rpx 10rpx rgba(235, 36, 51, 0.5);
					;
				}

				.lunch-btn-fail {
					margin-bottom: 60rpx;
					// width: 702rpx;
					height: 84rpx;
					border-radius: 42rpx;
					background: #e5e5e5;
				}

				.lunch-btn-event {
					margin-bottom: 60rpx;

					height: 84rpx;
					border-radius: 42rpx;
					@include background_color();
					opacity: 0.2;
				}

				.lunch-btn-font {
					@include font_color();
					position: absolute;
					top: 22rpx;
				}

				.btn-event {
					position: relative;
				}
			}

			.bargain-code-content {
				margin-top: 62rpx;

				.bargain-code-title {}

				.code-content {
					// margin-top: 30rpx;

					.user-item {
						padding: 15rpx 0;
						margin-bottom: 30rpx;

						.bargain-price {
							@include font_color();
						}
					}
				}
			}
		}

		.shareDetails {
			// margin: 88rpx 24rpx 0 24rpx;
			margin: 68rpx 0rpx 0 0rpx;
			padding: 24rpx;

			// background-color: #FFFFFF;
			.s-btn {
				margin: 0 24rpx;

				.lunch-btn {
					margin-bottom: 30rpx;
					// width: 702rpx;
					height: 84rpx;
					border-radius: 42rpx;
					@include background_color() // background: #ff2c3c;
					// box-shadow: 0rpx 3rpx 10rpx rgba(235, 36, 51, 0.5);
					;
				}

				.lunch-btn-fail {
					margin-bottom: 40rpx;
					// width: 702rpx;
					height: 84rpx;
					border-radius: 42rpx;
					background: #ffffff;
				}
			}

			.bargain-code-content {
				padding: 24rpx;
				background-color: #ffffff;

				// margin-top: 62rpx;
				.bargain-code-title {}

				.code-content {
					margin-top: 30rpx;

					.user-item {
						padding: 15rpx 0;
						margin-bottom: 30rpx;

						.bargain-price {
							@include font_color();
						}
					}
				}
			}
		}

		.tips {
			font-family: 'PingFang SC Bold';
			font-weight: bold;
			font-size: 28rpx;
			text-align: center;
			color: #999;
		}
	}
</style>