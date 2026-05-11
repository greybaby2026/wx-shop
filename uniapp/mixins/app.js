import { mapGetters, mapState } from "vuex";
export default {
  data() {},
  methods: {},
  computed: {
    ...mapGetters([
      "isLogin",
      "themeName",
      "themeColor",
      "userInfo",
      "appConfig",
    ]),
  },
  onShow() {
    if (this.isLogin) {
      this.$store.dispatch('checkUnread')
    }
  },
  onShareAppMessage() {
    const { share_image, share_intro, share_title } = this.appConfig;
    const { code } = this.userInfo;
    const share = {
      title: share_title,
      path: `/pages/index/index?invite_code=${code}`,
      imageUrl: share_image,
    };
    return share;
  },
  onShareTimeline() {
    const { share_image, share_intro, share_title } = this.appConfig;
    const { code } = this.userInfo;
    const share = {
      title: share_title,
      imageUrl: share_image,
    };
    return share;
  },
};
