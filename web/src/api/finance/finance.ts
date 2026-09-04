import request from '@/plugins/axios'

/** S 用户提现 **/
// 提现列表
export const apiWithdrawLists = (params: any) => request.get('/withdraw.withdraw/lists', { params })

// 提现详情
export const apiWithdrawDetail = (params: any) => request.get('/withdraw.withdraw/detail', { params })

// 审核通过
export const apiWithdrawPass = (data: any) => request.post('/withdraw.withdraw/pass', data)
// 审核拒绝
export const apiWithdrawRefuse = (data: any) => request.post('/withdraw.withdraw/refuse', data)

// 转账成功
export const apiTransferSuccess = (data: any) => request.post('/withdraw.withdraw/transferSuccess', data)
// 转账失败
export const apiTransferFail = (data: any) => request.post('/withdraw.withdraw/transferFail', data)

// 查询结果
export const apiWithdrawSearch = (params: any) => request.get('/withdraw.withdraw/search', { params })
/** E 用户提现 **/

/** S 余额明细 **/
// 查询资金记录(余额/积分/成长值)
export const apiAccountLog = (params: any) => request.get('/account_log/lists', { params })

// 获取搜索框变动类型数据
export const apiChangeTypeList = () => request.get('/account_log/getChangeType')

// 获取不可提现余额变动类型
export const apiBnwChangeType = () => request.get('/account_log/getBnwChangeType')

// 获取佣金变动类型
export const apiBwChangeType = () => request.get('/account_log/getBwChangeType')

/** E 余额明细 **/

/** S 财务概况 **/
// 财务概览
export const apiFinanceDataCenter = () => request.get('/finance.finance/dataCenter')
/** E 财务概况 **/

/** S 积分明细 **/
// 获取积分类型
export const apiIntegralChangeType = () => request.get('/account_log/getIntegralChangeType')

// // 获取搜索框变动类型数据
// export const apiChangeTypeList = () => request.get('/account_log/getChangeType')
/** E 积分明细 **/

export const apiActChangeType = () => request.get('/account_log/getActChangeType')
