<template>
    <view :class="themeName">
        
        <!-- 商品信息 -->
        <view class="flex bg-white m-t-20" style="padding: 20rpx">
            <u-image width="180rpx" height="180rpx" lazy-load :src="goodsData.goods_image" />
            <view class="m-l-20" style="flex: 1">
                <view class="line-2 nr">{{goodsData.goods_name}}</view>
                <view class="row-between flex m-t-20">
                    <view class="price flex primary">
                        <price :content="goodsData.goods_price" color="#101010" mainSize="36rpx"
                            minorSize="28rpx"></price>
                    </view>
                    <view class="muted">
                        x {{goodsData.goods_num}}
                    </view>
                </view>
            </view>
        </view>
        
        <!-- 商品评价 -->
        <view class="bg-white p-l-20">
            <view class="comment flex">
                <view class="nr label">
                    商品评价
                </view>
                <u-rate :count="5" gutter="20" size="38" inactive-color="#E5E5E5" inactive-icon="star-fill" v-model="commentObj.goods_comment"></u-rate>
                <text class="m-l-30 tips-comment" v-if="commentObj.goods_comment==1 || commentObj.goods_comment==2">差评</text>
                <text class="m-l-30 tips-comment" v-if="commentObj.goods_comment==3">中评</text>
                <text class="m-l-30 tips-comment" v-if="commentObj.goods_comment>=4">好评</text>
            </view>
            
            <view class="comment p-t-0">
                <view class="muted nr">
                    其它评价
                </view>
                
                <view class="flex m-t-20">
                    <view class="nr label">
                        描述相符
                    </view>
                    <u-rate :count="5" gutter="20" size="38" inactive-color="#E5E5E5" inactive-icon="star-fill" v-model="commentObj.description_comment"></u-rate>
                </view>
                
                <view class="flex m-t-20">
                    <view class="nr label">
                        服务态度
                    </view>
                    <u-rate :count="5" gutter="20" size="38" inactive-color="#E5E5E5" inactive-icon="star-fill" v-model="commentObj.service_comment"></u-rate>
                </view>
                
                <view class="flex m-t-20">
                    <view class="nr label">
                        配送服务
                    </view>
                    <u-rate :count="5" gutter="20" size="38" inactive-color="#E5E5E5" inactive-icon="star-fill" v-model="commentObj.express_comment"></u-rate>
                </view>
            </view>
        </view>
        
        <!-- 商品描述 -->
        <view class="bg-white m-t-20 p-b-30">
            
            <view class="md p-t-20 p-l-30">
                商品描述
            </view>
            
            <view class="p-l-20 p-r-20 m-t-20 p-b-20">
                <textarea name="address" class="desc nr"
                    v-model="commentObj.comment" placeholder="宝贝收到还满意吗，说说你的使用心得。分享给想买的他们吧！！" auto-blur />
            </view>
            
            <view class="p-l-20">
                <uploader v-model="commentObj.image" :deletable="true" preview-size="160rpx" :maxUpload="6" mutiple image-fit="aspectFill" />
            </view>
            
        </view>
        
        <view class="p-l-30 p-r-30 m-t-50">
            <view class="btn white flex lg row-center br60" @click="onAddComment">
                立即评价
            </view>
        </view>
    </view>
</template>

<script>
    import { apiCommentGoodsInfo, apiCommentAdd } from "@/api/order.js";
    export default {
        data() {
            return {
                goodsData: {}   ,//商品的数据
                commentObj: {
                    order_goods_id: 0,	    //是	int	订单商品id
                    goods_comment: 0,	    //是	int	商品评论星级 1 一星 2 二星 3三星 4四星 5五星
                    service_comment: 0,	    //是	int	服务评论星级 1 一星 2 二星 3三星 4四星 5五星
                    description_comment: 0,	//是	int	描述相符星级1 一星 2 二星 3三星 4四星 5五星
                    express_comment: 0,	    //是	int	物流评论星级 1 一星 2 二星 3三星 4四星 5五星
                    comment: '',            //评论内容
                    image: []       	    //是	array	图片
                }
            }
        },
        
        onLoad(options) {
            this.commentObj.order_goods_id = options.id
            this.getGoodsInfo()
        },
        
        methods: {
            getGoodsInfo() {
                apiCommentGoodsInfo({order_goods_id: this.commentObj.order_goods_id}).then(res => {
                    this.goodsData = res
                })
            },
            
            onAddComment() {
				if( !this.commentObj.goods_comment ) return this.$toast({ title: '请选择商品评价' })
				if( !this.commentObj.description_comment ) return this.$toast({ title: '请选择描述相符' })
				if( !this.commentObj.service_comment ) return this.$toast({ title: '请选择服务态度' })
				if( !this.commentObj.express_comment ) return this.$toast({ title: '请选择配送服务' })
                apiCommentAdd({...this.commentObj}).then(res => {
                    console.log(res)
                    this.$toast({
                        title: '评论成功'
                    }, {
                        tab: 3,
                        url: 1
                    });
                })
            },
            
            // uploader(event) {
            //     console.log(event)
            // }
        }
    }
</script>

<style lang="scss">
    
    .comment {
        padding: 30rpx 0;
        border-top: 1rpx solid #F2F2F2;
        .label {
            width: 140rpx;
            text-align: left;
        }
        .tips-comment {
            @include font_color()
        }
    }
    
    .desc {
        width: 100%;
        height: 240rpx;
        border-radius: 10rpx;
        box-sizing: border-box;
        background-color: #F8F8F8;
        padding:  20rpx 30rpx 0 30rpx;
    }
    
    .btn {
        height: 84rpx;
        @include background_color();
    }
</style>
