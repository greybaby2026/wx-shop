import request from '@/utils/request'

/** 门店结算账单(只读) —— 数据与后台「门店结算中心」同源, 服务端按当前账号所属门店过滤 */

// 账单列表(仅本店相关: 应付 or 应收)
export const apiSettlementList = (params) => request.get('store_settlement/lists', { params })

// 账单详情(含核销订单商品行明细)
export const apiSettlementDetail = (params) => request.get('store_settlement/detail', { params })
