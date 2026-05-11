import request from '@/plugins/axios'
import * as Interface from '@/api/setting/goods.d.ts'

/** S 商品 **/
// 获取商品设置
export const apiGoodsSettings = (): Promise<Interface.GoodsSettings_Res> =>
    request.get('/settings.goods.goods_settings/getConfig')
// 商品设置
export const apiGoodsSettingsAdd = (data: Interface.GoodsSettings_Req): Promise<any> =>
    request.post('/settings.goods.goods_settings/setConfig', data)
/** E 商品 **/
