<template>
    <el-tabs
        v-model="tabsActiveIndex"
        :stretch="true"
        class="window__aside-left"
    >
        <!-- 搜索 -->
        <div class="chat-search">
            <el-input
                v-model="searchValue"
                size="small"
                placeholder="请输入用户昵称搜索"
                :clearable="true"
                @keyup.enter.native="handleSearch"
            />
        </div>

        <!-- 用户列表 -->
        <el-tab-pane
            class="chat-user__list"
            label="用户列表"
            :name="TabsMap['USER_LIST']"
        >
            <el-scrollbar class="ls-scrollbar scrollbar-wrapper">
                <loading-more v-model="pagination.status" @load="getUserList">
                    <chat-user-item
                        v-for="(item, index) in SessionList['user']"
                        :key="item.id"
                        :is-read="item.is_read"
                        :name="item.nickname"
                        :avatar="item.avatar"
                        :message="item.msg"
                        :active="current === item.user_id"
                        :time="item.update_time"
                        :online="item.online"
                        :msg-type="item.msg_type"
                        @click.native="changeCurrent(item, index)"
                    />
                </loading-more>
            </el-scrollbar>
        </el-tab-pane>

        <!-- 群聊列表 -->
        <el-tab-pane
            label=""
            :name="TabsMap['GROUP_CHAT']"
            disabled
        ></el-tab-pane>
    </el-tabs>
</template>


<script>
import ChatUserItem from '@/components/ChatUserItem'
import { apiChatUserList } from '@/api/app'
import LoadingMore from '@/components/LoadingMore'
import { E_Load } from '@/utils/enum'
import { mapGetters } from 'vuex'
export default {
    name: 'TheWindowAsideLeft',

    components: {
        ChatUserItem,
        LoadingMore,
    },

    props: {
        current: {
            type: [String, Number],
            require: true,
        },
    },
    inject: ['send'],
    data() {
        return {
            /* 搜素内容 */
            searchValue: '',
            /* Tabs 索引 */
            tabsActiveIndex: '',
            /* Tabs 映射 */
            TabsMap: Object.freeze({
                USER_LIST: '1',
                GROUP_CHAT: '2',
            }),
            /* 分页器 */
            pagination: {
                size: 15,
                page: 1,
                status: E_Load['NORMAL'],
            },

            /* 会话列表 */
            SessionList: {
                user: [],
                group: [],
            },

            /* 当前会话 */
            // sessionCurrent: '',
        }
    },

    created() {
        /* 初始化Tabs选项 */
        this.tabsActiveIndex = this.TabsMap['USER_LIST']
        this.$on('useronline', this.useronlineEvent)
        this.$on('transfer', this.transferEvenr)
        this.$on('message', this.messageEvenr)

        // this.getUserList()
    },
    methods: {
        /**
         * 获取用户列表
         */
        getUserList() {
            return new Promise((resolve, reject) => {
                const { size, page, status } = this.pagination

                if (status == E_Load['EMPTY']) return
                apiChatUserList({
                    page_no: page,
                    page_size: size,
                    nickname: this.searchValue.trim(),
                })
                    .then((data) => {
                        if(!data) return
                        this.pagination.page++
                        // 防止列表出现重复的用户
                        data.lists.forEach((item) => {
                            let index = this.SessionList.user.findIndex(uitem => uitem.user_id ==  item.user_id)
                            if(index == -1) {
                                this.SessionList.user.push(item)
                            }
                        })
                        this.pagination.status = E_Load['NORMAL']
                        this.$nextTick(() => {
                            if (!data.lists.length < size) {
                                this.pagination.status = E_Load['EMPTY']
                            }
                        })
                        resolve(data)
                    })
                    .catch((err) => {

                        this.pagination.status = E_Load['NORMAL']
                        reject(err)
                    })
                    .finally(() => {})
            })
        },
        /**
         * 搜索用户列表
         */
        handleSearch() {
            this.pagination.page = 1
            this.pagination.status = E_Load['LOAD']
            this.SessionList.user = []
            this.getUserList()
        },
        /**
         * 转接事件
         */
        transferEvenr(data) {
            if (data.status == 'get_success') {
                this.setUser(data.user)
                this.$notify.success({
                    title: '转接通知',
                    message: `您有新的用户`,
                })
                return
            }

            this.SessionList.user = this.SessionList.user.filter((item) => {
                return item.user_id != this.current
            })
            this.$emit('change', '')
            this.$message.success({
                message: `转接成功`,
            })
        },
        /**
         * 用户上下线事件
         */
        useronlineEvent(data) {
            if (data.online) {
                this.$notify.success({
                    title: '上线通知',
                    message: `用户（${data.nickname}）上线`,
                })
            }
            this.setUser(data)
        },
        /**
         * 消息事件
         */
        messageEvenr(data) {
            let index = this.SessionList.user.findIndex((item) => {
                if(data.from_type == 'user') {
                    return data.from_id == item.user_id
                }else if(data.from_type == 'kefu') {
                    return data.to_id == item.user_id
                }
            })
            if (index != -1) {
                // 更新用户列表聊天
                this.$set(this.SessionList.user[index], 'msg_type', data.msg_type)
                this.$set(this.SessionList.user[index], 'msg', data.msg)
                this.$set(
                    this.SessionList.user[index],
                    'update_time',
                    data.update_time
                )
            }

            if (data.from_type == 'user') {
                if (data.from_id == this.current) {
                    this.send('read', {
                        user_id: this.current
                    })
                    return
                }
                this.$set(this.SessionList.user[index], 'is_read', 0)
            }
        },
        /**
         * 选中用户改变
         */
        changeCurrent(item, index) {
            this.$emit('change', item.user_id)
            if (!item.is_read) {
                this.send('read', {
                    user_id: item.user_id
                })

                this.$set(this.SessionList.user[index], 'is_read', 1)
            }
        },
        // 改变用户上下线的状态
        setUser(data) {
            let index = this.SessionList.user.findIndex(
                (item) => data.user_id == item.user_id
            )
            if (index != -1) {
                this.$set(this.SessionList.user[index], 'online', data.online)
            } else {
                this.SessionList.user.unshift(data)
            }
        },
    },
}
</script>


<style lang="scss" scoped>
.window__aside-left {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;

    .chat-search {
        padding: 0 10px 10px 10px;
    }

    &::v-deep .el-tabs__content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .chat-user__list {
        flex: 1;
        display: flex;
    }

    .scrollbar-wrapper {
        flex: 1;
        height: 630px;
    }
}
</style>
