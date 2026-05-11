<template>
    <view>
        <view class="add-bank bg-white">
            <view class="black md flex row-between">
                <view>提现银行：</view>
                <input type="text" v-model="form.name" placeholder="请填写银行名称，如中国银行" />
            </view>
            <view class="black md flex row-between">
                <view>银行支行：</view>
                <input type="text" v-model="form.branch" placeholder="请填写银行支行，如北京路支行" />
            </view>
            <view class="black md flex row-between">
                <view>开户姓名：</view>
                <input type="text" v-model="form.nickname" placeholder="请填写开户人姓名，如张三" />
            </view>
            <view class="black md flex row-between">
                <view>银行账号：</view>
                <input type="text" v-model="form.account" placeholder="请填写银行卡账号" />
            </view>
        </view>

        <template v-if="type == 1">
            <view class="btn flex row-center br60 md white" @click="addBankFunc">
                确认添加
            </view>
        </template>
        
        <template v-if="type == 2">
            <view class="btn flex row-center br60 md white" @click="editBankFunc">
                确认编辑
            </view>
        </template>

        <u-toast ref="uToast" />
    </view>
</template>

<script>
    import {
        apiAddBank,
        apiEditBank,
        apigetBankDetail
    } from "@/api/user.js"
    export default {
        data() {
            return {
                type: 1, //type1 = 添加 2=编辑
                form: {
                    name: '',
                    branch: '',
                    account: '',
                    nickname: ''
                }
            }
        },

        onLoad() {
            try {
                const id = this.$Route.query.id;
                if (id) {
                    this.getBankFunc(id)
                    this.type = 2
                }
            } catch (e) {
                console.log(e)
            }
        },

        methods: {
            async addBankFunc() {
                const res = await apiAddBank({
                    ...this.form
                })
                this.$refs.uToast.show({
                    title: '添加成功',
                    type: 'success'
                })
                setTimeout(() => {
                    uni.navigateBack()
                }, 1000)
            },
            
            async editBankFunc() {
                const res = await apiEditBank({
                    ...this.form
                })
                this.$refs.uToast.show({
                    title: '修改成功',
                    type: 'success'
                })
                setTimeout(() => {
                    uni.navigateBack()
                }, 1000)
            },
            
            async getBankFunc(id) {
                const res = await apigetBankDetail({id: id})
                
                for (const key in res) {
                    console.log(res[key])
                    this.$set(this.form, key, res[key])
                }
            }
        }
    }
</script>

<style lang="scss">
    .add-bank {
        padding: 0 30rpx;

        input {
            flex: 1;
            margin: 30rpx 0;
            text-align: right;
        }
    }

    .btn {
        width: 690rpx;
        height: 88rpx;
        margin: 0 30rpx;
        margin-top: 40rpx;
        box-sizing: border-box;
        background-color: $-color-primary;
    }
</style>
