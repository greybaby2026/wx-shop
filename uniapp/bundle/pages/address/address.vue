<template>
    <view :class="themeName">
        <view class="address">
            <!-- 收货地址空状态 -->
            <view class="no-address" v-if="addressList.length==0">
                <image src="@/static/images/empty/address.png"></image>
                <view class="sm muted">暂无添加地址，请添加~</view>
            </view>
            
            <!-- 收货地址的列表数据 -->
            <view class="address-list" v-else>
                <view v-for="(item, index) in addressList" :key="item.id" class="bg-white m-t-20" :data-id="item.id"
                    @tap="onSelect">
                    
                    <view class="user-address">
                        <view class="md bold">
                            {{item.contact}}
                            <text class="m-l-22">{{item.mobile}}</text>
                        </view>
                        <view class="lighter sm m-t-10 line-1">
                            {{item.province||item.country}} {{item.city}} {{item.district}} {{item.address}}
                        </view>
                    </view>
        
                    <view class="operation flex row-between">
                        <radio @click.stop="changeDefault(item.id)" style="transform:scale(0.8)" :color="themeColor" :value="item.id + ''"
                            :checked="item.is_default=='1' ? true : false">
                            <text class="xxl">设为默认</text>
                        </radio>
                        <view class="flex">
                            <view class="sm m-r-25" @click.stop="editAddress(item.id)">
                                <image class="icon-md m-r-5" src="@/static/images/address_icon_edit.png"></image>
                                编辑
                            </view>
                            <view class="sm" @click.stop="showSurePop(item.id)">
                                <image class="icon-md m-r-5" src="@/static/images/address_icon_del.png"></image>
                                删除
                            </view>
                        </view>
                    </view>
        
                </view>
            </view>
        
            <!-- 删除地址 -->
            <u-modal id="delete-dialog" v-model="delPupop" :showCancelButton="true" confirm-text="狠心删除"
                :confirm-color="themeColor" :show-title="false" @confirm="delAddressFun" @cancel="delPupop = false">
                <view class="tips-dialog">
                    <image class="icon-md" src="/static/images/icon_warning.png"></image>
                    <view style="margin-top:30rpx">确认删除该地址吗？</view>
                </view>
            </u-modal>
        
            <!-- 页脚 -->
            <view class="footer flex row-between fixed bg-white">
                <!-- #ifdef H5 || MP-WEIXIN -->
                <view class="btn wx-btn flex row-center br60 m-r-20" @click="getWxAddressFun" v-if="isWeixin">
                    <image class="icon-md m-r-10" src="/static/images/icon_wechat.png"></image>
                    <text class="md" style="font-weight: 300;">微信导入</text>
                </view>
                <!-- #endif -->
                <view class="btn add-btn white md flex br60 row-center" :class="themeName" @click="addAddress">新增收货地址
                </view>
            </view>
        </view>
    </view>
</template>

<script>
    // +----------------------------------------------------------------------
    // | LikeShop100%开源免费商用电商系统
    // +----------------------------------------------------------------------
    // | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
    // | 开源版本可自由商用，可去除界面版权logo
    // | 商业版本务必购买商业授权，以免引起法律纠纷
    // | 禁止对系统程序代码以任何目的，任何形式的再发布
    // | Gitee下载：https://gitee.com/likeshop_gitee/likeshop
    // | 访问官网：https://www.likemarket.net
    // | 访问社区：https://home.likemarket.net
    // | 访问手册：http://doc.likemarket.net
    // | 微信公众号：好象科技
    // | 好象科技开发团队 版权所有 拥有最终解释权
    // +----------------------------------------------------------------------
    // | Author: LikeShopTeam
    // +----------------------------------------------------------------------
    import {
        apiAddressLists,
        apiAddressDel,
        apiAddressEditDefault
    } from '@/api/user';
    import wechath5 from '@/utils/wechath5'
    import {
        isWeixinClient
    } from '@/utils/tools'
    export default {
        data() {
            return {
                addressList: [],    //地址数据列表
                delPupop: false,    //删除弹窗flag
                currentId: 0,       //当前需要删除的ID
                isWeixin: true,     //是不是微信，如果是就显示微信导入地址按钮
            };
        },

        onLoad: function(options) {
            if (options) {
                if (options.type) {
                    this.type = options.type;
                }
            }
            //#ifdef H5
            this.isWeixin = isWeixinClient()
            //#endif
        },

        onShow: function() {
            this.apiAddressListsFun();
        },

        methods: {
            // 获取地址列表
            apiAddressListsFun() {
                apiAddressLists().then(res => {
                    this.addressList = res;
                    console.log(res)
                });
            },
            
            // 选择当前地址作为支付地址
            onSelect(e) {
                if (this.type) {
                    let id = e.currentTarget.dataset.id;
                    uni.$emit('changeAddress', id);
                    uni.navigateBack();
                }
            },
            
            // 新增地址
            addAddress() {
                this.$Router.push('/bundle/pages/address_edit/address_edit')
            },

            // 编辑地址
            editAddress(id) {
                this.$Router.push({
                    path: '/bundle/pages/address_edit/address_edit',
                    query: {
                        id: id
                    }
                })
            },
            
            changeDefault(id) {
                apiAddressEditDefault({id}).then(res => {
                    this.apiAddressListsFun();
                })
            },
            
            // 删除地址弹窗
            showSurePop(id) {
                this.delPupop = true;
                this.currentId = id;
            },

            // 删除地址
            delAddressFun() {
                apiAddressDel({id: this.currentId}).then(res => {
                    this.$toast({
                        title: '删除成功'
                    });
                    this.delPupop = false
                    this.apiAddressListsFun();
                });
            },
            
            // 微信导入地址
            getWxAddressFun() {
                // #ifdef H5
                wechath5.getWxAddress().then((res) => {
                    uni.setStorageSync('wxAddress', JSON.stringify(res));
                    setTimeout(() => {
                        uni.navigateTo({
                            url: `/bundle/pages/address_edit/address_edit`
                        });
                    }, 200);
                })
                // #endif
                // #ifdef MP-WEIXIN
                uni.authorize({
                    scope: 'scope.address',
                    success: function(res) {
                        uni.chooseAddress({
                            success: function(res) {
                                console.log(res,'res');
                                uni.setStorageSync('wxAddress', JSON.stringify(res));
                                setTimeout(() => {
                                    uni.navigateTo({
                                        url: `/bundle/pages/address_edit/address_edit`
                                    });
                                }, 200);
                            },
                            fail: function(res) {
                                if (res.errMsg == 'chooseAddress:cancel') return this.$toast({
                                    title: '取消选择'
                                });
                            }
                        });
                    },
                    fail: function(res) {
                        uni.showModal({
                            title: '您已拒绝导入微信地址权限',
                            content: '是否进入权限管理，调整授权？',
                            success(res) {
                                if (res.confirm) {
                                    uni.openSetting({
                                        success: function(res) {}
                                    });
                                } else if (res.cancel) {
                                    return this.$toast({
                                        title: '已取消！'
                                    });
                                }
                            }

                        });
                    }
                });
                // #endif
            }
        },
		
		onUnload() {
			uni.$emit('changeAddress');
		}
    };
</script>
<style lang="scss">
    .icon-md {
        width: 36rpx;
        height: 36rpx;
        vertical-align: top;
    }

    .address {
        padding-bottom: calc(140rpx + env(safe-area-inset-bottom));

        .no-address {
            padding-top: 400rpx;
            text-align: center;

            image {
                width: 300rpx;
                height: 300rpx;
            }
        }

        .address-list {
            .user-address {
                border-bottom: 1rpx solid #E5E5E5;
                padding: 20rpx 30rpx 25rpx 30rpx;
            }

            .operation {
                height: 80rpx;
                padding: 0 30rpx 0 10rpx;
            }
        }

        .footer {
            left: 0;
            right: 0;
            bottom: 0;
            height: 118rpx;
            position: fixed;
            padding: 0 30rpx;
            box-sizing: content-box;
            padding-bottom: env(safe-area-inset-bottom);

            .icon-md {
                width: 34rpx;
                height: 34rpx;
                vertical-align: middle;
            }

            .btn {
                flex: 1;
                height: 80rpx;
            }

            .wx-btn {
                background: #F2F2F2;
            }

            .add-btn {
                @include background_color();
            }
        }
    }

    .tips-dialog {
        height: 230rpx;
        width: 100%;
        text-align: center;
    }
</style>
