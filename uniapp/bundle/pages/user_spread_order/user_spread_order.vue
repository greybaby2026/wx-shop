<template>
<view class="user-spread-order" :class="themeName">
   <tabs :current="active" @change="handleChange" bar-width="60" :is-scroll="false" :active-color="themeColor">
       <tab v-for="(item, index) in order" :key="index" :name="item.name">
           <spread-order  :type="item.type" :i="index" :index="active"></spread-order>
       </tab>
   </tabs>
</view>
</template>

<script>

import { DistributionOrderEnum } from "@/utils/enum";
import spreadOrder from '@/bundle/components/spread-order/spread-order.vue'
export default {
	components: {
		spreadOrder
	},
  data() {
    return {
      order: [{
        name: "全部",
        type: DistributionOrderEnum['ALL'],
      }, {
        name: "待返佣",
        type: DistributionOrderEnum['WAIT_RETURN'],
      }, {
        name: "已结算",
        type: DistributionOrderEnum['HANDLED'],
      }, {
        name: "已失效",
        type: DistributionOrderEnum['INVALED'],
      }],
      active: DistributionOrderEnum['ALL']
    };
  },
  onLoad: function (options) {
 
  },

  methods: {
    handleChange(index) {
      this.active = index
    },
  }
};
</script>
<style>
</style>