import * as Common from '../common'

/** S 店铺信息 **/
export interface ShopEdit_Req extends Common.Indexes {
    name: string // 商城名称
    logo: string // 商城logo
    admin_login_image: string // 管理后台登录页图片
    login_restrictions: 0 | 1 // 管理后台登录限制: 0-不限制 1-需要限制
    password_error_times?: number // 限制密码错误次数
    limit_login_time?: number // 限制禁止多少分钟不能登录
    status: 0 | 1 // 商城状态: 0-关闭 1-开启
    mall_contact: string // 联系人
    mall_contact_mobile: string // 联系人手机号
    return_contact: string // 退货联系人
    return_contact_mobile: string // 退货联系人手机号
    return_province: number // 退货省份id
    return_city: number // 退货城市id
    return_district: number // 退货区域id
    return_address: string // 退货详细地址
}

export interface ShopInfo_Res extends ShopEdit_Req, Common.Indexes {
    // close_example_image: string,    // 商城关闭示例图
    // address: string,                // 完整的退货地址
}
/** E 店铺信息 **/

/** S 备案信息 **/
export interface RecordInfo_Req {
    copyright?: string // 版权信息
    record_number?: string // 备案号
    record_system_link?: string // 备案号链接
}

export interface RecordInfo_Res extends Common.Indexes {
    copyright: string // 版权信息
    record_number: string // 备案号
    record_system_link: string // 备案号链接
}
/** E 备案信息 **/

/** S 分享设置 **/
export interface ShareInfo_Res {
    share_page: number // 分享页面
    share_title: string // 分享标题
    share_intro: string // 分享简介
    share_image: string // 分享图片
}

export interface ShareEdit_Req {
    share_page: number // 分享页面
    share_title?: string // 分享标题
    share_intro?: string // 分享简介
    share_image: string // 分享图片
}
/** E 分享设置 **/

/** S 政策协议 **/
export interface ProtocolInfo_Res extends Common.Indexes {
    service_agreement_name: string // 服务协议名称
    service_agreement_content: string // 服务协议内容
    privacy_policy_name: string // 隐私政策名称
    privacy_policy_content: string // 隐私政策内容
}

export interface ProtocolEdit_Req {
    service_agreement_name?: string // 服务协议名称
    service_agreement_content?: string // 服务协议内容
    privacy_policy_name?: string // 隐私政策名称
    privacy_policy_content?: string // 隐私政策内容
}
/** E 政策协议 **/
