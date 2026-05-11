import request from '@/plugins/axios'

/** S 数据概况 **/
// 获取数据概况
export const apiDataCenter = (): Promise<any> => request.get('/data.center/index')
/** E 数据概况 **/

/** S 流量分析 **/
// 获取流量分析
export const apiDataCenterVisit = (params: any): Promise<any> => request.get('/data.center/trafficAnalysis', { params })
/** E 流量分析 **/

// 获取用户分析
export const apiUserAnalysis = (params: any): Promise<any> => request.get('/data.center/userAnalysis', { params })

// 获取交易分析
export const apiTransactionAnalysis = (params: any): Promise<any> =>
    request.get('/data.center/transactionAnalysis', { params })

// 获取商品分析
export const apiGoodsAnalysis = (params: any): Promise<any> => request.get('/data.center/goodsAnalysis', { params })

// 获取商品排行
export const apiGoodsTop = (params: any): Promise<any> => request.get('/data.center/goodsTop', { params })
