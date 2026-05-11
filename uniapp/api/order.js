import request from '@/utils/request'

// 配送方式
export const apiDeliveryType = () => request.get('order/getDeliveryType')

// 物流查询
export const apiOrderLogistics = (params) => request.get('order/orderTraces', { params })

/** S 订单 **/
// 结算订单 | 提交订单
export const apiOrderPlace = (params) => request.post('order/placeOrder', params)

// 订单列表
export const apiOrderList = (params) => request.get('order/lists', { params })

// 订单详情
export const apiOrderDetail = (params) => request.get('order/detail', { params })

// 取消订单
export const apiOrderClose = (params) => request.post('order/cancel', params)

// 确认订单收货
export const apiOrderConfirm = (params) => request.post('order/confirm', params)

// 删除订单
export const apiOrderDelete = (params) => request.post('order/del', params)
/** E 订单 **/

/** S 核销订单 **/
// 核销列表
export const apiVerificationOrderList = (params) => request.get('verification/lists', { params })

// 核销详情
export const apiVerificationOrderDetail = (params) =>
    request.get('verification/verification', { params })

// 确认核销
export const apiVerificationOrderConfirm = (params) =>
    request.post('verification/verificationConfirm', params)
/** E 核销订单 **/

/** S 评价 **/
// 获取商品评价列表
export const apiCommentLists = (params) => request.get('goods_comment/lists', { params })

// 获取评价商品列表
export const apiCommentGoodsLists = (params) =>
    request.get('goods_comment/commentGoodsLists', { params })

// 添加商品评价
export const apiCommentAdd = (params) => request.post('goods_comment/add', params)

// 获取商品评价分类
export const apiCommentCategory = (params) =>
    request.get('goods_comment/commentCategory', { params })

// 获取商品评价分类
export const apiCommentGoodsInfo = (params) =>
    request.get('goods_comment/commentGoodsInfo', { params })
/** E 评价 **/

/** S 售后 **/
// 获取提交售后子订单商品信息
export const apiAfterSaleGoods = (params) => request.get('after_sale/orderGoodsInfo', { params })

// 提交售后商品申请
export const apiAfterSalePost = (params) => request.post('after_sale/apply', params)

// 售后列表
export const apiAfterSaleList = (params) => request.get('after_sale/lists', { params })

// 售后详情
export const apiAfterSaleDetail = (params) => request.get('after_sale/detail', { params })

// 取消售后申请
export const apiAfterSaleCancel = (params) => request.post('after_sale/cancel', params)

// 填写快递单号
export const apiExpressInfo = (params) => request.post('after_sale/returnGoods', params)
// 修改快递单号
export const apichangeExpressInfo = (params) => request.post('after_sale/changeDelivery', params)
/** E 售后 **/
//确认收货组件
export function getwxReceiveDetail(params) {
    return request.get('order/wxReceiveDetail', { params })
}
//查询确认收货
export function getwechatSyncCheck(params) {
    return request.get('order/wechatSyncCheck', { params })
}
