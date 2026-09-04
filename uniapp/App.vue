<script>
import { mapActions, mapGetters, mapMutations } from 'vuex'
import { apiDistributionCode, apiUserBindStore } from '@/api/user'
import { strToParams } from '@/utils/tools'
import { INVITE_CODE, PENDING_STORE_ID } from '@/config/cachekey'
import Cache from '@/utils/cache'
export default {
    async onLaunch(options) {
        Cache.set('OPENIMAGE_ENABLE', true)

        // 获取公共配置 + 主题配置（并行，无数据依赖）
        await Promise.all([
            this.getConfig().then((res) => {
                // #ifdef H5
                let favicon = document.querySelector('link[rel="icon"]')
                if (favicon) {
                    favicon.href = res.favicon
                } else {
                    favicon = document.createElement('link')
                    favicon.rel = 'icon'
                    favicon.href = res.favicon
                    document.head.appendChild(favicon)
                }
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
                return res
            }),
            this.getDecorateConfig()
        ])
        // 活动抵扣信息（不阻塞启动）
        this.getDeductInfo()

        // 获取个人信息 + 购物车（并行）
        this.getUser().then((res) => {
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
    onShow: function (options) {
        uni.hideTabBar()
        this.bindCode(options)
        // 分享配置和活动抵扣并行请求，不阻塞页面渲染
        this.getsetshareConfig()
        this.getDeductInfo()
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
            'getsetshareConfig',
            'getDeductInfo'
        ]),
        ...mapMutations(['logout']),
        async bindCode(options) {
            if (!options.query) return
            const sceneParams = strToParams(decodeURIComponent(options.query.scene || ''))
            const store_id = options.query.store_id || sceneParams.store_id
            if (store_id) {
                // 扫门店码绑定(首绑定终身):未登录先暂存,登录后补绑
                apiUserBindStore({ store_id: store_id, hide: 1 }).catch(() => {
                    Cache.set(PENDING_STORE_ID, store_id)
                })
            }
            let invite_code =
                options.query.invite_code ||
                sceneParams.invite_code
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
