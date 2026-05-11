import request from '@/plugins/axios'

// S 概览
// 分销概览
export const apiDistributionData = () => request.get('/distribution.distribution_data/dataCenter')

// 分销商收入排行榜接口
export const apiTopMemberEarnings = (params: any) =>
    request.get('/distribution.distribution_data/topMemberEarnings', { params })

// 分销商下级人数排行榜接口
export const apiTopMemberFans = (params: any) =>
    request.get('/distribution.distribution_data/topMemberFans', { params })

// E 概览

// S 分销商品

// 分销商品列表
export const apiDistributionGoodsLists = (params: any) =>
    request.get('/distribution.distribution_goods/lists', { params })

// 参不参与分销
export const apiDistributionGoodsJoin = (id: any) => request.post('/distribution.distribution_goods/join', id)

// 分销商品详情
export const apiDistributionGoodsDetails = (params: any) =>
    request.get('/distribution.distribution_goods/detail', { params })

// 分销商品设置
export const apiDistributionGoodsSet = (params: any) => request.post('/distribution.distribution_goods/set', params)

// E 分销商品

// S 分销会员

// 分销会员列表
export const apiDistributionMemberLists = (params: any) =>
    request.get('/distribution.distribution_member/lists', { params })

// 分销会员详情
export const apiDistributionMemberDetails = (params: any) =>
    request.get('/distribution.distribution_apply/detail', { params })

// 分销会员申请列表
export const apiDistributionApplyLists = (params: any) =>
    request.get('/distribution.distribution_apply/lists', { params })

// 分销会员申请拒绝
export const apiDistributionApplyPass = (params: any) => request.post('/distribution.distribution_apply/pass', params)

// 分销会员申请拒绝
export const apiDistributionApplyRefuse = (params: any) =>
    request.post('/distribution.distribution_apply/refuse', params)

// 开通分销
export const apiDistributionOpen = (params: any) => request.post('/distribution.distribution_member/open', params)

// 冻结/解冻
export const apiDistributionfreeze = (params: any) => request.post('/distribution.distribution_member/freeze', params)

// 调整等级获取
export const apiDistributionAdjustLevelInfo = (params: any) =>
    request.get('/distribution.distribution_member/adjustLevelInfo', { params })

// 调整等级设置
export const apiDistributionAdjustLevel = (params: any) =>
    request.post('/distribution.distribution_member/adjustLevel', params)

// 获取分销商详情
export const apiDistributionDetail = (params: any) =>
    request.get('/distribution.distribution_member/detail', { params })

// 查看下级
export const apiDistributionGetFans = (params: any) =>
    request.get('/distribution.distribution_member/getFans', { params })

// 查看下级
export const apiDistributionGetFansLists = (params: any) =>
    request.get('/distribution.distribution_member/getFansLists', { params })

// E 分销会员

// S 分销等级

// 分销等级列表
export const apiDistributionGradeLists = (params: any) => request.get('/distribution.distribution_level/lists', params)

// 分销等级
export const apiDistributionGradeDetail = (params: any) =>
    request.get('/distribution.distribution_level/detail', { params })

// 等级添加
export const apiDistributionGradeEdit = (params: any) => request.post('/distribution.distribution_level/edit', params)

// 等级添加
export const apiDistributionGradeAdd = (params: any) => request.post('/distribution.distribution_level/add', params)

// 等级删除
export const apiDistributionGradeDel = (params: any) => request.post('/distribution.distribution_level/delete', params)

// E 分销等级

// S 分销订单

// 分销等级
export const apiDistributionOrdersLists = (params: any) =>
    request.get('/distribution.distribution_order_goods/lists', { params })

// E 分销订单

// S 分销设置

// 分销等级
export const apiDistributionDetails = (params: any) =>
    request.get('/distribution.distribution_config/getConfig', { params })

// 分析设置
export const apiDistributionSet = (params: any) => request.post('/distribution.distribution_config/setConfig', params)

// E 分销设置
