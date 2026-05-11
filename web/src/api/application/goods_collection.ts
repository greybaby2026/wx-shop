import request from '@/plugins/axios'

//获取商品采集记录列表
export const apiGoodsGatherLogLists = (params: any) => request.get('/goods.goods_gather/logLists', { params })

//获取商品采集配置
export const apiGetGatherKey = (params: any) => request.get('/settings.goods.goods_settings/getGatherKey', { params })

//设置商品采集配置
export const apiSetGatherKey = (params: any) => request.post('/settings.goods.goods_settings/setGatherKey', params)

//采集
export const apiGoodsGather = (params: any) => request.post('/goods.goods_gather/gather', params)

//删除采集
export const apiDelGather = (params: any) => request.post('/goods.goods_gather/del', params)

//获取商品采集列表
export const apiGoodsGatherLists = (params: any) => request.get('/goods.goods_gather/lists', { params })

//上下架商品
export const apiCreateGoods = (params: any) => request.post('/goods.goods_gather/createGoods', params)

//采集商品详情
export const apiGatherGoodsDetails = (id: number) =>
    request.get('/goods.goods_gather/gatherGoodsDetail', { params: { id } })

//采集商品编辑
export const apiGatherGoodsEdit = (params: any) => request.post('/goods.goods_gather/gatherGoodsEdit', params)
