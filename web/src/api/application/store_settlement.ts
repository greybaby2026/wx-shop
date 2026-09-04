import request from '@/plugins/axios'

// 门店结算中心
export const apiStoreSettlementLists = (params: any) => request.get('/store_settlement.settlement/lists', { params })
export const apiStoreSettlementDetail = (params: any) => request.get('/store_settlement.settlement/detail', { params })
export const apiStoreSettlementGenerate = (params: any) => request.post('/store_settlement.settlement/generate', params)
export const apiStoreSettlementConfirm = (params: any) => request.post('/store_settlement.settlement/confirm', params)
export const apiStoreSettlementMarkPaid = (params: any) => request.post('/store_settlement.settlement/markpaid', params)
