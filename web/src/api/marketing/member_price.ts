import request from '@/plugins/axios'

/* 会员折扣 */
// 列表
export const apiMemberPriceLists = (params: any) => request.get('/marketing.discount/lists', { params })

// 会员参与折扣
export const apiMemberPriceJoin = (params: { goods_ids: any }) => request.post('/marketing.discount/join', params)

// 不参与会员折扣
export const apiMemberPriceQuit = (params: { goods_ids: any }) => request.post('/marketing.discount/quit', params)

// 折扣商品详情
export const apiMemberPriceDetail = (params: any) => request.get('/marketing.discount/detail', { params })

// 设置会员折扣
export const apiMemberPriceEdit = (params: any) => request.post('/marketing.discount/setDiscount', params)

// 其他状态列表
export const apiMemberPriceOtherLists = (params: any) => request.get('/marketing.discount/otherLists', { params })
