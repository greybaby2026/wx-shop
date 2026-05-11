import app from '@/store/modules/app'

export const mobileLink = [
    {
        name: '商城页面',
        key: 'shop',
        children: [
            {
                name: '基础页面',
                type: 'shop',
                link: {
                    path: '',
                    name: '',
                    params: {},
                    type: 'shop'
                }
            },
            {
                name: '微页面',
                type: 'page',
                link: {
                    path: '/pages/page/page',
                    name: '微页面',
                    params: {},
                    type: 'page'
                }
            }
        ]
    },
    {
        name: '商品',
        key: 'goods',
        children: [
            {
                name: '普通商品',
                type: 'goods',
                link: {
                    path: '/pages/goods_detail/goods_detail',
                    name: '普通商品',
                    params: {},
                    type: 'goods'
                }
            },
            {
                name: '秒杀商品',
                type: 'seckill',
                link: {
                    path: '/bundle/pages/seckill_detail/seckill_detail',
                    name: '秒杀商品',
                    params: {},
                    type: 'seckill'
                }
            },
            {
                name: '拼团商品',
                type: 'team',
                link: {
                    path: '/bundle/pages/goods_team_detail/goods_team_detail',
                    name: '拼团商品',
                    params: {},
                    type: 'team'
                }
            },
            {
                name: '预售商品',
                type: 'presell',
                link: {
                    path: '/bundle/pages/presell_detail/presell_detail',
                    name: '预售商品',
                    params: {},
                    type: 'presell'
                }
            },
            // {
            //     name: '砍价商品',
            //     type: 'bargain',
            //     link: {
            //         path: '/bundle/pages/bargain_progress/bargain_progress',
            //         name: '砍价商品',
            //         params: {},
            //         type: 'bargain',
            //     },
            // },
            {
                name: '商品分类',
                type: 'category',
                link: {
                    path: '/bundle/pages/category/category',
                    name: '商品分类',
                    params: {},
                    type: 'category'
                }
            }
        ]
    },
    {
        name: '营销活动',
        key: 'marking',
        children: [
            {
                name: '营销活动页面',
                type: 'marking',
                link: {
                    path: '',
                    name: '',
                    params: {},
                    type: 'marking'
                }
            },
            {
                name: '积分抽奖',
                type: 'draw',
                link: {
                    path: '/bundle/pages/luck_draw/luck_draw',
                    name: '积分抽奖',
                    params: {},
                    type: 'draw'
                }
            }
        ]
    },
    {
        name: '其他',
        key: 'other',
        children: [
            {
                name: '自定义链接',
                type: 'custom',
                link: {
                    path: '/pages/webview/webview',
                    name: '自定义链接',
                    params: {
                        url: ''
                    },
                    type: 'custom'
                }
            },
            {
                name: '微信小程序',
                type: 'mini_program',
                link: {
                    path: '',
                    name: '微信小程序',
                    params: {
                        originalId: '',
                        appId: '',
                        path: '',
                        query: '',
                        envVersion: 'release'
                    },
                    type: 'mini_program'
                }
            }
        ]
    }
]

export const pcLink = [
    {
        name: '商城页面',
        key: 'shop',
        children: [
            {
                name: '基础页面',
                type: 'shop',
                link: {
                    path: '',
                    name: '',
                    params: {},
                    type: 'shop'
                }
            }
        ]
    },
    {
        name: '商品',
        key: 'goods',
        children: [
            {
                name: '普通商品',
                type: 'goods',
                link: {
                    path: '/goods_details',
                    name: '普通商品',
                    params: {},
                    type: 'goods'
                }
            },
            {
                name: '秒杀商品',
                type: 'seckill',
                link: {
                    path: '/goods_details_seckill',
                    name: '秒杀商品',
                    params: {},
                    type: 'seckill'
                }
            },
            {
                name: '商品分类',
                type: 'category',
                link: {
                    path: '/category',
                    name: '商品分类',
                    params: {},
                    type: 'category'
                }
            }
        ]
    },
    {
        name: '营销活动',
        key: 'marking',
        children: [
            {
                name: '营销活动页面',
                type: 'marking',
                link: {
                    path: '',
                    name: '',
                    params: {},
                    type: 'marking'
                }
            }
        ]
    },
    {
        name: '其他',
        key: 'other',
        children: [
            {
                name: '自定义链接',
                type: 'custom',
                link: {
                    params: {},
                    path: '',
                    name: '自定义链接',
                    type: 'custom'
                }
            }
        ]
    }
]
