import Main from '@/layout/main.vue'
import Blank from '@/layout/blank.vue'
const routes = [
    {
        path: '/data',
        name: 'data',
        meta: { title: '数据' },
        redirect: '/data/flow_analysis',
        component: Main,
        children: [
            {
                path: '/data/flow_analysis',
                name: 'flow_analysis',
                meta: {
                    title: '流量分析',
                    parentPath: '/data',
                    icon: 'icon_shuju_liuliang',
                    permission: ['view']
                },
                component: () => import('@/views/data/flow_analysis.vue')
            },
            {
                path: '/data/user',
                name: 'user',
                meta: {
                    title: '用户分析',
                    parentPath: '/data',
                    icon: 'icon_shuju',
                    permission: ['view']
                },
                component: () => import('@/views/data/user.vue')
            },
            {
                path: '/data/transaction',
                name: 'transaction',
                meta: {
                    title: '交易分析',
                    parentPath: '/data',
                    icon: 'icon_dianpu_shoppingCar',
                    permission: ['view']
                },
                component: () => import('@/views/data/transaction.vue')
            },
            {
                path: '/data/goods/goods',
                name: 'goods',
                meta: {
                    title: '商品数据',
                    parentPath: '/data',
                    icon: 'tradingdata',
                    permission: ['view']
                },
                component: Blank,
                redirect: '/data/goods/goods',
                children: [
                    {
                        path: '/data/goods/goods',
                        name: 'goods',
                        meta: {
                            title: '商品分析',
                            parentPath: '/data',
                            permission: ['view']
                        },
                        component: () => import('@/views/data/goods/goods.vue')
                    },
                    {
                        path: '/data/goods/ranking',
                        name: 'goods',
                        meta: {
                            title: '商品排行',
                            parentPath: '/data',
                            permission: ['view']
                        },
                        component: () => import('@/views/data/goods/ranking.vue')
                    }
                ]
            }
        ]
    }
]

export default routes
