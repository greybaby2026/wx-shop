<template>
    <view
        v-if="isOpen"
        class="bubble-tips-container"
        :style="{ top: top, left: left }"
    >
        <view
            class="bubble-content flex"
            v-show="showBubble"
            v-for="(item, index) in currentList"
            :key="index"
        >
            <image class="bubble-img" :src="item.avatar" />
            <view class="xs">
                {{ item.content }}
            </view>
        </view>
    </view>
</template>

<script>
import { apiFootprint } from "@/api/store";
import Cache from "@/utils/cache";
export default {
    props: {
        //1-首页， 2-商品详情
        page: {
            type: Number,
        },
        // 是否停止切换
        discharge: {
            type: Boolean,
            default: false,
        },
        top: {
            type: String,
            default: "40rpx",
        },
        left: {
            type: String,
            default: "20rpx",
        },
        updateTime: {
            type: Number,
            default: 2 * 60 * 1000,
        },
    },
    data() {
        return {
            index: Cache.get(this.indexKey) || 0,
            list: [],
            currentList: [],
            status: 0,
            pages: [],
            timer: null,
            showBubble: false,
        };
    },
    watch: {
        index: {
            handler(newValue, oldValue) {
                if (this.list.length == 0) return;
                if (this.index - this.list.length > 0) {
                    this.showBubble = false;
                    let timer = setTimeout(() => {
                        this.index = 0;
                        Cache.set(this.indexKey, 0);
                        this.fadeUpBubble();
                        clearTimeout(timer);
                    }, 500);
                } else {
                    this.fadeUpBubble();
                    return;
                }
            },
            immediate: true,
        },
        discharge() {
            // 为了让活性的页面停止计时器
            if (this.discharge) {
                // 停止
                Cache.set(this.indexKey, this.index);
                clearInterval(this.timer);
                this.timer = null;
                return false;
            } else {
                this.fadeUpBubble();
            }
        },
    },
    computed: {
        isOpen() {
            return this.status && this.pages.includes(this.page);
        },
    },
    methods: {
        async $getBubbleLists() {
            const res = await apiFootprint();
            this.list = res.lists;
            this.status = res.status;
            this.pages = res.pages;
            var requestTime = new Date().getTime();
            Cache.set(this.requestKey, requestTime);
            if (!this.isOpen) {
                return;
            }
            this.fadeUpBubble();
        },
        async fadeUpBubble() {
            let requestTime = Cache.get(this.requestKey);
            let currentTime = new Date();
            this.showBubble = true;
            if (currentTime.getTime() - requestTime >= this.updateTime) {
                await this.$getBubbleLists();
                Cache.set(this.indexKey, 0);
            }
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }

            this.currentList = this.list.slice(this.index, this.index + 1);
            this.timer = setInterval(() => {
                Cache.set(this.indexKey, ++this.index);
            }, 3000);
        },
    },
    async created() {
        this.indexKey = "currentIndex" + this.page;
        this.requestKey = "requestTime" + this.page;
        var index = Cache.get(this.indexKey) || 0;
        var bubble = await apiFootprint();
        this.list = bubble?.lists || [];
        this.status = bubble?.status || 0;
        this.pages = bubble?.pages || [];
        if (this.list.length <= 0) {
            Cache.set(this.indexKey, 0);
            this.fadeUpBubble();
        } else if (index - this.list.length >= 0) {
            Cache.set(this.indexKey, 0);
            this.fadeUpBubble();
        } else {
            this.fadeUpBubble();
        }
    },
    onLoad() {},
    destroyed() {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    },
};
</script>

<style lang="scss">
.bubble-tips-container {
    position: absolute;
    z-index: 900;

    .bubble-content {
        padding: 4rpx 20rpx 4rpx 10rpx;
        background-color: rgba(0, 0, 0, 0.6);
        color: white;
        border-radius: 120rpx;

        .bubble-img {
            width: 50rpx;
            height: 50rpx;
            border-radius: 50%;
            margin-right: 10rpx;
        }
    }
}
</style>
