import * as Common from '../common'

export interface CouponSurvey_Res extends Common.Indexes {
    //基本概况信息
    base: {
        receive_num: number //领取数量
        today_receive_num: number //今天领取数量
        use_num: number //使用数量
        today_use_num: number //今天使用数量
    }
    // 优惠券领取排行榜
    sort_receive: [
        {
            id: number
            name: string
            num: number
        }
    ]
    // 优惠券使用排行榜
    sort_use: [
        {
            id: number
            name: string
            num: number
        }
    ]
}
