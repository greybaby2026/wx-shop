import { serviceEnum } from '@/utils/enum'
import { getClient } from '@/utils/tools'

// 后端preset key -> SCSS英文类名映射（同时支持中文和英文key）
var DEDUCT_THEME_NAME_MAP = {
    'father': 'deduct_fathers_day',
    '父亲节': 'deduct_fathers_day',
    'mid_autumn': 'deduct_mid_autumn',
    '中秋': 'deduct_mid_autumn',
    'teacher': 'deduct_teachers_day',
    '教师节': 'deduct_teachers_day',
    'national': 'deduct_national_day',
    '国庆': 'deduct_national_day',
    'double11': 'deduct_double_11',
    '双十一': 'deduct_double_11',
    'double12': 'deduct_double_12',
    '双十二': 'deduct_double_12',
    'new_year': 'deduct_new_year',
    '元旦': 'deduct_new_year',
    'spring': 'deduct_spring_festival',
    '春节': 'deduct_spring_festival',
    'valentine': 'deduct_love',
    '爱情色': 'deduct_love',
    'mothers_day': 'deduct_mothers_day',
    '母亲节': 'deduct_mothers_day',
    '618': 'deduct_618',
    'default': 'deduct_default',
    'qixi': 'deduct_love',
    'school': 'deduct_default',
};

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
    themeName: (state) => {
        var deductInfo = (state.activity_deduct || {}).deductInfo;
        if (deductInfo && deductInfo.active) {
            var currentTheme = deductInfo.current_theme || '';
            if (currentTheme) {
                var mapped = DEDUCT_THEME_NAME_MAP[currentTheme];
                if (mapped) return mapped;
                if (/^[a-zA-Z0-9_]+$/.test(currentTheme)) {
                    return 'deduct_' + currentTheme;
                }
            }
        }
        return state.decorate.config.theme || 'red_theme';
    },
    // 主题颜色
    themeColor: (state) => {
        var deductInfo = (state.activity_deduct || {}).deductInfo;
        if (deductInfo && deductInfo.active && deductInfo.theme_config && deductInfo.theme_config.primary_color) {
            return deductInfo.theme_config.primary_color;
        }
        const { theme, config } = state.decorate
        return theme[config.theme] || '#FF2C3C'
    },
    // 主题次要颜色（渐变色/强调色）
    themeMinorColor: (state) => {
        var deductInfo = (state.activity_deduct || {}).deductInfo;
        if (deductInfo && deductInfo.active && deductInfo.theme_config) {
            // 后端使用accent_color字段
            if (deductInfo.theme_config.accent_color) {
                return deductInfo.theme_config.accent_color;
            }
            if (deductInfo.theme_config.minor_color) {
                return deductInfo.theme_config.minor_color;
            }
        }
        // 基础主题次要色映射（与uni.scss中$themes的minor_color保持一致）
        var MINOR_COLOR_MAP = {
            'red_theme': '#F95F2F',
            'orange_theme': '#ffd200',
            'pink_theme': '#fd498f',
            'gold_theme': '#ebc389',
            'blue_theme': '#56ccf2',
            'green_theme': '#3de650',
        };
        var currentTheme = state.decorate.config.theme || 'red_theme';
        return MINOR_COLOR_MAP[currentTheme] || '#F95F2F';
    },
    // 活动抵扣信息
    deductInfo: (state) => (state.activity_deduct || {}).deductInfo || {},
    // 底部导航
    tabbar: (state) => state.decorate.config.tabbar || {},
    //弹窗广告
    screen: (state) => state.decorate.config.screen || {},
    site_statistic: (state) => state.app.config.site_statistic,
    unreadCount: (state) => state.notification.unreadCount,
    showNotificationPopup: (state) => state.notification.showPopup,
    currentNotice: (state) => state.notification.currentNotice,
}
