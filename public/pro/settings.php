<?php
require_once __DIR__ . '/../includes/auth.php';
requireAuth(true);
$pageTitle = 'Paramètres';
syncUser();
$user = getCurrentUser();
$token = getToken();
?>
<?php include __DIR__ . '/../includes/dashboard_layout_top.php'; ?>

<!-- Expansive Header & Profile Card -->
<div class="max-w-7xl mx-auto mb-12 animate-fade-in">
    <div class="relative rounded-[3rem] bg-navy-950 p-8 md:p-12 overflow-hidden shadow-2xl">
        <!-- Abstract Decorative Background -->
        <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-brand-600/20 to-transparent pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-accent-500/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
            <!-- Large Avatar -->
            <div class="relative group">
                <div class="w-28 h-28 md:w-36 md:h-36 rounded-full bg-gradient-to-tr from-brand-500 to-brand-400 p-1 shadow-2xl transition-transform group-hover:scale-105 duration-500">
                    <div class="w-full h-full rounded-full bg-navy-950 flex items-center justify-center text-5xl font-black text-white">
                        <?= strtoupper(substr($userEmail, 0, 1)) ?>
                    </div>
                </div>
                <div class="absolute bottom-2 right-2 w-8 h-8 bg-accent-500 border-4 border-navy-950 rounded-full shadow-lg animate-pulse"></div>
            </div>
            
            <div class="text-center md:text-left flex-1">
                <h1 class="text-4xl md:text-5xl font-display font-black text-white tracking-tight mb-2">
                    <?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?>
                </h1>
                <p class="text-brand-300 font-bold uppercase text-[10px] tracking-[0.3em] opacity-80">
                    Configuration de votre compte <?= htmlspecialchars($userRole) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Main Settings Card -->
<div class="max-w-7xl mx-auto animate-fade-in-up" style="animation-delay: 200ms">
    <div class="bg-white/70 backdrop-blur-2xl rounded-[3rem] border border-white shadow-premium overflow-hidden">
        
        <!-- Horizontal Navigation Tab Bar -->
        <div class="flex items-center border-b border-navy-100/50 px-8 py-2 bg-navy-50/30 overflow-x-auto no-scrollbar">
            <button onclick="switchTab('profile')" class="tab-link active px-8 py-6 flex items-center gap-3 transition-all relative group" data-tab="profile">
                <i data-lucide="user" class="text-navy-400 group-[.active]:text-brand-600" style="width:18px;height:18px;"></i>
                <span class="text-xs font-black text-navy-950 uppercase tracking-widest group-[.active]:text-brand-600 transition-colors">Identité</span>
                <div class="absolute bottom-0 left-4 right-4 h-1 bg-brand-600 rounded-t-full scale-x-0 group-[.active]:scale-x-100 transition-transform origin-center"></div>
            </button>
            <button onclick="switchTab('security')" class="tab-link px-8 py-6 flex items-center gap-3 transition-all relative group" data-tab="security">
                <i data-lucide="shield" class="text-navy-400 group-[.active]:text-accent-600" style="width:18px;height:18px;"></i>
                <span class="text-xs font-black text-navy-950 uppercase tracking-widest group-[.active]:text-accent-600 transition-colors">Sécurité</span>
                <div class="absolute bottom-0 left-4 right-4 h-1 bg-accent-600 rounded-t-full scale-x-0 group-[.active]:scale-x-100 transition-transform origin-center"></div>
            </button>
            <button onclick="switchTab('billing')" class="tab-link px-8 py-6 flex items-center gap-3 transition-all relative group" data-tab="billing">
                <i data-lucide="credit-card" class="text-navy-400 group-[.active]:text-indigo-600" style="width:18px;height:18px;"></i>
                <span class="text-xs font-black text-navy-950 uppercase tracking-widest group-[.active]:text-indigo-600 transition-colors">Facturation</span>
                <div class="absolute bottom-0 left-4 right-4 h-1 bg-indigo-600 rounded-t-full scale-x-0 group-[.active]:scale-x-100 transition-transform origin-center"></div>
            </button>
        </div>

        <div class="p-8 md:p-14">
            <!-- Profile Tab -->
            <section id="tab-profile" class="tab-content space-y-12">
                <div class="max-w-4xl">
                    <h3 class="text-2xl font-display font-black text-navy-950 tracking-tight mb-3">Informations de Profil</h3>
                    <p class="text-sm text-navy-500 font-medium leading-relaxed">Gérez vos détails personnels et les informations relatives à votre entreprise pour une meilleure visibilité.</p>
                </div>

                <form onsubmit="handleUpdateProfile(event)" class="max-w-4xl space-y-10">
                    <div class="grid md:grid-cols-2 gap-10">
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-navy-400 uppercase tracking-widest pl-1">Prénom</label>
                            <input type="text" id="s-firstname" class="w-full h-14 px-6 rounded-2xl bg-navy-50/50 border border-navy-100 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-500/5 transition-all outline-none font-bold text-navy-900"
                                   value="<?= htmlspecialchars($user['first_name'] ?? '') ?>">
                        </div>
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-navy-400 uppercase tracking-widest pl-1">Nom de famille</label>
                            <input type="text" id="s-lastname" class="w-full h-14 px-6 rounded-2xl bg-navy-50/50 border border-navy-100 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-500/5 transition-all outline-none font-bold text-navy-900"
                                   value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-10">
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-navy-400 uppercase tracking-widest pl-1">Adresse Email Profesionnelle</label>
                            <input type="email" id="s-email" class="w-full h-14 px-6 rounded-2xl bg-navy-50/50 border border-navy-100 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-500/5 transition-all outline-none font-bold text-navy-900"
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                        </div>
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-navy-400 uppercase tracking-widest pl-1">Téléphone Contact</label>
                            <input type="tel" id="s-phone" class="w-full h-14 px-6 rounded-2xl bg-navy-50/50 border border-navy-100 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-500/5 transition-all outline-none font-bold text-navy-900"
                                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="pt-8 border-t border-navy-100/50">
                        <div class="grid md:grid-cols-2 gap-10">
                            <div class="space-y-4">
                                <label class="block text-[10px] font-black text-navy-400 uppercase tracking-widest pl-1">Nom de l'Entreprise</label>
                                <input type="text" id="s-company" class="w-full h-14 px-6 rounded-2xl bg-navy-50/50 border border-navy-100 focus:border-brand-500 focus:bg-white transition-all outline-none font-bold text-navy-900"
                                       value="<?= htmlspecialchars($user['company_name'] ?? '') ?>">
                            </div>
                            <div class="space-y-4">
                                <label class="block text-[10px] font-black text-navy-400 uppercase tracking-widest pl-1">Numéro SIRET</label>
                                <div class="flex gap-3">
                                    <div class="relative flex-1">
                                        <input type="text" id="s-siret" class="w-full h-14 px-6 rounded-2xl bg-navy-50/50 border border-navy-100 focus:border-brand-500 focus:bg-white transition-all outline-none font-bold text-navy-900"
                                               value="<?= htmlspecialchars($user['siret'] ?? '') ?>" maxlength="14" placeholder="14 chiffres">
                                        <div id="s-siret-spinner" class="absolute right-3 top-1/2 -translate-y-1/2 hidden">
                                            <svg class="animate-spin h-4 w-4 text-brand-500" viewBox="0 0 24 24" fill="none">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <button type="button" onclick="lookupSiret()" class="h-14 px-5 bg-navy-100/50 hover:bg-navy-100 text-navy-600 rounded-2xl border border-navy-100 transition-all flex items-center justify-center">
                                        <i data-lucide="search" style="width:18px;height:18px;"></i>
                                    </button>
                                </div>
                                <p id="s-siret-status" class="text-[9px] font-bold text-navy-400 uppercase tracking-tight pl-1 mt-1"></p>
                            </div>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-10">
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-navy-400 uppercase tracking-widest pl-1">Forme Juridique</label>
                            <input type="text" id="s-legal-form" class="w-full h-14 px-6 rounded-2xl bg-navy-50/50 border border-navy-100 focus:border-brand-500 focus:bg-white transition-all outline-none font-bold text-navy-900"
                                   value="<?= htmlspecialchars($user['legal_form'] ?? '') ?>" placeholder="SAS, SARL, Auto-entrepreneur...">
                        </div>
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-navy-400 uppercase tracking-widest pl-1">Année de Création</label>
                            <input type="number" id="s-creation-year" class="w-full h-14 px-6 rounded-2xl bg-navy-50/50 border border-navy-100 focus:border-brand-500 focus:bg-white transition-all outline-none font-bold text-navy-900"
                                   value="<?= htmlspecialchars($user['creation_year'] ?? '') ?>" placeholder="Ex: 2020">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-navy-400 uppercase tracking-widest pl-1">Adresse de l'Entreprise</label>
                        <input type="text" id="s-address" class="w-full h-14 px-6 rounded-2xl bg-navy-50/50 border border-navy-100 focus:border-brand-500 focus:bg-white transition-all outline-none font-bold text-navy-900"
                               value="<?= htmlspecialchars($user['address'] ?? '') ?>" placeholder="123 rue de la Paix">
                    </div>

                    <div class="grid md:grid-cols-2 gap-10">
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-navy-400 uppercase tracking-widest pl-1">Code Postal</label>
                            <input type="text" id="s-zip" class="w-full h-14 px-6 rounded-2xl bg-navy-50/50 border border-navy-100 focus:border-brand-500 focus:bg-white transition-all outline-none font-bold text-navy-900"
                                   value="<?= htmlspecialchars($user['zip'] ?? '') ?>" placeholder="75000">
                        </div>
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-navy-400 uppercase tracking-widest pl-1">Ville</label>
                            <input type="text" id="s-city" class="w-full h-14 px-6 rounded-2xl bg-navy-50/50 border border-navy-100 focus:border-brand-500 focus:bg-white transition-all outline-none font-bold text-navy-900"
                                   value="<?= htmlspecialchars($user['city'] ?? '') ?>" placeholder="Paris">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-navy-400 uppercase tracking-widest pl-1">Description Professionnelle</label>
                        <textarea id="s-description" rows="4" class="w-full p-6 rounded-2xl bg-navy-50/50 border border-navy-100 focus:border-brand-500 focus:bg-white transition-all outline-none font-medium text-navy-900" 
                                  placeholder="Décrivez votre expertise et vos services..."><?= htmlspecialchars($user['description'] ?? '') ?></textarea>
                    </div>

                    <div class="grid md:grid-cols-2 gap-10">
                         <div class="space-y-4">
                            <label class="block text-[10px] font-black text-navy-400 uppercase tracking-widest pl-1">Zone d'Intervention</label>
                            <input type="text" id="s-zone" class="w-full h-14 px-6 rounded-2xl bg-navy-50/50 border border-navy-100 focus:border-brand-500 focus:bg-white transition-all outline-none font-bold text-navy-900"
                                   value="<?= htmlspecialchars($user['zone'] ?? '') ?>" placeholder="Ex: Île-de-France, Lyon + 50km">
                        </div>
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-navy-400 uppercase tracking-widest pl-1">Secteurs d'Activité</label>
                            <input type="text" id="s-sectors" class="w-full h-14 px-6 rounded-2xl bg-navy-50/50 border border-navy-100 focus:border-brand-500 focus:bg-white transition-all outline-none font-bold text-navy-900"
                                   value="<?php 
                                        $sectors = $user['sectors'] ?? '';
                                        if (is_string($sectors)) {
                                            $decoded = json_decode($sectors, true);
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                echo htmlspecialchars(implode(', ', $decoded));
                                            } else {
                                                echo htmlspecialchars($sectors);
                                            }
                                        } else if (is_array($sectors)) {
                                            echo htmlspecialchars(implode(', ', $sectors));
                                        }
                                   ?>" placeholder="Assurance, Rénovation...">
                        </div>
                    </div>

                    <div id="profile-msg" class="hidden"></div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" id="save-profile-btn" class="h-16 px-12 bg-navy-950 hover:bg-black text-white font-black rounded-2xl shadow-premium hover:shadow-2xl transition-all active:scale-95 flex items-center gap-4 group uppercase tracking-[0.2em] text-[11px]">
                            <span>Enregistrer les modifications</span>
                            <i data-lucide="check" style="width:18px;height:18px;"></i>
                        </button>
                    </div>
                </form>
            </section>

            <!-- Security Tab -->
            <section id="tab-security" class="tab-content hidden space-y-12">
                <div class="max-w-4xl">
                    <h3 class="text-2xl font-display font-black text-navy-950 tracking-tight mb-3">Sécurité & Accès</h3>
                    <p class="text-sm text-navy-500 font-medium leading-relaxed">Assurez la sécurité de votre compte expert en modifiant régulièrement vos identifiants de connexion.</p>
                </div>

                <form onsubmit="handleChangePassword(event)" class="max-w-4xl space-y-10">
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-navy-400 uppercase tracking-widest pl-1">Mot de passe actuel</label>
                        <input type="password" id="s-current-pwd" class="w-full h-14 px-6 rounded-2xl bg-navy-50/50 border border-navy-100 focus:border-navy-950 focus:bg-white focus:ring-4 focus:ring-navy-950/5 transition-all outline-none font-bold text-navy-900" placeholder="••••••••">
                    </div>

                    <div class="grid md:grid-cols-2 gap-10 pt-10 border-t border-navy-100/50">
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-navy-400 uppercase tracking-widest pl-1">Nouveau mot de passe</label>
                            <input type="password" id="s-new-pwd" class="w-full h-14 px-6 rounded-2xl bg-navy-50/50 border border-navy-100 focus:border-navy-950 focus:bg-white transition-all outline-none font-bold text-navy-900" placeholder="8 caractères min.">
                        </div>
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-navy-400 uppercase tracking-widest pl-1">Confirmer le nouveau mot de passe</label>
                            <input type="password" id="s-confirm-pwd" class="w-full h-14 px-6 rounded-2xl bg-navy-50/50 border border-navy-100 focus:border-navy-950 focus:bg-white transition-all outline-none font-bold text-navy-900" placeholder="••••••••">
                        </div>
                    </div>

                    <div id="pwd-msg" class="hidden"></div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" id="change-pwd-btn" class="h-16 px-12 bg-navy-950 hover:bg-black text-white font-black rounded-2xl shadow-premium hover:shadow-2xl transition-all active:scale-95 flex items-center gap-4 group uppercase tracking-[0.2em] text-[11px]">
                            <span>Mettre à jour la sécurité</span>
                            <i data-lucide="shield-check" style="width:18px;height:18px;"></i>
                        </button>
                    </div>
                </form>
            </section>

            <!-- Billing Tab -->
            <section id="tab-billing" class="tab-content hidden space-y-12">
                <!-- Invoice List -->
                <div>
                    <h4 class="text-[11px] font-black text-navy-400 uppercase tracking-widest mb-8 pl-1">Archives de Facturation</h4>
                    <div class="bg-white rounded-[2rem] border border-navy-100 overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-navy-50/50 border-b border-navy-100">
                                    <th class="px-8 py-4 text-[10px] font-black text-navy-950 uppercase tracking-widest">Date</th>
                                    <th class="px-8 py-4 text-[10px] font-black text-navy-950 uppercase tracking-widest">Réf</th>
                                    <th class="px-8 py-4 text-[10px] font-black text-navy-950 uppercase tracking-widest">Montant</th>
                                    <th class="px-8 py-4 text-[10px] font-black text-navy-950 uppercase tracking-widest">Status</th>
                                    <th class="px-8 py-4 text-[10px] font-black text-navy-950 uppercase tracking-widest text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody id="invoice-list-body" class="divide-y divide-navy-50">
                                <!-- Dynamic content -->
                                <tr>
                                    <td colspan="5" class="px-8 py-10 text-center text-sm font-medium text-navy-400">
                                        Chargement des factures...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<?php
$safeToken = addslashes($token ?? '');
$extraScript = <<<'JS'
<script>
const PHP_TOKEN = 'RAPPEL_PHP_TOKEN_PLACEHOLDER';
if (PHP_TOKEN) Auth.setToken(PHP_TOKEN);

function switchTab(tabId) {
    // Buttons
    document.querySelectorAll('.tab-link').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.tab === tabId);
    });
    // Contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.toggle('hidden', content.id !== `tab-${tabId}`);
    });

    if (tabId === 'billing') {
        updateBillingDisplay();
        fetchInvoices();
    }
    
    // Smooth scroll to top on mobile
    if (window.innerWidth < 768) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}
function updateBillingDisplay() {
    const user = Auth.getUser();
    if (!user || !user.subscription) return;
    const sub = user.subscription;

    const elName = document.getElementById('billing-plan-name');
    if (elName) elName.textContent = sub.plan_name || 'Abonnement Gratuit';

    const elDesc = document.getElementById('billing-plan-desc');
    if (elDesc) elDesc.textContent = sub.status === 'active' ? 'Tous les avantages débloqués' : 'Offre de base limitée';

    const elCredits = document.getElementById('billing-credits-val');
    if (elCredits) elCredits.textContent = sub.lead_credits || 0;
    
    const pct = sub.max_leads > 0 ? Math.min(100, (sub.lead_credits / sub.max_leads) * 100) : 0;
    const elBar = document.getElementById('billing-credits-bar');
    if (elBar) elBar.style.width = pct + '%';

    const price = parseFloat(sub.plan_price || 0);
    const elAmount = document.getElementById('billing-next-amount');
    if (elAmount) elAmount.textContent = '€' + price.toFixed(2);
    
    const statusEl = document.getElementById('billing-next-status');
    if (statusEl) {
        statusEl.textContent = price > 0 ? 'Mensuel' : 'Gratuit';
        statusEl.className = `text-[9px] font-black uppercase tracking-widest px-3 py-1 ${price > 0 ? 'bg-amber-500 shadow-amber-500/20' : 'bg-emerald-500 shadow-emerald-500/20'} rounded-full`;
    }
}

function showFeedback(el, type, text) {
    el.className = `p-4 rounded-xl text-[10px] font-black uppercase tracking-widest mb-6 animate-fade-in ${type === 'success' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-red-50 text-red-600 border border-red-100'}`;
    el.innerHTML = `<div class="flex items-center gap-3">
        <i data-lucide="${type === 'success' ? 'check-circle' : 'alert-circle'}" style="width:16px;height:16px;"></i>
        <span>${text}</span>
    </div>`;
    el.classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
    
    setTimeout(() => {
        el.classList.add('hidden');
    }, 6000);
}

async function handleUpdateProfile(e) {
    e.preventDefault();
    const btn = document.getElementById('save-profile-btn');
    const msg = document.getElementById('profile-msg');
    
    setButtonLoading(btn, true);
    
    try {
        const payload = {
            first_name: document.getElementById('s-firstname').value.trim(),
            last_name: document.getElementById('s-lastname').value.trim(),
            email: document.getElementById('s-email').value.trim(),
            phone: document.getElementById('s-phone').value.trim(),
            company_name: document.getElementById('s-company').value.trim(),
            siret: document.getElementById('s-siret').value.trim(),
            legal_form: document.getElementById('s-legal-form').value.trim(),
            creation_year: document.getElementById('s-creation-year').value.trim(),
            address: document.getElementById('s-address').value.trim(),
            zip: document.getElementById('s-zip').value.trim(),
            city: document.getElementById('s-city').value.trim(),
            description: document.getElementById('s-description').value.trim(),
            zone: document.getElementById('s-zone').value.trim(),
            sectors: document.getElementById('s-sectors').value.trim(),
        };

        const res = await apiFetch('/profile', { method: 'PATCH', body: JSON.stringify(payload)});

        const updatedUser = res.user || {};
        if (Object.keys(updatedUser).length) {
            Auth.setUser(updatedUser);
            await fetch('/rappel/public/api-session.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'set', token: Auth.getToken() || PHP_TOKEN, user: updatedUser })
            });
        }
        
        showFeedback(msg, 'success', 'Identité mise à jour avec succès.');
        showToast('Profil synchronisé !', 'success');
    } catch (err) {
        showFeedback(msg, 'error', err.message || 'Une erreur est survenue.');
    } finally {
        setButtonLoading(btn, false, 'Save Changes');
    }
}

async function lookupSiret(silent = false) {
    const raw = document.getElementById('s-siret').value.trim().replace(/\D/g,'');
    if (raw.length !== 9 && raw.length !== 14) {
        if (!silent) setSiretStatus('Format invalide (9 ou 14 chiffres)', 'error');
        return;
    }

    const spinner = document.getElementById('s-siret-spinner');
    if (!silent) {
        spinner.classList.remove('hidden');
        setSiretStatus('Recherche...', 'neutral');
    }

    try {
        const d = await apiFetch(`/company/lookup?siret=${raw}`);

        if (d.company_name)  document.getElementById('s-company').value = d.company_name;
        if (d.address)       document.getElementById('s-address').value      = d.address;
        if (d.zip_code)      document.getElementById('s-zip').value     = d.zip_code;
        if (d.city)          document.getElementById('s-city').value         = d.city;
        if (d.legal_form)    document.getElementById('s-legal-form').value   = d.legal_form;
        if (d.creation_year) document.getElementById('s-creation-year').value = d.creation_year;

        if (!silent) {
            setSiretStatus('Données importées avec succès', 'success');
            showToast('Données société récupérées !', 'success');
        } else {
            console.log('Auto-fill complete via SIRET lookup.');
        }
    } catch (err) {
        if (!silent) {
            setSiretStatus('Société non trouvée', 'error');
            showToast('Erreur lors de la recherche SIRET', 'error');
        }
    } finally {
        spinner.classList.add('hidden');
    }
}

function setSiretStatus(msg, type) {
    const el = document.getElementById('s-siret-status');
    el.textContent = msg;
    el.className = 'text-[9px] font-bold uppercase tracking-tight pl-1 mt-1 ' + {
        success: 'text-emerald-500',
        error:   'text-red-500',
        neutral: 'text-navy-400',
    }[type];
}

async function handleChangePassword(e) {
    e.preventDefault();
    const btn = document.getElementById('change-pwd-btn');
    const msg = document.getElementById('pwd-msg');
    const currentPwd = document.getElementById('s-current-pwd').value;
    const newPwd = document.getElementById('s-new-pwd').value;
    const confirmPwd = document.getElementById('s-confirm-pwd').value;
    
    if (newPwd && newPwd !== confirmPwd) {
        showFeedback(msg, 'error', 'Les mots de passe ne correspondent pas.');
        return;
    }
    
    setButtonLoading(btn, true);
    
    try {
        await apiFetch('/auth/change-password', { method: 'POST', body: JSON.stringify({
            current_password: currentPwd,
            new_password: newPwd,
        })});
        
        showFeedback(msg, 'success', 'Sécurité mise à jour.');
        document.getElementById('s-current-pwd').value = '';
        document.getElementById('s-new-pwd').value = '';
        document.getElementById('s-confirm-pwd').value = '';
    } catch (err) {
        showFeedback(msg, 'error', err.message || 'Le mot de passe actuel est incorrect.');
    } finally {
        setButtonLoading(btn, false, 'Update Security');
    }
}

async function fetchInvoices() {
    const listBody = document.getElementById('invoice-list-body');
    if (!listBody) return;

    try {
        const invoices = await apiFetch('/invoices');
        
        if (!invoices || invoices.length === 0) {
            listBody.innerHTML = `<tr><td colspan="5" class="px-8 py-10 text-center text-sm font-medium text-navy-400">Aucune facture trouvée.</td></tr>`;
            return;
        }

        listBody.innerHTML = invoices.map(inv => {
            const date = new Date(inv.created_at).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
            const statusClass = inv.status === 'paid' ? 'bg-emerald-50 text-emerald-600 border-emerald-100/50' : 'bg-amber-50 text-amber-600 border-amber-100/50';
            const statusLabel = inv.status === 'paid' ? 'Payé' : 'En attente';
            
            return `
                <tr class="hover:bg-navy-50/20 transition-colors group">
                    <td class="px-8 py-5 text-sm font-bold text-navy-900">${date}</td>
                    <td class="px-8 py-5 text-sm font-medium text-navy-500 font-mono">${inv.invoice_number}</td>
                    <td class="px-8 py-5 text-sm font-black text-navy-950">${inv.amount} ${inv.currency || '€'}</td>
                    <td class="px-8 py-5">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 ${statusClass} rounded-full text-[9px] font-black uppercase tracking-widest border">${statusLabel}</span>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="invoice.php?id=${inv.id}" title="Voir la facture" class="w-10 h-10 rounded-xl bg-white border border-navy-100 shadow-sm flex items-center justify-center text-navy-400 hover:text-brand-600 hover:border-brand-500 hover:shadow-md transition-all">
                                <i data-lucide="eye" style="width:16px;height:16px;"></i>
                            </a>
                            <button onclick="window.open('invoice.php?id=${inv.id}&print=1', '_blank')" title="Télécharger PDF / Imprimer" class="w-10 h-10 rounded-xl bg-white border border-navy-100 shadow-sm flex items-center justify-center text-navy-400 hover:text-emerald-600 hover:border-emerald-500 hover:shadow-md transition-all">
                                <i data-lucide="download" style="width:16px;height:16px;"></i>
                            </button>
                            <button onclick="sendInvoiceByEmail('${inv.id}', this)" title="Envoyer par email" class="w-10 h-10 rounded-xl bg-white border border-navy-100 shadow-sm flex items-center justify-center text-navy-400 hover:text-amber-600 hover:border-amber-500 hover:shadow-md transition-all">
                                <i data-lucide="mail" style="width:16px;height:16px;"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        if (typeof lucide !== 'undefined') lucide.createIcons();
    } catch (err) {
        console.error('Error fetching invoices:', err);
        listBody.innerHTML = `<tr><td colspan="5" class="px-8 py-10 text-center text-sm text-red-500 font-medium">Erreur lors du chargement des factures.</td></tr>`;
    }
}

async function sendInvoiceByEmail(id, btn) {
    const icon = btn.querySelector('i');
    const originalHtml = btn.innerHTML;
    
    // Loading state
    btn.disabled = true;
    btn.innerHTML = '<div class="w-4 h-4 border-2 border-amber-500 border-t-transparent rounded-full animate-spin"></div>';
    
    try {
        const res = await apiFetch(`/invoices/${id}/send`, { method: 'POST' });
        showToast(res.message, 'success');
    } catch (err) {
        showToast(err.message || "Erreur lors de l'envoi de l'email.", 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
}

document.addEventListener('DOMContentLoaded', () => { 
    if (typeof lucide !== 'undefined') lucide.createIcons(); 
    
    // Auto-fill if company info is missing but SIRET exists
    const siret = document.getElementById('s-siret')?.value.trim();
    const address = document.getElementById('s-address')?.value.trim();
    const legalForm = document.getElementById('s-legal-form')?.value.trim();
    
    if (siret && (!address || !legalForm)) {
        console.log('Proactive lookup: Basic company info missing, triggering auto-fill...');
        lookupSiret(true); // pass true for silent mode if preferred
    }
});
</script>

<style>
.tab-link.active {
    background-color: white;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}
.tab-link:not(.active) p { opacity: 0.6; }
.tab-link:not(.active) div { opacity: 0.8; }
</style>
JS;
$extraScript = str_replace('RAPPEL_PHP_TOKEN_PLACEHOLDER', $safeToken, $extraScript);
?>

<?php include __DIR__ . '/../includes/dashboard_layout_bottom.php'; ?>
