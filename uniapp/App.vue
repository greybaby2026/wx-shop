<script>
import { mapActions, mapGetters, mapMutations } from 'vuex'
import { apiDistributionCode } from '@/api/user'
import { strToParams } from '@/utils/tools'
import { INVITE_CODE } from '@/config/cachekey'
import Cache from '@/utils/cache'
export default {
    async onLaunch(options) {
        Cache.set('OPENIMAGE_ENABLE', true)

        console.log(options)
        // 获取公共配置
        await this.getConfig().then((res) => {
            // 当后台配置商城停止后相关商城进行跳转到空页面
            // #ifdef H5
            let favicon = document.querySelector('link[rel="icon"]')
            if (favicon) {
                favicon.href = res.favicon
                return
            }
            favicon = document.createElement('link')
            favicon.rel = 'icon'
            favicon.href = res.favicon
            document.head.appendChild(favicon)
            if (!res.h5_status) {
                setTimeout(() => {
                    uni.navigateTo({
                        url: '/bundle/pages/business_suspended/business_suspended'
                    })
                }, 0)
            }

            // #endif
            // #ifdef MP-WEIXIN
            if (!res.mnp_status) {
                setTimeout(() => {
                    uni.navigateTo({
                        url: '/bundle/pages/business_suspended/business_suspended'
                    })
                }, 0)
            }
            // #endif
        })
        // 获取主题配置
        await this.getDecorateConfig()

        // 获取个人信息
        this.getUser().then((res) => {
            // 更新购物车信息
            this.$store.dispatch('getCartNum')
            if (this.bindMobile && !res.mobile) {
                this.logout()
            }
        })
        //#ifdef H5
        const { clarity_app_id } = this.site_statistic

        if (clarity_app_id) {
            ;(function (c, l, a, r, i, t, y) {
                c[a] =
                    c[a] ||
                    function () {
                        ;(c[a].q = c[a].q || []).push(arguments)
                    }
                t = l.createElement(r)
                t.async = 1
                t.src = 'https://www.clarity.ms/tag/' + i
                y = l.getElementsByTagName(r)[0]
                y.parentNode.insertBefore(t, y)
            })(window, document, 'clarity', 'script', clarity_app_id)
        }

        //#endif
    },
    onLoad: function () {
        uni.hideTabBar()
        Cache.set('OPENIMAGE_ENABLE', true)
    },
    onUnload() {},
    onShow: async function (options) {
        uni.hideTabBar()
        this.bindCode(options)
        await this.getsetshareConfig()
    },
    onHide: function () {
        console.log('App Hide')
    },
    computed: {
        ...mapGetters(['site_statistic']),

        bindMobile() {
            // 强制绑定手机号
            return this.appConfig.coerce_mobile
        }
    },
    methods: {
        ...mapActions([
            'getConfig',
            'getDecorateConfig',
            'getUser',
            'getsetserviceConfig',
            'getsetshareConfig'
        ]),
        ...mapMutations(['logout']),
        async bindCode(options) {
            if (!options.query) return
            let invite_code =
                options.query.invite_code ||
                strToParams(decodeURIComponent(options.query.scene)).invite_code
            console.log(options)
            if (invite_code) {
                apiDistributionCode({
                    code: invite_code,
                    hide: 1
                }).catch(() => {
                    Cache.set(INVITE_CODE, invite_code)
                })
            }
        }
    }
}
</script>

<style lang="scss">
/*每个页面公共css */
@import 'styles/common.scss';
@import 'plugin/emoji-awesome/css/apple.css';
@import 'components/uview-ui/index.scss';
</style>
