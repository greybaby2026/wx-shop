import Main from '@/layout/main.vue'
import Blank from '@/layout/blank.vue'
const routes = [
    {
        path: '/setting',
        name: 'setting',
        meta: { title: '设置' },
        redirect: '/setting/shop',
        component: Main,
        children: [
            {
                path: '/setting/shop',
                name: 'setting_shop',
                meta: {
                    title: '店铺设置',
                    parentPath: '/setting',
                    icon: 'icon_set_store'
                },
                component: Blank,
                redirect: '/setting/shop/shop',
                children: [
                    {
                        path: '/setting/shop/shop',
                        name: 'setting_shop',
                        meta: {
                            title: '店铺信息',
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/shop/shop.vue')
                    },
                    {
                        path: '/setting/shop/record',
                        name: 'setting_record',
                        meta: {
                            title: '版权资质',
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/shop/record.vue')
                    },
                    // {
                    //     path: '/setting/shop/share',
                    //     name: 'setting_share',
                    //     meta: {
                    //         title: '分享设置',
                    //         parentPath: '/setting',
                    //         permission: ['view']
                    //     },
                    //     component: () => import('@/views/setting/shop/share.vue')
                    // },
                    {
                        path: '/setting/shop/protocol',
                        name: 'setting_protocol',
                        meta: {
                            title: '政策协议',
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/shop/protocol.vue')
                    },
                    {
                        path: '/setting/shop/statistics',
                        name: 'setting_statistics',
                        meta: {
                            title: '站点统计',
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/shop/statistics.vue')
                    }
                ]
            },
            {
                path: '/setting/payment/pay_method',
                name: 'setting_payment',
                meta: {
                    title: '支付设置',
                    parentPath: '/setting',
                    icon: 'icon_set_pay'
                },
                component: Blank,
                redirect: '/setting/payment/pay_method',
                children: [
                    {
                        path: '/setting/payment/pay_method',
                        name: 'setting_pay_method',
                        meta: {
                            title: '支付方式',
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/payment/pay_method.vue')
                    },
                    {
                        path: '/setting/payment/pay_config',
                        name: 'setting_pay_config',
                        meta: {
                            title: '支付配置',
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/payment/pay_config.vue')
                    },
                    {
                        path: '/setting/payment/pay_method_edit',
                        name: 'setting_pay_method_edit',
                        meta: {
                            hidden: true,
                            title: '支付方式设置',
                            parentPath: '/setting',
                            prevPath: '/setting/payment/pay_method'
                        },
                        component: () => import('@/views/setting/payment/pay_method_edit.vue')
                    },
                    {
                        path: '/setting/payment/pay_config_edit',
                        name: 'setting_pay_config_edit',
                        meta: {
                            hidden: true,
                            title: '支付配置设置',
                            parentPath: '/setting',
                            prevPath: '/setting/payment/pay_config'
                        },
                        component: () => import('@/views/setting/payment/pay_config_edit.vue')
                    }
                ]
            },
            {
                path: '/setting/delivery',
                name: 'delivery',
                meta: {
                    title: '配送设置',
                    parentPath: '/setting',
                    icon: 'icon_set_peisong'
                },
                component: Blank,
                redirect: '/setting/delivery/index',
                children: [
                    {
                        path: '/setting/delivery/index',
                        name: 'delivery_index',
                        meta: {
                            title: '配送方式',
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/delivery/index.vue')
                    },
                    {
                        path: '/setting/delivery/express',
                        name: 'delivery_express',
                        meta: {
                            hidden: true,
                            title: '快递公司',
                            parentPath: '/setting',
                            prevPath: '/setting/delivery/index'
                        },
                        component: () => import('@/views/setting/delivery/express.vue')
                    },
                    {
                        path: '/setting/delivery/express_edit',
                        name: 'delivery_express_edit',
                        meta: {
                            hidden: true,
                            title: '新增快递公司',
                            parentPath: '/setting',
                            prevPath: '/setting/delivery/index'
                        },
                        component: () => import('@/views/setting/delivery/express_edit.vue')
                    },
                    {
                        path: '/setting/delivery/freight',
                        name: 'delivery_freight',
                        meta: {
                            hidden: true,
                            title: '运费模板',
                            parentPath: '/setting',
                            prevPath: '/setting/delivery/index'
                        },
                        component: () => import('@/views/setting/delivery/freight.vue')
                    },
                    {
                        path: '/setting/delivery/freight_edit',
                        name: 'delivery_freight_edit',
                        meta: {
                            hidden: true,
                            title: '新增运费模板',
                            parentPath: '/setting',
                            prevPath: '/setting/delivery/index'
                        },
                        component: () => import('@/views/setting/delivery/freight_edit.vue')
                    },
                    {
                        path: '/setting/delivery/logistics',
                        name: 'delivery_logistics',
                        meta: {
                            hidden: true,
                            title: '物流接口',
                            parentPath: '/setting',
                            prevPath: '/setting/delivery/index'
                        },
                        component: () => import('@/views/setting/delivery/logistics.vue')
                    }
                ]
            },
            {
                path: '/setting/permissions',
                name: 'admin',
                meta: {
                    title: '平台权限',
                    parentPath: '/setting',
                    icon: 'icon_set_quanxian'
                },
                component: Blank,
                redirect: '/setting/permissions/admin',
                children: [
                    {
                        path: '/setting/permissions/admin',
                        name: 'permissions_admin',
                        meta: {
                            title: '管理员',
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/permissions/admin.vue')
                    },
                    {
                        path: '/setting/permissions/admin_edit',
                        name: 'permissions_admin_edit',
                        meta: {
                            hidden: true,
                            title: '管理员',
                            parentPath: '/setting',
                            prevPath: '/setting/permissions/admin'
                        },
                        component: () => import('@/views/setting/permissions/admin_edit.vue')
                    },
                    {
                        path: '/setting/permissions/role',
                        name: 'permissions_role',
                        meta: {
                            title: '角色',
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/permissions/role.vue')
                    },
                    {
                        path: '/setting/permissions/role_edit',
                        name: 'permissions_role_edit',
                        meta: {
                            hidden: true,
                            title: '编辑角色',
                            parentPath: '/setting',
                            prevPath: '/setting/permissions/role'
                        },
                        component: () => import('@/views/setting/permissions/role_edit.vue')
                    }
                ]
            },
            {
                path: '/setting/goods/goods',
                name: 'goods',
                meta: {
                    title: '商品设置',
                    parentPath: '/setting',
                    icon: 'icon_set_product'
                },
                component: Blank,
                redirect: '/setting/goods/goods',
                children: [
                    {
                        path: '/setting/goods/goods',
                        name: 'setting_goods',
                        meta: {
                            title: '商品设置',
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/goods/goods.vue')
                    }
                ]
            },
            {
                path: '/setting/user/user_setting',
                name: 'user_setting',
                meta: {
                    title: '用户设置',
                    parentPath: '/setting',
                    icon: 'icon_set_user'
                },
                component: Blank,
                redirect: '/setting/user/user_setting',
                children: [
                    {
                        path: '/setting/user/user_setting',
                        name: 'user_setting',
                        meta: {
                            title: '用户设置',
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/user/user_setting.vue')
                    },
                    {
                        path: '/setting/user/login_register',
                        name: 'login_register',
                        meta: {
                            title: '登录注册',
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/user/login_register.vue')
                    },
                    {
                        path: '/setting/user/withdraw_deposit',
                        name: 'withdraw_deposit',
                        meta: {
                            title: '用户提现',
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/user/withdraw_deposit.vue')
                    }
                ]
            },
            {
                path: '/setting/order/order',
                name: 'order',
                meta: {
                    title: '交易设置',
                    parentPath: '/setting',
                    icon: 'icon_set_jiaoyi'
                },
                component: Blank,
                redirect: '/setting/order/order',
                children: [
                    {
                        path: '/setting/order/order',
                        name: 'setting_order',
                        meta: {
                            title: '交易设置',
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/order/order.vue')
                    }
                ]
            },
            {
                path: '/setting/map',
                name: 'map',
                meta: {
                    title: '地图设置',
                    parentPath: '/setting',
                    icon: 'icon_caiwu_jifen',
                    permission: ['view']
                },
                component: () => import('@/views/setting/map/setting.vue')
            },
            {
                path: '/setting/storage/storage',
                name: 'order',
                meta: {
                    title: '存储设置',
                    parentPath: '/setting',
                    icon: 'icon_set_save'
                },
                component: Blank,
                redirect: '/setting/storage/storage',
                children: [
                    {
                        path: '/setting/storage/storage',
                        name: 'setting_storage',
                        meta: {
                            title: '设置A',
                            parentPath: '/setting',
                            hidden: true
                        },
                        component: () => import('@/views/setting/storage/storage.vue')
                    },
                    {
                        path: '/setting/storage/index',
                        name: 'setting_storage_index',
                        meta: {
                            title: '存储设置',
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/storage/index.vue')
                    }
                ]
            },
            {
                path: '/setting/system_maintain/journal',
                name: 'system_maintain',
                meta: {
                    title: '系统维护',
                    parentPath: '/setting',
                    icon: 'icon_set_weihu'
                },
                component: Blank,
                redirect: '/setting/system_maintain/journal',
                children: [
                    {
                        path: '/setting/system_maintain/journal',
                        name: 'journal',
                        meta: {
                            title: '系统日志',
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/system_maintain/journal.vue')
                    },
                    {
                        path: '/setting/system_maintain/cache',
                        name: 'cache',
                        meta: {
                            hidden: false,
                            title: '系统缓存',
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/system_maintain/cache.vue')
                    },
                    {
                        path: '/setting/system_maintain/updata',
                        name: 'updata',
                        meta: {
                            title: '系统更新',
                            hidden: false,
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/system_maintain/updata.vue')
                    },
                    {
                        path: '/setting/system_maintain/error_journal',
                        name: 'error_journal',
                        meta: {
                            title: '异常日志',
                            hidden: true,
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/system_maintain/error_journal.vue')
                    },
                    {
                        path: '/setting/task',
                        name: 'task',
                        meta: {
                            hidden: false,
                            title: '定时任务',
                            parentPath: '/setting',
                            permission: ['view']
                        },
                        component: () => import('@/views/setting/task/task.vue')
                    },
                    {
                        path: '/setting/task_edit',
                        name: 'task_edit',
                        meta: {
                            hidden: true,
                            title: '定时任务',
                            parentPath: '/setting'
                        },
                        component: () => import('@/views/setting/task/task_edit.vue')
                    }
                ]
            }
        ]
    }
]

export default routes
