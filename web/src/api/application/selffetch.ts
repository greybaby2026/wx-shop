import request from '@/plugins/axios'
import * as Interface from '@/api/application/selffetch.d'

/** S 门店自提 **/
// 门店自提--列表
export const apiSelffetchShopList = (params: Interface.SelffetchShopList_Req): Promise<any> =>
    request.get('/selffetch_shop.selffetch_shop/lists', { params })

// 门店自提--新增
export const apiSelffetchShopAdd = (params: Interface.SelffetchShopAdd_Req): Promise<any> =>
    request.post('/selffetch_shop.selffetch_shop/add', params)

// 门店自提--详情
export const apiSelffetchShopDetail = (params: Interface.SelffetchShopDetail_Req): Promise<any> =>
    request.get('/selffetch_shop.selffetch_shop/detail', { params })

// 门店自提--编辑
export const apiSelffetchShopEdit = (params: Interface.SelffetchShopEdit_Req): Promise<any> =>
    request.post('/selffetch_shop.selffetch_shop/edit', params)

// 门店自提--编辑状态
export const apiSelffetchShopStatus = (params: Interface.SelffetchShopStatus_Req): Promise<any> =>
    request.post('/selffetch_shop.selffetch_shop/status', params)

// 门店自提--删除
export const apiSelffetchShopDel = (params: Interface.SelffetchShopDel_Req): Promise<any> =>
    request.post('/selffetch_shop.selffetch_shop/del', params)

export const apiMapRegionSearch = (params: Interface.MapRegionSearch_Req): Promise<any> =>
    request.get('/selffetch_shop.selffetch_shop/regionSearch', { params })
/** E 门店自提 **/

/** S 核销员 **/
// 核销员--列表
export const apiSelffetchVerifierList = (params: Interface.SelffetchVerifierList_Req): Promise<any> =>
    request.get('/selffetch_shop.selffetch_verifier/lists', { params })

// 核销员--新增
export const apiSelffetchVerifierAdd = (params: Interface.SelffetchVerifierAdd_Req): Promise<any> =>
    request.post('/selffetch_shop.selffetch_verifier/add', params)

// 核销员--详情
export const apiSelffetchVerifierDetail = (params: Interface.SelffetchVerifierDetail_Req): Promise<any> =>
    request.get('/selffetch_shop.selffetch_verifier/detail', { params })

// 核销员--编辑
export const apiSelffetchVerifierEdit = (params: Interface.SelffetchVerifierEdit_Req): Promise<any> =>
    request.post('/selffetch_shop.selffetch_verifier/edit', params)

// 核销员--编辑状态
export const apiSelffetchVerifierStatus = (params: Interface.SelffetchVerifierStatus_Req): Promise<any> =>
    request.post('/selffetch_shop.selffetch_verifier/status', params)

// 核销员--删除
export const apiSelffetchVerifierDel = (params: Interface.SelffetchVerifierDel_Req): Promise<any> =>
    request.post('/selffetch_shop.selffetch_verifier/del', params)
/** E 核销员 **/

/** S 核销订单 **/
// 核销订单--核销
export const apiSelffetchVerification = (params: Interface.SelffetchVerification_Req): Promise<any> =>
    request.post('/selffetch_shop.verification/verification', params)

// 核销订单--列表
export const apiSelffetchVerificationList = (params: Interface.SelffetchVerificationList_Req): Promise<any> =>
    request.get('selffetch_shop.verification/lists', { params })

// 核销订单--查询
export const apiSelffetchVerificationQuery = (params: Interface.SelffetchVerificationQuery_Req): Promise<any> =>
    request.get('selffetch_shop.verification/verificationQuery', { params })

// 核销订单--详情
export const apiSelffetchVerificationDetail = (params: Interface.SelffetchVerificationDetail_Req): Promise<any> =>
    request.get('selffetch_shop.verification/verificationDetail', { params })
/** E 核销订单**/
