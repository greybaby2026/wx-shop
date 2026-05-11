<template>
    <view class="lists flex flex-wrap" v-if="lists.length">
        <view class="item" v-for="(item, index) in lists" :key="index">
            <router-link
                :to="{
                    path: '/bundle/pages/category/category',
                    query: {
                        id: item.id
                    }
                }"
            >
                <view class="wrap line-1">
                    <text class="name xs">{{ item.name }}</text>
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
        }
    },
    data() {
        return {}
    },
    methods: {},
    computed: {},
    created() {}
}
</script>
<style lang="scss" scoped>
.lists {
    .item {
        text-align: center;
        width: 33.333%;
        margin-bottom: 20rpx;

        &:not(:nth-of-type(3n)) {
            padding-right: 20rpx;
        }

        .wrap {
            border-radius: 6px;
            background: #fff;
            height: 74rpx;
            line-height: 74rpx;
            padding: 0 10rpx;
        }
    }
}
</style>
