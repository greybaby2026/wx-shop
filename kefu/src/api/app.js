import request from '@/plugins/axios'
import { terminal } from '@/utils/enum'

// 登录
export const apiLogin = data => request.post('/kefuapi/login/account', {...data, terminal})

// 退出登录
export const apiLogout = () => request.post('/kefuapi/login/logout')



// 用户列表
export const apiChatUserList = params => request.get('/kefuapi/chat/user', {params})

// 聊天记录
export const apiChatHistory = params => request.get('/kefuapi/chat/record', {params})

// 客服列表
export const apiServiceList = params => request.get('/kefuapi/chat/online', {params})


// 快捷回复
export const apiReplyList = params => request.get('/kefuapi/chat/reply', {params})


// 获取用户详情
export const apiUserInfo = params => request.get('/kefuapi/chat/userInfo', {params})

// 获取订单列表
export const apiUserOrder = params => request.get('/kefuapi/chat/order', {params})

// 文件上传
export const apiUploadFile = data => request.post('/kefuapi/file/formImage', data)

// 获取客服详情
export const apiKefuDetail = () => request.get('/kefuapi/chat/kefuInfo')

// 获取配置信息
export const apiGetConfig = () => request.get('/kefuapi/chat/config')
