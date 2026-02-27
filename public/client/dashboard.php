<?php
require_once __DIR__ . '/../includes/auth.php';
requireAuth(false);
$pageTitle = 'Mon Espace';
$user = getCurrentUser();
$token = getToken();
if (($user['role'] ?? '') === 'provider') { header('Location: /rappel/public/pro/dashboard.php'); exit; }
if (($user['role'] ?? '') === 'admin')    { header('Location: /rappel/public/admin/dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include __DIR__ . '/../includes/head.php'; ?>
    <style>


    /* Modal */
    #modal-overlay {
        position: fixed; inset: 0; background: rgba(14,22,72,.5); backdrop-filter: blur(6px);
        z-index: 999; display: none; align-items: center; justify-content: center; padding: 1rem;
    }
    #modal-overlay.open { display: flex; animation: fadeIn .2s ease; }
    #modal-box {
        background: #fff; border-radius: 2rem; width: 100%; max-width: 540px;
        max-height: 90vh; overflow-y: auto; box-shadow: 0 40px 80px rgba(14,22,72,.2);
        animation: slideUp .3s cubic-bezier(.16,1,.3,1);
    }
    @keyframes slideUp { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }
    @keyframes fadeIn  { from { opacity:0; } to { opacity:1; } }
    @keyframes spin    { to { transform: rotate(360deg); } }

    /* Steps in modal */
    .step-dot { width:2rem; height:2rem; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:900; font-size:.7rem; transition:all .3s; }
    .step-dot.done  { background:#0E1648; color:#fff; }
    .step-dot.active{ background:#7CCB63; color:#fff; box-shadow:0 0 0 4px rgba(124,203,99,.2); }
    .step-dot.todo  { background:#e2e8f0; color:#94a3b8; }

    /* Sector grid */
    .sector-btn { background:#f8fafc; border-radius:2rem; padding:1.5rem .75rem; cursor:pointer; text-align:center; transition:all .3s cubic-bezier(.16,1,.3,1); display:flex; flex-direction:column; align-items:center; gap:.75rem; border:2px solid transparent; }
    .sector-btn:hover { background:#fff; box-shadow:0 12px 24px rgba(14,22,72,0.08); transform:translateY(-2px); }
    .sector-btn.selected { background:#fff; border-color:#0E1648; box-shadow:0 0 0 1px #0E1648; }
    .s-icon-wrap { width:3.5rem; height:3.5rem; border-radius:1.25rem; display:flex; align-items:center; justify-content:center; transition:all .3s; }
    .s-label { font-size:.85rem; font-weight:800; color:#0E1648; letter-spacing:-0.01em; }
    .sector-btn.selected .s-label { color:#0E1648; }

    /* Range input */
    input[type=range] { -webkit-appearance:none; width:100%; height:6px; border-radius:3px; background:#e2e8f0; outline:none; }
    input[type=range]::-webkit-slider-thumb { -webkit-appearance:none; width:20px; height:20px; border-radius:50%; background:#0E1648; cursor:pointer; box-shadow:0 2px 8px rgba(14,22,72,.3); }

    /* Stepper track */
    .track-fill { transition: width .7s cubic-bezier(.16,1,.3,1); }

    /* Card hover */
    .req-card { transition: all .3s ease; }
    .req-card:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(14,22,72,.1); }

    /* Toast */
    .toast-item { animation: slideUp .3s ease; }



    /* Status pill */
    .pill { display:inline-flex; align-items:center; gap:.3rem; padding:.2rem .7rem; border-radius:9999px; font-size:.6rem; font-weight:900; text-transform:uppercase; letter-spacing:.08em; border:1px solid; }

    /* Skeleton loader */
    .skeleton { background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%); background-size:200%; animation: shimmer 1.5s infinite; border-radius:.75rem; }
    @keyframes shimmer { 0%{background-position:200%} 100%{background-position:-200%} }
    /* Bottom nav */
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

    /* Welcome Banner Glass pattern */
    .glass-pattern {
        background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.05) 1px, transparent 0);
        background-size: 24px 24px;
    }

    body { padding-bottom: 90px; }
    </style>
</head>
<body class="min-h-screen">

<!-- ===== Top Bar ===== -->
<header class="sticky top-0 z-50 bg-white/82 backdrop-blur-xl border-b border-navy-100/50 h-20 flex items-center justify-between px-4 sm:px-8 shadow-sm">
    <a href="/rappel/public/" class="flex items-center">
        <img src="/rappel/public/assets/img/logo.png" alt="Rappelez-moi" class="h-10 w-auto object-contain">
    </a>
    <div class="flex items-center gap-3">
        <button onclick="openModal()" title="Nouvelle demande"
                class="h-12 w-12 sm:w-auto sm:px-6 rounded-2xl bg-[#0E1648] text-white text-sm font-black flex items-center justify-center gap-2 hover:bg-brand-700 transition-all active:scale-95 shadow-lg shadow-brand-900/20">
            <i data-lucide="plus" style="width:24px;height:24px;"></i>
            <span class="hidden sm:inline">Nouvelle demande</span>
        </button>
        <a href="/rappel/public/client/settings.php"
           class="w-12 h-12 rounded-2xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-all" title="Paramètres">
            <i data-lucide="settings" style="width:24px;height:24px;"></i>
        </a>
        <a href="/rappel/public/logout.php"
           class="w-12 h-12 rounded-2xl bg-slate-100 hover:bg-red-50 hover:text-red-500 flex items-center justify-center text-slate-500 transition-all" title="Déconnexion">
            <i data-lucide="log-out" style="width:24px;height:24px;"></i>
        </a>
    </div>
</header>

<!-- ===== Content ===== -->
<div class="max-w-4xl mx-auto px-4 py-8 space-y-8 pb-24">

    <!-- Welcome Banner -->
    <div class="relative rounded-[2.5rem] bg-[#0E1648] p-8 sm:p-10 overflow-hidden shadow-2xl">
        <div class="absolute inset-0 pointer-events-none glass-pattern opacity-40"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-brand-400/10 to-transparent pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 backdrop-blur-md mb-4 group cursor-default">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <p class="text-white/80 text-[10px] font-black uppercase tracking-widest">Connecté en tant que Client</p>
                </div>
                <h1 class="text-3xl sm:text-4xl font-display font-black text-white leading-tight">
                    Bonjour,<br>
                    <span class="text-emerald-400"><?= htmlspecialchars($user['company_name'] ?? ($user['first_name'] ?? 'Compte')) ?></span>
                </h1>
                <p class="text-white/50 text-sm font-medium mt-3 flex items-center gap-2">
                    <i data-lucide="mail" style="width:14px;height:14px;"></i>
                    <?= htmlspecialchars($user['email'] ?? '') ?>
                </p>
            </div>
            <button onclick="openModal()"
                    class="flex-shrink-0 flex items-center justify-center gap-3 bg-white text-[#0E1648] font-black px-8 py-4 rounded-[1.5rem] hover:bg-emerald-400 hover:text-white transition-all text-sm shadow-xl active:scale-95 group">
                <i data-lucide="plus-circle" class="group-hover:rotate-90 transition-transform duration-300" style="width:24px;height:24px;"></i>
                Nouvelle demande
            </button>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-3 gap-3 sm:gap-4">
        <?php foreach([
            ['id'=>'kpi-active', 'icon'=>'clock',        'label'=>'En cours',    'clr'=>'#3B82F6'],
            ['id'=>'kpi-quotes', 'icon'=>'file-text',    'label'=>'Devis reçus', 'clr'=>'#F59E0B'],
            ['id'=>'kpi-done',   'icon'=>'check-circle', 'label'=>'Réalisés',    'clr'=>'#10B981'],
        ] as $k): ?>
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-100 text-center group hover:shadow-md transition-all">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto mb-3 transition-transform group-hover:scale-110"
                 style="background:<?= $k['clr'] ?>18;">
                <i data-lucide="<?= $k['icon'] ?>" style="width:22px;height:22px;color:<?= $k['clr'] ?>;margin:auto;"></i>
            </div>
            <p id="<?= $k['id'] ?>" class="text-3xl sm:text-4xl font-black text-slate-900 skeleton w-10 h-9 mx-auto mb-1 rounded-lg"></p>
            <p class="text-[11px] sm:text-xs font-black text-slate-400 uppercase tracking-widest"><?= $k['label'] ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Pending Quotes -->
    <div id="quotes-section" class="hidden">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-amber-400 rounded-full animate-pulse"></div>
                <h2 class="text-xs font-black text-slate-900 uppercase tracking-widest">Devis en attente</h2>
            </div>
            <span class="text-[10px] font-black text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-100 flex items-center gap-1">
                <i data-lucide="alert-circle" style="width:11px;height:11px;"></i> Action requise
            </span>
        </div>
        <div id="quotes-container" class="space-y-4"></div>
    </div>

    <!-- Requests Tracking -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-brand-600 rounded-full"></div>
                <h2 class="text-xs font-black text-slate-900 uppercase tracking-widest">Suivi de mes demandes</h2>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="filterRequests('all')" data-filter="all"
                        class="filter-btn text-[11px] font-black px-4 py-2 rounded-full border border-brand-500 bg-brand-500 text-white transition-all">Tout</button>
                <button onclick="filterRequests('active')" data-filter="active"
                        class="filter-btn text-[11px] font-black px-4 py-2 rounded-full border border-slate-200 text-slate-500 hover:border-brand-400 hover:text-brand-600 transition-all">En cours</button>
                <button onclick="filterRequests('done')" data-filter="done"
                        class="filter-btn text-[11px] font-black px-4 py-2 rounded-full border border-slate-200 text-slate-500 hover:border-brand-400 hover:text-brand-600 transition-all">Terminés</button>
            </div>
        </div>
        <div id="requests-container" class="space-y-4">
            <!-- Skeleton loaders -->
            <div class="skeleton h-32 rounded-3xl"></div>
            <div class="skeleton h-32 rounded-3xl" style="opacity:.7"></div>
        </div>
    </div>
</div>

<!-- ============================
     NEW REQUEST MODAL (Multi-step)
     ============================ -->
<div id="modal-overlay" onclick="handleOverlayClick(event)">
    <div id="modal-box">
        <!-- Header -->
        <div class="p-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white rounded-t-[2rem] z-10">
            <div>
                <h2 class="font-display font-black text-slate-900 text-lg">Nouvelle demande</h2>
                <p class="text-xs text-slate-400 font-medium mt-0.5" id="modal-subtitle">Décrivez votre projet en quelques étapes</p>
            </div>
            <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500">
                <i data-lucide="x" style="width:16px;height:16px;"></i>
            </button>
        </div>

        <!-- Step Indicator -->
        <div class="px-6 pt-4 pb-2 flex items-center gap-2">
            <?php foreach([1,2,3] as $s): ?>
            <div class="flex items-center gap-2 flex-1">
                <div class="step-dot <?= $s===1?'active':'todo' ?>" id="step-dot-<?= $s ?>"><?= $s ?></div>
                <?php if($s<3): ?><div class="flex-1 h-0.5 bg-slate-100 rounded-full" id="step-line-<?= $s ?>"></div><?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="flex justify-between px-6 mb-4">
            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Besoin</span>
            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Coordonnées</span>
            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Détails</span>
        </div>

        <div id="modal-error" class="mx-6 mb-4 hidden p-3 bg-red-50 border border-red-100 rounded-xl text-xs font-bold text-red-600 flex items-center gap-2">
            <i data-lucide="alert-circle" style="width:14px;height:14px;"></i>
            <span id="modal-error-text"></span>
        </div>

        <!-- STEP 1: Besoin -->
        <div id="modal-step-1" class="px-6 pb-6 space-y-5">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Secteur d'activité *</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4" id="sector-grid">
                    <?php foreach([
                        ['val'=>'assurance',  'icon'=>'shield',      'label'=>'Assurance', 'bg'=>'#EEF2FF', 'clr'=>'#4F46E5'],
                        ['val'=>'renovation', 'icon'=>'hammer',      'label'=>'Rénovation', 'bg'=>'#FFF7ED', 'clr'=>'#EA580C'],
                        ['val'=>'energie',    'icon'=>'zap',         'label'=>'Énergie',    'bg'=>'#FEFCE8', 'clr'=>'#CA8A04'],
                        ['val'=>'finance',    'icon'=>'trending-up', 'label'=>'Finance',    'bg'=>'#F0FDF4', 'clr'=>'#16A34A'],
                        ['val'=>'garage',     'icon'=>'car',         'label'=>'Garage',     'bg'=>'#F0F9FF', 'clr'=>'#0284C7'],
                        ['val'=>'telecom',    'icon'=>'smartphone',  'label'=>'Télécoms',   'bg'=>'#FAF5FF', 'clr'=>'#9333EA'],
                    ] as $s): ?>
                    <button type="button" class="sector-btn" data-value="<?= $s['val'] ?>" onclick="selectSector(this)">
                        <div class="s-icon-wrap" style="background:<?= $s['bg'] ?>;">
                            <i data-lucide="<?= $s['icon'] ?>" style="width:24px;height:24px;color:<?= $s['clr'] ?>;"></i>
                        </div>
                        <span class="s-label"><?= $s['label'] ?></span>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Description du besoin *</label>
                <textarea id="m-need" rows="3" placeholder="Décrivez votre projet en quelques mots..."
                          class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none font-medium text-slate-900 text-sm resize-none transition-all"></textarea>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Budget estimé</label>
                <input type="range" id="m-budget" min="0" max="50000" step="500" value="5000" oninput="updateBudget(this.value)">
                <div class="flex justify-between mt-1.5">
                    <span class="text-[10px] font-black text-slate-400">Pas de limite</span>
                    <span id="budget-display" class="text-xs font-black text-brand-600">5 000 €</span>
                </div>
            </div>
            <button onclick="goStep(2)" class="w-full h-12 bg-[#0E1648] text-white font-black rounded-xl text-sm uppercase tracking-widest hover:bg-brand-700 transition-all active:scale-95">
                Continuer <i data-lucide="arrow-right" style="width:16px;height:16px;display:inline;vertical-align:middle;"></i>
            </button>
        </div>

        <!-- STEP 2: Coordonnées -->
        <div id="modal-step-2" class="px-6 pb-6 space-y-4 hidden">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Prénom *</label>
                    <input type="text" id="m-firstname" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
                           class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none font-bold text-slate-900 text-sm transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Nom *</label>
                    <input type="text" id="m-lastname" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
                           class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none font-bold text-slate-900 text-sm transition-all">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Téléphone *</label>
                <input type="tel" id="m-phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                       placeholder="06 00 00 00 00"
                       class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none font-bold text-slate-900 text-sm transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Email</label>
                <input type="email" id="m-email" value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                       class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-100 text-slate-500 outline-none font-bold text-sm cursor-not-allowed" disabled>
            </div>
            <div class="flex gap-3 pt-2">
                <button onclick="goStep(1)" class="flex-1 h-12 border border-slate-200 text-slate-600 font-black rounded-xl text-sm hover:bg-slate-50 transition-all">
                    ← Retour
                </button>
                <button onclick="goStep(3)" class="flex-2 flex-1 h-12 bg-[#0E1648] text-white font-black rounded-xl text-sm uppercase tracking-widest hover:bg-brand-700 transition-all active:scale-95">
                    Continuer →
                </button>
            </div>
        </div>

        <!-- STEP 3: Détails + Submit -->
        <div id="modal-step-3" class="px-6 pb-6 space-y-4 hidden">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Adresse d'intervention</label>
                <input type="text" id="m-address" value="<?= htmlspecialchars($user['address'] ?? '') ?>"
                       placeholder="12 rue de la Paix"
                       class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none font-bold text-slate-900 text-sm transition-all">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Code postal</label>
                    <input type="text" id="m-zip" value="<?= htmlspecialchars($user['zip'] ?? '') ?>"
                           placeholder="75000"
                           class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none font-bold text-slate-900 text-sm transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Ville</label>
                    <input type="text" id="m-city" value="<?= htmlspecialchars($user['city'] ?? '') ?>"
                           placeholder="Paris"
                           class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none font-bold text-slate-900 text-sm transition-all">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Disponibilité souhaitée</label>
                <div class="grid grid-cols-3 gap-2" id="slot-grid">
                    <?php foreach(['Dès que possible','Cette semaine','Ce mois-ci'] as $slot): ?>
                    <button type="button" class="sector-btn text-center" data-value="<?= $slot ?>" onclick="selectSlot(this)">
                        <?= $slot ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Summary Box -->
            <div class="bg-brand-50 border border-brand-100 rounded-2xl p-4 space-y-2">
                <p class="text-[10px] font-black text-brand-600 uppercase tracking-widest mb-2">Récapitulatif</p>
                <div class="flex flex-wrap gap-2 text-xs text-slate-700 font-medium">
                    <span id="recap-sector" class="px-2 py-0.5 bg-white rounded-lg border border-brand-100 font-bold"></span>
                    <span id="recap-budget" class="px-2 py-0.5 bg-white rounded-lg border border-brand-100 font-bold"></span>
                </div>
                <p id="recap-need" class="text-xs text-slate-600 italic mt-1"></p>
            </div>

            <!-- Consent -->
            <label class="flex items-start gap-3 cursor-pointer group">
                <input type="checkbox" id="m-consent" class="mt-0.5 w-4 h-4 rounded border-slate-300 text-brand-600 cursor-pointer flex-shrink-0">
                <span class="text-xs text-slate-500 font-medium leading-relaxed group-hover:text-slate-700 transition-colors">
                    J'accepte d'être contacté par un expert partenaire conformément à la
                    <a href="/rappel/public/legal.php" class="text-brand-600 font-bold hover:underline" target="_blank">politique de confidentialité</a>.
                </span>
            </label>

            <div class="flex gap-3 pt-2">
                <button onclick="goStep(2)" class="flex-1 h-12 border border-slate-200 text-slate-600 font-black rounded-xl text-sm hover:bg-slate-50 transition-all">
                    ← Retour
                </button>
                <button onclick="submitRequest()" id="submit-btn"
                        class="flex-1 h-12 bg-accent-500 hover:bg-accent-600 text-white font-black rounded-xl text-sm uppercase tracking-widest transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="send" style="width:15px;height:15px;"></i>
                    Envoyer
                </button>
            </div>
        </div>

        <!-- SUCCESS STATE -->
        <div id="modal-success" class="hidden px-6 pb-8 text-center">
            <div class="w-20 h-20 rounded-full bg-emerald-50 border-4 border-emerald-200 flex items-center justify-center mx-auto mb-5">
                <i data-lucide="check-circle" class="text-emerald-500" style="width:36px;height:36px;"></i>
            </div>
            <h3 class="text-xl font-display font-black text-slate-900 mb-2">Demande envoyée !</h3>
            <p class="text-sm text-slate-500 font-medium mb-6">Un expert vous contactera sous <strong class="text-slate-900">24h</strong>. Vous recevrez une notification dès l'attribution.</p>
            <button onclick="closeModal(); loadData();"
                    class="w-full h-12 bg-[#0E1648] text-white font-black rounded-xl text-sm hover:bg-brand-700 transition-all">
                Voir mes projets
            </button>
        </div>
    </div>
</div>

<script>
const TOKEN  = '<?= addslashes($token) ?>';
const FULL_NAME = '<?= addslashes(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?>';
let allRequests = [];
let selectedSectorID = '', selectedSectorText = '', selectedSlot = '', currentStep = 1;

/* ===== INIT ===== */
document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') lucide.createIcons();
    if (TOKEN) Auth.setToken(TOKEN);
    loadData();

    // Check for openModal param
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('openModal')) {
        setTimeout(openModal, 400);
        // Clear param without reload
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    // Select default slot
    const firstSlot = document.querySelector('#slot-grid .sector-btn');
    if (firstSlot) selectSlot(firstSlot);
});

async function loadData() {
    try {
        const [stats, requests, quotes] = await Promise.all([
            apiFetch('/client/stats'),
            apiFetch('/leads'),
            apiFetch('/client/quotes'),
        ]);
        renderKPIs(stats);
        allRequests = requests || [];
        renderRequests(allRequests);
        renderQuotes(quotes);
    } catch(e) { console.error(e); }
}

/* ===== KPIs ===== */
function renderKPIs(s) {
    ['kpi-active','kpi-quotes','kpi-done'].forEach(id => {
        const el = document.getElementById(id);
        el.classList.remove('skeleton','w-10','h-8');
    });
    document.getElementById('kpi-active').textContent = s.active_requests ?? 0;
    document.getElementById('kpi-quotes').textContent = s.pending_quotes ?? 0;
    document.getElementById('kpi-done').textContent   = s.completed_interventions ?? 0;
}

/* ===== FILTER ===== */
function filterRequests(type) {
    document.querySelectorAll('.filter-btn').forEach(b => {
        const active = b.dataset.filter === type;
        b.classList.toggle('bg-brand-500', active);
        b.classList.toggle('border-brand-500', active);
        b.classList.toggle('text-white', active);
        b.classList.toggle('border-slate-200', !active);
        b.classList.toggle('text-slate-500', !active);
    });
    if (type === 'active') {
        renderRequests(allRequests.filter(r => !['completed','closed','cancelled'].includes((r.status||'').toLowerCase())));
    } else if (type === 'done') {
        renderRequests(allRequests.filter(r => ['completed','closed'].includes((r.status||'').toLowerCase())));
    } else {
        renderRequests(allRequests);
    }
}

/* ===== QUOTES ===== */
function renderQuotes(quotes) {
    const section   = document.getElementById('quotes-section');
    const container = document.getElementById('quotes-container');
    const pending   = (quotes || []).filter(q => q.status === 'attente_client' || q.status === 'draft');
    if (!pending.length) { section.classList.add('hidden'); return; }
    section.classList.remove('hidden');
    container.innerHTML = pending.map(q => `
        <div class="bg-white rounded-2xl border border-amber-100 shadow-sm overflow-hidden req-card">
            <div class="p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-[#0E1648] flex items-center justify-center text-white flex-shrink-0">
                        <i data-lucide="user-check" style="width:16px;height:16px;"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Expert partenaire</p>
                        <p class="font-black text-slate-900 text-sm">${escapeHtml(q.provider_company || q.provider_name || '—')}</p>
                    </div>
                    <span class="text-[9px] text-slate-400 font-bold">${new Date(q.created_at).toLocaleDateString('fr-FR')}</span>
                </div>
                <h3 class="font-black text-slate-900 mb-1">${escapeHtml(q.need || q.project_name || 'Projet')}</h3>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black text-slate-900">${parseFloat(q.amount||0).toLocaleString('fr-FR')}</span>
                    <span class="text-xs font-bold text-slate-400">€ HT</span>
                </div>
                ${q.description ? `<p class="text-xs text-slate-500 mt-2">${escapeHtml(q.description)}</p>` : ''}
            </div>
            <div class="flex border-t border-slate-100">
                <button onclick="doQuote('${q.id}','refuse')"
                        class="flex-1 py-3.5 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all">
                    Refuser
                </button>
                <div class="w-px bg-slate-100"></div>
                <button onclick="doQuote('${q.id}','accept')"
                        class="flex-1 py-3.5 text-[10px] font-black uppercase tracking-widest text-white bg-[#0E1648] hover:bg-brand-600 transition-all flex items-center justify-center gap-1.5">
                    <i data-lucide="check" style="width:12px;height:12px;"></i> Valider
                </button>
            </div>
        </div>
    `).join('');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

async function doQuote(id, action) {
    if (!confirm(action === 'accept' ? 'Valider ce devis ?' : 'Refuser ce devis ?')) return;
    try {
        await apiFetch(`/client/${action === 'accept' ? 'accept' : 'refuse'}-quote/${id}`, { method: 'PATCH' });
        showToast(action === 'accept' ? '✓ Devis validé — l\'expert est notifié' : 'Devis refusé', 'success');
        loadData();
    } catch(e) { showToast(e.message, 'error'); }
}

/* ===== REQUESTS ===== */
function renderRequests(reqs) {
    const c = document.getElementById('requests-container');
    if (!reqs || !reqs.length) {
        c.innerHTML = `
            <div class="bg-white rounded-3xl p-12 flex flex-col items-center text-center border border-dashed border-slate-200">
                <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center mb-5">
                    <i data-lucide="inbox" class="text-slate-300" style="width:28px;height:28px;"></i>
                </div>
                <h3 class="font-black text-slate-900 text-base mb-2">Aucune demande trouvée</h3>
                <p class="text-sm text-slate-500 mb-6 max-w-xs">Notre réseau d'experts certifiés est prêt à répondre à vos besoins B2B.</p>
                <a href="/rappel/public/client/mes-demandes.php" onclick="openModal()" class="inline-flex items-center gap-2 bg-[#0E1648] text-white font-black px-8 py-3 rounded-2xl text-sm hover:bg-brand-700 transition-all active:scale-95">
                    <i data-lucide="plus" style="width:15px;height:15px;"></i>
                    Soumettre un besoin
                </a>
            </div>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();
        return;
    }

    c.innerHTML = reqs.map(req => {
        const d = new Date(req.created_at).toLocaleDateString('fr-FR', {day:'numeric',month:'short',year:'numeric'});
        const step = getStep(req.status);
        const sCls = getStatusCls(req.status);
        const sLbl = getStatusLabel(req.status);
        const hasExpert = req.provider_name || req.provider_company;
        const steps = [
            {label:'Reçu',    icon:'check-circle'},
            {label:'Assigné', icon:'user-check'},
            {label:'Devis',   icon:'file-text'},
            {label:'Réalisé', icon:'shield-check'},
        ];

        return `
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden req-card">
            <!-- Header row -->
            <div class="p-5 pb-0 flex flex-wrap items-start gap-3 justify-between">
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="pill bg-brand-50 text-brand-600 border-brand-100">${escapeHtml(req.sector || 'Expertise')}</span>
                        <span class="pill ${sCls}">${sLbl}</span>
                    </div>
                    <h3 class="font-black text-slate-900 text-base leading-tight">${escapeHtml(req.need || req.sector || 'Projet')}</h3>
                    ${req.address ? `<p class="flex items-center gap-1 text-xs text-slate-400 font-medium mt-1"><i data-lucide="map-pin" style="width:11px;height:11px;"></i>${escapeHtml(req.address)}</p>` : ''}
                </div>
                <span class="text-[9px] text-slate-400 font-bold whitespace-nowrap">${d}</span>
            </div>

            <!-- Stepper -->
            <div class="p-5">
                <div class="relative flex justify-between items-start">
                    <!-- Track -->
                    <div class="absolute top-5 left-5 right-5 h-0.5 bg-slate-100 z-0"></div>
                    <div class="absolute top-5 left-5 h-0.5 bg-brand-500 z-0 track-fill"
                         style="width:calc(${step.percent}% - 2.5rem);"></div>
                    ${steps.map((s,i) => {
                        const active = step.current > i;
                        return `<div class="flex flex-col items-center gap-1.5 z-10 w-10">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all
                                        ${active ? 'bg-brand-600 text-white shadow-md shadow-brand-200/50' : 'bg-white border-2 border-slate-200 text-slate-300'}">
                                <i data-lucide="${s.icon}" style="width:15px;height:15px;"></i>
                            </div>
                            <span class="text-[8px] font-black uppercase tracking-widest text-center leading-tight ${active ? 'text-slate-700' : 'text-slate-300'}">${s.label}</span>
                        </div>`;
                    }).join('')}
                </div>
            </div>

            ${hasExpert ? `
            <!-- Expert card -->
            <div class="mx-5 mb-5 rounded-2xl bg-slate-50 border border-slate-100 p-4">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Expert partenaire attribué</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#0E1648] text-white flex items-center justify-center font-black text-base flex-shrink-0">
                        ${(req.provider_name || req.provider_company || '?')[0].toUpperCase()}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-black text-slate-900 text-sm">${escapeHtml(req.provider_company || req.provider_name)}</p>
                        ${req.provider_name && req.provider_company ? `<p class="text-xs text-slate-500">${escapeHtml(req.provider_name)}</p>` : ''}
                    </div>
                    <div class="flex gap-2">
                        ${req.provider_phone ? `<a href="tel:${escapeHtml(req.provider_phone)}"
                            class="w-9 h-9 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-600 hover:text-brand-600 hover:border-brand-300 transition-all" title="${escapeHtml(req.provider_phone)}">
                            <i data-lucide="phone" style="width:14px;height:14px;"></i></a>` : ''}
                        ${req.provider_email ? `<a href="mailto:${escapeHtml(req.provider_email)}"
                            class="w-9 h-9 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-600 hover:text-brand-600 hover:border-brand-300 transition-all" title="${escapeHtml(req.provider_email)}">
                            <i data-lucide="mail" style="width:14px;height:14px;"></i></a>` : ''}
                    </div>
                </div>
            </div>` : ''}
        </div>`;
    }).join('');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

/* ===== MODAL ===== */
function openModal() {
    document.getElementById('modal-overlay').classList.add('open');
    document.body.style.overflow = 'hidden';
    goStep(1);
    document.getElementById('modal-success').classList.add('hidden');
    document.getElementById('modal-error').classList.add('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}
function closeModal() {
    document.getElementById('modal-overlay').classList.remove('open');
    document.body.style.overflow = '';
}
function handleOverlayClick(e) {
    if (e.target === document.getElementById('modal-overlay')) closeModal();
}

function goStep(s) {
    currentStep = s;
    [1,2,3].forEach(n => {
        document.getElementById(`modal-step-${n}`).classList.toggle('hidden', n !== s);
        const dot = document.getElementById(`step-dot-${n}`);
        if (n < s)      dot.className = 'step-dot done';
        else if (n === s) dot.className = 'step-dot active';
        else             dot.className = 'step-dot todo';
    });

    const subtitles = ['Décrivez votre projet','Vos coordonnées','Détails & confirmation'];
    document.getElementById('modal-subtitle').textContent = subtitles[s-1];

    // Update recap on step3
    if (s === 3) updateRecap();
    document.getElementById('modal-error').classList.add('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

/* Sector */
function selectSector(btn) {
    document.querySelectorAll('#sector-grid .sector-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    selectedSectorID = btn.dataset.value;
    selectedSectorText = btn.querySelector('.s-label').textContent;
}
function selectSlot(btn) {
    document.querySelectorAll('#slot-grid .sector-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    selectedSlot = btn.dataset.value;
}

/* Budget range */
function updateBudget(val) {
    const v = parseInt(val);
    document.getElementById('budget-display').textContent = v === 0 ? 'Pas de limite' : v.toLocaleString('fr-FR') + ' €';
}

function updateRecap() {
    document.getElementById('recap-sector').textContent = selectedSectorText || '—';
    const bv = parseInt(document.getElementById('m-budget').value);
    document.getElementById('recap-budget').textContent = bv === 0 ? 'Budget libre' : bv.toLocaleString('fr-FR') + ' €';
    document.getElementById('recap-need').textContent   = document.getElementById('m-need').value || '(description non renseignée)';
}

function showModalError(msg) {
    const el = document.getElementById('modal-error');
    document.getElementById('modal-error-text').textContent = msg;
    el.classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

/* Validate steps */
function validateStep(s) {
    if (s === 1) {
        if (!selectedSectorID) { showModalError('Veuillez choisir un secteur.'); return false; }
        if (!document.getElementById('m-need').value.trim()) { showModalError('Veuillez décrire votre besoin.'); return false; }
    }
    if (s === 2) {
        if (!document.getElementById('m-firstname').value.trim()) { showModalError('Prénom requis.'); return false; }
        if (!document.getElementById('m-lastname').value.trim())  { showModalError('Nom requis.'); return false; }
        const ph = document.getElementById('m-phone').value.trim();
        if (!ph) { showModalError('Téléphone requis.'); return false; }
    }
    return true;
}
const origGoStep = goStep;
window.goStep = function(s) {
    if (s > currentStep && !validateStep(currentStep)) return;
    origGoStep(s);
};

/* Submit */
async function submitRequest() {
    if (!document.getElementById('m-consent').checked) {
        showModalError('Veuillez accepter d\'être contacté.');
        return;
    }
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<span style="width:18px;height:18px;border:2px solid rgba(255,255,255,.3);border-top-color:white;border-radius:50%;display:inline-block;animation:spin 1s linear infinite;"></span>';

    const payload = {
        first_name:   document.getElementById('m-firstname').value.trim(),
        last_name:    document.getElementById('m-lastname').value.trim(),
        phone:        document.getElementById('m-phone').value.trim(),
        email:        '<?= addslashes($user['email'] ?? '') ?>',
        service_type: selectedSectorID,
        need:         document.getElementById('m-need').value.trim(),
        budget:       parseInt(document.getElementById('m-budget').value),
        time_slot:    selectedSlot,
        address:      document.getElementById('m-address').value.trim(),
        zip_code:     document.getElementById('m-zip').value.trim(),
        city:         document.getElementById('m-city').value.trim(),
    };

    try {
        await apiFetch('/leads', { method: 'POST', body: JSON.stringify(payload) });
        // Show success
        [1,2,3].forEach(n => document.getElementById(`modal-step-${n}`).classList.add('hidden'));
        document.getElementById('modal-success').classList.remove('hidden');
        document.getElementById('modal-subtitle').textContent = 'Demande envoyée avec succès !';
    } catch(e) {
        showModalError(e.message || 'Erreur lors de l\'envoi.');
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="send" style="width:15px;height:15px;"></i> Envoyer';
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
}

/* ===== Helpers ===== */
function getStep(status) {
    const s = (status||'').toLowerCase();
    if (s==='completed'||s==='closed')   return {current:4, percent:100};
    if (s==='confirmé'||s==='signe')     return {current:3, percent:66};
    if (s==='assigned'||s==='assigne'||s==='processed') return {current:2, percent:33};
    return {current:1, percent:5};
}
function getStatusLabel(s) {
    return ({pending:'En attente', assigned:'Assigné', assigne:'Assigné', processed:'En traitement',
             confirmé:'Devis validé', closed:'Terminé', completed:'Terminé', cancelled:'Annulé'}
    )[(s||'').toLowerCase()] || s || '—';
}
function getStatusCls(s) {
    const m = (s||'').toLowerCase();
    if (m==='closed'||m==='completed') return 'bg-emerald-50 text-emerald-600 border-emerald-100';
    if (m==='cancelled')               return 'bg-red-50 text-red-500 border-red-100';
    if (m==='confirmé'||m==='signe')   return 'bg-brand-50 text-brand-600 border-brand-100';
    if (m==='processed'||m==='assigned'||m==='assigne') return 'bg-amber-50 text-amber-600 border-amber-100';
    return 'bg-slate-50 text-slate-400 border-slate-200';
}
</script>

<!-- Bottom Navigation -->
<nav class="bottom-nav">
    <a href="/rappel/public/client/dashboard.php" class="bnav-btn active">
        <i data-lucide="home" style="width:24px;height:24px;"></i>
        <span>Accueil</span>
    </a>
    <a href="/rappel/public/client/mes-demandes.php" class="bnav-btn">
        <i data-lucide="list" style="width:24px;height:24px;"></i>
        <span>Demandes</span>
    </a>
    <div class="flex-1 relative flex justify-center">
        <button onclick="openModal()" class="absolute -top-10 w-16 h-16 bg-[#0E1648] rounded-3xl flex items-center justify-center shadow-2xl shadow-brand-900/40 active:scale-90 transition-all group">
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

<script src="/rappel/public/assets/js/app.js?v=4.1"></script>
<?php include __DIR__ . '/../includes/cookie_banner.php'; ?>
</body>
</html>
