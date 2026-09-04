<template>
    <div class="kefu-ai-bot">
        <div class="ls-card">
            <el-tabs v-model="activeTab" @tab-click="handleTabClick">
                <el-tab-pane label="AI配置" name="config">
                    <el-form ref="configFormRef" :model="configForm" label-width="160px" size="small" class="m-t-24">
                        <div class="nr weight-500 m-b-16">DeepSeek 配置</div>
                        <el-form-item label="API Key">
                            <el-input v-model="configForm.deepseek_api_key" placeholder="请输入DeepSeek API Key" show-password style="max-width: 500px" />
                        </el-form-item>
                        <el-form-item label="模型">
                            <el-select v-model="configForm.deepseek_model" placeholder="请选择模型" style="max-width: 500px">
                                <el-option label="deepseek-chat" value="deepseek-chat" />
                                <el-option label="deepseek-reasoner" value="deepseek-reasoner" />
                            </el-select>
                        </el-form-item>
                        <el-form-item label="温度参数">
                            <div style="max-width: 500px; width: 100%">
                                <el-slider v-model="configForm.deepseek_temperature" :min="0" :max="1" :step="0.1" show-input />
                            </div>
                        </el-form-item>
                        <el-form-item label="最大Token">
                            <el-input-number v-model="configForm.deepseek_max_tokens" :min="100" :max="4096" :step="100" style="max-width: 500px" />
                        </el-form-item>

                        <div class="nr weight-500 m-b-16 m-t-24">企业微信客服配置</div>
                        <el-form-item label="Corp ID">
                            <el-input v-model="configForm.wecom_corp_id" placeholder="请输入企业ID" style="max-width: 500px" />
                        </el-form-item>
                        <el-form-item label="客服 Secret">
                            <el-input v-model="configForm.wecom_kefu_secret" placeholder="请输入企微客服Secret" show-password style="max-width: 500px" />
                        </el-form-item>
                        <el-form-item label="回调 Token">
                            <el-input v-model="configForm.wecom_callback_token" placeholder="请输入企微回调Token" style="max-width: 500px" />
                        </el-form-item>
                        <el-form-item label="回调 AES Key">
                            <el-input v-model="configForm.wecom_encoding_aes_key" placeholder="请输入企微回调AES Key" style="max-width: 500px" />
                        </el-form-item>
                        <el-form-item label="客服 ID">
                            <el-input v-model="configForm.wecom_open_kfid" placeholder="请输入企微客服ID" style="max-width: 500px" />
                        </el-form-item>

                        <div class="nr weight-500 m-b-16 m-t-24">日报推送配置</div>
                        <el-form-item label="群机器人Webhook">
                            <el-input v-model="configForm.wecom_report_webhook" placeholder="如：https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=xxx" style="max-width: 500px" />
                            <div class="muted xxs m-t-4">在企微群中添加机器人，复制Webhook地址填入</div>
                        </el-form-item>
                    </el-form>
                    <div class="m-t-24">
                        <el-button type="primary" size="small" @click="onSaveConfig">保存配置</el-button>
                        <el-button size="small" @click="onSendReport" :loading="sendingReport">发送测试日报</el-button>
                    </div>
                </el-tab-pane>

                <el-tab-pane label="提示词管理" name="prompt">
                    <div class="m-t-24">
                        <el-input
                            v-model="promptForm.prompt"
                            type="textarea"
                            :rows="20"
                            placeholder="请输入系统提示词"
                            maxlength="5000"
                            show-word-limit
                        />
                        <div class="flex row-between m-t-16">
                            <span class="muted">当前提示词字数：{{ promptForm.prompt ? promptForm.prompt.length : 0 }}</span>
                            <el-button type="primary" size="small" @click="onSavePrompt">保存提示词</el-button>
                        </div>
                    </div>
                </el-tab-pane>

                <el-tab-pane label="知识库管理" name="knowledge">
                    <div class="m-t-24">
                        <el-alert title="知识库用于存放品牌事实信息（产品介绍、售后政策等），由人工维护，精而准。" type="info" show-icon :closable="false" class="m-b-16" />
                        <div class="flex m-b-16">
                            <el-button type="primary" size="small" @click="onAddKnowledge">添加知识</el-button>
                            <el-select v-model="knowledgeFilter.category" placeholder="分类筛选" clearable size="small" class="m-l-10" style="width: 140px" @change="getKnowledgeLists()">
                                <el-option v-for="item in categoryOptions" :key="item.value" :label="item.label" :value="item.value" />
                            </el-select>
                            <el-input v-model="knowledgeFilter.keyword" placeholder="搜索知识标题" clearable size="small" class="m-l-10" style="width: 200px" @clear="getKnowledgeLists()" @keyup.enter.native="getKnowledgeLists()">
                                <el-button slot="append" icon="el-icon-search" @click="getKnowledgeLists()"></el-button>
                            </el-input>
                        </div>
                        <el-table :data="pager.lists" v-loading="pager.loading" style="width: 100%" size="mini">
                            <el-table-column prop="id" label="ID" min-width="60" />
                            <el-table-column prop="title" label="标题" min-width="160" show-overflow-tooltip />
                            <el-table-column label="分类" min-width="80">
                                <template v-slot="scope">{{ getCategoryLabel(scope.row.category) }}</template>
                            </el-table-column>
                            <el-table-column label="状态" min-width="80">
                                <template v-slot="scope">
                                    <el-tag :type="scope.row.status === 1 ? 'success' : 'warning'" size="mini">{{ scope.row.status === 1 ? '启用' : '待审核' }}</el-tag>
                                </template>
                            </el-table-column>
                            <el-table-column prop="use_count" label="使用次数" min-width="80" />
                            <el-table-column prop="create_time" label="创建时间" min-width="140" />
                            <el-table-column label="操作" width="200" fixed="right">
                                <template v-slot="scope">
                                    <el-button type="text" size="small" @click="onEditKnowledge(scope.row)">编辑</el-button>
                                    <el-button v-if="scope.row.status === 0" type="text" size="small" @click="onApproveKnowledge(scope.row.id)">审核通过</el-button>
                                    <ls-dialog class="inline" @confirm="onDeleteKnowledge(scope.row.id)">
                                        <el-button type="text" size="small" slot="trigger">删除</el-button>
                                    </ls-dialog>
                                </template>
                            </el-table-column>
                        </el-table>
                        <div class="m-t-24 pagination">
                            <ls-pagination v-model="pager" @change="getKnowledgeLists()" />
                        </div>
                    </div>
                </el-tab-pane>

                <el-tab-pane label="对话经验" name="faq">
                    <div class="m-t-24">
                        <el-alert title="对话经验由人工客服回复自动积累，注入AI提示词作为Few-shot参考。30天无人再问会自动过期清理。" type="info" show-icon :closable="false" class="m-b-16" />
                        <div class="flex m-b-16">
                            <el-button type="primary" size="small" @click="onAddFaq">手动添加</el-button>
                            <el-select v-model="faqFilter.category" placeholder="分类筛选" clearable size="small" class="m-l-10" style="width: 140px" @change="getFaqLists()">
                                <el-option v-for="item in categoryOptions" :key="item.value" :label="item.label" :value="item.value" />
                            </el-select>
                            <el-input v-model="faqFilter.keyword" placeholder="搜索问题或回答" clearable size="small" class="m-l-10" style="width: 200px" @clear="getFaqLists()" @keyup.enter.native="getFaqLists()">
                                <el-button slot="append" icon="el-icon-search" @click="getFaqLists()"></el-button>
                            </el-input>
                        </div>
                        <el-table :data="faqList" v-loading="faqLoading" style="width: 100%" size="mini">
                            <el-table-column prop="id" label="ID" min-width="60" />
                            <el-table-column prop="question" label="用户问题" min-width="160" show-overflow-tooltip />
                            <el-table-column prop="answer" label="回复内容" min-width="200" show-overflow-tooltip />
                            <el-table-column label="分类" min-width="80">
                                <template v-slot="scope">{{ getCategoryLabel(scope.row.category) }}</template>
                            </el-table-column>
                            <el-table-column label="状态" min-width="80">
                                <template v-slot="scope">
                                    <el-switch :value="scope.row.status === 1" :active-value="true" :inactive-value="false" @change="onToggleFaq(scope.row)" />
                                </template>
                            </el-table-column>
                            <el-table-column prop="hit_count" label="命中次数" min-width="80" />
                            <el-table-column label="过期时间" min-width="140">
                                <template v-slot="scope">{{ scope.row.expire_time > 0 ? new Date(scope.row.expire_time * 1000).toLocaleDateString() : '永久' }}</template>
                            </el-table-column>
                            <el-table-column label="操作" width="150" fixed="right">
                                <template v-slot="scope">
                                    <el-button type="text" size="small" @click="onEditFaq(scope.row)">编辑</el-button>
                                    <ls-dialog class="inline" @confirm="onDeleteFaq(scope.row.id)">
                                        <el-button type="text" size="small" slot="trigger">删除</el-button>
                                    </ls-dialog>
                                </template>
                            </el-table-column>
                        </el-table>
                        <div class="m-t-24 pagination">
                            <el-pagination
                                :current-page="faqPage"
                                :page-size="15"
                                :total="faqTotal"
                                layout="prev, pager, next"
                                @current-change="onFaqPageChange"
                            />
                        </div>
                    </div>
                </el-tab-pane>
            </el-tabs>
        </div>

        <knowledge-edit ref="knowledgeEditRef" @refresh="getKnowledgeLists()" />

        <el-dialog :title="faqDialogTitle" :visible.sync="faqDialogVisible" width="600px" :close-on-click-modal="false">
            <el-form ref="faqFormRef" :model="faqForm" label-width="100px" size="small">
                <el-form-item label="用户问题" prop="question" :rules="[{ required: true, message: '请输入问题' }]">
                    <el-input v-model="faqForm.question" placeholder="如：周末休息吗？" />
                </el-form-item>
                <el-form-item label="回复内容" prop="answer" :rules="[{ required: true, message: '请输入回复' }]">
                    <el-input v-model="faqForm.answer" type="textarea" :rows="4" placeholder="如：我们周末正常营业，不休息哦~" />
                </el-form-item>
                <el-form-item label="分类">
                    <el-select v-model="faqForm.category" style="width: 100%">
                        <el-option v-for="item in categoryOptions" :key="item.value" :label="item.label" :value="item.value" />
                    </el-select>
                </el-form-item>
                <el-form-item label="启用">
                    <el-switch v-model="faqForm.statusBool" />
                </el-form-item>
            </el-form>
            <div slot="footer">
                <el-button size="small" @click="faqDialogVisible = false">取消</el-button>
                <el-button type="primary" size="small" @click="onSaveFaq">保存</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import {
    apiAiBotGetConfig, apiAiBotSetConfig,
    apiAiBotGetPrompt, apiAiBotSetPrompt,
    apiAiBotKnowledgeLists, apiAiBotKnowledgeDel, apiAiBotKnowledgeApprove,
    apiAiBotFaqLists, apiAiBotFaqAdd, apiAiBotFaqEdit, apiAiBotFaqDel, apiAiBotFaqToggle,
    apiAiBotSendReport
} from '@/api/application/kefu_ai_bot'
import { RequestPaging } from '@/utils/util'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import KnowledgeEdit from './knowledge-edit.vue'

@Component({ components: { LsDialog, LsPagination, KnowledgeEdit } })
export default class KefuAiBot extends Vue {
    activeTab = 'config'

    configForm: any = {
        deepseek_api_key: '', deepseek_model: 'deepseek-chat',
        deepseek_temperature: 0.7, deepseek_max_tokens: 2048,
        wecom_corp_id: '', wecom_kefu_secret: '', wecom_callback_token: '',
        wecom_encoding_aes_key: '', wecom_open_kfid: '', wecom_report_webhook: ''
    }
    promptForm: any = { prompt: '' }
    knowledgeFilter: any = { category: '', keyword: '' }
    pager: RequestPaging = new RequestPaging()
    sendingReport = false
    categoryOptions = [
        { label: '商品', value: 'product' }, { label: '订单', value: 'order' },
        { label: '售后', value: 'after_sale' }, { label: '分销', value: 'distribution' },
        { label: '物流', value: 'delivery' }, { label: '通用', value: 'general' }
    ]

    faqFilter: any = { category: '', keyword: '' }
    faqList: any[] = []
    faqLoading = false
    faqPage = 1
    faqTotal = 0
    faqDialogVisible = false
    faqDialogTitle = '添加对话经验'
    faqForm: any = { id: 0, question: '', answer: '', category: 'general', statusBool: true }

    getCategoryLabel(value: string): string {
        const item = this.categoryOptions.find(o => o.value === value)
        return item ? item.label : value
    }

    getConfig() {
        apiAiBotGetConfig({}).then((res: any) => {
            this.configForm = { ...this.configForm, ...res }
            this.configForm.deepseek_temperature = parseFloat(this.configForm.deepseek_temperature) || 0.7
            this.configForm.deepseek_max_tokens = parseInt(this.configForm.deepseek_max_tokens) || 2048
        })
    }
    onSaveConfig() {
        apiAiBotSetConfig({ ...this.configForm }).then(() => { this.$message.success('保存成功'); this.getConfig() })
    }
    onSendReport() {
        this.sendingReport = true
        apiAiBotSendReport({ date: '' }).then(() => {
            this.$message.success('日报已推送，请查看企微群')
        }).catch(() => {
            this.$message.error('推送失败，请检查Webhook配置')
        }).finally(() => { this.sendingReport = false })
    }

    getPrompt() {
        apiAiBotGetPrompt({}).then((res: any) => { this.promptForm.prompt = res.system_prompt || '' })
    }
    onSavePrompt() {
        apiAiBotSetPrompt({ system_prompt: this.promptForm.prompt }).then(() => { this.$message.success('保存成功'); this.getPrompt() })
    }

    getKnowledgeLists(page?: number) {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apiAiBotKnowledgeLists,
            params: { category: this.knowledgeFilter.category || undefined, keyword: this.knowledgeFilter.keyword || undefined }
        })
    }
    onAddKnowledge() { (this.$refs.knowledgeEditRef as KnowledgeEdit).open() }
    onEditKnowledge(row: any) { (this.$refs.knowledgeEditRef as KnowledgeEdit).open(row.id) }
    onApproveKnowledge(id: number) { apiAiBotKnowledgeApprove({ id }).then(() => { this.$message.success('审核通过'); this.getKnowledgeLists() }) }
    onDeleteKnowledge(id: number) { apiAiBotKnowledgeDel({ id }).then(() => { this.getKnowledgeLists() }) }

    getFaqLists() {
        this.faqLoading = true
        apiAiBotFaqLists({
            page_no: this.faqPage, page_size: 15,
            category: this.faqFilter.category || undefined,
            keyword: this.faqFilter.keyword || undefined
        }).then((res: any) => {
            this.faqList = res.lists || []
            this.faqTotal = res.count || 0
        }).finally(() => { this.faqLoading = false })
    }
    onAddFaq() {
        this.faqDialogTitle = '添加对话经验'
        this.faqForm = { id: 0, question: '', answer: '', category: 'general', statusBool: true }
        this.faqDialogVisible = true
    }
    onEditFaq(row: any) {
        this.faqDialogTitle = '编辑对话经验'
        this.faqForm = { id: row.id, question: row.question, answer: row.answer, category: row.category, statusBool: row.status === 1 }
        this.faqDialogVisible = true
    }
    onSaveFaq() {
        const formRef = this.$refs.faqFormRef as HTMLFormElement
        formRef.validate((valid: boolean) => {
            if (!valid) return
            const params = { ...this.faqForm, status: this.faqForm.statusBool ? 1 : 0 }
            delete params.statusBool
            const api = params.id ? apiAiBotFaqEdit : apiAiBotFaqAdd
            api(params).then(() => { this.$message.success('保存成功'); this.faqDialogVisible = false; this.getFaqLists() })
        })
    }
    onDeleteFaq(id: number) { apiAiBotFaqDel({ id }).then(() => { this.getFaqLists() }) }
    onToggleFaq(row: any) {
        const newStatus = row.status === 1 ? 0 : 1
        apiAiBotFaqToggle({ id: row.id, status: newStatus }).then(() => { this.getFaqLists() })
    }
    onFaqPageChange(page: number) {
        this.faqPage = page
        this.getFaqLists()
    }

    handleTabClick(tab: any) {
        if (tab.name === 'config') this.getConfig()
        else if (tab.name === 'prompt') this.getPrompt()
        else if (tab.name === 'knowledge') this.getKnowledgeLists()
        else if (tab.name === 'faq') this.getFaqLists()
    }

    created() { this.getConfig() }
}
</script>

<style lang="scss" scoped>
.pagination { padding-right: 5%; display: flex; justify-content: flex-end; }
</style>
