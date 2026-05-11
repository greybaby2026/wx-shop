import request from "@/utils/request";

// 积分商城
export function apiIntegralGoods(params) {
  return request.get("integral_goods/lists", { params });
}

// 积分商品详情
export function apiIntegralGoodsDetail(params) {
  return request.get("integral_goods/detail", { params });
}

// 积分兑换订单结算
export function apiIntegralSettlement(params) {
  return request.get("integral_order/settlement", { params });
}

// 积分兑换提交订单
export function apiIntegralSubmitOrder(params) {
  return request.post("integral_order/submitOrder", params);
}

// 积分兑换订单列表
export function apiIntegralOrder(params) {
  return request.get("integral_order/lists", { params });
}

//删除积分兑换订单
export function apiDelIntegralOrder(params) {
  return request.post("integral_order/del", params);
}

//积分兑换订单订单详情
export function apiIntegralOrderDetail(params) {
  return request.get("integral_order/detail", { params });
}

//取消积分兑换订单
export function apiCancelIntegralOrder(params) {
  return request.post("integral_order/cancel", params);
}

//确认收货积分兑换订单
export function apiConfirmIntegralOrder(params) {
  return request.post("integral_order/confirm", params);
}
// 查看物流
export function apiIntegralOrderTraces(params) {
  return request.get("integral_order/orderTraces", { params });
}
//确认收货组件
export function getwxReceiveDetail(params) {
  return request.get("integralOrder/wxReceiveDetail", { params });
}
//查询确认收货
export function getwechatSyncCheck(params) {
  return request.get("integralOrder/wechatSyncCheck", { params });
}
