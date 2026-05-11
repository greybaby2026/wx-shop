<template>
    <div class="express-edit flex-col">
        <div class="ls-card express-edit__header">
            <el-page-header @back="$router.go(-1)" :content="id ? '编辑快递公司' : '新增快递公司'"></el-page-header>
        </div>
        <div class="ls-card express-edit__form m-t-10" v-loading="loading">
            <el-form class="ls-form" ref="form" :model="form" label-width="120px" size="small">
                <el-form-item
                    label="快递名称"
                    required
                    prop="name"
                    :rules="[
                        {
                            required: true,
                            message: '请输入快递名称',
                            trigger: ['blur', 'change']
                        }
                    ]"
                >
                    <el-input v-model="form.name" placeholder="请输入快递名称"></el-input>
                </el-form-item>
                <el-form-item label="快递图标">
                    <material-select v-model="form.icon" :limit="1" />
                    <div class="muted">快递图标，建议宽高尺寸200px*200px。</div>
                </el-form-item>
                <el-form-item label="快递编码">
                    <el-input v-model="form.code" placeholder=""></el-input>
                    <div class="muted">快递公司编码。</div>
                </el-form-item>
                <el-form-item label="快递100编码">
                    <el-input v-model="form.code100" placeholder=""></el-input>
                    <div class="muted">快递公司在快递100平台的编码，用于快递查询跟踪</div>
                </el-form-item>
                <el-form-item label="快递鸟编码">
                    <el-input v-model="form.codebird" placeholder=""></el-input>
                    <div class="muted">快递公司在快递鸟平台的编码，用于快递查询跟踪</div>
                </el-form-item>
                <el-form-item label="排序">
                    <el-input v-model="form.sort" placeholder=""></el-input>
                </el-form-item>
            </el-form>
        </div>
        <div class="express-edit__footer bg-white ls-fixed-footer">
            <div class="btns row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="handleSave">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import MaterialSelect from '@/components/material-select/index.vue'
import { apiExpressAdd, apiExpressDetail, apiExpressEdit } from '@/api/setting/delivery'
@Component({
    components: {
        MaterialSelect
    }
})
export default class ExpressEdit extends Vue {
    $refs!: { form: any }
    id!: any
    form = {
        name: '',
        icon: '',
        code: '',
        code100: '',
        codebird: '',
        sort: ''
    }
    loading = false
    handleSave() {
        this.$refs.form.validate((valid: boolean) => {
            if (valid) {
                const api = this.id ? apiExpressEdit(this.form) : apiExpressAdd(this.form)
                api.then(() => {
                    this.$router.go(-1)
                })
            } else {
                return false
            }
        })
    }

    getExpressDetail() {
        this.loading = true
        apiExpressDetail({ id: this.id }).then((res: any) => {
            this.loading = false
            this.form = res
        })
    }

    created() {
        this.id = this.$route.query.id
        this.id && this.getExpressDetail()
    }
}
</script>
<style lang="scss" scoped>
.express-edit {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;
    &__header {
        flex: none;
    }
}
</style>
