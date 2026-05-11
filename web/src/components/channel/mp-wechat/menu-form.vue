<template>
    <el-form :inline="true" label-position="top" :model="form" :rules="rules" ref="form">
        <el-form-item label="菜单名称" prop="name" v-if="mode !== 'index'">
            <el-input v-model="form.name"></el-input>
        </el-form-item>

        <el-form-item :label="mode !== 'index' ? '菜单类型' : ''" prop="type">
            <el-select v-model="form.type" placeholder="请选择">
                <el-option label="网页" value="view"></el-option>
                <el-option label="小程序" value="miniprogram"></el-option>
            </el-select>
        </el-form-item>

        <div v-if="form.type === 'view'">
            <el-form-item label="网址" prop="url" required>
                <el-input v-model="form.url"></el-input>
            </el-form-item>
        </div>

        <div v-if="form.type === 'miniprogram'">
            <el-form-item label="网址" prop="url" required>
                <el-input v-model="form.url"></el-input>
            </el-form-item>
            <el-form-item label="AppID" prop="appid" required>
                <el-input v-model="form.appid"></el-input>
            </el-form-item>
            <el-form-item label="路径" prop="pagepath" required>
                <el-input v-model="form.pagepath"></el-input>
            </el-form-item>
        </div>
    </el-form>
</template>

<script lang="ts">
import { Vue, Component, Prop, Watch } from 'vue-property-decorator'

@Component
export default class MPWechatMenuForm extends Vue {
    /** S Props **/
    @Prop({ default: 'normal' }) mode?: 'index' | 'normal'
    @Prop() name?: string
    @Prop() type?: string
    @Prop() url?: string
    @Prop() appid?: string
    @Prop() pagepath?: string
    /** E Props **/

    /** S Data **/
    // 表单数据
    form: any = {
        name: '',
        type: '',
        url: '',
        appid: '',
        pagepath: ''
    }

    // 表单检验
    rules: object = {
        name: [
            {
                required: true,
                message: '必填项不能为空',
                trigger: ['blur', 'change']
            },
            {
                min: 1,
                max: 18,
                message: '长度限制18个字符',
                trigger: ['blur', 'change']
            }
        ],
        type: [
            {
                required: true,
                message: '必填项不能为空',
                trigger: ['blur', 'change']
            }
        ],
        url: [
            {
                required: true,
                message: '必填项不能为空',
                trigger: ['blur', 'change']
            },
            {
                pattern: /[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?/,
                message: '请输入合法链接',
                trigger: ['blur', 'change']
            }
        ],
        appid: [
            {
                validator: (rule: object, value: string, callback: Function) => {
                    if (value || this.form.type !== 'miniprogram') {
                        callback()
                    } else {
                        callback(new Error())
                    }
                },
                message: '必填项不能为空',
                trigger: ['blur', 'change']
            }
        ],
        pagepath: [
            {
                validator: (rule: object, value: string, callback: Function) => {
                    if (value || this.form.type !== 'miniprogram') {
                        callback()
                    } else {
                        callback(new Error())
                    }
                },
                message: '必填项不能为空',
                trigger: ['blur', 'change']
            }
        ]
    }

    /** E Data **/

    /** S Methods **/
    validateForm(): any {
        const refs = this.$refs.form as HTMLFormElement
        let result = false
        refs.validate((valid: boolean) => (result = valid))
        return { valid: result, data: { ...this.form } }
    }

    // 清除表单数据
    clearFrom(): void {
        Object.keys(this.form).forEach((key: string) => {
            this.$set(this.form, key, '')
        })
    }

    resetFrom(): void {
        const refs = this.$refs.form as HTMLFormElement
        refs.resetFields()
    }
    /** E Methods **/

    @Watch('form', { deep: true })
    handler(valueObj: any) {
        this.$emit('update:name', valueObj.name)
        this.$emit('update:type', valueObj.type)
        this.$emit('update:url', valueObj.url)
        this.$emit('update:appid', valueObj.appid)
        this.$emit('update:pagepath', valueObj.pagepath)
    }
    @Watch('name', { immediate: true })
    handlerName(value: string) {
        this.$set(this.form, 'name', value)
    }
    @Watch('type', { immediate: true })
    handlerType(value: string) {
        this.$set(this.form, 'type', value)
    }
    @Watch('url', { immediate: true })
    handlerUrl(value: string) {
        this.$set(this.form, 'url', value)
    }
    @Watch('appid', { immediate: true })
    handlerAppid(value: string) {
        this.$set(this.form, 'appid', value)
    }
    @Watch('pagepath', { immediate: true })
    handlerPagePath(value: string) {
        this.$set(this.form, 'pagepath', value)
    }
}
</script>
