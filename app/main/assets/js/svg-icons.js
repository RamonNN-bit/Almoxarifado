// SVGs finos e modernos para substituir Font Awesome
const SVG_ICONS = {
    warehouse: `<svg class="icon icon-warehouse" viewBox="0 0 24 24">
        <path d="M3 21h18l-1-7H4l-1 7z"/>
        <path d="M3 10h18l-1-7H4l-1 7z"/>
        <path d="M9 10v11"/>
        <path d="M15 10v11"/>
    </svg>`,

    home: `<svg class="icon icon-home" viewBox="0 0 24 24">
        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
        <polyline points="9,22 9,12 15,12 15,22"/>
    </svg>`,

    boxes: `<svg class="icon icon-boxes" viewBox="0 0 24 24">
        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
        <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
        <line x1="12" y1="22.08" x2="12" y2="12"/>
    </svg>`,

    tools: `<svg class="icon icon-tools" viewBox="0 0 24 24">
        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
    </svg>`,

    clipboardList: `<svg class="icon icon-clipboard-list" viewBox="0 0 24 24">
        <path d="M9 11H5a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2h-4"/>
        <rect x="9" y="3" width="6" height="4" rx="2" ry="2"/>
        <line x1="9" y1="12" x2="15" y2="12"/>
        <line x1="9" y1="16" x2="15" y2="16"/>
        <line x1="9" y1="20" x2="15" y2="20"/>
    </svg>`,

    signOut: `<svg class="icon icon-sign-out" viewBox="0 0 24 24">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
        <polyline points="16,17 21,12 16,7"/>
        <line x1="21" y1="12" x2="9" y2="12"/>
    </svg>`,

    bell: `<svg class="icon icon-bell" viewBox="0 0 24 24">
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
    </svg>`,

    checkCircle: `<svg class="icon icon-check-circle" viewBox="0 0 24 24">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
        <polyline points="22,4 12,14.01 9,11.01"/>
    </svg>`,

    exclamationCircle: `<svg class="icon icon-exclamation-circle" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>`,

    exclamationTriangle: `<svg class="icon icon-exclamation-triangle" viewBox="0 0 24 24">
        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
        <line x1="12" y1="9" x2="12" y2="13"/>
        <line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>`,

    clock: `<svg class="icon icon-clock" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/>
        <polyline points="12,6 12,12 16,14"/>
    </svg>`,

    timesCircle: `<svg class="icon icon-times-circle" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/>
        <line x1="15" y1="9" x2="9" y2="15"/>
        <line x1="9" y1="9" x2="15" y2="15"/>
    </svg>`,

    box: `<svg class="icon icon-box" viewBox="0 0 24 24">
        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
        <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
        <line x1="12" y1="22.08" x2="12" y2="12"/>
    </svg>`,

    hashtag: `<svg class="icon icon-hashtag" viewBox="0 0 24 24">
        <line x1="4" y1="9" x2="20" y2="9"/>
        <line x1="4" y1="15" x2="20" y2="15"/>
        <line x1="10" y1="3" x2="8" y2="21"/>
        <line x1="16" y1="3" x2="14" y2="21"/>
    </svg>`,

    paperPlane: `<svg class="icon icon-paper-plane" viewBox="0 0 24 24">
        <line x1="22" y1="2" x2="11" y2="13"/>
        <polygon points="22,2 15,22 11,13 2,9 22,2"/>
    </svg>`,

    check: `<svg class="icon icon-check" viewBox="0 0 24 24">
        <polyline points="20,6 9,17 4,12"/>
    </svg>`,

    times: `<svg class="icon icon-times" viewBox="0 0 24 24">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
    </svg>`,

    eye: `<svg class="icon icon-eye" viewBox="0 0 24 24">
        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
        <circle cx="12" cy="12" r="3"/>
    </svg>`,

    plus: `<svg class="icon icon-plus" viewBox="0 0 24 24">
        <line x1="12" y1="5" x2="12" y2="19"/>
        <line x1="5" y1="12" x2="19" y2="12"/>
    </svg>`,

    sort: `<svg class="icon icon-sort" viewBox="0 0 24 24">
        <path d="M3 6h18"/>
        <path d="M7 12h10"/>
        <path d="M10 18h4"/>
    </svg>`,

    boxOpen: `<svg class="icon icon-box-open" viewBox="0 0 24 24">
        <path d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2H5a2 2 0 0 0-2-2z"/>
        <path d="M8 21v-4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v4"/>
        <path d="M12 3v18"/>
    </svg>`,

    list: `<svg class="icon icon-list" viewBox="0 0 24 24">
        <line x1="8" y1="6" x2="21" y2="6"/>
        <line x1="8" y1="12" x2="21" y2="12"/>
        <line x1="8" y1="18" x2="21" y2="18"/>
        <line x1="3" y1="6" x2="3.01" y2="6"/>
        <line x1="3" y1="12" x2="3.01" y2="12"/>
        <line x1="3" y1="18" x2="3.01" y2="18"/>
    </svg>`,

    tag: `<svg class="icon icon-tag" viewBox="0 0 24 24">
        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
        <line x1="7" y1="7" x2="7.01" y2="7"/>
    </svg>`,

    ruler: `<svg class="icon icon-ruler" viewBox="0 0 24 24">
        <path d="M21.3 8.7l-5.6-5.6c-.4-.4-1-.4-1.4 0L2.7 14.3c-.4.4-.4 1 0 1.4l5.6 5.6c.4.4 1 .4 1.4 0L21.3 10.1c.4-.4.4-1 0-1.4z"/>
        <path d="M14.5 4.5l5 5"/>
        <path d="M16.5 6.5l2 2"/>
        <path d="M13.5 3.5l2 2"/>
        <path d="M10.5 0.5l2 2"/>
    </svg>`,

    industry: `<svg class="icon icon-industry" viewBox="0 0 24 24">
        <path d="M2 3h6l4 4v13H2V3z"/>
        <path d="M10 7h6l4 4v9H10V7z"/>
        <path d="M14 11v6"/>
        <path d="M18 11v6"/>
    </svg>`,

    cube: `<svg class="icon icon-cube" viewBox="0 0 24 24">
        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
        <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
        <line x1="12" y1="22.08" x2="12" y2="12"/>
    </svg>`,

    download: `<svg class="icon icon-download" viewBox="0 0 24 24">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
        <polyline points="7,10 12,15 17,10"/>
        <line x1="12" y1="15" x2="12" y2="3"/>
    </svg>`,

    clipboardCheck: `<svg class="icon icon-clipboard-check" viewBox="0 0 24 24">
        <path d="M9 11H5a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2h-4"/>
        <rect x="9" y="3" width="6" height="4" rx="2" ry="2"/>
        <polyline points="9,12 11,14 15,10"/>
    </svg>`,

    user: `<svg class="icon icon-user" viewBox="0 0 24 24">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
        <circle cx="12" cy="7" r="4"/>
    </svg>`,

    calendarDay: `<svg class="icon icon-calendar-day" viewBox="0 0 24 24">
        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
        <line x1="16" y1="2" x2="16" y2="6"/>
        <line x1="8" y1="2" x2="8" y2="6"/>
        <line x1="3" y1="10" x2="21" y2="10"/>
        <path d="M8 14h.01"/>
        <path d="M12 14h.01"/>
        <path d="M16 14h.01"/>
        <path d="M8 18h.01"/>
        <path d="M12 18h.01"/>
        <path d="M16 18h.01"/>
    </svg>`
};

// Função para substituir ícones Font Awesome por SVGs
function replaceFontAwesomeIcons() {
    const iconMap = {
        'fa-warehouse': 'warehouse',
        'fa-home': 'home',
        'fa-boxes': 'boxes',
        'fa-tools': 'tools',
        'fa-clipboard-list': 'clipboardList',
        'fa-sign-out-alt': 'signOut',
        'fa-bell': 'bell',
        'fa-check-circle': 'checkCircle',
        'fa-exclamation-circle': 'exclamationCircle',
        'fa-exclamation-triangle': 'exclamationTriangle',
        'fa-clock': 'clock',
        'fa-times-circle': 'timesCircle',
        'fa-box': 'box',
        'fa-hashtag': 'hashtag',
        'fa-paper-plane': 'paperPlane',
        'fa-check': 'check',
        'fa-times': 'times',
        'fa-eye': 'eye',
        'fa-plus': 'plus',
        'fa-sort': 'sort',
        'fa-box-open': 'boxOpen',
        'fa-list': 'list',
        'fa-tag': 'tag',
        'fa-ruler': 'ruler',
        'fa-industry': 'industry',
        'fa-cube': 'cube',
        'fa-download': 'download',
        'fa-clipboard-check': 'clipboardCheck',
        'fa-user': 'user',
        'fa-calendar-day': 'calendarDay'
    };

    // Substituir todos os ícones Font Awesome
    Object.keys(iconMap).forEach(faClass => {
        const elements = document.querySelectorAll(`.${faClass}`);
        elements.forEach(element => {
            const svgIcon = iconMap[faClass];
            if (SVG_ICONS[svgIcon]) {
                element.outerHTML = SVG_ICONS[svgIcon];
            }
        });
    });
}

// Executar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', replaceFontAwesomeIcons);
