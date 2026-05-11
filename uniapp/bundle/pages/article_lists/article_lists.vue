<template>
	<view class="article-lists">
		<u-tabs :list="categoryLists" :is-scroll="true" :current="current" @change="changeTab"></u-tabs>
		
		<mescroll-uni ref="mescrollRef" bgColor="white" top="80rpx" @init="mescrollInit" :down="downOption"
			:up="upOption" @down="downCallback" @up="upCallback">
			<block v-for="(item, index) in articleList" :key="index">
				<view class="flex flex-1 row-between card bg-white" @click="toNoticeDetail(item.id)">
					<!-- 左侧信息 -->
					<view class="flex-col flex-1 col-between " style="height: 180rpx;">
						<!-- 标题 -->
						<view class="nr bold line-2">{{item.title}}</view>
						<!-- 时间&浏览量 -->
						<view class="xs muted flex flex-1 row-between col-bottom">
							<view class="">
								{{item.create_time}}
							</view>
							<view class="">
								<u-icon name="eye" size="30" />
								<text class="m-l-8">{{item.visit}}人浏览</text>
							</view>
						</view>
					</view>
					<!-- 右侧图片 -->
					<view class="m-l-20" style="min-width: 240rpx;">
						<u-image width="240" height="180" :src="item.image" />
					</view>
				</view>
			</block>
		</mescroll-uni>
	</view>
</template>

<script>
	import {
		apiArticleCategoryLists,
		apiArticleLists
	} from "@/api/store.js"
	import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins.js";
	import MescrollMoreItemMixin from "@/components/mescroll-uni/mixins/mescroll-more-item.js";
	export default {
		mixins: [MescrollMixin, MescrollMoreItemMixin],
		data() {
			return {
				// 文章资讯列表
				articleList: [], 

				downOption: {
					auto: false // 不自动加载 (mixin已处理第一个tab触发downCallback)
				},

				upOption: {
					auto: true, // 不自动加载
					noMoreSize: 4, //如果列表已无数据,可设置列表的总数量要大于半页才显示无更多数据;避免列表数据过少(比如只有一条数据),显示无更多数据会不好看; 默认5
					empty: {
						icon: '/static/images/empty/notice.png',
						tip: '暂无资讯文章', // 提示
						fixed: true,
						top: "200rpx",
					}
				},
				
				current: 0, // 分类索引 
				categoryLists: [
					{
						name: '全部',
						id: ' ',
					},
				], // 分类数据
			}
		},
		methods: {
			upCallback({
				num
			}) {
				apiArticleLists({
					page_no: num,
					page_size: 15,
					cid: this.categoryLists[this.current].id,
				}).then(res => {
					if (num == 1) this.articleList = []; //如果是第一页需手动置空列表
					this.articleList = this.articleList.concat(res.lists); //追加新数据
					this.mescroll.endSuccess(res.lists.length, res.count == 0 ? false : true, '');
				}).catch(err => {
					this.mescroll.endErr()
				})
			},
			
			// 去到详情
			toNoticeDetail(id) {
				console.log(id)
				this.$Router.push({
					path: '/bundle/pages/store_notice_detail/store_notice_detail',
					query: {
						id: id,
						type: 'article', 
					}
				})
			},
			
			// 分类切换
			changeTab(index) {
				console.log('index', index)
				this.current = index;
				this.upCallback({
					num: 1,
				})
			},
			
			// 获取分类列表
			getArticleCategoryLists() {
				apiArticleCategoryLists()
				.then(res => {
					this.categoryLists.push(...res.lists)
					// console.log('res', res)
				})
				.catch(err => {
					console.log('err', err)
				})
			}
		},

		onShow() {
			this.upCallback({
				num: 1
			})
		},
		
		onLoad() {
			this.getArticleCategoryLists()
		}
	}
</script>


<style lang="scss" scoped>
	.article-lists {
		// padding: 20rpx 0;
		.card {
			margin: 20rpx 20rpx 0;
			padding: 20rpx;
			border-radius: 14rpx;
		}
	}
</style>
