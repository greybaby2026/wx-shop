import request from '@/plugins/axios'

// 直播间列表
export const apiLiveRoomLists = (params: any) => request.get('/live.LiveRoom/lists', { params })

// 创建直播间
export const apiLiveRoomAdd = (params: any) => request.post('/live.LiveRoom/add', params)

// 删除直播间
export const apiLiveRoomDel = (params: any) => request.post('/live.LiveRoom/del', params)

// 上传素材
export const apiLiveRoomUploadMaterial = (params: any) => request.post('/live.LiveRoom/uploadMaterial', params)

// 直播商品列表
export const apiLiveGoodsLists = (params: any) => request.get('/live.LiveGoods/lists', { params })

// 添加直播商品
export const apiLiveGoodsAdd = (params: any) => request.post('/live.LiveGoods/add', params)

// 删除直播商品
export const apiLiveGoodsDel = (params: any) => request.post('/live.LiveGoods/del', params)

// // 上传直播相关图片
// export const apiLiveImgUpload = (params: any) => request.post('/Upload/wechatMaterial', params)
