
const fs = require("fs");
let code = fs.readFileSync("public/adminator_templete/2026.js", "utf8");
let start = code.indexOf("const n=[");
let end = code.indexOf(",M={Day");

if(start > -1 && end > -1) {
    let newSidebar = `const adminMenu=[
        {
            label: "Menu Utama",
            items: [
                {key: "dashboard", text: "Dashboard", href: "/dashboard", icon: "<path d=\"M3 12 12 3l9 9\"/><path d=\"M5 10v10h14V10\"/>"}
            ]
        },
        {
            label: "Modul ATK",
            items: [
                {key: "atk", text: "Master ATK", href: "/atk", icon: "<rect x=\"3\" y=\"4\" width=\"18\" height=\"16\" rx=\"2\"/>"},
                {key: "permintaan-atk", text: "Permintaan / PO ATK", href: "/permintaan-atk", icon: "<path d=\"M12 2 15 8l6.5 1-4.8 4.6L18 20l-6-3-6 3 1.3-6.4L2.5 9 9 8z\"/>"},
                {key: "stok-atk", text: "Stok ATK", href: "/stok-atk", icon: "<path d=\"M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4\"/><polyline points=\"7 10 12 15 17 10\"/><line x1=\"12\" y1=\"15\" x2=\"12\" y2=\"3\"/>"},
                {key: "pemakaian-atk", text: "Pemakaian ATK", href: "/pemakaian-atk", icon: "<circle cx=\"12\" cy=\"12\" r=\"9\"/>"},
                {key: "scan", text: "QR Scanner", href: "/atk/scan", icon: "<path d=\"M3 4a1 1 0 011-1h3v2H5v3H3V4zm2 14v-3H3v4a1 1 0 001 1h3v-2H5zm14-14h-3V2h4v5h-2V4zm-3 14h3v-3h2v4a1 1 0 01-1 1h-4v-2z\"/>"}
            ]
        },
        {
            label: "Modul Kendaraan",
            items: [
                {key: "kendaraan", text: "Master Kendaraan", href: "/kendaraan", icon: "<rect x=\"3\" y=\"4\" width=\"18\" height=\"16\" rx=\"2\"/>"},
                {key: "perjalanan-kendaraan", text: "Jarak Tempuh", href: "/perjalanan-kendaraan", icon: "<circle cx=\"12\" cy=\"12\" r=\"9\"/>"},
                {key: "bbm-kendaraan", text: "BBM Kendaraan", href: "/bbm-kendaraan", icon: "<path d=\"M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4\"/><polyline points=\"7 10 12 15 17 10\"/><line x1=\"12\" y1=\"15\" x2=\"12\" y2=\"3\"/>"},
                {key: "pemeliharaan-kendaraan", text: "Pemeliharaan", href: "/pemeliharaan-kendaraan", icon: "<path d=\"M12 2 15 8l6.5 1-4.8 4.6L18 20l-6-3-6 3 1.3-6.4L2.5 9 9 8z\"/>"}
            ]
        },
        {
            label: "Modul Keamanan",
            items: [
                {key: "keamanan", text: "Laporan Keamanan", href: "/keamanan", icon: "<circle cx=\"12\" cy=\"12\" r=\"9\"/>"},
                {key: "evaluasi-keamanan", text: "Evaluasi Keamanan", href: "/evaluasi-keamanan", icon: "<path d=\"M12 2 15 8l6.5 1-4.8 4.6L18 20l-6-3-6 3 1.3-6.4L2.5 9 9 8z\"/>"}
            ]
        },
        {
            label: "Modul Manajemen",
            items: [
                {key: "aset", text: "Manajemen Aset", href: "/aset", icon: "<path d=\"M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z\"/>"}
            ]
        },
        {
            label: "Modul Reporting",
            items: [
                {key: "laporan-atk", text: "Laporan ATK", href: "/laporan/atk", icon: "<rect x=\"3\" y=\"4\" width=\"18\" height=\"16\" rx=\"2\"/>"},
                {key: "laporan-kendaraan", text: "Laporan Kendaraan", href: "/laporan/kendaraan", icon: "<rect x=\"3\" y=\"4\" width=\"18\" height=\"16\" rx=\"2\"/>"},
                {key: "laporan-aset", text: "Laporan Aset", href: "/laporan/aset", icon: "<rect x=\"3\" y=\"4\" width=\"18\" height=\"16\" rx=\"2\"/>"}
            ]
        }
    ];
    
    const staffMenu=[
        {
            label: "Menu Utama",
            items: [
                {key: "dashboard", text: "Dashboard", href: "/dashboard", icon: "<path d=\"M3 12 12 3l9 9\"/><path d=\"M5 10v10h14V10\"/>"}
            ]
        },
        {
            label: "Modul ATK",
            items: [
                {key: "permintaan-atk", text: "Permintaan / PO ATK", href: "/permintaan-atk", icon: "<path d=\"M12 2 15 8l6.5 1-4.8 4.6L18 20l-6-3-6 3 1.3-6.4L2.5 9 9 8z\"/>"},
                {key: "scan", text: "QR Scanner", href: "/atk/scan", icon: "<path d=\"M3 4a1 1 0 011-1h3v2H5v3H3V4zm2 14v-3H3v4a1 1 0 001 1h3v-2H5zm14-14h-3V2h4v5h-2V4zm-3 14h3v-3h2v4a1 1 0 01-1 1h-4v-2z\"/>"}
            ]
        },
        {
            label: "Modul Keamanan",
            items: [
                {key: "keamanan", text: "Laporan Keamanan", href: "/keamanan", icon: "<circle cx=\"12\" cy=\"12\" r=\"9\"/>"}
            ]
        },
        {
            label: "Modul Kendaraan",
            items: [
                {key: "perjalanan-kendaraan", text: "Jarak Tempuh", href: "/perjalanan-kendaraan", icon: "<circle cx=\"12\" cy=\"12\" r=\"9\"/>"},
                {key: "bbm-kendaraan", text: "BBM Kendaraan", href: "/bbm-kendaraan", icon: "<path d=\"M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4\"/><polyline points=\"7 10 12 15 17 10\"/><line x1=\"12\" y1=\"15\" x2=\"12\" y2=\"3\"/>"},
                {key: "pemeliharaan-kendaraan", text: "Pemeliharaan", href: "/pemeliharaan-kendaraan", icon: "<path d=\"M12 2 15 8l6.5 1-4.8 4.6L18 20l-6-3-6 3 1.3-6.4L2.5 9 9 8z\"/>"}
            ]
        }
    ];
    
    const n=(window.GAS_USER_ROLE === "admin") ? adminMenu : staffMenu`;
    code = code.substring(0, start) + newSidebar + code.substring(end);
    fs.writeFileSync("public/adminator_templete/2026.js", code);
    console.log("Sidebar injected to new template.");
}

