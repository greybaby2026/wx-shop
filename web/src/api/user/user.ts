import request from '@/plugins/axios'
import * as Interface from '@/api/user/user.d.ts'

/** S 用户等级 **/
// 获取用户等级列表
export const apiUserLevelList = (params: any): Promise<Interface.LevelLists_Res> =>
    request.get('/user.user_level/lists', { params })

// 新增用户等级
export const apiUserLevelAdd = (data: Interface.UserLevelAdd_Req): Promise<any> =>
    request.post('/user.user_level/add', data)

// 获取用户等级详情
export const apiUserLevelDetail = (params: Interface.UserLevelDetail_Req): Promise<Interface.UserLevelDetail_Res> =>
    request.get('/user.user_level/detail', { params })

// 编辑用户等级
export const apiUserLevelEdit = (data: Interface.UserLevelEdit_Req): Promise<any> =>
    request.post('/user.user_level/edit', data)

// 删除用户等级
export const apiUserLevelDel = (data: Interface.UserLevelDel_Req): Promise<any> =>
    request.post('/user.user_level/del', data)
/** E 用户等级 **/

/** S 用户标签 **/
// 获取用户标签列表
export const apiUserLabelList = (params: Interface.LabelLists_Req): Promise<Interface.LabelLists_Res> =>
    request.get('/user.user_label/lists', { params })

// 新增用户标签
export const apiUserLabelAdd = (data: Interface.LabelAdd_Req): Promise<any> =>
    request.post('/user.user_label/add', data)

// 获取用户标签详情
export const apiUserLabelDetail = (params: Interface.LabelDetail_Req): Promise<Interface.LabelDetail_Res> =>
    request.get('/user.user_label/detail', { params })

// 编辑用户标签详情
export const apiUserLabelEdit = (data: Interface.LabelEdit_Req): Promise<any> =>
    request.post('/user.user_label/edit', data)

// 删除用户标签
export const apiUserLabelDel = (data: Interface.LabelDel_Req): Promise<any> =>
    request.post('/user.user_label/del', data)
/** E 用户标签 **/

/** S 用户管理 **/
// 用户列表
export const apiUserList = (params: any): Promise<any> => request.get('/user.user/lists', { params })
// 用户搜索条件列表
export const apiUserSearchList = (): Promise<any> => request.get('/user.user/otherList')
// 用户详情
export const apiUserDetail = (params: any): Promise<any> => request.get('/user.user/detail', { params })
// 更新用户基本信息
export const apiUserSetInfo = (params: any): Promise<any> => request.post('/user.user/setInfo', params)
// 批量设置用户标签
export const apiUserSetLabel = (params: any): Promise<any> => request.post('/user.user/setLabel', params)
// 更新用户标签
export const apiUserSetUserLabel = (params: any): Promise<any> => request.post('/user.user/setUserLabel', params)
// 调整用户钱包
export const apiUserSetAdjustUserWallet = (params: any): Promise<any> =>
    request.post('/user.user/adjustUserWallet', params)
// 获取用户粉丝
export const apiUserGetFans = (params: any): Promise<any> => request.get('/user.user/getFans', { params })
/** E 用户管理 **/

/** S 用户概述 **/
// 用户概况
export const apiUserIndex = (): Promise<any> => request.get('/user.user/index')
/** E 用户管理 **/

/** S 我的邀请 **/
// 用户信息(用于我的邀请人列表页)
export const apiUserInfo = (params: any): Promise<any> => request.get('/user.user/info', { params })
// 我邀请的人列表
export const apiUserInviterLists = (params: any): Promise<any> => request.get('/user.user/userInviterLists', { params })
/** E 我的邀请 **/

/** S 调整上级分销商 **/
// 调整上级分销商
export const apiUseradjustFirstLeader = (params: any): Promise<any> =>
    request.post('/user.user/adjustFirstLeader', params)
// 选择用户列表
export const apiSelectUserLists = (params: any): Promise<any> => request.get('/user.user/selectUserLists', { params })
/** E 调整上级分销商 **/
