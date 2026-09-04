import request from '@/utils/request'

// 小程序绑定手机号
export const apiMnpBindMobile = (params) => request.post('user/getMobileByMnp', params)

// 绑定手机号
export const apiBindMobile = (params) => request.post('user/bindMobile', params)

// 发送验证码-绑定手机号
export const apiBindMobileCaptcha = (data) => request.post('user/bindMobileCaptcha', data)

// 个人中心
export const apiUserCentre = () => request.get('user/centre')

// 资质信息
export const apiCopyright = () => request.get('config/copyright')

// S 个人设置
// 获取用户信息
export const apiGetUserInfo = () => request.get('user/info')
// 设置用户信息
export const apiSetUserInfo = (params) => request.post('user/setInfo', params)
// 退出u登录
export const apiLogout = () => request.post('login/logout')
// 设置用户登录登录密码
export const apiSetPassword = (params) => request.post('user/setPassword', params)
// 修改用户登录录密码
export const apiChangePassword = (params) => request.post('user/changePassword', params)
// 重置用户登录录密码
export const apiResetPassword = (params) => request.post('user/resetPassword', params)
// 发送验证码- 重置用户登录录密码
export const apiResetPasswordCaptcha = (params) => request.post('user/resetPasswordCaptcha', params)
// 验证是否设置了密码
export const apiHasPassword = (params) => request.post('user/hasPassword', params)

//  E 个人设置

// S 钱包
// 我的钱包数据
export const apiWalletData = () => request.get('user/wallet')
//  E 钱包

// S 支付密码
// 设置/修改支付密码
export const setPassword = (params) => request.post('user/setPayPassword', params)

// 判断是否有设置密码
export const hasPayPassword = () => request.get('user/hasPayPassword')

// 发送验证码-找回支付密码
export const apiresetPayPasswordCaptcha = (params) =>
    request.post('user/resetPayPasswordCaptcha', params)

// 重置支付密码
export const apiresetPayPassword = (params) => request.post('user/resetPayPassword', params)

// E 支付密码

// S 充值
// 充值模版
export const apiRechargeTemplateLists = () => request.get('recharge/rechargeTemplateLists')

// 充值
export const apiRecharge = (params) => request.post('recharge/recharge', params)

// 充值记录
export const apiRechargeRecord = (params) =>
    request.get('recharge/lists', {
        params
    })
// E 充值

// S 转账
// 余额转账
export const transfer = (params) => request.post('user/transfer', params)

// 账户明细
export const userTransferInfo = (params) =>
    request.get('user/transferIn', {
        params
    })

// 获取最近转账记录
export const getTransferRecent = (params) => request.get('user/transferRecent')

// 转账记录
export const transferRecord = (params) =>
    request.get('user/transferLists', {
        params
    })

// 账户明细
export const userBill = (params) =>
    request.get('account_log/lists', {
        params
    })
// E 转账

// S  地址
// 获取地址列表
export const apiAddressLists = () => request.get('user_address/lists')

// 获取地址详情
export const apiAddressDetail = (params) =>
    request.get('user_address/detail', {
        params
    })

// 添加收货地址
export const apiAddressAdd = (params) => request.post('user_address/add', params)

// 编辑收货地址
export const apiAddressEdit = (params) => request.post('user_address/edit', params)

// 设置默认收货地址
export const apiAddressEditDefault = (params) => request.post('user_address/setDefault', params)

// 删除收货地址
export const apiAddressDel = (params) => request.post('user_address/del', params)

// 微信导入收货地址转ID
export const apiAddresshandleRegion = (params) =>
    request.get('user_address/handleRegion', {
        params
    })
// E 地址

// S 收藏
// 获取收藏列表
export const apiCollectLists = (params) =>
    request.get('collect/lists', {
        params
    })

// 添加取消收藏商品
export const apiHandleCollectgoods = (params) => request.post('collect/handleCollectgoods', params)
// E 收藏

// S 会员中心
// 会员等级
export const apiUserlevel = (params) => request.get('user/userlevel')
// E 会员中心

// S 分销

// 申请分销
export const apiDistributionApply = (data) => request.post('distribution/apply', data)

// 申请主页
export const apiDistributionIndex = () => request.get('distribution/index')

// 申请详情
export const apiDistributionApplyDetail = () => request.get('distribution/applyDetail')

// 填写邀请码
export const apiDistributionCode = (data) => request.post('distribution/code', data)

// 分销订单列表
export const apiDistributionOrder = (params) => request.get('distribution/order', { params })

// 月度账单
export const apiDistributionMonthBill = (params) =>
    request.get('distribution/monthBill', { params })

// 月度账单明细
export const apiDistributionMonthDetail = (params) =>
    request.get('distribution/monthDetail', { params })

// 我的粉丝
export const apiDistributionFans = (params) => request.get('distribution/fans', { params })

// E 分销

// S 提现

// 提现配置
export const apiWithdrawGetConfig = (params) => request.get('withdraw/getConfig', { params })

// 提现申请
export const apiWithdrawApply = (params) => request.post('withdraw/apply', params)

// 提现申请列表
export const apiWithdrawLists = (params) => request.get('withdraw/lists', { params })

// 提现详情
export const apiWithdrawDetail = (params) => request.get('withdraw/detail', { params })
// E 提现

// S 签到

// 签到列表
export const apiSignLists = (params) => request.get('sign/lists')

// 签到
export const apiSign = (params) => request.post('sign/sign', params)

// 获取签到说明
export const apiSignGetRemark = (params) => request.get('sign/getRemark')

// 积分明细
export const apiAccountLogLists = (params) => request.get('account_log/lists', { params })
// E 签到

// 聊天记录
export function apiChatRecord(data) {
    return request.get('chat/chatRecord', { params: data })
}

//客服配置
export function apiChatConfig(params) {
    return request.get('chat/chatConfig', { params })
}

//客服商品信息
export function apiChatGoods(params) {
    return request.get('chat/goods', { params })
}

// 微信小程序绑定openid
export const apiMnpAuthLogin = (params) => request.post('login/mnpAuthLogin', params)

// 公众号绑定openid
export const apiOaAuthLogin = (params) => request.post('login/oaAuthLogin', params)

//注销账号
export const apiuserDelete = (params) => request.post('userDelete/delete', params)

//检测注销
export const apiuserDeletecheck = (params) => request.post('userDelete/check', params)

//注册奖励
export const apiupdateRegisterAward = (params) => request.post('user/updateRegisterAward', params)
export function withdrawReceive(params) {
    return request.post('withdraw/receive', params)


//扫码绑定门店(首绑定终身)
export const apiUserBindStore = (params) => request.post('user/bindStore', params)

}
