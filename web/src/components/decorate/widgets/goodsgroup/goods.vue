<template>
    <div
        class="goods-group"
        :style="{
            margin: `-${styles.margin / 2}px`,
            'background-color': styles.content_bg_color,
            'border-radius': `${styles.border_radius_top}px ${styles.border_radius_top}px ${styles.border_radius_bottom}px ${styles.border_radius_bottom}px`,
            padding: `${styles.padding / 2}px`
        }"
    >
        <div
            class="goods-lists"
            :class="{
                larger: content.style == 1,
                perline: content.style == 2,
                swiper: content.style == 3,
                lists: content.style == 4
            }"
        >
            <div class="goods-wrap" v-for="(item, index) in goods" :key="index">
                <div
                    class="goods-item"
                    :style="{
                        'background-color': styles.bg_color,
                        margin: `${styles.margin / 2}px`,
                        'border-radius': `${styles.goods_border_radius}px`
                    }"
                >
                    <div class="goods-image">
                        <el-image :src="item.image" fit="cover">
                            <img slot="error" class="image-error" src="@/assets/images/goods_image.png" alt="" />
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
                            <div :class="content.style == 3 ? 'line-1' : 'line-2'">
                                {{ item.name || '这里是商品标题' }}
                            </div>
                        </div>
                        <div class="flex price-btn flex-wrap">
                            <div class="flex-1 flex col-baseline">
                                <div
                                    v-if="content.show_price"
                                    class="price weight-500 m-r-5 xl"
                                    :style="{
                                        color: styles.price_color
                                    }"
                                >
                                    <span class="xs">￥</span>{{ parseFloat(item.sell_price) || '0' }}
                                </div>
                                <div class="muted line-through xs" v-if="content.show_scribing_price">
                                    ￥{{ parseFloat(item.lineation_price) || '0' }}
                                </div>
                            </div>
                            <div
                                class="buy-btn xs"
                                v-if="content.show_btn && (content.style == 1 || content.style == 4)"
                                :style="[btnStyle]"
                            >
                                {{ content.btn_text }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
@Component
export default class Goods extends Vue {
    @Prop() content!: any
    @Prop() styles!: any

    get goods() {
        const { data, goods_type, category } = this.content
        if (goods_type == 2) {
            return [...Array(category.num).keys()].map(() => ({}))
        }
        return data.length ? data : [{}, {}, {}, {}]
    }
    get btnStyle() {
        const { btn_bg_color, btn_color, btn_border_radius, btn_border_color } = this.styles
        const style = {
            'background-color': btn_bg_color,
            color: btn_color,
            'border-radius': `${btn_border_radius}px`,
            'border-color': btn_border_color
        }
        return style
    }
}
</script>

<style lang="scss" scoped>
.goods-group {
    overflow: hidden;
    .goods-lists {
        overflow: hidden;
        &.larger {
            .goods-wrap {
                .goods-item {
                    .goods-info {
                        padding: 10px;
                    }
                    .goods-image {
                        padding-top: 46%;
                    }
                }
            }
        }

        &.perline {
            display: flex;
            flex-wrap: wrap;
            .goods-wrap {
                width: 50%;
                .goods-info {
                    .name {
                        line-height: 18px;
                        height: 36px;
                    }
                }
            }
        }
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

                    .buy-btn {
                        flex: none;
                        background: #ff2c3c;
                        padding: 2px 16px;
                        color: #fff;
                        border-radius: 2px;
                        border: 1px solid transparent;
                    }
                    .name {
                        color: #333333;
                    }
                    .price {
                        color: #ff2c3c;
                    }
                }
            }
        }
    }
}
</style>
