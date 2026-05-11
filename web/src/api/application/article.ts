import request from '@/plugins/axios'
/** S 文章资讯 **/

// 文章/帮助列表
export const apiArticleLists = (params: any) => request.get('/article/lists', { params })

// 删除文章/帮助
export const apiArticleDelete = (params: any) => request.post('/article/delete', params)

// 获取文章/帮助详情
export const apiArticleDetail = (params: any) => request.get('/article/detail', { params })

// 编辑文章/帮助
export const apiArticleEdit = (params: any) => request.post('/article/edit', params)

// 添加文章/帮助
export const apiArticleAdd = (params: any) => request.post('/article/add', params)

// 修改文章/帮助状态
export const apiArticleIsShow = (params: any) => request.post('/article/isShow', params)

// 分类列表
export const apiArticleCategoryLists = (params: any) => request.get('/article_category/lists', { params })

// 删除分类
export const apiArticleCategoryDelete = (params: any) => request.post('/article_category/delete', params)

// 获取分类详情
export const apiArticleCategoryDetail = (params: any) => request.get('/article_category/detail', { params })

// 编辑分类
export const apiArticleCategoryEdit = (params: any) => request.post('/article_category/edit', params)

// 添加分类
export const apiArticleCategoryAdd = (params: any) => request.post('/article_category/add', params)

// 修改分类状态
export const apiArticleCategoryIsShow = (params: any) => request.post('/article_category/isShow', params)
/** E 文章资讯 **/
