import Main from '@/layout/main.vue'

const routes = [
    {
        path: '/user',
        name: 'user',
        meta: { title: '用户' },
        redirect: '/user/profile',
        component: Main,
        children: [
            {
                path: '/user/profile',
                name: 'user_profile',
                meta: {
                    title: '用户概况',
                    parentPath: '/user',
                    icon: 'icon_user',
                    permission: ['view']
                },
                component: () => import('@/views/user/profile.vue')
            },
            {
                path: '/user/lists',
                name: 'user_list',
                meta: {
                    title: '用户管理',
                    parentPath: '/user',
                    icon: 'icon_user_guanli',
                    permission: ['view'],
                    keepAlive: true
                },
                component: () => import('@/views/user/lists.vue')
            },
            {
                path: '/user/user_details',
                name: 'user_details',
                meta: {
                    title: '用户详情',
                    parentPath: '/user',
                    hidden: true,
                    prevPath: '/user/lists'
                },
                component: () => import('@/views/user/user_details.vue')
            },
            {
                path: '/user/grade',
                name: 'user_grade',
                meta: {
                    title: '用户等级',
                    parentPath: '/user',
                    icon: 'icon_user_dengji',
                    permission: ['view'],
                    keepAlive: true
                },
                component: () => import('@/views/user/grade.vue')
            },
            {
                path: '/user/grade_edit',
                name: 'user_grade_edit',
                meta: {
                    hidden: true,
                    title: '编辑等级',
                    parentPath: '/user',
                    prevPath: '/user/grade'
                },
                component: () => import('@/views/user/grade_edit.vue')
            },
            {
                path: '/user/tag',
                name: 'user_tag',
                meta: {
                    title: '用户标签',
                    parentPath: '/user',
                    icon: 'icon_user_biaoqian',
                    permission: ['view'],
                    keepAlive: true
                },
                component: () => import('@/views/user/tag.vue')
            },
            {
                path: '/user/tag_edit',
                name: 'user_tag_edit',
                meta: {
                    hidden: true,
                    title: '编辑标签',
                    parentPath: '/user',
                    prevPath: '/user/tag'
                },
                component: () => import('@/views/user/tag_edit.vue')
            },
            {
                path: '/user/invitation_list',
                name: 'user_invitation_list',
                meta: {
                    hidden: true,
                    title: '邀请列表',
                    parentPath: '/user',
                    prevPath: '/user/invitation_list'
                },
                component: () => import('@/views/user/invitation_list.vue')
            }
        ]
    }
]

export default routes
