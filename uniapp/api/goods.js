import request from '@/utils/request'

/** S 商品 **/
// 获取首页商品列表
export const apiGoodsLists = (params) => request.get('goods/lists', { params })

// 获取商品详情
export const apiGoodsDetail = (params) => request.get('goods/detail', { params })

// 商品搜索记录
export const apiSearchHistory = () => request.get('goods/searchRecord')

// 清空商品搜索记录
export const apiSearchHistoryClear = () => request.post('goods/clearRecord')

// 商品分类
export const apiGoodsCategory = () =>
    request.get('goodsCategory/lists', { params: { page_no: 1, page_size: 100 } })

// 商品营销
export const apiGoodsMarketing = (params) => request.get('goods/goodsMarketing', { params })
/** E 商品 **/

/** S 收藏 **/
// 商品收藏
export const apiGoodsCollect = (params) => request.post('collect/handleCollectgoods', params)

// 商品收藏列表
export const apiGoodsCollectList = (params) => request.get('collect/lists', { params })
/** E 收藏 **/

/** S 评价 **/
// 商品评价分类
export const apiGoodsCommentCategory = (params) =>
    request.get('goods_comment/commentCategory', { params })

// 商品评价列表
export const apiGoodsCommentList = (params) => request.get('goods_comment/lists', { params })
/** E 评价 **/

/** S 拼团商品 **/
// 列表
export const apiGoodsTeamList = (params) => request.get('team/lists', { params })

// 详情
export const apiGoodsTeamDetail = (params) => request.get('team/detail', { params })

// 开团/参团
export const apiGoodsTeamPlace = (params) => request.post('team/kaituan', params)

// 记录
export const apiGoodsTeamHistory = (params) => request.get('team/record', { params })
/** S 拼团商品 **/

//品牌列表
export const apiBrandLists = (params) => request.get('goods_brand/lists', { params })
//商品购买检测
export const apicheckCanBuy = (params) => request.get('goods/checkCanBuy', { params })
