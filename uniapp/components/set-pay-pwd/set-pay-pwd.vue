<template>
	<view>
        <!-- 判断手机号是否已经绑定 -->
		<u-modal :value="showTips" ref="uModal" :showCancelButton="true" :confirm-color="themeColor" async-close
			confirm-text="前往绑定" @confirm="toSetting" @cancel="goBack">
			<view class="slot-content row-center" style="padding: 40rpx;">
				您暂未绑定手机号，请先绑定
			</view>
		</u-modal>
        
        <!-- 设置或修改交易密码 -->
		<u-modal ref="uModalSet" :value="!showTips && showSetPwd" title="设置交易密码" :showCancelButton="true"
			:confirm-color="themeColor" async-close confirm-text="确定" @confirm="onSetPwd" @cancel="goBack">
			<view class="slot-content" style="padding: 40rpx;">
				<view style="font-size: 42rpx;">{{mobile}}</view>
				<view class="m-t-20">
					<u-input type="password" v-model="setPwd" :border="true" placeholder="请设置您的交易密码"
						style="width: 100%" />
				</view>
				<view class="m-t-20">
					<u-input type="password" v-model="comfirmPwd" :border="true" placeholder="再次确认交易密码"
						style="width: 100%" />
				</view>
			</view>
		</u-modal>
        
        <!-- 输入交易密码 -->
		<u-modal ref="uModalInput" :value="showConfirm" :showCancelButton="true" :confirm-color="themeColor" async-close
			confirm-text="确定" @confirm="onConfirm" @cancel="toSetting" cancel-text="忘记密码" title="请输入密码">
			<view class="slot-content flex row-center" style="padding: 40rpx;">
				<u-icon class="icon-close" name="close" @click="showInputPwd"></u-icon>
				<u-input type="password" v-model="pwd" :border="true" placeholder="请输入您的交易密码" style="width: 100%" />
			</view>
		</u-modal>
        
	</view>
</template>

<script>
	import {
		hasPayPassword,
		setPassword
	} from "@/api/user";
	import {
		mapGetters
	} from 'vuex'
	export default {
		name: "set-pay-pwd",
		data() {
			return {
				showSetPwd: false,
				showConfirm: false,
				pwd: '',
				setPwd: '',
				comfirmPwd: ''
			};
		},
		methods: {
            // 设置交易密码
			onSetPwd() {
				let {
					setPwd,
					comfirmPwd
				} = this
				this.$refs.uModalSet.clearLoading()
				if (!setPwd) {
					this.$toast({
						title: '请填写交易密码'
					});
					return;
				}
				if (!comfirmPwd) {
					this.$toast({
						title: '请填写确认密码'
					});
					return;
				}
				if (setPwd != comfirmPwd) {
					this.$toast({
						title: '两次密码输入不一致'
					});
					return;
				}
				if (setPwd.length < 4 || setPwd.length > 8) {
					this.$toast({
						title: '请输入长度为4-6位的密码'
					});
					return;
				}
                // 设置密码
				setPassword({pay_password: setPwd}).then(res => {
					this.showSetPwd = false;
                    this.$toast({
                    	title: '设置成功！'
                    });
				})
			},
            // 返回上一级页面
			goBack() {
				uni.navigateBack()
			},
            // 去设置手机号码
			toSetting() {
				uni.navigateTo({
					url: '/bundle/pages/user_profile/user_profile'
				})
				this.$refs.uModal.clearLoading()
			},
            // 判断有没有支付密码
			hasPayWord() {				
				hasPayPassword().then(res => {
					if(res.has_pay_password == false) {
                        console.log(res )
                        this.showSetPwd = true
                    }
				})
			},
            // 确认拉起交易密码
			onConfirm() {
				this.$refs.uModalInput.clearLoading()
				this.$emit('confirm', this.pwd)
			},
            // 显示或隐藏交易密码
			showInputPwd() {
				setTimeout(() => {
					this.pwd = ""
				}, 1000)
				this.showConfirm = !this.showConfirm
			}
		},
		computed: {
			...mapGetters(['userInfo']),
			mobile() {
				if (this.userInfo.mobile) {
					return this.userInfo.mobile.replace(/(\d{3})\d{4}(\d{4})/, '$1****$2')
				}
			},
			showTips() {
				console.log(this.userInfo)
				return Boolean(!this.userInfo.mobile)
			}
		}
	}
</script>

<style lang="scss" scoped>
	.icon-close {
		position: absolute;
		top: 30rpx;
		right: 30rpx;
	}
</style>
