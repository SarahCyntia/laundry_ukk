import type { MenuItem } from "@/layouts/default-layout/config/types";

const MainMenuConfig: Array<MenuItem> = [

    {
        pages: [
            {
                heading: "Beranda",
                name: "beranda",
                route: "/beranda",
                keenthemesIcon: "element-11",
            },
        ],
    },

    {
        pages: [
            {
                heading: "Dashboard",
                name: "dashboard",
                route: "/dashboard",
                keenthemesIcon: "element-11",
            },
        ],
    },
    {
        pages: [
            {
                heading: "Dashboard Mitra",
                name: "dashboard-mitra",
                route: "/dashboard-mitra",
                keenthemesIcon: "element-11",
            },
        ],
    },
    {
        pages: [
            {
                heading: "Pelanggan",
                name: "pelanggan",
                route: "/pelanggan",
                keenthemesIcon: "element-11",
            },
        ],
    },
    {
        pages: [
            {
                heading: "Riwayat",
                name: "riwayat",
                route: "/pelanggan/riwayat",
                keenthemesIcon: "element-11",
            },
        ],
    },
    {
        pages: [
            {
                heading: "Antar",
                name: "antar",
                route: "/pelanggan/antar",
                keenthemesIcon: "element-11",
            },
        ],
    },
    {
        pages: [
            {
                heading: "menunggu-verifikasi",
                name: "menunggu-verifikasi",
                route: "/menunggu-verifikasi",
                keenthemesIcon: "element-11",
            },
        ],
    },
    {
        pages: [
            {
                heading: "Jemput",
                name: "jemput",
                route: "/pelanggan/jemput",
                keenthemesIcon: "element-11",
            },
        ],
    },

    // WEBSITE
    {
        heading: "Website",
        route: "/dashboard/website",
        name: "website",
        pages: [
            // MASTER
            {
                sectionTitle: "Master",
                route: "/master",
                keenthemesIcon: "cube-3",
                name: "master",
                sub: [
                    {
                        sectionTitle: "User",
                        route: "/users",
                        name: "master-user",
                        sub: [
                            {
                                heading: "Role",
                                name: "master-role",
                                route: "/dashboard/master/users/roles",
                            },
                            {
                                heading: "User",
                                name: "master-user",
                                route: "/dashboard/master/users",
                            },
                        ],
                    },
                ],
            },
            {
                heading: "Setting",
                route: "/dashboard/setting",
                name: "setting",
                keenthemesIcon: "setting-2",
            },
           
          
           
            // {
            //     heading: "Data Pelanggan",
            //     route: "/dashboard/data-pelanggan",
            //     name: "datapelanggan",
            //     keenthemesIcon: "setting-2",
            // },
            {
                heading: "Antar-jemput",
                route: "/admin",
                name: "antar-jemput",
                keenthemesIcon: "bi bi-truck",
            },
            {
                heading: "Pendapatan Laundry",
                route: "/admin/pendapatan",
                name: "pendapatan",
                keenthemesIcon: "bi bi-truck",
            },
            {
                heading: "Laundry Detail",
                route: "/dashboard/laundrydetail",
                name: "laundrydetail",
                keenthemesIcon: "bi bi-truck",
            },

            {
                heading: "Kecamatan",
                route: "/kecamatan",
                name: "kecamatan",
                keenthemesIcon: "bi bi-bank",
            },
             {
               heading: "Data Mitra",
               route: "/mitra",
               name: "mitra",
               keenthemesIcon: "bi bi-person-lines-fill",
           },
            {
                heading: "Data Pelanggan",
                route: "/dashboard/data-pelanggan",
                name: "datapelanggan",
                keenthemesIcon: "bi bi-person-lines-fill",
            },
            {
                heading: "Data Order",
                route: "/data-order",
                name: "data-order",
                keenthemesIcon: "bi bi-clipboard2-data",
            },
            
            
        ],
        
    },
    {
        heading: "Mitra",
        route: "/transaksi",
        name: "transaksi",
        pages: [
            
            {
               heading: "Profil",
               route: "/mitra/profil",
               name: "profil",
               keenthemesIcon: "bi bi-person-fill",
           },
            {
                heading: "Transaksi",
                route: "/mitra/transaksi",
                name: "transaksi",
                keenthemesIcon: "bi bi-truck",
            },
            {
                heading: "Layanan",
                route: "/mitra/layanan",
                name: "layanan",
                keenthemesIcon: "bi bi-list-ul",
            },
             
            {
                heading: "Laporan Laundry",
                route: "/mitra/laporan-laundry",
                name: "laporan-laundry",
                keenthemesIcon: "bi bi-receipt",
            },
            {
                heading: "Laporan Keuangan",
                route: "/mitra/laporan-keuangan",
                name: "laporan-keuangan",
                keenthemesIcon: "bi bi-currency-exchange",
            },
             
        ],
        
    },
    {
        heading: "Dashboard Mitra",
        route: "/dashboard-mitra",
        name: "dashboard-mitra",
        pages: [
            
            {
               heading: "Order Masuk",
               route: "/dashboard-mitra/order-masuk",
               name: "order-masuk",
               keenthemesIcon: "bi bi-journal-arrow-down",
           },
            {
                heading: "Order Proses",
                route: "/dashboard-mitra/order-proses",
                name: "order-proses",
                keenthemesIcon: "bi bi-patch-exclamation",
            },
            {
                heading: "Order Siap Diambil",
                route: "/dashboard-mitra/order-siap-ambil",
                name: "order-siap-ambil",
                keenthemesIcon: "bi bi-box-seam",
            },
            {
                heading: "Order Selesai",
                route: "/dashboard-mitra/order-selesai",
                name: "order-selesai",
                keenthemesIcon: "bi bi-journal-check",
            },
             
        ],
        
    },
    
];

export default MainMenuConfig;
