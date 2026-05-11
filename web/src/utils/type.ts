/*
枚举类型
*/

// 商品类型
export enum GoodsType {
    'all',
    'sale',
    'inventory',
    'soldout',
    'warehouse'
}

// 订单管理类型
export enum OrderType {
    'all_count', //全部
    'pay_count', //待支付
    'delivery_count', //待收货
    'receive_count', //待发货
    'finish_count', //已完成
    'close_count' //已关闭
}

// 售后订单
export enum AfterSaleType {
    'all', //全部
    'ing', //售后中
    'success', //售后成功
    'fail' //售后拒绝
}

// 优惠券类型
export enum CouponType {
    'all',
    'not',
    'conduct',
    'end'
}

// 拼团类型
export enum teamType {
    'all',
    'not',
    'conduct',
    'end'
}

// 拼团类型
export enum SeckillType {
    'all',
    'not',
    'conduct',
    'end'
}
export enum PreSellType {
    'all',
    'wait',
    'start',
    'end'
}

// 分销会员申请类型
export enum disType {
    'all',
    'wait',
    'pass',
    'refuse'
}

// 接口Code状态
export enum APIResponseCodeType {
    'success' = 1, // 成功
    'error' = 0, // 失败
    'redirect' = -1, // 重定向
    'page' = 2, // 跳转页面
    'tips' = 10 // 提示
}

// 页面模式
export enum PageMode {
    'ADD' = 'add', // 添加
    'EDIT' = 'edit' // 编辑
}

// 商品采集详情
export enum GatherDetailMode {
    'wait', // 待处理
    'already' // 已处理
}
