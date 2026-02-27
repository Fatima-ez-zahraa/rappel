/*RAPPEL - Global App JS */

const API_URL = '/rappel/api';
// Fallback if accessed without /rappel prefix (e.g. root domain)
const ACTUAL_API_URL = window.API_BASE_URL || (window.location.pathname.startsWith('/rappel') ? '/rappel/api' : '/api');

// Auth helpers (token stored in sessionStorage for JS calls) 
const Auth = {
    getToken: () => sessionStorage.getItem('rappel_token') || '',
    setToken: (t) => sessionStorage.setItem('rappel_token', t),
    getUser: () => {
        try { return JSON.parse(sessionStorage.getItem('rappel_user') || 'null'); } catch { return null; }
    },
    setUser: (u) => sessionStorage.setItem('rappel_user', JSON.stringify(u)),
    isLoggedIn: () => !!sessionStorage.getItem('rappel_token'),
    clear: () => { sessionStorage.removeItem('rappel_token'); sessionStorage.removeItem('rappel_user'); },
};

// Global Helpers
window.escapeHtml = function (value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
};

window.formatRelativeTime = function (dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 0) return "À l'instant";
    if (diffInSeconds < 60) return `Il y a ${diffInSeconds}s`;

    const diffInMinutes = Math.floor(diffInSeconds / 60);
    if (diffInMinutes < 60) return `Il y a ${diffInMinutes}m`;

    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) return `Il y a ${diffInHours}h`;

    // Set both to midnight for accurate day difference
    const d1 = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    const d2 = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const diffDays = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return "Aujourd'hui";
    if (diffDays === 1) return "Hier";
    if (diffDays < 7) return `Il y a ${diffDays} jours`;

    return date.toLocaleDateString('fr-FR');
};

// API fetch helper 
window.apiFetch = async function (endpoint, options = {}) {
    const token = Auth.getToken() || (window.PHP_TOKEN || '');
    const headers = {
        'Content-Type': 'application/json',
        ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
        ...(options.headers || {}),
    };
    const res = await fetch(`${ACTUAL_API_URL}${endpoint}`, { ...options, headers });
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || data.message || 'Erreur API');
    return data;
}

// Header scroll effect
function initHeader() {
    const header = document.getElementById('main-header');
    if (!header) return;
    const update = () => {
        if (window.scrollY > 20) {
            header.classList.add('bg-white/80', 'backdrop-blur-xl', 'border-b', 'border-navy-100/50', 'py-3', 'shadow-sm');
            header.classList.remove('bg-transparent', 'py-5');
        } else {
            header.classList.remove('bg-white/80', 'backdrop-blur-xl', 'border-b', 'border-navy-100/50', 'py-3', 'shadow-sm');
            header.classList.add('bg-transparent', 'py-5');
        }
    };
    window.addEventListener('scroll', update, { passive: true });
    update();
}

// Mobile menu (public header)
function initMobileMenu() {
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');
    const iconMenu = document.getElementById('icon-menu');
    const iconClose = document.getElementById('icon-close');
    if (!btn || !menu) return;

    btn.addEventListener('click', () => {
        const isOpen = !menu.classList.contains('hidden');
        menu.classList.toggle('hidden', isOpen);
        menu.classList.toggle('flex', !isOpen);
        iconMenu.classList.toggle('hidden', !isOpen);
        iconClose.classList.toggle('hidden', isOpen);
    });
}

// Smooth scroll for anchor links
function initScrollLinks() {
    document.querySelectorAll('.scroll-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.dataset.target;
            const el = document.getElementById(targetId);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth' });
            } else {
                // Navigate to home then scroll
                window.location.href = `/rappel/public/#${targetId}`;
            }
        });
    });
    // Handle hash on page load
    if (window.location.hash) {
        setTimeout(() => {
            const el = document.getElementById(window.location.hash.substring(1));
            if (el) el.scrollIntoView({ behavior: 'smooth' });
        }, 300);
    }
}

// Sidebar (dashboard)
let sidebarOpen = true;
let isMobile = window.innerWidth < 1024;

function updateSidebarState() {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('dashboard-main');
    const toggleIcon = document.getElementById('toggle-icon');
    if (!sidebar) return;

    if (isMobile) {
        // On mobile: sidebar slides in/out via translateX (off-canvas)
        // Content stays full-width (no padding shift)
        sidebar.style.width = '280px';
        sidebar.classList.remove('sidebar-collapsed');
        // Remove any desktop padding override
        if (main) {
            main.style.paddingLeft = '';
        }
    } else {
        // On desktop: adjust width and content padding
        const openWidth = '260px';
        const collapsedWidth = '80px';
        sidebar.style.width = sidebarOpen ? openWidth : collapsedWidth;
        sidebar.classList.toggle('sidebar-collapsed', !sidebarOpen);
        if (main) {
            main.style.paddingLeft = sidebarOpen ? openWidth : collapsedWidth;
        }
        // Show/hide labels
        document.querySelectorAll('.sidebar-label, .sidebar-user-info, .sidebar-logout-text').forEach(el => {
            el.style.display = sidebarOpen ? '' : 'none';
        });
        document.querySelectorAll('.sidebar-logo-full').forEach(el => el.style.display = sidebarOpen ? '' : 'none');
        document.querySelectorAll('.sidebar-logo-icon').forEach(el => el.style.display = sidebarOpen ? 'none' : '');
        if (toggleIcon) {
            toggleIcon.setAttribute('data-lucide', sidebarOpen ? 'chevron-left' : 'chevron-right');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }
}

function toggleSidebar() {
    sidebarOpen = !sidebarOpen;
    updateSidebarState();
}

function openSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    if (sidebar) sidebar.classList.add('mobile-open');
    if (overlay) overlay.classList.remove('hidden');
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    if (sidebar) sidebar.classList.remove('mobile-open');
    if (overlay) overlay.classList.add('hidden');
}

function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar) return;

    const handleResize = () => {
        const wasMobile = isMobile;
        isMobile = window.innerWidth < 1024;
        if (isMobile !== wasMobile) {
            if (isMobile) closeSidebar();
            updateSidebarState();
        }
    };
    window.addEventListener('resize', handleResize, { passive: true });
    updateSidebarState();
}

// Toast notifications 
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    const colors = {
        success: 'bg-green-50 border-green-200 text-green-800',
        error: 'bg-red-50 border-red-200 text-red-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800',
    };
    toast.className = `fixed bottom-6 right-6 z-[9999] px-6 py-4 rounded-2xl border font-bold shadow-premium
                       ${colors[type] || colors.info} animate-fade-in`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

// Loading spinner helper

function setButtonLoading(btn, loading, originalText) {
    if (loading) {
        btn.disabled = true;
        btn.dataset.originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner"></span>';
    } else {
        btn.disabled = false;
        btn.innerHTML = originalText || btn.dataset.originalText || 'Envoyer';
    }
}

// Cookie Consent 
function initCookies() {
    const banner = document.getElementById('cookie-banner');
    if (!banner) return;

    // Debug: Force show with ?reset_cookies=1
    if (window.location.search.includes('reset_cookies=1')) {
        localStorage.removeItem('cookie_consent');
    }

    const consent = localStorage.getItem('cookie_consent');
    if (!consent) {
        console.log("Cookie banner: showing (no consent found)");
        setTimeout(() => {
            banner.classList.remove('translate-y-32', 'opacity-0', 'pointer-events-none');
            banner.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
        }, 600);
    } else {
        console.log("Cookie banner: hidden (consent already given: " + consent + ")");
    }
}

function acceptCookies() {
    localStorage.setItem('cookie_consent', 'accepted');
    hideCookieBanner();
    // Here you would normally trigger GTM or other analytics scripts
    showToast('Merci de votre confiance !', 'success');
}

function refuseCookies() {
    localStorage.setItem('cookie_consent', 'refused');
    hideCookieBanner();
    showToast('Cookies refusés.', 'info');
}

function hideCookieBanner() {
    const banner = document.getElementById('cookie-banner');
    if (banner) {
        banner.classList.add('translate-y-32', 'opacity-0', 'pointer-events-none');
        banner.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
    }
}

// ---- Init on DOM ready ----
document.addEventListener('DOMContentLoaded', () => {
    initHeader();
    initMobileMenu();
    initScrollLinks();
    initSidebar();
    initCookies();
    if (typeof lucide !== 'undefined') lucide.createIcons();
});
