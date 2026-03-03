<?php
require_once __DIR__ . '/../includes/auth.php';
requireAuth(false);
$pageTitle = 'Mes Devis';
$user = getCurrentUser();
$token = getToken();
if (($user['role'] ?? '') === 'provider') { header('Location: /rappel/public/pro/quotes.php'); exit; }
if (($user['role'] ?? '') === 'admin')    { header('Location: /rappel/public/admin/dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include __DIR__ . '/../includes/head.php'; ?>
    <style>
    body { padding-bottom: 90px; }
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
    .bnav-btn.active { color:#0E1648; }
    .bnav-btn.active i { transform: translateY(-2px); color: #0E1648; }
    .q-pill { display:inline-flex; align-items:center; gap:.3rem; padding:.25rem .7rem; border-radius:9999px; font-size:.6rem; font-weight:900; text-transform:uppercase; letter-spacing:.08em; border:1px solid; }
    </style>
</head>
<body class="min-h-screen">

<header class="sticky top-0 z-50 bg-white/82 backdrop-blur-xl border-b border-navy-100/50 h-20 flex items-center justify-between px-4 sm:px-8 shadow-sm">
    <a href="/rappel/public/client/dashboard.php" class="w-12 h-12 rounded-2xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-all flex-shrink-0">
        <i data-lucide="arrow-left" style="width:24px;height:24px;"></i>
    </a>
    <h1 class="font-display font-black text-navy-950 text-xl tracking-tight">Voir mes devis</h1>
    <a href="/rappel/public/client/settings.php" class="w-12 h-12 rounded-2xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-all">
        <i data-lucide="settings" style="width:24px;height:24px;"></i>
    </a>
</header>

<div class="max-w-4xl mx-auto px-4 py-6 space-y-4">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
        <div class="grid grid-cols-2 gap-3">
            <select id="q-filter" class="form-select rounded-xl">
                <option value="all">Tous les statuts</option>
                <option value="attente_client">En attente client</option>
                <option value="accepted">Accept&eacute; client</option>
                <option value="rejected">Refus&eacute; client</option>
                <option value="completed">R&eacute;alis&eacute;</option>
            </select>
            <select id="q-sort" class="form-select rounded-xl">
                <option value="recent">Plus récents</option>
                <option value="amount_desc">Montant décroissant</option>
                <option value="amount_asc">Montant croissant</option>
            </select>
        </div>
    </div>

    <div id="quotes-list" class="space-y-4">
        <div class="card p-8 text-center text-navy-400">Chargement...</div>
    </div>
</div>

<script src="/rappel/public/assets/js/app.js?v=4.1"></script>
<script>
const TOKEN = '<?= addslashes($token ?? '') ?>';
if (TOKEN) Auth.setToken(TOKEN);

let quotesState = [];

function statusLabel(s) {
    const k = String(s || '').toLowerCase();
    return ({
        attente_client: 'En attente client',
        sent: 'Envoy\u00e9',
        envoye: 'Envoy\u00e9',
        accepted: 'Accept\u00e9 par client',
        signe: 'Accept\u00e9 par client',
        rejected: 'Refus\u00e9 par client',
        refuse: 'Refus\u00e9 par client',
        completed: 'R\u00e9alis\u00e9',
        realise: 'R\u00e9alis\u00e9',
        draft: 'Brouillon'
    })[k] || (s || 'Inconnu');
}

function statusClass(s) {
    const k = String(s || '').toLowerCase();
    if (k === 'accepted' || k === 'signe') return 'bg-accent-50 text-accent-700 border-accent-100';
    if (k === 'completed' || k === 'realise') return 'bg-emerald-50 text-emerald-700 border-emerald-100';
    if (k === 'attente_client' || k === 'draft' || k === 'sent' || k === 'envoye') return 'bg-amber-50 text-amber-700 border-amber-100';
    if (k === 'rejected' || k === 'refuse') return 'bg-red-50 text-red-700 border-red-100';
    return 'bg-slate-50 text-slate-600 border-slate-200';
}

function fmtAmount(v) {
    const n = parseFloat(v || 0);
    return `${n.toLocaleString('fr-FR')} €`;
}

function getFilteredQuotes() {
    const filter = document.getElementById('q-filter').value;
    const sort = document.getElementById('q-sort').value;
    let arr = quotesState.filter(q => filter === 'all' ? true : String(q.status || '').toLowerCase() === filter);
    arr = arr.sort((a, b) => {
        if (sort === 'amount_desc') return parseFloat(b.amount || 0) - parseFloat(a.amount || 0);
        if (sort === 'amount_asc') return parseFloat(a.amount || 0) - parseFloat(b.amount || 0);
        return new Date(b.created_at || 0).getTime() - new Date(a.created_at || 0).getTime();
    });
    return arr;
}

function renderQuotes() {
    const list = document.getElementById('quotes-list');
    const quotes = getFilteredQuotes();
    if (!quotes.length) {
        list.innerHTML = `<div class="card p-10 text-center">
            <i data-lucide="file-text" class="text-navy-300 mx-auto mb-3" style="width:34px;height:34px;"></i>
            <p class="font-black text-navy-900">Aucun devis trouvé</p>
        </div>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();
        return;
    }

    list.innerHTML = quotes.map(q => {
        const pending = ['attente_client', 'draft', 'sent', 'envoye'].includes(String(q.status || '').toLowerCase());
        return `<article class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Expert partenaire</p>
                    <h3 class="font-black text-slate-900">${escapeHtml(q.provider_company || q.provider_name || '—')}</h3>
                </div>
                <span class="q-pill ${statusClass(q.status)}">${statusLabel(q.status)}</span>
            </div>
            <p class="font-bold text-navy-900">${escapeHtml(q.need || q.project_name || 'Projet')}</p>
            ${q.description ? `<p class="text-sm text-navy-500 mt-1">${escapeHtml(q.description)}</p>` : ''}
            <div class="flex items-center justify-between mt-4">
                <p class="text-2xl font-display font-black text-navy-950">${fmtAmount(q.amount)}</p>
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">${new Date(q.created_at).toLocaleDateString('fr-FR')}</p>
            </div>
            <div class="mt-3">
                <a href="/rappel/public/quote-view.php?id=${encodeURIComponent(q.id)}" class="inline-flex h-9 items-center px-3 rounded-xl border border-slate-200 text-slate-700 text-[10px] font-black uppercase tracking-widest hover:border-brand-300 hover:text-brand-700 transition-all">
                    Voir devis
                </a>
            </div>
            ${pending ? `<div class="mt-4 flex gap-2">
                <button onclick="quoteAction('${q.id}','refuse')" class="flex-1 h-10 rounded-xl border border-slate-200 text-slate-600 font-black text-xs uppercase tracking-widest hover:text-red-600 hover:border-red-200 transition-all">Refuser</button>
                <button onclick="quoteAction('${q.id}','accept')" class="flex-1 h-10 rounded-xl bg-[#0E1648] text-white font-black text-xs uppercase tracking-widest hover:bg-brand-700 transition-all">Valider</button>
            </div>` : '' }
        </article>`;
    }).join('');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

async function quoteAction(id, action) {
    if (!confirm(action === 'accept' ? 'Valider ce devis ?' : 'Refuser ce devis ?')) return;
    try {
        await apiFetch(`/client/${action === 'accept' ? 'accept' : 'refuse'}-quote/${id}`, { method: 'PATCH' });
        showToast(action === 'accept' ? 'Devis validé.' : 'Devis refusé.', 'success');
        await loadQuotes();
    } catch (e) {
        showToast(e.message || 'Erreur.', 'error');
    }
}

async function loadQuotes() {
    try {
        quotesState = await apiFetch('/client/quotes');
        if (!Array.isArray(quotesState)) quotesState = [];
        renderQuotes();
    } catch (e) {
        document.getElementById('quotes-list').innerHTML = `<div class="form-error">${escapeHtml(e.message || 'Erreur de chargement')}</div>`;
    }
}

document.getElementById('q-filter').addEventListener('change', renderQuotes);
document.getElementById('q-sort').addEventListener('change', renderQuotes);
document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') lucide.createIcons();
    loadQuotes();
});
</script>

<nav class="bottom-nav">
    <a href="/rappel/public/client/dashboard.php" class="bnav-btn">
        <i data-lucide="home" style="width:24px;height:24px;"></i>
        <span>Accueil</span>
    </a>
    <a href="/rappel/public/client/mes-demandes.php" class="bnav-btn">
        <i data-lucide="list" style="width:24px;height:24px;"></i>
        <span>Demandes</span>
    </a>
    <a href="/rappel/public/client/quotes.php" class="bnav-btn active">
        <i data-lucide="file-text" style="width:24px;height:24px;"></i>
        <span>Devis</span>
    </a>
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


