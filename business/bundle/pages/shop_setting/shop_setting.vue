<template>
  <view class="store-setting">
    <view class="container">
      <view class="container__title lighter sm"> 店铺信息 </view>
      <view class="container__card">
        <view class="card-item row-between">
          <view class="card-item__label"> 后台LOGO </view>
          <view class="card-item__content">
            <u-image
              width="100rpx"
              height="100rpx"
              :src="form.logo"
              shape="circle"
              @click="chooseImg"
            ></u-image>
          </view>
        </view>
        <view class="card-item row-between">
          <view class="card-item__label"> 店铺/商城名称 </view>
          <view class="card-item__content">
            <u-input input-align="right" v-model="form.name"></u-input>
          </view>
        </view>
      </view>
    </view>
    <view class="container">
      <view class="container__title lighter sm"> 经营设置 </view>
      <view class="container__card">
        <view class="card-item row-between">
          <view class="card-item__label card-item__label--width">
            小程序商城
          </view>
          <view class="card-item__content">
            <u-switch v-model="form.status"></u-switch>
          </view>
        </view>
      </view>
    </view>
    <view class="container">
      <view class="container__title lighter sm">
        联系方式（有订单产生时，可短信通知商城联系人）
      </view>
      <view class="container__card">
        <view class="card-item">
          <view class="card-item__label card-item__label--width"> 联系人 </view>
          <view class="card-item__content">
            <u-input v-model="form.mall_contact"></u-input>
          </view>
        </view>
        <view class="card-item">
          <view class="card-item__label card-item__label--width">
            手机号码
          </view>
          <view class="card-item__content">
            <u-input v-model="form.mall_contact_mobile"></u-input>
          </view>
        </view>
      </view>
    </view>
    <!-- <view class="container">
			<view class="container__title lighter sm">
				退货地址（有售后订单时，快递退回的地址）
			</view>
			<view class="container__card ">
				<view class="card-item">
					<view class="card-item__label card-item__label--width">
						联系人
					</view>
					<view class="card-item__content">
						<u-input  v-model="form.return_contact"></u-input>
					</view>
				</view>
				<view class="card-item">
					<view class="card-item__label card-item__label--width">
						手机号码
					</view>
					<view class="card-item__content">
						<u-input  v-model="form.return_contact_mobile"></u-input>
					</view>
				</view>
				<view class="card-item">
					<view class="card-item__label card-item__label--width">
						选择地区
					</view>
					<view class="card-item__content flex-1" >
						<u-input  v-model="form.region_address" disabled @click="openAddressSel"></u-input>
						<u-icon name="arrow-down"></u-icon>
					</view>
					
				</view>
				<view class="card-item">
					<view class="card-item__label card-item__label--width">
						详细地址
					</view>
					<view class="card-item__content flex-1">
						<u-input type="textarea"  v-model="form.return_address"></u-input>
					</view>
				</view>
			</view>
		</view> -->
    <view class="footer">
      <view class="fixed-footer">
        <view class="save-btn" @click="submit"> 保存 </view>
      </view>
    </view>
    <u-select
      v-model="showRegion"
      confirm-color="#101010"
      cancel-color="#999"
      mode="mutil-column-auto"
      @confirm="regionChange"
      :list="lists"
    ></u-select>
    <u-toast ref="uToast" />
  </view>
</template>

<script>
import { apiSetShopInfo, apiGetShopInfo } from "@/api/store";
import area from "@/utils/area";
import { uploadFile } from "@/utils/tools.js";
export default {
  data() {
    return {
      form: {
        name: "", //商城名称
        logo: "", //logo
        status: "", //小程序商城开关
        mall_contact: "", //联系人
        mall_contact_mobile: "", //手机号码
        return_contact: "", //退货联系人
        return_contact_mobile: "", //退货手机号码
        return_province: "", //退货地址
        return_city: "",
        return_district: "",
        region_address: "",
      },
      showRegion: false,
    };
  },
  created() {
    uni.$on("uAvatarCropper", (path) => {
      this.avatar = path;
      const fileInfo = uploadFile(path).then((res) => {
        this.form.logo = res.uri;
      });
    });
  },
  methods: {
    async getData() {
      this.form = await apiGetShopInfo();
      this.form.status = !!this.form.status;
      this.getregin(this.form);
    },
    async submit() {
      await apiSetShopInfo(this.form);
      uni.showToast({
        title: "保存成功！",
        duration: 1000,
      });
    },
    openAddressSel() {
      this.showRegion = true;
    },
    // 地区选择，选择当前省市区的ID
    regionChange(region) {
      this.form.return_province = region[0].value;
      this.form.return_city = region[1].value;
      this.form.return_district = region[2].value;
      this.form.region_address =
        region[0].label + " " + region[1].label + " " + region[2].label;
    },
    getregin(form) {
      let province;
      let city;
      let district;
      province = area.find((item) => item.value == form.return_province);
      city = province.children.find((item) => item.value == form.return_city);
      district = city.children.find(
        (item) => item.value == form.return_district
      );
      this.form.region_address = `${province.label} ${city.label} ${district.label}`;
    },
    //选择图片
    chooseImg() {
      uni.navigateTo({
        url: "components/uview-ui/components/u-avatar-cropper/u-avatar-cropper?destWidth=300&rectWidth=200&fileType=jpg",
      });
    },
  },
  onShow() {
    this.lists = area; //省市区数据
    this.getData();
  },
};
</script>

<style lang="scss">
.store-setting {
  .container {
    margin-bottom: 20rpx;

    &__title {
      padding: 26rpx 20rpx;
    }

    &__card {
      background-color: #fff;
      margin: 0 20rpx;
      padding: 0 30rpx;
      border-radius: 10rpx;

      .card-item {
        display: flex;
        padding: 30rpx 0;
        align-items: center;
        // justify-content: space-between;

        &:not(:last-of-type) {
          border-bottom: $-solid-border;
        }

        &__label {
          margin-right: 40rpx;

          &--width {
            width: 140rpx;
          }
        }

        &__content {
          display: flex;
          justify-content: end;
          width: 300rpx;
        }
      }
    }
  }

  .footer {
    height: 120rpx;

    .fixed-footer {
      position: fixed;
      bottom: 0;
      box-sizing: content-box;
      width: 100%;
      background-color: #fff;
      padding-bottom: env(safe-area-inset-bottom);

      .save-btn {
        height: 82rpx;
        line-height: 82rpx;
        text-align: center;
        border-radius: 41rpx;
        margin: 20rpx;
        background-color: $-color-primary;
        color: #fff;
        font-size: 30rpx;
      }
    }
  }
}
</style>
