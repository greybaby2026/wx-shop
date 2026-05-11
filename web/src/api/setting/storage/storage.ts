import request from '@/plugins/axios'
import * as Interface from '@/api/setting/system_maintain/system_maintain.d.ts'

/** S 存储设置 **/
// 获取存储引擎列表
export const apiStorageList = (): Promise<any> => request.get('/settings.shop.Storage/lists')

// 获取存储配置信息
export const apiStorageIndex = (params: any): Promise<any> => request.get('/settings.shop.Storage/index', { params })

// 更新配置
export const apiStorageSetup = (params: any): Promise<any> => request.post('/settings.shop.Storage/setup', params)

// 切换默认存储引擎
export const apiStorageChange = (params: any): Promise<any> => request.post('/settings.shop.Storage/change', params)
/** E 存储设置 **/
