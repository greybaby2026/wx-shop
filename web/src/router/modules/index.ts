import Main from '@/layout/main.vue'

const routes = [
    {
        path: '/',
        name: 'index',
        meta: { title: '首页' },
        redirect: '/index',
        component: Main,
        children: [
            {
                path: '/index',
                name: 'index',
                meta: {
                    hidden: true,
                    title: '首页',
                    parentPath: '/',
                    permission: ['view']
                },
                component: () => import('@/views/index/home.vue')
            }
        ]
    }
]

export default routes
