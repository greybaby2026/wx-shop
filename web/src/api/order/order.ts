import request from '@/plugins/axios'

// S 订单

// 订单列表其他列表参数
export const apiOtherLists = () => request.get('/order.order/otherLists')

// 订单详情
export const apiOrderDetail = (params: any) => request.get('/order.order/detail', { params })

// 订单列表
export const apiOrderLists = (params: any) => request.get('/order.order/lists', { params })

// 商家备注
export const apiOrderRemarks = (params: any) => request.post('/order.order/orderRemarks', params)

// 取消订单
export const apiOrderCancel = (params: any) => request.post('/order.order/cancel', params)

// 发货信息
export const apiOrderDeliveryInfo = (params: any) => request.get('/order.order/deliveryInfo', { params })

// 发货
export const apiOrderDelivery = (params: any) => request.post('/order.order/delivery', params)

// 物流查询
export const apiOrderLogistics = (params: any) => request.get('/order.order/logistics', { params })

// 编辑收货地址
export const apiOrderAddressEdit = (params: any) => request.post('/order.order/addressEdit', params)

// 确认收货
export const apiOrderConfirm = (params: any) => request.post('/order.order/confirm', params)
// 确认付款
export const apiOrderConfirmpay = (params: any) => request.post('/order.order/confirmOfflinePay', params)

// 修改运费
export const apiOrderChangeExpressPrice = (params: any) => request.post('/order.order/changeExpressPrice', params)

// 修改商品价格
export const apiOrderChangeGoodsPrice = (params: any) => request.post('/order.order/changePrice', params)

// 修改物流
export const apiOrderChangeDelivery = (params: any) => request.post('/order.order/changeDelivery', params)
// E 订单

// S 售后

// 售后列表
export const apiAfterSaleLists = (params: any) => request.get('/after_sale.after_sale/lists', { params })

// 售后详情
export const apiAfterSaleDetail = (params: any) => request.get('/after_sale.after_sale/detail', { params })

// 同意售后
export const apiAfterSaleAgree = (params: any) => request.post('/after_sale.after_sale/agree', params)

// 拒绝售后
export const apiAfterSaleRefuse = (params: any) => request.post('/after_sale.after_sale/refuse', params)

// 卖家确认收货
export const apiAfterSaleConfirmGoods = (params: any) => request.post('/after_sale.after_sale/confirmGoods', params)

// 卖家拒绝收货
export const apiAfterSaleRefuseGoods = (params: any) => request.post('/after_sale.after_sale/refuseGoods', params)

// 卖家同意退款
export const apiAfterSaleAgreeRefund = (params: any) => request.post('/after_sale.after_sale/agreeRefund', params)

// 卖家拒绝退款
export const apiAfterSaleRefuseRefund = (params: any) => request.post('/after_sale.after_sale/refuseRefund', params)

// 卖家确认退款
export const apiAfterSaleConfirmRefund = (params: any) => request.post('/after_sale.after_sale/confirmRefund', params)

// E 售后
export const apiOrderImport = (params: any) => request.post('/order.DeliveryBatch/delivery', params)
export const apiDeliveryBatch = (params?: any) => request.get('/order.DeliveryBatch/index', { params })
export const apiDeliveryBatchdown = (params?: any) => request.get('/order.DeliveryBatch/down', { params })
export const apiDeliveryBatchfail = (params?: any) => request.get('/order.DeliveryBatch/down2', { params })
