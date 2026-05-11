import request from '@/utils/request'
import wechath5 from '@/utils/wechath5'
import store from 'store'

// 账号登录
export const apiAccountLogin = data => 
	request.post("login/account", {...data})
	
// 重置登录密码
export const apiResetPassword = data => 
	request.post("Setting/resetPassword", data)


// 获取服务协议
export const apiPolicyAgreement = (params) => request.get('config/getPolicyAgreement', {params})

// 获取公共配置
export const apiConfig = () => request.get('config/getConfig')
