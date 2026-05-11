import request from '@/plugins/axios'

// 新增
export const apiPreSellAdd = (data: any) => request.post('/marketing.presell/add', data)

// 编辑
export const apiPresellEdit = (data: any) => request.post('/marketing.presell/edit', data)

// 详情
export const apiPreSellDetail = (params: { id: number }) => request.get('/marketing.presell/detail', { params })

// 列表
export const apiPresellLists = (params: { id: number }) => request.get('/marketing.presell/lists', { params })

// 秒杀概况
export const apiSeckillSurvey = (params: { id: number }) => request.get('/marketing.seckill/survey', { params })

// 删除
export const apiPreselllDel = (params: { id: number }) => request.post('/marketing.presell/delete', params)

// 开启
export const apiPreselllOpen = (params: any) => request.post('/marketing.presell/start', { id: params })

// 结束
export const apiPreselllStop = (params: any) => request.post('/marketing.presell/end', { id: params })
