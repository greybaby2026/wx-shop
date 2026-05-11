<template>
    <view :class="themeName">
        <u-tabs font-size="28" name="tabsName" inactive-color="#333" :active-color="themeColor" :list="tabList"
            :is-scroll="false" :current="current" @change="change">
        </u-tabs>

        <block v-for="(items,index) in tabList" :key="index">

            <view v-show="index == current">
                <mescroll-uni ref="mescrollRef" bgColor="white" top="100rpx" @init="mescrollInit"
                    :down="downOption" :up="upOption" @down="downCallback" @up="upCallback">
                
                    <!-- 待评价 -->
                   <view v-show="items.type == 0">
                        <view class="bg-white m-b-20" v-for="(item,cIndex) in items.list" :key="cIndex">
                            <view class="flex " style="padding: 20rpx 20rpx 0 20rpx" @click="goPage(item.goods_id)"
                                :data-url="'/pages/goods_details/goods_details?id=' + item.goods_id">
                                <u-image width="180rpx" height="180rpx" lazy-load :src="item.goods_image" />
                                <view class="m-l-20" style="flex: 1">
                                    <view class="line-2 nr">{{item.goods_name}}</view>
                                    <view class="row-between flex m-t-20">
                                        <view class="price flex primary">
                                            <price :content="item.goods_price" color="#101010" mainSize="36rpx"
                                                minorSize="28rpx"></price>
                                        </view>
                                        <view class="muted">
                                            x {{item.goods_num}}
                                        </view>
                                    </view>
                                </view>
                            </view>
                            <view class="flex row-right">
                                <view class="btn flex  row-center m-r-24 m-b-28 br60" @click="goToPostComment(item.id)">
                                    评价商品
                                </view>
                            </view>
                        </view>
                    </view>
                
                    <view v-if="items.type == 1">
                        <view class="bg-white p-l-24 p-r-24 p-b-24 m-b-20" v-for="(item,cIndex) in items.list"
                            :key="cIndex">
                            <view class="muted xs commen-header" v-if="item.goods_comment != null">
                                评论时间： {{item.goods_comment.create_time||'-'}}
                            </view>
                            <view class="nr m-b-24 m-t-24" v-if="item.goods_comment != null">
                                {{item.goods_comment.comment}}
                            </view>
							<view v-if="item.goods_comment.goods_comment_image && item.goods_comment.goods_comment_image.length" class="flex flex-wrap m-t-20">
								<view v-for="(sitem, sindex) in item.goods_comment.goods_comment_image" :key="sindex" class="comment-img"
									@tap="previewImage(item.goods_comment.goods_comment_image, sindex, $event)">
									<u-image width="160rpx" height="160rpx" border-radius="6rpx" :src="sitem.uri" />
								</view>
							</view>
                            <view class="flex goods" style="" @click="goPage(item.goods_id)"
                                :data-url="'/pages/goods_details/goods_details?id=' + item.goods_id">
                                <u-image width="160rpx" height="160rpx" lazy-load :src="item.goods_image" />
                                <view class="m-l-20" style="flex: 1">
                                    <view class="line-2 nr">{{item.goods_name}}</view>
                                    <view class="row-between flex m-t-20">
                                        <view class="muted xs flex" v-if="item.goods_comment != null">
                                           评分 <u-rate :count="5" gutter="10" size="26" inactive-color="#E5E5E5"
                                                inactive-icon="star-fill" v-model="item.goods_comment.goods_comment">
                                            </u-rate>
                                        </view>
                                    </view>
                                </view>
                            </view>
                        </view>
                    </view>

                </mescroll-uni>
            </view>
            
        </block>

    </view>
</template>

<script>
    import {
        apiCommentGoodsLists
    } from "@/api/order.js"
    import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins.js";
    import MescrollMoreItemMixin from "@/components/mescroll-uni/mixins/mescroll-more-item.js";
    export default {
        mixins: [MescrollMixin, MescrollMoreItemMixin],
        data() {
            return {
                tabList: [{
                    tabsName: '待评价',
                    list: [],
                    type: 0
                }, {
                    tabsName: '已评价',
                    list: [],
                    type: 1
                }],
                current: 0,
                
                downOption: {
                    auto: false // 不自动加载 (mixin已处理第一个tab触发downCallback)
                },
                upOption: {
                    auto: true, // 不自动加载
                    noMoreSize: 4, //如果列表已无数据,可设置列表的总数量要大于半页才显示无更多数据;避免列表数据过少(比如只有一条数据),显示无更多数据会不好看; 默认5
                    empty: {
                        icon: '../../../bundle/static/images/null_pingjia.png',
                        tip: '暂无评价数据', // 提示
                        fixed: true,
                        top: "200rpx",
                    }
                }
            }
        },

        async onShow() {
          
        },

        methods: {
            change(index) {
                this.current = index;
                this.upCallback({num:1,size:10})
            },
            
            async upCallback(page) {
                let num = page.num;
                let size = page.size
                let index = this.current
                let ref = await this.$refs;
                apiCommentGoodsLists({
                    type: this.tabList[index].type,
                    page_no: num,
                    page_size: 15
                }).then(res => {
                    if (num === 1) this.tabList[index].list = []
                    this.tabList[index].list = [...this.tabList[index].list, ...res.lists]
                    ref.mescrollRef[index].mescroll.endSuccess(res.lists.length, res.count);
                }).catch(err => {
                    this.mescroll.endErr()
                })
            },

            goPage(id) {
                uni.navigateTo({
                    url: `/pages/goods_detail/goods_detail?id=${id}`
                })
            },

            goToPostComment(goods_id) {
                uni.navigateTo({
                    url: `/bundle/pages/post_comment/post_comment?id=${goods_id}`
                })
            },
			
			previewImage(imgArr, current) {
				console.log(urls, current)
				const urls = imgArr.map(item => item.uri);
				uni.previewImage({
					current,
					// 当前显示图片的http链接
					urls // 需要预览的图片http链接列表
			
				});
			}
        }
    }
</script>

<style lang="scss">
    .btn {
        width: 180rpx;
        @include font_color();
        @include border_color();
        border-width: 1rpx;
        border-style: solid;
        padding: 8rpx 24rpx;
    }

    .commen-header {
        padding: 20rpx 0;
        border-bottom: 1rpx solid #E5E5E5;
    }
	
	.comment-img {
		margin-bottom: 20rpx;
	
		&:not(:nth-last-of-type(4n+1)) {
			margin-right: 20rpx;
		}
	}

    .goods {
        border-radius: 10rpx;
        background-color: #F6F6F6;
        padding: 20rpx 20rpx 0 20rpx
    }
</style>
