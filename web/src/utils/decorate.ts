import title from '@/components/decorate/widgets/title'

const icon_search = require('@/assets/images/icon_search.png')
const icon_title = require('@/assets/images/icon_title.png')
const icon_banner = require('@/assets/images/icon_banner.png')
const icon_navigation = require('@/assets/images/icon_navigation.png')
const icon_rubik = require('@/assets/images/icon_rubik.png')
const icon_tabs = require('@/assets/images/icon_tabs.png')
const icon_notice = require('@/assets/images/icon_notice.png')
const icon_blank = require('@/assets/images/icon_search.png')
const icon_separate = require('@/assets/images/icon_separate.png')
const icon_goods = require('@/assets/images/icon_goods.png')
const icon_coupon = require('@/assets/images/icon_coupon.png')
const icon_seckill = require('@/assets/images/icon_seckill.png')
const icon_spellgroup = require('@/assets/images/icon_spellgroup.png')
const icon_video = require('@/assets/images/icon_video.png')
const icon_graphic = require('@/assets/images/icon_graphic.png')
const icon_mnplive = require('@/assets/images/icon_mnplive.png')
const icon_adv = require('@/assets/images/icon_adv.png')
const icon_hot_area = require('@/assets/images/icon_hotarea.png')
const icon_presell = require('@/assets/images/icon_presell.png')
const icon_editor = require('@/assets/images/icon_editor.png')

export const decorateMenu = [
    {
        title: '常用组件',
        name: 'basics',
        children: [
            {
                title: '搜索框',
                name: 'search',
                icon: icon_search,
                show: 1,
                content: {
                    text: '请输入关键字搜索'
                },
                styles: {
                    text_align: 'left',
                    border_radius: 0,
                    root_bg_color: '',
                    bg_color: '#FFFFFF',
                    icon_color: '#999999',
                    color: '#999999',
                    padding_top: 12,
                    padding_horizontal: 15,
                    padding_bottom: 12
                }
            },
            {
                title: '标题',
                name: 'title',
                icon: icon_title,
                show: 1,
                content: {
                    style: 1,
                    title: '主标题',
                    subtitle: '副标题',
                    hidden_subtitle: 0,
                    show_more: 1,
                    more_title: '更多',
                    link: {}
                },
                styles: {
                    root_bg_color: '',
                    bg_color: '#F5F5F5',
                    title_color: '#333333',
                    title_font_size: 14,
                    subtitle_color: '#999999',
                    subtitle_font_size: 12,
                    more_color: '#999999',
                    padding_top: 0,
                    padding_horizontal: 0,
                    padding_bottom: 0,
                    border_radius_top: 0,
                    border_radius_bottom: 0
                }
            },
            {
                title: '轮播图',
                name: 'banner',
                icon: icon_banner,
                show: 1,
                content: {
                    data: [
                        {
                            url: '',
                            link: {}
                        }
                    ]
                },
                styles: {
                    root_bg_color: 'rgba(0,0,0,0)',
                    border_radius: 0, // 图片圆角
                    indicator_style: 1, //指示器样式
                    indicator_align: 'left', //指示器位置
                    indicator_color: '#FF2C3C', //指示器颜色
                    padding_top: 0,
                    padding_horizontal: 0,
                    padding_bottom: 0
                }
            },
            {
                title: '菜单导航',
                name: 'navigation',
                icon: icon_navigation,
                show: 1,
                content: {
                    style: 1,
                    data: [
                        {
                            url: '',
                            name: '导航',
                            link: {}
                        },
                        {
                            url: '',
                            name: '导航',
                            link: {}
                        },
                        {
                            url: '',
                            name: '导航',
                            link: {}
                        },
                        {
                            url: '',
                            name: '导航',
                            link: {}
                        }
                    ]
                },
                styles: {
                    nav_style: 1,
                    nav_line: 2,
                    nav_line_num: 4,
                    indicator_style: 1, //指示器样式
                    indicator_color: '#FF2C3C', //指示器颜色
                    bg_color: '#FFFFFF',
                    root_bg_color: '',
                    color: '#333333',
                    padding_top: 0,
                    padding_horizontal: 0,
                    padding_bottom: 0,
                    border_radius_top: 0,
                    border_radius_bottom: 0,
                    img_border_radius: 0
                }
            },
            {
                title: '图片魔方',
                name: 'rubik',
                icon: icon_rubik,
                show: 1,
                content: {
                    style: 1,
                    data: [
                        {
                            url: '',
                            link: {}
                        }
                    ]
                },
                styles: {
                    border_radius: 0,
                    root_bg_color: '',
                    line_color: '#e5e5e5',
                    font_color: '#333',
                    padding_top: 0,
                    padding_horizontal: 0,
                    padding_bottom: 0
                }
            },
            {
                title: '公告',
                name: 'notice',
                icon: icon_notice,
                show: 1,
                content: {
                    icon: '',
                    icon_type: 1,
                    show_tag: 1,
                    num: 1,
                    data: []
                },
                styles: {
                    root_bg_color: '',
                    bg_color: '#FFFFFF',
                    border_radius: 7,
                    line_color: '#E5E5E5',
                    color: '#333333',
                    padding_top: 0,
                    padding_horizontal: 0,
                    padding_bottom: 0
                }
            },
            {
                title: '视频',
                name: 'video',
                icon: icon_video,
                show: 1,
                content: {
                    url: '',
                    video_type: 1,
                    poster: '',
                    proportion: 1 //比列
                },
                styles: {
                    root_bg_color: '',
                    border_radius_top: 0,
                    border_radius_bottom: 0,
                    padding_top: 0,
                    padding_horizontal: 0,
                    padding_bottom: 0
                }
            },
            {
                title: '图文',
                name: 'graphic',
                icon: icon_graphic,
                show: 1,
                content: {
                    data: [
                        {
                            url: '',
                            title: '标题名称',
                            title_color: '#333333',
                            subtitle: '副标题名称',
                            subtitle_color: '#666666',
                            link: {},
                            bg_color: '#FFFFFF'
                        }
                    ]
                },
                styles: {
                    root_bg_color: '',
                    border_radius_top: 0,
                    border_radius_bottom: 0,
                    padding_top: 10,
                    padding_horizontal: 10,
                    padding_bottom: 0
                }
            },
            {
                title: '热区',
                name: 'hotarea',
                icon: icon_hot_area,
                show: 1,
                content: {
                    data: {
                        imgurl: '',
                        areaLists: []
                    }
                },
                styles: {
                    root_bg_color: '',
                    border_radius_top: 0,
                    border_radius_bottom: 0,
                    padding_top: 0,
                    padding_horizontal: 0,
                    padding_bottom: 0
                }
            },
            {
                title: '富文本',
                name: 'edit',
                icon: icon_editor,
                show: 1,
                content: {
                    data: '<p><font face="微软雅黑">点击右侧编辑「富文本」👉</font></p><p><font face="微软雅黑">您可以对文字进行<b>加粗</b>、<i>斜体</i>、<u>下划线</u>、<strike>删除线</strike>、<font color="#46acc8">文字颜色</font>、以及字号<font size="5">大</font>小等简单排版操作。</font></p><p><font face="微软雅黑">也可以在这里插入图片，方便用户查看。</font></p>'
                },
                styles: {
                    root_bg_color: '',
                    border_radius_top: 6,
                    border_radius_bottom: 6,
                    padding_top: 10,
                    padding_horizontal: 10,
                    padding_bottom: 0
                }
            }
        ]
    },
    {
        title: '商品组件',
        name: 'goods',
        children: [
            {
                title: '商品组',
                name: 'goodsgroup',
                icon: icon_goods,
                show: 1,
                content: {
                    style: 1,
                    goods_type: 1,
                    show_title: 1,
                    show_price: 1,
                    show_scribing_price: 1,
                    show_btn: 1,
                    btn_text: '购买',
                    category: {
                        id: '',
                        name: '',
                        num: 1
                    },
                    data: []
                },
                styles: {
                    title_color: '#333333',
                    scribing_price_color: '#999999',
                    price_color: '#FF2C3C',
                    btn_bg_color: '#FF2C3C',
                    content_bg_color: '',
                    btn_color: '#FFFFFF',
                    btn_border_radius: 30,
                    btn_border_color: '',
                    root_bg_color: '',
                    bg_color: '#FFFFFF',
                    margin: 10,
                    padding: 10,
                    padding_top: 10,
                    padding_horizontal: 10,
                    padding_bottom: 10,
                    border_radius_top: 0,
                    border_radius_bottom: 0,
                    goods_border_radius: 4
                }
            },

            {
                title: '选项卡',
                name: 'tabs',
                icon: icon_tabs,
                show: 1,
                content: {
                    active: 0,
                    show_line: 1,
                    has_active_bg: 0,
                    data: [
                        {
                            name: '选项卡',
                            style: 1,
                            goods_type: 1,
                            show_title: 1,
                            show_price: 1,
                            show_scribing_price: 1,
                            show_btn: 1,
                            btn_text: '购买',
                            category: {
                                id: '',
                                name: '',
                                num: 1
                            },
                            title_color: '#333333',
                            scribing_price_color: '#999999',
                            price_color: '#FF2C3C',
                            btn_bg_color: '#FF2C3C',
                            btn_color: '#FFFFFF',
                            btn_border_radius: 30,
                            btn_border_color: '',
                            root_bg_color: '',
                            content_bg_color: '',
                            bg_color: '#FFFFFF',
                            padding: 0,
                            margin: 10,
                            padding_top: 10,
                            padding_horizontal: 10,
                            padding_bottom: 10,
                            goods_border_radius: 4,
                            data: []
                        }
                    ]
                },
                styles: {
                    root_bg_color: '',
                    bg_color: '#FFFFFF',
                    color: '#333333',
                    active_color: '#FF2C3C',
                    line_color: '#FF2C3C',
                    active_bg_color: '',
                    padding: 10,
                    padding_top: 0,
                    padding_horizontal: 0,
                    padding_bottom: 0,
                    border_radius_top: 0,
                    border_radius_bottom: 0
                }
            }
        ]
    },
    {
        title: '营销组件',
        name: 'marketing',
        children: [
            {
                title: '优惠券',
                name: 'coupon',
                icon: icon_coupon,
                show: 1,
                content: {
                    style: 1,
                    title: '超值优惠券',
                    bg_type: 1,
                    data: []
                },
                styles: {
                    root_bg_color: '#FFFFFF',
                    bg_color: '#FCE7E7',
                    title_color: '#FFFFFF',
                    bg_image: '',
                    text_color: '#FF2C3C',
                    btn_bg_color: '#FF2C3C',
                    btn_text_color: '#FFFFFF',
                    padding_top: 10,
                    padding_horizontal: 10,
                    padding_bottom: 10,
                    money_color: '#FF2C3C',
                    condition_color: '#333333',
                    scene_color: '#999999'
                }
            },
            {
                title: '限时秒杀',
                name: 'seckill',
                icon: icon_seckill,
                show: 1,
                content: {
                    style: 1,
                    data_type: 1,
                    num: 1,
                    data: [],
                    header_bg_type: 1,
                    header_bg_image: '',
                    header_icon_image: '',
                    header_title: '超值秒杀',
                    show_haeder_more: 1,
                    header_more_text: '更多',
                    show_title: 1,
                    show_price: 1,
                    show_scribing_price: 1,
                    show_sell: 1,
                    show_btn: 1,
                    btn_text: '马上抢'
                },
                styles: {
                    root_bg_color: '',
                    content_bg_color: '#FFFFFF',
                    goods_bg_color: '#F8F8F8',
                    header_title_color: '#FFFFFF',
                    header_more_color: '#FFFFFF',
                    header_bg_color: '#FF624B',
                    title_color: '#333333',
                    scribing_price_color: '#999999',
                    sell_color: '#999999',
                    price_color: '#FF2C3C',
                    btn_bg_color: '#FF2C3C',
                    btn_color: '#FFFFFF',
                    header_title_size: 20,
                    padding_top: 10,
                    margin: 10,
                    padding_horizontal: 10,
                    padding_bottom: 10,
                    goods_border_radius: 4,
                    border_radius_top: 4,
                    border_radius_bottom: 4
                }
            },
            {
                title: '预售活动',
                name: 'presell',
                icon: icon_presell,
                show: 1,
                content: {
                    style: 1,
                    data_type: 1,
                    num: 1,
                    data: [],
                    header_bg_type: 1,
                    header_bg_image: '',
                    header_icon_image: '',
                    header_title: '预售精选',
                    show_haeder_more: 1,
                    header_more_text: '更多',
                    show_title: 1,
                    show_price: 1,
                    show_scribing_price: 1,
                    show_sell: 1,
                    show_btn: 1,
                    btn_text: '预定'
                },
                styles: {
                    root_bg_color: '',
                    content_bg_color: '#FFFFFF',
                    goods_bg_color: '#F8F8F8',
                    header_title_color: '#FFFFFF',
                    header_more_color: '#FFFFFF',
                    header_bg_color: '#B54BD5',
                    title_color: '#333333',
                    scribing_price_color: '#999999',
                    sell_color: '#999999',
                    price_color: '#FF2C3C',
                    btn_bg_color: '#A411D1',
                    btn_color: '#FFFFFF',
                    header_title_size: 20,
                    padding_top: 10,
                    margin: 10,
                    padding_horizontal: 10,
                    padding_bottom: 10,
                    goods_border_radius: 4,
                    border_radius_top: 4,
                    border_radius_bottom: 4
                }
            },
            {
                title: '拼团活动',
                name: 'spellgroup',
                icon: icon_spellgroup,
                show: 1,
                content: {
                    data: [],
                    style: 1,
                    data_type: 1,
                    num: 1,
                    header_bg_type: 1,
                    header_bg_image: '',
                    header_icon_image: '',
                    header_title: '今日必拼',
                    show_haeder_more: 1,
                    header_more_text: '更多',
                    show_title: 1,
                    show_price: 1,
                    show_scribing_price: 1,
                    show_sell: 1,
                    show_btn: 1,
                    btn_text: '去拼团',
                    show_group_num: 1
                },
                styles: {
                    root_bg_color: '',
                    content_bg_color: '#FFFFFF',
                    goods_bg_color: '#F8F8F8',
                    header_title_color: '#FFFFFF',
                    header_more_color: '#FFFFFF',
                    header_bg_color: '#FF5382',
                    title_color: '#333333',
                    scribing_price_color: '#999999',
                    sell_color: '#999999',
                    price_color: '#FF2C3C',
                    btn_bg_color: '#FF2C3C',
                    btn_color: '#FFFFFF',
                    group_num_color: '#FF2C3C',
                    header_title_size: 20,
                    padding_top: 10,
                    margin: 10,
                    padding_horizontal: 10,
                    padding_bottom: 10,
                    goods_border_radius: 4,
                    border_radius_top: 4,
                    border_radius_bottom: 4
                }
            },
            {
                title: '小程序直播',
                name: 'mnplive',
                icon: icon_mnplive,
                show: 1,
                content: {
                    style: 1,
                    num: 1,
                    data: [],
                    header_bg_type: 1,
                    header_bg_image: '',
                    header_icon_image: '',
                    header_title: '直播间',
                    show_haeder_more: 1,
                    header_more_text: '更多'
                },
                styles: {
                    root_bg_color: '',
                    content_bg_color: '',
                    bg_color: '#FFFFFF',
                    header_title_color: '#333333',
                    header_more_color: '#999999',
                    header_bg_color: '',
                    title_color: '#333333',
                    header_title_size: 18,
                    padding_top: 0,
                    margin: 20,
                    padding_horizontal: 10,
                    padding_bottom: 0,
                    goods_border_radius: 4,
                    border_radius_top: 4,
                    border_radius_bottom: 4
                }
            }
        ]
    },
    {
        title: '辅助类',
        name: 'auxiliary',
        children: [
            {
                title: '空白间距',
                name: 'blank',
                show: 1,
                icon: icon_blank,
                styles: {
                    height: 10,
                    bg_color: '#FFFFFF',
                    root_bg_color: '',
                    border_radius_bottom: 0,
                    padding_top: 0,
                    padding_horizontal: 0,
                    padding_bottom: 0
                }
            },
            {
                title: '分割线',
                show: 1,
                name: 'separate',
                icon: icon_separate,
                content: {
                    separate: 'solid'
                },
                styles: {
                    root_bg_color: '',
                    line_color: '#e5e5e5',
                    padding_top: 10,
                    padding_horizontal: 0,
                    padding_bottom: 10
                }
            }
        ]
    }
]

export const pcDecorateMenu = [
    {
        title: '广告位',
        name: 'adv',
        icon: icon_adv,
        show: 1,
        content: {
            data: [
                {
                    url: '',
                    link: {}
                }
            ],
            style: 1
        },
        styles: {}
    },
    {
        title: '商品组',
        name: 'goodsgroup',
        icon: icon_goods,
        show: 1,
        content: {
            data: [],
            title: '主标题',
            subtitle: '副标题',
            show_more: 1,
            show_adv: 1,
            adv_url: '',
            adv_link: {},
            goods_type: 1,
            category: {
                id: '',
                name: '',
                num: 1
            }
        },
        styles: {}
    },
    {
        title: '限时秒杀',
        name: 'seckill',
        icon: icon_seckill,
        show: 1,
        content: {
            data: [],
            title: '限时秒杀',
            show_more: 1,
            data_type: 1,
            num: 1
        },
        styles: {}
    }
]
