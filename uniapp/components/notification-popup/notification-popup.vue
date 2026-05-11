<template>
    <view v-if="showPopup && currentNotice" class="notification-popup">
        <view class="notification-bar" :class="{'notification-slide-in': animating}" @tap="handleAction">
            <view class="notification-left">
                <view class="notification-icon">
                    <u-icon :name="sceneIcon" :color="themeColor" size="28"></u-icon>
                    <view v-if="unreadCount > 1" class="notification-badge">{{ unreadCount }}</view>
                </view>
                <view class="notification-text">
                    <text class="notification-title">{{ currentNotice.scene_desc || '系统通知' }}</text>
                    <text class="notification-content">{{ currentNotice.content }}</text>
                </view>
            </view>
            <view class="notification-right">
                <view class="notification-go" @tap.stop="handleAction">
                    <text>查看</text>
                    <u-icon name="arrow-right" size="20" color="#fff"></u-icon>
                </view>
                <view class="notification-close" @tap.stop="handleClose">
                    <u-icon name="close" size="18" color="#ccc"></u-icon>
                </view>
            </view>
        </view>
    </view>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
    name: 'NotificationPopup',
    data() {
        return {
            animating: false,
            autoCloseTimer: null,
        }
    },
    computed: {
        ...mapGetters(['showNotificationPopup', 'currentNotice', 'themeColor', 'unreadCount']),
        showPopup() {
            return this.showNotificationPopup && this.currentNotice
        },
        sceneIcon() {
            if (!this.currentNotice) return 'bell'
            const icons = {
                106: 'order',
                107: 'car',
                108: 'close-circle',
                109: 'checkmark-circle',
                110: 'red-packet',
                111: 'wallet',
                112: 'star',
                113: 'wallet',
            }
            return icons[this.currentNotice.scene_id] || 'bell'
        },
    },
    watch: {
        showPopup(val) {
            if (val) {
                this.$nextTick(() => {
                    this.animating = true
                })
                this.startAutoClose()
            } else {
                this.animating = false
                this.clearAutoClose()
            }
        },
    },
    methods: {
        handleClose() {
            this.$store.dispatch('hideNotificationPopup')
            if (this.currentNotice) {
                this.$store.dispatch('markAsRead', this.currentNotice.id)
            }
        },
        handleAction() {
            if (!this.currentNotice) return
            const navigate = this.currentNotice.navigate
            this.$store.dispatch('markAsRead', this.currentNotice.id)
            if (navigate) {
                const [path, queryStr] = navigate.split('?')
                const query = {}
                if (queryStr) {
                    queryStr.split('&').forEach(pair => {
                        const [key, val] = pair.split('=')
                        if (key) query[key] = decodeURIComponent(val)
                    })
                }
                this.$Router.push({ path, query })
            } else {
                this.$Router.push({ path: '/bundle/pages/message_center/message_center' })
            }
        },
        startAutoClose() {
            this.clearAutoClose()
            this.autoCloseTimer = setTimeout(() => {
                this.handleClose()
            }, 8000)
        },
        clearAutoClose() {
            if (this.autoCloseTimer) {
                clearTimeout(this.autoCloseTimer)
                this.autoCloseTimer = null
            }
        },
        formatTime(timestamp) {
            if (!timestamp) return ''
            let date
            if (typeof timestamp === 'string' && timestamp.includes('-')) {
                date = new Date(timestamp.replace(/-/g, '/'))
            } else {
                const ts = typeof timestamp === 'string' ? parseInt(timestamp) : timestamp
                date = new Date(ts * 1000)
            }
            if (isNaN(date.getTime())) return ''
            const now = new Date()
            const diff = now - date
            if (diff < 0) return '刚刚'
            if (diff < 60000) return '刚刚'
            if (diff < 3600000) return Math.floor(diff / 60000) + '分钟前'
            if (diff < 86400000) return Math.floor(diff / 3600000) + '小时前'
            const m = date.getMonth() + 1
            const d = date.getDate()
            return m + '月' + d + '日'
        },
    },
    beforeDestroy() {
        this.clearAutoClose()
    },
}
</script>

<style lang="scss" scoped>
.notification-popup {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10080;
    pointer-events: none;
    padding: 0 20rpx 20rpx;
    padding-bottom: calc(env(safe-area-inset-bottom) + 120rpx);
}

.notification-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fff;
    border-radius: 16rpx;
    box-shadow: 0 4rpx 24rpx rgba(0, 0, 0, 0.12);
    padding: 16rpx 16rpx 16rpx 20rpx;
    pointer-events: auto;
    transform: translateY(120%);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
}

.notification-slide-in {
    transform: translateY(0);
    opacity: 1;
}

.notification-left {
    display: flex;
    align-items: center;
    flex: 1;
    min-width: 0;
}

.notification-icon {
    width: 48rpx;
    height: 48rpx;
    border-radius: 50%;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 14rpx;
    flex-shrink: 0;
    position: relative;
}

.notification-badge {
    position: absolute;
    top: -10rpx;
    right: -16rpx;
    min-width: 28rpx;
    height: 28rpx;
    line-height: 28rpx;
    text-align: center;
    font-size: 18rpx;
    color: #fff;
    background: #ff2c3c;
    border-radius: 14rpx;
    padding: 0 8rpx;
    font-weight: bold;
}

.notification-text {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
}

.notification-title {
    font-size: 24rpx;
    font-weight: bold;
    color: #333;
    line-height: 1.3;
}

.notification-content {
    font-size: 22rpx;
    color: #999;
    line-height: 1.4;
    margin-top: 2rpx;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.notification-right {
    display: flex;
    align-items: center;
    flex-shrink: 0;
    margin-left: 16rpx;
}

.notification-go {
    display: flex;
    align-items: center;
    padding: 8rpx 20rpx;
    border-radius: 20rpx;
    color: #fff;
    font-size: 22rpx;
    margin-right: 8rpx;
    @include background_color();
}

.notification-close {
    padding: 8rpx;
}
</style>
