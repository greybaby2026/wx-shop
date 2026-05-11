<template>
    <u-popup v-model="showPop" height="70vh" mode="bottom" border-radius="14" :closeable="true"
        :safe-area-inset-bottom="true">
        <view class="bg-white spec-contain">
            <view class="spec-header flex">
                <u-image class="m-r-20" width="180rpx" height="180rpx" border-radius="10rpx"
                    @tap="previewImage(checkedsku.image || image)" :src="checkedsku.image || image"></u-image>
                <view class="goods-info p-t-30">
                    <view class="primary flex">
                        <price :content="checkedsku.sell_price" main-size="46rpx" minor-size="38rpx" color="#FF2C3C">
                        </price>
                    </view>
                    <view class="sm m-t-20">
                        库存：{{ checkedsku.stock }}件
                    </view>
                </view>
            </view>
            <!-- 规格 -->
            <view class="spec-main">
                <scroll-view style="min-height: 600rpx;" scroll-y="true">
                    <view class="spec-list">
                        <view v-for="(item, index) in specList" :key="index" class="spec">
                            <view class="flex row-between">
                                <view class="name m-b-30">{{ item.name }}</view>
                            </view>
                            <view class="flex wrap">
                                <view v-for="(specitem, index2) in item.spec_list" :key="index2"
                                    :class="'spec-item sm ' + (specChecked[index].id == specitem.id ? 'checked' : '')"
                                    @tap="choseSpecItem(index, specitem.id)">
                                    {{ specitem.value }}
                                </view>
                            </view>
                        </view>
                    </view>
                </scroll-view>
            </view>
        </view>
    </u-popup>
</template>

<script>
export default {
    data() {
        return {
			specChecked: [],
			checkedsku: {}
        };
    },

    components: {},
    props: {
        value: {
            type: Boolean
        },
        specList: {
            type: Array,
			default: () => []
        },
		skuList: {
		    type: Array,
			default: () => []
		},
		image: {
			type: String
		}
    },
    computed: {
		showPop:{
			get() {
				return this.value
			},
			set(val) {
				this.$emit('input', val)
			}
		},
		skuMap() {
			const skuMap = new Map()
			this.skuList.forEach(item => skuMap.set(item.spec_value_ids, item))
			return skuMap
		},
		specCheckedIds() {
			return this.specChecked.map((item) => item.id).sort((a,b) => a-b).join()
		}
	},
    watch: {
		specList(val) {
			this.specChecked = val.map((item, index) => ({
				name: item.name,
				id: item.spec_list[0].id
			}))
		},
		specCheckedIds: {
			handler(val) {
				this.checkedsku = this.skuMap.get(val) || {}
			},
			deep: true,
		}
    },
    methods: {

        // 点击选中规格。
        choseSpecItem(index, specid) {
			this.specChecked[index].id = specid
        },

        previewImage(current) {
            uni.previewImage({
                current,
                // 当前显示图片的http链接
                urls: [current] // 需要预览的图片http链接列表
            });
        }
    }
};
</script>
<style lang="scss" scoped>
.spec-contain {
    border-radius: 10rpx 10rpx 0 0;
    overflow: hidden;
    position: relative;

    .close {
        position: absolute;
        right: 10rpx;
        top: 10rpx;
    }

    .spec-header {
        padding: 30rpx;
        padding-right: 70rpx;
        align-items: flex-start;
        border: $-solid-border;

        .vip-price {
            margin: 0 24rpx;
            background-color: #FFE9BA;
            line-height: 36rpx;
            border-radius: 6rpx;
            overflow: hidden;

            .price-name {
                background-color: #101010;
                padding: 3rpx 12rpx;
                color: #FFD4B7;
                position: relative;
                overflow: hidden;

                &::after {
                    content: '';
                    display: block;
                    width: 20rpx;
                    height: 20rpx;
                    position: absolute;
                    right: -15rpx;
                    background-color: #FFE9BA;
                    border-radius: 50%;
                    top: 50%;
                    transform: translateY(-50%);
                    box-sizing: border-box;
                }
            }
        }
    }

    .spec-main {
        .spec-list {
            padding: 30rpx 20rpx;

            .spec-item {
                line-height: 52rpx;
                padding: 0 30rpx;
                background-color: #F6F6F6;
                border-radius: 30rpx;
                margin: 0 20rpx 20rpx 0;
                border: 1rpx solid #F6F6F6;

                &.checked {
                    font-weight: 500;
                    border: 1rpx solid $-color-primary;
                    color: $-color-primary;
                    background-color: #EAEFFE;
                }

            }
        }
    }
}
</style>