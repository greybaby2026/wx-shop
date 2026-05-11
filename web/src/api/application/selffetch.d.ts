import * as Common from '../common'

/** S 门店自提 **/
// 门店列表
export interface SelffetchShopList_Req extends Common.Paging_Req {
    name?: string // 门店名称
    status?: 1 | 0 // 门店状态：1-启用； 0-停用;
}

// 门店添加
export interface SelffetchShopAdd_Req {
    name: string // 门店名称
    image: string // 门店LOGO
    contact: string // 联系人
    mobile: string // 联系电话
    province: number // 省
    city?: number // 市
    district?: number // 区
    address: string // 详细地址
    longitude: string // 经度
    latitude: string // 纬度
    business_start_time: string // 营业开始时间
    business_end_time: string // 营业结束时间
    weekdays: string // 营业周天,逗号隔开如 1,2,3,4,5,6,7
    status: number // 门店状态:1-启用;0-停用;
    remark?: string // 门店简介
}

// 门店详情
export interface SelffetchShopDetail_Req {
    id: number // 门店ID
}

// 门店编辑
export interface SelffetchShopEdit_Req extends SelffetchShopAdd_Req {
    id: number // 门店ID
}

// 门店状态编辑
export interface SelffetchShopStatus_Req {
    id: number // 门店ID
    status: number // 门店状态:1-启用;0-停用;
}

// 门店删除
export interface SelffetchShopDel_Req {
    id: number // 门店ID
}

// 地图区域搜素
export interface MapRegionSearch_Req {
    keyword: string // 搜索关键字
    boundary: string // 搜索区域
    key: string // 开发秘钥
}
/** E 门店自提 **/

/** S 核销员 **/
// 核销员列表
export interface SelffetchVerifierList_Req extends Common.Paging_Req {
    name?: string // 核销员名称
    status?: 1 | 0 // 核销员状态：1-启用； 0-停用;
}

// 核销员添加
export interface SelffetchVerifierAdd_Req {
    name: string // 核销员名称
    user_id: number // 用户id
    selffetch_shop_id: number // 自提门店id
    mobile: string // 联系电话
    status: 0 | 1 // 核销员状态: 1-启用; 0-禁用;
}

// 核销员详情
export interface SelffetchVerifierDetail_Req {
    id: number // 核销员ID
}

// 核销员编辑
export interface SelffetchVerifierEdit_Req extends SelffetchVerifierAdd_Req {
    id: number // 核销员ID
}

// 核销员状态编辑
export interface SelffetchVerifierStatus_Req {
    id: number // 核销员ID
    status: number // 核销员状态:1-启用;0-停用;
}

// 核销员删除
export interface SelffetchVerifierDel_Req {
    id: number // 核销员ID
}
/** E 核销员 **/

/** S 核销订单 **/
// 核销订单--列表
export interface SelffetchVerificationList_Req extends Common.Paging_Req {
    pickup_code?: string // 提货码
    verification_status?: 0 | 1 // 核销状态: 0-待核销; 1-已核销;
    order_sn?: string // 订单信息
    user_info?: string // 用户信息
    goods_name?: string // 商品名称
    contact_info?: string // 收货人信息
    time_type?: string // 时间类型: create_time-下单时间; pay_time-支付时间
    start_time?: string // 开始时间
    end_time?: string // 结束时间
}

// 核销订单--查询
export interface SelffetchVerificationQuery_Req {
    id: number // 订单ID
}

// 核销订单--详情
export interface SelffetchVerificationDetail_Req {
    id: number // 订单ID
}

// 核销订单--核销
export interface SelffetchVerification_Req {
    id: number // 订单ID
    confirm?: number | null
}
/** E 核销订单 **/
