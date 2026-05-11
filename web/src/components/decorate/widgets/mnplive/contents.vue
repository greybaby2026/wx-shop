<template>
    <widget-root :styles="styles">
        <div
            class="mnplive"
            :style="{
                'background-color': styles.content_bg_color,
                'border-radius': `${styles.border_radius_top}px ${styles.border_radius_top}px ${styles.border_radius_bottom}px ${styles.border_radius_bottom}px`
            }"
        >
            <div class="mnplive-header flex" :style="[headerStyle]">
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
            <div class="mnplive-wrap" :style="{ margin: `-${styles.margin / 2}px` }">
                <div
                    class="mnplive-lists"
                    :class="{
                        larger: content.style == 1,
                        perline: content.style == 2,
                        lists: content.style == 3
                    }"
                >
                    <div class="mnplive-item-wrap" v-for="(item, index) in lists" :key="index">
                        <div
                            class="mnplive-item"
                            :style="{
                                'background-color': styles.bg_color,
                                margin: `${styles.margin / 2}px`,
                                'border-radius': `${styles.goods_border_radius}px`
                            }"
                        >
                            <div class="item-image">
                                <div class="status flex">
                                    <img class="live-img" src="@/assets/images/live.gif" alt="" />
                                    <span class="m-l-10 xs">直播中</span>
                                </div>
                                <div class="name">直播间名称</div>
                                <el-image :src="item.image" fit="cover">
                                    <img
                                        slot="error"
                                        class="image-error"
                                        src="@/assets/images/goods_image.png"
                                        alt=""
                                    />
                                </el-image>
                            </div>
                            <div class="info">
                                <div>
                                    <span class="lg weight-500">主播：主播名</span>
                                    <span v-if="content.style == 1" class="sm"> ｜ 直播商品：10件 </span>
                                    <div v-else class="sm m-t-10">直播商品：10件</div>
                                </div>
                                <div class="xs muted m-t-10">开播时间：2021-10-12 15:00</div>
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

    get lists() {
        const { num } = this.content
        return [...Array(num).keys()].map(() => ({}))
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
.mnplive {
    overflow: hidden;
    .mnplive-header {
        height: 50px;
        background-repeat: no-repeat;
        background-size: 100% auto;
        padding: 0 5px 0 10px;
    }
    .mnplive-wrap {
        overflow: hidden;
        .mnplive-lists {
            overflow: hidden;
            .mnplive-item-wrap {
                .mnplive-item {
                    .item-image {
                        height: 0;
                        position: relative;
                        .status {
                            color: #fff;
                            position: absolute;
                            top: 0;
                            border-radius: 0 30px 30px 0;
                            background: #ccc;
                            padding: 5px 10px;
                            background: linear-gradient(#ff2c3c 0%, #f95f2f 100%);
                            z-index: 1;
                            .live-img {
                                width: 15px;
                                height: 15px;
                            }
                        }
                        .name {
                            position: absolute;
                            background: linear-gradient(rgba(255, 255, 255, 0) 0%, #808080 100%);
                            bottom: 0;
                            width: 100%;
                            font-weight: 500;
                            font-size: 15px;
                            padding: 8px 10px;
                            z-index: 1;
                            color: #fff;
                        }
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
                    .info {
                        padding: 10px;
                    }
                }
            }
            &.larger {
                .item-image {
                    padding-top: 42%;
                }
            }
            &.perline {
                display: flex;
                flex-wrap: wrap;
                .mnplive-item-wrap {
                    width: 50%;
                    .mnplive-item {
                        .item-image {
                            padding-top: 100%;
                        }
                    }
                }
            }
            &.lists {
                .mnplive-item-wrap {
                    .mnplive-item {
                        display: flex;
                        .item-image {
                            flex: none;
                            width: 144px;
                            padding-top: 144px;
                        }
                    }
                }
            }
        }
    }
}
</style>
