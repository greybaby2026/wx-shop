<template>
    <widget-root :styles="styles">
        <div
            class="seckill"
            :style="{
                'background-color': styles.content_bg_color,
                'border-radius': `${styles.border_radius_top}px ${styles.border_radius_top}px ${styles.border_radius_bottom}px ${styles.border_radius_bottom}px`
            }"
        >
            <div class="seckill-header flex" :style="[headerStyle]">
                <div class="flex-1 flex">
                    <img
                        v-if="content.header_icon_image"
                        class="flex-none m-r-5"
                        style="height: 22px"
                        :src="$getImageUri(content.header_icon_image)"
                    />
                    <div
                        class="line-1"
                        :style="{
                            'max-width': '200px',
                            color: styles.header_title_color,
                            'font-size': `${styles.header_title_size}px`
                        }"
                    >
                        {{ content.header_title }}
                    </div>
                </div>
                <div
                    class="more flex-none xs"
                    v-if="content.show_haeder_more"
                    :style="{
                        color: styles.header_more_color
                    }"
                >
                    {{ content.header_more_text }}
                    <i class="el-icon-arrow-right"></i>
                </div>
            </div>
            <div class="seckill-goods" :style="{ margin: `-${styles.margin / 2}px`, padding: '10px' }">
                <div
                    class="goods-lists"
                    :class="{
                        lists: content.style == 1,
                        swiper: content.style == 2
                    }"
                >
                    <div class="goods-wrap" v-for="(item, index) in goods" :key="index">
                        <div
                            class="goods-item"
                            :style="{
                                'background-color': styles.goods_bg_color,
                                margin: `${styles.margin / 2}px`,
                                'border-radius': `${styles.goods_border_radius}px`
                            }"
                        >
                            <div class="goods-image">
                                <el-image :src="item.image" fit="cover">
                                    <img
                                        slot="error"
                                        class="image-error"
                                        src="@/assets/images/goods_image.png"
                                        alt=""
                                    />
                                </el-image>
                            </div>
                            <div class="goods-info p-5">
                                <div
                                    v-if="content.show_title"
                                    class="m-b-4 name"
                                    :style="{
                                        color: styles.title_color
                                    }"
                                >
                                    <div :class="content.style == 2 ? 'line-1' : 'line-2'">
                                        {{ item.name || '这里是商品标题' }}
                                    </div>
                                </div>
                                <div
                                    class="muted xs"
                                    :style="{
                                        color: styles.sell_color
                                    }"
                                    v-if="content.style == 1 && content.show_sell"
                                >
                                    已预定0件
                                </div>
                                <div class="flex price-btn flex-wrap" v-if="content.style == 1">
                                    <div class="flex-1 flex col-baseline">
                                        <div
                                            v-if="content.show_price"
                                            class="price weight-500 m-r-5 xl"
                                            :style="{
                                                color: styles.price_color
                                            }"
                                        >
                                            <span class="xs">￥</span>{{ parseFloat(item.min_activity_price) || '0' }}
                                        </div>
                                        <div
                                            v-if="content.show_scribing_price"
                                            class="muted line-through xs"
                                            :style="{
                                                color: styles.scribing_price_color
                                            }"
                                        >
                                            ￥{{ parseFloat(item.min_price) || '0' }}
                                        </div>
                                    </div>
                                    <div
                                        class="buy-btn xs"
                                        v-if="content.style == 1 && content.show_btn"
                                        :style="{
                                            'background-color': styles.btn_bg_color,
                                            color: styles.btn_color
                                        }"
                                    >
                                        {{ content.btn_text }}
                                    </div>
                                </div>
                                <div
                                    v-else
                                    class="flex presell-price"
                                    :style="{ backgroundColor: styles.btn_bg_color, color: styles.btn_color }"
                                >
                                    预售价
                                    <div v-if="content.show_price" class="weight-500 m-r-5 xl">
                                        <span class="xs">￥</span>
                                        <span class="xs">{{ parseFloat(item.min_activity_price) || '0' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </widget-root>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import WidgetRoot from '@/components/decorate/widget-root.vue'
@Component({
    components: {
        WidgetRoot
    }
})
export default class Goods extends Vue {
    @Prop() content!: any
    @Prop() styles!: any

    get goods() {
        const { data, data_type, num } = this.content
        if (data_type == 1) {
            return [...Array(num).keys()].map(() => ({}))
        }
        return data.length ? data : [{}, {}, {}, {}]
    }

    get headerStyle() {
        const { header_bg_type, header_bg_image } = this.content
        const { header_bg_color } = this.styles
        return header_bg_type == 1
            ? {
                  'background-color': header_bg_color
              }
            : {
                  'background-image': `url(${this.$getImageUri(header_bg_image)})`
              }
    }
}
</script>

<style lang="scss" scoped>
.seckill {
    overflow: hidden;
    .seckill-header {
        color: #fff;
        height: 50px;
        background-color: #ff624b;
        background-repeat: no-repeat;
        background-size: 100% auto;
        padding: 0 5px 0 10px;
    }
    .seckill-goods {
        .goods-lists {
            overflow: hidden;

            &.swiper {
                display: flex;
                .goods-wrap {
                    flex: 0 0 36%;
                    width: 36%;
                }
            }
            &.lists {
                .goods-wrap {
                    .goods-item {
                        flex-direction: row;
                        .goods-image {
                            width: 120px;
                            padding-top: 120px;
                        }
                        .goods-info {
                            position: relative;
                            padding: 10px;
                            .name {
                                line-height: 18px;
                                height: 36px;
                            }
                            .price-btn {
                                position: absolute;
                                right: 10px;
                                left: 10px;
                                bottom: 10px;
                            }
                        }
                    }
                }
            }
            .goods-wrap {
                overflow: hidden;
                .goods-item {
                    overflow: hidden;
                    display: flex;
                    flex-direction: column;
                    .goods-image {
                        position: relative;
                        width: 100%;
                        padding-top: 100%;
                        box-sizing: border-box;
                        .el-image {
                            position: absolute;
                            top: 0;
                            right: 0;
                            bottom: 0;
                            left: 0;
                            width: 100%;
                            .image-error {
                                width: 100%;
                                height: 100%;
                                object-fit: cover;
                            }
                        }
                    }
                    .goods-info {
                        flex: 1;
                        .name {
                            color: #333333;
                        }
                        .price {
                            color: #ff2c3c;
                        }
                        .buy-btn {
                            padding: 6px 15px;
                            color: #ffffff;
                            background-color: #ff2c3c;
                            border-radius: 60px;
                        }
                    }
                }
            }
        }
    }
    .presell-price {
        background-color: #a411d1;
        color: white;
        font-size: 12px;
        align-items: center;
        justify-content: center;
    }
}
</style>
