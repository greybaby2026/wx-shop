import { mapGetters, mapState } from "vuex";
export default {
  data() {},
  methods: {
    /**
     * 事件驱动的页面数据刷新（替代原先「手动重跑页面生命周期」的做法）：
     *  - orderListRefresh：订单详情页删除订单返回后，通知列表页刷新
     *  - loginSuccess   ：静默登录成功后（如 token 失效自动登录），通知当前页刷新数据
     * 页面若具备 refreshOrderData()（订单/核销类页面的既有能力）则刷新；其它页面不响应，
     * 避免像原先那样「任何页面都被重跑一遍 onLoad/onShow」。
     */
    __onPageDataEvent() {
      if (typeof this.refreshOrderData === 'function') {
        this.refreshOrderData()
      }
    }
  },
  onLoad() {
    // 仅页面会执行 onLoad（组件无此生命周期），故监听只在页面级注册
    uni.$on('orderListRefresh', this.__onPageDataEvent)
    uni.$on('loginSuccess', this.__onPageDataEvent)
  },
  onUnload() {
    uni.$off('orderListRefresh', this.__onPageDataEvent)
    uni.$off('loginSuccess', this.__onPageDataEvent)
  },
  computed: {
    ...mapGetters([
      "isLogin",
      "themeName",
      "themeColor",
      "themeMinorColor",
      "userInfo",
      "appConfig",
    ]),
    themeCssVars() {
      return {
        '--theme-primary': this.themeColor,
        '--theme-minor': this.themeMinorColor,
      }
    }
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
