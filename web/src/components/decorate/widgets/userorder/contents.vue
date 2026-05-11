<template>
    <widget-root :styles="styles">
        <div
            class="user-order"
            :style="{
                'background-color': styles.bg_color,
                'border-radius': `${styles.border_radius_top}px ${styles.border_radius_top}px ${styles.border_radius_bottom}px ${styles.border_radius_bottom}px`
            }"
        >
            <div class="order-title flex row-between">
                <div class="weight-500">{{ content.text }}</div>
                <div class="sm muted">全部订单 <i class="el-icon-arrow-right"></i></div>
            </div>
            <div class="order-nav flex">
                <div class="nav-item flex-col col-center">
                    <el-image class="nav-icon" :src="$getImageUri(content.pay_icon)">
                        <div slot="error" class="image-error muted flex row-center">
                            <i class="el-icon-picture font-size-20"></i>
                        </div>
                    </el-image>
                    <div class="xs">{{ content.pay_name }}</div>
                </div>
                <div class="nav-item flex-col col-center">
                    <el-image class="nav-icon" :src="$getImageUri(content.delivery_icon)">
                        <div slot="error" class="image-error muted flex row-center">
                            <i class="el-icon-picture font-size-20"></i>
                        </div>
                    </el-image>
                    <div class="xs">{{ content.delivery_name }}</div>
                </div>
                <div class="nav-item flex-col col-center">
                    <el-image class="nav-icon" :src="$getImageUri(content.take_icon)">
                        <div slot="error" class="image-error muted flex row-center">
                            <i class="el-icon-picture font-size-20"></i>
                        </div>
                    </el-image>
                    <div class="xs">{{ content.take_name }}</div>
                </div>
                <div class="nav-item flex-col col-center">
                    <el-image class="nav-icon" :src="$getImageUri(content.comment_icon)">
                        <div slot="error" class="image-error muted flex row-center">
                            <i class="el-icon-picture font-size-20"></i>
                        </div>
                    </el-image>
                    <div class="xs">{{ content.comment_name }}</div>
                </div>
                <div class="nav-item flex-col col-center">
                    <el-image class="nav-icon" :src="$getImageUri(content.sale_icon)">
                        <div slot="error" class="image-error muted flex row-center">
                            <i class="el-icon-picture font-size-20"></i>
                        </div>
                    </el-image>
                    <div class="xs">{{ content.sale_name }}</div>
                </div>
            </div>
        </div>
    </widget-root>
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
}
</script>

<style lang="scss" scoped>
.user-order {
    .order-title {
        border-bottom: $--border-base;
        padding: 12px 15px;
    }
    .order-nav {
        padding: 13px 0;
        .nav-item {
            flex: 1;
            .nav-icon {
                width: 26px;
                height: 26px;
                margin-bottom: 6px;
            }
        }
    }
}
</style>
