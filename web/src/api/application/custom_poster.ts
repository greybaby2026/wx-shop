import request from '@/plugins/axios'

//获取商品海报
export const apiGoodsPosterGet = () => request.get('/poster.poster/getGoodsConfig')

// 设置商品海报
export const apiGoodsPosterSet = (data: any) => request.post('/poster.poster/setGoodsConfig', data)

//获取邀请海报
export const apiInvitationPosterGet = () => request.get('/poster.poster/getDistributionConfig')

// 设置邀请海报
export const apiInvitationPosterSet = (data: any) => request.post('/poster.poster/setDistributionConfig', data)
