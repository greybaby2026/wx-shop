import Main from '@/layout/main.vue'
import Blank from '@/layout/blank.vue'
const routes = [
    {
        path: '/goods',
        name: 'goods',
        meta: { title: '商品' },
        redirect: '/goods/lists',
        component: Main,
        children: [
            {
                path: '/goods/lists',
                name: 'goods_lists',
                meta: {
                    title: '商品管理',
                    parentPath: '/goods',
                    icon: 'icon_goods',
                    permission: ['view'],
                    keepAlive: true
                },
                component: () => import('@/views/goods/lists.vue')
            },
            {
                path: '/goods/category',
                name: 'goods_category',
                meta: {
                    title: '分类管理',
                    parentPath: '/goods',
                    icon: 'icon_sort',
                    permission: ['view'],
                    keepAlive: true
                },
                component: () => import('@/views/goods/category.vue')
            },
            {
                path: '/goods/brand',
                name: 'goods_brand',
                meta: {
                    title: '品牌管理',
                    parentPath: '/goods',
                    icon: 'icon_pinpai',
                    permission: ['view'],
                    keepAlive: true
                },
                component: () => import('@/views/goods/brand.vue')
            },
            {
                path: '/goods/unit',
                name: 'goods_unit',
                meta: {
                    title: '商品单位',
                    icon: 'icon_danwei',
                    parentPath: '/goods',
                    permission: ['view'],
                    keepAlive: true
                },
                component: () => import('@/views/goods/unit.vue')
            },
            {
                path: '/goods/supplier',
                name: 'goods_supplier',
                meta: {
                    title: '供应商',
                    parentPath: '/goods',
                    icon: 'icon_gongyingshang',
                    keepAlive: true
                },
                redirect: '/goods/supplier/lists',
                component: Blank,
                children: [
                    {
                        path: '/goods/supplier/lists',
                        name: 'supplier_lists',
                        meta: {
                            title: '供应商管理',
                            parentPath: '/goods',
                            permission: ['view'],
                            keepAlive: true
                        },
                        component: () => import('@/views/goods/supplier/lists.vue')
                    },
                    {
                        path: '/goods/supplier/category',
                        name: 'supplier_category',
                        meta: {
                            title: '供应商分类',
                            parentPath: '/goods',
                            permission: ['view'],
                            keepAlive: true
                        },
                        component: () => import('@/views/goods/supplier/category.vue')
                    },
                    {
                        path: '/goods/supplier/edit',
                        name: 'supplier_edit',
                        meta: {
                            hidden: true,
                            title: '新增供应商',
                            parentPath: '/goods',
                            prevPath: '/goods/supplier/lists'
                        },
                        component: () => import('@/views/goods/supplier/edit.vue')
                    }
                ]
            },
            {
                path: '/goods/release',
                name: 'goods_release',
                meta: {
                    hidden: true,
                    title: '新增商品',
                    parentPath: '/goods',
                    prevPath: '/goods/lists'
                },
                component: () => import('@/views/goods/release.vue')
            },
            {
                path: '/goods/new_release',
                name: 'goods_new_release',
                meta: {
                    hidden: true,
                    title: '复制商品',
                    parentPath: '/goods',
                    prevPath: '/goods/lists'
                },
                component: () => import('@/views/goods/new_release.vue')
            },
            {
                path: '/goods/brand_edit',
                name: 'brand_edit',
                meta: {
                    hidden: true,
                    title: '新增品牌',
                    parentPath: '/goods',
                    prevPath: '/goods/brand'
                },
                component: () => import('@/views/goods/brand_edit.vue')
            },
            {
                path: '/goods/category_edit',
                name: 'category_edit',
                meta: {
                    hidden: true,
                    title: '新增分类',
                    parentPath: '/goods',
                    prevPath: '/goods/category'
                },
                component: () => import('@/views/goods/category_edit.vue')
            },
            {
                path: '/goods/evaluation',
                name: 'goods_evaluation',
                meta: {
                    title: '评价管理',
                    parentPath: '/goods',
                    icon: 'icon_pingjia',
                    permission: ['view'],
                    keepAlive: true
                },
                component: () => import('@/views/goods/evaluation.vue')
            },
            {
                path: '/goods/guarantee',
                name: 'goods_guarantee',
                meta: {
                    title: '保障服务',
                    parentPath: '/goods',
                    icon: 'icon_caiwu_yue',
                    permission: ['view'],
                    keepAlive: true
                },
                component: () => import('@/views/goods/guarantee.vue')
            }
        ]
    }
]

export default routes
