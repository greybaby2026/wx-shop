<template>
    <div class="ls-brand-edit">
        <div class="ls-card ls-brand-edit__header">
            <el-page-header @back="$router.go(-1)" :content="id ? '编辑品牌' : '新增品牌'"></el-page-header>
        </div>

        <div class="ls-card ls-brand-edit__form m-t-10" v-loading="loading">
            <el-form ref="form" :model="form" label-width="120px" size="small" :rules="rules">
                <el-form-item label="品牌名称" required prop="name">
                    <el-input
                        class="ls-input"
                        v-model="form.name"
                        maxlength="8"
                        show-word-limit
                        placeholder="请输入品牌名称"
                    ></el-input>
                </el-form-item>
                <el-form-item label="品牌图标" required prop="image">
                    <material-select v-model="form.image" :limit="1" />
                    <div class="xs muted">建议尺寸：宽80像素*高80像素的jpg，jpeg，png，gif图片</div>
                </el-form-item>
                <el-form-item label="排序">
                    <el-input style="width: 220px" v-model="form.sort" placeholder=""></el-input>
                </el-form-item>
                <el-form-item label="是否显示" required>
                    <el-switch v-model="form.is_show" :active-value="1" :inactive-value="0"></el-switch>
                </el-form-item>
            </el-form>
        </div>
        <div class="ls-brand-edit__footer bg-white ls-fixed-footer">
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
import { apiBrandAdd, apiBrandDetail, apiBrandEdit } from '@/api/goods'
@Component({
    components: {
        MaterialSelect
    }
})
export default class AddBrand extends Vue {
    $refs!: { form: any }
    id!: any
    loading = false
    form = {
        name: '',
        image: '',
        sort: '',
        is_show: 1
    }

    rules = {
        name: [
            {
                required: true,
                message: '请输入品牌名称',
                trigger: ['blur', 'change']
            }
        ],
        image: [
            {
                required: true,
                message: '请添加品牌图标',
                trigger: ['blur', 'change']
            }
        ]
    }

    handleSave() {
        this.$refs.form.validate((valid: boolean, object: any) => {
            if (valid) {
                const ref = /[^0-9]/g
                if (ref.test(this.form.sort)) {
                    this.$message.error('排序必须是数字')
                    return
                }
                const api = this.id ? apiBrandEdit(this.form) : apiBrandAdd(this.form)
                api.then(() => {
                    this.$router.go(-1)
                })
            } else {
                return false
            }
        })
    }

    getBrandDetail() {
        this.loading = true
        apiBrandDetail(this.id).then((res: any) => {
            this.form = res
            this.loading = false
        })
    }

    created() {
        this.id = this.$route.query.id
        this.id && this.getBrandDetail()
    }
}
</script>

<style lang="scss" scoped>
.ls-brand-edit {
    padding-bottom: 80px;
    .ls-input {
        width: 380px;
    }
}
</style>
