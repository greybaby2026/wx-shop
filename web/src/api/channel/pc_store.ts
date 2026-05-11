import request from '@/plugins/axios'

/** S pc设置 **/
// 获取pc设置
export const apiPCSettings = (): Promise<any> => request.get('/settings.pc.pc_setting/getConfig')

// pc设置
export const apiPCSettingsSet = (data: any): Promise<any> => request.post('/settings.pc.pc_setting/setConfig', data)
/** E pc设置 **/
