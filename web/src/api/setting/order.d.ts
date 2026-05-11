import * as Common from '@/api/common'

/** S 用户设置 **/
// 获取商品设置
export interface OrderConfig_Res {
    cancel_unpaid_orders: 0 | 1 //系统取消待付款订单 0-关闭系统自动取消待付款订单 1-订单提交{cancel_unpaid_orders_times}分钟内未付款，系统自动取消
    cancel_unpaid_orders_times: number // 取消未付款订单时间,单位：分钟
    cancel_unshipped_orders: 0 | 1 // 买家取消待发货订单 0-关闭买家取消待发货订单 1-待发货订单{cancel_unshipped_orders_times}分钟内允许买家取消
    cancel_unshipped_orders_times: number // 取消待发货订单时间,单位：分钟
    automatically_confirm_receipt: 0 | 1 // 系统自动确认收货 0-关闭系统自动确认收货 1-订单发货后{automatically_confirm_receipt_days}天内，系统 自动确认收货
    automatically_confirm_receipt_days: number // 系统自动收货时间,单位：天
    after_sales: 0 | 1 // 买家售后维权时效 0-关闭售后维权 1-订单确认收货{after_sales_days}天内，可申请售后维权
    after_sales_days: number // 售后维权时间，单位：天
    inventory_occupancy: 1 // 库存占用时机 1-订单提交占用库存
    return_inventory: 0 | 1 // 取消未付款/未发货的订单退回库存 0-无需退回库存 1-需要退回库存
    return_coupon: 0 | 1 // 取消未付款/未发货订单退回优惠券券 0-无需退还优惠券 1-需要退还优惠券
}
/** E 用户设置 **/
