import request from '@/plugins/axios'
import * as Interface from '@/api/setting/shop.d'

/** S 店铺信息 **/
// 店铺信息--获取
export const apiSettingShopInfo = (): Promise<Interface.ShopInfo_Res> =>
    request.get('/settings.shop.shop_setting/getShopInfo')

// 店铺信息--设置
export const apiSettingShopEdit = (params: Interface.ShopEdit_Req): Promise<any> =>
    request.post('/settings.shop.shop_setting/setShopInfo', params)
/** E 店铺信息 **/

/** S 备案信息 **/
// 获取备案信息
export const apiRecordInfo = (): Promise<Interface.RecordInfo_Res> =>
    request.get('/settings.shop.shop_setting/getRecordInfo')

// 编辑备案信息
export const apiRecordEdit = (params: Interface.RecordInfo_Req) =>
    request.post('/settings.shop.shop_setting/setRecordInfo', params)
/** E 备案信息 **/

/** S 分享设置 **/
// 获取分享信息
export const apiShareInfo = (): Promise<Interface.ShopInfo_Res> =>
    request.get('/settings.shop.shop_setting/getShareSetting')

// 设置分享设置
export const apiShareEdit = (params: Interface.ShareEdit_Req) =>
    request.post('/settings.shop.shop_setting/setShareSetting', params)
/** E 分享设置 **/

/** S 政策协议 **/
// 获取政策协议
export const apiProtocolInfo = (): Promise<Interface.ProtocolInfo_Res> =>
    request.get('/settings.shop.shop_setting/getPolicyAgreement')

// 设置政策协议
export const apiProtocolEdit = (params: Interface.ProtocolEdit_Req) =>
    request.post('/settings.shop.shop_setting/setPolicyAgreement', params)
/** E 政策协议 **/
