<template>
    <div class="page-info">
        <attribute-tabs title="页面设置">
            <el-form ref="form" label-width="80px" size="small">
                <attribute-item>
                    <el-form-item label="页面标题" v-if="configs.setTitle">
                        <el-input
                            v-model="pagesInfo.common.title"
                            @change="handleChange"
                            maxlength="8"
                            show-word-limit
                            placeholder="请输入页面标题"
                        ></el-input>
                    </el-form-item>
                    <el-form-item label="页面背景" v-if="configs.setBg">
                        <el-radio-group v-model="pagesInfo.common.background_type">
                            <el-radio label="1">背景颜色</el-radio>
                            <el-radio label="2">背景图片</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item v-if="pagesInfo.common.background_type == 1">
                        <color-select v-model="pagesInfo.common.background_color" reset-color="#F5F5F5" />
                    </el-form-item>
                    <el-form-item v-if="pagesInfo.common.background_type == 2">
                        <material-select v-model="pagesInfo.common.background_image" :limit="1" :size="60" />
                    </el-form-item>
                    <el-form-item label="文字颜色" v-if="configs.setBg">
                        <el-radio-group v-model="pagesInfo.common.title_color">
                            <el-radio label="1">白色</el-radio>
                            <el-radio label="2">黑色</el-radio>
                        </el-radio-group>
                    </el-form-item>
                </attribute-item>
            </el-form>
        </attribute-tabs>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import AttributeTabs from '@/components/decorate/attribute-tabs.vue'
import ColorSelect from '@/components/decorate/color-select.vue'
import MaterialSelect from '@/components/material-select/index.vue'
import AttributeItem from '@/components/decorate/attribute-item.vue'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        MaterialSelect,
        AttributeItem
    }
})
export default class SearchAttribute extends Vue {
    @Prop({
        default: () => ({})
    })
    config!: any
    /** S data **/
    defaultConfig = {
        setTitle: true,
        setBg: true
    }
    /** E data **/
    /** S computed **/

    get pagesInfo() {
        const { pagesInfo } = this.$store.state.decorate
        return pagesInfo
    }

    set pagesInfo(val) {
        this.$store.commit('setPagesInfo', val)
    }

    get configs() {
        //
        return Object.assign({}, this.defaultConfig, this.config)
    }

    /** E computed **/

    handleChange(val: string) {
        this.pagesInfo.common.title = val.trim()
    }
}
</script>
