import request from '@/plugins/axios'
// 获取足迹气泡列表
export const apiFootprintList = () => request.get('/footprint/lists')

// 足迹气泡状态
export const apiFootprintStatus = (data: any) => request.post('/footprint/status', data)

// 设置足迹气泡
export const apiFootprintEdit = (data: any): Promise<any> => request.post('/footprint/setConfig', data)

// 获取足迹气泡设置
export const apiFootprintSetting = (): Promise<any> => request.get('/footprint/getConfig')
