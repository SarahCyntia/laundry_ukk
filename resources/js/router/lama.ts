import {
    createRouter,
    createWebHistory,
    type RouteRecordRaw,
} from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useConfigStore } from "@/stores/config";
import NProgress from "nprogress";
import "nprogress/nprogress.css";
import DashboardLayout from "@/pages/dashboard/app.vue";


import PelangganIndex from '@/pages/pelanggan/index.vue'
import PelangganAntar from '@/pages/pelanggan/antar.vue'
import PelangganJemput from '@/pages/pelanggan/jemput.vue'
import PelangganRiwayat from '@/pages/pelanggan/riwayat.vue'



declare module "vue-router" {
    interface RouteMeta {
        pageTitle?: string;
        permission?: string;
    }
}

const routes: Array<RouteRecordRaw> = [
    
{
        path: "/",
        component : () => import("@/pages/dashboard/app.vue"),
        // meta: {
        //     requiresAuth: true,
        // },
        children: [
            //  {
            //     path: "/dashboard",
            //     name: "dashboard",
            //     component: () => import("@/pages/dashboard/beranda.vue"),
            // },
            {
                path: "/beranda",
                name: "beranda",
                component: () => import("@/pages/dashboard/beranda.vue"),
                // meta: {
                //     pageTitle: "Beranda",
                //     breadcrumbs: ["Beranda"],
                // },
            },
        ],
    },
{
        path: "/pelanggan",
        name: "pelanggan",
        component : () => import("@/pages/pelanggan/index.vue"),
    
    },
{
        path: "/pelanggan/riwayat",
        name: "riwayat",
        component : () => import("@/pages/pelanggan/riwayat.vue"),
    
    },
{
        path: "/pelanggan/antar",
        name: "antar",
        component : () => import("@/pages/pelanggan/antar.vue"),
    
    },
{
        path: "/pelanggan/jemput",
        name: "jemput",
        component : () => import("@/pages/pelanggan/jemput.vue"),
    
    },


    {
        path: "/",
        redirect: "/dashboard",
        component: () => import("@/layouts/default-layout/DefaultLayout.vue"),
        meta: {
            middleware: "auth",
        },
        children: [
            {
                path: "/dashboard",
                name: "dashboard",
                component: () => import("@/pages/dashboard/Index.vue"),
                // meta: {
                //     pageTitle: "Dashboard",
                //     breadcrumbs: ["Dashboard"],
                // },
            },
            {
                path: "/dashboard/profile",
                name: "dashboard.profile",
                component: () => import("@/pages/dashboard/profile/Index.vue"),
                meta: {
                    pageTitle: "Profile",
                    breadcrumbs: ["Profile"],
                },
            },
            {
                path: "/dashboard/setting",
                name: "dashboard.setting",
                component: () => import("@/pages/dashboard/setting/Index.vue"),
                meta: {
                    pageTitle: "Website Setting",
                    breadcrumbs: ["Website", "Setting"],
                },
            },
            {
                path: "/admin",
                name: "antar-jemput",
                component: () => import("@/pages/admin/antarjemput.vue"),
            },
            {
                path: "/admin/pendapatan",
                name: "pendapatan",
                component: () => import("@/pages/admin/pendapatan.vue"),
            },
            // {
            //     path: "/admin/transaksilayanan",
            //     name: "transaksilayanan",
            //     component: () => import("@/pages/admin/transaksilayananlama.vue"),
            // },
            {
                path: "/admin/tambah-pelanggan",
                name: "tambahpelanggan",
                component: () => import("@/pages/admin/tambahpelanggan.vue"),
            },
            {
                path: "/dashboard/data-pelanggan",
                name: "datapelanggan",
                component: () => import("@/pages/dashboard/datapelanggan/index.vue"),
            },

            // MASTER
            {
                path: "/dashboard/master/users/roles",
                name: "dashboard.master.users.roles",
                component: () =>
                    import("@/pages/dashboard/master/users/roles/Index.vue"),
                meta: {
                    pageTitle: "User Roles",
                    breadcrumbs: ["Master", "Users", "Roles"],
                },
            },
            {
                path: "/dashboard/master/users",
                name: "dashboard.master.users",
                component: () =>
                    import("@/pages/dashboard/master/users/Index.vue"),
                meta: {
                    pageTitle: "Users",
                    breadcrumbs: ["Master", "Users"],
                },
            },
            // layanan
            {
                path: "/layanan/jenis-item",
                name: "layanan.jenisitem",
                component: () =>
                    import("@/pages/layanan/jenisitem.vue"),
            },
            {
                path: "/mitra/mitra",
                name: "mitra.mitra",
                component: () =>
                    import("@/pages/mitra/index.vue"),
            },
            {
                path: "/layanan/jenis-layanan",
                name: "layanan.jenislayanan",
                component: () =>
                    import("@/pages/layanan/jenislayanan.vue"),
            },
            {
                path: "/layanan/harga-jenis-layanan",
                name: "layanan.hargajenislayanan",
                component: () =>
                    import("@/pages/layanan/hargajenislayanan.vue"),
            },
            {
                path: "/layanan/layanan-prioritas",
                name: "layanan.layananprioritas",
                component: () =>
                    import("@/pages/layanan/layananprioritas.vue"),
            },
            {
                path: "/layanan/layanan-tambahan",
                name: "layanan.layanantambahan",
                component: () =>
                    import("@/pages/layanan/layanantambahan.vue"),
            },
             // transaksi
            {
                path: "/transaksi/transaksilayanan",
                name: "transaksi.transaksilayanan",
                component: () =>
                    import("@/pages/transaksi/transaksilayanan.vue"),
            },
           
        ],
    },
    {
        path: "/",
        component: () => import("@/layouts/AuthLayout.vue"),
        children: [
            {
                path: "/sign-in",
                name: "sign-in",
                component: () => import("@/pages/auth/sign-in/Index.vue"),
                meta: {
                    pageTitle: "Sign In",
                    middleware: "guest",
                },
            },
            {
                path: "/sign-up",
                name: "sign-up",
                component: () => import("@/pages/auth/sign-up/Index.vue"),
                meta: {
                    pageTitle: "Sign up",
                    middleware: "guest",
                },
            },
            {
                path: "/login-pemilik",
                name: "login-pemilik",
                component: () => import("@/pages/auth/login-pemilik/index.vue"),
            
            },
            {
                path: "/daftar-pemilik",
                name: "daftar-pemilik",
                component: () => import("@/pages/auth/daftar-pemilik/index.vue"),
            
            },
        ],
    },
    {
        path: "/",
        component: () => import("@/layouts/SystemLayout.vue"),
        children: [
            {
                // the 404 route, when none of the above matches
                path: "/404",
                name: "404",
                component: () => import("@/pages/errors/Error404.vue"),
                meta: {
                    pageTitle: "Error 404",
                },
            },
            {
                path: "/500",
                name: "500",
                component: () => import("@/pages/errors/Error500.vue"),
                meta: {
                    pageTitle: "Error 500",
                },
            },
        ],
    },
    {
        path: "/:pathMatch(.*)*",
        redirect: "/404",
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to) {
        // If the route has a hash, scroll to the section with the specified ID; otherwise, scroll to the top of the page.
        if (to.hash) {
            return {
                el: ato.hash,
                top: 80,
                behavior: "smooth",
            };
        } else {
            return {
                top: 0,
                left: 0,
                behavior: "smooth",
            };
        }
    },
});

router.beforeEach(async (to, from, next) => {
    if (to.name) {
        // Start the route progress bar.
        NProgress.start();
    }

    const authStore = useAuthStore();
    const configStore = useConfigStore();

    // current page view title
    if (to.meta.pageTitle) {
        document.title = `${to.meta.pageTitle} - ${import.meta.env.VITE_APP_NAME
            }`;
    } else {
        document.title = import.meta.env.VITE_APP_NAME as string;
    }

    // reset config to initial state
    configStore.resetLayoutConfig();

    // verify auth token before each page change
    if (!authStore.isAuthenticated) await authStore.verifyAuth();

    if (to.name == "dashboard" && authStore.user.role?.name != "admin"){
        next({ name: "beranda" });
    }
    // if (to.name == "dashboard" && authStore.user.role?.name != "mitra"){
    //     next({ name: "beranda" });
    // }

    // before page access check if page requires authentication
     if (to.meta.middleware == "auth") {
        if (authStore.isAuthenticated) {
            if (
                to.meta.permission &&
                !authStore.user.permission.includes(to.meta.permission)
            ) {
                next({ name: "404" });
            } else if (to.name === "dashboard" && authStore.user.role?.name === "pelanggan") {
                next({ name: "beranda" });
            } else if (to.meta.checkDetail == false) {
                next();
            }

            next();
        } else {
            next({ name: "sign-in" });
        }
    } else if (to.meta.middleware == "guest" && authStore.isAuthenticated) {
        next({ name: "dashboard" });
    } else {
        next();
    }
    // if (to.meta.middleware == "auth") {
    //     if (authStore.isAuthenticated) {
    //         if (
    //             to.meta.permission &&
    //             !authStore.user.permission.includes(to.meta.permission)
    //         ) {
    //             next({ name: "404" });
    //         } else if (to.meta.checkDetail == false) {
    //             next();
    //         }

    //         next();
    //     } else {
    //         next({ name: "sign-in" });
    //     }
    // } else if (to.meta.middleware == "guest" && authStore.isAuthenticated) {
    //     next({ name: "dashboard" });
    // } else {
    //     next();
    // }
});

router.afterEach(() => {
    // Complete the animation of the route progress bar.
    NProgress.done();
});

export default router;
