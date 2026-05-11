import router from './router'
import store from './store'
import NProgress from 'nprogress'


const loginPath = '/login'
const defaultPath = '/'
const whiteList = ['login']
NProgress.configure({ showSpinner: false }) 

router.beforeEach((to, from, next) => {
    NProgress.start()
    // 设置文档title
    to.meta?.title && (document.title = to.meta.title)
    let { token } = to.query
    if(token) {
        store.commit('SET_TOKEN', token)
    }
    token = token || store.getters.token;
    if (token) {
        if (to.path === loginPath) {
            next({ path: defaultPath })
        } else {
            next();
        }
    } else {
        if (whiteList.includes(to.name)) {
            // 在免登录白名单，直接进入
            next()
        } else {
            next({ path: loginPath, query: { redirect: to.fullPath } })
        }
    }

})


router.afterEach(() => {
    NProgress.done()
})
