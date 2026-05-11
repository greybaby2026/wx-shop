import request from '@/utils/request'

/** S 商品 **/

// 商品列表
export const apiGoodsLists = (params) => request.get('goods/lists', {params})

// 商品操作
export const apiGoodsOperation = (params) => request.post('goods/status', params)

// 商品详情
export const apiGoodsDetail = (params) => request.get('goods/detail', {params})

// 商品编辑
export const apiGoodsEdit = (params) => request.post('goods/edit', params)


/** E 商品 **/