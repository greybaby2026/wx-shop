import request from '@/plugins/axios'

// 商品列表
export const apiWorkbenchIndex = (data: any) => request.get('/workbench/index', data)

// 商品top50
export const apiWorkbenchTopGoods = (params: any) => request.get('/workbench/topGoods50', { params })

// 用户top50
export const apiWorkbenchTopUser = (params: any) => request.get('/workbench/topUser50', { params })

// 检测更新
export const apiCheckRefresh = (params?: any) => request.get('/config/checkLegal', { params })
