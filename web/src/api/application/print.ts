import request from '@/plugins/axios'

// 打印机列表
export const apiPrintLists = (params: any) => request.get('/printer.printer/printerLists', { params })

// 打印机详情
export const apiPrintDetail = (params: any) => request.get('/printer.printer/printerDetail', { params })

// 打印机类型
export const apiPrintType = (params: any) => request.get('/printer.printer/getPrinterType', { params })

// 添加打印机
export const apiAddPrint = (params: any) => request.post('/printer.printer/addPrinter', params)

// 编辑打印机
export const apiEditPrint = (params: any) => request.post('/printer.printer/editPrinter', params)

// 删除打印机
export const apiDelPrint = (params: any) => request.post('/printer.printer/deletePrinter', params)

// 测试打印机
export const apiTestPrint = (params: any) => request.post('/printer.printer/testPrinter', params)

// 订单打印
export const apiOrderPrint = (params: any) => request.post('/order.order/orderPrint', params)

// 自动打印状态切换
export const apiAutoPrint = (params: any) => request.post('/printer.printer/autoPrint', params)

// 模版列表
export const apiTemplateLists = (params: any) => request.get('/printer.printer/printerTemplateLists', { params })

// 添加模板
export const apiAddTemplate = (params: any) => request.post('/printer.printer/addTemplate', params)

// 编辑模板
export const apiEditTemplate = (params: any) => request.post('/printer.printer/editTemplate', params)

// 删除模板
export const apiDelTemplate = (params: any) => request.post('/printer.printer/deleteTemplate', params)

// 模版详情
export const apiTemplateDetail = (params: any) => request.get('/printer.printer/templateDetail', { params })
