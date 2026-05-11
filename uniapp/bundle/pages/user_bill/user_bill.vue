<template>
  <view :class="themeName">
    <u-tabs
      font-size="28"
      name="tabsName"
      inactive-color="#333"
      :active-color="themeColor"
      :list="list"
      :is-scroll="false"
      :current="current"
      @change="change"
    >
    </u-tabs>

    <mescroll-uni
      ref="mescrollRef"
      bgColor="white"
      top="100rpx"
      @init="mescrollInit"
      :down="downOption"
      :up="upOption"
      @down="downCallback"
      @up="upCallback"
    >
      <view class="list mt20" v-if="lists.length !== 0">
        <view v-for="(item, index) in lists" :key="index" class="item">
          <view class="bill-list bg-white">
            <view class="bill-item flex row-between">
              <view>
                <view class="black mb10">{{ item.type_desc }}</view>
                <view class="xs muted">{{ item.create_time }}</view>
              </view>
              <view
                class="lg"
                :class="{
                  income: item.change_amount_desc[0] == '+' ? false : true,
                }"
                >{{ item.change_amount_desc }}</view
              >
            </view>
          </view>
        </view>
      </view>
    </mescroll-uni>
  </view>
</template>

<script>
// +----------------------------------------------------------------------
// | LikeShop100%开源免费商用电商系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | Gitee下载：https://gitee.com/likeshop_gitee/likeshop
// | 访问官网：https://www.likemarket.net
// | 访问社区：https://home.likemarket.net
// | 访问手册：http://doc.likemarket.net
// | 微信公众号：好象科技
// | 好象科技开发团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | Author: LikeShopTeam
// +----------------------------------------------------------------------
import { userBill } from "@/api/user";
import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins.js";
import MescrollMoreItemMixin from "@/components/mescroll-uni/mixins/mescroll-more-item.js";
export default {
  mixins: [MescrollMixin, MescrollMoreItemMixin],
  data() {
    return {
      list: [
        {
          tabsName: "全部",
          isShow: true,
          type: "bnw",
        },
        {
          tabsName: "支出",
          isShow: false,
          type: "bnw_dec",
        },
        {
          tabsName: "收入",
          isShow: false,
          type: "bnw_inc",
        },
      ],
      current: 0,
      lists: [],

      downOption: {
        auto: false, // 不自动加载 (mixin已处理第一个tab触发downCallback)
      },
      upOption: {
        auto: true, // 不自动加载
        noMoreSize: 4, //如果列表已无数据,可设置列表的总数量要大于半页才显示无更多数据;避免列表数据过少(比如只有一条数据),显示无更多数据会不好看; 默认5
        empty: {
          icon: "/static/images/empty/order.png",
          tip: "暂无账户明细", // 提示
          fixed: true,
          top: "200rpx",
        },
      },
    };
  },

  methods: {
    change(index) {
      for (let i = 0; i < this.list.length; i++) {
        this.list[i].isShow = false;
      }
      this.current = index;
      this.upCallback({
        num: 1,
        size: 10,
      });
      this.list[index].isShow = true;
    },

    upCallback(page) {
      let num = page.num;
      let size = (page.size += 5);

      userBill({
        type: this.list[this.current].type,
        page_no: num,
        page_size: size,
      })
        .then((res) => {
          console.log(res);
          if (page.num == 1) this.lists = []; //如果是第一页需手动置空列表
          this.lists = this.lists.concat(res.lists); //追加新数据
          this.mescroll.endBySize(res.lists.length, res.count);
        })
        .catch((err) => {
          this.mescroll.endBySize();
        });
    },
  },

  onShow() {},
};
</script>
<style lang="scss">
.list {
  .item {
    .time {
      padding: 30rpx;
    }

    .bill-list {
      .bill-item {
        padding: 20rpx 30rpx;

        // border-bottom: $-solid-border;
        .income {
          color: red;
        }
      }
    }
  }
}
</style>
