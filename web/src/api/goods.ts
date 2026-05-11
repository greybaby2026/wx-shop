import request from '@/plugins/axios'

// S 商品管理
// 商品列表
export const apiGoodsAdd = (data: any) => request.post('/goods.goods/add', data)

// 商品编辑
export const apiGoodsEdit = (data: any) => request.post('/goods.goods/edit', data)

// 商品列表
export const apiGoodsLists = (params: any) => request.get('/goods.goods/lists', { params })
// 商品列表
export const apiGoodsCommonLists = (params: any) => request.get('/goods.goods/commonLists', { params })

// 商品状态
export const apiGoodsStatus = (data: any) => request.post('/goods.goods/status', data)

// 删除商品
export const apiGoodsDel = (data: any) => request.post('/goods.goods/del', data)

// 移动分类
export const apiMoveCategory = (data: any) => request.post('/goods.goods/changeCategory', data)

// 商品排序
export const apiGoodsSort = (data: any) => request.post('/goods.goods/sort', data)

// 商品详情
export const apiGoodsDetail = (id: number) => request.get('/goods.goods/detail', { params: { id } })

// 商品分类/单位/供货商/品牌/运费模板列表
// 商品列表
export const apiGoodsOtherList = (params: any) => request.get('/goods.goods/otherList', { params })

// 修改商品名称
export const apiGoodsRename = (data: any) => request.post('goods.goods/rename ', data)

// E 商品管理

// S 品牌管理
// 新增品牌
export const apiBrandAdd = (data: any) => request.post('/goods.goods_brand/add', data)

// 品牌列表
export const apiBrandLists = (params: any) => request.get('/goods.goods_brand/lists', { params })

// 修改品牌状态
export const apiBrandStatus = (data: any) => request.post('/goods.goods_brand/status', data)

// 删除品牌
export const apiBrandDel = (data: any) => request.post('/goods.goods_brand/del', data)

// 商品详情
export const apiBrandDetail = (id: number) => request.get('/goods.goods_brand/detail', { params: { id } })

// 删除品牌
export const apiBrandEdit = (data: any) => request.post('/goods.goods_brand/edit', data)

// E 品牌管理

// S 供应商管理
// 供应商分类添加
export const apiSupplierCategoryAdd = (data: any) => request.post('/goods.goods_supplier_category/add', data)

// 供应商分类列表
export const apiSupplierCategoryLists = (params: any) => request.get('goods.goods_supplier_category/lists', { params })

// 供应商分类删除
export const apiSupplierCategoryDel = (id: number) => request.post('goods.goods_supplier_category/del', { id })

// 供应商分类编辑
export const apiSupplierCategoryEdit = (data: any) => request.post('goods.goods_supplier_category/edit', data)

// 添加供应商
export const apiSupplierAdd = (data: any) => request.post('/goods.goods_supplier/add', data)

// 供应商列表
export const apiSupplierLists = (params: any) => request.get('/goods.goods_supplier/lists', { params })

// 供应商删除
export const apiSupplierDel = (id: number) => request.post('goods.goods_supplier/del', { id })

// 供应商详情
export const apiSupplierDetail = (id: number) => request.get('/goods.goods_supplier/detail', { params: { id } })

// 供应商编辑
export const apiSupplierEdit = (data: any) => request.post('/goods.goods_supplier/edit', data)

// E 供应商管理

// S 商品分类
// 商品分类添加
export const apiCategoryAdd = (data: any) => request.post('/goods.goods_category/add', data)

// 商品分类列表
export const apiCategoryLists = (params: any) => request.get('/goods.goods_category/lists', { params })

// 商品分类列表
export const apiCategoryCommonLists = (params: any) => request.get('/goods.goods_category/commonLists', { params })

// 修改分类状态
export const apiCategoryStatus = (data: any) => request.post('/goods.goods_category/status', data)

// 分类删除
export const apiCategoryDel = (id: number) => request.post('goods.goods_category/del', { id })

// 商品分类编辑
export const apiCategoryEdit = (data: any) => request.post('/goods.goods_category/edit', data)

// 商品分类编辑
export const apiCategoryDetail = (id: number) => request.get('/goods.goods_category/detail', { params: { id } })

// E 商品分类

// S 商品单位
// 商品单位新增
export const apiUnitAdd = (data: any) => request.post('/goods.goods_unit/add', data)

// 商品单位列表
export const apiUnitLists = (params: any) => request.get('/goods.goods_unit/lists', { params })

// 商品单位删除
export const apiUnitDel = (id: number) => request.post('goods.goods_unit/del', { id })

// 商品单位编辑
export const apiUnitEdit = (data: any) => request.post('/goods.goods_unit/edit', data)

// E 商品单位

// S 商品评价

// 商品评价列表
export const apiGoodsCommentLists = (params: any) => request.get('goods.goods_comment/lists', { params })

// 商品评价删除
export const apiGoodsCommentDel = (data: any) => request.post('goods.goods_comment/del', data)

// 商品评价商家回复
export const apiGoodsCommentReply = (data: any) => request.post('goods.goods_comment/reply', data)

// 商品评价状态
export const apiGoodsCommentStatus = (data: any) => request.post('/goods.goods_comment/status', data)

// 虚拟评价列表
export const apiGoodsCommentAssistantLists = (params: any) =>
    request.get('goods.goods_comment_assistant/lists', { params })

// 添加虚拟评价
export const apiGoodsCommentAssistantAdd = (data: any) => request.post('goods.goods_comment_assistant/add', data)

// E 商品评价

// 保障服务
export const apigoodsServiceGuaranteelists = (data?: any) => request.get('goods.goodsServiceGuarantee/lists', data)
export const apigoodsServiceGuaranteetAdd = (data: any) => request.post('goods.goodsServiceGuarantee/add', data)
export const apigoodsServiceGuaranteetDel = (data: any) => request.post('goods.goodsServiceGuarantee/delete', data)
export const apigoodsServiceGuaranteetEdit = (data: any) => request.post('goods.goodsServiceGuarantee/edit', data)
//发货模版
export const GoodsDeliveryTemplate = () => request.get('goods.GoodsDeliveryTemplate/lists')
export const addGoodsDeliveryTemplate = (data: any) => request.post('goods.GoodsDeliveryTemplate/add', data)

export const editGoodsDeliveryTemplate = (data: any) => request.post('goods.GoodsDeliveryTemplate/edit', data)

export const delGoodsDeliveryTemplate = (data: any) => request.post('goods.GoodsDeliveryTemplate/delete', data)
