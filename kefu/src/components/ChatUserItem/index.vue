<template>
    <div :class="['chat-user-item', { 'chat-user-item--active': active }]">
        <!-- Avatar -->
        <div class="chat__avatar" :class="{ 'chat__avatar--online': online }">
            <el-badge is-dot :hidden="Boolean(isRead)">
                <el-avatar :size="38" :src="avatar"></el-avatar>
            </el-badge>
        </div>

        <div class="chat__msg">
            <!-- User -->
            <div class="chat__msg-info">
                <div class="user-name nr normal line-1">{{ name }}</div>
                <div class="msg-time xs muted m-l-5">{{ getTime }}</div>
            </div>

            <!-- Message -->
            <div class="chat__msg-content xs">
                <div class="msg-content muted line-1 flex-1" v-html="getMessage"></div>

                <!-- <el-badge
                  class="msg-badge"
                  v-show="!badge"
                  :value="''"
                />-->
            </div>
        </div>
    </div>
</template>


<script>
/**
 * 聊天用户组件
 * 聊天窗口列表固有组件，其宽度可随父容器适应
 * @param {String} avatar 头像
 * @param {String} name 用户昵称
 * @param {String} message 消息内容
 * @param {String} time 消息时间
 * @param {Number} badge 消息数量
 * @param {Boolean} active 活跃
 */
import { timeFormatChat } from '@/utils/date'
export default {
    name: 'ChatUserItem',

    props: {
        avatar: {
            type: String,
            default: ''
        },

        name: {
            type: String,
            default: ''
        },

        message: {
            type: String,
            default: ''
        },

        time: {
            type: [String, Number],
            default: ''
        },

        badge: {
            type: Number,
            default: 0
        },

        active: {
            type: Boolean,
            default: true
        },
        online: {
            type: Number
        },
        isRead: {
            type: [Boolean, Number],
        },
        msgType: {
            type: Number
        },
    },
    computed: {
        getTime() {
            return this.time ? timeFormatChat(new Date(this.time).getTime()) : ''
        },
        getMessage() {
            switch (this.msgType) {
                case 1: return this.message.replace(/\[em-([a-z_]+)\]/g, `<span class="em em-$1"></span>`)
                case 2: return '图片'
                case 3: return '商品'
            }
        },
        imageURL() {
            return this.$store.getters.baseUrl
        }
    }

}
</script>


<style lang="scss" scoped>
.chat-user-item {
    display: flex;
    align-items: center;
    padding: 0 10px;
    height: 70px;
    cursor: pointer;

    &:hover {
        // background-color: #F5F5F5;
        // box-shadow: inset 0 1px 1px 0 rgba(0, 0, 0, 0.2);
    }

    &--active {
        background-color: #f5f5f5;
    }

    .chat__avatar {
        display: flex;
        align-items: center;
        height: 38px;
        margin-right: 10px;
        opacity: 0.5;
        &--online {
            opacity: 1;
        }
        ::v-deep .el-badge__content.is-dot {
            height: 10px;
            width: 10px;
            box-sizing: content-box;
        }
    }

    .chat__msg {
        flex: 1;
        display: flex;
        flex-direction: column;
        height: 38px;
        overflow: hidden;

        &-info {
            display: flex;
            align-items: center;
            justify-content: space-between;

            .user-name {
                flex: 1;
                white-space: nowrap;
                text-overflow: ellipsis;
            }

            .msg-time {
            }
        }

        &-content {
            flex: 1;
            display: flex;
            align-items: center;

            .msg-badge {
                margin-left: auto;
                line-height: 0;
            }
        }
    }
}
</style>