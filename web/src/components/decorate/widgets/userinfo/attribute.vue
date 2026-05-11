<template>
    <div>
        <attribute-tabs title="会员信息">
            <div>
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="背景设置">
                        <el-form-item label="背景图片">
                            <el-radio-group v-model="content.background_type">
                                <el-radio :label="1">系统默认</el-radio>
                                <el-radio :label="2">自定义</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item v-show="content.background_type == 2">
                            <material-select
                                v-model="content.background_image"
                                :size="60"
                                upload-bg="#fff"
                                :enable-domain="false"
                                :enable-delete="false"
                            >
                                <template v-slot:preview="{ item }">
                                    <div class="image-select">
                                        <el-image style="width: 100%; height: 100%" :src="item"></el-image>
                                    </div>
                                </template>
                                <div slot="upload" class="image-select flex-col row-center col-center">
                                    <i class="el-icon-plus lg"></i>
                                    <div>添加图片</div>
                                </div>
                            </material-select>
                            <div class="muted">建议图片尺寸：750px*340px</div>
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="会员信息">
                        <el-form-item label="默认头像">
                            <el-radio-group v-model="content.avatar_type">
                                <el-radio :label="1">系统默认</el-radio>
                                <el-radio :label="2">自定义</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item v-show="content.avatar_type == 2">
                            <material-select
                                v-model="content.avatar"
                                :size="60"
                                upload-bg="#fff"
                                :enable-domain="false"
                            >
                                <i class="el-icon-plus lg"></i>
                            </material-select>
                            <div class="muted">建议尺寸：100*100</div>
                        </el-form-item>
                        <el-form-item label="会员编号">
                            <el-radio-group v-model="content.show_user_sn">
                                <el-radio :label="1">显示</el-radio>
                                <el-radio :label="0">隐藏</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label="资产内容">
                            <el-checkbox-group v-model="content.assets">
                                <el-checkbox v-for="(item, index) in assetsOptions" :label="item.value" :key="index">{{
                                    item.name
                                }}</el-checkbox>
                            </el-checkbox-group>
                        </el-form-item>
                        <el-form-item label="等级入口">
                            <el-radio-group v-model="content.show_member">
                                <el-radio :label="1">显示</el-radio>
                                <el-radio :label="0">隐藏</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </attribute-item>
                </el-form>
            </div>
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
import MaterialSelect from '@/components/material-select/index.vue'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem,
        MaterialSelect
    }
})
export default class Attribute extends Vue {
    /** S data **/
    assetsOptions = [
        {
            name: '余额',
            value: 1
        },
        {
            name: '积分',
            value: 2
        },
        {
            name: '优惠券',
            value: 3
        },
        {
            name: '收藏',
            value: 4
        }
    ]

    /** E data **/

    /** S computed **/

    get content() {
        return this.$store.getters.content
    }

    set content(val) {
        const data = {
            key: 'content',
            value: val
        }
        this.$store.commit('setAttribute', data)
    }
    get styles() {
        return this.$store.getters.styles
    }

    /** E computed **/

    /** S methods **/

    /** E methods **/
}
</script>

<style lang="scss" scoped>
.image-select {
    height: 120px;
    width: 280px;
    border-radius: 4px;
    background: #fff;
    border: $--border-base;
    cursor: pointer;
    box-sizing: border-box;
}
</style>
