import request from '@/plugins/axios'

// 新增秒杀
export const apiFreeShippingAdd = (data: any) => request.post('/free_shipping.free_shipping/add', data)

// 编辑秒杀
export const apiFreeShippingEdit = (data: any) => request.post('/free_shipping.free_shipping/edit', data)

// 秒杀详情
export const apiFreeShippingDetail = (params: { id: number }) =>
    request.get('/free_shipping.free_shipping/detail', { params })

// 秒杀列表
export const apiFreeShippingLists = (params: { id: number }) =>
    request.get('/free_shipping.free_shipping/lists', { params })

// 删除秒杀
export const apiFreeShippingDel = (data: { id: number }) => request.post('/free_shipping.free_shipping/delete', data)

// 开启秒杀
export const apiFreeShippingOpen = (data: { id: number }) => request.post('/free_shipping.free_shipping/start', data)

// 结束秒杀
export const apiFreeShippingStop = (data: { id: number }) => request.post('/free_shipping.free_shipping/end', data)
