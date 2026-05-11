<template>
    <widget-root :styles="styles">
        <div
            class="notice"
            :style="{
                'background-color': styles.bg_color,
                'border-radius': `${styles.border_radius}px`
            }"
        >
            <div class="flex">
                <img
                    class="flex-none"
                    style="height: 17px"
                    :src="
                        content.icon_type == 1 ? require('@/assets/images/notice_icon.png') : $getImageUri(content.icon)
                    "
                />

                <div :style="{ 'border-color': styles.line_color }" class="flex left-line flex-1">
                    <div class="new-tag flex-none" v-if="content.show_tag">最新</div>
                    <div class="xs line-1" :style="{ color: styles.color }">公告标题</div>
                </div>

                <i class="el-icon-arrow-right"></i>
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
}
</script>

<style lang="scss" scoped>
.notice {
    padding: 10px;
    .left-line {
        border-left: 1px solid #e5e5e5;
        padding-left: 8px;
        margin-left: 8px;
    }
    .new-tag {
        color: #ff2c3c;
        padding: 0px 5px;
        margin-right: 5px;
        font-size: 10px;
        border-radius: 16px;
        background: transparent;
        border: 1px solid #ff2c3c;
    }
}
</style>
