"use strict";(self.webpackChunk=self.webpackChunk||[]).push([[1],{939(e,t,a){const adminMenu=[
    {
        label: "Menu Utama",
        items: [
            {key: "dashboard", text: "Dashboard", href: "/dashboard", icon: "<path d=\'M3 12 12 3l9 9\'/><path d=\'M5 10v10h14V10\'/>"}
        ]
    },
    {
        label: "Modul ATK",
        items: [
            {key: "atk", text: "Master ATK", href: "/atk", icon: "<rect x=\'3\' y=\'4\' width=\'18\' height=\'16\' rx=\'2\'/>"},
            {key: "permintaan-atk", text: "Permintaan / PO ATK", href: "/permintaan-atk", icon: "<path d=\'M12 2 15 8l6.5 1-4.8 4.6L18 20l-6-3-6 3 1.3-6.4L2.5 9 9 8z\'/>"},
            {key: "stok-atk", text: "Stok ATK", href: "/stok-atk", icon: "<path d=\'M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4\'/><polyline points=\'7 10 12 15 17 10\'/><line x1=\'12\' y1=\'15\' x2=\'12\' y2=\'3\'/>"},
            {key: "pemakaian-atk", text: "Pemakaian ATK", href: "/pemakaian-atk", icon: "<circle cx=\'12\' cy=\'12\' r=\'9\'/>"},
            {key: "scan", text: "QR Scanner", href: "/atk/scan", icon: "<path d=\'M3 4a1 1 0 011-1h3v2H5v3H3V4zm2 14v-3H3v4a1 1 0 001 1h3v-2H5zm14-14h-3V2h4v5h-2V4zm-3 14h3v-3h2v4a1 1 0 01-1 1h-4v-2z\'/>"}
        ]
    },
    {
        label: "Modul Kendaraan",
        items: [
            {key: "kendaraan", text: "Master Kendaraan", href: "/kendaraan", icon: "<rect x=\'3\' y=\'4\' width=\'18\' height=\'16\' rx=\'2\'/>"},
            {key: "perjalanan-kendaraan", text: "Jarak Tempuh", href: "/perjalanan-kendaraan", icon: "<circle cx=\'12\' cy=\'12\' r=\'9\'/>"},
            {key: "bbm-kendaraan", text: "BBM Kendaraan", href: "/bbm-kendaraan", icon: "<path d=\'M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4\'/><polyline points=\'7 10 12 15 17 10\'/><line x1=\'12\' y1=\'15\' x2=\'12\' y2=\'3\'/>"},
            {key: "pemeliharaan-kendaraan", text: "Pemeliharaan", href: "/pemeliharaan-kendaraan", icon: "<path d=\'M12 2 15 8l6.5 1-4.8 4.6L18 20l-6-3-6 3 1.3-6.4L2.5 9 9 8z\'/>"}
        ]
    },
    {
        label: "Modul Keamanan",
        items: [
            {key: "keamanan", text: "Laporan Keamanan", href: "/keamanan", icon: "<circle cx=\'12\' cy=\'12\' r=\'9\'/>"},
            {key: "evaluasi-keamanan", text: "Evaluasi Keamanan", href: "/evaluasi-keamanan", icon: "<path d=\'M12 2 15 8l6.5 1-4.8 4.6L18 20l-6-3-6 3 1.3-6.4L2.5 9 9 8z\'/>"}
        ]
    },
    {
        label: "Modul Manajemen",
        items: [
            {key: "aset", text: "Manajemen Aset", href: "/aset", icon: "<path d=\'M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z\'/>"}
        ]
    },
    {
        label: "Modul Reporting",
        items: [
            {key: "laporan-atk", text: "Laporan ATK", href: "/laporan/atk", icon: "<rect x=\'3\' y=\'4\' width=\'18\' height=\'16\' rx=\'2\'/>"},
            {key: "laporan-kendaraan", text: "Laporan Kendaraan", href: "/laporan/kendaraan", icon: "<rect x=\'3\' y=\'4\' width=\'18\' height=\'16\' rx=\'2\'/>"},
            {key: "laporan-aset", text: "Laporan Aset", href: "/laporan/aset", icon: "<rect x=\'3\' y=\'4\' width=\'18\' height=\'16\' rx=\'2\'/>"}
        ]
    }
];

const staffMenu=[
    {
        label: "Menu Utama",
        items: [
            {key: "dashboard", text: "Dashboard", href: "/dashboard", icon: "<path d=\'M3 12 12 3l9 9\'/><path d=\'M5 10v10h14V10\'/>"}
        ]
    },
    {
        label: "Modul ATK",
        items: [
            {key: "permintaan-atk", text: "Permintaan / PO ATK", href: "/permintaan-atk", icon: "<path d=\'M12 2 15 8l6.5 1-4.8 4.6L18 20l-6-3-6 3 1.3-6.4L2.5 9 9 8z\'/>"},
            {key: "scan", text: "QR Scanner", href: "/atk/scan", icon: "<path d=\'M3 4a1 1 0 011-1h3v2H5v3H3V4zm2 14v-3H3v4a1 1 0 001 1h3v-2H5zm14-14h-3V2h4v5h-2V4zm-3 14h3v-3h2v4a1 1 0 01-1 1h-4v-2z\'/>"}
        ]
    },
    {
        label: "Modul Keamanan",
        items: [
            {key: "keamanan", text: "Laporan Keamanan", href: "/keamanan", icon: "<circle cx=\'12\' cy=\'12\' r=\'9\'/>"}
        ]
    },
    {
        label: "Modul Kendaraan",
        items: [
            {key: "perjalanan-kendaraan", text: "Jarak Tempuh", href: "/perjalanan-kendaraan", icon: "<circle cx=\'12\' cy=\'12\' r=\'9\'/>"},
            {key: "bbm-kendaraan", text: "BBM Kendaraan", href: "/bbm-kendaraan", icon: "<path d=\'M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4\'/><polyline points=\'7 10 12 15 17 10\'/><line x1=\'12\' y1=\'15\' x2=\'12\' y2=\'3\'/>"},
            {key: "pemeliharaan-kendaraan", text: "Pemeliharaan", href: "/pemeliharaan-kendaraan", icon: "<path d=\'M12 2 15 8l6.5 1-4.8 4.6L18 20l-6-3-6 3 1.3-6.4L2.5 9 9 8z\'/>"}
        ]
    }
];

const n=(window.GAS_USER_ROLE === "admin") ? adminMenu : staffMenu,M={Day:"timeGridDay",Week:"timeGridWeek",Month:"dayGridMonth",Agenda:"listWeek"};let E=null;function A(e){if(E)try{E.destroy()}catch{}E=new f.Vv(e,{plugins:[b.A,y.A,k.A,w.Ay],initialView:"dayGridMonth",initialDate:"2026-04-25",headerToolbar:!1,height:"100%",expandRows:!0,dayMaxEvents:3,fixedWeekCount:!1,firstDay:0,nowIndicator:!0,selectable:!0,editable:!0,events:x,dayHeaderFormat:{weekday:"short"}}),E.render(),function(e){const t=e.closest(".cal-main")||document,a=t.querySelector(".cal-month"),n=()=>{if(!a||!E)return;const e=E.getDate(),t=e.toLocaleString("en-US",{month:"long"}),n=e.getFullYear();a.innerHTML=`${t} <span class="yr">${n}</span>`};t.querySelectorAll(".cal-nav-btn").forEach((e,t)=>{e.addEventListener("click",()=>{E&&(0===t&&E.prev(),1===t&&E.next(),n())})});const o=t.querySelector(".cal-today-btn");o&&o.addEventListener("click",()=>{E.today(),n()}),t.querySelectorAll(".cal-view-tab").forEach(e=>{e.addEventListener("click",()=>{const a=e.textContent.trim(),o=M[a]||"dayGridMonth";t.querySelectorAll(".cal-view-tab").forEach(t=>t.classList.toggle("is-active",t===e)),E.changeView(o),n()})}),setTimeout(n,0)}(e)}let L=null,C=null,S=null,D=null,T=[],N=[],V=0;function $(e,t){if(!t)return 1;const a=t.toLowerCase(),n=e.label.toLowerCase();return n===a?100:n.startsWith(a)?50:n.includes(a)?20:e.section.toLowerCase().includes(a)?5:0}function B(){D.innerHTML=0===N.length?'<div class="palette-empty">No results</div>':N.map((e,t)=>`\n      <div class="palette-result${t===V?" is-selected":""}" role="option" data-index="${t}" aria-selected="${t===V}">\n        <span class="palette-result-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">${e.icon||""}</svg></span>\n        <span class="palette-result-label">${e.label}</span>\n        <span class="palette-result-section">${e.section}</span>\n      </div>\n    `).join("")}function P(e){N=T.map(t=>({item:t,s:$(t,e)})).filter(e=>e.s>0).sort((e,t)=>t.s-e.s).slice(0,12).map(e=>e.item),V=0,B()}function H(e){e&&(I(),"action"===e.kind&&"function"==typeof e.action?e.action():e.href&&("_blank"===e.target?window.open(e.href,"_blank","noopener"):window.location.href=e.href))}function R(){const e=D.querySelector(".palette-result.is-selected");e&&"function"==typeof e.scrollIntoView&&e.scrollIntoView({block:"nearest"})}function q(){C&&!document.contains(C)&&(C=null,L=null,S=null,D=null),C||(L=document.createElement("div"),L.className="palette-backdrop",L.innerHTML='\n  <div class="palette-modal" role="dialog" aria-modal="true" aria-label="Command palette">\n    <div class="palette-input-row">\n      <svg viewBox="0 0 24 24" class="palette-icon" aria-hidden="true">\n        <circle cx="11" cy="11" r="7" fill="none" stroke="currentColor" stroke-width="2"/>\n        <path d="m21 21-4.3-4.3" fill="none" stroke="currentColor" stroke-width="2"/>\n      </svg>\n      <input class="palette-input" type="text" placeholder="Search pages, actions…" autocomplete="off" spellcheck="false">\n      <kbd class="palette-esc">esc</kbd>\n    </div>\n    <div class="palette-results" role="listbox"></div>\n    <div class="palette-foot">\n      <span><kbd>↑</kbd><kbd>↓</kbd> navigate</span>\n      <span><kbd>↵</kbd> select</span>\n      <span><kbd>esc</kbd> close</span>\n    </div>\n  </div>\n',document.body.appendChild(L),C=L.querySelector(".palette-modal"),S=L.querySelector(".palette-input"),D=L.querySelector(".palette-results"),L.addEventListener("click",e=>{e.target===L&&I()}),S.addEventListener("input",()=>P(S.value)),S.addEventListener("keydown",e=>{"ArrowDown"===e.key?(e.preventDefault(),V=Math.min(V+1,N.length-1),B(),R()):"ArrowUp"===e.key?(e.preventDefault(),V=Math.max(V-1,0),B(),R()):"Enter"===e.key?(e.preventDefault(),H(N[V])):"Escape"===e.key&&(e.preventDefault(),I())}),D.addEventListener("click",e=>{const t=e.target.closest(".palette-result");t&&H(N[Number(t.getAttribute("data-index"))])})),0===T.length&&(T=function(){const e=[];for(const t of n)for(const a of t.items)if(a.children)for(const n of a.children)e.push({kind:"page",label:n.text,section:`${t.label} › ${a.text}`,href:n.href,icon:a.icon});else a.href&&"#"!==a.href&&e.push({kind:"page",label:a.text,section:t.label,href:a.href,icon:a.icon});return e.push({kind:"action",label:"Toggle theme (light / dark)",section:"Action",action:()=>{const e=document.documentElement,t="dark"===e.getAttribute("data-theme")?"light":"dark";e.setAttribute("data-theme",t);try{localStorage.setItem("dash26-theme",t)}catch{}const a=document.getElementById("themeToggle");a&&a.click()},icon:'<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>'}),e.push({kind:"link",label:"View on GitHub",section:"External",href:"https://github.com/puikinsh/Adminator-admin-dashboard",target:"_blank",icon:'<path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/>'}),e.push({kind:"link",label:"Documentation",section:"External",href:"https://puikinsh.github.io/Adminator-admin-dashboard/",target:"_blank",icon:'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/>'}),e}()),S.value="",P(""),document.body.classList.add("has-palette-open"),setTimeout(()=>S.focus(),0)}function I(){C&&document.body.classList.remove("has-palette-open")}function W(){return document.body.classList.contains("has-palette-open")}let z=!1;function O(){s(),i(),z||(z=!0,document.addEventListener("click",e=>{e.target.closest("[data-palette-open]")&&(e.preventDefault(),q())}),document.addEventListener("keydown",e=>{if((e.metaKey||e.ctrlKey)&&"k"===e.key)return e.preventDefault(),void(W()?I():q());if("/"===e.key&&!W()){const t=document.activeElement&&document.activeElement.tagName,a=document.activeElement&&document.activeElement.isContentEditable;"INPUT"===t||"TEXTAREA"===t||"SELECT"===t||a||(e.preventDefault(),q())}})),function(){if(!document.querySelector("canvas[data-chart-key]"))return;u(),new MutationObserver(e=>{e.some(e=>"data-theme"===e.attributeName)&&u()}).observe(document.documentElement,{attributes:!0})}(),function(){if(!document.querySelector("[data-vmap]"))return;g(),new MutationObserver(e=>{e.some(e=>"data-theme"===e.attributeName)&&g()}).observe(document.documentElement,{attributes:!0})}(),function(){const e=document.querySelector("[data-fc]");if(!e)return;A(e),new MutationObserver(e=>{e.some(e=>"data-theme"===e.attributeName)&&E&&E.render()}).observe(document.documentElement,{attributes:!0})}()}"loading"===document.readyState?document.addEventListener("DOMContentLoaded",O):O()}},e=>{e.O(0,[707,311,96],()=>{return t=939,e(e.s=t);var t});e.O()}]);