<template>
    <view class="m-t-20">
        
        <template v-if="type == 1">
            <view class="item bb">
                <view>联系人</view>
                <view class="flex row-right">
                    <input type="text" placeholder="请输入联系人姓名" v-model="nickname" />
                </view>
            </view>
        </template>
        
        <template v-if="type == 2">
            <view class="item bb">
                <view>联系电话</view>
                <view class="flex row-right">
                    <input type="text" placeholder="请输入联系电话" v-model="mobile" />
                </view>
            </view>
        </template>
        
        <template v-if="type == 3">
            <view class="item bb" style="align-items:flex-start">
                <view>店铺简介</view>
                <view style="text-align: left;" class="m-t-4">
                     <textarea v-model="intro" placeholder="请输入店铺简介"/>
                </view>
            </view>
        </template>
        
        <view class="br60 btn flex row-center white md" @click="onSubmit(type)">
            保存
        </view>
        
        <u-toast ref="uToast" />
    </view>
</template>

<script>    
    import {
        apiSetShopInfo
    } from '@/api/store'
    export default {
        data() {
            return {
                type: 1,    //1=联系人 2=联系电话 3=简介
                
                nickname: '',
                mobile: '',
                intro: ''
            }
        },
        
        onLoad() {
            try{
                const type = this.$Route.query.type;
                if(type) {
                    this.type = type;
                    this.nickname = this.shopInfo.nickname
                    this.mobile = this.shopInfo.mobile
                    this.intro = this.shopInfo.intro
                }
                
            }catch(e){
                //TODO handle the exception
            }
        },
        
        methods: {
            async onSubmit(type) {
                if(type == 1) {
                    const res = await apiSetShopInfo({nickname: this.nickname})
                } else if(type == 2) {
                    const res = await apiSetShopInfo({mobile: this.mobile})
                } else if(type == 3) {
                    const res = await apiSetShopInfo({intro: this.intro})
                }
                
                this.$refs.uToast.show({
                    title: '设置成功',
                    type: 'success'
                })
                setTimeout(() => {
                    this.$Router.back()
                }, 1000)
            }
        }
    }
</script>

<style lang="scss">
    .item {
        padding: 30rpx;
        /* #ifndef APP-NVUE */
        display: flex;
        /* #endif */
        flex-direction: row;
        align-items: center;
        background-color: $-color-white;
        justify-content: space-between;
        >view:first-child {
            width: 180rpx;
            color: $-color-black;
            font-size: $-font-size-nr;
            font-weight: 500;
        }
        >view:last-child {
            flex: 1;
            text-align: right;
            textarea {
                width: 560rpx;
                height: 300rpx;
            }
        }
    }
    
    .btn {
        width: 690rpx;
        height: 88rpx;
        margin: 0 auto;
        margin-top: 40rpx;
        background-color: $-color-primary;
    }
</style>
