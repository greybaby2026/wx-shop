import { baseURL } from '@/config/app.js'
import { ClientEnum } from '@/utils/enum'
import store from '@/store'
import { isObject } from '@vue/shared'

// 获取客户端
export const getClient = () => {
    return handleClientEvent({
        // 微信小程序
        MP_WEIXIN: () => ClientEnum['MP_WEIXIN'],
        // 微信公众号
        OA_WEIXIN: () => ClientEnum['OA_WEIXIN'],
        // H5
        H5: () => ClientEnum['H5'],
        // APP
        IOS: () => ClientEnum['IOS'],
        ANDROID: () => ClientEnum['ANDROID'],
        MP_TOUTIAO: () => ClientEnum['MP_TOUTIAO'],
        // 其它
        OTHER: () => null
    })
}

// 根据端处理事件
export const handleClientEvent = ({
    MP_WEIXIN,
    OA_WEIXIN,
    H5,
    IOS,
    ANDROID,
    MP_TOUTIAO,
    OTHER
}) => {
    const sys = uni.getSystemInfoSync()
    // #ifdef MP-WEIXIN
    return MP_WEIXIN()
    // #endif

    // #ifdef H5
    return isWeixinClient() ? OA_WEIXIN() : H5()
    // #endif

    // #ifdef APP-PLUS
    return sys.platform == 'ios' ? IOS() : ANDROID()
    // #endif
    // #ifdef MP-TOUTIAO
    return MP_TOUTIAO()
    // #endif

    return OTHER()
}

//节流
export const trottle = (func, time = 1000) => {
    let previous = new Date(0).getTime()
    return (...args) => {
        let now = new Date().getTime()
        if (now - previous > time) {
            const result = func(...args)
            previous = now
            return result
        }
    }
}

//防抖
export const debounce = (func, time = 1000) => {
    let timer = null
    return (...args) => {
        if (timer) {
            clearTimeout(timer)
        }
        timer = setTimeout(() => {
            timer = null
            func(...args)
        }, time)
    }
}

//判断是否为微信环境
export function isWeixinClient() {
    var ua = navigator.userAgent.toLowerCase()
    if (ua.match(/MicroMessenger/i) == 'micromessenger') {
        //这是微信环境
        return true
    } else {
        //这是非微信环境
        return false
    }
}

//判断是否为安卓环境
export function isAndroid() {
    let u = navigator.userAgent
    return u.indexOf('Android') > -1 || u.indexOf('Adr') > -1
}

//获取url后的参数  以对象返回
export function strToParams(str) {
    var newparams = {}
    for (let item of str.split('&')) {
        newparams[item.split('=')[0]] = item.split('=')[1]
    }
    return newparams
}

//重写encodeURL函数
export function urlencode(str) {
    str = (str + '').toString()
    return encodeURIComponent(str)
        .replace(/!/g, '%21')
        .replace(/'/g, '%27')
        .replace(/\(/g, '%28')
        .replace(/\)/g, '%29')
        .replace(/\*/g, '%2A')
        .replace(/%20/g, '+')
}

//一维数组截取为二维数组

export function arraySlice(data, array = [], optNum = 10) {
    data = JSON.parse(JSON.stringify(data))
    if (data.length <= optNum) {
        data.length > 0 && array.push(data)
        return array
    }
    array.push(data.splice(0, optNum))
    return arraySlice(data, array, optNum)
}

//对象参数转为以？&拼接的字符
export function paramsToStr(params) {
    let p = ''
    if (typeof params == 'object') {
        p = '?'
        for (let props in params) {
            p += `${props}=${params[props]}&`
        }
        p = p.slice(0, -1)
    }
    return p
}

// 获取wxml元素

export function getRect(selector, all, context) {
    return new Promise(function (resolve) {
        let qurey = uni.createSelectorQuery()

        if (context) {
            qurey = uni.createSelectorQuery().in(context)
        }

        qurey[all ? 'selectAll' : 'select'](selector)
            .boundingClientRect(function (rect) {
                if (all && Array.isArray(rect) && rect.length) {
                    resolve(rect)
                }
                if (!all && rect) {
                    resolve(rect)
                }
            })
            .exec()
    })
}

// 轻提示
export function toast(info = {}, navigateOpt) {
    let title = info.title || ''
    let icon = info.icon || 'none'
    let endtime = info.endtime || 2000
    if (title)
        uni.showToast({
            title: title,
            icon: icon,
            duration: endtime
        })
    if (navigateOpt != undefined) {
        if (typeof navigateOpt == 'object') {
            let tab = navigateOpt.tab || 1,
                url = navigateOpt.url || ''
            switch (tab) {
                case 1:
                    //跳转至 table
                    setTimeout(function () {
                        uni.switchTab({
                            url: url
                        })
                    }, endtime)
                    break
                case 2:
                    //跳转至非table页面
                    setTimeout(function () {
                        uni.navigateTo({
                            url: url
                        })
                    }, endtime)
                    break
                case 3:
                    //返回上页面
                    setTimeout(function () {
                        uni.navigateBack({
                            delta: parseInt(url)
                        })
                    }, endtime)
                    break
                case 4:
                    //关闭当前所有页面跳转至非table页面
                    setTimeout(function () {
                        uni.reLaunch({
                            url: url
                        })
                    }, endtime)
                    break
                case 5:
                    //关闭当前页面跳转至非table页面
                    setTimeout(function () {
                        uni.redirectTo({
                            url: url
                        })
                    }, endtime)
                    break
            }
        } else if (typeof navigateOpt == 'function') {
            setTimeout(function () {
                navigateOpt && navigateOpt()
            }, endtime)
        }
    }
}

//当前页面

export function currentPage() {
    let pages = getCurrentPages()
    let currentPage = pages[pages.length - 1]
    return currentPage || {}
}

//菜单跳转
export function menuJump(item) {
    const { is_tab, link, link_type } = item
    switch (link_type) {
        case 1:
            // 本地跳转
            if (is_tab) {
                uni.switchTab({
                    url: link
                })
                return
            }
            uni.navigateTo({
                url: link
            })
            break

        case 2:
            // webview
            uni.navigateTo({
                url: '/pages/webview/webview?url=' + link
            })
            break

        case 3: // tabbar
    }
}

export function uploadFile(path, options = {}) {
    const { header, name } = options
    return new Promise((resolve, reject) => {
        uni.uploadFile({
            url: baseURL + '/shopapi/Upload/image',
            filePath: path,
            name: name || 'file',
            header: {
                token: store.getters.token,
                version: '1.2.1.20210717',
                ...header
            },
            fileType: 'image',
            cloudPath: '',
            success: (res) => {
                console.log('uploadFile res ==> ', res)
                let data = JSON.parse(res.data)

                if (data.code == 1) {
                    resolve(data.data)
                } else {
                    reject()
                }
            },
            fail: (err) => {
                console.log(err)
                reject()
            }
        })
    })
}

// H5复制方法
export function copy(str) {
    // #ifdef H5
    let aux = document.createElement('input')
    aux.setAttribute('value', str)
    document.body.appendChild(aux)
    aux.select()
    document.execCommand('copy')
    document.body.removeChild(aux)
    uni.showToast({
        title: '复制成功'
    })
    // #endif

    // #ifndef H5
    uni.setClipboardData({
        data: str.toString(),
        success() {
            // #ifdef MP-TOUTIAO
            uni.showToast({
                title: '复制成功'
            })
            // #endif
        },
        fail(err) {
            console.log(err)
        }
    })
    // #endif
}

// 格式化输出价格
export function formatPrice({ price, take = 'all', prec = undefined }) {
    let [integer, decimals = ''] = (price + '').split('.')

    // 小数位补
    if (prec !== undefined) {
        const LEN = decimals.length
        for (let i = prec - LEN; i > 0; --i) decimals += '0'
        decimals = decimals.substr(0, prec)
    }

    switch (take) {
        case 'int':
            return integer
        case 'dec':
            return decimals
        case 'all':
            return integer + '.' + decimals
    }
}

// 将px转为prx并添加单位
export function px2rpx(value) {
    if (!value) return 0
    const rpxValue = value / (uni.upx2px(100) / 100)
    return rpxValue
}

/**
 * @description 图片完整域名
 * @param uri { string } 图片链接
 * @return { string }
 */
export const getImageUri = (uri = '') => {
    const oss_domain = store.state.app.config.oss_domain || ''
    return oss_domain + uri
}

/**
 * @description 获取不重复的id
 * @param length { Number } id的长度
 * @return { String } id
 */
export const getNonDuplicateID = (length = 8) => {
    let idStr = Date.now().toString(36)
    idStr += Math.random().toString(36).substr(3, length)
    return idStr
}

/**
 * @description 获取父组件的参数，因为支付宝/头条小程序不支持provide/inject的写法
 * @param name { String } 组件的name值
 * @return { Object } 父组件实例
 */

export function getParent(name = undefined) {
    let parent = this.$parent
    // 通过while历遍，这里主要是为了H5需要多层解析的问题
    while (parent) {
        // 父组件
        if (parent.$options && parent.$options.name !== name) {
            // 如果组件的name不相等，继续上一级寻找
            parent = parent.$parent
        } else {
            return parent
        }
    }
    return false
}

/**
 * @description 用于热区组件px转rpx
 * @param val 需要转换的px值
 */
export function pxTorpx(val) {
    let result = 0
    uni.getSystemInfo({
        success: function (res) {
            console.log(750 / res.screenWidth)
            let screenWidth = res.screenWidth
            result = val * (750 / screenWidth)
        }
    })
    return result
}

/**
 * @description 获取当前微信小程序基础库版本号
 */
export function getBaseLibraryVersion() {
    const { SDKVersion } = wx.getSystemInfoSync()
    return SDKVersion
}
/**
 * @description 用于比较当前微信基础库版本与目标基础库版本
 * 示例用法：比较小程序基础库版本是否大于等于 2.12.0 compareWeChatVersion("2.12.0")
 * return 1 =>当前版本大于目标版本  return -1 =>当前版本小于目标版本 return 0 =>当前版本等于目标版本
 * @param {string} targetVersion 目标微信版本号
 *
 */
export function compareWeChatVersion(targetVersion) {
    const currentVersion = getBaseLibraryVersion()
    if (currentVersion === targetVersion) {
        return 0 // 当前版本与目标版本相同
    } else if (currentVersion > targetVersion) {
        return 1 // 当前版本大于目标版本
    } else {
        return -1 // 当前版本小于目标版本
    }
}

// 路由跳转
export function navigateTo(link, navigateType = 'navigateTo') {
    // 如果是小程序跳转
    if (link.type === 'mini_program') {
        navigateToMiniProgram(link)
        return
    }
    const url = link?.params ? `${link.path}?${objectToQuery(link?.params)}` : link.path
    ;(navigateType == 'switchTab' || link.canTab) && uni.switchTab({ url })
    navigateType == 'navigateTo' && uni.navigateTo({ url })
    navigateType == 'reLaunch' && uni.reLaunch({ url })
}
/**
 * @description 对象格式化为Query语法
 * @param { Object } params
 * @return {string} Query语法
 */
export function objectToQuery(params) {
    let query = ''
    for (const props of Object.keys(params)) {
        const value = params[props]
        const part = encodeURIComponent(props) + '='
        if (!isEmpty(value)) {
            console.log(encodeURIComponent(props), isObject(value))
            if (isObject(value)) {
                for (const key of Object.keys(value)) {
                    if (!isEmpty(value[key])) {
                        const params = props + '[' + key + ']'
                        const subPart = encodeURIComponent(params) + '='
                        query += subPart + encodeURIComponent(value[key]) + '&'
                    }
                }
            } else {
                query += part + encodeURIComponent(value) + '&'
            }
        }
    }
    return query.slice(0, -1)
}
/**
 * @description 是否为空
 * @param {unknown} value
 * @return {Boolean}
 */
export const isEmpty = (value) => {
    return value == null && typeof value == 'undefined'
}
/**
 * @description 小程序跳转
 * @param link 跳转信息，由装修数据进行输入
 */
export function navigateToMiniProgram(link) {
    const query = link.params
    // #ifdef H5

    window.open(
        `weixin://dl/business/?appid=${query?.appId}&path=${query?.path}&env_version=${
            query?.envVersion
        }&query=${encodeURIComponent(query?.query)}`
    )
    // #endif
    // #ifdef MP
    uni.navigateToMiniProgram({
        appId: query?.appId,
        path: query?.path,
        extraData: query?.query,
        envVersion: query?.envVersion
    })
    // #endif
    // #ifdef APP-PLUS
    let envType = 0
    switch (query?.envVersion) {
        case 'release':
            envType = 0
            break
        case 'develop':
            envType = 1
            break
        case 'trial':
            envType = 2
            break
    }
    plus.share.getServices(
        function (res) {
            var sweixin = null
            for (var i = 0; i < res.length; i++) {
                var t = res[i]
                if (t.id == 'weixin') {
                    sweixin = t
                }
            }
            if (sweixin) {
                sweixin.launchMiniProgram({
                    id: query?.originalId, // 要跳转小程序的原始ID
                    path: query?.path,
                    type: envType
                })
            }
        },
        function (res) {
            console.log(JSON.stringify(res))
        }
    )

    // #endif
}
/**
 * 数组扁平化
 * @param {*} arr 数组
 * @param {*} key 子项
 * @param {8} time 扁平层级
 */
export function flattenArray(arr, key) {
    let result = []
    arr.forEach((item) => {
        if (Array.isArray(item[key])) {
            result.push(item)
            result = result.concat(flattenArray(item[key], key)) // 递归处理子数组
        } else {
            result.push(item) // 非数组直接加入结果
        }
    })

    return result
}

// 获取底部安全区
export function getSafeBottom() {
    const { screenHeight, safeArea } = uni.getSystemInfoSync()
    return screenHeight - safeArea.bottom
}

/**
 * 通用轮询函数
 * @param {Function} taskFn - 要执行的异步任务函数（必须返回 Promise）
 * @param {Function} checkFn - 检查是否结束轮询的函数（接收 taskFn 的结果作为参数）
 * @param {Number} interval - 每次轮询的时间间隔（毫秒）
 * @param {Number} maxAttempts - 最大轮询次数
 * @returns {Promise} - 任务成功 resolve(result)，失败 reject(error)
 */
export function createPoll(taskFn, checkFn, interval = 1000, maxAttempts = 10) {
    let attempts = 0
    let timer = null
    let stopped = false

    const stop = () => {
        stopped = true
        if (timer) clearTimeout(timer)
    }

    const start = () =>
        new Promise((resolve, reject) => {
            const execute = async () => {
                if (stopped) {
                    return reject(new Error('轮询已被手动停止'))
                }

                try {
                    const result = await taskFn()
                    if (checkFn(result)) {
                        resolve(result)
                    } else {
                        attempts++
                        if (attempts < maxAttempts) {
                            timer = setTimeout(execute, interval)
                        } else {
                            reject(new Error('轮询超时或未满足条件'))
                        }
                    }
                } catch (error) {
                    reject(error)
                }
            }

            execute()
        })

    return { start, stop }
}
