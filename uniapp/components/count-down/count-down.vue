<template>
    <view class="count-down">
        <u-count-down
            :timestamp="timestamp"
            :color="color"
            separator="zh"
            :show-hours="false"
            :show-minutes="false"
            :show-seconds="false"
            :font-size="fontSize"
            :separator-size="separatorSize"
            :bg-color="bgColor || themeColor"
            :separator-color="separatorColor || themeColor"
        />
        <!-- :height="height" -->

        <u-count-down
            :timestamp="timestampHMS"
            :color="color"
            :separator="separator"
            :show-days="false"
            :font-size="fontSize"
            :separator-size="separatorSize"
            :bg-color="bgColor || themeColor"
            :separator-color="separatorColor || themeColor"
        />
    </view>
</template>

<script>
/**
 * @description 倒计时组件
 * @property {Number|String} timeEnd 结束时间戳 (必填项)
 * @property {String} separator 分隔符，colon为英文冒号，zh为中文 （默认：colon）
 * @property {Number|String} fontSize 倒计时字体大小，单位rpx （默认：24）
 * @property {Number|String} separatorSize 分隔符的字体大小，单位rpx （默认：22）
 * @property {String} bgColor 倒计时数字的背景颜色 （默认：主题色）
 * @property {String} separatorColor 分隔符的颜色 （默认：主题色）
 * @property {String} color 倒计时数字的颜色 （默认：#FFFFFF）
 * @property {String | Number} height 数字高度值(宽度等同此值) （默认：40）
 * @example <count-down :time-end="endTime" />
 */

export default {
    name: 'CountDown',

    props: {
        // 结束时间戳(秒)
        timeEnd: {
            type: Number | String,
            required: true
        },

        // 	分隔符，colon为英文冒号，zh为中文
        separator: {
            type: String,
            default: 'colon'
        },

        // 倒计时字体大小，单位rpx
        fontSize: {
            type: Number | String,
            default: 24
        },

        // 分隔符的字体大小，单位rpx
        separatorSize: {
            type: Number | String,
            default: 22
        },

        // 倒计时数字的背景颜色
        bgColor: {
            type: String
        },

        // 分隔符的颜色
        separatorColor: {
            type: String
        },

        // 倒计时数字的颜色
        color: {
            type: String,
            default: '#FFFFFF'
        },

        // 数字高度值(宽度等同此值)，设置边框时看情况是否需要设置此值
        height: {
            type: String | Number,
            default: 40
        }
    },

    computed: {
        // 剩余--时间戳
        timestamp() {
            const nowTimestamp = new Date().getTime().toString().substr(0, 10)
            return this.timeEnd - nowTimestamp
        },

        // 时分秒--时间戳
        timestampHMS() {
            const DAY = 60 * 60 * 24
            return this.timestamp % DAY
        }
    }
}
</script>

<style lang="scss" scoped>
.count-down {
    display: inline-flex;
    align-items: center;
}
</style>
