import request from '@/plugins/axios'

// 砍价活动列表
export const apiBargainActivityLists = (params: any) => request.get('/bargain.bargain_activity/lists', { params })

// 删除砍价活动
export const apiBargainActivityDelete = (params: any) => request.post('/bargain.bargain_activity/delete', params)

// 结束砍价活动
export const apiBargainActivityStop = (params: any) => request.post('/bargain.bargain_activity/stop', params)

// 确认砍价活动
export const apiBargainActivityConfirm = (params: any) => request.post('/bargain.bargain_activity/confirm', params)

// 编辑砍价活动
export const apiBargainActivityEdit = (params: any) => request.post('/bargain.bargain_activity/edit', params)

// 砍价活动详情
export const apiBargainActivityDetail = (params: any) => request.get('/bargain.bargain_activity/detail', { params })

// 新增砍价活动
export const apiBargainActivityAdd = (params: any) => request.post('/bargain.bargain_activity/add', params)

// 选择商品
export const apiBargainActivityChooseGoods = (params: any) =>
    request.post('/bargain.bargain_activity/chooseGoods', params)

// 商品列表
export const apiBargainActivityGoodsLists = (params: any) =>
    request.get('/bargain.bargain_activity/goodsLists', { params })

// 活动记录
export const apiBargainActivityRecord = (params: any) =>
    request.get('/bargain.bargain_activity/activityRecord', { params })

// 结束砍价记录
export const apiBargainActivityStopInitiate = (params: any) =>
    request.post('/bargain.bargain_activity/stopInitiate', params)
