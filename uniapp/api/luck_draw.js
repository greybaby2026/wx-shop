import request from '@/utils/request'

// 抽奖记录
export const apiLuckyDrawRecord = params => request.get('lucky_draw/record', { params })

// 中奖记录
export const apiLuckyDrawWinningList = params => request.get('lucky_draw/winningList', { params })

// 抽奖活动信息
export const apiLuckyDrawActivity = params => request.get('lucky_draw/activity', {params})

// 开始抽奖
export const apiLuckyDrawStart = params => request.post('lucky_draw/lottery', params)