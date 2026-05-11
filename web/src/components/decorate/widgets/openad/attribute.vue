<template>
    <div>
        <attribute-tabs title="弹窗广告">
            <div class="m-20">
                <div class="bold md">弹窗设置</div>
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <el-form-item label-width="80px" label="是否开启">
                        <el-radio-group v-model="content.enable">
                            <el-radio label="1">开启</el-radio>
                            <el-radio label="0">关闭</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label-width="80px" label="广告图">
                        <material-select
                            class="m-r-10"
                            ref="materialSelect"
                            v-model="content.image"
                            :size="72"
                            upload-bg="#fff"
                            :enable-domain="false"
                        >
                            <div class="text-center">
                                <i class="el-icon-plus"></i>
                                <div>添加照片</div>
                            </div>
                        </material-select>
                        <div class="muted">建议尺寸：588*708</div>
                    </el-form-item>
                    <el-form-item label-width="80px" label="设置链接">
                        <link-select v-model="content.link" />
                    </el-form-item>
                    <el-form-item label-width="80px" label="显示设置">
                        <el-radio-group v-model="content.show_config">
                            <el-radio label="1">仅首次</el-radio>
                            <el-radio label="2">多次</el-radio>
                        </el-radio-group>
                        <div class="muted" v-if="content.show_config == 1">
                            进入商城只显示一次广告，再次进入商城不再显示，更换广告图后进入商城可再次显示
                        </div>
                    </el-form-item>
                    <el-form-item label-width="80px" label="显示次数" v-if="content.show_config == 2">
                        <el-input v-model="content.show_config_number">
                            <template slot="append">次/日</template></el-input
                        >
                        <div class="muted">每次进入商城显示的广告，建议每人每日显示弹窗不超过3次</div>
                    </el-form-item>
                </el-form>
            </div>

            <!-- <div slot="styles">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <el-form ref="form" label-width="80px" size="small" label-position="left">
                        <attribute-item title="关闭设置">
                            <el-form-item label="底部背景">
                                <color-select v-model="styles.root_bg_color" reset-color="" />
                            </el-form-item>
                            <el-form-item label="图标颜色">
                                <color-select v-model="styles.icon_color" reset-color="" />
                            </el-form-item>
                        </attribute-item>
                        <attribute-item title="边距设置">
                            <el-form-item label="上边距">
                                <slider v-model="styles.padding_top" />
                            </el-form-item>
                            <el-form-item label="下边距">
                                <slider v-model="styles.padding_bottom" />
                            </el-form-item>
                            <el-form-item label="左右边距">
                                <slider v-model="styles.padding_horizontal" />
                            </el-form-item>
                        </attribute-item>
                    </el-form>
                </el-form>
            </div> -->
        </attribute-tabs>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import AttributeTabs from '@/components/decorate/attribute-tabs.vue'
import ColorSelect from '@/components/decorate/color-select.vue'
import StyleChose from '@/components/decorate/style-chose.vue'
import Slider from '@/components/decorate/slider.vue'
import AttributeItem from '@/components/decorate/attribute-item.vue'
import LinkSelect from '@/components/link-select/index.vue'
import MaterialSelect from '@/components/material-select/index.vue'
import Draggable from 'vuedraggable'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem,
        MaterialSelect,
        LinkSelect,
        Draggable
    }
})
export default class Attribute extends Vue {
    @Prop() content!: any
    @Prop() styles!: any
    /** S data **/
    $refs!: { materialSelect: any }
    /** E data **/

    /** S computed **/

    /** E computed **/

    /** S methods **/
    onAdd() {
        if (this.content.data.length > 5) {
            return this.$message.warning('最多五张图片')
        }
        this.content.data.push({
            url: '',
            link: {}
        })
    }
    onDelete(index: number) {
        this.content.data.splice(index, 1)
    }
    /** E methods **/
}
</script>

<style lang="scss" scoped>
.nav-list {
    .nav-item {
        background: #f9f9f9;
        padding: 20px;
        margin-bottom: 20px;
        cursor: move;
        &.forbid {
            cursor: unset;
        }
    }
}
.add-nav {
    width: 100%;
}
</style>
