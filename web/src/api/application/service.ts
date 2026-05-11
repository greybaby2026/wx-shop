import request from '@/plugins/axios'

// 获取在线客服
export const apiServiceGetConfig = (params: any) => request.get('/kefu.kefu_config/getConfig', { params })

// 设置在线客服
export const apiServiceSetConfig = (params: any) => request.post('/kefu.kefu_config/setConfig', params)

// 客服列表
export const apikefuLists = (params: any) => request.get('/kefu.kefu/lists', { params })

// 添加客服
export const apikefuAdd = (params: any) => request.post('/kefu.kefu/add', params)

// 编辑客服
export const apikefuEdit = (params: any) => request.post('/kefu.kefu/edit', params)

// 客服登录
export const apikefuLogin = (params: any) => request.post('/kefu.kefu/login', params)

// 客服详情
export const apikefuDetail = (params: any) => request.get('/kefu.kefu/detail', { params })

// 设置客服启用状态
export const apikefuStatus = (params: any) => request.post('/kefu.kefu/status', params)

// 删除客服
export const apikefuDel = (params: any) => request.post('/kefu.kefu/del', params)

// 话术列表
export const apikefuLangLists = (params: any) => request.get('/kefu.kefu_lang/lists', { params })

// 添加话术
export const apikefuLangAdd = (params: any) => request.post('/kefu.kefu_lang/add', params)

// 编辑话术
export const apikefuLangEdit = (params: any) => request.post('/kefu.kefu_lang/edit', params)

// 删除话术
export const apikefuLangDel = (params: any) => request.post('/kefu.kefu_lang/del', params)
