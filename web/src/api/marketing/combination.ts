import request from '@/plugins/axios'

// 拼团活动列表
export const apiTeamLists = (params: any) => request.get('/marketing.team/lists', { params })

// 删除拼团活动
export const apiTeamDel = (params: any) => request.post('/marketing.team/delete', params)

// 结束拼团活动
export const apiTeamStop = (params: any) => request.post('/marketing.team/stop', params)

// 确认拼团活动
export const apiTeamConfirm = (params: any) => request.post('/marketing.team/open', params)

// 编辑拼团活动
export const apiTeamEdit = (params: any) => request.post('/marketing.team/edit', params)

// 拼团活动详情
export const apiTeamDetail = (params: any) => request.get('/marketing.team/detail', { params })

// 新增拼团活动
export const apiTeamAdd = (params: any) => request.post('/marketing.team/add', params)

//
export const apiTeamSurvey = (params: any) => request.get('/marketing.team/survey', { params })

// 拼团记录
export const apiTeamRecord = (params: any): Promise<any> => request.get('/marketing.team/record', { params })
// 在拼团记录关闭拼团
export const apiTeamCancel = (params: any) => request.post('/marketing.team/cancel', params)
