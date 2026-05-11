import { serviceEnum } from '@/utils/enum'
import { getClient } from '@/utils/tools'

export default {
    // 公共配置
    appConfig: (state) => state.app.config || {},
    // 客服配置
    ServiceConfig: (state) => state.app.serviceConfig[serviceEnum[getClient()]] || {},
    // 分享配置
    shareConfig: (state) => state.app.shareConfig || [],

    // 用户信息
    userInfo: (state) => state.app.userInfo || {},
    // token
    token: (state) => state.app.token,
    // 客户端
    client: (state) => state.app.client,
    // 是否登录
    isLogin: (state) => !!state.app.token,
    // 主题名字
    themeName: (state) => state.decorate.config.theme || 'red_theme',
    // 主题颜色
    themeColor: (state) => {
        const { theme, config } = state.decorate
        return theme[config.theme] || '#FF2C3C'
    },
    // 底部导航
    tabbar: (state) => state.decorate.config.tabbar || {},
    //弹窗广告
    screen: (state) => state.decorate.config.screen || {},
    site_statistic: (state) => state.app.config.site_statistic,
    unreadCount: (state) => state.notification.unreadCount,
    showNotificationPopup: (state) => state.notification.showPopup,
    currentNotice: (state) => state.notification.currentNotice,
}
