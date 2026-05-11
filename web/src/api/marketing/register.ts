import request from '@/plugins/axios'

export const apiregisterSave = (data: any) => request.post('/marketing.registerAward/setConfig', data)
export const apiregisterdetial = (data?: any) => request.get('/marketing.registerAward/getConfig', data)
