<?php
require_once __DIR__ . '/../includes/auth.php';
requireAuth(false);
$pageTitle = 'Mes Demandes';
$user = getCurrentUser();
$token = getToken();
if (($user['role'] ?? '') === 'provider') { header('Location: /rappel/public/pro/dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<?php include __DIR__ . '/../includes/head.php'; ?>
<script>window.API_BASE_URL = '/rappel/api';</script>
<script src="/rappel/public/assets/js/app.js?v=4.6"></script>
<style>
body { padding-bottom: 90px; background: #D3D3D3 !important; }
.no-scrollbar::-webkit-scrollbar { display:none !important; }
.no-scrollbar { -ms-overflow-style:none !important; scrollbar-width:none !important; }
.pill { display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .9rem;border-radius:9999px;font-size:.65rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em;border:1px solid; }
.stacked-card { background: white; border-radius: 2.5rem; border: 1px solid rgba(226, 232, 240, 0.4); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); margin-bottom: 1.5rem; position: relative; overflow: hidden; }
.stacked-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.08); }
.stepper-line { position: absolute; top: 1.25rem; left: 2rem; right: 2rem; height: 1px; background: #EEF2FF; z-index: 1; }
.stepper-progress { position: absolute; top: 1.25rem; left: 2rem; height: 1px; background: #0E1648; z-index: 2; transition: width 0.8s cubic-bezier(0.65, 0, 0.35, 1); }
.step-circle { width: 2.5rem; height: 2.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 3; background: white; border: 1px solid #EEF2FF; transition: all 0.4s ease; }
.step-circle.active { background: #0E1648; border-color: #0E1648; color: white; transform: scale(1.1); box-shadow: 0 8px 16px rgba(14, 22, 72, 0.2); }
.step-circle.todo { border-color: #EEF2FF; background: #F8FAFC; color: #CBD5E1; }
.step-circle.done { background: #0E1648; border-color: #0E1648; color: white; }
.req-card { transition:all .25s ease; cursor:pointer; }
.req-card:hover { transform:translateY(-2px); box-shadow:0 12px 32px rgba(14,22,72,.1); }
.req-card.selected { border-color:#0E1648!important; box-shadow:0 0 0 2px rgba(14,22,72,.15); }
@keyframes shimmer{0%{background-position:200%}100%{background-position:-200%}}
@keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
@keyframes slideLeft{from{opacity:0;transform:translateX(24px)}to{opacity:1;transform:translateX(0)}}
@keyframes slideRight{from{opacity:0;transform:translateX(-24px)}to{opacity:1;transform:translateX(0)}}
@keyframes spin{to{transform:rotate(360deg)}}
.animate-in { animation:fadeIn .3s ease forwards; }
.step-in { animation:slideLeft .28s cubic-bezier(.16,1,.3,1) forwards; }
.step-back { animation:slideRight .28s cubic-bezier(.16,1,.3,1) forwards; }
.fade-up { opacity:0;animation:fadeIn .4s ease forwards; }
#detail-panel { transition: all .35s cubic-bezier(.16,1,.3,1); }
.filter-tab { padding:.75rem 1.75rem; border-radius:1.25rem; font-size:.85rem; font-weight:900; text-transform:uppercase; letter-spacing:.08em; cursor:pointer; transition:all .2s; border:1.5px solid transparent; }
.filter-tab.active { background:#0E1648; color:#fff; border-color:#0E1648; }
.filter-tab:not(.active) { background:#fff; color:#94a3b8; border-color:#e2e8f0; }
.filter-tab:not(.active):hover { border-color:#0E1648; color:#0E1648; }
.tl-line { position:absolute; left:.9rem; top:2.25rem; bottom:0; width:2px; background:#e2e8f0; }
.tl-dot { width:1.8rem; height:1.8rem; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
/* Modal */
#nr-overlay { display:none; }
#nr-overlay.open { display:flex; animation:fadeIn .2s ease; }
#nr-box { animation:slideLeft .3s cubic-bezier(.16,1,.3,1); }
/* Sector cards */
.sec-card { background:#f8fafc; border-radius:2rem; padding:1.5rem .75rem; cursor:pointer; text-align:center; transition:all .3s cubic-bezier(.16,1,.3,1); display:flex; flex-direction:column; align-items:center; gap:.75rem; border:2px solid transparent; }
.sec-card:hover { background:#fff; box-shadow:0 12px 24px rgba(14,22,72,0.08); transform:translateY(-2px); }
.sec-card.sel { background:#fff; border-color:#0E1648; box-shadow:0 0 0 1px #0E1648; }
.sec-icon-wrap { width:3.5rem; height:3.5rem; border-radius:1.25rem; display:flex; align-items:center; justify-content:center; transition:all .3s; }
.sec-lbl { font-size:.85rem; font-weight:800; color:#0E1648; letter-spacing:-0.01em; }
.sec-card.sel .sec-lbl { color:#0E1648; }
/* Step progress */
.sdot { width:2rem; height:2rem; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.68rem; font-weight:900; transition:all .3s; flex-shrink:0; }
.sdot.done  { background:#0E1648; color:#fff; }
.sdot.active{ background:#7CCB63; color:#fff; box-shadow:0 0 0 4px rgba(124,203,99,.25); }
.sdot.todo  { background:#e2e8f0; color:#94a3b8; }
/* Budget range */
input[type=range]{-webkit-appearance:none;width:100%;height:6px;border-radius:3px;background:#e2e8f0;outline:none;}
input[type=range]::-webkit-slider-thumb{-webkit-appearance:none;width:22px;height:22px;border-radius:50%;background:#0E1648;cursor:pointer;box-shadow:0 2px 8px rgba(14,22,72,.25);}

/* Bottom Nav Styles */
.bottom-nav { 
    position:fixed; bottom:0; left:0; right:0; 
    background: rgba(255, 255, 255, 0.82);
    backdrop-filter: blur(24px); 
    -webkit-backdrop-filter: blur(24px);
    border-top: 1px solid rgba(14, 22, 72, 0.08); 
    z-index:40; 
    safe-area-inset-bottom:env(safe-area-inset-bottom); padding-bottom:env(safe-area-inset-bottom); 
    height: 75px; display: flex; align-items: center; 
    box-shadow: 0 -4px 20px rgba(0,0,0,0.03);
}
.bnav-btn { flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.25rem; padding:.5rem; font-size:.65rem; font-weight:800; text-transform:uppercase; letter-spacing:.05em; color:#94a3b8; transition:all .2s; cursor:pointer; text-decoration:none; }
.bnav-btn i { transition: transform 0.2s ease; }
.bnav-btn.active { color:#0E1648; }
.bnav-btn.active i { transform: translateY(-2px); color: #0E1648; }
.bnav-btn:hover { color:#0E1648; }

</style>
</head>
<body class="min-h-screen">
<body class="min-h-screen">

<header class="sticky top-0 z-50 bg-slate-50/80 backdrop-blur-xl h-20 flex items-center gap-5 px-4 sm:px-8">
    <div class="flex items-center gap-3">
        <div class="w-2.5 h-2.5 rounded-full bg-[#0E1648]"></div>
        <h1 class="font-black text-brand-950 text-base uppercase tracking-widest">Suivi de mes demandes</h1>
    </div>
    
    <div class="flex-1 flex justify-end gap-2 overflow-x-auto no-scrollbar py-2">
        <button class="filter-tab active !rounded-full !py-2 !px-5" data-filter="all" onclick="setFilter('all',this)">Tout</button>
        <button class="filter-tab !rounded-full !py-2 !px-5" data-filter="active" onclick="setFilter('active',this)">En cours</button>
        <button class="filter-tab !rounded-full !py-2 !px-5" data-filter="done" onclick="setFilter('done',this)">Terminés</button>
    </div>

    <button onclick="openNewModal()" 
            class="w-12 h-12 rounded-2xl bg-[#0E1648] text-white flex items-center justify-center shadow-lg shadow-brand-950/20 active:scale-95 transition-all">
        <i data-lucide="plus" style="width:24px;height:24px;"></i>
    </button>
</header>

<div class="max-w-4xl mx-auto px-4 py-8 pb-32">
    <!-- List container (Full width) -->
    <div id="req-list" class="space-y-6">
        <!-- Skeleton Loaders -->
        <div class="skeleton h-64 rounded-[2.5rem]"></div>
        <div class="skeleton h-64 rounded-[2.5rem]" style="opacity:.6"></div>
    </div>
</div>

<!-- Mobile detail drawer -->
<div id="drawer-overlay" class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm hidden" onclick="closeDrawer()"></div>
<div id="drawer" class="fixed bottom-0 left-0 right-0 z-50 bg-white rounded-t-3xl shadow-2xl max-h-[90vh] overflow-y-auto translate-y-full transition-transform duration-300 ease-out">
    <div class="w-10 h-1 bg-slate-200 rounded-full mx-auto mt-3 mb-1"></div>
    <div id="drawer-content" class="p-5 space-y-5"></div>
</div>

<!-- ===== Nouvelle Demande Modal (multi-step) ===== -->
<div id="nr-overlay" class="fixed inset-0 z-[999] bg-[#0E1648]/60 backdrop-blur-md items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden" id="nr-box">

        <!-- Header -->
        <div class="relative bg-[#0E1648] px-6 pt-6 pb-8">
            <button onclick="closeNewModal()" class="absolute top-4 right-4 w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/70 transition-all">
                <i data-lucide="x" style="width:14px;height:14px;"></i>
            </button>
            <p class="text-white/50 text-[10px] font-black uppercase tracking-widest mb-1">Nouvelle demande</p>
            <h2 id="nr-title" class="text-xl font-display font-black text-white">Votre secteur</h2>
            <!-- Step dots -->
            <div class="flex items-center gap-2 mt-5">
                <div class="sdot active" id="nr-dot-1">1</div>
                <div class="flex-1 h-0.5 bg-white/20 rounded-full" id="nr-line-1"></div>
                <div class="sdot todo" id="nr-dot-2">2</div>
                <div class="flex-1 h-0.5 bg-white/20 rounded-full" id="nr-line-2"></div>
                <div class="sdot todo" id="nr-dot-3">3</div>
            </div>
        </div>

        <!-- Error -->
        <div id="nr-error" class="hidden mx-6 mt-4 p-3 bg-red-50 border border-red-100 rounded-xl text-xs font-bold text-red-600 flex items-center gap-2">
            <i data-lucide="alert-circle" style="width:14px;height:14px;"></i>
            <span id="nr-error-text"></span>
        </div>

        <!-- Step 1: Secteur -->
        <div id="nr-s1" class="p-6 space-y-4">
            <p class="text-xs text-slate-500 font-medium">Choisissez le domaine d'expertise dont vous avez besoin.</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                <?php foreach([
                    ['val'=>'assurance',  'icon'=>'shield',      'label'=>'Assurance', 'bg'=>'#EEF2FF', 'clr'=>'#4F46E5'],
                    ['val'=>'renovation', 'icon'=>'hammer',      'label'=>'Rénovation', 'bg'=>'#FFF7ED', 'clr'=>'#EA580C'],
                    ['val'=>'energie',    'icon'=>'zap',         'label'=>'Énergie',    'bg'=>'#FEFCE8', 'clr'=>'#CA8A04'],
                    ['val'=>'finance',    'icon'=>'trending-up', 'label'=>'Finance',    'bg'=>'#F0FDF4', 'clr'=>'#16A34A'],
                    ['val'=>'garage',     'icon'=>'car',         'label'=>'Garage',     'bg'=>'#F0F9FF', 'clr'=>'#0284C7'],
                    ['val'=>'telecom',    'icon'=>'smartphone',  'label'=>'Télécoms',   'bg'=>'#FAF5FF', 'clr'=>'#9333EA'],
                ] as $sec): ?>
                <button type="button" class="sec-card" data-val="<?= $sec['val'] ?>" data-icon="<?= $sec['icon'] ?>" onclick="nrSelSector(this)">
                    <div class="sec-icon-wrap" style="background:<?= $sec['bg'] ?>;">
                        <i data-lucide="<?= $sec['icon'] ?>" style="width:24px;height:24px;color:<?= $sec['clr'] ?>;"></i>
                    </div>
                    <span class="sec-lbl"><?= $sec['label'] ?></span>
                </button>
                <?php endforeach; ?>
            </div>
            <button onclick="nrGo(2)" class="w-full h-12 bg-[#0E1648] hover:bg-brand-700 text-white font-black rounded-2xl text-sm uppercase tracking-widest transition-all active:scale-95">
                Continuer →
            </button>
        </div>

        <!-- Step 2: Besoin & Budget -->
        <div id="nr-s2" class="p-6 space-y-5 hidden">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Décrivez votre besoin *</label>
                <textarea id="nr-need" rows="4" placeholder="Ex : Je souhaite comparer des offres d'assurance habitation pour 120m²..."
                    class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none text-sm font-medium text-slate-900 resize-none transition-all leading-relaxed"></textarea>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Budget estimé</label>
                <input type="range" id="nr-budget" min="0" max="50000" step="500" value="5000" oninput="nrUpdateBudget(this.value)">
                <div class="flex justify-between mt-1.5">
                    <span class="text-[10px] text-slate-400 font-bold">Pas de limite</span>
                    <span id="nr-budget-lbl" class="text-xs font-black text-brand-600">5 000 €</span>
                </div>
            </div>
            <div class="flex gap-3">
                <button onclick="nrGo(1)" class="flex-1 h-12 border border-slate-200 text-slate-600 font-black rounded-2xl text-sm hover:bg-slate-50 transition-all">← Retour</button>
                <button onclick="nrGo(3)" class="flex-1 h-12 bg-[#0E1648] hover:bg-brand-700 text-white font-black rounded-2xl text-sm uppercase tracking-widest transition-all active:scale-95">Continuer →</button>
            </div>
        </div>

        <!-- Step 3: Coordonnées -->
        <div id="nr-s3" class="p-6 space-y-4 hidden">
            <p class="text-xs text-slate-500 font-medium">Vos coordonnées pour être contacté par notre expert.</p>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Prénom</label>
                    <input type="text" id="nr-first" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
                        class="w-full h-11 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none text-sm font-bold text-slate-900 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Nom</label>
                    <input type="text" id="nr-last" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
                        class="w-full h-11 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none text-sm font-bold text-slate-900 transition-all">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Téléphone *</label>
                <input type="tel" id="nr-phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                    placeholder="06 00 00 00 00"
                    class="w-full h-11 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none text-sm font-bold text-slate-900 transition-all">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Code postal</label>
                    <input type="text" id="nr-zip" value="<?= htmlspecialchars($user['zip'] ?? '') ?>"
                        placeholder="75000"
                        class="w-full h-11 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none text-sm font-bold text-slate-900 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Ville</label>
                    <input type="text" id="nr-city" value="<?= htmlspecialchars($user['city'] ?? '') ?>"
                        placeholder="Paris"
                        class="w-full h-11 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none text-sm font-bold text-slate-900 transition-all">
                </div>
            </div>
            <!-- Recap badge -->
            <div class="rounded-2xl bg-brand-50 border border-brand-100 p-3 flex items-center gap-3">
                <span id="nr-recap-icon" class="text-2xl"></span>
                <div>
                    <p class="text-[9px] font-black text-brand-600 uppercase tracking-widest">Récapitulatif</p>
                    <p id="nr-recap-text" class="text-xs font-bold text-slate-700"></p>
                </div>
            </div>
            <div class="flex gap-3">
                <button onclick="nrGo(2)" class="flex-1 h-12 border border-slate-200 text-slate-600 font-black rounded-2xl text-sm hover:bg-slate-50 transition-all">← Retour</button>
                <button onclick="submitNr()" id="nr-btn"
                    class="flex-1 h-12 bg-emerald-500 hover:bg-emerald-600 text-white font-black rounded-2xl text-sm uppercase tracking-widest transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="send" style="width:15px;height:15px;"></i> Envoyer
                </button>
            </div>
        </div>

        <!-- Success -->
        <div id="nr-success" class="hidden p-8 text-center">
            <div class="w-20 h-20 rounded-3xl bg-emerald-50 border-4 border-emerald-200 flex items-center justify-center mx-auto mb-5">
                <i data-lucide="check-circle" class="text-emerald-500" style="width:36px;height:36px;"></i>
            </div>
            <h3 class="text-xl font-display font-black text-slate-900 mb-2">Demande envoyée !</h3>
            <p class="text-sm text-slate-500 font-medium mb-6">Un expert vous contactera sous <strong class="text-slate-900">24h ouvrées</strong>.</p>
            <button onclick="closeNewModal(); load();" class="w-full h-12 bg-[#0E1648] text-white font-black rounded-2xl text-sm hover:bg-brand-700 transition-all">
                Voir mes projets
            </button>
        </div>

    </div>
</div>

<script>
const TOKEN = '<?= addslashes($token) ?>';
const USER  = <?= json_encode($user) ?>;
let allReqs = [], activeFilter = 'all', selectedId = null;


function init() {
    try {
        if (typeof Auth !== 'undefined' && TOKEN) {
            Auth.setToken(TOKEN);
        }
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        load();
    } catch(err) {
        console.error('INIT ERROR:', err);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

async function load() {
    try {
        if (typeof apiFetch === 'undefined') {
            throw new Error('apiFetch is not defined! Check app.js loading.');
        }
        allReqs = await apiFetch('/leads') || [];
        render(allReqs);
    } catch(e) { 
        console.error('Load Error:', e);
        document.getElementById('req-list').innerHTML = `
            <div class="bg-red-50 border border-red-100 p-6 rounded-2xl text-center">
                <p class="text-red-600 font-bold mb-1">Erreur de chargement</p>
                <p class="text-[10px] text-red-500 mb-3">${e.message}</p>
                <button onclick="load()" class="text-xs font-black text-brand-950 underline">Réessayer</button>
            </div>
        `;
    }
}

function setFilter(f, btn) {
    activeFilter = f;
    document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const map = {
        all: allReqs,
        active: allReqs.filter(r => !['completed','closed','cancelled'].includes((r.status||'').toLowerCase())),
        done: allReqs.filter(r => ['completed','closed'].includes((r.status||'').toLowerCase())),
        cancelled: allReqs.filter(r => (r.status||'').toLowerCase() === 'cancelled'),
    };
    render(map[f] || allReqs);
}

function render(list) {
    const c = document.getElementById('req-list');
    if (!c) return;
    
    if (!Array.isArray(list) || list.length === 0) {
        c.innerHTML = `
            <div class="bg-white rounded-[2.5rem] border border-dashed border-slate-200 p-16 text-center animate-in">
                <div class="w-20 h-20 rounded-3xl bg-slate-50 flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="layers" class="text-slate-300" style="width:32px;height:32px;"></i>
                </div>
                <p class="font-black text-slate-900 text-lg mb-2">Aucune demande trouvée</p>
                <p class="text-sm text-slate-500 font-medium mb-8">Vous n'avez pas encore de projet actif dans cette catégorie.</p>
                <button onclick="openNewModal()" class="inline-flex items-center gap-3 h-12 px-8 bg-[#0E1648] text-white text-sm font-black rounded-2xl hover:bg-brand-700 transition-all">
                    + Créer une demande
                </button>
            </div>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();
        return;
    }

    const steps = [
        { label: 'REÇU',    icon: 'check-circle' },
        { label: 'ASSIGNÉ', icon: 'user-check' },
        { label: 'DEVIS',   icon: 'file-text' },
        { label: 'RÉALISÉ', icon: 'shield-check' }
    ];

    c.innerHTML = list.map((r, i) => {
        const d = new Date(r.created_at).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
        const step = getStep(r.status);
        const sCls = statusCls(r.status), sLbl = statusLabel(r.status);

        return `
        <div class="stacked-card p-6 sm:p-8 animate-in" style="animation-delay:${i * 80}ms">
            <!-- Top info -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <span class="pill bg-indigo-50 text-indigo-700 border-indigo-100">${escapeHtml(r.sector || 'Expertise')}</span>
                    <span class="pill ${sCls}">${sLbl}</span>
                </div>
                <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest">${d}</span>
            </div>

            <!-- Content -->
            <div class="mb-8">
                <h2 class="text-2xl font-black text-slate-900 mb-2">${escapeHtml(r.need || r.sector || 'Projet')}</h2>
                ${r.address ? `<p class="flex items-center gap-2 text-sm text-slate-400 font-medium"><i data-lucide="map-pin" style="width:14px;height:14px;"></i> ${escapeHtml(r.address)}${r.city ? ' — ' + escapeHtml(r.city) : ''}</p>` : ''}
            </div>

            <!-- Stepper -->
            <div class="relative pt-4 pb-2">
                <div class="stepper-line"></div>
                <div class="stepper-progress" style="width: ${Math.max(0, (step.current - 1) * 33.33)}%"></div>
                
                <div class="relative flex justify-between">
                    ${steps.map((s, idx) => {
                        const state = (idx + 1) < step.current ? 'done' : ((idx + 1) === step.current ? 'active' : 'todo');
                        return `
                        <div class="flex flex-col items-center gap-3 w-20">
                            <div class="step-circle ${state}">
                                <i data-lucide="${state === 'done' ? 'check' : s.icon}" style="width:${state === 'active' ? 18 : 14}px;height:${state === 'active' ? 18 : 14}px;"></i>
                            </div>
                            <span class="text-[9px] font-black tracking-widest ${state === 'todo' ? 'text-slate-300' : 'text-slate-900'}">${s.label}</span>
                        </div>`;
                    }).join('')}
                </div>
            </div>
        </div>`;
    }).join('');

    if (typeof lucide !== 'undefined') lucide.createIcons();
}

/* Redundant logic removal */
function showDetail() {}
function buildDetailHtml() {}
function buildTimeline() {}
function closeDrawer() {}

/* ===== New Request Modal (multi-step) ===== */
let nrStep = 1, nrSector = '', nrSectorID = '', nrSectorIcon = '';
const NR_TITLES = ['Votre secteur', 'Votre besoin', 'Vos coordonnées'];

function openNewModal() {
    const ov = document.getElementById('nr-overlay');
    ov.classList.add('open');
    nrGoStep(1, false);
    document.getElementById('nr-error').classList.add('hidden');
    document.getElementById('nr-success').classList.add('hidden');
    document.getElementById('nr-s1').classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}
function closeNewModal() {
    document.getElementById('nr-overlay').classList.remove('open');
    nrStep = 1;
}
document.getElementById('nr-overlay').addEventListener('click', e => { if (e.target===document.getElementById('nr-overlay')) closeNewModal(); });

function nrGoStep(s, animate=true) {
    // Hide all steps
    [1,2,3].forEach(n => {
        document.getElementById(`nr-s${n}`).classList.add('hidden');
        // Dots
        const dot = document.getElementById(`nr-dot-${n}`);
        if (n < s)       { dot.className='sdot done'; dot.innerHTML='<i data-lucide="check" style="width:12px;height:12px;"></i>'; }
        else if (n === s){ dot.className='sdot active'; dot.textContent=n; }
        else             { dot.className='sdot todo';  dot.textContent=n; }
    });
    const el = document.getElementById(`nr-s${s}`);
    el.classList.remove('hidden');
    if (animate) { el.classList.remove('step-in','step-back'); void el.offsetWidth; el.classList.add('step-in'); }
    document.getElementById('nr-title').textContent = NR_TITLES[s - 1];
    document.getElementById('nr-error').classList.add('hidden');
    nrStep = s;
    if (s === 3) nrBuildRecap();
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function nrGo(s) {
    // Validate before advancing
    if (s > nrStep) {
        if (nrStep === 1 && !nrSector) { nrShowErr('Veuillez choisir un secteur.'); return; }
        if (nrStep === 2 && !document.getElementById('nr-need').value.trim()) { nrShowErr('Veuillez décrire votre besoin.'); return; }
    }
    nrGoStep(s);
}

function nrSelSector(btn) {
    document.querySelectorAll('.sec-card').forEach(b => b.classList.remove('sel'));
    btn.classList.add('sel');
    nrSector = btn.querySelector('.sec-lbl').textContent;
    nrSectorID = btn.dataset.val;
    nrSectorIcon = btn.dataset.icon;
}

function nrUpdateBudget(v) {
    const n = parseInt(v);
    document.getElementById('nr-budget-lbl').textContent = n === 0 ? 'Pas de limite' : n.toLocaleString('fr-FR') + ' €';
}

function nrBuildRecap() {
    const need = document.getElementById('nr-need').value.trim();
    const bv   = parseInt(document.getElementById('nr-budget').value);
    const recapIcon = document.getElementById('nr-recap-icon');
    recapIcon.innerHTML = `<i data-lucide="${nrSectorIcon || 'file-text'}" style="width:24px;height:24px;"></i>`;
    document.getElementById('nr-recap-text').textContent =
        `${nrSector||'—'} · Budget ${bv===0?'libre':bv.toLocaleString('fr-FR')+' €'} · ${need.slice(0,40)}${need.length>40?'…':''}`;
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function nrShowErr(msg) {
    const el = document.getElementById('nr-error');
    document.getElementById('nr-error-text').textContent = msg;
    el.classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

async function submitNr() {
    const phone = document.getElementById('nr-phone').value.trim();
    const need  = document.getElementById('nr-need').value.trim();
    if (!phone) { nrShowErr('Téléphone requis.'); return; }
    const btn = document.getElementById('nr-btn');
    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span style="width:18px;height:18px;border:2px solid rgba(255,255,255,.3);border-top-color:white;border-radius:50%;display:inline-block;animation:spin 1s linear infinite;"></span>';
    try {
        await apiFetch('/leads', { method:'POST', body: JSON.stringify({
            first_name: document.getElementById('nr-first').value.trim() || USER.first_name||'',
            last_name:  document.getElementById('nr-last').value.trim()  || USER.last_name||'',
            phone, email: USER.email||'', service_type: nrSectorID, need,
            budget: parseInt(document.getElementById('nr-budget').value)||0,
            zip_code: document.getElementById('nr-zip').value.trim(),
            city: document.getElementById('nr-city').value.trim(),
        })});
        // Success screen
        [1,2,3].forEach(n => document.getElementById(`nr-s${n}`).classList.add('hidden'));
        document.getElementById('nr-success').classList.remove('hidden');
        document.getElementById('nr-title').textContent = 'Envoyé !';
        if (typeof lucide !== 'undefined') lucide.createIcons();
    } catch(e) {
        nrShowErr(e.message||"Erreur lors de l'envoi.");
        btn.disabled = false;
        btn.innerHTML = orig;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
}

/* Utilities */
function escapeHtml(v) {
    if(!v) return '';
    return String(v).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#039;"}[m]));
}

function getStep(s) {
    const v=(s||'').toLowerCase();
    if(v==='completed'||v==='closed') return{current:4,percent:100};
    if(v==='quote_sent'||v==='confirmé'||v==='signe')  return{current:3,percent:66};
    if(v==='assigned'||v==='assigne'||v==='processed') return{current:2,percent:33};
    return{current:1,percent:5};
}
function statusLabel(s){return({pending:'En attente',assigned:'Assigné',assigne:'Assigné',processed:'En traitement',quote_sent:'Devis envoyé',confirmé:'Devis validé',closed:'Terminé',completed:'Terminé',cancelled:'Annulé'})[(s||'').toLowerCase()]||s||'—';}
function statusCls(s){const m=(s||'').toLowerCase();if(m==='closed'||m==='completed')return'bg-emerald-50 text-emerald-600 border-emerald-100';if(m==='cancelled')return'bg-red-50 text-red-500 border-red-100';if(m==='confirmé'||m==='signe'||m==='quote_sent')return'bg-brand-50 text-brand-600 border-brand-100';if(m==='processed'||m==='assigned'||m==='assigne')return'bg-amber-50 text-amber-600 border-amber-100';return'bg-slate-50 text-slate-400 border-slate-200';}
</script>
<!-- Bottom Navigation -->
<nav class="bottom-nav">
    <a href="/rappel/public/client/dashboard.php" class="bnav-btn">
        <i data-lucide="home" style="width:24px;height:24px;"></i>
        <span>Accueil</span>
    </a>
    <a href="/rappel/public/client/mes-demandes.php" class="bnav-btn active">
        <i data-lucide="list" style="width:24px;height:24px;"></i>
        <span>Demandes</span>
    </a>
    <div class="flex-1 relative flex justify-center">
        <button onclick="openNewModal()" class="absolute -top-10 w-16 h-16 bg-[#0E1648] rounded-3xl flex items-center justify-center shadow-2xl shadow-brand-900/40 active:scale-90 transition-all group">
            <i data-lucide="plus" class="text-white group-hover:scale-125 transition-transform" style="width:32px;height:32px;"></i>
        </button>
        <span class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mt-8">Nouveau</span>
    </div>
    <a href="/rappel/public/client/settings.php" class="bnav-btn">
        <i data-lucide="settings" style="width:24px;height:24px;"></i>
        <span>Profil</span>
    </a>
    <a href="/rappel/public/logout.php" class="bnav-btn">
        <i data-lucide="log-out" style="width:24px;height:24px;"></i>
        <span>Quitter</span>
    </a>
</nav>


<?php include __DIR__ . '/../includes/cookie_banner.php'; ?>
</body>
</html>
