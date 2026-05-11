import request from '@/plugins/axios'

/** S 商品 **/
// 获取商品设置
export const apiMapGet = (): Promise<any> => request.get('/settings.shop.shop_setting/getMapKey')
// 商品设置
export const apiMapSet = (data: any): Promise<any> => request.post('/settings.shop.shop_setting/setMapKey', data)
/** E 商品 **/
