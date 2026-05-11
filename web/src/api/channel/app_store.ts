import request from '@/plugins/axios'
import * as Interface from '@/api/channel/app_store.d.ts'

/** S APP设置 **/
// 获取APP设置
export const apiAppSettings = (): Promise<Interface.AppSettings_Res> =>
    request.get('/settings.app.app_setting/getConfig')
// APP设置
export const apiAppSettingsSet = (data: Interface.AppSettings_Req): Promise<any> =>
    request.post('/settings.app.app_setting/setConfig', data)
/** E APP设置 **/
