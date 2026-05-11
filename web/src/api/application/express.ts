import request from '@/plugins/axios'

// 获取面单类型
export const apiBetFaceSheetType = (params: any) =>
    request.get('/express_assistant.face_sheet_setting/getFaceSheetType', {
        params
    })

// 获取电子面单配置
export const apiGetFaceSheetConfig = (params: any) =>
    request.get('/express_assistant.face_sheet_setting/getConfig', { params })

// 电子面单设置
export const apiFaceSheetConfigSetting = (params: any) =>
    request.post('/express_assistant.face_sheet_setting/setConfig', params)

// 获取电子面单支付方式
export const apiGetFaceSheetPayment = (params: any) =>
    request.get('/express_assistant.face_sheet_template/getFaceSheetPayment', {
        params
    })

// 添加电子面单模版
export const apiFaceSheetTemplateAdd = (params: any) =>
    request.post('/express_assistant.face_sheet_template/add', params)

// 获取电子面单模版详情
export const apiFaceSheetTemplateDetail = (params: any) =>
    request.get('/express_assistant.face_sheet_template/detail', { params })

// 编辑电子面单模版
export const apiFaceSheetTemplateEdit = (params: any) =>
    request.post('/express_assistant.face_sheet_template/edit', params)

// 删除电子面单模版
export const apiFaceSheetTemplateDel = (params: any) =>
    request.post('/express_assistant.face_sheet_template/delete', params)

// 获取电子面单模版列表
export const apiFaceSheetTemplateLists = (params: any) =>
    request.get('/express_assistant.face_sheet_template/lists', { params })

// 添加发件人
export const apiFaceSheetSenderAdd = (params: any) => request.post('/express_assistant.face_sheet_sender/add', params)

// 发件人详情
export const apiFaceSheetSenderDetail = (params: any) =>
    request.get('/express_assistant.face_sheet_sender/detail', { params })

// 编辑发件人
export const apiFaceSheetSenderEdit = (params: any) => request.post('/express_assistant.face_sheet_sender/edit', params)

// 删除发件人
export const apiFaceSheetSenderDel = (params: any) =>
    request.post('/express_assistant.face_sheet_sender/delete', params)

// 发件人列表
export const apiFaceSheetSenderLists = (params: any) =>
    request.get('/express_assistant.face_sheet_sender/lists', { params })

// 删除发件人
export const apiFaceSheetPrint = (params: any) => request.post('/express_assistant.face_sheet_order/print', params)
