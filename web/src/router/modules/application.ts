import Main from '@/layout/main.vue'
import Blank from '@/layout/blank.vue'
const routes = [
    {
        path: '/application',
        name: 'application',
        meta: { title: '应用' },
        redirect: '/application/app',
        component: Main,
        children: [
            {
                path: '/application/app',
                name: 'application_app',
                meta: {
                    title: '应用中心',
                    parentPath: '/application',
                    icon: 'icon_yingyongcenter',
                    permission: ['view']
                },
                component: () => import('@/views/application/app.vue')
            }
        ]
    },
    // 地址库
    {
        path: '/address',
        name: 'address',
        meta: {
            hidden: true,
            title: '地址库',
            moduleName: 'address'
        },
        redirect: '/address',
        component: Main,
        children: [
            {
                path: '/address/lists',
                name: 'address',
                meta: {
                    title: '地址库',
                    parentPath: '/application',
                    moduleName: 'address',
                    icon: 'icon_dianpu_home',
                    permission: ['view']
                },
                component: () => import('@/views/application/address/lists.vue')
            }
        ]
    },
    // 分享设置
    {
        path: '/share/setting',
        name: 'share',
        meta: {
            hidden: true,
            title: '分享设置',
            moduleName: 'share'
        },
        redirect: '/share/mpSetting',
        component: Main,
        children: [
            {
                path: '/share/mpSetting',
                name: 'address',
                meta: {
                    title: '微信小程序',
                    parentPath: '/application',
                    moduleName: 'share',
                    icon: 'icon_qudao_weixin',
                    permission: ['view']
                },
                component: () => import('@/views/application/share/mpsetting.vue')
            },
            {
                path: '/share/oaSetting',
                name: 'address',
                meta: {
                    title: '微信公众号',
                    parentPath: '/application',
                    moduleName: 'share',
                    icon: 'icon_qudao_app',
                    permission: ['view']
                },
                component: () => import('@/views/application/share/oasetting.vue')
            }
        ]
    },
    // 文章资讯
    {
        path: '/article',
        name: 'article',
        meta: {
            hidden: true,
            title: '文章资讯',
            moduleName: 'article'
        },
        redirect: '/article',
        component: Main,
        children: [
            {
                path: '/article/lists',
                name: 'article',
                meta: {
                    title: '资讯管理',
                    parentPath: '/application',
                    moduleName: 'article',
                    icon: 'icon_notice',
                    permission: ['view']
                },
                component: () => import('@/views/application/article/lists.vue')
            },
            {
                path: '/article/article_edit',
                name: 'article_edit',
                meta: {
                    hidden: true,
                    title: '资讯管理',
                    parentPath: '/article',
                    prevPath: '/article/lists',
                    moduleName: 'article'
                },
                component: () => import('@/views/application/article/article_edit.vue')
            },
            {
                path: '/article/category_lists',
                name: 'category_lists',
                meta: {
                    title: '资讯分类',
                    parentPath: '/application',
                    moduleName: 'article',
                    icon: 'icon_notice',
                    permission: ['view']
                },
                component: () => import('@/views/application/article/category_lists.vue')
            }
        ]
    },
    // 虚拟评价
    {
        path: '/evaluation',
        name: 'evaluation',
        meta: {
            hidden: true,
            title: '虚拟评价',
            moduleName: 'evaluation'
        },
        redirect: '/evaluation/index',
        component: Main,
        children: [
            {
                path: '/evaluation/index',
                name: 'evaluation',
                meta: {
                    title: '虚拟评价',
                    parentPath: '/application',
                    moduleName: 'evaluation',
                    icon: 'icon_pingjia',
                    permission: ['view']
                },
                component: () => import('@/views/application/evaluation/index.vue')
            },
            {
                path: '/evaluation/add',
                name: 'evaluation_add',
                meta: {
                    hidden: true,
                    title: '添加评价',
                    parentPath: '/application',
                    prevPath: '/evaluation/index',
                    moduleName: 'evaluation'
                },
                component: () => import('@/views/application/evaluation/add.vue')
            }
        ]
    },
    // 直播间
    {
        path: '/live_broadcast',
        name: 'live_broadcast',
        meta: {
            hidden: true,
            title: '直播间',
            moduleName: 'live_broadcast'
        },
        redirect: '/live_broadcast/lists',
        component: Main,
        children: [
            {
                path: '/live_broadcast/lists',
                name: 'live_broadcast_list',
                meta: {
                    title: '直播间',
                    parentPath: '/application',
                    moduleName: 'live_broadcast',
                    icon: 'icon_xcxzb_zb',
                    permission: ['view']
                },
                component: () => import('@/views/application/live_broadcast/lists.vue')
            },
            {
                path: '/live_broadcast/edit',
                name: 'live_broadcast_edit',
                meta: {
                    hidden: true,
                    title: '创建直播间',
                    parentPath: '/application',
                    prevPath: '/live_broadcast/lists',
                    moduleName: 'live_broadcast'
                },
                component: () => import('@/views/application/live_broadcast/edit.vue')
            },
            {
                path: '/live_broadcast/goods',
                name: 'live_broadcast_goods',
                meta: {
                    title: '直播商品',
                    parentPath: '/application',
                    icon: 'icon_pintuan',
                    moduleName: 'live_broadcast',
                    permission: ['view']
                },
                component: () => import('@/views/application/live_broadcast/goods.vue')
            },
            {
                path: '/live_broadcast/add_broadcast_goods',
                name: 'live_broadcast_goods_add',
                meta: {
                    hidden: true,
                    title: '添加直播商品',
                    parentPath: '/application',
                    icon: '',
                    moduleName: 'live_broadcast'
                },
                component: () => import('@/views/application/live_broadcast/add_broadcast_goods.vue')
            }
        ]
    },
    // 在线客服
    {
        path: '/service',
        name: 'service',
        meta: {
            hidden: true,
            title: '客服配置',
            moduleName: 'service'
        },
        redirect: '/service/setting',
        component: Main,
        children: [
            {
                path: '/service/setting',
                name: 'service_setting',
                meta: {
                    title: '客服配置',
                    parentPath: '/application',
                    moduleName: 'service',
                    icon: 'icon_kefu_comments',
                    permission: ['view']
                },
                component: () => import('@/views/application/service/setting.vue')
            },
            {
                path: '/service/lists',
                name: 'service_lists',
                meta: {
                    title: '客服列表',
                    parentPath: '/application',
                    moduleName: 'service',
                    icon: 'icon_user_biaoqian',
                    permission: ['view']
                },
                component: () => import('@/views/application/service/lists.vue')
            },
            {
                path: '/service/edit',
                name: 'service_edit',
                meta: {
                    hidden: true,
                    title: '客服列表',
                    parentPath: '/application',
                    prevPath: '/service/lists',
                    moduleName: 'service'
                },
                component: () => import('@/views/application/service/edit.vue')
            },
            {
                path: '/service/speech_lists',
                name: 'service_speech_lists',
                meta: {
                    title: '客服话术',
                    parentPath: '/application',
                    moduleName: 'service',
                    icon: 'icon_notice_buyer',
                    permission: ['view']
                },
                component: () => import('@/views/application/service/speech_lists.vue')
            }
        ]
    },
    // 商城公告
    {
        path: '/notice',
        name: 'notice',
        meta: {
            hidden: true,
            title: '商城公告',
            moduleName: 'notice'
        },
        redirect: '/notice',
        component: Main,
        children: [
            {
                path: '/notice/lists',
                name: 'notice',
                meta: {
                    title: '公告管理',
                    parentPath: '/application',
                    moduleName: 'notice',
                    icon: 'icon_notice',
                    permission: ['view']
                },
                component: () => import('@/views/application/notice/lists.vue')
            },
            {
                path: '/notice/notice_edit',
                name: 'notice_edit',
                meta: {
                    hidden: true,
                    title: '商城公告',
                    parentPath: '/notice',
                    prevPath: '/notice/lists',
                    moduleName: 'notice'
                },
                component: () => import('@/views/application/notice/notice_edit.vue')
            }
        ]
    },
    // 消息通知
    {
        path: '/sms',
        name: 'sms',
        meta: {
            title: '通知买家',
            hidden: true,
            moduleName: 'sms'
        },
        redirect: '/sms/buyers',
        component: Main,
        children: [
            {
                path: '/sms/buyers',
                name: 'sms_buyers',
                meta: {
                    title: '通知买家',
                    parentPath: '/application',
                    moduleName: 'sms',
                    icon: 'icon_notice_buyer'
                },
                component: Blank,
                redirect: '/sms/buyers/buyers',
                children: [
                    {
                        path: '/sms/buyers/buyers',
                        name: 'sms_buyers',
                        meta: {
                            title: '业务通知',
                            parentPath: '/application',
                            moduleName: 'sms',
                            permission: ['view']
                        },
                        component: () => import('@/views/application/sms/buyers/buyers.vue')
                    },
                    {
                        path: '/sms/buyers/setting',
                        name: 'setting',
                        meta: {
                            hidden: true,
                            title: '设置',
                            parentPath: '/application',
                            moduleName: 'sms'
                        },
                        component: () => import('@/views/application/sms/buyers/setting.vue')
                    },
                    {
                        path: '/sms/buyers/business',
                        name: 'business',
                        meta: {
                            title: '验证码',
                            parentPath: '/application',
                            moduleName: 'sms',
                            permission: ['view']
                        },
                        component: () => import('@/views/application/sms/buyers/business.vue')
                    }
                ]
            },
            {
                path: '/sms/seller',
                name: 'seller',
                meta: {
                    title: '卖家通知',
                    parentPath: '/application',
                    moduleName: 'sms',
                    icon: 'icon_notice_seller',
                    permission: ['view']
                },
                component: () => import('@/views/application/sms/seller.vue')
            },
            {
                path: '/sms/sms',
                name: 'sms',
                meta: {
                    title: '短信设置',
                    parentPath: '/application',
                    moduleName: 'sms',
                    icon: 'icon_notice_mail',
                    permission: ['view']
                },
                component: () => import('@/views/application/sms/sms.vue')
            },
            {
                path: '/sms/sms_edit',
                name: 'sms_edit',
                meta: {
                    hidden: true,
                    title: '短信设置详情',
                    parentPath: '/application',
                    prevPath: '/sms/sms',
                    moduleName: 'sms'
                },
                component: () => import('@/views/application/sms/sms_edit.vue')
            }
        ]
    },
    // 自提门店
    {
        path: '/selffetch',
        name: 'selffetch',
        meta: {
            title: '自提门店',
            hidden: true,
            moduleName: 'selffetch'
        },
        redirect: '/selffetch/selffetch_shop',
        component: Main,
        children: [
            {
                path: '/selffetch/selffetch_order',
                name: 'selffetch_order',
                meta: {
                    title: '核销订单',
                    parentPath: '/application',
                    moduleName: 'selffetch',
                    icon: 'icon_hexiao_order',
                    permission: ['view']
                },
                component: () => import('@/views/application/selffetch/order.vue')
            },
            {
                path: '/selffetch/selffetch_shop',
                name: 'selffetch_shop',
                meta: {
                    title: '自提门店',
                    parentPath: '/application',
                    moduleName: 'selffetch',
                    icon: 'icon_ziti_store',
                    permission: ['view']
                },
                component: () => import('@/views/application/selffetch/shop.vue')
            },
            {
                path: '/selffetch/selffetch_shop_edit',
                name: 'selffetch_shop_edit',
                meta: {
                    title: '编辑自提门店',
                    hidden: true,
                    parentPath: '/application',
                    prevPath: '/selffetch/selffetch_shop',
                    moduleName: 'selffetch'
                },
                component: () => import('@/views/application/selffetch/shop_edit.vue')
            },
            {
                path: '/selffetch/selffetch_verifier',
                name: 'selffetch_verifier',
                meta: {
                    title: '核销员',
                    parentPath: '/application',
                    moduleName: 'selffetch',
                    icon: 'icon_hexiao_member',
                    permission: ['view']
                },
                component: () => import('@/views/application/selffetch/verifier.vue')
            },
            {
                path: '/selffetch/selffetch_verifier_edit',
                name: 'selffetch_verifier_edit',
                meta: {
                    title: '编辑核销员',
                    hidden: true,
                    parentPath: '/application',
                    prevPath: '/selffetch/selffetch_verifier',
                    moduleName: 'selffetch'
                },
                component: () => import('@/views/application/selffetch/verifier_edit.vue')
            }
        ]
    },
    // 快递助手
    {
        path: '/express',
        name: 'express',
        meta: {
            title: '快递助手',
            hidden: true,
            moduleName: 'express'
        },
        redirect: '/express/batch',
        component: Main,
        children: [
            {
                path: '/express/batch',
                name: 'express',
                meta: {
                    title: '批量打印',
                    parentPath: '/application',
                    moduleName: 'express',
                    icon: 'icon_kdzs_pldy',
                    permission: ['view']
                },
                component: () => import('@/views/application/express/batch.vue')
            },
            {
                path: '/express/sender',
                name: 'edit_express',
                meta: {
                    title: '发件人模版',
                    parentPath: '/application',
                    moduleName: 'express',
                    icon: 'icon_kdzs_fjrmb',
                    permission: ['view']
                },
                component: () => import('@/views/application/express/sender.vue')
            },
            {
                path: '/express/sender_edit',
                name: 'sender_edit',
                meta: {
                    hidden: true,
                    title: '编辑发件人模版',
                    parentPath: '/application',
                    moduleName: 'express',
                    prevPath: '/express/sender'
                },
                component: () => import('@/views/application/express/sender_edit.vue')
            },
            {
                path: '/express/face_sheet',
                name: 'face_sheet',
                meta: {
                    title: '面单模版',
                    parentPath: '/application',
                    moduleName: 'express',
                    icon: 'icon_kdzs_mdmb',
                    permission: ['view']
                },
                component: () => import('@/views/application/express/face_sheet.vue')
            },
            {
                path: '/express/face_sheet_edit',
                name: 'face_sheet_edit',
                meta: {
                    hidden: true,
                    title: '电子面单模版编辑',
                    parentPath: '/application',
                    moduleName: 'express',
                    prevPath: '/express/face_sheet'
                },
                component: () => import('@/views/application/express/face_sheet_edit.vue')
            },
            {
                path: '/express/face_sheet_set',
                name: 'face_sheet_set',
                meta: {
                    title: '面单设置',
                    parentPath: '/application',
                    moduleName: 'express',
                    icon: 'icon_kdzs_mdsz',
                    permission: ['view']
                },
                component: () => import('@/views/application/express/face_sheet_set.vue')
            }
        ]
    },
    // ----小票打印
    {
        path: '/print',
        name: 'print',
        meta: {
            title: '打印机管理',
            hidden: true,
            moduleName: 'print'
        },
        redirect: '/print/list',
        component: Main,
        children: [
            {
                path: '/print/list',
                name: 'print',
                meta: {
                    title: '打印机管理',
                    parentPath: '/application',
                    moduleName: 'print',
                    icon: 'icon_xpdy_dyjgl',
                    permission: ['view']
                },
                component: () => import('@/views/application/print/list.vue')
            },
            {
                path: '/print/edit_print',
                name: 'edit_print',
                meta: {
                    hidden: true,
                    title: '打印机',
                    parentPath: '/application',
                    moduleName: 'print',
                    prevPath: '/print/list'
                },
                component: () => import('@/views/application/print/edit_print.vue')
            },
            {
                path: '/print/template',
                name: 'template',
                meta: {
                    title: '模板管理',
                    parentPath: '/application',
                    icon: 'icon_xpdy_mbgl',
                    moduleName: 'print',
                    permission: ['view']
                },
                component: () => import('@/views/application/print/template.vue')
            },
            {
                path: '/print/edit_template',
                name: 'edit_template',
                meta: {
                    hidden: true,
                    title: '模板管理',
                    parentPath: '/application',
                    moduleName: 'print',
                    prevPath: '/print/template',
                    icon: 'icon_qiandao_jilu'
                },
                component: () => import('@/views/application/print/edit_template.vue')
            }
        ]
    },

    // 充值
    {
        path: '/recharge',
        name: 'recharge',
        meta: {
            title: '充值概览',
            hidden: true,
            moduleName: 'recharge'
        },
        redirect: '/recharge/survey',
        component: Main,
        children: [
            {
                path: '/recharge/survey',
                name: 'survey',
                meta: {
                    title: '充值概览',
                    parentPath: '/application',
                    moduleName: 'recharge',
                    icon: 'icon_pintuan_data',
                    permission: ['view']
                },
                component: () => import('@/views/application/recharge/survey.vue')
            },
            {
                path: '/recharge/rule',
                name: 'rule',
                meta: {
                    title: '充值规则',
                    parentPath: '/application',
                    moduleName: 'recharge',
                    icon: 'icon_caiwu_yue',
                    permission: ['view']
                },
                component: () => import('@/views/application/recharge/rule.vue')
            },
            {
                path: '/recharge/record',
                name: 'record',
                meta: {
                    title: '充值记录',
                    parentPath: '/application',
                    moduleName: 'recharge',
                    icon: 'icon_order_guanli',
                    permission: ['view']
                },
                component: () => import('@/views/application/recharge/record.vue')
            },
            {
                path: '/recharge/commission',
                name: 'recharge_commission',
                meta: {
                    title: '充值佣金',
                    parentPath: '/application',
                    moduleName: 'recharge',
                    icon: 'icon_fenxiao_dingdan',
                    permission: ['view']
                },
                component: () => import('@/views/application/recharge/commission.vue')
            }
        ]
    },
    // 分销
    {
        path: '/distribution',
        name: 'distribution',
        meta: {
            title: '分销',
            hidden: true,
            moduleName: 'distribution'
        },
        redirect: '/distribution/survey',
        component: Main,
        children: [
            {
                path: '/distribution/survey',
                name: 'survey',
                meta: {
                    title: '分销概览',
                    parentPath: '/application',
                    moduleName: 'distribution',
                    icon: 'icon_fenxiao_data',
                    permission: ['view']
                },
                component: () => import('@/views/application/distribution/survey.vue')
            },
            {
                path: '/distribution/store',
                name: 'store',
                meta: {
                    title: '分销商',
                    parentPath: '/application',
                    moduleName: 'distribution',
                    icon: 'icon_fenxiao_member',
                    permission: ['view']
                },
                children: [
                    {
                        path: '/distribution/store',
                        name: 'distribution_store',
                        meta: {
                            title: '分销商',
                            parentPath: '/application',
                            moduleName: 'distribution',
                            permission: ['view']
                        },
                        component: () => import('@/views/application/distribution/store/store.vue')
                    },
                    {
                        path: '/distribution/store_detail',
                        name: 'distribution_store_detail',
                        meta: {
                            hidden: true,
                            title: '分销商详情',
                            parentPath: '/application',
                            prevPath: '/distribution/store',
                            moduleName: 'distribution'
                        },
                        component: () => import('@/views/application/distribution/store/store_detail.vue')
                    },
                    {
                        path: '/distribution/distribution_apply',
                        name: 'distribution_apply',
                        meta: {
                            title: '分销申请',
                            parentPath: '/application',
                            prevPath: '/distribution/distribution_apply',
                            moduleName: 'distribution',
                            permission: ['view']
                        },
                        component: () => import('@/views/application/distribution/store/apply.vue')
                    },
                    {
                        path: '/distribution/distribution_apply_details',
                        name: 'distribution_apply_details',
                        meta: {
                            hidden: true,
                            title: '分销申请详情',
                            parentPath: '/application',
                            prevPath: '/distribution/distribution_apply',
                            moduleName: 'distribution'
                        },
                        component: () => import('@/views/application/distribution/store/apply_details.vue')
                    },
                    {
                        path: '/distribution/lower_level_list',
                        name: 'lower_level_list',
                        meta: {
                            hidden: true,
                            title: '下级列表',
                            parentPath: '/application',
                            prevPath: '/distribution/store',
                            moduleName: 'distribution'
                        },
                        component: () => import('@/views/application/distribution/store/lower_level_list.vue')
                    }
                ],
                component: Blank
            },
            {
                path: '/distribution/distribution_goods',
                name: 'distribution_goods',
                meta: {
                    title: '分销商品',
                    parentPath: '/application',
                    icon: 'icon_fenxiao_goods',
                    moduleName: 'distribution',
                    permission: ['view']
                },
                component: () => import('@/views/application/distribution/distribution_goods.vue')
            },
            {
                path: '/distribution/distribution_goods_edit',
                name: 'distribution_goods_edit',
                meta: {
                    hidden: true,
                    title: '设置分销佣金',
                    parentPath: '/application',
                    prevPath: '/distribution/distribution_goods',
                    moduleName: 'distribution'
                },
                component: () => import('@/views/application/distribution/distribution_goods_edit.vue')
            },
            {
                path: '/distribution/distribution_orders',
                name: 'distribution_orders',
                meta: {
                    title: '分销订单',
                    parentPath: '/application',
                    icon: 'icon_dianpu_xiangqing',
                    moduleName: 'distribution',
                    permission: ['view']
                },
                component: () => import('@/views/application/distribution/distribution_orders.vue')
            },
            {
                path: '/distribution/distribution_grade',
                name: 'distribution_grade',
                meta: {
                    title: '分销等级',
                    parentPath: '/application',
                    icon: 'icon_fenxiao_grade',
                    moduleName: 'distribution',
                    permission: ['view']
                },
                component: () => import('@/views/application/distribution/distribution_grade.vue')
            },
            {
                path: '/distribution/distribution_grade_edit',
                name: 'distribution_grade_edit',
                meta: {
                    hidden: true,
                    title: '分销等级编辑',
                    parentPath: '/application',
                    prevPath: '/distribution/distribution_grade',
                    moduleName: 'distribution'
                },
                component: () => import('@/views/application/distribution/distribution_grade_edit.vue')
            },
            {
                path: 'distribution/setting',
                name: 'setting',
                meta: {
                    title: '分销设置',
                    parentPath: '/application',
                    moduleName: 'distribution',
                    icon: 'icon_fenxiao_set'
                },
                component: Blank,
                children: [
                    {
                        path: '/distribution/base_setting',
                        name: 'base_setting',
                        meta: {
                            title: '基础设置',
                            parentPath: '/application',
                            moduleName: 'distribution',
                            permission: ['view']
                        },
                        component: () => import('@/views/application/distribution/setting/base_setting.vue')
                    },
                    {
                        path: '/distribution/result_setting',
                        name: 'result_setting',
                        meta: {
                            title: '结算设置',
                            parentPath: '/application',
                            moduleName: 'distribution',
                            permission: ['view']
                        },
                        component: () => import('@/views/application/distribution/setting/result_setting.vue')
                    }
                ]
            }
        ]
    },
    // 积分商城
    {
        path: '/integral_mall',
        name: 'integral_mall',
        meta: {
            title: '积分商城',
            hidden: true,
            moduleName: 'integral_mall'
        },
        redirect: '/integral_mall/integral_goods',
        component: Main,
        children: [
            {
                path: '/integral_mall/integral_goods',
                name: 'integral_goods',
                meta: {
                    title: '积分商品',
                    parentPath: '/application',
                    moduleName: 'integral_mall',
                    icon: 'icon_goods',
                    permission: ['view']
                },
                component: () => import('@/views/application/integral_mall/integral_goods.vue')
            },
            {
                path: '/integral_mall/exchange_order',
                name: 'exchange_order',
                meta: {
                    title: '兑换订单',
                    parentPath: '/application',
                    moduleName: 'integral_mall',
                    icon: 'icon_set_jiaoyi',
                    permission: ['view']
                },
                component: () => import('@/views/application/integral_mall/exchange_order.vue')
            },
            {
                path: '/integral_mall/integral_goods_edit',
                name: 'integral_goods_edit',
                meta: {
                    hidden: true,
                    title: '积分商品',
                    parentPath: '/application',
                    moduleName: 'integral_mall',
                    prevPath: '/integral_mall/integral_goods'
                },
                component: () => import('@/views/application/integral_mall/integral_goods_edit.vue')
            },
            {
                path: '/integral_mall/exchange_order_detail',
                name: 'exchange_order_detail',
                meta: {
                    hidden: true,
                    title: '兑换订单详情',
                    parentPath: '/application',
                    moduleName: 'integral_mall',
                    prevPath: '/integral_mall/exchange_order'
                },
                component: () => import('@/views/application/integral_mall/exchange_order_detail.vue')
            }
        ]
    },
    // 足迹气泡
    {
        path: '/footprint',
        name: 'footprint',
        meta: {
            hidden: true,
            title: '足迹气泡',
            moduleName: 'footprint'
        },
        redirect: '/footprint/index',
        component: Main,
        children: [
            {
                path: '/footprint/index',
                name: 'footprint_index',
                meta: {
                    title: '足迹气泡',
                    parentPath: '/application',
                    moduleName: 'footprint',
                    icon: 'icon_pingjia',
                    permission: ['view']
                },
                component: () => import('@/views/application/footprint/index.vue')
            },
            {
                path: '/footprint/edit',
                name: 'footprint_edit',
                meta: {
                    title: '气泡设置',
                    parentPath: '/application',
                    moduleName: 'footprint',
                    icon: 'icon_danwei',
                    permission: ['view']
                },
                component: () => import('@/views/application/footprint/setting.vue')
            }
        ]
    },
    // 自定义海报
    {
        path: '/goods_collection',
        name: 'custom_poster',
        meta: {
            hidden: true,
            title: '自定义海报',
            moduleName: 'custom_poster'
        },
        redirect: '/custom_poster/goods',
        component: Main,
        children: [
            {
                path: '/custom_poster/goods',
                name: 'custom_poster_goods',
                meta: {
                    title: '商品海报',
                    parentPath: '/application',
                    moduleName: 'custom_poster',
                    icon: 'icon_pintuan',
                    permission: ['view']
                },
                component: () => import('@/views/application/custom_poster/goods.vue')
            },
            {
                path: '/custom_poster/invitation',
                name: 'custom_poster',
                meta: {
                    title: '邀请海报',
                    parentPath: '/application',
                    moduleName: 'custom_poster',
                    icon: 'icon_dianpu_sucai',
                    permission: ['view']
                },
                component: () => import('@/views/application/custom_poster/invitation.vue')
            }
        ]
    },
    // 商品采集
    {
        path: '/goods_collection',
        name: 'goods_collection',
        meta: {
            hidden: true,
            title: '商品采集',
            moduleName: 'goods_collection'
        },
        redirect: '/goods_collection/index',
        component: Main,
        children: [
            {
                path: '/goods_collection/index',
                name: 'goods_collection_index',
                meta: {
                    title: '商品采集',
                    parentPath: '/application',
                    moduleName: 'goods_collection',
                    icon: 'icon_pintuan',
                    permission: ['view']
                },
                component: () => import('@/views/application/goods_collection/index.vue')
            },
            {
                path: '/goods_collection/details',
                name: 'goods_collection_details',
                meta: {
                    hidden: true,
                    title: '查看采集详情',
                    parentPath: '/application',
                    moduleName: 'goods_collection',
                    prevPath: '/goods_collection/index'
                },
                component: () => import('@/views/application/goods_collection/details.vue')
            }
        ]
    }
,
    {
        path: '/activity_deduct',
        name: 'activity_deduct',
        meta: { hidden: true, title: '充值抵扣', moduleName: 'activity_deduct' },
        redirect: '/activity_deduct/config',
        component: Main,
        children: [
            {
                path: '/activity_deduct/config',
                name: 'activity_deduct_config',
                meta: { title: '充值抵扣', parentPath: '/application', moduleName: 'activity_deduct', icon: 'icon_activity', permission: ['view'] },
                component: () => import('@/views/application/activity_deduct/index.vue')
            }
        ]
    }
]

export default routes
