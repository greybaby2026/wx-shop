<template>
  <view class="p-24">
    <u-parse
      :html="content"
      :selectable="true"
      :show-with-animation="true"
    ></u-parse>
  </view>
</template>

<script>
import { apiPolicyAgreement } from "@/api/app";
export default {
  data() {
    return {
      content: "",
    };
  },

  methods: {
    // 获取服务协议
    getPolicyAgreement() {
      apiPolicyAgreement({
        type: this.type,
      }).then((res) => {
        this.content = res.content;

        uni.setNavigationBarTitle({
          title: String(res.name),
        });
      });
    },
  },

  onLoad() {
    this.type = this.$Route.query.type;
    this.getPolicyAgreement();
    // if (this.type == 1) {
    //   uni.setNavigationBarTitle({
    //     title: "服务协议",
    //   });
    if (this.type == 3) {
      uni.setNavigationBarTitle({
        title: "分销协议",
      });
    }
    // } else if (this.type == 2) {
    //   uni.setNavigationBarTitle({
    //     title: "隐私政策",
    //   });
    // } else if (this.type == 4) {
    //   uni.setNavigationBarTitle({
    //     title: "注销协议",
    //   });
    // }
  },
};
</script>

<style></style>
