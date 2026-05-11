import request from '@/plugins/axios'

export const apiSiteStatisticInfo = (params?: any) => request.get('/settings.SiteStatistic/set', params)
export const apiSiteStatisticset = (params?: any) => request.post('/settings.SiteStatistic/set', params)
