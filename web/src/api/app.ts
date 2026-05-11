import request from '@/plugins/axios'

// 登录
export const apiLogin = (data: any) => request.post('/login/account', data)

// 退出登录
export const apiLogout = () => request.get('/login/logout')

// 配置
export const apiconfig = () => request.get('/config/getConfig')

// 退出登录
export const apiDownLoad = () => request.get('', { params: { d: 1 } })

// 页面跳转列表
export const apiLinkList = (params: any) => request.get('/theme.DecorateThemePage/getshoppage', { params })

// 页面跳转列表
export const apiPcLinkList = (params: any) => request.get('/theme.PcDecorateThemePage/getPcpage', { params })

// 权限列表
export const apiAuth = () => request.get('/config/getAuth')

// 获取管理员基本信息
export const apiAdminInfo = () => request.get('/auth.admin/getAdminInfo')

// 修改管理员密码
export const apiAdminResetPassword = (params: any) => request.post('/auth.admin/resetPassword', params)
