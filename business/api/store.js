import request from '@/utils/request'

// 首页
export const apiIndex = () => request.get('workbench/index')

//获取店铺信息
export const apiGetShopInfo = (params) => request.post('Setting/getShopConfig')

// 设置店铺信息
export const apiSetShopInfo = (params) => request.post('Setting/setShopConfig', params)

// 商品分析
export const apiStatisticsGoodslist = () => request.get('finance/goodsCenter')

// 交易分析
export const apiStatisticsTrading = () => request.get('finance/dealCenter')

// 访问交易分析
export const apiStatisticsVisit = () => request.get('finance/visitCenter')