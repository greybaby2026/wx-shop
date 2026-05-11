<template>
    <widget-root :styles="styles">
        <div class="coupon" :style="{ '--bgcolor': styles.root_bg_color || '#ffffff' }">
            <div class="coupon-style1" v-if="content.style == 1">
                <div class="coupon-item" v-for="(item, index) in list" :key="index">
                    <div
                        class="coupon-con flex"
                        :style="{
                            'background-color': styles.bg_color,
                            color: styles.text_color
                        }"
                    >
                        <div class="coupon-price flex-col col-center row-center flex-none">
                            <div class="font-size-20 weight-500">
                                ￥<span class="font-size-30 money">{{ parseFloat(item.money) || '10' }}</span>
                            </div>
                            <div class="xs">
                                {{ item.condition || '满100可用' }}
                            </div>
                        </div>
                        <div
                            class="coupon-line"
                            :style="{
                                'background-image': `repeating-linear-gradient(${styles.text_color}, ${styles.text_color} 4px, transparent 4px,transparent 8px)`
                            }"
                        ></div>
                        <div class="coupon-info flex flex-1">
                            <div class="flex-1">
                                <div style="max-width: 130px" class="lg weight-500 line-1">
                                    {{ item.name || '优惠券名称' }}
                                </div>
                                <div class="xs m-t-5">
                                    {{ item.use_type || '全场通用' }}
                                </div>
                            </div>
                            <div
                                class="coupon-btn xs flex-none"
                                :style="{
                                    'background-color': styles.btn_bg_color,
                                    color: styles.btn_text_color
                                }"
                            >
                                领取
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="coupon-style2 flex" v-if="content.style == 2">
                <div class="coupon-item flex-none" v-for="(item, index) in list" :key="index">
                    <div
                        class="coupon-con flex"
                        :style="{
                            'background-color': styles.bg_color,
                            color: styles.text_color
                        }"
                    >
                        <div class="coupon-price flex-col col-center row-center flex-none">
                            <div class="font-size-20 weight-500">
                                ￥<span class="font-size-30 money">{{ parseFloat(item.money) || '10' }}</span>
                            </div>
                            <div class="xs">
                                {{ item.condition || '满100可用' }}
                            </div>
                        </div>
                        <div
                            class="coupon-line"
                            :style="{
                                'background-image': `repeating-linear-gradient(${styles.text_color} 0px, ${styles.text_color} 4px, transparent 4px,transparent 8px)`
                            }"
                        ></div>
                        <div class="coupon-btn">立即领取</div>
                    </div>
                </div>
            </div>
            <div
                class="coupon-style3"
                v-if="content.style == 3"
                :style="[content.bg_type == 2 ? { 'background-image': `url(${styles.bg_image})` } : {}]"
            >
                <div class="title xxl" :style="{ color: styles.title_color }">{{ content.title }}</div>
                <div class="coupon-list flex">
                    <div class="coupon-item flex-none" v-for="(item, index) in list" :key="index">
                        <div
                            class="coupon-con flex-col col-center row-center"
                            :style="{
                                'background-color': styles.bg_color
                            }"
                        >
                            <div class="coupon-price">
                                <div class="font-size-20 weight-500" :style="{ color: styles.money_color }">
                                    ￥<span class="font-size-30 money">{{ parseFloat(item.money) || '10' }}</span>
                                </div>
                            </div>
                            <div class="xs" :style="{ color: styles.condition_color }">
                                {{ item.condition || '满100可用' }}
                            </div>
                            <div class="xs m-t-2" :style="{ color: styles.scene_color }">
                                {{ item.use_type || '全场通用' }}
                            </div>
                            <div
                                class="coupon-btn xs flex-none"
                                :style="{
                                    'background-color': styles.btn_bg_color,
                                    color: styles.btn_text_color
                                }"
                            >
                                立即领取
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
export default class Contents extends Vue {
    @Prop() content!: any
    @Prop() styles!: any

    get list() {
        return this.content.data.length ? this.content.data : [{}, {}, {}]
    }
}
</script>

<style lang="scss" scoped>
.coupon {
    overflow: hidden;
    .coupon-style1,
    .coupon-style2 {
        .coupon-item {
            overflow: hidden;
            height: 80px;
            &:not(:last-of-type) {
                margin-bottom: 10px;
            }
            .coupon-con {
                height: 100%;
                position: relative;
                border-radius: 5px;
                color: #ff2c3c;
                border: 1px solid currentColor;
                background: #fce7e7;
                &::before,
                &::after {
                    content: '';
                    position: absolute;
                    background-color: var(--bgcolor);
                    width: 14px;
                    height: 14px;
                    border-radius: 50%;
                    top: 50%;
                    left: 0;
                    transform: translate(-50%, -50%);
                    border: 1px solid currentColor;
                    box-sizing: border-box;
                }
                .coupon-price {
                    width: 110px;
                    padding: 0 10px;
                    box-sizing: border-box;
                    .money {
                        line-height: 35px;
                    }
                }
                .coupon-line {
                    width: 2px;
                    height: 56px;
                    background-size: 2px 10px;
                    background-repeat: repeat-y;
                    background-position: 50%;
                }
                .coupon-info {
                    padding: 0 10px 0 15px;
                    .coupon-btn {
                        padding: 4px 20px;
                        border-radius: 14px;
                        background: #ff2214;
                        color: #ffffff;
                    }
                }
            }
        }
    }
    .coupon-style2 {
        .coupon-item {
            &:not(:last-of-type) {
                margin-right: 10px;
                margin-bottom: 0;
            }
            .coupon-con {
                &::before {
                    top: 0;
                    left: 111px;
                }
                &::after {
                    bottom: -14px;
                    left: 111px;
                    top: auto;
                }

                .coupon-btn {
                    width: 40px;
                    padding: 0 10px;
                    text-align: center;
                    white-space: wrap;
                    line-height: 14px;
                }
            }
        }
    }
    .coupon-style3 {
        background-image: url('../../../../assets/images/coupon_bg.png');
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        padding: 10px;
        border-radius: 10px;
        .coupon-list {
            margin-top: 14px;
            overflow: hidden;
            .coupon-item {
                width: 95px;
                height: 120px;
                &:not(:last-of-type) {
                    margin-right: 10px;
                }
                .coupon-con {
                    background: #ffffff;
                    height: 100%;
                    border-radius: 5px;
                    .coupon-price {
                        .money {
                            line-height: 35px;
                        }
                    }
                    .coupon-btn {
                        padding: 1px 10px;
                        border-radius: 14px;
                        background: #ff2214;
                        color: #ffffff;
                        margin-top: 8px;
                    }
                }
            }
        }
    }
}
</style>
