import request from '@/utils/request'

// 获取我的优惠券
export const apiCouponGet = (params) => request.get('coupon/my', { params })

// 秒杀列表
export const apiSeckillLists = (params) => request.get('seckill/lists', { params })

// 秒杀详情
export const apiSeckillDetail = (params) => request.get('seckill/detail', { params })

// 秒杀下单
export const apiSeckillPlace = (params) => request.post('seckill/buy', params)

// 预售列表
export const apiPresellLists = (params) => request.get('presell/lists', { params })
