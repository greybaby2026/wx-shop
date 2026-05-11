<template>
  <view class="store-information">
    <mescroll-uni
      ref="mescrollRef"
      bgColor="white"
      top="0rpx"
      @init="mescrollInit"
      :down="downOption"
      :up="upOption"
      @down="downCallback"
      @up="upCallback"
    >
      <block v-for="(item, index) in noticeList" :key="index">
        <view @click="toNoticeDetail(item.id)">
          <section-card
            :name="item.name"
            :views="item.views"
            :image="item.image"
            :desc="item.synopsis"
            :create_time="item.create_time"
            :is_tips="index == 0"
          />
        </view>
      </block>
    </mescroll-uni>
  </view>
</template>

<script>
import { apiShopNoticeList } from "@/api/store.js";
import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins.js";
import MescrollMoreItemMixin from "@/components/mescroll-uni/mixins/mescroll-more-item.js";
import { mapGetters, mapState } from "vuex";

export default {
  mixins: [MescrollMixin, MescrollMoreItemMixin],
  data() {
    return {
      noticeList: [],

      downOption: {
        auto: false, // 不自动加载 (mixin已处理第一个tab触发downCallback)
      },

      upOption: {
        auto: true, // 不自动加载
        noMoreSize: 4, //如果列表已无数据,可设置列表的总数量要大于半页才显示无更多数据;避免列表数据过少(比如只有一条数据),显示无更多数据会不好看; 默认5
        empty: {
          icon: "/static/images/empty/notice.png",
          tip: "暂无商城公告", // 提示
          fixed: true,
          top: "200rpx",
        },
      },
    };
  },
  methods: {
    upCallback({ num }) {
      apiShopNoticeList({
        page_no: num,
        page_size: 15,
      })
        .then((res) => {
          if (num == 1) this.noticeList = []; //如果是第一页需手动置空列表
          this.noticeList = this.noticeList.concat(res.lists); //追加新数据
          this.mescroll.endSuccess(
            res.lists.length,
            res.more == 0 ? false : true,
            ""
          );
        })
        .catch((err) => {
          this.mescroll.endErr();
        });
    },

    toNoticeDetail(id) {
      console.log(id);
      this.$Router.push({
        path: "/bundle/pages/store_notice_detail/store_notice_detail",
        query: {
          id: id,
          type: "store_notice",
        },
      });
    },
  },
  onShareAppMessage() {
    const { code } = this.userInfo;

    const share = {
      path: `/bundle/pages/store_notice/store_notice?invite_code=${code}`,
    };
    return share;
  },
  computed: {
    ...mapGetters(["userInfo"]),
  },
  onShow() {
    this.upCallback({ num: 1 });
  },
};
</script>

<style lang="scss" scoped>
.store-information {
  padding: 20rpx 20rpx 0;
}
</style>
