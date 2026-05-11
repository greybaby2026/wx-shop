<template>
    <div class="nav-content">
        <div class="nav-list">
            <draggable v-model="nav" animation="300">
                <div class="nav-item ls-del-wrap" v-for="(item, index) in nav" :key="index">
                    <div class="flex">
                        <material-select
                            v-if="hasImg"
                            class="m-r-10"
                            ref="materialSelect"
                            v-model="item.url"
                            :size="60"
                            upload-bg="#fff"
                            :enable-domain="false"
                        >
                            <i class="el-icon-plus lg"></i>
                        </material-select>
                        <div>
                            <el-form-item label="标题" label-width="40px">
                                <el-input
                                    style="width: 200px"
                                    v-model="item.name"
                                    maxlength="6"
                                    show-word-limit
                                    placeholder="请输入标题"
                                ></el-input>
                            </el-form-item>
                            <el-form-item label="链接" label-width="40px">
                                <link-select :client="client" v-model="item.link" />
                            </el-form-item>
                        </div>
                    </div>
                    <i @click="onDelete(index)" class="el-icon-close ls-icon-del"></i>
                </div>
            </draggable>
        </div>
        <el-form-item label-width="0" v-if="this.limit == -1 || (this.limit != -1 && this.nav.length < this.limit)">
            <el-button size="small" class="add-nav" @click="onAdd">+ 添加导航</el-button>
        </el-form-item>
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
export default class NavLists extends Vue {
    /** S props **/

    @Prop() value!: any[]
    @Prop({ default: -1 }) limit!: number
    @Prop() client!: string
    @Prop({ default: true }) hasImg!: boolean

    /** E props **/

    /** S data **/

    /** E data **/
    get nav() {
        return this.value
    }

    set nav(val) {
        this.$emit('input', val)
    }

    /** S methods **/
    onAdd() {
        if (this.limit != -1 && this.nav.length >= this.limit) {
            return this.$message.error(`最多添加${this.limit}个`)
        }
        this.nav.push({
            url: '',
            name: '导航',
            link: {}
        })
    }
    onDelete(index: number) {
        this.nav.splice(index, 1)
    }
    /** E methods **/
}
</script>

<style lang="scss" scoped>
.nav-list {
    .nav-item {
        background: #f9f9f9;
        padding: 15px 20px 0;
        cursor: move;
        margin-bottom: 20px;
    }
}
.add-nav {
    width: 100%;
}
</style>
