<template>
    <view class="img-list flex flex-wrap col-top" v-if="lists.length">
        <view
            class="item text-center"
            :style="{ width: `${100 / lineNum}%` }"
            v-for="(item, index) in lists"
            :key="index"
        >
            <router-link
                :to="{
                    path: '/bundle/pages/category/category',
                    query: {
                        id: item.id
                    }
                }"
            >
                <view class="flex-col col-center m-b-20">
                    <view class="bg-white">
                        <u-image mode="aspectFit" width="150rpx" height="150rpx" :src="item.image">
                        </u-image>
                    </view>
                    <view class="text m-t-10 xs p-l-10 p-r-10">{{ item.name }}</view>
                </view>
            </router-link>
        </view>
    </view>
</template>

<script type="text/javascript">
export default {
    props: {
        lists: {
            type: Array,
            default: () => []
        },
        lineNum: {
            type: Number,
            default: 4
        }
    },
    data() {
        return {}
    },
    methods: {},
    watch: {
        lists: {
            immediate: true,
            handler(val) {
                console.log(val)
            }
        }
    },
    created() {}
}
</script>
<style lang="scss" scoped>
.img-list {
    padding: 20rpx 10rpx;
}
</style>
