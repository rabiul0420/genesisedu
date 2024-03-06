export default [
    {
        path: '',
        component: () => import("../pages/public/home"),
        // meta: {
        //     title: 'Welcome to  ${name}'
        // }
    },
    {
        path: 'about',
        component: () => import("../pages/public/about"),
    },
    // {
    //     path: '/course',
    //     component: () => import("../pages/public/course"),
    // },
    {
        path: 'contact',
        component: () => import("../pages/public/contact"),
    },
    // {
    //     path: '/login',
    //     component: () => import("../pages/public/login"),
    // },
    // {
    //     path: '/register',
    //     component: () => import("../pages/public/register"),
    // },
]