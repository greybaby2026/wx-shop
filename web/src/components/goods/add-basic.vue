<template>
    <div class="add-basic">
        <el-form-item label="商品类型" required prop="type">
            <div class="flex goods-type">
                <div
                    v-for="(item, index) in goodsType"
                    :key="index"
                    :class="{
                        active: item.type == value.type,
                        disabled: value.id
                    }"
                    @click="!value.id && (value.type = item.type)"
                >
                    <div>{{ item.label }}</div>
                    <div class="muted m-t-4">{{ item.desc }}</div>
                </div>
            </div>
        </el-form-item>
        <el-form-item label="商品编码" required prop="code">
            <el-input style="width: 460px" v-model="value.code" placeholder="请输入商品编码"></el-input>
        </el-form-item>
        <el-form-item label="商品名称" required prop="name">
            <el-input
                v-model="value.name"
                type="textarea"
                style="width: 460px"
                maxlength="100"
                rows="3"
                show-word-limit
                placeholder="请输入商品名称"
            ></el-input>
        </el-form-item>

        <el-form-item label="商品分类" required prop="category_id">
            <el-cascader
                v-model="value.category_id"
                style="width: 280px"
                :options="categoryList"
                :props="{
                    multiple: true,
                    checkStrictly: true,
                    label: 'name',
                    value: 'id',
                    children: 'sons',
                    emitPath: false
                }"
                clearable
                filterable
            ></el-cascader>
            <router-link target="_blank" to="/goods/category_edit" class="m-l-10">
                <el-button type="text" size="small">新增分类</el-button>
            </router-link>
            <span class="primary m-8">|</span>
            <el-button type="text" size="small" @click="$emit('refresh')">刷新</el-button>
        </el-form-item>
        <el-form-item label="商品轮播图" required prop="goods_image">
            <material-select v-model="value.goods_image" :limit="10" />
            <div class="muted">建议尺寸：800*800，可拖拽改变图片顺序，默认首张图为主图，最多上传10张</div>
        </el-form-item>
        <el-form-item label="添加视频">
            <el-switch v-model="addVideo"></el-switch>
        </el-form-item>
        <template v-if="addVideo">
            <el-form-item label="视频来源">
                <el-radio-group v-model="value.video_source">
                    <el-radio :label="1">视频素材库</el-radio>
                    <el-radio :label="2">视频链接</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="选择视频" required v-if="value.video_source == 1" prop="video">
                <material-select v-model="value.video" type="video"></material-select>
                <div class="muted">手机端播放，建议时长：9-30秒，视频宽高比16:9</div>
            </el-form-item>
            <el-form-item label="视频链接" required v-else prop="video">
                <el-input style="width: 460px" v-model="value.video" placeholder="请输入视频链接"></el-input>
                <div class="muted">手机端播放，建议时长：9-30秒，视频宽高比16:9</div>
            </el-form-item>
            <el-form-item label="视频封面">
                <material-select v-model="value.video_cover" />
                <div class="muted">建议尺寸：800*800</div>
            </el-form-item>
        </template>
        <el-form-item label="商品品牌">
            <el-select v-model="value.brand_id" placeholder="请选择品牌">
                <el-option v-for="item in brandList" :key="item.id" :label="item.name" :value="item.id"></el-option>
            </el-select>
            <router-link target="_blank" to="/goods/brand_edit" class="m-l-10">
                <el-button type="text" size="small">新增品牌</el-button>
            </router-link>
            <span class="primary m-8">|</span>
            <el-button type="text" size="small" @click="$emit('refresh')">刷新</el-button>
        </el-form-item>
        <el-form-item label="商品单位">
            <el-select v-model="value.unit_id" placeholder="请选择单位">
                <el-option v-for="item in unitList" :key="item.id" :label="item.name" :value="item.id"></el-option>
            </el-select>
            <router-link target="_blank" to="/goods/unit" class="m-l-10">
                <el-button type="text" size="small">新增单位</el-button>
            </router-link>
            <span class="primary m-8">|</span>
            <el-button type="text" size="small" @click="$emit('refresh')">刷新</el-button>
        </el-form-item>
        <el-form-item label="供应商">
            <el-select v-model="value.supplier_id" placeholder="请选择供应商">
                <el-option v-for="item in supplierList" :key="item.id" :label="item.name" :value="item.id"></el-option>
            </el-select>
            <router-link target="_blank" to="/goods/supplier/edit" class="m-l-10">
                <el-button type="text" size="small">新增供应商</el-button>
            </router-link>
            <span class="primary m-8">|</span>
            <el-button type="text" size="small" @click="$emit('refresh')">刷新</el-button>
        </el-form-item>
        <el-form-item label="自定义分享海报">
            <material-select v-model="value.poster" :limit="1" />
            <div class="muted">建议尺寸：750*1280</div>
        </el-form-item>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import MaterialSelect from '@/components/material-select/index.vue'
@Component({
    components: {
        MaterialSelect
    }
})
export default class AddBasic extends Vue {
    @Prop() value: any
    @Prop({ default: () => ({}) }) lists: any
    addVideo = false
    form = {}
    fileList = []
    goodsType = [
        {
            type: 1,
            label: '实物商品',
            desc: '物流发货'
        },
        {
            type: 2,
            label: '虚拟商品',
            desc: '虚拟发货'
        }
    ]
    @Watch('value.video', { immediate: true })
    videoChange(val: string) {
        if (val) {
            this.addVideo = true
        }
    }
    get categoryList() {
        return this.lists.category_list || []
    }

    get brandList() {
        return this.lists.brand_list || []
    }

    get unitList() {
        return this.lists.unit_list || []
    }

    get supplierList() {
        return this.lists.supplier_list || []
    }

    created() {}
}
</script>

<style scoped lang="scss">
.add-basic {
    .goods-type {
        & > div {
            cursor: pointer;
            line-height: 1.3;
            border: $--border-base;
            padding: 10px 20px;
            margin-right: 20px;
            &.active {
                color: $--color-primary;
                border-color: currentColor;
            }
            &.disabled {
                cursor: not-allowed;
            }
        }
    }
}
</style>
