// 公告添加
export interface notice_add {
    name: string //	是	string	公告标题
    synopsis: string //  否	string	公告简介
    image: string //  否	string	公告封面图
    content: string //	是	string	公告内容
    sort: number //  否	int	公告排序
    status: number //	是	int	公告状态:0-隐藏;1-显示
}

// 公告列表
export interface notice_lists {
    name: string //	string	公告标题
    views: number //	number	浏览量
    likes: number //	number	点赞量
    sort: number // 	int	公告排序
    status: number //	int	公告状态:0-隐藏;1-显示
    create_time: string //  string	创建时间
}

// 公告编辑
export interface notice_edit {
    id: number //	是	int	公告ID
    name: string //	是	string	公告标题
    synopsis: string //  否	string	公告简介
    image: string //  否	string	公告封面图
    content: string //	是	string	公告内容
    sort: number //  否	int	公告排序
    status: number //	是	int	公告状态:0-隐藏;1-显示
}

// 公告状态修改
export interface notice_status {
    id: number //	是	int	公告ID
    status: number //	是	int	公告状态:0-隐藏;1-显示
}

// 公告删除
export interface notice_del {
    id: number //	是	int	公告ID
}

// 公告详情
export interface notice_detail {
    id: number //	是	int	公告ID
}
