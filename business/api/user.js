import request from '@/utils/request'

// 个人中心
export const apiUserCentre = () => request.get('Setting/getShopConfig')


// S 个人设置
// 获取用户信息
export const apiGetUserInfo = () => request.get('Setting/getAdminInfo')

// 退出登录
export const apiLogout = () => request.post('login/logout')

// 设置用户登录登录密码
export const apiSetPassword = params => request.post('shop/changePwd', params)
//  E 个人设置

// 账户明细
export const userBill = (params) => request.get('account_log/lists', {
	params
})
// E 转账

//财务概况
export const userFinance = (params) => request.get('Finance/dataCenter')

