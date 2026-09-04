import Main from '@/layout/main.vue'

const routes = [
    {
        path: '/finance',
        name: 'finance',
        meta: { title: '财务' },
        redirect: '/finance/profile',
        component: Main,
        children: [
            {
                path: '/finance/profile',
                name: 'profile',
                meta: {
                    title: '财务概况',
                    parentPath: '/finance',
                    icon: 'icon_caiwu',
                    permission: ['view']
                },
                component: () => import('@/views/finance/profile.vue')
            },
            {
                path: '/finance/user_withdrawal',
                name: 'user_withdrawal',
                meta: {
                    title: '提现记录',
                    parentPath: '/finance',
                    icon: 'icon_caiwu_tixian',
                    permission: ['view'],
                    keepAlive: true
                },
                component: () => import('@/views/finance/user_withdrawal.vue')
            },
            {
                path: '/finance/account_log',
                name: 'account_log',
                meta: {
                    title: '余额明细',
                    parentPath: '/finance',
                    icon: 'icon_caiwu_yue',
                    permission: ['view'],
                    keepAlive: true
                },
                component: () => import('@/views/finance/account_log.vue')
            },
            {
                path: '/finance/integral_list',
                name: 'integral_list',
                meta: {
                    title: '积分明细',
                    parentPath: '/finance',
                    icon: 'icon_caiwu_jifen',
                    permission: ['view'],
                    keepAlive: true
                },
                component: () => import('@/views/finance/integral_list.vue')
            },
            {
                path: '/finance/act_log',
                name: 'act_log',
                meta: {
                    title: '活动余额记录',
                    parentPath: '/finance',
                    icon: 'icon_caiwu_yue',
                    permission: ['view'],
                    keepAlive: true
                },
                component: () => import('@/views/finance/act_log.vue')
            },
            {
                path: '/finance/commission_log',
                name: 'commission_log',
                meta: {
                    title: '佣金明细',
                    parentPath: '/finance',
                    icon: 'icon_set_jiaoyi',
                    permission: ['view'],
                    keepAlive: true
                },
                component: () => import('@/views/finance/commission_log.vue')
            }
        ]
    }
]

export default routes
