<template>
    <view :class="themeName">
        <view class="address-edit">
            <view class="p-l-30 p-r-30 bg-white">
        
                <!-- 联系方式 -->
                <view class="form-item bb flex">
                    <view class="label">收货人</view>
                    <input class="m-l-10" v-model="addressObj.contact" name="contact" type="text" placeholder="请填写收货人姓名" />
                </view>
                <view class="form-item bb flex">
                    <view class="label">联系方式</view>
                    <input class="m-l-10" name="mobile" v-model="addressObj.mobile" type="number"
                        placeholder="请填写手机号码"></input>
                </view>
        
                <!-- 地区选择 -->
                <view @click="showRegion = true">
                    <view class="form-item bb flex">
                        <view class="label">所在地区</view>
                        <view class="flex flex-1 row-between m-l-10" style="width: 100%;">
                            <input name="region" v-model="region" disabled type="text" placeholder="请选择省、市、区"></input>
                            <u-icon color="#707070" name="arrow-right"></u-icon>
                        </view>
                    </view>
                </view>
        
                <!-- 详细地址填写 -->
                <view class="form-item flex col-top bb m-t-10" style="height: 170rpx;">
                    <view class="label m-t-20">详细地址</view>
                    <textarea name="address" class="nr flex-1 m-l-10 p-t-25" style="height: 130rpx;"
                        v-model="addressObj.address" placeholder="请填写小区、街道、门牌号等信息" auto-blur />
                </view>
        
                <view>
                    <view class="m-t-10 m-b-10 bg-white flex row-between form-item">
                        <view class="label" style="width: 250rpx;">
                            设置为默认地址
                        </view>
                        <u-switch size="35" v-model="addressObj.is_default" :active-color="themeColor"
                            inactive-color="#eee"></u-switch>
                    </view>
                </view>
        
            </view>
        
            <button class="my-btn white flex br60 row-center" @click="onSubmit">完成</button>
        
            <!-- 地址选择 -->
            <!-- <u-select v-model="showRegion" :confirm-color="themeColor" cancel-color="#999" mode="mutil-column-auto"
                 :list="lists"></u-select> -->
			<u-picker 
				v-model="showRegion"
				:confirm-color="themeColor"
				cancel-color="#999"
				mode="region"
				@confirm="regionChange"
			>
			</u-picker>
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
        apiAddressEdit,
        apiAddressDetail,
        hasRegionCode,
        apiAddresshandleRegion,
        apiAddressAdd
    } from '@/api/user';
    import { trottle } from "@/utils/tools";
    export default {
        data() {
            return {
                addressObj: {
                    contact: '',    //联系方式
                    mobile: '',     //手机号码
                    province: '',   //省
                    city: '',       //市
                    district: '',   //区
                    address: '',    //详细地址
                    is_default: 0  //是否默认
                },
                region: '',         //省市区显示
                addressId: '',      //编辑的商品ID
                defaultRegion: ['广东省', '广州市', '番禺区'],
                defaultRegionCode: '440113',    //地区代码
                showRegion: false,  //显示地区选择的flag
            };
        },

        onLoad: function(options) {
            this.addressId = parseInt(options?.id)
            this.onSubmit=trottle(this.onSubmit,3000)
            if (options?.id) {
                uni.setNavigationBarTitle({
                    title: '编辑地址'
                });
                this.apiAddressDetailFun();
            } else {
                uni.setNavigationBarTitle({
                    title: '添加地址'
                });
                this.getWxAddressFun();
            }
        },
        onUnload: function() {
            uni.removeStorageSync('wxAddress');
        },
        methods: {
            // 是否默认地址
            changeChecked(e) {
                if (e) {
                    this.addressObj.is_default = 1
                } else {
                    this.addressObj.is_default = 0
                }
            },

            // 提交地址信息
            onSubmit () {
                let addressId = this.addressId;
                let addressObj = {
                    province_id: this.addressObj.province_id,
                    city_id: this.addressObj.city_id,
                    district_id: this.addressObj.district_id,
                    type: this.addressObj.type,
                    is_default: this.addressObj.is_default,
                    address: this.addressObj.addressId
                }
				this.addressObj.is_default = this.addressObj.is_default ? 1 : 0
                if (!this.addressObj.contact) return this.$toast({
                    title: '请填写收货人姓名'
                });
                if (!this.addressObj.contact.length >= 20) return this.$toast({
                    title: '输入的收货人长度不得大于20位'
                })
                if (!this.addressObj.mobile) return this.$toast({
                    title: '请填写手机号码'
                });
                if (!this.region) return this.$toast({
                    title: '请选择地区'
                });
                if (!this.addressObj.address) return this.$toast({
                    title: '请填写小区、街道、门牌号等信息'
                });

                if (addressId) {
                    apiAddressEdit({...this.addressObj}).then(res => {
                        this.$toast({
                            title: '编辑成功'
                        }, {
                            tab: 3,
                            url: 1
                        });
                    }).catch(err => {
                        return this.$toast({
                            title: err
                        });
                    });
                } else {
                    apiAddressAdd({...this.addressObj}).then(res => {
                        this.$toast({
                            title: '添加成功'
                        }, {
                            tab: 3,
                            url: 1
                        });
                    }).catch(err => {
                        return this.$toast({
                            title: err
                        });
                    });
                }
            },

            // 地区选择，选择当前省市区的ID
            regionChange(region) {
				console.log(region)
				const { city, province, area } = region
                this.addressObj.province_id =province.value;
                this.addressObj.city_id = city.value;
                this.addressObj.district_id = area.value;
                this.region = province.label + " " + city.label + " " + area.label
            },

            // 编辑的时候获取
            apiAddressDetailFun() {
                apiAddressDetail({
                    id: this.addressId
                }).then(res => {
                    res.type -= 1
                    this.addressObj = res;
                    this.region = `${res.province} ${res.city} ${res.district}`
                });
            },

            getWxAddressFun() {
                let wxAddress = uni.getStorageSync('wxAddress');
                if (!wxAddress) return;
                wxAddress = JSON.parse(wxAddress)
                console.log(wxAddress)
                let {
                    userName: contact,
                    telNumber: mobile,
                    provinceName: province,
                    cityName: city,
                    detailInfo: address
                } = wxAddress;
                let district = wxAddress.countryName || wxAddress.countyName
                apiAddresshandleRegion({
                    province,
                    city,
                    district
                }).then(res => {
                    if (res.province && res.city && res.district) {
						this.region = `${province} ${city} ${district}`;
						this.addressObj.province_id = res.province;
						this.addressObj.city_id = res.city;
						this.addressObj.district_id = res.district;
                    }
					this.addressObj.contact = contact;
					this.addressObj.mobile = mobile
					this.addressObj.address = address
                });
            }
        }
    };
</script>
<style lang="scss">
    .address-edit {
        padding-top: 10rpx;

            .bb {
                border-bottom: 1px solid #e5e5e5;
            }

            .form-item {
                height: 110rpx;

                .label {
                    width: 150rpx;
                    font-weight: 500;
                    font-size: 30rpx;
                }

                input {
                    height: 100%;
                    flex: 1;
                }
            }

        .my-btn {
            @include background_color();
            height: 80rpx;
            margin: 30rpx 26rpx;
            margin-top: 80rpx;
            text-align: center;
        }
    }
</style>
