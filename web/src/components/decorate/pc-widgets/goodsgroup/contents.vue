<template>
    <div class="goods bg-white">
        <div class="goods-header flex row-between">
            <div class="title">
                {{ content.title }}
                <span class="nr muted line-1">{{ content.subtitle }}</span>
            </div>
            <div class="more lighter" v-if="content.show_more">更多 <i class="el-icon-arrow-right"></i></div>
        </div>
        <div class="banner m-b-16" v-if="content.show_adv == 1">
            <el-image :src="$getImageUri(content.adv_url)" style="width: 100%; height: 200px" fit="cover">
                <div slot="error" class="image-error muted flex row-center">
                    <i class="el-icon-picture font-size-40"></i>
                </div>
            </el-image>
        </div>
        <div class="goods-lists">
            <div class="goods-item" v-for="(item, index) in goods" :key="index">
                <div class="goods-image">
                    <el-image :src="item.image" fit="cover">
                        <img slot="error" class="image-error" src="@/assets/images/goods_image.png" alt="" />
                    </el-image>
                </div>
                <div class="goods-name line-2">
                    {{ item.name || '这里是商品标题' }}
                </div>
                <div class="goods-price flex-1 flex col-baseline">
                    <div class="price weight-500 m-r-5 xl">
                        <span class="xs">￥</span>{{ parseFloat(item.sell_price) || '0' }}
                    </div>
                    <div class="muted line-through xs">￥{{ parseFloat(item.lineation_price) || '0' }}</div>
                    <div class="flex-1"></div>
                    <div class="muted xs">0人购买</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import Indicator from '@/components/decorate/indicator.vue'
import WidgetRoot from '@/components/decorate/widget-root.vue'
@Component({
    components: {
        Indicator,
        WidgetRoot
    }
})
export default class Contents extends Vue {
    @Prop() content!: any
    @Prop() styles!: any
    get goods() {
        const { data, goods_type, category } = this.content
        if (goods_type == 2) {
            return [...Array(category.num).keys()].map(() => ({}))
        }
        return data.length ? data : [{}, {}, {}, {}, {}]
    }
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
