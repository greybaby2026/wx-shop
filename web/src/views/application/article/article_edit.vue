<template>
    <div class="substance_edit-help">
        <div class="ls-card">
            <el-page-header :content="mode == 'add' ? '添加资讯' : '编辑资讯'" @back="$router.go(-1)" />
        </div>

        <div class="ls-card m-t-16 form-container">
            <div class="form-left">
                <el-form :rules="rules" ref="form" :model="form" label-width="120px" size="small">
                    <!-- 标题输入框 -->
                    <el-form-item label="文章标题" prop="title">
                        <el-input class="ls-input" v-model="form.title" show-word-limit placeholder="请输入文章标题">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="文章分类" prop="cid">
                        <el-select v-model="form.cid" placeholder="请选择">
                            <el-option v-for="item in categoryList" :key="item.id" :label="item.name" :value="item.id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="文章简介" prop="synopsis">
                        <el-input class="ls-input" v-model="form.synopsis" placeholder="请输入简介"></el-input>
                    </el-form-item>
                    <el-form-item label="文章封面" prop="image">
                        <material-select :limit="1" v-model="form.image" />
                        <div class="muted xs">建议尺寸：240*180像素</div>
                    </el-form-item>
                    <el-form-item label="排序" prop="sort">
                        <el-input
                            type="number"
                            class="ls-input"
                            v-model.number="form.sort"
                            placeholder="请输入排序值"
                        ></el-input>
                        <div class="muted xs">默认为0，数值越大越排在前面</div>
                    </el-form-item>
                    <!-- 帮助状态 -->
                    <el-form-item label="文章状态" prop="is_show">
                        <el-radio v-model="form.is_show" :label="1">显示</el-radio>
                        <el-radio v-model="form.is_show" :label="0">隐藏</el-radio>
                    </el-form-item>
                </el-form>
            </div>
            <div class="form-right">
                <!-- 富文本编辑器 -->
                <el-form :rules="rules" label-width="120px" size="small">
                    <el-form-item label="文章内容" class="form-edit" prop="content">
                        <ls-editor v-model="form.content" />
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <!-- 底部保存或取消 -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="onSubmit('form')">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiArticleCategoryLists, apiArticleAdd, apiArticleEdit, apiArticleDetail } from '@/api/application/article'
import MaterialSelect from '@/components/material-select/index.vue'
import LsEditor from '@/components/editor.vue'
import { PageMode } from '@/utils/type'

@Component({
    components: {
        LsEditor,
        MaterialSelect
    }
})
export default class ArticleEdit extends Vue {
    /** S Data **/
    mode: string = PageMode.ADD // 当前页面: add-添加角色 edit-编辑角色

    // 分类列表
    categoryList: Array<object> = []

    // 表单数据
    form: any = {
        id: 0, // 当前编辑用户的身份ID
        title: '', // 标题
        cid: '', // 分类id
        synopsis: '', // 简介
        image: '', // 封面图片
        content: '', // 内容
        sort: 0, // 排序值
        is_show: 1 // 显示: 0-隐藏 1-显示(默认)
    }

    // 表单校验
    rules: object = {
        title: [
            {
                required: true,
                message: '请输入文章标题',
                trigger: 'blur'
            }
        ],
        cid: [
            {
                required: true,
                message: '请选择文章分类',
                trigger: 'change'
            }
        ],
        // synopsis: [{
        // 	required: true,
        // 	message: '请输入文章简介',
        // 	trigger: 'blur'
        // }],
        // sort: [{
        // 	required: true,
        // 	message: '请输入排序',
        // 	trigger: 'blur'
        // }],
        content: [
            {
                required: true,
                message: '请输入文章标题',
                trigger: 'blur'
            }
        ]
    }

    /** E Data **/

    /** S Methods **/
    // 提交表单
    onSubmit(formName: string) {
        const refs = this.$refs[formName] as HTMLFormElement
        refs.validate((valid: boolean): void => {
            if (!valid) {
                return
            }

            // 请求发送
            switch (this.mode) {
                case PageMode.ADD:
                    return this.handleArticleAdd()
                case PageMode.EDIT:
                    return this.handleArticleEdit()
            }
        })
    }

    // 添加帮助文章
    handleArticleAdd() {
        apiArticleAdd(this.form).then(() => {
            setTimeout(() => this.$router.go(-1), 500)
        })
    }

    // 编辑帮助文章
    handleArticleEdit() {
        apiArticleEdit(this.form).then(() => {
            setTimeout(() => this.$router.go(-1), 500)
        })
    }

    // 初始化表单数据: 编辑
    initArticleEdit() {
        apiArticleDetail({
            id: this.form.id
        }).then(res => {
            Object.keys(res).map(item => {
                this.$set(this.form, item, res[item])
            })
        })
    }

    // 初始化文章分类数据
    initCategoryLists() {
        apiArticleCategoryLists({}).then(res => {
            this.categoryList = res.lists
        })
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        const query: any = this.$route.query
        if (query.mode) {
            this.mode = query.mode
        }

        this.initCategoryLists()

        if (this.mode === PageMode.EDIT) {
            this.form.id = query.id * 1
            this.initArticleEdit()
        }
    }

    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.form-container {
    display: flex;

    .form-left {
        width: 500px;
    }

    .form-right {
        width: 800px;
    }
}
</style>
