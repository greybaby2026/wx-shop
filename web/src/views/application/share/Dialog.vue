<template>
    <ls-dialog
        title="分享设置"
        class="m-l-10 inline"
        width="800px"
        top="20vh"
        :rules="rules"
        ref="Dialog"
        :async="true"
        @confirm="handleConfirm"
    >
        <el-form ref="form" size="mini" label-width="120px" :model="formData">
            <el-form-item label="变量替换">
                <el-button type="primary" @click="handleName">{{ varTitle.name }}</el-button>
                <el-button type="primary" @click="handlePrice" v-if="formData.id == 4">价格</el-button>
            </el-form-item>
            <el-form-item label="分享标题">
                <el-input v-model="formData.title" @focus="handleSelected('title')"></el-input>
            </el-form-item>
            <el-form-item label="分享简介" v-if="formData.id != 1 && formData.id != 2">
                <el-input
                    v-model="formData.synopsis"
                    type="textarea"
                    :rows="3"
                    style="width: 300px"
                    @focus="handleSelected('synopsis')"
                ></el-input>
            </el-form-item>
            <el-form-item label="分享图片" prop="image" required>
                <material-select :limit="1" v-model="formData.image" />
                <div class="flex">
                    <div class="muted xs m-r-16">建议尺寸：500*400</div>
                </div>
            </el-form-item>
        </el-form>
    </ls-dialog>
</template>
<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import { apishareDetial, apishareEdit } from '@/api/application/share'
import MaterialSelect from '@/components/material-select/index.vue'
import title from '@/components/decorate/widgets/title'
import { Message } from 'element-ui'

@Component({
    components: {
        LsDialog,
        MaterialSelect
    }
})
export default class LsWithdrawalDetails extends Vue {
    $refs!: {
        Dialog: any
    }
    type = ''
    varName = ''
    formData: any = {
        id: '',
        title: '',
        synopsis: '',
        image: ''
    }
    @Prop() value: any
    // 表单检验
    rules: object = {
        image: [
            {
                required: true,
                message: '必填项不能为空',
                trigger: ['blur', 'change']
            }
        ]
    }

    openDialog(val: any) {
        this.formData.id = val
        this.$refs.Dialog.open()
        this.getData()
    }
    getData() {
        apishareDetial({ id: this.formData.id }).then(res => {
            this.formData = res
        })
    }
    async handleConfirm() {
        if (this.formData.image == '') {
            return Message({ type: 'error', message: '请选择分享图片' })
        }
        await apishareEdit(this.formData)
        this.$refs.Dialog.close()
        this.$emit('reflsh')
    }
    handlePrice() {
        if (!this.formData.title) {
            this.formData.title = ''
        }
        if (!this.formData.synopsis) {
            this.formData.synopsis = ''
        }
        if (this.type == 'title') {
            this.formData.title += '{price}'
        } else {
            this.formData.synopsis += '{price}'
        }
    }
    handleName() {
        if (!this.formData.title) {
            this.formData.title = ''
        }
        if (!this.formData.synopsis) {
            this.formData.synopsis = ''
        }
        if (this.type == 'title') {
            this.formData.title += this.varTitle.content
        } else {
            this.formData.synopsis += this.varTitle.content
        }
    }
    handleSelected(type: any) {
        this.type = type
    }
    get varTitle(): any {
        switch (+this.formData.id) {
            case 1:
                return { name: '店铺名称', content: '{name}' }
            case 2:
                return { name: '用户昵称', content: '{nickname}' }
            case 3:
                return { name: '店铺名称', content: '{name}' }
            case 4:
                return { name: '商品昵称', content: '{goodname}' }
            case 5:
                return { name: '用户昵称', content: '{nickname}' }
            case 6:
                return { name: '用户昵称', content: '{nickname}' }
            default:
                return ''
        }
    }
}
</script>
