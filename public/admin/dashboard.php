<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$isAdminTheme = true;
$pageTitle = 'Admin - Tableau de bord';
$token = getToken();
$view = $_GET['view'] ?? 'overview';
$allowedViews = ['overview', 'plans', 'providers', 'leads', 'dispatch', 'analytics'];
if (!in_array($view, $allowedViews, true)) {
    $view = 'overview';
}
$showOverview = $view === 'overview';
$showPlans = $showOverview || $view === 'plans';
$showProviders = $showOverview || $view === 'providers';
$showLeads = $showOverview || $view === 'leads';
$showDispatch = $showOverview || $view === 'dispatch';
$showAnalytics = $showOverview || $view === 'analytics';
?>
<?php include __DIR__ . '/../includes/dashboard_layout_top.php'; ?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <div class="flex items-center gap-3 mb-2">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                 style="background:linear-gradient(135deg,#4f46e5,#7c3aed);box-shadow:0 0 20px rgba(99,102,241,0.4);">
                <i data-lucide="shield-check" class="text-white" style="width:18px;height:18px;"></i>
            </div>
            <span class="text-xs font-bold uppercase tracking-widest" style="color:#818cf8;">Panneau d'administration</span>
        </div>
        <h1 class="text-3xl font-display font-bold text-navy-950">Pilotage Admin</h1>
        <p class="text-navy-500 font-medium mt-1">Prestataires, leads, dispatch automatique et analytics</p>
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
        ];
        foreach ($views as $viewKey => $viewMeta):
            $isCurrentView = ($view === $viewKey);
        ?>
        <a href="?view=<?= $viewKey ?>"
           class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition-all duration-200 <?= $isCurrentView
               ? 'text-white'
               : 'hover:opacity-80' ?>"
           style="<?= $isCurrentView
               ? 'background:rgba(99,102,241,0.25);border:1px solid rgba(99,102,241,0.4);color:#c7d2fe;'
               : 'background:rgba(15,23,42,0.65);border:1px solid rgba(100,116,139,0.45);color:#b8c6dd;' ?>">
            <i data-lucide="<?= $viewMeta['icon'] ?>" style="width:13px;height:13px;"></i>
            <?= $viewMeta['label'] ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>


<div id="admin-stats" class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-8">
    <?php for ($i = 0; $i < 6; $i++): ?>
    <div class="card p-5 animate-pulse">
        <div class="h-3 bg-navy-100 rounded w-2/3 mb-3"></div>
        <div class="h-7 bg-navy-100 rounded w-1/2"></div>
    </div>
    <?php endfor; ?>
</div>

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

    <div class="grid grid-cols-3 gap-4 mb-6" id="leads-psm-cards">
        <div class="rounded-xl border border-navy-100 p-4 text-center">
            <p class="text-xs font-bold text-navy-500 uppercase">Leads / jour</p>
            <p class="text-2xl font-display font-bold text-navy-950" id="metric-day">0</p>
        </div>
        <div class="rounded-xl border border-navy-100 p-4 text-center">
            <p class="text-xs font-bold text-navy-500 uppercase">Leads / semaine</p>
            <p class="text-2xl font-display font-bold text-navy-950" id="metric-week">0</p>
        </div>
        <div class="rounded-xl border border-navy-100 p-4 text-center">
            <p class="text-xs font-bold text-navy-500 uppercase">Leads / mois</p>
            <p class="text-2xl font-display font-bold text-navy-950" id="metric-month">0</p>
        </div>
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

<?php
$safeToken = addslashes($token ?? '');
$extraScript = <<<'JS'
<script>
const PHP_TOKEN = '__PHP_TOKEN__';
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

        renderStats(stats || {});
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

function renderStats(s) {
    const cards = [
        { label: 'Prestataires', value: s.total_providers ?? 0, icon: 'users' },
        { label: 'Leads total', value: s.total_leads ?? 0, icon: 'inbox' },
        { label: 'Abonnements actifs', value: s.active_subscriptions ?? 0, icon: 'badge-check' },
        { label: 'Revenu mois', value: (s.monthly_revenue ?? 0) + ' EUR', icon: 'trending-up' },
        { label: 'Leads jour', value: s.leads_today ?? 0, icon: 'calendar-days' },
        { label: 'Leads semaine', value: s.leads_week ?? 0, icon: 'calendar-range' }
    ];

    const html = cards.map((card) => `
        <div class="card p-5">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3" style="background:rgba(99,102,241,0.15);color:#818cf8;">
                <i data-lucide="${escapeHtml(card.icon)}" style="width:18px;height:18px;"></i>
            </div>
            <p class="text-2xl font-display font-bold text-navy-950">${escapeHtml(card.value)}</p>
            <p class="text-xs text-navy-500 font-bold uppercase tracking-wider mt-1">${escapeHtml(card.label)}</p>
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
    const day = document.getElementById('metric-day');
    const week = document.getElementById('metric-week');
    const month = document.getElementById('metric-month');
    if (day) day.textContent = analytics.leads_today ?? 0;
    if (week) week.textContent = analytics.leads_week ?? 0;
    if (month) month.textContent = analytics.leads_month ?? 0;

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
    loadAdminAll();
    const dispatchBtn = document.getElementById('dispatch-btn');
    if (dispatchBtn) {
        dispatchBtn.addEventListener('click', runAutoDispatch);
    }
    const planForm = document.getElementById('plan-form');
    if (planForm) {
        planForm.addEventListener('submit', submitPlanForm);
    }
});
</script>
JS;
$extraScript = str_replace('__PHP_TOKEN__', $safeToken, $extraScript);
?>

<?php include __DIR__ . '/../includes/dashboard_layout_bottom.php'; ?>
