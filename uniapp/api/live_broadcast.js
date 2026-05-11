import request from '@/utils/request'

// 直播间列表
export const apiLiveRoomLists = params => request.get('LiveRoom/lists', { params })


