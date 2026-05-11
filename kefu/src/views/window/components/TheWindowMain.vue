<template>
  <div class="window__main">
    <!-- History views -->
    <overlay-scrollbars
      class="history-contain"
      :options="{
        scrollbars: { autoHide: 'scroll' },
        overflowBehavior: { x: 'hidden' },
        callbacks: { onScroll: handlerMoreHistory },
      }"
      ref="scrollbar"
    >
      <!-- History Loading Status -->
      <morebar
        v-if="toId"
        :status="pagination.status"
        @onmore="getHistoryMore"
      ></morebar>

      <!-- Hisstory Content -->
      <div v-for="(item, index) in historyList" :key="item.id" :id="item.id">
        <div class="text-center muted p-t-10" v-if="timeFormat(item, index)">
          {{ timeFormat(item, index) }}
        </div>
        <!-- His message -->
        <div
          class="message-contain message--his"
          v-if="item.from_type === 'user'"
        >
          <chat-message :avatar="imageURL + item.from_avatar">
            <chat-content
              slot="his"
              :type="item.msg_type"
              :content="item.msg"
              :imageURL="imageURL"
            />
          </chat-message>
        </div>

        <!-- Mine message -->
        <div class="message-contain message--my" v-else>
          <chat-message :avatar="imageURL + item.from_avatar">
            <chat-content
              slot="my"
              :type="item.msg_type"
              :content="item.msg"
              :imageURL="imageURL"
            />
          </chat-message>
        </div>
      </div>

      <!-- No Session -->
      <template v-if="!toId">
        <el-empty description="请选择聊天用户" style="height: 100%"></el-empty>
      </template>
    </overlay-scrollbars>

    <!-- Editor -->
    <div class="editor-contain">
      <!-- Widget -->
      <div class="editor__widget">
        <!-- 发送表情 -->
        <el-popover
          placement="top"
          title
          width="240"
          trigger="click"
          :disabled="!toId"
        >
          <div class="flex flex-wrap" style="gap: 4px">
            <span
              :class="`em ${item} `"
              v-for="(item, index) in emoji"
              :key="index"
              @click="sendEmoji(item)"
              style="font-size: 20px"
            ></span>
          </div>
          <el-tooltip
            slot="reference"
            class="item"
            effect="dark"
            content="表情"
            placement="bottom"
          >
            <div class="flex" style="margin-top: -2px">
              <img
                src="@/assets/images/biaoqing.png"
                style="height: 20px; width: 20px; cursor: pointer"
              />
            </div>
          </el-tooltip>
        </el-popover>
        <!-- 选择图片 -->
        <el-tooltip
          slot="reference"
          class="item"
          effect="dark"
          content="图片"
          placement="bottom"
        >
          <el-upload
            :action="uploadURL"
            accept=".jpg, .jpeg, .png, .JPG, .JPEG"
            :headers="{ token: $store.getters.token, version }"
            :show-file-list="false"
            :before-upload="beforeImageUpload"
            :on-success="sendMessageImage"
            :disabled="!toId"
          >
            <i class="widget-item el-icon-picture-outline-round"></i>
          </el-upload>
        </el-tooltip>
        <!-- 快捷回复 -->
        <el-tooltip
          slot="reference"
          class="item"
          effect="dark"
          content="快捷回复"
          placement="bottom"
        >
          <i
            class="widget-item el-icon-chat-line-round m-b-4"
            @click="isShowReply"
          ></i>
        </el-tooltip>
        <!-- 切换客服 -->
        <el-popover
          placement="top"
          title
          width="120"
          trigger="manual"
          v-model="showKefu"
          :disabled="!toId"
        >
          <div>
            <el-scrollbar class="ls-scrollbar" style="height: 120px">
              <div class="kefu-list" v-if="kefuLists.length">
                <div
                  class="kefu-item flex m-b-10"
                  style="cursor: pointer"
                  v-for="(item, index) in kefuLists"
                  :key="index"
                  @click="transfer(item)"
                >
                  <img
                    style="width: 20px; height: 20px; border-radius: 50%"
                    :src="item.avatar"
                    alt
                  />
                  <div class="line-1 m-l-8 xs">{{ item.nickname }}</div>
                </div>
              </div>
              <div v-else class="muted xs">暂无可转接客服</div>
            </el-scrollbar>
          </div>
          <el-tooltip
            slot="reference"
            class="item"
            effect="dark"
            content="转线"
            placement="bottom"
          >
            <i
              class="widget-item el-icon-refresh"
              @click="showKefu = !showKefu"
            ></i>
          </el-tooltip>
        </el-popover>
      </div>

      <!-- Editor -->
      <el-input
        class="editor__textarea"
        v-model="editorContent"
        type="textarea"
        placeholder="请输入内容"
        @keydown.native="handleKeydown"
        @keydown.native.enter.prevent
        @keyup.native.enter="onSendMessage"
      />

      <!-- Footer -->
      <div class="editor__action">
        <el-button
          type="primary"
          size="small"
          :disabled="!editorContent || !toId"
          :loading="false"
          @click="onSendMessage"
          >发送</el-button
        >
      </div>
    </div>

    <!-- 快捷回复 -->
    <quick-reply v-model="showReply" @select="selectReply" />
  </div>
</template>

<script>
import ChatMessage from "@/components/ChatMessage";
import QuickReply from "./QuickReply";
import { apiChatHistory, apiUploadFile, apiServiceList } from "@/api/app";
import { E_Msg, E_MsgEvent, E_Load } from "@/utils/enum";
import Morebar from "@/components/Morebar";
import ChatContent from "@/components/ChatContent";
import config from "@/config";

import emojiArr from "@/utils/emojiArr.js";
import { debounce } from "@/utils/util.js";
import { timeFormatChat } from "@/utils/date";
// import notice from "@/assets/notice.mp3";

export default {
  name: "TheWindowMain",

  components: {
    ChatMessage,
    QuickReply,
    Morebar,
    ChatContent,
  },

  props: {
    toId: {
      type: [String, Number],
      default: "",
    },

    fromId: {
      type: [String, Number],
      default: "",
    },
  },

  inject: ["sendMessage", "send"],

  data() {
    return {
      // 编辑内容
      editorContent: "",
      // 历史消息列表
      historyList: [],
      // 显示快捷回复
      showReply: false,
      /* 分页器 */
      pagination: {
        size: 15,
        page: 1,
        status: E_Load["NORMAL"],
        total: 0,
        more: true,
      },
      /* 滚动条 */
      scrollbar: null,
      uploadURL: config.baseURL + "/kefuapi/file/image",
      version: config.version,
      kefuLists: [],
      showKefu: false,

      showEmoji: false, // 表情输入框
      emoji: [],

      handlerMoreHistory: null,
    };
  },

  watch: {
    toId: {
      handler(id) {
        this.changeSession(id);
      },
      immediate: true,
    },

    showKefu(val) {
      if (val) this.getKefuLists();
    },
  },
  computed: {
    timeFormat() {
      return (item, index) => {
        let timeFmt = timeFormatChat(item.create_time_stamp);
        if (
          index &&
          item.create_time_stamp -
            this.historyList[index - 1].create_time_stamp <
            300 &&
          !item.show_time
        ) {
          timeFmt = "";
        }

        return timeFmt;
      };
    },
    imageURL() {
      return this.$store.getters.baseUrl;
    },
  },
  created() {
    this.$on("message", this.receiveMessage);

    this.emoji = emojiArr;

    this.handlerMoreHistory = debounce(300, this.loadHistoryMore);
  },

  mounted() {
    this.scrollbar = this.$refs["scrollbar"].osInstance();
  },

  methods: {
    // 出顶加载更多

    async loadHistoryMore(e) {
      const { scrollTop } = e.target;
      if (scrollTop < 20) {
        let id = this.historyList[0] ? this.historyList[0].id : "";
        const topEl = document.getElementById(id);
        this.pagination.page++;
        this.historyList[0] && (this.historyList[0].show_time = true);
        await this.getChatHistory(this.toId);
        this.scrollbar.scroll(topEl);
      }

      console.log("scrollTop", scrollTop);
    },

    // 打开快捷回复
    isShowReply() {
      if (this.toId) {
        this.showReply = true;
      }
    },

    // 发送表情
    sendEmoji(item) {
      this.ring = true;
      this.editorContent += `[${item}]`;
      console.log(item);
    },
    handleKeydown(event) {
      if (event.shiftKey && event.keyCode == 13) {
        this.editorContent += "\r\n";
      }
    },
    /**
     * 点击发送消息
     */
    onSendMessage(event) {
      if (event.shiftKey) return;
      if (!this.toId) return this.$message.error("请选择聊天用户");
      this.sendMessage({
        msg: this.editorContent,
        msg_type: E_Msg["TEXT"],
        to_id: this.toId,
      });
      this.editorContent = "";

      const { max, position } = this.scrollbar.scroll();
      if (max.y - position.y <= 50) {
        this.scrollbar.scroll("100%");
      }
    },

    /**
     * 发送图片消息
     */
    sendMessageImage({ code, data, msg }) {
      if (code !== 1) return this.$message.error(msg);

      this.sendMessage({
        msg: data.url,
        msg_type: E_Msg["IMAGE"],
        to_id: this.toId,
      });
    },

    /**
     * 收到消息
     */
    receiveMessage(message) {
      console.log(message, 1111);
      if (message.to_id == this.toId || message.from_id == this.toId) {
        this.historyList.push(message);
      }
      const { max, position } = this.scrollbar.scroll();
      if (max.y - position.y <= 50) {
        this.$nextTick(() => {
          this.scrollbar.scroll("100%");
        });
      }
    },

    /**
     * 获取历史消息
     */
    getChatHistory(id) {
      if (!id) return;
      return new Promise((resolve, reject) => {
        const { size, page, status } = this.pagination;
        console.log(status);
        if (status == E_Load["EMPTY"]) return;

        this.pagination.status = E_Load["LOAD"];
        apiChatHistory({
          user_id: id,
          page_no: page,
          page_size: size,
        })
          .then((data) => {
            this.historyList = [...data.lists, ...this.historyList];
            this.pagination.total = data.count;
            this.pagination.status =
              data.lists.length < size ? E_Load["EMPTY"] : E_Load["NORMAL"];
            resolve(data);
          })
          .catch((err) => {
            this.pagination.status = E_Load["ERROR"];
            reject(err);
          });
      });
    },

    /**
     * 获取更多记录信息
     */
    async getHistoryMore() {
      const topEl = document.getElementById(this.historyList[0].id);
      this.historyList[0].show_time = true;
      this.pagination.page++;
      await this.getChatHistory(this.toId);
      this.scrollbar.scroll(topEl);
    },

    // 选择快捷回复
    selectReply(msg) {
      this.editorContent = msg;
    },

    /**
     * 切换会话
     */
    async changeSession(id) {
      this.pagination.size = 15;
      this.pagination.page = 1;
      this.pagination.total = 0;
      this.pagination.more = true;
      this.pagination.status = E_Load["NORMAL"];
      this.historyList = [];
      id && (await this.getChatHistory(id));

      this.$nextTick(() => {
        this.scrollbar.scroll("100%");
        console.log(this.scrollbar.scroll());
      });
    },

    /**
     * 上传图片之前
     */
    beforeImageUpload(file) {
      // const isJPG = file.type === 'image/jpeg';
      // const isPNG = file.type === 'image/png';
      const isLt2M = file.size / 1024 / 1024 < 2;

      if (!isLt2M) {
        this.$message.error("上传图片大小不能超过 2MB!");
      }
      return isLt2M;
    },

    getKefuLists() {
      apiServiceList().then((res) => {
        this.kefuLists = res;
      });
    },

    transfer(item) {
      this.send("transfer", {
        user_id: this.toId, // 用户id
        kefu_id: item.id, // 新客服id
      });
      this.showKefu = false;
    },
  },
};
</script>

<style lang="scss" scoped>
.window__main {
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100%;

  /* 历史消息容器 */
  .history-contain {
    flex: 1;
    padding: 0 20px;
    width: 100%;

    .history-status {
      padding: 10px;
      font-size: 12px;
      text-align: center;

      &--load {
        color: $--color-primary;
      }

      &--more {
        color: $--color-primary;
        cursor: pointer;
      }

      &--empty {
        color: $--color-text-regular;
      }
    }

    .message-contain {
      padding: 10px 0;
    }

    .message--my {
      display: flex;
      justify-content: flex-end;
    }

    .message--his {
      display: flex;
      justify-content: flex-start;
    }
  }

  /* 编辑器容器 */
  .editor-contain {
    display: flex;
    flex-direction: column;
    height: 180px;
    padding: 0 10px 20px 10px;
    border-top: 1px solid $--border-color-base;

    // 小部件
    .editor__widget {
      display: flex;
      align-items: center;
      height: 40px;
      gap: 12px;

      .widget-item {
        font-size: 20px;
        color: $--color-text-secondary;
        cursor: pointer;
      }
    }

    // 编辑框
    .editor__textarea {
      flex: 1;

      &::v-deep .el-textarea__inner {
        height: 100%;
        padding: 0;
        border: none;
      }
    }

    // 行为
    .editor__action {
      display: flex;
      justify-content: flex-end;
      padding-top: 10px;
    }
  }
}
</style>
