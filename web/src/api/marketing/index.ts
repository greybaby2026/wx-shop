import request from '@/plugins/axios'

/** S 营销主页 **/

// 获取营销模块
export const apiMarketingModule = () => request.get('/config/getMarketingModule')

// 获取应用模块
export const apiAppModule = () => request.get('/config/getAppModule')

/** E 营销主页 **/
// 获取活动
export const apiGetActivity = (params: any) => request.get('/common/activity', { params })

// 获取活动商品
export const apiGetActivityGoods = (params: any) => request.get('/common/activityGoods', { params })

// 设置消费奖励
export const apiAwardIntegralSet = (params: any) => request.post('award_integral/setConfig', params)

// 获取消费奖励
export const apiAwardIntegralGet = () => request.get('award_integral/getConfig')
