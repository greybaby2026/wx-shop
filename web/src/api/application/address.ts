import request from '@/plugins/axios'

export const apiaddressLists = (params?: any) => request.get('/marketing.address_library/lists', { params })
export const apiaddDetial = (params: any) => request.get('/marketing.address_library/detail', { params })

export const apiaddressDefault = (params: any) => request.post('/marketing.address_library/default', params)

export const apiaddressAdd = (params: any) => request.post('/marketing.address_library/add', params)
export const apiaddressEdit = (params: any) => request.post('/marketing.address_library/edit', params)
export const apiaddressDel = (params: any) => request.post('/marketing.address_library/del', params)
