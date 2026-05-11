import request from '@/plugins/axios'

export const apiashareLists = (params?: any) => request.get('/marketing.dev_share/lists', { params })
export const apishareDetial = (params: any) => request.get('/marketing.dev_share/detail', { params })

export const apishareEdit = (params: any) => request.post('/marketing.dev_share/edit', params)
