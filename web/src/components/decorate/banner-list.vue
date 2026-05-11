<template>
    <div class="banner">
        <div>
            <div class="banner-list">
                <draggable v-model="banner" animation="300">
                    <div class="banner-item ls-del-wrap m-b-20" v-for="(item, index) in banner" :key="index">
                        <material-select v-model="item.url" :enable-domain="false" :enable-delete="false">
                            <template v-slot:preview="{ item }">
                                <div class="banner-iamge muted">
                                    <el-image fit="cover" :src="item"></el-image>
                                </div>
                            </template>
                            <div slot="upload" class="banner-iamge muted">
                                <div class="add-image flex-col row-center col-center">
                                    <i class="el-icon-plus font-size-30"></i>
                                    添加图片
                                </div>
                            </div>
                        </material-select>

                        <div class="flex m-t-10">
                            <div class="m-r-10 lighter">链接地址</div>
                            <link-select :client="client" v-model="item.link" />
                        </div>
                        <i v-if="!closeDelete" @click="handleDelete(index)" class="el-icon-close ls-icon-del"></i>
                    </div>
                </draggable>
            </div>
            <el-button size="small" v-if="banner.length < limit" class="add-banner" @click="handleAdd"
                >+ 添加图片</el-button
            >
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import LinkSelect from '@/components/link-select/index.vue'
import MaterialSelect from '@/components/material-select/index.vue'
import Draggable from 'vuedraggable'
@Component({
    components: {
        MaterialSelect,
        LinkSelect,
        Draggable
    }
})
export default class StyleChose extends Vue {
    /** S props **/

    @Prop() value!: any[]

    @Prop({ default: 9999 }) limit!: number

    @Prop() client!: string
    @Prop({ default: false }) closeDelete!: boolean

    /** E props **/

    /** S data **/
    $refs!: { materialSelect: any }
    index = -1

    /** E data **/
    get banner() {
        return this.value
    }

    set banner(val) {
        this.$emit('input', val)
    }

    /** S methods **/
    handleAdd() {
        if (this.banner.length < this.limit) {
            this.banner.push({
                url: '',
                link: {}
            })
        } else {
            this.$message.error(`最多添加${this.limit}张图片`)
        }
    }
    handleDelete(index: number) {
        if (this.banner.length <= 1) {
            return this.$message.error('最少保留一张图片')
        }
        this.banner.splice(index, 1)
    }

    /** E methods **/
}
</script>

<style lang="scss" scoped>
.banner-list {
    .banner-item {
        background: #f9f9f9;
        padding: 10px 30px;
        cursor: move;
        .banner-iamge {
            width: 300px;
            height: 136px;
            .el-image,
            .add-image {
                cursor: pointer;
                width: 100%;
                height: 100%;
                border-radius: 4px;
                background: #fff;
                border: 1px dashed $--border-base;
            }
        }
    }
}
.add-banner {
    width: 100%;
    margin-bottom: 20px;
}
</style>
