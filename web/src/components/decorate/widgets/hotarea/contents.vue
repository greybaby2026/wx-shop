<template>
    <widget-root :styles="styles">
        <div class="">
            <div class="1">
                <div class="item-image" v-if="content.data.imgurl">
                    <el-image fit="cover" :src="$getImageUri(content.data.imgurl)" style="overflow: visible"></el-image>
                </div>
                <el-image style="height: 375px; width: 100%" v-else>
                    <div slot="error" class="image-error muted flex row-center">
                        <i class="el-icon-picture font-size-40"></i>
                    </div>
                </el-image>
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
export default class SearchContents extends Vue {
    @Prop() content!: object | any[]
    @Prop() styles!: object | any[]
}
</script>

<style lang="scss" scoped>
.graphic {
    .graphic-list {
        display: flex;
        .graphic-item {
            overflow: hidden;
            flex: none;
            width: 140px;
            background-color: #fff;
            &:not(:last-of-type) {
                margin-right: 10px;
            }
            .info {
                width: 100%;
                text-align: center;
                font-size: 15px;
                line-height: 1;
                padding: 10px 8px;
                .title {
                    font-weight: bold;
                    font-size: 16px;
                }
                .subtitle {
                    margin-top: 10px;
                    font-size: 12px;
                }
            }
        }
    }
}
</style>
