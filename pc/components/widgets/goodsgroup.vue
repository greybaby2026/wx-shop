<template>
    <div class="goods bg-white">
        <div class="goods-header flex row-between">
            <div class="title">
                {{ content.title }}
                <span class="nr muted">{{ content.subtitle }}</span>
            </div>
            <div class="more lighter" v-if="content.show_more">
                <nuxt-link to="/goods_list">
                    更多 <i class="el-icon-arrow-right"></i>
                </nuxt-link>
            </div>
        </div>
        <div class="banner m-b-16" v-if="content.show_adv == 1">
            <app-link
                :to="{
                    path: content.adv_link.path,
                    query: content.adv_link.params,
                }"
            >
                <el-image
                    :src="$getImageUri(content.adv_url)"
                    style="width: 100%; height: 200px"
                    fit="cover"
                >
                </el-image>
            </app-link>
        </div>
        <div class="goods-lists">
            <nuxt-link
                class="goods-item"
                :to="`/goods_details?id=${item.id}`"
                v-for="(item, index) in goods"
                :key="index"
            >
                <div class="goods-image">
                    <el-image :src="item.image" fit="cover"> </el-image>
                </div>
                <div class="goods-name line-2">
                    {{ item.name }}
                </div>
                <div class="goods-price flex-1 flex col-baseline">
                    <div class="price weight-500 m-r-5 xl">
                        <price-formate
                            :price="item.sell_price"
                            :first-size="18"
                        />
                    </div>
                    <div class="muted line-through xs" v-if="Number(item.lineation_price)">
                        <price-formate
                            :price="item.lineation_price"
                            :first-size="12"
                            :second-size="12"
                            :subscript-size="12"
                        />
                    </div>
                     <div class="flex-1"></div>
                    <div class="muted xs" v-if="item.is_sales_num">{{item.sales_num || '0'}}人购买</div>
                </div>
            </nuxt-link>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        content: {
            type: [Object, Array],
        },
        styles: {
            type: [Object, Array],
        },
    },
    computed: {
        goods() {
            return this.content.data
        },
    },
}
</script>

<style lang="scss" scoped>
.goods {
    margin-top: 16px;
    border-radius: 8px;
    padding: 0 20px;
    .goods-header {
        height: 66px;
        .title {
            font-size: 20px;
        }
    }
    .goods-lists {
        display: flex;
        flex-wrap: wrap;
        .goods-item {
            width: 212px;
            margin-bottom: 24px;
            // box-shadow: 0 0 6px rgb(0 0 0 / 10%);
            border-radius: 4px;
            &:not(:nth-of-type(5n)) {
                margin-right: 20px;
            }
            .goods-image {
                height: 0;
                padding-top: 100%;
                position: relative;
                .el-image {
                    position: absolute;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    top: 0;
                    z-index: 0;
                }
            }
            .goods-name {
                margin-bottom: 10px;
                margin-top: 14px;
                height: 40px;
                line-height: 20px;
            }
            .goods-price {
                color: #ff2c3c;
            }
        }
    }
}
</style>