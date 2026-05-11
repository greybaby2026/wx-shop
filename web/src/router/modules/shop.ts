import Main from '@/layout/main.vue'
import Blank from '@/layout/blank.vue'
import DecorateLayout from '@/layout/decorate.vue'
const routes = [
    {
        path: '/shop',
        name: 'shop',
        meta: { title: '店铺' },
        redirect: '/shop/index',
        component: Main,
        children: [
            {
                path: '/shop/index',
                name: 'shop_index',
                meta: {
                    title: '店铺主页',
                    parentPath: '/shop',
                    icon: 'icon_dianpu_home',
                    permission: ['view']
                },
                component: () => import('@/views/shop/index.vue')
            },
            {
                path: '/shop/category',
                name: 'shop_category',
                meta: {
                    title: '商品分类',
                    parentPath: '/shop',
                    icon: 'icon_dianpu_fenlei'
                },
                redirect: '/decorate/category'
            },
            {
                path: '/shop/goods_detail',
                name: 'shop_goods_detail',
                meta: {
                    title: '商品详情',
                    parentPath: '/shop',
                    icon: 'icon_dianpu_xiangqing'
                },
                redirect: '/decorate/goods_detail'
            },
            {
                path: '/shop/cart',
                name: 'shop_cart',
                meta: {
                    title: '购物车',
                    parentPath: '/shop',
                    icon: 'icon_dianpu_shoppingCar'
                },
                redirect: '/decorate/cart'
            },
            {
                path: '/shop/user',
                name: 'shop_user',
                meta: {
                    title: '个人中心',
                    parentPath: '/shop',
                    icon: 'icon_user'
                },
                redirect: '/decorate/user'
            },
            {
                path: '/shop/tabbar',
                name: 'shop_tabbar',
                meta: {
                    title: '底部导航',
                    parentPath: '/shop',
                    icon: 'icon_dianpu_daohang',
                    permission: ['view']
                },
                component: () => import('@/views/shop/tabbar.vue')
            },
            {
                path: '/shop/pages',
                name: 'shop_pages',
                meta: {
                    title: '微页面',
                    parentPath: '/shop',
                    icon: 'icon_dianpu_weiyem',
                    permission: ['view']
                },
                component: Blank,
                children: [
                    {
                        path: '/shop/pages/lists',
                        name: 'pages_lists',
                        meta: {
                            title: '页面管理',
                            parentPath: '/shop',
                            permission: ['view']
                        },
                        component: () => import('@/views/shop/pages/lists.vue')
                    },
                    {
                        path: '/shop/pages/template',
                        name: 'pages_template',
                        meta: {
                            title: '页面模板',
                            parentPath: '/shop',
                            permission: ['view']
                        },
                        component: () => import('@/views/shop/pages/template.vue')
                    }
                ]
            },
            {
                path: '/shop/theme',
                name: 'shop_theme',
                meta: {
                    title: '商城风格',
                    parentPath: '/shop',
                    icon: 'icon_dianpu_fengge',
                    permission: ['view']
                },
                component: () => import('@/views/shop/theme.vue')
            },
            {
                path: '/shop/open_screen',
                name: 'shop_open_screen',
                meta: {
                    title: '弹窗广告',
                    parentPath: '/shop',
                    icon: 'icon_qudao_app'
                },
                component: () => import('@/views/shop/open_screen.vue')
            },
            {
                path: '/shop/material',
                name: 'material',
                meta: {
                    title: '素材中心',
                    parentPath: '/shop',
                    icon: 'icon_dianpu_sucai',
                    permission: ['view']
                },
                component: () => import('@/views/shop/material.vue')
            },
            {
                path: '/shop/pc',
                name: 'pc',
                meta: {
                    title: 'PC商城',
                    parentPath: '/shop',
                    icon: 'icon_pcshop',
                    permission: ['view']
                },
                component: Blank,
                children: [
                    {
                        path: '/shop/pc/index',
                        name: 'pc_index',
                        meta: {
                            title: '首页装修',
                            parentPath: '/shop',
                            permission: ['view']
                        },
                        component: () => import('@/views/shop/pc/index.vue')
                    },
                    {
                        path: '/shop/pc/adv',
                        name: 'pc_adv',
                        meta: {
                            title: '广告管理',
                            parentPath: '/shop',
                            permission: ['view']
                        },
                        component: () => import('@/views/shop/pc/adv.vue')
                    }
                ]
            }
        ]
    },
    {
        path: '/decorate',
        name: 'decorate',
        meta: { title: '装修', hidden: true },
        redirect: '/decorate/index',
        component: DecorateLayout,
        children: [
            {
                path: '/decorate/index',
                name: 'decorate_index',
                meta: {
                    title: '微页面',
                    parentPath: '/decorate',
                    permission: ['view']
                },
                component: () => import('@/views/decorate/index.vue')
            },
            {
                path: '/decorate/category',
                name: 'decorate_category',
                meta: {
                    title: '商品分类',
                    parentPath: '/decorate',
                    permission: ['view']
                },
                component: () => import('@/views/decorate/category.vue')
            },
            {
                path: '/decorate/cart',
                name: 'decorate_cart',
                meta: {
                    title: '购物车',
                    parentPath: '/decorate',
                    permission: ['view']
                },
                component: () => import('@/views/decorate/cart.vue')
            },
            {
                path: '/decorate/user',
                name: 'decorate_user',
                meta: {
                    title: '个人中心',
                    parentPath: '/decorate',
                    permission: ['view']
                },
                component: () => import('@/views/decorate/user.vue')
            },
            {
                path: '/decorate/goods_detail',
                name: 'decorate_goods_detail',
                meta: {
                    title: '商品详情',
                    parentPath: '/decorate',
                    permission: ['view']
                },
                component: () => import('@/views/decorate/goods_detail.vue')
            },
            {
                path: '/decorate/pc_index',
                name: 'decorate_pc_index',
                meta: {
                    title: 'PC首页',
                    parentPath: '/decorate',
                    permission: ['view']
                },
                component: () => import('@/views/decorate/pc_index.vue')
            }
        ]
    }
]

export default routes
