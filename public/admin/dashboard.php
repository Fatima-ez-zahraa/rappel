<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$isAdminTheme = true;
$pageTitle = 'Admin - Tableau de bord';
$token = getToken();
$view = $_GET['view'] ?? 'overview';
$allowedViews = ['overview', 'plans', 'providers', 'leads', 'dispatch', 'analytics', 'settings'];
if (!in_array($view, $allowedViews, true)) {
    $view = 'overview';
}
$showOverview = $view === 'overview';
$showPlans = $showOverview || $view === 'plans';
$showProviders = $showOverview || $view === 'providers';
$showLeads = $showOverview || $view === 'leads';
$showDispatch = $showOverview || $view === 'dispatch';
$showAnalytics = $showOverview || $view === 'analytics';
$showSettings = $view === 'settings';
?>
<?php include __DIR__ . '/../includes/dashboard_layout_top.php'; ?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
    <div class="animate-fade-in-up">
        <h1 class="text-4xl font-display font-black text-white tracking-tight">Pilotage Admin</h1>
        <p class="text-navy-400 font-medium mt-1 text-sm">Supervision en temps réel des flux et opportunités</p>
    </div>

    <!-- View selector tabs -->
    <div class="flex flex-wrap items-center gap-2">
        <?php
        $views = [
            'overview'  => ['icon' => 'layout-dashboard', 'label' => 'Vue globale'],
            'plans'     => ['icon' => 'credit-card', 'label' => 'Forfaits'],
            'providers' => ['icon' => 'briefcase-business', 'label' => 'Prestataires'],
            'leads'     => ['icon' => 'inbox', 'label' => 'Leads'],
            'dispatch'  => ['icon' => 'shuffle', 'label' => 'Dispatch'],
            'analytics' => ['icon' => 'bar-chart-3', 'label' => 'Analytics'],
            'settings'  => ['icon' => 'settings', 'label' => 'Parametres'],
        ];
        foreach ($views as $viewKey => $viewMeta):
            $isCurrentView = ($view === $viewKey);
        ?>
        <a href="?view=<?= $viewKey ?>"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-wider transition-all duration-300 border <?= $isCurrentView
               ? 'bg-accent-500/20 border-accent-500/40 text-accent-400 shadow-[0_0_15px_rgba(124,203,99,0.1)]'
               : 'bg-white/5 border-white/10 text-navy-400 hover:bg-white/10 hover:border-white/20' ?>">
            <i data-lucide="<?= $viewMeta['icon'] ?>" style="width:14px;height:14px;"></i>
            <?= $viewMeta['label'] ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>


<?php if (!$showSettings): ?>
<div id="admin-stats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <?php for ($i = 0; $i < 4; $i++): ?>
    <div class="card p-6 min-h-[120px] flex flex-col justify-center animate-pulse">
        <div class="w-10 h-10 rounded-xl bg-white/5 mb-4"></div>
        <div class="h-3 bg-white/10 rounded w-2/3 mb-2"></div>
        <div class="h-6 bg-white/10 rounded w-1/2"></div>
    </div>
    <?php endfor; ?>
</div>
<?php endif; ?>

<?php if ($showPlans): ?>
<section id="admin-plans" class="card p-6 mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5">
        <div>
            <h2 class="text-lg font-display font-bold text-navy-950">Gestion des forfaits</h2>
            <p class="text-sm text-navy-500 font-medium">Ces forfaits alimentent la page Plans & Tarifs des prestataires.</p>
        </div>
    </div>

    <form id="plan-form" class="grid md:grid-cols-2 lg:grid-cols-5 gap-3 mb-5">
        <input type="hidden" id="plan-id">
        <input id="plan-name" class="form-input h-11 rounded-xl" placeholder="Nom du forfait" required>
        <input id="plan-price" type="number" step="0.01" min="0" class="form-input h-11 rounded-xl" placeholder="Prix" required>
        <input id="plan-currency" class="form-input h-11 rounded-xl" placeholder="Devise (EUR)" value="EUR">
        <input id="plan-stripe-id" class="form-input h-11 rounded-xl" placeholder="stripe_price_id (optionnel)">
        <button type="submit" class="btn btn-accent rounded-xl h-11">Enregistrer</button>
        <textarea id="plan-features" class="form-textarea md:col-span-2 lg:col-span-5 rounded-xl" rows="3" placeholder="Fonctionnalites (une par ligne)"></textarea>
    </form>

    <div class="overflow-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-navy-500 border-b border-navy-100">
                    <th class="py-2 pr-2">Nom</th>
                    <th class="py-2 pr-2">Prix</th>
                    <th class="py-2 pr-2">Devise</th>
                    <th class="py-2 pr-2">Fonctionnalites</th>
                    <th class="py-2 pr-2">Actions</th>
                </tr>
            </thead>
            <tbody id="admin-plans-body">
                <tr><td colspan="5" class="py-6 text-center text-navy-400">Chargement...</td></tr>
            </tbody>
        </table>
    </div>
</section>
<?php endif; ?>

<?php if ($showProviders): ?>
<section id="admin-providers" class="card p-6 mb-8">
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-lg font-display font-bold text-navy-950">Prestataires - Infos complètes</h2>
    </div>
    <div class="overflow-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-navy-500 border-b border-navy-100">
                    <th class="py-2 pr-3">Prestataire</th>
                    <th class="py-2 pr-3">Contact</th>
                    <th class="py-2 pr-3">Niches</th>
                    <th class="py-2 pr-3">Forfait</th>
                    <th class="py-2 pr-3">Leads J/S/M</th>
                    <th class="py-2 pr-3">Total</th>
                    <th class="py-2 pr-3 text-right">Abonnement</th>
                </tr>
            </thead>
            <tbody id="providers-table-body">
                <tr><td colspan="6" class="py-8 text-center text-navy-400">Chargement...</td></tr>
            </tbody>
        </table>
    </div>
</section>
<?php endif; ?>

<?php if ($showLeads): ?>
<section id="admin-leads" class="card p-6 mb-8">
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-lg font-display font-bold text-navy-950">Leads - Toutes les informations</h2>
    </div>
    <div class="overflow-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-navy-500 border-b border-navy-100">
                    <th class="py-2 pr-3">Client</th>
                    <th class="py-2 pr-3">Secteur</th>
                    <th class="py-2 pr-3">Besoin</th>
                    <th class="py-2 pr-3">Budget</th>
                    <th class="py-2 pr-3">Statut</th>
                    <th class="py-2 pr-3">Assigné à</th>
                </tr>
            </thead>
            <tbody id="leads-table-body">
                <tr><td colspan="6" class="py-8 text-center text-navy-400">Chargement...</td></tr>
            </tbody>
        </table>
    </div>
</section>
<?php endif; ?>

<?php if ($showDispatch): ?>
<section id="admin-dispatch" class="card p-6 mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5">
        <div>
            <h2 class="text-lg font-display font-bold text-navy-950">Dispatch automatique des leads</h2>
            <p class="text-sm text-navy-500 font-medium">Priorité aux secteurs les plus demandés puis équilibrage entre prestataires par niche</p>
        </div>
        <button id="dispatch-btn" class="btn btn-accent rounded-xl px-5 py-3">Lancer le dispatch automatique</button>
    </div>
    <div class="grid lg:grid-cols-2 gap-5">
        <div class="rounded-xl border border-navy-100 p-4">
            <p class="text-xs font-bold uppercase tracking-wider text-navy-500 mb-2">État du dispatch</p>
            <div id="dispatch-state" class="text-sm text-navy-700">Chargement...</div>
            <div id="dispatch-result" class="mt-3 text-sm"></div>
        </div>
        <div class="rounded-xl border border-navy-100 p-4">
            <p class="text-xs font-bold uppercase tracking-wider text-navy-500 mb-2">Demande par secteur</p>
            <div id="dispatch-demand" class="text-sm text-navy-700">Chargement...</div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($showAnalytics): ?>
<section id="admin-analytics" class="card p-6 mb-8">
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-lg font-display font-bold text-navy-950">Forfaits et cadence des leads</h2>
    </div>

    <div class="grid lg:grid-cols-2 gap-5">
        <div class="rounded-xl border border-navy-100 p-4 overflow-auto">
            <p class="text-xs font-bold uppercase tracking-wider text-navy-500 mb-2">Forfait par prestataire</p>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-navy-500 border-b border-navy-100">
                        <th class="py-2 pr-2">Prestataire</th>
                        <th class="py-2 pr-2">Forfait</th>
                        <th class="py-2 pr-2">Leads J/S/M</th>
                    </tr>
                </thead>
                <tbody id="plans-table-body">
                    <tr><td colspan="3" class="py-6 text-center text-navy-400">Chargement...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="rounded-xl border border-navy-100 p-4 overflow-auto">
            <p class="text-xs font-bold uppercase tracking-wider text-navy-500 mb-2">Secteurs les plus demandés</p>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-navy-500 border-b border-navy-100">
                        <th class="py-2 pr-2">Secteur</th>
                        <th class="py-2 pr-2">Demandes</th>
                    </tr>
                </thead>
                <tbody id="sector-table-body">
                    <tr><td colspan="2" class="py-6 text-center text-navy-400">Chargement...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($showSettings): ?>
<section id="admin-settings" class="grid lg:grid-cols-2 gap-6 mb-8">
    <div class="card p-6">
        <div class="mb-5">
            <h2 class="text-lg font-display font-bold text-navy-950">Profil administrateur</h2>
            <p class="text-sm text-navy-500 font-medium">Mettez a jour vos informations de compte.</p>
        </div>

        <form id="admin-profile-form" class="space-y-4">
            <div class="grid md:grid-cols-2 gap-3">
                <div>
                    <label for="admin-first-name" class="form-label">Prenom</label>
                    <input id="admin-first-name" class="form-input rounded-xl" placeholder="Prenom">
                </div>
                <div>
                    <label for="admin-last-name" class="form-label">Nom</label>
                    <input id="admin-last-name" class="form-input rounded-xl" placeholder="Nom">
                </div>
            </div>

            <div>
                <label for="admin-email" class="form-label">Email</label>
                <input id="admin-email" type="email" class="form-input rounded-xl" placeholder="admin@rappel.fr" required>
            </div>

            <div>
                <label for="admin-phone" class="form-label">Telephone</label>
                <input id="admin-phone" class="form-input rounded-xl" placeholder="+33 6 00 00 00 00">
            </div>

            <button type="submit" id="admin-profile-submit" class="btn btn-accent rounded-xl">Enregistrer le profil</button>
        </form>
    </div>

    <div class="card p-6">
        <div class="mb-5">
            <h2 class="text-lg font-display font-bold text-navy-950">Securite</h2>
            <p class="text-sm text-navy-500 font-medium">Modifiez votre mot de passe administrateur.</p>
        </div>

        <form id="admin-password-form" class="space-y-4">
            <div>
                <label for="admin-current-password" class="form-label">Mot de passe actuel</label>
                <input id="admin-current-password" type="password" class="form-input rounded-xl" required>
            </div>

            <div>
                <label for="admin-new-password" class="form-label">Nouveau mot de passe</label>
                <input id="admin-new-password" type="password" class="form-input rounded-xl" minlength="8" required>
            </div>

            <div>
                <label for="admin-confirm-password" class="form-label">Confirmation</label>
                <input id="admin-confirm-password" type="password" class="form-input rounded-xl" minlength="8" required>
            </div>

            <button type="submit" id="admin-password-submit" class="btn btn-primary rounded-xl">Mettre a jour le mot de passe</button>
        </form>
    </div>
</section>
<?php endif; ?>

<?php
$safeToken = addslashes($token ?? '');
$extraScript = <<<'JS'
<script>
const PHP_TOKEN = '__PHP_TOKEN__';
const ADMIN_VIEW = '__ADMIN_VIEW__';
let adminPlansCache = [];

function escapeHtml(input) {
    return String(input ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

async function loadAdminAll() {
    if (PHP_TOKEN) Auth.setToken(PHP_TOKEN);

    try {
        const [stats, providers, leads, analytics, dispatch, plans] = await Promise.all([
            apiFetch('/admin/stats'),
            apiFetch('/admin/providers'),
            apiFetch('/admin/leads'),
            apiFetch('/admin/analytics'),
            apiFetch('/admin/dispatch'),
            apiFetch('/admin/plans')
        ]);

        renderStats(stats || {}, analytics || {});
        renderAdminPlans(Array.isArray(plans) ? plans : []);
        renderProviders(Array.isArray(providers) ? providers : []);
        renderLeads(Array.isArray(leads) ? leads : []);
        renderAnalytics(analytics || {});
        renderDispatchOverview(dispatch || {});
    } catch (err) {
        console.error('Admin load error:', err);
        if (typeof showToast === 'function') {
            showToast('Erreur de chargement des données admin', 'error');
        }
    }
}

async function loadAdminSettings() {
    if (PHP_TOKEN) Auth.setToken(PHP_TOKEN);
    try {
        const profile = await apiFetch('/profile');
        fillSettingsForm(profile?.user || {});
    } catch (err) {
        console.error('Settings load error:', err);
        if (typeof showToast === 'function') {
            showToast('Erreur de chargement des parametres', 'error');
        }
    }
}

function fillSettingsForm(user) {
    const firstName = document.getElementById('admin-first-name');
    const lastName = document.getElementById('admin-last-name');
    const email = document.getElementById('admin-email');
    const phone = document.getElementById('admin-phone');

    if (firstName) firstName.value = user.first_name || '';
    if (lastName) lastName.value = user.last_name || '';
    if (email) email.value = user.email || '';
    if (phone) phone.value = user.phone || '';
}

async function submitAdminProfile(event) {
    event.preventDefault();
    const payload = {
        first_name: (document.getElementById('admin-first-name')?.value || '').trim(),
        last_name: (document.getElementById('admin-last-name')?.value || '').trim(),
        email: (document.getElementById('admin-email')?.value || '').trim(),
        phone: (document.getElementById('admin-phone')?.value || '').trim()
    };

    try {
        await apiFetch('/profile', { method: 'PATCH', body: JSON.stringify(payload) });
        if (typeof showToast === 'function') {
            showToast('Profil admin mis a jour', 'success');
        }
        await loadAdminSettings();
    } catch (err) {
        console.error('Profile update error:', err);
        if (typeof showToast === 'function') {
            showToast('Echec de mise a jour du profil', 'error');
        }
    }
}

async function submitAdminPassword(event) {
    event.preventDefault();
    const currentPassword = document.getElementById('admin-current-password')?.value || '';
    const newPassword = document.getElementById('admin-new-password')?.value || '';
    const confirmPassword = document.getElementById('admin-confirm-password')?.value || '';

    if (newPassword.length < 8) {
        if (typeof showToast === 'function') showToast('Le nouveau mot de passe doit contenir 8 caracteres minimum', 'error');
        return;
    }

    if (newPassword !== confirmPassword) {
        if (typeof showToast === 'function') showToast('La confirmation du mot de passe ne correspond pas', 'error');
        return;
    }

    try {
        await apiFetch('/auth/change-password', {
            method: 'POST',
            body: JSON.stringify({
                current_password: currentPassword,
                new_password: newPassword
            })
        });

        const form = document.getElementById('admin-password-form');
        if (form) form.reset();
        if (typeof showToast === 'function') {
            showToast('Mot de passe mis a jour', 'success');
        }
    } catch (err) {
        console.error('Password update error:', err);
        if (typeof showToast === 'function') {
            showToast('Echec de mise a jour du mot de passe', 'error');
        }
    }
}

function renderAdminPlans(plans) {
    adminPlansCache = plans;
    const body = document.getElementById('admin-plans-body');
    if (!body) return;

    if (!plans.length) {
        body.innerHTML = '<tr><td colspan="5" class="py-6 text-center text-navy-400">Aucun forfait configure.</td></tr>';
        return;
    }

    body.innerHTML = plans.map((plan) => {
        const features = Array.isArray(plan.features) ? plan.features.join(' | ') : '-';
        return `
            <tr class="border-b border-navy-50">
                <td class="py-2 pr-2 font-semibold text-navy-900">${escapeHtml(plan.name || '')}</td>
                <td class="py-2 pr-2">${escapeHtml(plan.price ?? 0)}</td>
                <td class="py-2 pr-2">${escapeHtml(plan.currency || 'EUR')}</td>
                <td class="py-2 pr-2 text-xs">${escapeHtml(features)}</td>
                <td class="py-2 pr-2">
                    <div class="flex gap-2">
                        <button type="button" class="btn btn-outline btn-sm rounded-lg" onclick="editPlan('${escapeHtml(plan.id || '')}')">Modifier</button>
                        <button type="button" class="btn btn-ghost btn-sm rounded-lg text-red-500" onclick="deletePlan('${escapeHtml(plan.id || '')}')">Supprimer</button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function editPlan(planId) {
    const plan = adminPlansCache.find((p) => String(p.id) === String(planId));
    if (!plan) return;

    document.getElementById('plan-id').value = plan.id || '';
    document.getElementById('plan-name').value = plan.name || '';
    document.getElementById('plan-price').value = plan.price ?? '';
    document.getElementById('plan-currency').value = plan.currency || 'EUR';
    document.getElementById('plan-stripe-id').value = plan.stripe_price_id || '';
    document.getElementById('plan-features').value = Array.isArray(plan.features) ? plan.features.join('\n') : '';
}

function resetPlanForm() {
    const form = document.getElementById('plan-form');
    if (form) form.reset();
    const idEl = document.getElementById('plan-id');
    if (idEl) idEl.value = '';
    const currencyEl = document.getElementById('plan-currency');
    if (currencyEl && !currencyEl.value) currencyEl.value = 'EUR';
}

async function submitPlanForm(event) {
    event.preventDefault();
    const planId = document.getElementById('plan-id').value.trim();
    const payload = {
        name: document.getElementById('plan-name').value.trim(),
        price: parseFloat(document.getElementById('plan-price').value || '0'),
        currency: document.getElementById('plan-currency').value.trim() || 'EUR',
        stripe_price_id: document.getElementById('plan-stripe-id').value.trim(),
        features: document.getElementById('plan-features').value
    };

    try {
        if (planId) {
            await apiFetch(`/admin/plans/${planId}`, { method: 'PATCH', body: JSON.stringify(payload) });
            if (typeof showToast === 'function') showToast('Forfait mis a jour', 'success');
        } else {
            await apiFetch('/admin/plans', { method: 'POST', body: JSON.stringify(payload) });
            if (typeof showToast === 'function') showToast('Forfait cree', 'success');
        }
        resetPlanForm();
        await loadAdminAll();
    } catch (err) {
        console.error('Plan save error:', err);
        if (typeof showToast === 'function') showToast('Echec enregistrement forfait', 'error');
    }
}

async function deletePlan(planId) {
    if (!planId) return;
    if (!confirm('Supprimer ce forfait ?')) return;

    try {
        await apiFetch(`/admin/plans/${planId}`, { method: 'DELETE' });
        if (typeof showToast === 'function') showToast('Forfait supprime', 'success');
        if (document.getElementById('plan-id').value === planId) {
            resetPlanForm();
        }
        await loadAdminAll();
    } catch (err) {
        console.error('Plan delete error:', err);
        if (typeof showToast === 'function') showToast('Echec suppression forfait', 'error');
    }
}

async function toggleSubscription(providerId, newStatus) {
    if (!providerId) return;
    try {
        await apiFetch(`/admin/users/${providerId}`, { 
            method: 'PATCH', 
            body: JSON.stringify({ subscription_status: newStatus }) 
        });
        if (typeof showToast === 'function') {
            showToast(`Abonnement ${newStatus === 'active' ? 'activé' : 'désactivé'}`, 'success');
        }
        await loadAdminAll();
    } catch (err) {
        console.error('Subscription toggle error:', err);
        if (typeof showToast === 'function') {
            showToast('Erreur lors de la modification de l\'abonnement', 'error');
        }
    }
}

function renderStats(s, analytics = {}) {
    const leadsDay = s.leads_today ?? analytics.leads_today ?? 0;
    const leadsWeek = s.leads_week ?? analytics.leads_week ?? 0;
    const leadsMonth = s.leads_month ?? analytics.leads_month ?? 0;
    const cards = [
        { label: 'Prestataires', value: s.total_providers ?? 0, icon: 'users' },
        {
            label: 'Leads total',
            value: s.total_leads ?? 0,
            icon: 'inbox',
            meta: `Jour: ${leadsDay} | Semaine: ${leadsWeek} | Mois: ${leadsMonth}`
        },
        { label: 'Abonnements actifs', value: s.active_subscriptions ?? 0, icon: 'badge-check' },
        { label: 'Revenu mois', value: (s.monthly_revenue ?? 0) + ' EUR', icon: 'trending-up' }
    ];

    const html = cards.map((card) => `
        <div class="card p-5">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3" style="background:rgba(99,102,241,0.15);color:#818cf8;">
                <i data-lucide="${escapeHtml(card.icon)}" style="width:18px;height:18px;"></i>
            </div>
            <p class="text-2xl font-display font-bold text-navy-950">${escapeHtml(card.value)}</p>
            <p class="text-xs text-navy-500 font-bold uppercase tracking-wider mt-1">${escapeHtml(card.label)}</p>
            ${card.meta ? `<p class="text-xs text-navy-500 mt-2">${escapeHtml(card.meta)}</p>` : ''}
        </div>
    `).join('');

    const el = document.getElementById('admin-stats');
    if (el) {
        el.innerHTML = html;
    }
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function renderProviders(providers) {
    const body = document.getElementById('providers-table-body');
    if (!body) return;

    if (!providers.length) {
        body.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-navy-400">Aucun prestataire.</td></tr>';
        return;
    }

    body.innerHTML = providers.map((p) => {
        const fullName = `${p.first_name || ''} ${p.last_name || ''}`.trim() || 'N/A';
        const contact = [p.email || '', p.phone || '', p.city || ''].filter(Boolean).join(' | ');
        const niches = Array.isArray(p.sectors_list) ? p.sectors_list.join(', ') : '';
        const plan = p.plan_name || 'Forfait inactif';
        const cadence = `${p.assigned_leads_day || 0} / ${p.assigned_leads_week || 0} / ${p.assigned_leads_month || 0}`;
        const isActive = p.subscription_status === 'active';
        const actionBtn = isActive 
            ? `<button onclick="toggleSubscription('${p.id}', 'inactive')" class="btn btn-sm rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-xs px-2 py-1">Désactiver</button>`
            : `<button onclick="toggleSubscription('${p.id}', 'active')" class="btn btn-sm rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-xs px-2 py-1 border border-emerald-600">Activer</button>`;

        return `
            <tr class="border-b border-navy-50 align-top">
                <td class="py-3 pr-3">
                    <p class="font-bold text-navy-900">${escapeHtml(p.company_name || fullName)}</p>
                    <p class="text-xs text-navy-500">${escapeHtml(fullName)}</p>
                </td>
                <td class="py-3 pr-3 text-navy-700">${escapeHtml(contact || 'N/A')}</td>
                <td class="py-3 pr-3 text-navy-700">${escapeHtml(niches || '-')}</td>
                <td class="py-3 pr-3 text-navy-700">${escapeHtml(plan)}</td>
                <td class="py-3 pr-3 text-navy-700">${escapeHtml(cadence)}</td>
                <td class="py-3 pr-3 font-bold text-navy-900">${escapeHtml(p.assigned_leads_total || 0)}</td>
                <td class="py-3 pr-3 text-right">${actionBtn}</td>
            </tr>
        `;
    }).join('');
}

function renderLeads(leads) {
    const body = document.getElementById('leads-table-body');
    if (!body) return;

    if (!leads.length) {
        body.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-navy-400">Aucun lead.</td></tr>';
        return;
    }

    body.innerHTML = leads.map((l) => {
        const client = `${l.name || 'Inconnu'} | ${l.phone || ''} ${l.email ? '| ' + l.email : ''}`;
        const assignedName = `${l.assigned_provider_first_name || ''} ${l.assigned_provider_last_name || ''}`.trim();
        const assignedTo = assignedName || l.assigned_provider_email || '-';

        return `
            <tr class="border-b border-navy-50 align-top">
                <td class="py-3 pr-3 text-navy-800">${escapeHtml(client)}</td>
                <td class="py-3 pr-3 text-navy-700">${escapeHtml(l.sector || '-')}</td>
                <td class="py-3 pr-3 text-navy-700">${escapeHtml(l.need || '-')}</td>
                <td class="py-3 pr-3 text-navy-700">${escapeHtml(l.budget || 0)}</td>
                <td class="py-3 pr-3 text-navy-700">${escapeHtml(l.status || 'pending')}</td>
                <td class="py-3 pr-3 text-navy-700">${escapeHtml(assignedTo)}</td>
            </tr>
        `;
    }).join('');
}

function renderDispatchOverview(dispatchData) {
    const state = document.getElementById('dispatch-state');
    const demand = document.getElementById('dispatch-demand');
    if (state) {
        state.innerHTML = `Leads non assignés: <strong>${escapeHtml(dispatchData.pending_unassigned ?? 0)}</strong>`;
    }
    if (demand) {
        const rows = Array.isArray(dispatchData.sector_demand) ? dispatchData.sector_demand.slice(0, 10) : [];
        if (!rows.length) {
            demand.innerHTML = 'Aucune demande secteur.';
        } else {
            demand.innerHTML = rows.map((row) => `
                <div class="flex items-center justify-between py-1 border-b border-navy-50 last:border-0">
                    <span>${escapeHtml(row.sector || 'Non renseigné')}</span>
                    <strong>${escapeHtml(row.demand || 0)}</strong>
                </div>
            `).join('');
        }
    }
}

function renderAnalytics(analytics) {
    const plansBody = document.getElementById('plans-table-body');
    if (plansBody) {
        const providers = Array.isArray(analytics.providers) ? analytics.providers : [];
        if (!providers.length) {
            plansBody.innerHTML = '<tr><td colspan="3" class="py-6 text-center text-navy-400">Aucune donnée forfait.</td></tr>';
        } else {
            plansBody.innerHTML = providers.map((p) => {
                const fullName = `${p.first_name || ''} ${p.last_name || ''}`.trim() || p.email || 'N/A';
                const cadence = `${p.assigned_leads_day || 0} / ${p.assigned_leads_week || 0} / ${p.assigned_leads_month || 0}`;
                return `
                    <tr class="border-b border-navy-50">
                        <td class="py-2 pr-2">${escapeHtml(fullName)}</td>
                        <td class="py-2 pr-2">${escapeHtml(p.plan_name || 'Forfait inactif')}</td>
                        <td class="py-2 pr-2">${escapeHtml(cadence)}</td>
                    </tr>
                `;
            }).join('');
        }
    }

    const sectorBody = document.getElementById('sector-table-body');
    if (sectorBody) {
        const sectors = Array.isArray(analytics.sector_demand) ? analytics.sector_demand : [];
        if (!sectors.length) {
            sectorBody.innerHTML = '<tr><td colspan="2" class="py-6 text-center text-navy-400">Aucune donnée secteur.</td></tr>';
        } else {
            sectorBody.innerHTML = sectors.slice(0, 12).map((s) => `
                <tr class="border-b border-navy-50">
                    <td class="py-2 pr-2">${escapeHtml(s.sector || 'Non renseigné')}</td>
                    <td class="py-2 pr-2 font-bold text-navy-900">${escapeHtml(s.demand || 0)}</td>
                </tr>
            `).join('');
        }
    }
}

async function runAutoDispatch() {
    const btn = document.getElementById('dispatch-btn');
    const result = document.getElementById('dispatch-result');
    if (!btn) return;

    const original = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Dispatch en cours...';

    try {
        const response = await apiFetch('/admin/dispatch', { method: 'POST' });
        if (result) {
            const assigned = response.assigned_count ?? 0;
            result.innerHTML = `<p class="text-emerald-700 font-semibold">${escapeHtml(assigned)} lead(s) assigné(s).</p>`;
        }
        if (typeof showToast === 'function') {
            showToast('Dispatch automatique terminé', 'success');
        }
        await loadAdminAll();
    } catch (err) {
        console.error('Dispatch error:', err);
        if (result) {
            result.innerHTML = '<p class="text-red-700 font-semibold">Échec du dispatch automatique.</p>';
        }
        if (typeof showToast === 'function') {
            showToast('Échec du dispatch automatique', 'error');
        }
    } finally {
        btn.disabled = false;
        btn.textContent = original;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (ADMIN_VIEW === 'settings') {
        loadAdminSettings();
    } else {
        loadAdminAll();
    }

    const dispatchBtn = document.getElementById('dispatch-btn');
    if (dispatchBtn) {
        dispatchBtn.addEventListener('click', runAutoDispatch);
    }

    const planForm = document.getElementById('plan-form');
    if (planForm) {
        planForm.addEventListener('submit', submitPlanForm);
    }

    const profileForm = document.getElementById('admin-profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', submitAdminProfile);
    }

    const passwordForm = document.getElementById('admin-password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', submitAdminPassword);
    }
});
</script>
JS;
$extraScript = str_replace('__PHP_TOKEN__', $safeToken, $extraScript);
$extraScript = str_replace('__ADMIN_VIEW__', addslashes($view), $extraScript);
?>

<?php include __DIR__ . '/../includes/dashboard_layout_bottom.php'; ?>
