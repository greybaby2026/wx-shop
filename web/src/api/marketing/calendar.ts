import request from '@/plugins/axios'

// 日历数据概览
export const apiCalendarData = () => request.post('/sign.sign/dataCenter')

// 获取签到规则
export const apiCalendarGetRule = (params: any) => request.get('/sign.sign/getConfig', { params })

// 设置签到规则
export const apiCalendarSetRule = (params: any) => request.post('/sign.sign/setConfig', params)

// 添加连续签到规则
export const apiCalendarAddRule = (params: any) => request.post('/sign.sign/add', params)

// 编辑连续签到规则
export const apiCalendarEditRule = (params: any) => request.post('/sign.sign/edit', params)

// 删除连续签到规则
export const apiCalendarDelRule = (params: any) => request.post('/sign.sign/delete', params)

// 签到记录
export const apiCalendarRecord = (params: any) => request.get('/sign.sign/lists', { params })
