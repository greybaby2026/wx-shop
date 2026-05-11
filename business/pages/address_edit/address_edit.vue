<template>
	<view>
		<view class="address-edit">

			<!-- 联系方式 -->
			<template>
				<view class="form-item bb flex">
					<view class="label">收货人</view>
					<input class="m-l-10" v-model="refund_address.contact" name="nickname" type="text"
						placeholder="请填写收货人姓名" />
				</view>
				<view class="form-item bb flex">
					<view class="label">联系方式</view>
					<input class="m-l-10" name="mobile" v-model="refund_address.mobile" type="number"
						placeholder="请填写手机号码"></input>
				</view>
			</template>

			<!-- 地区选择 -->
			<view @click="showRegion = true">
				<view class="form-item bb flex">
					<view class="label">省/市/区</view>
					<view class="flex flex-1 row-between m-l-10" style="width: 100%;">
						<input name="region" class="m-r-10" v-model="region" disabled type="text" value="请选择"></input>
						<u-icon color="#707070" name="arrow-down"></u-icon>
					</view>
				</view>
			</view>

			<!-- 详细地址填写 -->
			<view class="form-item flex col-top bb">
				<view class="label">详细地址</view>
				<input name="address" v-model="refund_address.address" placeholder="请填写小区、街道、门牌号等信息" />
			</view>
		</view>

		<button class="my-btn md white flex br60 row-center" @click="onSubmit">完成</button>

		<!-- 地址选择 -->
		<u-select v-model="showRegion" confirm-color="#101010" cancel-color="#999"
			mode="mutil-column-auto" @confirm="regionChange" :list="lists"></u-select>

		<u-toast ref="uToast" />
	</view>
</template>

<script>
	import {
		apiSetShopInfo
	} from '@/api/store'
	import {
		apiOrderGetAddress,
		apiOrderEditAddress
	} from '@/api/order'

	import area from '@/utils/area'
	export default {
		data() {
			return {
				id: 0,

				refund_address: {
					contact: '', //联系方式
					mobile: '', //手机号码
					province: '', //省
					province_name: '',
					city: '', //市
					city_name: '',
					district: '', //区
					district_name: '',
					address: '', //详细地址
				},
				region: '请选择', //省市区显示
				showRegion: false, //显示地区选择的flag
				lists: [] //省市区的数据
			};
		},

		onLoad: function(options) {
			this.lists = area; //省市区数据

			try {
				const id = this.$Route.query.id;
				if (id) {
					this.id = id;
					this.getAddressFunc(id)
				}
			} catch (e) {
				console.log(e)
			}
		},
		methods: {
			// 提交地址信息
			async onSubmit() {
				if (!this.refund_address.contact) return this.$toast({
					title: '请填写收货人姓名'
				});
				if (!this.refund_address.mobile) return this.$toast({
					title: '请填写手机号码'
				});
				if (!this.region) return this.$toast({
					title: '请选择地区'
				});
				if (!this.refund_address.address) return this.$toast({
					title: '请填写小区、街道、门牌号等信息'
				});

				await apiOrderEditAddress({
					id: this.id,
					...this.refund_address
				})
				setTimeout(() => {
					uni.$emit('editAddress')
					this.$Router.back()
				}, 1000)
			},

			async getAddressFunc(id) {
				const res = await apiOrderGetAddress({
					id
				})

				this.refund_address = res
				this.region = res.province_name + " " + res.city_name + " " + res.district_name
			},

			// 地区选择，选择当前省市区的ID
			regionChange(region) {
				this.refund_address.province = region[0].value;
				this.refund_address.city = region[1].value;
				this.refund_address.district = region[2].value;
				this.region = region[0].label + " " + region[1].label + " " + region[2].label
			}
		}
	};
</script>
<style lang="scss">
	.address-edit {
		padding-top: 20rpx;

		.bb {
			border-bottom: 1px solid #F8F8F8;
		}

		.form-item {
			padding: 30rpx;
			background-color: $-color-white;

			.label {
				width: 150rpx;
				color: $-color-black;
				font-size: $-font-size-nr;
				font-weight: 500;
			}

			input {
				text-align: right;
				height: 100%;
				flex: 1;
			}
		}
	}

	.my-btn {
		height: 88rpx;
		margin: 30rpx 26rpx;
		margin-top: 40rpx;
		text-align: center;
		background-color: $-color-primary;
	}
</style>
