<template>
  <div class="window-contain">
    <div class="window">
      <!-- Header -->
      <header class="window-header">
        <the-window-header
          :isStatus="isStatus"
          :open="open"
          @handleNotice="handlenotice"
        ></the-window-header>
      </header>

      <section class="window-content">
        <!-- Aside Left -->
        <aside class="window-aside--left">
          <the-window-aside-left
            :current="sessionID"
            @change="changeSession"
            ref="user"
          ></the-window-aside-left>
        </aside>

        <!-- Main -->
        <main class="window-main">
          <the-window-main
            :to-id="sessionID"
            :from-id="userInfo.id"
            ref="chat"
          ></the-window-main>
        </main>

        <!-- Aside Right -->
        <aside class="window-aside--right">
          <the-window-aside-right :to-id="sessionID"></the-window-aside-right>
        </aside>
      </section>
    </div>
    <!-- 提示音 -->
    <easy-ring
      log
      :open="open"
      :ring.sync="ring"
      :loop="false"
      :src="notice"
      ref="ring"
    />
    <!-- 获取声音提示弹框 -->
    <el-dialog width="20%" :visible="showDialog" :show-close="false">
      是否允许开启音效
      <span slot="footer" class="dialog-footer">
        <el-button @click="showDialog = false">拒 绝</el-button>
        <el-button type="primary" @click="handlevisible">同 意</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import TheWindowHeader from "./components/TheWindowHeader";
import TheWindowMain from "./components/TheWindowMain";
import TheWindowAsideLeft from "./components/TheWindowAsideLeft";
import TheWindowAsideRight from "./components/TheWindowAsideRight";

import Socket from "@/utils/socket";
import config from "@/config";
import { E_Msg, E_MsgEvent, terminal } from "@/utils/enum";
import { mapGetters, mapActions } from "vuex";
import { EasyRingVueComponent as EasyRing } from "easy-ring";

export default {
  name: "Window",

  components: {
    TheWindowHeader,
    TheWindowAsideLeft,
    TheWindowAsideRight,
    TheWindowMain,
    EasyRing,
  },

  data() {
    return {
      sessionID: "",
      isStatus: false,
      //提示音相关
      open: false,
      ring: false,
      notice: require("@/assets/notice.mp3"),
      showDialog: false,
    };
  },

  computed: {
    ...mapGetters(["token", "userInfo", "wsUrl"]),
  },

  provide() {
    return {
      sendMessage: this.sendMessage,
      send: this.send,
      closeChatServe: this.closeChatServe,
      reChatServe: this.reChatServe,
    };
  },

  async created() {
    await this.getUserInfo();
    await this.initChatServe();
  },
  beforeDestroy() {
    this.closeChatServe();
  },
  methods: {
    handlenotice() {
      console.log(111);
      this.open = !this.open;
    },
    /**
     * 开启声音
     */
    handlevisible() {
      this.open = true; // ① 开启组件
      this.showDialog = false;
    },
    openComponent() {},
    ...mapActions(["getUserInfo"]),
    /**
     * 发送消息
     */
    sendMessage(data) {
      this.send(E_MsgEvent["CHAT"], {
        to_type: "user",
        ...data,
      });
    },

    /**
     * 通用发送
     */
    send(event, data) {
      this.socketServe.send({
        event,
        data: data,
      });
      // this.$refs.ring.stopRing();
      // this.test = false;
    },

    // 关闭连接
    closeChatServe() {
      this.socketServe.close();
    },

    // 重新连接
    reChatServe() {
      this.socketServe.init();
    },

    /**
     * 初始化聊天服务
     */
    initChatServe() {
      return new Promise((resolve, reject) => {
        const _this = this;
        this.socketServe = new Socket({
          ws: this.wsUrl,

          params: {
            token: this.token,
            type: "kefu",
            terminal,
          },

          open() {
            _this.isStatus = true;
            resolve();
          },

          message({ data }) {
            const { event, data: content } = JSON.parse(data) || {};
            console.log("e", event, content);
            switch (event) {
              case E_MsgEvent["CHAT"]:
                if (content.from_type == "user") {
                  _this.ring = true;
                }
                _this.$refs["chat"].$emit("message", content);
                _this.$refs["user"].$emit("message", content);
                break;
              case E_MsgEvent["ERROR"]:
                _this.$message.error(content.msg);
                break;
              case E_MsgEvent["NOTICE"]:
                _this.$message.info(content.msg);
                break;
              case E_MsgEvent["PING"]:
                console.log("===============心跳============");
                break;
              case E_MsgEvent["USER_ONLINE"]:
                _this.$refs["user"].$emit("useronline", content);
                break;
              case E_MsgEvent["TRANSFER"]:
                _this.$refs["user"].$emit("transfer", content);
                break;
            }
          },

          error(e) {
            reject();
          },

          close() {
            _this.isStatus = false;
          },
        });
      });
    },

    /**
     * 切换会话
     */
    changeSession(userID) {
      console.log(userID);
      this.sessionID = userID;
    },
  },
  mounted() {
    this.showDialog = true;
  },
};
</script>

<style lang="scss" scoped>
.window-contain {
  $--window-width: 1200px;
  $--window-height: 800px;

  $--window-header-height: 60px;
  $--window-aside-l-width: 240px;
  $--window-aside-r-width: 240px;

  display: flex;
  justify-content: center;
  align-items: center;
  padding-top: 50px;
  width: 100vw;
  min-height: 800px;
  min-width: 1200px;
  .window {
    display: flex;
    flex-direction: column;
    width: $--window-width;
    height: $--window-height;
    background-color: #ffffff;

    &-header {
      height: $--window-header-height;
    }

    &-content {
      flex: 1;
      display: flex;
      flex-direction: row;

      .window-aside {
        &--left {
          width: $--window-aside-l-width;
          border-right: 1px solid $--border-color-base;
        }

        &--right {
          width: $--window-aside-r-width;
          border-left: 1px solid $--border-color-base;
        }
      }

      .window-main {
        flex: 1;
      }
    }
  }
}
</style>
