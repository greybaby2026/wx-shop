<template>
    <view>
        <view class="select-bank">
            <block v-for="(item, index) in bank_list" :key="item.id">
                <view class="m-t-20">
                    <u-swipe-action :vibrate-short="true" :index="index" bg-color="transparent" :btn-width="130"
                        @click="clickBtn($event, item.id)" :options="options">
                        <view class="bank-item flex row-between bg-white" @click="selectBankFunc(item)">
                            <view class="normal nr">{{item.name}} ({{item.account.substring(item.account.length-4)}})</view>
                            <view class="nr setting" @click.stop="toAddBankFunc(item)">编辑</view>
                        </view>
                    </u-swipe-action>
                </view>
            </block>
        </view>
        
        <router-link to="/bundle/pages/bank_add/bank_add">
            <view class="btn flex row-center br60 md white">
                新增提现账户
            </view>
        </router-link>

        <u-toast ref="uToast" />
    </view>
</template>

<script>
    import {
        apiGetWithdrawInfo,
        apiDelBank
    } from "@/api/user.js"
    export default {
        data() {
            return {
                bank_list: [],
                selectIndex: -1,

                options: [{
                    text: '删除',
                    style: {
                        backgroundColor: '#FF2C3C'
                    }
                }],
            }
        },

        onShow() {
            this.getWithdrawInfoFunc();
        },

        methods: {
            async getWithdrawInfoFunc() {
                const {
                    bank_list
                } = await apiGetWithdrawInfo();
                console.log(bank_list)
                this.bank_list = bank_list;
            },

            async clickBtn(event, id) {
                const res = await apiDelBank({id})
                this.$refs.uToast.show({
                    title: '删除成功',
                    type: 'success'
                })
                this.getWithdrawInfoFunc();
            },
            
            toAddBankFunc(data) {
                this.$Router.push({
                    path: '/bundle/pages/bank_add/bank_add',
                    query: {
                        id: data.id
                    }
                })
            },
            
            selectBankFunc(item) {
                uni.$emit('getBank', item)
                this.$Router.back()
            }
        }
    }
</script>

<style lang="scss">
    .select-bank {
        width: 100%;

        .bank-item {
            width: 100vw;
            padding: 30rpx;

            .setting {
                color: $-color-primary;
            }
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
