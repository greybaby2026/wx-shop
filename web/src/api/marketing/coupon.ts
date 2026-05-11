import request from '@/plugins/axios'
import * as Interface from '@/api/marketing/coupon.d.ts'

// 优惠券列表
export const apiCouponAdd = (data: any) => request.post('/marketing.coupon/add', data)

// 优惠券编辑
export const apiCouponEdit = (data: any) => request.post('/marketing.coupon/edit', data)

// 优惠券列表
export const apiCouponLists = (params: any) => request.get('/marketing.coupon/lists', { params })

// 优惠券状态
export const apiCouponStatus = (data: any) => request.post('/marketing.coupon/status', data)

// 删除优惠券
export const apiCouponDel = (data: any) => request.post('/marketing.coupon/delete', data)

// 开启优惠券
export const apiCouponOpen = (data: any) => request.post('/marketing.coupon/open', data)

// 关闭优惠券
export const apiCouponStop = (data: any) => request.post('/marketing.coupon/stop', data)

// 优惠券排序
export const apiCouponSort = (data: any) => request.post('/marketing.coupon/sort', data)

// 优惠券详情
export const apiCouponDetail = (id: number) => request.get('/marketing.coupon/detail', { params: { id } })

// 优惠券信息
export const apiCouponInfo = (id: number) => request.get('/marketing.coupon/info', { params: { id } })

// 优惠券概况
export const apiCouponSurvey = (): Promise<Interface.CouponSurvey_Res> => request.get('/marketing.coupon/survey')

// 优惠券记录
export const apiCouponRecord = (params: any) => request.get('/marketing.coupon/record', { params })

// 优惠券作废
export const apiCouponVoid = (id: any) => request.post('/marketing.coupon/void', id)

// 优惠券发放
export const apiCouponSend = (data: any) => request.post('/marketing.coupon/send', data)

// 优惠券发放
export const apiCouponCommonLists = (params: any) => request.get('/marketing.coupon/commonLists', { params })
