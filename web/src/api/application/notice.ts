import request from '@/plugins/axios'
import * as Interface from '@/api/application/notice.d'
/** S 商城公告 **/

// 添加公告
export const apiNoticeAdd = (params: Interface.notice_add) => request.post('/shop_notice.shop_notice/add', params)

// 公告列表
export const apiNoticeLists = (params: Interface.notice_lists) =>
    request.get('/shop_notice.shop_notice/lists', { params })

// 添加公告
export const apiNoticeEdit = (params: Interface.notice_edit) => request.post('/shop_notice.shop_notice/edit', params)

// 修改公告状态
export const apiNoticeStatus = (params: Interface.notice_status) =>
    request.post('/shop_notice.shop_notice/status', params)

// 公告详情
export const apiNoticeDetail = (params: Interface.notice_detail) =>
    request.get('/shop_notice.shop_notice/detail', { params })

// 删除公告
export const apiNoticeDel = (params: Interface.notice_del) => request.post('/shop_notice.shop_notice/del', params)

/** E 商城公告 **/
