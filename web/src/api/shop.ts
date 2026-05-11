import request from '@/plugins/axios'
import * as Interface from './type/shop'

// S 装饰

//获取主题配置
export const apiThemeConfig = (params: { type: number }) =>
    request.get('/theme.DecorateThemeConfig/getContent', { params })

// 设置主题配置
export const apiThemeConfigSet = (data: any) => request.post('/theme.DecorateThemeConfig/setContent', data)

// 保存装修页面
export const apiThemePageLists = (params: { name: number }) => request.get('/theme.DecorateThemePage/lists', { params })

// 保存装修页面
export const apiThemePageAdd = (data: any) => request.post('/theme.DecorateThemePage/add', data)

// 编辑装修页面
export const apiThemePageEdit = (data: any) => request.post('/theme.DecorateThemePage/edit', data)

// 编辑装修Pc页面
export const apiPcThemePageEdit = (data: any) => request.post('/theme.PcDecorateThemePage/setpage', data)

// 编辑装修Pc页面
export const apiPcThemePageDetail = (params: { id?: number; type?: number }) =>
    request.get('/theme.PcDecorateThemePage/getPage', { params })

// 装修页面详情
export const apiThemePageDetail = (params: { id?: number; type?: number }) =>
    request.get('/theme.DecorateThemePage/getPage', { params })

// 设置装修页面为主页
export const apiThemePageSetHome = (data: { id: number }) => request.post('/theme.DecorateThemePage/setHome', data)

// 删除装修页面
export const apiThemePageDel = (data: { id: number }) => request.post('/theme.DecorateThemePage/del', data)

// 页面模板
export const apiSystemThemePage = (params: any) => request.get('/theme.SystemThemePage/lists', { params })

// 店铺主页
export const apiThemePageIndex = () => request.get('/theme.DecorateThemePage/index')

// PC主页
export const apiPcThemePageIndex = () => request.get('/theme.PcDecorateThemePage/index')

// E 装饰

/** S 素材管理 **/

// 添加文件分类
export const apiFileAddCate = (data: Interface.FileAddCate_Req) => request.post('/file/addCate', data)

// 编辑文件分类
export const apiFileEditCate = (data: Interface.FileEditCate_Req) => request.post('/file/editCate', data)

// 删除文件分类
export const apiFileDelCate = (data: { id: number }) => request.post('/file/delCate', data)

// 文件分类列表
export const apiFileListCate = (params: any) => request.get('/file/listCate', { params })

// 文件列表
export const apiFileList = (params: any) => request.get('/file/lists', { params })

// 文件删除
export const apiFileDel = (data: { ids: any[] }) => request.post('/file/delete', data)

// 文件移动
export const apiFileMove = (data: { ids: any[]; cid: number }) => request.post('/file/move', data)

// 文件重命名
export const apiFileRename = (data: { id: number; name: string }) => request.post('file/rename', data)

/** E 素材管理 **/
