<template>
    <div class="index">
        <div v-for="(item, index) in pagesData" :key="index">
            <template v-if="item.name == 'adv'">
                <adv
                    v-if="item.show"
                    :content="item.content"
                    :styles="item.styles"
                />
            </template>
            <template v-if="item.name == 'banner'">
                <banner
                    v-if="item.show"
                    :content="item.content"
                    :styles="item.styles"
                />
            </template>
            <template v-if="item.name == 'goodsgroup'">
                <goodsgroup
                    v-if="item.show"
                    :content="item.content"
                    :styles="item.styles"
                />
            </template>
            <template v-if="item.name == 'seckill'">
                <seckill
                    v-if="item.show"
                    :content="item.content"
                    :styles="item.styles"
                />
            </template>
        </div>
        <!-- 服务协议 -->
        <server-explan></server-explan>
    </div>
</template>

<script>
import adv from "@/components/widgets/adv.vue";
import banner from "@/components/widgets/banner.vue";
import goodsgroup from "@/components/widgets/goodsgroup.vue";
import seckill from "@/components/widgets/seckill.vue";
import headerMixins from "@/mixins/header";
export default {
    mixins: [headerMixins],
    components: {
        adv,
        banner,
        goodsgroup,
        seckill,
    },
    async asyncData({ query, $get }) {
        // 获取商品分类数据
        let { data } = await $get("Pc/getPage", {
            type: 1,
        });
        return {
            pagesData: data.content,
        };
    },
    data() {
        return {
            pagesData: [],
        };
    },
    methods: {},
};
</script>

<style lang="scss" scoped></style>
