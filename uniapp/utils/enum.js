// 接口code类型
export const APICodeEnum = {
    1: 'success', // 成功
    0: 'fail', // 失败
    '-1': 'redirect', // 重定向
    1020: 'closeShop', // 商城关闭
    10: 'tips' // 提示
}

// 页面状态
export const PageStatusEnum = {
    LOADING: 'loading', // 加载中
    NORMAL: 'normal', // 正常
    ERROR: 'error', // 异常
    EMPTY: 'empty' // 为空
}

// 客户端
export const ClientEnum = {
    MP_WEIXIN: 1, // 微信-小程序
    OA_WEIXIN: 2, // 微信-公众号
    H5: 3, // H5
    PC: 4, // PC
    IOS: 5, // 苹果APP
    ANDROID: 6, //安卓APP
    MP_TOUTIAO: 7 // 头条
}
export const serviceEnum = {
    1: 'mnp',
    2: 'oa',
    3: 'h5',
    4: 'h5',
    5: 'app',
    6: 'app'
}
// 支付状态
export const PaymentStatusEnum = {
    CLOSE: 0, // 取消
    SUCCESS: 1, // 成功
    FAIL: 2 // 失败
}

// 支付方式
export const PayWayEnum = {
    WALLET: 1, // 钱包
    WECHAT: 2, // 微信
    ALIPAY: 3, // 支付宝
    TOUTIAO: 4, //头要
    DOWMLINE: 5 //线下支付
}

// 订单来源
export const OrderSourceEnum = {
    BUY_NOW: 'buy_now', // 立即购买
    CART: 'cart' // 购物车
}

// 订单下单类型 【主要用于下单结算】
export const OrderTypeEnum = {
    GOODS: 0, // 普通商品
    TEAM: 1, // 拼团商品
    SECKILL: 2, // 秒杀商品
    BARGAIN: 3, // 砍价商品
    VIRTUAL: 4, // 虚拟商品
    PRESELL: 5, // 预售商品
    LUCK: 6 // 抽奖商品
}

// 短信发送
export const SMSEnum = {
    REGISTER: 'ZCYZ', // 注册
    FINDPWD: 'ZHMM', // 找回密码
    LOGIN: 'YZMDL', // 登陆
    CHANGE_MOBILE: 'BGSJHM', // 更换手机号
    BIND: 'BDSJHM', // 绑定手机号
    SJSQYZ: 'SJSQYZ' // 商家入驻申请验证
}

// 排序类型
export const SortEnum = {
    NONE: '',
    ASC: 'asc', // 升序
    DESC: 'desc' // 降序
}

// 分销订单状态
export const DistributionOrderEnum = {
    ALL: 0,
    WAIT_RETURN: 1,
    HANDLED: 2,
    INVALED: 3
}

// 字节小程序宿主appname
export const TtAppNameEnum = {
    Toutiao: 'toutiao',
    Douyin: 'douyin',
    news_article_lite: 'toutiao_lite',
    live_stream: 'huoshan',
    XiGua: 'xigua',
    PPX: 'pipixia',
    devtools: 'toutiao'
}
