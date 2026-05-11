import request from '@/plugins/axios'

// 活动列表
export const apiLuckyDrawLists = (params: any) => request.get('/lucky_draw.lucky_draw/lists', { params })

// 删除活动
export const apiLuckyDrawDelete = (params: any) => request.post('/lucky_draw.lucky_draw/delete', params)

// 结束活动
export const apiLuckyDrawEnd = (params: any) => request.post('/lucky_draw.lucky_draw/end', params)

// 开始活动
export const apiLuckyDrawStart = (params: any) => request.post('/lucky_draw.lucky_draw/start', params)

// 编辑活动
export const apiLuckyDrawEdit = (params: any) => request.post('lucky_draw.lucky_draw/edit', params)

// 活动详情
export const apiLuckyDrawDetail = (params: any) => request.get('/lucky_draw.lucky_draw/detail', { params })

// 添加活动
export const apiLuckyDrawAdd = (params: any) => request.post('lucky_draw.lucky_draw/add', params)

// 获取奖品类型
export const apiLuckyDrawGetPrizeType = () => request.get('lucky_draw.lucky_draw/getPrizeType')

// 活动抽奖记录
export const apiLuckyDrawRecord = (params: any) => request.get('lucky_draw.lucky_draw/record', { params })
