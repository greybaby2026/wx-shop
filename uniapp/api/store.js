import request from '@/utils/request'

// 商城首页
export const apiIndex = () => request.get('DecorateTheme/index')

// 商城微页面
export const apiMiniPage = (params) =>
    request.get('DecorateTheme/getIndexPage', {
        params
    })

// 商城主题配置
export const apiDecorateConfig = () => request.get('decorateTheme/getConfig')

// 商城个人中心或者分类
export const apiGetPage = (params) =>
    request.get('decorateTheme/getPage', {
        params
    })

// 获取购物车列表
export const apiCartLists = () => request.get('cart/lists')

// 购物车添加商品
export const apiCartAdd = (params) => request.post('cart/add', params)

// 删除购物车商品
export const apiCartDel = (params) => request.post('cart/del', params)

// 购物车中商品数量的修改
export const apiCartCountChange = (params) => request.post('cart/change', params)

// 购物车中选中状态
export const apiCartSelectChange = (params) => request.post('cart/selected', params)

// 购物车中数量
export const apiCartNum = () => request.get('cart/num')

// 自提门店列表
export const apiSelffetchStore = (params) =>
    request.get('selffetch_shop/lists', {
        params
    })

/** S 优惠券 **/
// 领取优惠券
export const apiCouponGet = (params) => request.post('coupon/receive', params)

// 领券中心
export const apiCouponList = (params) => request.get('coupon/lists', { params })

// 优惠券列表【订单结算】
export const apiCouponGoods = (params) => request.post('coupon/orderCoupon', params)
/** E 优惠券 **/

// 消息中心
export const apiNoticeGet = () => request.get('notice/index')

// 消息列表
export const apiNoticeListGet = (params) => request.get('notice/lists', { params })

// 未读消息
export const apiNoticeUnread = () => request.get('notice/unread')

// 标记已读
export const apiNoticeRead = (params) => request.post('notice/read', params)

// 商城公告
export const apiShopNoticeList = () => request.get('shop_notice/lists')

// 商城公告
export const apiShopNoticeDetail = (params) => request.get('shop_notice/detail', { params })

// 在线客服
export const apiServiceConfig = () => request.get('service/getConfig')

/** E 商城资讯 **/
// 商城资讯/帮助列表
export const apiArticleLists = (params) => request.get('article/lists', { params })

// 商城资讯/帮助中心分类
export const apiArticleCategoryLists = () => request.get('article/articleCategoryLists')

// 商城资讯/帮助中心详情
export const apiArticleDatail = (params) => request.get('article/detail', { params })
/** E 商城资讯 **/

// 足迹气泡
export const apiFootprint = () => request.get('footprint/lists')
//清空失效商品
export const apiclearAll = (params) => request.post('cart/del_ids', params)
//修改商品sku
export const apieditgoodsitem = (params) => request.post('cart/edit_goods_item', params)
export const apiregisterAward = () => request.get('registerAward/getConfig')
