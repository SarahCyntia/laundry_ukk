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


import PelangganIndex from '@/pages/pelanggan/index.vue'
import PelangganAntar from '@/pages/pelanggan/antar.vue'
import PelangganJemput from '@/pages/pelanggan/jemput.vue'
import PelangganRiwayat from '@/pages/pelanggan/riwayat.vue'



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

  {
    path: "/",
    component: () => import("@/pages/dashboard/app.vue"),
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
    path: "/pelanggan/antar",
    name: "antar",
    component: () => import("@/pages/pelanggan/antar.vue"),

  },
  {
    path: "/pelanggan/jemput",
    name: "jemput",
    component: () => import("@/pages/pelanggan/jemput.vue"),

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
        path: "sign-in",
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
  if (to.name) NProgress.start();

  const authStore = useAuthStore();
  const configStore = useConfigStore();

  // Update title
  document.title = to.meta.pageTitle
    ? `${to.meta.pageTitle} - ${import.meta.env.VITE_APP_NAME}`
    : import.meta.env.VITE_APP_NAME;

  // Reset layout config
  configStore.resetLayoutConfig();

  // Cek auth
  if (!authStore.isAuthenticated) await authStore.verifyAuth();

  // 🔒 Middleware: Auth
  if (to.meta.middleware === "auth") {
    if (!authStore.isAuthenticated) {
      next({ name: "sign-in" });
      return;
    }

    // 🔹 Cek role access
    if (to.meta.role && !to.meta.role.includes(authStore.user.role?.name)) {
      // misal pelanggan coba akses halaman admin
      if (authStore.user.role?.name === "pelanggan") {
        next({ name: "pelanggan" });
      } else {
        next({ name: "admin.dashboard" });
      }
      return;
    }
  }

  // 🚫 Middleware: Guest
  if (to.meta.middleware === "guest" && authStore.isAuthenticated) {
    if (authStore.user.role?.name === "pelanggan") {
      next({ name: "pelanggan" });
    } else {
      next({ name: "admin.dashboard" });
    }
    return;
  }

  next();
});

router.afterEach(() => {
  NProgress.done();
});

export default router;