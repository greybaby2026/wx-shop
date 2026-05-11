import * as Common from '@/api/common'

/** S 用户等级 **/
// 获取用户等级列表
export interface LevelLists_Res extends Common.Paging_Res {
    lists: [
        {
            id: number // id
            name: string // 等级名称
            level: number // 级别：1-系统默认（不显示删除按钮）
            image: string // 等级图标
            num: number // 用户数
        }
    ]
}

// 新增用户等级
export interface UserLevelAdd_Req {
    name: string // 等级名称
    rank: number // 级别
    image?: string // 等级图标
    background_image?: string // 背景图标
    remark: string // 描述
    level_discount: number // 设置等级折扣：0-无等级折扣；1-参与等级折扣
    discount: number // 等级折扣
    condition_type: number // 设置等级条件：0-满足以下任意条件；1-满足以下全部条件
    single_money: any // 单笔消费金额
    total_money: any // 累计消费金额
    total_num: any // 累计消费次数
}

// 获取用户等级详情
export interface UserLevelDetail_Req {
    id: number // id
}
export interface UserLevelDetail_Res extends Common.Indexes {
    name: string // 等级名称
    level: number // 级别
    image: string // 图标
    background_image: string // 背景图
    remark: string // 备注
    level_discount: number // 设置等级折扣：0-无等级折扣；1-参与等级折扣
    discount: number // 等级折扣
    condition_type: 0 | 1 // 设置等级条件：0-满足以下任意条件；1-满足以下全部条件
    single_money: number // 单笔消费金额（空表示没选中）
    total_money: number // 累计消费金额（空表示没选中）
    total_num: number // 累计消费次数 （空表示没选中）
}

// 编辑用户等级
export interface UserLevelEdit_Req {
    id: number // id
    name: string // 等级名称
    rank: number // 级别
    image: string // 图标
    background_image: string // 背景图
    remark: string // 备注
    level_discount: number // 设置等级折扣：0-无等级折扣；1-参与等级折扣
    discount: number // 等级折扣
    condition_type: 0 | 1 // 设置等级条件：0-满足以下任意条件；1-满足以下全部条件
    single_money: any // 单笔消费金额（空表示没选中）
    total_money: any // 累计消费金额（空表示没选中）
    total_num: any // 累计消费次数 （空表示没选中）
}

// 删除用户等级
export interface UserLevelDel_Req {
    id: number // id
}
/** E 用户等级 **/

/** S 用户标签 **/
// 获取用户标签列表
export interface LabelLists_Req extends Common.Paging_Req {
    name?: string // 名称
}
export interface LabelLists_Res extends Common.Paging_Res {
    lists: [
        {
            id: number // id
            name: string // 名称
            label_type: 0 | 1 // 标签类型：0-手动标签；1-自动标签
            num: number // 用户数
        }
    ]
}

// 新增用户标签
export interface LabelAdd_Req {
    name: string // 名称
    remark: string // 描述
    label_type: 0 | 1 // 标签类型：0-手动标签；1-自动标签
}

// 编辑用户标签
export interface LabelEdit_Req {
    id: number // id
    name: string // 名称
    remark: string // 描述
    label_type: 0 | 1 // 标签类型：0-手动标签；1-自动标签
}

// 获取用户标签详情
export interface LabelDetail_Req {
    id: number // id
}
export interface LabelDetail_Res extends Common.Indexes {
    name: string // 名称
    remark: string // 描述
    label_type: 0 | 1 // 标签类型：0-手动标签；1-自动标签
}

// 删除用户标签
export interface LabelDel_Req {
    ids: Array // id
}
/** E 用户标签 **/
