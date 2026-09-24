<script>
import { mapActions, mapGetters, mapMutations } from 'vuex'
import { apiDistributionCode, apiUserBindStore } from '@/api/user'
import { strToParams } from '@/utils/tools'
import { INVITE_CODE, PENDING_STORE_ID } from '@/config/cachekey'
import Cache from '@/utils/cache'
export default {
    async onLaunch(options) {
        // ⚠️ 开屏广告「只展示一次」隐式约定（与 components/open-advertisement 联动，勿单独删除任一侧）：
        //    此处把 OPENIMAGE_ENABLE 置为 true 表示「本次冷启动允许展示」，组件展示后会置为 false，
        //    从而保证同一次启动内只弹一次。判断 show_config=2 的次数则记在 OPENIMAGE_NUMBER。
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
        // 同上：每次冷启动重置一次，使开屏广告在本轮启动内可再展示一次
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
                if (!this.$store.getters.token) {
                    Cache.set(PENDING_STORE_ID, store_id)
                } else {
                    // 已登录:立即绑定并给出明确反馈(绑定成功/已绑定其他门店/门店已停用)
                    apiUserBindStore({ store_id: store_id, hide: 1 })
                        .then((res) => {
                            if (res && res.is_new) {
                                this.$toast({ title: `已加入${res.store_name || ''}` })
                            } else if (
                                res &&
                                res.store_id &&
                                String(res.store_id) !== String(store_id)
                            ) {
                                this.$toast({ title: res.msg || '您已加入其他门店，无法更换' })
                            }
                        })
                        .catch((msg) => {
                            this.$toast({
                                title: typeof msg === 'string' && msg ? msg : '门店绑定失败'
                            })
                        })
                }
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
