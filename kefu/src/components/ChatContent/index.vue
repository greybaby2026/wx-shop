<template>
  <div class="chat-content">
    <!-- Text Message -->
    <template v-if="MsgMap['TEXT'] === type">
      <div
        class="chat-text"
        v-html="$options.filters.textToHtml(content)"
      ></div>
    </template>

    <!-- Image Message -->
    <template v-else-if="MsgMap['IMAGE'] === type">
      <el-image style="width: 16em" :src="imageURL + content"></el-image>
    </template>

    <!-- Image Message -->
    <template v-else-if="MsgMap['GOODS'] === type">
      <div class="flex goods-message">
        <div class="goods-image m-r-10">
          <el-image
            :src="imageURL + goods.image"
            style="width: 80px; height: 80px"
          ></el-image>
        </div>
        <div class="">
          <div class="goods-name nr line-2">
            {{ goods.name }}
          </div>
          <div class="goods-price m-t-10 xs">￥{{ goods.min_price }}</div>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import { E_Msg } from "@/utils/enum";
export default {
  name: "ChatContent",

  props: {
    type: {
      type: [String, Number],
      require: true,
    },

    content: {
      type: [String, Number],
      require: true,
    },

    imageURL: {
      type: String,
    },
  },

  data() {
    return {
      MsgMap: Object.freeze({ ...E_Msg }),
    };
  },

  filters: {
    textToHtml(content) {
      console.log(content);

      return content.replace(
        /\[em-([a-z_]+)\]/g,
        `<span class="em em-$1"></span>`
      );
    },
  },

  computed: {
    goods() {
      return JSON.parse(this.content);
    },
  },
};
</script>

<style lang="scss" scoped>
.chat-content {
  .goods-message {
    width: 300px;
    .goods-image {
      min-width: 80px;
      min-height: 80px;
    }

    .goods-price {
      color: #ff2c3c;
    }
  }
  .chat-text {
    word-wrap: break-word;
  }
}
</style>
