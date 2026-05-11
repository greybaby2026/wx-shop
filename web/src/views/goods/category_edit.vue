<template>
    <div class="ls-category-edit">
        <div class="ls-card category-edit__header">
            <el-page-header @back="$router.go(-1)" :content="id ? '编辑分类' : '新增分类'"></el-page-header>
        </div>
        <el-form ref="form" :model="form" label-width="120px" size="small" :rules="rules">
            <div class="ls-card category-edit__base m-t-16" v-loading="loading">
                <div class="nr weight-500 m-b-20">基础信息</div>
                <el-form-item label="分类名称" prop="name" required>
                    <el-input
                        class="ls-input"
                        v-model="form.name"
                        maxlength="8"
                        show-word-limit
                        placeholder="请输入分类名称"
                    ></el-input>
                </el-form-item>
                <el-form-item label="父级分类" required>
                    <el-radio-group v-model="hasPid" class="m-r-16">
                        <el-radio :label="0">无父级分类</el-radio>
                        <el-radio :label="1">有父级分类</el-radio>
                    </el-radio-group>
                    <el-alert
                        class="ls-tips"
                        title="选择无上级分类，则表明此分类为一级分类；选择有上级分类，则表明此分类为选中分类的子分类"
                        type="warning"
                        :closable="false"
                    >
                    </el-alert>
                </el-form-item>
                <el-form-item label="" required v-if="hasPid == 1">
                    <el-cascader
                        v-model="form.pid"
                        :options="categoryList"
                        :props="{
                            checkStrictly: true,
                            value: 'id',
                            label: 'name',
                            children: 'sons',
                            emitPath: false
                        }"
                        clearable
                    >
                        <template slot-scope="{ node, data }" v-if="data.level <= 2">
                            <span>{{ data.name }}</span>
                            <span v-if="!node.isLeaf"> ({{ data.sons.length }}) </span>
                        </template>
                    </el-cascader>
                </el-form-item>
                <el-form-item label="分类图标" prop="image">
                    <material-select :limit="1" v-model="form.image" />
                    <div class="muted xs">建议尺寸：宽200像素*高200像素的jpg，jpeg，png图片</div>
                </el-form-item>
                <el-form-item label="排序">
                    <el-input style="width: 220px" v-model="form.sort"></el-input>
                    <div class="muted xs">排序值必须为整数；数值越小，越靠前</div>
                </el-form-item>
                <el-form-item label="是否显示">
                    <el-switch v-model="form.is_show" :active-value="1" :inactive-value="0"></el-switch>
                </el-form-item>
            </div>
            <!-- <div class="ls-card category-edit__pc m-t-16" v-if="hasPid == 0">
                <div class="nr weight-500 m-b-20">PC商城</div>
                <el-form-item
                    label="首页推荐"
                    required
                >
                    <el-radio-group
                        v-model="form.is_recommend"
                        class="m-r-16"
                    >
                        <el-radio :label="1">推荐</el-radio>
                        <el-radio :label="0">不推荐</el-radio>
                    </el-radio-group>
                    <div class="muted xs">商品分类是否推荐在PC商城首页楼层显示</div>
                </el-form-item>
            </div> -->
        </el-form>
        <div class="category-edit__footer bg-white ls-fixed-footer">
            <div class="btns row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="handleSave">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import MaterialSelect from '@/components/material-select/index.vue'
import { apiCategoryAdd, apiCategoryDetail, apiCategoryEdit, apiCategoryLists } from '@/api/goods'
@Component({
    components: {
        MaterialSelect
    }
})
export default class AddCategory extends Vue {
    $refs!: { form: any }
    id!: any
    loading = false
    hasPid = 0
    categoryList = []
    form = {
        name: '',
        pid: '',
        image: '',
        sort: '',
        is_show: 1,
        is_recommend: 0
    }

    rules = {
        name: [
            {
                required: true,
                message: '请输入分类名称',
                trigger: ['blur', 'change']
            }
        ]
    }

    handleSave() {
        this.$refs.form.validate((valid: boolean) => {
            if (valid) {
                if (!this.hasPid) {
                    this.form.pid = ''
                }
                const api = this.id ? apiCategoryEdit(this.form) : apiCategoryAdd(this.form)
                api.then(() => {
                    this.$router.go(-1)
                })
            } else {
                return false
            }
        })
    }

    getCategoryDetail() {
        this.loading = true
        apiCategoryDetail(this.id).then((res: any) => {
            if (res.pid) {
                this.hasPid = 1
            }
            this.form = res
            this.loading = false
        })
    }

    getCategoryList() {
        apiCategoryLists({ page_type: 1 }).then((res: any) => {
            res?.lists.forEach((item: any) => {
                item.sons &&
                    item.sons.forEach((sitem: any) => {
                        delete sitem.sons
                    })
            })
            this.categoryList = res?.lists
        })
    }

    created() {
        this.id = this.$route.query.id
        this.id && this.getCategoryDetail()
        this.getCategoryList()
    }
}
</script>

<style lang="scss" scoped>
.ls-category-edit {
    padding-bottom: 80px;
    .ls-input {
        width: 380px;
    }
    .ls-tips {
        line-height: 1;
        display: inline-flex;
        width: auto;
        padding: 8px;
        ::v-deep .el-alert__content {
            padding-right: 40px;
        }
    }
}
</style>
