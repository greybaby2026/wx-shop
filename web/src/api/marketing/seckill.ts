import request from '@/plugins/axios'

// 新增秒杀
export const apiSeckillAdd = (data: any) => request.post('/marketing.seckill/add', data)

// 编辑秒杀
export const apiSeckillEdit = (data: any) => request.post('/marketing.seckill/edit', data)

// 秒杀详情
export const apiSeckillDetail = (params: { id: number }) => request.get('/marketing.seckill/detail', { params })

// 秒杀列表
export const apiSeckillLists = (params: { id: number }) => request.get('/marketing.seckill/lists', { params })

// 秒杀概况
export const apiSeckillSurvey = (params: { id: number }) => request.get('/marketing.seckill/survey', { params })

// 删除秒杀
export const apiSeckillDel = (params: { id: number }) => request.post('/marketing.seckill/delete', params)

// 开启秒杀
export const apiSeckillOpen = (params: { id: number }) => request.post('/marketing.seckill/open', { params })

// 结束秒杀
export const apiSeckillStop = (params: { id: number }) => request.post('/marketing.seckill/stop', { params })
