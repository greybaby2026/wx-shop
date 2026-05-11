<!-- 新增/编辑用户标签-->
<template>
    <div class="user-tag-edit">
        <!-- 导航头部 -->
        <div class="ls-card">
            <el-page-header
                v-if="form.reply_type == 1"
                @back="$router.go(-1)"
                :content="mode === 'add' ? '新增关注回复' : '编辑关注回复'"
            />
            <el-page-header
                v-if="form.reply_type == 2"
                @back="$router.go(-1)"
                :content="mode === 'add' ? '新增关键字回复' : '编辑关键字回复'"
            />
            <el-page-header
                v-if="form.reply_type == 3"
                @back="$router.go(-1)"
                :content="mode === 'add' ? '新增默认回复' : '编辑默认回复'"
            />
        </div>

        <!-- 主要内容 -->
        <el-form :rules="formRules" ref="formRef" :model="form" label-width="120px" size="small">
            <div class="ls-card m-t-16">
                <div class="card-title">关注回复</div>
                <div class="card-content m-t-24">
                    <el-form-item label="规则名称" prop="name">
                        <el-input v-model="form.name" placeholder="请输入规则名称"></el-input>
                        <div class="muted xs">方便通过名称管理关注回复内容</div>
                    </el-form-item>
                    <el-form-item v-if="form.reply_type == 2" label="关键词" prop="keyword">
                        <el-input v-model="form.keyword" placeholder="请输入关键词"></el-input>
                    </el-form-item>
                    <el-form-item v-if="form.reply_type == 2" label="排序值" prop="sort">
                        <el-input v-model.number="form.sort" placeholder="请输入排序值"></el-input>
                        <div class="muted xs">关键词排序值</div>
                    </el-form-item>
                    <el-form-item v-if="form.reply_type == 2" label="匹配方式" prop="matching_type">
                        <el-select class="ls-select" v-model="form.matching_type" placeholder="请选择匹配方式">
                            <el-option label="全匹配" :value="1"></el-option>
                            <el-option label="模糊匹配" :value="2"></el-option>
                        </el-select>
                        <div class="muted xs">模糊匹配时，关键词部分匹配用户输入的内容即可</div>
                    </el-form-item>
                    <el-form-item label="内容类型" prop="content_type">
                        <el-select class="ls-select" v-model="form.content_type" placeholder="请选择内容类型">
                            <el-option label="文本" :value="1"></el-option>
                        </el-select>
                        <div class="muted xs">暂时支持文本类型</div>
                    </el-form-item>
                    <el-form-item label="回复内容" prop="content">
                        <el-input
                            class="ls-input-textarea"
                            v-model="form.content"
                            placeholder="请输入回复内容"
                            type="textarea"
                            :rows="6"
                        >
                        </el-input>
                    </el-form-item>
                    <el-form-item v-if="form.reply_type == 2" label="回复数量" prop="reply_num" required>
                        <el-radio-group class="m-r-16" v-model="form.reply_num">
                            <el-radio class="m-r-16" :label="1">回复匹配首条</el-radio>
                        </el-radio-group>
                        <div class="muted xs m-r-16">设置关键词匹配多条时回复的数量，暂时支持回复一条内容</div>
                    </el-form-item>
                    <el-form-item label="启用状态">
                        <div class="flex">
                            <el-switch
                                v-model="form.status"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                            />
                            <span class="m-l-16">{{ form.status ? '开启' : '关闭' }}</span>
                        </div>
                    </el-form-item>
                </div>
            </div>
        </el-form>

        <!-- 底部保存或取消 -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="onSubmit()">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { PageMode } from '@/utils/type'
import { apiMpWeChatReplyAdd, apiMpWeChatReplyEdit, apiMpWeChatReplyDetail } from '@/api/channel/mp_wechat'
@Component({
    components: {}
})
export default class replyEdit extends Vue {
    /** S Data **/
    mode: string = PageMode.ADD // 当前页面【add: 添加用户等级 | edit: 编辑用户等级】
    //replyType = 0  // 回复类型 1-关注回复 2-关键词回复 3-默认回复
    identity: number | null = null // 当前编辑回复ID  valid: mode = 'edit'
    form = {
        reply_type: 1, // 回复类型 1-关注回复 2-关键词回复 3-默认回复
        name: '', // 规格名称
        content_type: 1, // 内容类型 1-文本
        content: '', // 内容
        status: 1, // 启用状态 0-禁用 1-开启
        keyword: '', // 关键词 *关键词回复必填
        matching_type: 1, // 匹配方式 1-全匹配 2-模糊匹配 *关键词回复必填
        sort: '', // 排序值 *关键词回复必填
        reply_num: 1 // 回复数量 *关键词回复必填 1-回复匹配首条
    }

    formRules = {
        name: [
            {
                required: true,
                message: '请输入规则名称',
                trigger: 'blur'
            }
        ],
        keyword: [
            {
                required: true,
                message: '请输入关键词',
                trigger: 'blur'
            }
        ],
        sort: [
            {
                required: true,
                message: '请输入排序值',
                trigger: 'blur'
            },
            {
                type: 'number',
                min: 1,
                message: '请输入大于0的数字值',
                trigger: 'blur'
            }
        ],
        matching_type: [
            {
                required: true,
                message: '请选择匹配方式',
                trigger: 'change'
            }
        ],
        content_type: [
            {
                required: true,
                message: '请选择内容类型',
                trigger: 'change'
            }
        ],
        content: [
            {
                required: true,
                message: '请输入回复内容',
                trigger: 'blur'
            }
        ]
    }
    $refs!: {
        formRef: any
    }
    /** E Data **/

    /** S Methods **/
    // 表单提交
    onSubmit() {
        // 验证表单格式是否正确
        this.$refs.formRef.validate((valid: boolean): any => {
            if (!valid) {
                return
            }

            // 请求发送
            switch (this.mode) {
                case PageMode.ADD:
                    return this.handleMpWeChatReplyAdd()
                case PageMode.EDIT:
                    return this.handleMpWeChatReplyEdit()
            }
        })
    }

    // 新增
    handleMpWeChatReplyAdd() {
        apiMpWeChatReplyAdd(this.form)
            .then(() => {
                //this.$message.success('添加成功!')
                setTimeout(() => this.$router.go(-1), 500)
            })
            .catch(() => {
                //this.$message.error('保存失败!')
            })
    }

    // 编辑
    handleMpWeChatReplyEdit() {
        const params = this.form
        const id: number = this.identity as number
        apiMpWeChatReplyEdit({
            ...params,
            id
        })
            .then(() => {
                //this.$message.success('保存成功!')
                setTimeout(() => this.$router.go(-1), 500)
                //this.initMpWeChatReplyDetail()
            })
            .catch(() => {
                // this.$message.error('保存失败!')
            })
    }
    // 表单初始化数据 [编辑模式] mode => edit
    initMpWeChatReplyDetail() {
        apiMpWeChatReplyDetail({
            id: this.identity as number
        })
            .then((res: any) => {
                Object.keys(res).map(key => {
                    this.$set(this.form, key, res[key])
                })
            })
            .catch(() => {
                // this.$message.error('数据初始化失败，请刷新重载！')
            })
    }
    /** E Methods **/

    /** S Life Cycle **/
    created() {
        const query: any = this.$route.query

        if (query.mode) {
            this.mode = query.mode
        }
        if (query.replyType) {
            this.form.reply_type = query.replyType * 1
        }
        // 编辑模式：初始化数据
        if (this.mode === PageMode.EDIT) {
            this.identity = query.id
            this.initMpWeChatReplyDetail()
        }
    }

    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.ls-card {
    .ls-input {
        width: 133px;
    }

    .ls-input-textarea {
        width: 300px;
    }

    .card-title {
        font-size: 14px;
        font-weight: 500;
    }
}

.user-tag-edit {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;

    &__header {
        flex: none;
    }
}
</style>
