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
            {
                heading: "Data Pelanggan",
                route: "/dashboard/datapelanggan",
                name: "datapelanggan",
                keenthemesIcon: "setting-2",
            },
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
                heading: "Transaksi Layanan",
                route: "/admin/transaksilayanan",
                name: "transaksilayanan",
                keenthemesIcon: "bi bi-truck",
            },
            {
                heading: "Data Pelanggan",
                route: "/admin/tambahpelanggan",
                name: "tambahpelanggan",
                keenthemesIcon: "bi bi-truck",
            },
            
        ],
        
    },
    {
        heading: "Layanan",
        route: "/layanan",
        name: "layanan",
        pages: [
            
            {
                heading: "Jenis Item",
                route: "/layanan/jenisitem",
                name: "jenisitem",
                keenthemesIcon: "bi bi-truck",
            },
            {
                heading: "Jenis Layanan",
                route: "/layanan/jenislayanan",
                name: "jenislayanan",
                keenthemesIcon: "bi bi-truck",
            },
            {
                heading: "Harga Jenis Layanan",
                route: "/layanan/hargajenislayanan",
                name: "hargajenislayanan",
                keenthemesIcon: "bi bi-truck",
            },
            {
                heading: "Layanan Prioritas",
                route: "/layanan/layananprioritas",
                name: "layananprioritas",
                keenthemesIcon: "bi bi-truck",
            },
            
        ],
        
    },
    
];

export default MainMenuConfig;
