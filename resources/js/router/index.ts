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
import "nprogress/nprogress.css";



declare module "vue-router" {
  interface RouteMeta {
    pageTitle?: string;
    permission?: string;
    middleware?: "auth" | "guest";
    role?: string[];
  }
}
// declare module "vue-router" {
//     interface RouteMeta {
//         pageTitle?: string;
//         permission?: string;
//     }
// }

const routes: Array<RouteRecordRaw> = [

  // {
  //   path: "/",
  //   component: () => import("@/pages/dashboard/app.vue"),
  //   // meta: {
  //   //     requiresAuth: true,
  //   // },
  //   children: [
  //     //  {
  //     //     path: "/dashboard",
  //     //     name: "dashboard",
  //     //     component: () => import("@/pages/dashboard/beranda.vue"),
  //     // },
  //     {
  //       path: "/beranda",
  //       name: "beranda",
  //       component: () => import("@/pages/dashboard/beranda.vue"),
  //       meta: {
  //         requiresAuth: true,
  //           // pageTitle: "Beranda",
  //           // breadcrumbs: ["Beranda"],
  //       },
  //     },
  //   ],
  // },


  {
    path: '/DetailLaundry/:id',
    name: 'DetailLaundry',
    component: () => import('@/pages/dashboard/DetailLaundry.vue'),

  },
  {
    path: '/RiwayatTransaksi',
    name: 'RiwayatTransaksi',
    component: () => import('@/pages/dashboard/RiwayatTransaksi.vue'),

  },
  {
    path: '/pelanggan/profil-pelanggan',
    name: 'pelanggan.profil-pelanggan',
    component: () => import('@/pages/pelanggan/profil-pelanggan.vue'),

  },

  {
    path: "/beranda",
    name: "beranda",
    component: () => import("@/pages/dashboard/beranda.vue"),
    meta: {
      requiresAuth: true,
      // pageTitle: "Beranda",
      // breadcrumbs: ["Beranda"],
    },
  },
  {
    path: "/menunggu-verifikasi",
    name: "menunggu-verifikasi",
    component: () => import("@/pages/dashboard/menunggu-verifikasi.vue"),
    meta: { pageTitle: "Sign Up", middleware: "guest" },
    // meta: {
    //     pageTitle: "Beranda",
    //     breadcrumbs: ["Beranda"],
    // },
  },
  {
    path: "/pelanggan",
    name: "pelanggan",
    component: () => import("@/pages/pelanggan/index.vue"),

  },
  {
    path: "/pelanggan/riwayat",
    name: "riwayat",
    component: () => import("@/pages/pelanggan/riwayat.vue"),

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
        path: "/mitra",
        name: "mitra",
        component: () =>
          import("@/pages/mitra/index.vue"),
      },
      {
        path: "/mitra/profil",
        name: "profil",
        component: () =>
          import("@/pages/mitra/profil.vue"),
      },
      {
        path: "/mitra/transaksi",
        name: "transaksi",
        component: () =>
          import("@/pages/mitra/transaksi/index.vue"),
      },
      {
        path: "/mitra/layanan",
        name: "layanan",
        component: () =>
          import("@/pages/mitra/layanan/index.vue"),
      },
      {
        path: "/dashboard/laundrydetail",
        name: "dashboard.laundrydetail",
        component: () =>
          import("@/pages/dashboard/LaundryDetail.vue"),
      },
      {
        path: "/kecamatan",
        name: "kecamatan",
        component: () =>
          import("@/pages/kecamatan/index.vue"),
      },
      {
        path: "/data-order",
        name: "data-order",
        component: () =>
          import("@/pages/data-order/index.vue"),
      },

    ],
  },

  {
    path: "/",
    component: () => import("@/layouts/default-layout/DefaultLayout.vue"),
    meta: {
      middleware: "auth",
    },
    children: [
      {
        path: "/dashboard-mitra",
        name: "dashboard-mitra",
        component: () => import("@/pages/dashboard-mitra/index.vue"),
      },

      // layanan

      {
        path: "/mitra/profil",
        name: "profil",
        component: () =>
          import("@/pages/mitra/profil.vue"),
      },
      {
        path: "/mitra/transaksi",
        name: "transaksi",
        component: () =>
          import("@/pages/mitra/transaksi/index.vue"),
      },
      {
        path: "/mitra/layanan",
        name: "layanan",
        component: () =>
          import("@/pages/mitra/layanan/index.vue"),
      },
      {
        path: "/mitra/laporan-laundry",
        name: "laporan-laundry",
        component: () =>
          import("@/pages/mitra/laporan-laundry.vue"),
      },



      {
        path: "/dashboard-mitra/order-masuk",
        name: "order-masuk",
        component: () =>
          import("@/pages/dashboard-mitra/order-masuk.vue"),
      },

      {
        path: "/dashboard-mitra/order-proses",
        name: "order-proses",
        component: () =>
          import("@/pages/dashboard-mitra/order-proses.vue"),
      },

      {
        path: "/dashboard-mitra/order-siap-ambil",
        name: "order-siap-ambil",
        component: () =>
          import("@/pages/dashboard-mitra/order-siap-ambil.vue"),
      },
      {
        path: "/dashboard-mitra/order-selesai",
        name: "order-selesai",
        component: () =>
          import("@/pages/dashboard-mitra/order-selesai.vue"),
      },




      {
        path: "/dashboard/laundrydetail",
        name: "dashboard.laundrydetail",
        component: () =>
          import("@/pages/dashboard/LaundryDetail.vue"),
      },

    ],
  },

























  {
    path: '/register-mitra',
    name: 'RegisterMitra',
    component: () => import('@/pages/dashboard/register-mitra.vue'),
    meta: { guest: true },
  },
  {
    path: "/",
    component: () => import("@/layouts/AuthLayout.vue"),
    children: [

      {
        path: "/sign-in",
        name: "sign-in",
        component: () => import("@/pages/auth/sign-in/Index.vue"),
        meta: { pageTitle: "Sign In", middleware: "guest" },
      },
      {
        path: "sign-up",
        name: "sign-up",
        component: () => import("@/pages/auth/sign-up/Index.vue"),
        meta: { pageTitle: "Sign Up", middleware: "guest" },
      },
      {
        path: "/sign-in/lupa-password",
        name: "sign-in/lupa-password",
        component: () => import("@/pages/auth/sign-in/lupa-password.vue"),
        meta: { pageTitle: "Forgot Password", middleware: "guest" },
      },
      {
        path: "/sign-in/reset-password",
        name: "sign-in/reset-password",
        component: () => import("@/pages/auth/sign-in/reset-password.vue"),
        meta: { pageTitle: "Reset Password", middleware: "guest" },
      },

      //               {
      //     path: "/pelanggan",
      //     component: DashboardLayout,
      //     meta: { middleware: "auth", role: ["pelanggan"] },
      //     children: [
      //       { path: "", name: "pelanggan", component: PelangganIndex },
      //       { path: "antar", name: "pelanggan.antar", component: PelangganAntar },
      //       { path: "jemput", name: "pelanggan.jemput", component: PelangganJemput },
      //       { path: "riwayat", name: "pelanggan.riwayat", component: PelangganRiwayat },
      //     ],
      //   },

      // 🟣 ADMIN
      //   {
      //     path: "/admin",
      //     component: () => import("@/layouts/default-layout/DefaultLayout.vue"),
      //     meta: { middleware: "auth", role: ["admin", "pemilik"] },
      //     children: [
      //       {
      //         path: "",
      //         name: "admin.dashboard",
      //         component: () => import("@/pages/dashboard/Index.vue"),
      //       },
      //       {
      //         path: "pendapatan",
      //         name: "admin.pendapatan",
      //         component: () => import("@/pages/admin/pendapatan.vue"),
      //       },
      //       {
      //         path: "antar-jemput",
      //         name: "admin.antarjemput",
      //         component: () => import("@/pages/admin/antarjemput.vue"),
      //       },
      //       {
      //         path: "tambah-pelanggan",
      //         name: "admin.tambahpelanggan",
      //         component: () => import("@/pages/admin/tambahpelanggan.vue"),
      //       },
      //       // kamu bisa tambahkan route master / layanan / transaksi di sini
      //     ],
      //   },
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



// -------------------------------
// Router Setup
// -------------------------------
const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to) {
    if (to.hash) {
      return { el: to.hash, top: 80, behavior: "smooth" };
    }
    return { top: 0, left: 0, behavior: "smooth" };
  },
});

// -------------------------------
// Middleware & Guards
// -------------------------------
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

  // before page access check if page requires authentication
  if (to.meta.middleware == "auth") {
    if (authStore.isAuthenticated) {
      if (
        to.meta.permission &&
        !authStore.user.permission.includes(to.meta.permission)
      ) {
      } else if (to.name === "dashboard" && authStore.user.role?.name === "pelanggan") {
        next({ name: "beranda" });
      } else if (to.name === "dashboard" && authStore.user.role?.name === "mitra") {
        next({ name: "dashboard-mitra" });
      } else if (to.meta.checkDetail == false) {
        next();
      }
      next();
    } else {
      next({ name: "beranda" });
    }
  } else if (to.meta.middleware == "guest" && authStore.isAuthenticated) {
    console.log('auth: ', authStore.isAuthenticated)
    next({ name: "beranda" });
  } else {
    next();
  }
});

router.afterEach(() => {
  NProgress.done();
});

export default router;
