<template>
  <div class="goods-list flex flex-wrap">
    <div @click="goTo(item)" class="goods-item bg-white pointer"
      :style="{ marginRight: (index + 1) % num == 0 ? 0 : '14px' }" v-for="(item, index) in list" :key="index">
      <el-image class="goods-img" lazy :src="item.image" alt=""></el-image>
      <div class="name line-2">{{ item.name }}</div>
      <div class="seckill flex row-between" v-if="type == 'seckill'">
        <div class="primary flex">
          秒杀价
          <price-formate :price="item.min_seckill_price" :first-size="18" />
        </div>
        <div :class="['btn bg-primary white', {'not-start' : status == 0, end: status == 2}]">{{getSeckillText}}</div>
      </div>
      <div class="flex row-between flex-wrap" v-else>

        <div class="price flex">
          <div class="primary m-r-8">
            <price-formate :price="item.sell_price" :first-size="16" />
          </div>
          <div class="muted sm line-through" v-if="Number(item.lineation_price)">
            <price-formate :price="item.lineation_price" />
          </div>
        </div>
        <div class="muted xs" v-if="item.is_sales_num">{{ item.sales_num || '0' }}人购买</div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      list: {
        type: Array,
        default: () => [],
      },
      num: {
        type: Number,
        default: 5,
      },
      type: {
        type: String,
      },
      status: {
        type: Number,
      },
    },
    watch: {
      list: {
        immediate: true,
        handler: function(val) {},
      },
    },
    computed: {
      getSeckillText() {
        switch (this.status) {
          case 0:
            return '未开始'
          case 1:
            return '立即抢购'
          case 2:
            return '已结束'
        }
      }
    },
    methods:{
        goTo(item) {
            if(this.type == 'seckill') {
                this.$router.push('/goods_details_seckill?id=' + item.id)
            }else {
                this.$router.push('/goods_details?id=' + item.id)
            }
        }
    }
  };
</script>

<style lang="scss" scoped>
  .goods-list {
    align-items: stretch;

    .goods-item {
      display: block;
      box-sizing: border-box;
      width: 224px;
      height: 310px;
      margin-bottom: 16px;
      padding: 12px 12px 16px;
      border-radius: 4px;
      transition: all 0.2s;

      &:hover {
        transform: translateY(-8px);
        box-shadow: 0px 0px 6px rgba(0, 0, 0, 0.1);
      }

      .goods-img {
        width: 200px;
        height: 200px;
      }

      .name {
        margin-bottom: 10px;
        height: 40px;
        line-height: 20px;
      }

      .seckill {
        .btn {
          padding: 4px 12px;
          border-radius: 4px;
          border: 1px solid transparent;

          &.not-start {
            border-color: $--color-primary;
            color: $--color-primary;
            background-color: transparent;
          }

          &.end {
            background-color: #E5E5E5;
            color: #fff;
          }
        }
      }
    }
  }
</style>
