import { mapGetters, mapState } from 'vuex'

const ServiceMixin = {
    data() {
        return
    },
    computed: {
        ...mapGetters(['ServiceConfig'])
    },
    methods: {
        goService() {
            console.log(this.ServiceConfig, 'ServiceConfig')
            const _this = this

            switch (+this.ServiceConfig.way) {
                case 1:
                    this.$Router.push({
                        path: `/bundle/pages/artificial_service/artificial_service`
                    })
                    break
                case 2:
                    if (!this.ServiceConfig.phone) {
                        return _this.$toast({
                            title: '请在后台配置客服电话号码'
                        })
                    }
                    uni.makePhoneCall({
                        phoneNumber: this.ServiceConfig.phone,
                        success(res) {},
                        fail(err) {}
                    })
                    break
                case 3:
                    this.$Router.push({
                        path: '/bundle/pages/service/service',
                        query: {
                            goods_id: this.goods_id,
                            type: 'normal'
                        }
                    })
                    break
                case 4:
                    // #ifdef MP-WEIXIN
                    wx.openCustomerServiceChat({
                        extInfo: { url: this.ServiceConfig.kefu_link },
                        corpId: this.ServiceConfig.enterprise_id,
                        success(res) {},
                        fail(err) {
                            console.log(err)

                            _this.$toast({
                                title: '请在后台配置企业微信客服'
                            })
                        }
                    })
                    // #endif
                    // #ifdef H5
                    if (!this.ServiceConfig.kefu_link) {
                        return _this.$toast({
                            title: '请在后台配置企业微信客服'
                        })
                    }
                    window.open(this.ServiceConfig.kefu_link, '_self')
                    // #endif
                    // #ifdef APP-PLUS
                    plus.share.getServices((res) => {
                        console.log(res)

                        const wechat = res.find((i) => i.id === 'weixin')

                        if (wechat) {
                            wechat.openCustomerServiceChat(
                                {
                                    corpid: this.ServiceConfig.enterprise_id, //第三步获取
                                    url: this.ServiceConfig.kefu_link
                                },
                                (src) => {
                                    console.log('success:')
                                },
                                (err) => {
                                    console.log('error:')
                                }
                            )
                        } else {
                            uni.showToast({
                                title: '当前环境不支持',
                                icon: 'error'
                            })
                        }
                    })
                    // #endif

                    break
                default:
                    break
            }
        }
    }
}

export default ServiceMixin
