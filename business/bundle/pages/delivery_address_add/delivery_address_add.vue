<template>
    <view style="height: 100vh">
        <view class="bg-white">
            <view class="card flex col-top">
                <view class="m-r-30" style="width: 120rpx">联系人</view>
                <view class="" style="flex: 1">
                    <input v-model="contact" placeholder="请输入" />
                </view>
            </view>
        </view>
        <view class="bg-white">
            <view class="card flex col-top" @click="showRegion = true">
                <view class="m-r-30" style="width: 120rpx">所在地区</view>
                <view class="flex row-between" style="flex: 1">
                    <input v-model="region" placeholder="请选择" name="region" />
                    <u-icon color="#707070" name="arrow-down"></u-icon>
                </view>
            </view>
        </view>
        <view class="bg-white">
            <view class="card flex col-top">
                <view class="m-r-30" style="width: 120rpx">详细地址</view>
                <view class="" style="flex: 1">
                    <input v-model="address" placeholder="请输入" />
                </view>
            </view>
        </view>

        <view class="bg-white">
            <view class="card flex col-top">
                <view class="m-r-30" style="width: 120rpx">手机号</view>
                <view class="" style="flex: 1">
                    <input v-model="mobile" placeholder="请输入" />
                </view>
            </view>
        </view>
        <view class="btn-card">
            <button class="btn" @click="handleSubmit">保存</button>
        </view>
        <u-select
            v-model="showRegion"
            confirm-color="#101010"
            cancel-color="#999"
            mode="mutil-column-auto"
            @confirm="regionChange"
            :list="arealists"
        ></u-select>
    </view>
</template>
<script>
import {
    apideliveryaddressadd,
    apideliveryaddressdetial,
    apideliveryaddressEdit
} from '@/api/order'
import area from '@/utils/area'

export default {
    data() {
        return {
            id: '',
            province_id: '',
            city_id: '',
            district_id: '',
            contact: '',
            mobile: '',
            showRegion: false,
            arealists: [],
            region: '',
            address: ''
        }
    },
    methods: {
        handleSubmit() {
            const api = this.id ? apideliveryaddressEdit : apideliveryaddressadd
            api({
                id: this.id,
                contact: this.contact,
                mobile: this.mobile,
                province_id: this.province_id,
                city_id: this.city_id,
                district_id: this.district_id,
                address: this.address
            }).then(() => {
                this.$Router.back()
            })
            uni.$emit('addressedit')
        },
        // 地区选择，选择当前省市区的ID
        regionChange(region) {
            this.province_id = region[0].value
            this.city_id = region[1].value
            this.district_id = region[2].value
            this.region = region[0].label + ' ' + region[1].label + ' ' + region[2].label
        },
        getDetial(id) {
            apideliveryaddressdetial({ id }).then((res) => {
                this.contact = res.contact
                this.mobile = res.mobile
                this.address = res.address
                this.province_id = res.province_id
                this.city_id = res.city_id
                this.district_id = res.district_id
                this.region = res.province + ' ' + res.city + ' ' + res.district
            })
        }
    },
    onLoad() {
        const id = this.$Route.query.id
        if (id) {
            this.id = id
            this.getDetial(id)
        }
        this.arealists = area
    },
    onShow() {}
}
</script>
<style lang="scss" scoped>
page {
    background-color: white;
}
.card {
    background-color: #f7f7f7;
    padding: 30rpx;
    border-radius: 10rpx;
    margin: 20rpx;
}
.btn-card {
    margin-top: 30rpx;
    padding: 0 30rpx;
    z-index: 999;
    background-color: white;
}
.btn {
    background-color: $-color-primary;
    height: 90rpx;
    line-height: 90rpx;
    color: white;
}
</style>
