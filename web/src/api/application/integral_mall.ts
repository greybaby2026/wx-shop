import request from '@/plugins/axios'

// 获取积分商品列表
export const apiIntegralGoodsLists = (params: any) => request.get('/integral.integral_goods/lists', { params })

// 添加积分商品
export const apiIntegralGoodsAdd = (params: any) => request.post('/integral.integral_goods/add', params)

// 编辑积分商品
export const apiIntegralGoodsEdit = (params: any) => request.post('/integral.integral_goods/edit', params)

// 删除积分商品
export const apiIntegralGoodsDel = (params: any) => request.post('/integral.integral_goods/del', params)

// 切换积分商品状态
export const apiIntegralGoodsStatus = (params: any) => request.post('/integral.integral_goods/status', params)

// 获取积分商品详情
export const apiIntegralGoodsDetail = (params: any) => request.get('/integral.integral_goods/detail', { params })

// 兑换订单列表
export const apiIntegralOrderLists = (params: any) => request.get('/integral.integral_order/lists', { params })

// 兑换订单详情
export const apiIntegralOrderDetail = (params: any) => request.get('/integral.integral_order/detail', { params })

// 发货信息
export const apiIntegralOrderDeliveryInfo = (params: any) =>
    request.get('/integral.integral_order/deliveryInfo', { params })

// 发货
export const apiIntegralGoodsDelivery = (params: any) => request.post('/integral.integral_order/delivery', params)

// 确认收货
export const apiIntegralGoodsConfirm = (params: any) => request.post('/integral.integral_order/confirm', params)

// 确认收货
export const apiIntegralGoodsCancel = (params: any) => request.post('/integral.integral_order/cancel', params)

// 确认付款
export const apiIntegralGoodsPay = (params: any) => request.post('/integral.integral_order/confirmOfflinePay', params)

// 物流查询
export const apiIntegralGoodsLogistics = (params: any) => request.get('/integral.integral_order/logistics', { params })
