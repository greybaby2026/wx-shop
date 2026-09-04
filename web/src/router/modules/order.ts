import Main from '@/layout/main.vue'
import Blank from '@/layout/blank.vue'
const routes = [
    {
        path: '/order',
        name: 'order',
        meta: { title: '订单' },
        redirect: '/order/order',
        component: Main,
        children: [
            {
                path: '/order/order',
                name: 'order_order',
                meta: {
                    title: '订单管理',
                    parentPath: '/order',
                    icon: 'icon_order_guanli',
                    permission: ['view'],
                    keepAlive: true
                },
                component: () => import('@/views/order/order.vue')
            },
            {
                path: '/order/order_detail',
                name: 'order_detail',
                meta: {
                    hidden: true,
                    title: '订单详情',
                    parentPath: '/order',
                    prevPath: '/order/order'
                },
                component: () => import('@/views/order/order_detail.vue')
            },
            {
                path: '/order/order_delivery',
                name: 'order_delivery',
                meta: {
                    hidden: true,
                    title: '订单发货',
                    parentPath: '/order',
                    prevPath: '/order/order'
                },
                component: () => import('@/views/order/order_delivery.vue')
            },
            {
                path: '/order/query_delivery',
                name: 'query_delivery',
                meta: {
                    hidden: true,
                    title: '物流详情',
                    parentPath: '/order',
                    prevPath: '/order/order'
                },
                component: () => import('@/views/order/query_delivery.vue')
            },
            {
                path: '/order/after_sales',
                name: 'after_sales',
                meta: {
                    title: '售后订单',
                    parentPath: '/order',
                    icon: 'icon_order_shouhou',
                    permission: ['view'],
                    keepAlive: true
                },
                component: () => import('@/views/order/after_sales.vue')
            },
            {
                path: '/order/after_sales_detail',
                name: 'after_sales_detail',
                meta: {
                    hidden: true,
                    title: '售后订单详情',
                    parentPath: '/order'
                },
                component: () => import('@/views/order/after_sales_detail.vue')
            },
            {
                path: '/order/delivery',
                name: 'delivery',
                meta: {
                    icon: 'icon_dianpu_xiangqing',
                    hidden: false,
                    title: '批量发货',
                    parentPath: '/order'
                },
                component: () => import('@/views/order/delivery.vue')
            {
                path: '/order/erp_order',
                name: 'erp_order',
                meta: {
                    title: 'ERP订单',
                    parentPath: '/order',
                    icon: 'icon_order_guanli',
                    permission: ['view'],
                    keepAlive: true
                },
                component: () => import('@/views/order/erp_order_list.vue')
            },
            {
                path: '/order/erp_order_detail',
                name: 'erp_order_detail',
                meta: {
                    hidden: true,
                    title: 'ERP订单详情',
                    parentPath: '/order',
                    prevPath: '/order/erp_order'
                },
                component: () => import('@/views/order/erp_order_detail.vue')
            },

            }
        ]
    }
]

export default routes
