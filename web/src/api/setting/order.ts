import request from '@/plugins/axios'
import * as Interface from '@/api/setting/order.d.ts'

/** S 订单设置 **/
// 获取订单设置
export const apiOrderConfig = (): Promise<Interface.OrderConfig_Res> =>
    request.get('/settings.order.transaction_settings/getConfig')
// 设置订单设置
export const apiOrderConfigSet = (data: any): Promise<any> =>
    request.post('/settings.order.transaction_settings/setConfig', data)
/** E 订单设置 **/
