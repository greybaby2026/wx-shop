import Main from '@/layout/main.vue'
import Blank from '@/layout/blank.vue'
const routes = [
    {
        path: '/marketing',
        name: 'marketing',
        meta: { title: '营销' },
        redirect: '/marketing/index',
        component: Main,
        children: [
            {
                path: '/marketing/index',
                name: 'marketing_index',
                meta: {
                    title: '营销中心',
                    parentPath: '/marketing',
                    icon: 'icon_yingxiaowf',
                    permission: ['view']
                },
                component: () => import('@/views/marketing/index.vue')
            }
        ]
    },
    // ----日历签到
    {
        path: '/calendar',
        name: 'calendar',
        meta: {
            title: '签到概览',
            hidden: true,
            moduleName: 'calendar'
        },
        redirect: '/calendar/survey',
        component: Main,
        children: [
            {
                path: '/calendar/survey',
                name: 'survey',
                meta: {
                    title: '签到概览',
                    parentPath: '/marketing',
                    moduleName: 'calendar',
                    icon: 'icon_coupons_data',
                    permission: ['view']
                },
                component: () => import('@/views/marketing/calendar/survey.vue')
            },
            {
                path: '/calendar/rule',
                name: 'rule',
                meta: {
                    title: '签到规则',
                    parentPath: '/marketing',
                    moduleName: 'calendar',
                    icon: 'icon_qiandao_guize',
                    permission: ['view']
                },
                component: () => import('@/views/marketing/calendar/rule.vue')
            },
            {
                path: '/calendar/record',
                name: 'record',
                meta: {
                    title: '签到记录',
                    parentPath: '/marketing',
                    moduleName: 'calendar',
                    icon: 'icon_qiandao_jilu',
                    permission: ['view']
                },
                component: () => import('@/views/marketing/calendar/record.vue')
            }
        ]
    },
    // 优惠券
    {
        path: '/coupon',
        name: 'coupon',
        meta: {
            title: '优惠券',
            hidden: true,
            moduleName: 'coupon'
        },
        redirect: '/coupon/survey',
        component: Main,
        children: [
            {
                path: '/coupon/survey',
                name: 'coupon_survey',
                meta: {
                    hidden: true,
                    title: '优惠券概览',
                    parentPath: '/marketing',
                    icon: 'icon_coupons_data',
                    moduleName: 'coupon',
                    permission: ['view']
                },
                component: () => import('@/views/marketing/coupon/survey.vue')
            },
            {
                path: '/coupon/lists',
                name: 'coupon_lists',
                meta: {
                    title: '优惠券',
                    parentPath: '/marketing',
                    icon: 'icon_coupons',
                    moduleName: 'coupon',
                    permission: ['view']
                },
                component: () => import('@/views/marketing/coupon/lists.vue')
            },
            {
                path: '/coupon/receive_record',
                name: 'receive_record',
                meta: {
                    title: '领取记录',
                    parentPath: '/marketing',
                    icon: 'icon_order_guanli',
                    moduleName: 'coupon',
                    permission: ['view']
                },
                component: () => import('@/views/marketing/coupon/receive_record.vue')
            },
            {
                path: '/coupon/edit',
                name: 'coupon_edit',
                meta: {
                    hidden: true,
                    title: '优惠券',
                    parentPath: '/marketing',
                    prevPath: '/coupon/lists',
                    moduleName: 'coupon'
                },
                component: () => import('@/views/marketing/coupon/edit.vue')
            },
            {
                path: '/coupon/grant',
                name: 'coupon_grant',
                meta: {
                    hidden: true,
                    title: '发放优惠券',
                    parentPath: '/marketing',
                    prevPath: '/coupon/lists',
                    moduleName: 'coupon'
                },
                component: () => import('@/views/marketing/coupon/grant.vue')
            }
        ]
    },

    // 砍价活动
    {
        path: '/bargain',
        name: 'bargain',
        meta: {
            hidden: true,
            title: '砍价活动',
            moduleName: 'bargain'
        },
        redirect: '/bargain/lists',
        component: Main,
        children: [
            {
                path: '/bargain/lists',
                name: 'bargain',
                meta: {
                    title: '砍价活动',
                    parentPath: '/marketing',
                    moduleName: 'bargain',
                    icon: 'icon_kanjia',
                    permission: ['view']
                },
                component: () => import('@/views/marketing/bargain/lists.vue')
            },
            {
                path: '/bargain/bargain_edit',
                name: 'bargain_edit',
                meta: {
                    hidden: true,
                    title: '砍价活动',
                    parentPath: '/marketing',
                    prevPath: '/bargain/lists',
                    moduleName: 'bargain'
                },
                component: () => import('@/views/marketing/bargain/bargain_edit.vue')
            },
            {
                path: '/bargain/bargain_record',
                name: 'bargain_record',
                meta: {
                    title: '砍价记录',
                    parentPath: '/marketing',
                    icon: 'icon_order_guanli',
                    moduleName: 'bargain',
                    permission: ['view']
                },
                component: () => import('@/views/marketing/bargain/bargain_record.vue')
            }
        ]
    },

    // 会员价
    {
        path: '/member_price',
        name: 'member_price',
        meta: {
            hidden: true,
            title: '会员折扣',
            moduleName: 'member_price'
        },
        redirect: '/member_price/index',
        component: Main,
        children: [
            {
                path: '/member_price/index',
                name: 'member_price_index',
                meta: {
                    title: '会员折扣',
                    parentPath: '/marketing',
                    moduleName: 'member_price',
                    icon: 'icon_kanjia',
                    permission: ['view'],
                    keepAlive: true
                },
                component: () => import('@/views/marketing/member_price/index.vue')
            },
            {
                path: '/member_price/edit',
                name: 'member_price_edit',
                meta: {
                    hidden: true,
                    title: '会员折扣',
                    parentPath: '/marketing',
                    prevPath: '/member_price/index',
                    moduleName: 'member_price'
                },
                component: () => import('@/views/marketing/member_price/edit.vue')
            }
        ]
    },
    // 拼团活动
    {
        path: '/combination',
        name: 'combination',
        meta: {
            hidden: true,
            title: '拼团活动',
            moduleName: 'combination'
        },
        redirect: '/combination/lists',
        component: Main,
        children: [
            {
                path: '/combination/survey',
                name: 'combination',
                meta: {
                    hidden: true,
                    title: '拼团概览',
                    parentPath: '/marketing',
                    moduleName: 'combination',
                    icon: 'icon_pintuan_data',
                    permission: ['view']
                },
                component: () => import('@/views/marketing/combination/survey.vue')
            },
            {
                path: '/combination/lists',
                name: 'combination',
                meta: {
                    title: '拼团活动',
                    parentPath: '/marketing',
                    moduleName: 'combination',
                    icon: 'icon_pintuan2',
                    permission: ['view']
                },
                component: () => import('@/views/marketing/combination/lists.vue')
            },
            {
                path: '/combination/edit',
                name: 'edit',
                meta: {
                    hidden: true,
                    title: '拼团活动',
                    prevPath: '/combination/lists',
                    moduleName: 'combination'
                },
                component: () => import('@/views/marketing/combination/edit.vue')
            },
            {
                path: '/combination/record',
                name: 'record',
                meta: {
                    title: '拼团记录',
                    icon: 'icon_order_guanli',
                    parentPath: '/marketing',
                    moduleName: 'combination'
                },
                component: () => import('@/views/marketing/combination/record.vue')
            }
        ]
    },
    // 限时秒杀
    {
        path: '/seckill',
        name: 'seckill',
        meta: {
            hidden: true,
            title: '限时秒杀',
            moduleName: 'seckill'
        },
        redirect: '/seckill/lists',
        component: Main,
        children: [
            {
                path: '/seckill/lists',
                name: 'seckill',
                meta: {
                    title: '秒杀活动',
                    parentPath: '/marketing',
                    moduleName: 'seckill',
                    icon: 'Field-time',
                    permission: ['view']
                },
                component: () => import('@/views/marketing/seckill/lists.vue')
            },
            {
                path: '/seckill/edit',
                name: 'seckill_edit',
                meta: {
                    hidden: true,
                    title: '秒杀活动',
                    parentPath: '/marketing',
                    prevPath: '/seckill/lists',
                    moduleName: 'seckill'
                },
                component: () => import('@/views/marketing/seckill/edit.vue')
            }
        ]
    },
    // 预售活动
    {
        path: '/presell',
        name: 'presell',
        meta: {
            hidden: true,
            title: '预售活动',
            moduleName: 'presell'
        },
        redirect: '/presell/lists',
        component: Main,
        children: [
            {
                path: '/presell/lists',
                name: 'presell',
                meta: {
                    title: '预售活动',
                    parentPath: '/marketing',
                    moduleName: 'presell',
                    icon: 'Field-time',
                    permission: ['view']
                },
                component: () => import('@/views/marketing/presell/lists.vue')
            },
            {
                path: '/presell/edit',
                name: 'presell_edit',
                meta: {
                    hidden: true,
                    title: '预售活动',
                    parentPath: '/marketing',
                    prevPath: '/presell/lists',
                    moduleName: 'presell'
                },
                component: () => import('@/views/marketing/presell/edit.vue')
            }
        ]
    },

    // 积分抽奖
    {
        path: '/lucky_draw',
        name: 'lucky_draw',
        meta: {
            hidden: true,
            title: '积分抽奖',
            moduleName: 'lucky_draw'
        },
        redirect: '/lucky_draw/index',
        component: Main,
        children: [
            {
                path: '/lucky_draw/index',
                name: 'lucky_draw_index',
                meta: {
                    title: '积分抽奖',
                    parentPath: '/marketing',
                    moduleName: 'lucky_draw',
                    icon: 'icon_xycj_cj',
                    permission: ['view']
                },
                component: () => import('@/views/marketing/lucky_draw/index.vue')
            },
            {
                path: '/lucky_draw/edit',
                name: 'lucky_draw_edit',
                meta: {
                    hidden: true,
                    title: '编辑积分抽奖',
                    parentPath: '/marketing',
                    prevPath: '/lucky_draw/index',
                    moduleName: 'lucky_draw'
                },
                component: () => import('@/views/marketing/lucky_draw/edit.vue')
            },
            {
                path: '/lucky_draw/log',
                name: 'lucky_draw_log',
                meta: {
                    hidden: true,
                    title: '幸运记录',
                    parentPath: '/marketing',
                    prevPath: '/lucky_draw/log',
                    moduleName: 'lucky_draw'
                },
                component: () => import('@/views/marketing/lucky_draw/log.vue')
            }
        ]
    },

    // 消费奖励
    {
        path: '/consumption_reward',
        name: 'consumption_reward',
        meta: {
            hidden: true,
            title: '消费奖励',
            moduleName: 'consumption_reward'
        },
        redirect: '/consumption_reward',
        component: Main,
        children: [
            {
                path: '/consumption_reward/setting',
                name: 'consumption_reward_setting',
                meta: {
                    title: '消费奖励',
                    parentPath: '/marketing',
                    moduleName: 'consumption_reward',
                    icon: 'icon_caiwu',
                    permission: ['view']
                },
                component: () => import('@/views/marketing/consumption_reward/setting.vue')
            }
        ]
    },
    // 包邮活动
    {
        path: '/free_shipping',
        name: 'free_shipping',
        meta: {
            hidden: true,
            title: '包邮',
            moduleName: 'free_shipping'
        },
        redirect: '/free_shipping/index',
        component: Main,
        children: [
            {
                path: '/free_shipping/index',
                name: 'free_shipping_index',
                meta: {
                    title: '包邮活动',
                    parentPath: '/marketing',
                    moduleName: 'free_shipping',
                    icon: 'icon_caiwu',
                    permission: ['view']
                },
                component: () => import('@/views/marketing/free_shipping/index.vue')
            },
            {
                path: '/free_shipping/edit',
                name: 'free_shipping_edit',
                meta: {
                    hidden: true,
                    title: '包邮活动详情',
                    parentPath: '/marketing',
                    prevPath: '/free_shipping/index',
                    moduleName: 'free_shipping',
                    icon: 'icon_caiwu'
                },
                component: () => import('@/views/marketing/free_shipping/edit.vue')
            }
        ]
    },
    // // 注册奖励
    {
        path: '/register_award',
        name: 'register_award',
        meta: {
            hidden: true,
            title: '注册奖励',
            moduleName: 'register_award'
        },
        redirect: '/register_award/index',
        component: Main,
        children: [
            {
                path: '/register_award/index',
                name: 'register_award_index',
                meta: {
                    title: '注册奖励',
                    parentPath: '/marketing',
                    moduleName: 'register_award',
                    icon: 'icon_caiwu',
                    permission: ['view']
                },
                component: () => import('@/views/marketing/register/index.vue')
            }
        ]
    }
]

export default routes
