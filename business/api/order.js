import request from '@/utils/request'

/** S 订单 **/

// 订单列表
export const apiOrderList = (params) => request.get('order/lists', { params })

// 订单详情
export const apiOrderDetail = (params) => request.get('order/detail', { params })

// 取消订单
export const apiOrderClose = (params) => request.post('order/cancel', params)

// 确认订单收货
export const apiOrderConfirm = (params) => request.post('order/confirm', params)
// 确认订单付款
export const apiOrderConfirmpay = (params) => request.post('order/confirmOfflinePay', params)
// 订单发货
export const apiOrderDelivery = (params) => request.post('Order/delivery', params)

// 获取快递公司列表
export const apiOrderExpress = (params) => request.get('Order/getDeliverInfo', { params })

// 订单查看物流
export const apiOrderLogistics = (params) => request.get('Order/orderTraces', { params })

// 获取地址
export const apiOrderGetAddress = (params) => request.get('Order/getAddress', { params })

// 删除订单
export const apiOrderDelete = (params) => request.post('order/del', params)

// 修改订单地址
export const apiOrderEditAddress = (params) => request.post('Order/addressEdit', params)

/** E 订单 **/

// 核销订单列表
export const apiVerificationOrderList = (params) =>
    request.get('Order/verificationOrderLists', { params })

// 核销订单详情
export const apiVerificationOrderDetail = (params) => request.get('verification/detail', { params })

// 确认核销
export const apiVerificationOrderConfirm = (params) => request.post('Order/verification', params)

export const apideliveryaddressLists = (params) => request.get('address_library/lists', { params })
export const apideliveryaddressedit = (params) => request.post('address_library/default', params)
export const apideliveryaddressdel = (params) => request.post('address_library/del', params)
export const apideliveryaddressadd = (params) => request.post('address_library/add', params)
export const apideliveryaddressEdit = (params) => request.post('address_library/edit', params)

export const apideliveryaddressdetial = (params) =>
    request.get('address_library/detail', { params })
