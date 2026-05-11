import request from '@/plugins/axios'

// 充值数据概览
export const apiRechargeData = (params?: any) => request.post('/recharge.recharge/dataCenter', params)

// 充值规则
export const apiRechargeGetRule = (params: any) => request.get('/recharge.recharge/getConfig', { params })

// 设置充值规则
export const apiRechargeSetRule = (params: any) => request.post('/recharge.recharge/setConfig', params)

// 充值记录
export const apiRechargeRecord = (params: any) => request.get('/recharge.recharge/lists', { params })

// 充值佣金列表
export const apiRechargeCommissionLists = (params: any) => request.get('/recharge.recharge/commissionLists', { params })
