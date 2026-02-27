<?php
require_once __DIR__ . '/../includes/auth.php';
requireAuth(false);
$pageTitle = 'Mon Profil';
$user = getCurrentUser();
$token = getToken();
if (($user['role'] ?? '') === 'provider') { header('Location: /rappel/public/pro/settings.php'); exit; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include __DIR__ . '/../includes/head.php'; ?>
    <style>
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

    /* Welcome Banner Glass pattern */
    .glass-pattern {
        background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.05) 1px, transparent 0);
        background-size: 24px 24px;
    }
    
    body { padding-bottom: 90px; }
    </style>
</head>
<body class="min-h-screen">

<!-- Mobile top bar -->
<header class="sticky top-0 z-50 bg-white/82 backdrop-blur-xl border-b border-navy-100/50 h-20 flex items-center justify-between px-4 sm:px-8 shadow-sm">
    <a href="/rappel/public/client/dashboard.php" class="w-12 h-12 rounded-2xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-all flex-shrink-0">
        <i data-lucide="arrow-left" style="width:24px;height:24px;"></i>
    </a>
    <a href="/rappel/public/">
        <img src="/rappel/public/assets/img/logo.png" alt="Rappelez-moi" class="h-10 w-auto object-contain">
    </a>
    <a href="/rappel/public/logout.php"
       class="w-12 h-12 rounded-2xl bg-slate-100 hover:bg-red-50 hover:text-red-500 flex items-center justify-center text-slate-500 transition-all" title="Déconnexion">
        <i data-lucide="log-out" style="width:24px;height:24px;"></i>
    </a>
</header>

<div class="max-w-2xl mx-auto px-4 py-8 space-y-6">

    <!-- Profile Header -->
    <div class="relative rounded-[2.5rem] bg-[#0E1648] p-8 overflow-hidden shadow-2xl">
        <div class="absolute inset-0 pointer-events-none glass-pattern opacity-40"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-brand-400/10 to-transparent pointer-events-none"></div>
        <div class="relative z-10 flex items-center gap-6">
            <div class="w-20 h-20 rounded-3xl bg-white/10 border border-white/20 backdrop-blur-md flex items-center justify-center text-3xl font-black text-white flex-shrink-0">
                <?= strtoupper(substr($user['email'] ?? 'U', 0, 1)) ?>
            </div>
            <div>
                <h1 class="text-2xl font-display font-black text-white leading-tight">
                    <?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: ($user['company_name'] ?? 'Mon Profil')) ?>
                </h1>
                <p class="text-white/50 text-sm font-medium mt-1 flex items-center gap-2">
                    <i data-lucide="mail" style="width:14px;height:14px;"></i>
                    <?= htmlspecialchars($user['email'] ?? '') ?>
                </p>
                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-emerald-500/20 border border-emerald-500/30 font-black text-[9px] text-emerald-400 uppercase tracking-widest mt-3">
                    <span class="w-1 h-1 rounded-full bg-emerald-400"></span>
                    Compte Entreprise
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
        <div class="flex border-b border-slate-100 overflow-x-auto no-scrollbar">
            <?php foreach([
                ['id'=>'profile',  'icon'=>'user',     'label'=>'Profil'],
                ['id'=>'security', 'icon'=>'shield',   'label'=>'Sécurité'],
                ['id'=>'data',     'icon'=>'database',  'label'=>'Mes données'],
            ] as $i => $tab): ?>
            <button onclick="switchTab('<?= $tab['id'] ?>')"
                    class="tab-btn flex-1 flex flex-col sm:flex-row items-center justify-center gap-1.5 px-4 py-4 text-xs font-black uppercase tracking-widest transition-all whitespace-nowrap
                           <?= $i === 0 ? 'text-brand-600 border-b-2 border-brand-600 bg-brand-50/30' : 'text-slate-400 hover:text-slate-700' ?>"
                    data-tab="<?= $tab['id'] ?>">
                <i data-lucide="<?= $tab['icon'] ?>" style="width:18px;height:18px;"></i>
                <?= $tab['label'] ?>
            </button>
            <?php endforeach; ?>
        </div>

        <div class="p-6">

            <!-- ==== PROFILE ==== -->
            <section id="tab-profile" class="tab-content space-y-5">
                <div id="profile-msg" class="hidden"></div>
                <form onsubmit="saveProfile(event)" class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Prénom</label>
                            <input type="text" id="s-firstname" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
                                   class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none font-bold text-slate-900 transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Nom</label>
                            <input type="text" id="s-lastname" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
                                   class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none font-bold text-slate-900 transition-all text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Email <span class="normal-case font-medium opacity-60">(non modifiable)</span></label>
                        <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                               class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-100 text-slate-500 font-bold text-sm cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Téléphone</label>
                        <input type="tel" id="s-phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                               placeholder="06 00 00 00 00"
                               class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none font-bold text-slate-900 transition-all text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Nom de l'entreprise</label>
                        <input type="text" id="s-company" value="<?= htmlspecialchars($user['company_name'] ?? '') ?>"
                               placeholder="Ma Société SAS"
                               class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none font-bold text-slate-900 transition-all text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Adresse</label>
                        <input type="text" id="s-address" value="<?= htmlspecialchars($user['address'] ?? '') ?>"
                               placeholder="12 rue de la Paix"
                               class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none font-bold text-slate-900 transition-all text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Code postal</label>
                            <input type="text" id="s-zip" value="<?= htmlspecialchars($user['zip'] ?? '') ?>"
                                   placeholder="75000"
                                   class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none font-bold text-slate-900 transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Ville</label>
                            <input type="text" id="s-city" value="<?= htmlspecialchars($user['city'] ?? '') ?>"
                                   placeholder="Paris"
                                   class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none font-bold text-slate-900 transition-all text-sm">
                        </div>
                    </div>
                    <button type="submit" id="save-btn"
                            class="w-full h-12 bg-[#0E1648] hover:bg-brand-700 text-white font-black rounded-xl transition-all active:scale-95 text-sm uppercase tracking-widest">
                        Enregistrer
                    </button>
                </form>
            </section>

            <!-- ==== SECURITY ==== -->
            <section id="tab-security" class="tab-content hidden space-y-5">
                <div id="pwd-msg" class="hidden"></div>
                <form onsubmit="savePassword(event)" class="space-y-5">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Mot de passe actuel</label>
                        <input type="password" id="s-old-pwd" placeholder="••••••••"
                               class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none font-bold text-slate-900 transition-all text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Nouveau mot de passe</label>
                        <input type="password" id="s-new-pwd" placeholder="8 caractères minimum"
                               class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none font-bold text-slate-900 transition-all text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Confirmer le nouveau mot de passe</label>
                        <input type="password" id="s-confirm-pwd" placeholder="••••••••"
                               class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-400 outline-none font-bold text-slate-900 transition-all text-sm">
                    </div>
                    <button type="submit" id="pwd-btn"
                            class="w-full h-12 bg-[#0E1648] hover:bg-brand-700 text-white font-black rounded-xl transition-all active:scale-95 text-sm uppercase tracking-widest">
                        Mettre à jour
                    </button>
                </form>
            </section>

            <!-- ==== DATA (GDPR) ==== -->
            <section id="tab-data" class="tab-content hidden space-y-4">
                <!-- Export -->
                <div class="rounded-2xl bg-slate-50 border border-slate-200 p-5 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-700 flex-shrink-0">
                            <i data-lucide="download" style="width:18px;height:18px;"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-900 text-sm">Exporter mes données</h4>
                            <p class="text-xs text-slate-500">Téléchargez toutes vos demandes et informations au format JSON (RGPD).</p>
                        </div>
                    </div>
                    <button onclick="doExport()" id="export-btn"
                            class="w-full h-10 bg-white border border-slate-200 hover:bg-slate-900 hover:text-white hover:border-slate-900 text-slate-700 font-bold rounded-xl text-xs uppercase tracking-widest transition-all">
                        Télécharger mes données
                    </button>
                </div>

                <!-- Delete -->
                <div class="rounded-2xl bg-red-50 border border-red-100 p-5 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white border border-red-100 flex items-center justify-center text-red-600 flex-shrink-0">
                            <i data-lucide="trash-2" style="width:18px;height:18px;"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-red-700 text-sm">Supprimer mon compte</h4>
                            <p class="text-xs text-red-500">Action irréversible. Toutes vos données seront définitivement supprimées.</p>
                        </div>
                    </div>
                    <button onclick="doDelete()" id="delete-btn"
                            class="w-full h-10 bg-red-600 hover:bg-red-700 text-white font-black rounded-xl text-xs uppercase tracking-widest transition-all active:scale-95">
                        Supprimer définitivement
                    </button>
                </div>
            </section>

        </div>
    </div>
</div>

<script>
const TOKEN = '<?= addslashes($token) ?>';
if (TOKEN) Auth.setToken(TOKEN);

/* Tab switching */
function switchTab(id) {
    document.querySelectorAll('.tab-btn').forEach(b => {
        const active = b.dataset.tab === id;
        b.classList.toggle('text-brand-600', active);
        b.classList.toggle('border-b-2', active);
        b.classList.toggle('border-brand-600', active);
        b.classList.toggle('bg-brand-50/30', active);
        b.classList.toggle('text-slate-400', !active);
    });
    document.querySelectorAll('.tab-content').forEach(s => s.classList.toggle('hidden', s.id !== `tab-${id}`));
}

/* Feedback helper */
function showFeedback(el, type, msg) {
    el.className = `p-3 rounded-xl text-xs font-bold flex items-center gap-2 mb-2 animate-fade-in
        ${type === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-600 border border-red-100'}`;
    el.innerHTML = `<i data-lucide="${type === 'success' ? 'check-circle' : 'alert-circle'}" style="width:14px;height:14px;"></i>${msg}`;
    el.classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

/* Save Profile */
async function saveProfile(e) {
    e.preventDefault();
    const btn = document.getElementById('save-btn');
    const msg = document.getElementById('profile-msg');
    setButtonLoading(btn, true);
    try {
        const res = await apiFetch('/profile', { method: 'PATCH', body: JSON.stringify({
            first_name:   document.getElementById('s-firstname').value.trim(),
            last_name:    document.getElementById('s-lastname').value.trim(),
            phone:        document.getElementById('s-phone').value.trim(),
            company_name: document.getElementById('s-company').value.trim(),
            address:      document.getElementById('s-address').value.trim(),
            zip:          document.getElementById('s-zip').value.trim(),
            city:         document.getElementById('s-city').value.trim(),
        })});
        if (res.user) {
            Auth.setUser(res.user);
            await fetch('/rappel/public/api-session.php', {
                method: 'POST', headers: {'Content-Type':'application/json'},
                body: JSON.stringify({ action:'set', token: Auth.getToken(), user: res.user })
            });
        }
        showFeedback(msg, 'success', 'Profil mis à jour avec succès.');
    } catch(err) {
        showFeedback(msg, 'error', err.message || 'Erreur lors de la sauvegarde.');
    } finally {
        setButtonLoading(btn, false, 'Enregistrer');
    }
}

/* Change Password */
async function savePassword(e) {
    e.preventDefault();
    const btn = document.getElementById('pwd-btn');
    const msg = document.getElementById('pwd-msg');
    const np  = document.getElementById('s-new-pwd').value;
    const cp  = document.getElementById('s-confirm-pwd').value;
    if (np !== cp) { showFeedback(msg, 'error', 'Les mots de passe ne correspondent pas.'); return; }
    setButtonLoading(btn, true);
    try {
        await apiFetch('/auth/change-password', { method:'POST', body: JSON.stringify({
            current_password: document.getElementById('s-old-pwd').value,
            new_password: np,
        })});
        showFeedback(msg, 'success', 'Mot de passe modifié.');
        ['s-old-pwd','s-new-pwd','s-confirm-pwd'].forEach(id => document.getElementById(id).value = '');
    } catch(err) {
        showFeedback(msg, 'error', err.message || 'Mot de passe actuel incorrect.');
    } finally {
        setButtonLoading(btn, false, 'Mettre à jour');
    }
}

/* Export */
async function doExport() {
    const btn = document.getElementById('export-btn');
    setButtonLoading(btn, true);
    try {
        const data = await apiFetch('/client/export');
        const blob = new Blob([JSON.stringify(data, null, 2)], {type:'application/json'});
        const url  = URL.createObjectURL(blob);
        const a    = Object.assign(document.createElement('a'), {
            href: url, download: `rappel_export_${new Date().toISOString().split('T')[0]}.json`
        });
        document.body.appendChild(a); a.click(); URL.revokeObjectURL(url); a.remove();
    } catch(err) {
        showToast('Erreur export : ' + err.message, 'error');
    } finally {
        setButtonLoading(btn, false, 'Télécharger mes données');
    }
}

/* Delete */
async function doDelete() {
    if (!confirm('Supprimer définitivement votre compte ? Toutes vos données seront perdues.')) return;
    const btn = document.getElementById('delete-btn');
    setButtonLoading(btn, true);
    try {
        await apiFetch('/client/delete', {method:'DELETE'});
        window.location.href = '/rappel/public/logout.php';
    } catch(err) {
        showToast('Erreur : ' + err.message, 'error');
        setButtonLoading(btn, false, 'Supprimer définitivement');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') lucide.createIcons();
});
</script>

<!-- Bottom Navigation -->
<nav class="bottom-nav">
    <a href="/rappel/public/client/dashboard.php" class="bnav-btn">
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
    <a href="/rappel/public/client/settings.php" class="bnav-btn active">
        <i data-lucide="settings" style="width:24px;height:24px;"></i>
        <span>Profil</span>
    </a>
    <a href="/rappel/public/logout.php" class="bnav-btn">
        <i data-lucide="log-out" style="width:24px;height:24px;"></i>
        <span>Quitter</span>
    </a>
</nav>

<!-- Minimal modal logic needed for the "Nouveau" button if it's reused -->
<script>
function openModal() { window.location.href = '/rappel/public/client/dashboard.php?openModal=1'; }
</script>

<script src="/rappel/public/assets/js/app.js?v=4.1"></script>
<?php include __DIR__ . '/../includes/cookie_banner.php'; ?>
</body>
</html>
