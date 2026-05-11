<template>
  <div class="window__header">
    <!-- Title -->
    <div class="window-title">客服工作台</div>

    <!-- Widget -->
    <div class="window-widget">
      <!-- User -->
      <div class="widget-item m-r-20">
        <div class="notice">
          <!-- <span class="notice-text">声音</span> -->

          <img
            src="@/assets/images/noticeon.png"
            title="开启声音"
            class="image"
            v-if="open"
            @click="handleclick"
          />
          <img
            src="@/assets/images/noticeoff.png"
            title="关闭声音"
            class="image"
            v-else
            @click="handleclick"
          />

          <!-- 
          <el-switch
            v-model="value1"
            active-color="#13ce66"
            inactive-color="#dddfe5"
          >
          </el-switch> -->
        </div>

        <span class="m-r-6">{{ userInfo.nickname }}</span>

        <el-popover
          placement="right"
          title=""
          width="60"
          trigger="manual"
          class="line-status"
          popper-class="on-line"
          v-model="isTC"
        >
          <div class="">
            <div
              class="flex text-center"
              style="cursor: pointer"
              @click="onLine(true)"
            >
              <span
                class="m-r-10"
                style="
                  display: block;
                  content: ' ';
                  border-radius: 50%;
                  height: 10px;
                  width: 10px;
                  background-color: #00c24c;
                "
              ></span>
              在线
            </div>
            <div
              class="flex text-center"
              style="cursor: pointer"
              @click="offLine(false)"
            >
              <span
                class="m-r-10"
                style="
                  display: block;
                  content: ' ';
                  border-radius: 50%;
                  height: 10px;
                  width: 10px;
                  background-color: #c5c5c5;
                "
              ></span>
              离线
            </div>
          </div>
          <div
            class=""
            style="position: relative"
            slot="reference"
            @click="isTC = !isTC"
          >
            <el-avatar icon="el-icon-user" :src="userInfo.avatar"></el-avatar>
            <div
              v-if="status == false"
              style="
                position: absolute;
                top: 30px;
                left: 30px;
                height: 10px;
                width: 10px;
                border-radius: 50%;
                background-color: #c5c5c5;
              "
            ></div>
            <div
              v-else
              style="
                position: absolute;
                top: 30px;
                left: 30px;
                height: 10px;
                width: 10px;
                border-radius: 50%;
                background-color: #00c24c;
              "
            ></div>
          </div>
        </el-popover>
      </div>

      <!-- Logout -->
      <el-popconfirm title="确定退出吗？" @confirm="handleLogout">
        <div class="widget-item" slot="reference">
          <i class="el-icon-right"></i>
          <span class="m-l-6">退出</span>
        </div>
      </el-popconfirm>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  name: "TheWindowHeader",

  inject: ["closeChatServe", "reChatServe", "reload"],

  props: {
    isStatus: Boolean,
    open: {
      type: Boolean,
      default: false,
    },
  },
  emits: ["handleNotice"],
  data() {
    return {
      status: false,
      isTC: false,
      notice: false,
    };
  },

  watch: {
    isStatus(val) {
      this.status = val;
    },
  },

  computed: {
    ...mapGetters(["userInfo"]),
  },

  methods: {
    handleclick() {
      this.$emit("handleNotice");
    },
    ...mapActions(["logout"]),
    handleLogout() {
      const params = {};
      if (this.userInfo.shop_id != 0) {
        params.type = 1;
      }
      this.logout().then((res) => {
        this.$router.push({
          path: "/login",
          query: params,
        });
      });
    },
    onLine(val) {
      if (!this.status && val) {
        console.log("reChatServe");
        this.reChatServe();
      }

      this.status = val;
      this.isTC = false;
    },

    offLine(val) {
      if (this.status) {
        this.closeChatServe();
      }
      this.status = val;
      this.isTC = false;
      console.log(val);
    },
  },
};
</script>

<style lang="scss" scoped>
.window__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 100%;
  padding: 0 20px;
  background-color: $--color-primary;
  color: #ffffff;

  .window-title {
    font-size: 16px;
  }

  .window-widget {
    display: flex;
    align-items: center;

    .widget-item {
      display: flex;
      align-items: center;
      cursor: pointer;

      &:nth-child(n + 2) {
        margin-left: 20px;
      }
    }
  }

  .on-line {
    display: block;
    content: " ";
    border-radius: 50%;
    height: 10px;
    width: 10px;
    background-color: #00c24c;
  }

  .off-line {
    display: block;
    content: " ";
    border-radius: 50%;
    height: 10px;
    width: 10px;
    background-color: #c5c5c5;
  }
}
.notice {
  margin-right: 22px;
  display: flex;
  align-items: center;
  .notice-text {
    margin-right: 5px;
  }
  .image {
    width: 25px;
  }
}
</style>
