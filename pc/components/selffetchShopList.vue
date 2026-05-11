<template>
  <div class="address-list">
    <el-dialog title="选择门店" :visible.sync="showDialog" width="700px">
      <div class="list black infinite-list" v-infinite-scroll="loadShopList" style="overflow:auto">
        <div
          :class="['item m-b-16 flex', { active: item.id == selectId }]"
          v-for="item in list"
          :key="item.id"
          @click="onSelectShop(item)"
        >
        <div class="flex-1">
          <div>
            <span class="bold">{{ item.name }}</span>
           
          </div>

          <div class="lighter m-t-8">{{ item.detailed_address }}</div>

          <div class="muted m-t-8">
            <i class="el-icon-time"></i>
            <span>{{ item.business_start_time + '-' + item.business_end_time }}</span>
          </div>
        </div>
          
          <span class="muted m-l-10 flex-none">
              <i class="el-icon-position"></i>
              {{ item.distance }}
            </span>
        </div>
      </div>

      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="onConfirm">确认</el-button>
        <el-button @click="showDialog = false">取消</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>

export default {

  props: {
    value: {
      type: Boolean,
      default: false,
    },

    list: {
      type: Array,
      require: true,
    }
  },

  data() {
    return {
      showDialog: false,
      selectId: '',
    };
  },

  methods: {
    onConfirm() {
      const index = this.selectId;
      const shop = this.list.find(item => item.id === index)

      this.$emit("confirm", shop);
      this.showDialog = false;
    },

    onSelectShop(shop) {
      this.selectId = shop.id
    },

    loadShopList() {
      console.log('Loading Shopping ...')
      this.$emit("load", this.selectId)
    },
  },

  watch: {
    value(val) {
      this.showDialog = val;
    },

    showDialog(val) {
      this.$emit("input", val);
    },

    list(data) {
      this.selectId = data?.[0]?.['id']
    }
  },

};
</script>

<style lang="scss" scoped>
.address-list {
  ::v-deep .el-dialog__body {
    height: 460px;
    overflow-y: auto;
  }
  .list {
    margin: 0 auto;
    .item {
      position: relative;
      cursor: pointer;
      padding: 16px 20px;
      height: 100px;
      border: 1px solid $--border-color-base;
      border-radius: 2px;
      &.active {
        border-color: $--color-primary;
      }
      &.disabled {
        &::before {
          z-index: 9;
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          display: block;
          content: "";
          width: 100%;
          height: 100%;
          background-color: rgba(255, 255, 255, .5);
        }
      }
      .oprate {
        position: absolute;
        right: 20px;
        bottom: 9px;
      }
    }
  }
  .dialog-footer {
    text-align: center;
    .el-button {
      width: 160px;
    }
  }
}
</style>
