import request from '@/plugins/axios'

export const apiAiBotGetConfig = (params: any) => request.get('/kefu.kefu_ai_bot/getConfig', { params })
export const apiAiBotSetConfig = (params: any) => request.post('/kefu.kefu_ai_bot/setConfig', params)

export const apiAiBotGetPrompt = (params: any) => request.get('/kefu.kefu_ai_bot/getPrompt', { params })
export const apiAiBotSetPrompt = (params: any) => request.post('/kefu.kefu_ai_bot/setPrompt', params)

export const apiAiBotKnowledgeLists = (params: any) => request.get('/kefu.kefu_ai_bot/knowledgeLists', { params })
export const apiAiBotKnowledgeDetail = (params: any) => request.get('/kefu.kefu_ai_bot/knowledgeDetail', { params })
export const apiAiBotKnowledgeAdd = (params: any) => request.post('/kefu.kefu_ai_bot/knowledgeAdd', params)
export const apiAiBotKnowledgeEdit = (params: any) => request.post('/kefu.kefu_ai_bot/knowledgeEdit', params)
export const apiAiBotKnowledgeDel = (params: any) => request.post('/kefu.kefu_ai_bot/knowledgeDel', params)
export const apiAiBotKnowledgeApprove = (params: any) => request.post('/kefu.kefu_ai_bot/knowledgeApprove', params)
export const apiAiBotKnowledgeReject = (params: any) => request.post('/kefu.kefu_ai_bot/knowledgeReject', params)

export const apiAiBotFaqLists = (params: any) => request.get('/kefu.kefu_ai_bot/faqLists', { params })
export const apiAiBotFaqAdd = (params: any) => request.post('/kefu.kefu_ai_bot/faqAdd', params)
export const apiAiBotFaqEdit = (params: any) => request.post('/kefu.kefu_ai_bot/faqEdit', params)
export const apiAiBotFaqDel = (params: any) => request.post('/kefu.kefu_ai_bot/faqDel', params)
export const apiAiBotFaqToggle = (params: any) => request.post('/kefu.kefu_ai_bot/faqToggle', params)
export const apiAiBotSendReport = (params: any) => request.post('/kefu.kefu_ai_bot/sendReport', params)
