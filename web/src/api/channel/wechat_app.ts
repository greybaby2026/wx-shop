import request from '@/plugins/axios'
import * as Interface from '@/api/channel/wechat_app.d.ts'

/** S 微信小程序设置 **/
// 获取微信小程序设置
export const apiWechatMiniSetting = (): Promise<Interface.WechatMiniSetting_Res> =>
    request.get('/wechat.mini_program_setting/getConfig')
// 微信小程序设置
export const apiWechatMiniSettingSet = (data: Interface.WechatMiniSetting_Req): Promise<any> =>
    request.post('/wechat.mini_program_setting/setConfig', data)
/** E 微信小程序设置 **/

export const apiWechatMiniUpload = (data: any): Promise<any> =>
    request.post('/wechat.mini_program_setting/uploadMnp', data)

export const apiWechatMinigetlog = (params: any): Promise<any> =>
    request.get('/wechat.mini_program_setting/uploadMnpLogLists', { params })
