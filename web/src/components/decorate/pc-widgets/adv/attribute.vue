<template>
    <div>
        <attribute-tabs title="广告位">
            <div>
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="展示样式">
                        <style-chose v-model="content.style" :data="styleData" />
                    </attribute-item>
                    <attribute-item title="图片设置" :desc="`建议图片尺寸：${imageCongig.width}*180px`">
                        <el-form-item label-width="0">
                            <banner-list
                                v-model="content.data"
                                :close-delete="true"
                                client="pc"
                                :limit="imageCongig.limit"
                            />
                        </el-form-item>
                    </attribute-item>
                </el-form>
            </div>
        </attribute-tabs>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import AttributeTabs from '@/components/decorate/attribute-tabs.vue'
import ColorSelect from '@/components/decorate/color-select.vue'
import StyleChose from '@/components/decorate/style-chose.vue'
import Slider from '@/components/decorate/slider.vue'
import AttributeItem from '@/components/decorate/attribute-item.vue'
import BannerList from '@/components/decorate/banner-list.vue'

@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem,
        BannerList
    }
})
export default class Attribute extends Vue {
    /** S data **/
    imageCongig: any = {}
    styleData = [
        {
            name: '单张图片',
            value: 1,
            num: 1
        },
        {
            name: '双列图片',
            value: 2,
            num: 2
        },
        {
            name: '三列图片',
            value: 3,
            num: 3
        },
        {
            name: '四列图片',
            value: 4,
            num: 4
        }
    ]
    /** E data **/

    @Watch('content.style', { immediate: true })
    styleChange(val: number) {
        switch (val) {
            case 1:
                this.imageCongig = {
                    width: '1180px'
                }
                break
            case 2:
                this.imageCongig = {
                    width: '585px'
                }
                break
            case 3:
                this.imageCongig = {
                    width: '386px'
                }
                break
            case 4:
                this.imageCongig = {
                    width: '287px'
                }
                break
            default:
                this.imageCongig = {
                    width: '1180px'
                }
        }
        const num = this.styleData.find(item => item.value == val)?.num || 1
        this.imageCongig.limit = num
        this.content.data = this.handleArray(num)
    }

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
    handleArray(num: number) {
        const data = JSON.parse(JSON.stringify(this.content.data))
        const array = []
        for (let i = 0; i < num; i++) {
            if (data[i]) {
                array.push(data[i])
            } else {
                array.push({
                    url: '',
                    link: {}
                })
            }
        }
        return array
    }
    /** E methods **/
}
</script>

<style lang="scss" scoped></style>
