export const client = 4

export const loginType = {
    SMS: 0,
    ACCOUNT: 1
}


// 短信发送
export const SMSType = {
    // 注册
    REGISTER: 'ZCYZ',
    // 找回密码
    FINDPWD: 'ZHMM',
    // 登陆
    LOGIN: 'YZMDL',
    // 商家申请入驻
    SJSQYZ: 'SJSQYZ',
    // 更换手机号
    CHANGE_MOBILE: 'BGSJHM',
    // 绑定手机号
    BIND: 'BDSJHM'
}

export const FieldType = {
    NONE: '',
    SEX: 'sex',
    NICKNAME: 'nickname',
    AVATAR: 'avatar',
    MOBILE: 'mobile'
}


// 售后状态
export const AfterSaleType = {
    // 售后申请 
    NORMAL: 'apply',
    // 处理中
    HANDLING: 'status_ing',
    // 已处理
    FINISH: 'status_success_fail'
}
