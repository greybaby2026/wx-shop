import request from '@/plugins/axios'

/** S 物流接口 **/

// 获取物流接口
export const apideliveryWay = () => request.get('settings.delivery.delivery_way/getConfig')

// 设置物流接口
export const apideliveryWayset = (data: any) => request.post('settings.delivery.delivery_way/setConfig', data)

/** S 物流接口 **/

/** S 快递公司 **/

// 新增快递公司
export const apiExpressAdd = (data: any) => request.post('/settings.delivery.express/add', data)

// 编辑快递公司
export const apiExpressEdit = (data: any) => request.post('/settings.delivery.express/edit', data)

// 删除快递公司
export const apiExpressDel = (data: any) => request.post('/settings.delivery.express/del', data)

// 快递公司详情
export const apiExpressDetail = (params: any) => request.get('/settings.delivery.express/detail', { params })

// 快递公司列表
export const apiExpressLists = (params: any) => request.get('/settings.delivery.express/lists', { params })

/** E 快递公司 **/

/** S 物流接口 **/

// 获取物流接口
export const apiLogisticsConfig = () => request.get('settings.delivery.logistics_config/getLogisticsConfig')

// 设置物流接口
export const apiSetLogisticsConfig = (data: any) =>
    request.post('settings.delivery.logistics_config/setLogisticsConfig', data)

/** S 物流接口 **/

/** S 运费模板 **/

// 添加运费模板
export const apiFreightAdd = (data: any) => request.post('settings.delivery.freight/add', data)

// 编辑运费模板
export const apiFreightEdit = (data: any) => request.post('settings.delivery.freight/edit', data)

// 运费模板列表
export const apiFreightLists = (params: any) => request.get('settings.delivery.freight/lists', { params })

// 运费模板详情
export const apiFreightDetail = (params: any) => request.get('settings.delivery.freight/detail', { params })

// 删除运费模板
export const apiFreightDel = (data: any) => request.post('settings.delivery.freight/del', data)
/** S 运费模板 **/
