import request from '@/utils/request'


// 砍价商品列表
export const apiBargainLists = params => request.get('bargain/lists')

// 砍价商品详情
export const apiBargainDetail = params => request.get('bargain/detail', { params })

// 发起砍价
export const apiBargainInitiate = params => request.post('bargain/initiate', params)

// 帮助砍价
export const apiBargainHelp = params => request.post('bargain/help', params)

// 砍价记录
export const apiBargainRecord = params => request.get('bargain/record', { params })

// 砍价进度
export const apiBargainProgress = params => request.get('bargain/bargainProgress', { params })

// 分享帮砍详情
export const apiBargainShareDetail = params => request.get('bargain/shareDetail', { params })

// 砍价结算
export const apiBargainSettle = params => request.post('bargain/settle', params)

// 砍价下单
export const apiBargainBuy = params => request.post('bargain/buy', params)

// 获取海报二维码
export const apiMnpQrCodeInitiate = params => request.get('share/getMnpQrCodeInitiate', { params })