import request from '@/plugins/axios'

/** S H5设置 **/
// 获取H5设置
export const apiH5Settings = (): Promise<any> => request.get('/settings.h5.h_five_setting/getConfig')
// H5设置
export const apiH5SettingsSet = (data: any): Promise<any> => request.post('/settings.h5.h_five_setting/setConfig', data)
/** E H5设置 **/
