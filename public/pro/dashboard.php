<?php
require_once __DIR__ . '/../includes/auth.php';
requireAuth(true);
$pageTitle = 'Tableau de bord';
$user = getCurrentUser();
$token = getToken();
?>
<?php include __DIR__ . '/../includes/dashboard_layout_top.php'; ?>

<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-display font-bold text-navy-950">Tableau de bord</h1>
        <p class="text-navy-500 font-medium mt-1">Bienvenue, <?= htmlspecialchars($user['first_name'] ?? explode('@', $user['email'])[0]) ?></p>
    </div>
    <a href="/rappel/public/pro/leads.php" class="btn btn-primary rounded-xl px-6 shadow-premium">
        <i data-lucide="plus" style="width:18px;height:18px;"></i>
        Voir mes leads
    </a>
</div>

<!-- Stats Cards -->
<div id="stats-grid" class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php for ($i = 0; $i < 4; $i++): ?>
    <div class="card p-6 animate-pulse">
        <div class="h-3 bg-navy-100 rounded w-2/3 mb-4"></div>
        <div class="h-8 bg-navy-100 rounded w-1/2 mb-2"></div>
        <div class="h-3 bg-navy-100 rounded w-3/4"></div>
    </div>
    <?php endfor; ?>
</div>

<!-- Recent Leads & Activity -->
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 card p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-display font-bold text-navy-950">Leads recents</h2>
            <a href="/rappel/public/pro/leads.php" class="text-sm font-bold text-accent-600 hover:text-accent-700 transition-colors">Voir tout -></a>
        </div>
        <div id="recent-leads">
            <div class="flex items-center justify-center py-12">
                <div class="spinner spinner-dark"></div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="card p-6">
            <h2 class="text-lg font-display font-bold text-navy-950 mb-5">Actions rapides</h2>
            <div class="space-y-3">
                <a href="/rappel/public/pro/leads.php" class="flex items-center gap-4 p-4 rounded-xl hover:bg-navy-50 transition-colors group">
                    <div class="w-10 h-10 bg-brand-100 rounded-xl flex items-center justify-center text-brand-700 group-hover:bg-brand-200 transition-colors">
                        <i data-lucide="users" style="width:20px;height:20px;"></i>
                    </div>
                    <div>
                        <p class="font-bold text-navy-900 text-sm">Mes Leads</p>
                        <p class="text-xs text-navy-400 font-medium">Gerer vos contacts</p>
                    </div>
                    <i data-lucide="chevron-right" class="ml-auto text-navy-300" style="width:16px;height:16px;"></i>
                </a>

                <a href="/rappel/public/pro/quotes.php" class="flex items-center gap-4 p-4 rounded-xl hover:bg-navy-50 transition-colors group">
                    <div class="w-10 h-10 bg-accent-100 rounded-xl flex items-center justify-center text-accent-700 group-hover:bg-accent-200 transition-colors">
                        <i data-lucide="file-text" style="width:20px;height:20px;"></i>
                    </div>
                    <div>
                        <p class="font-bold text-navy-900 text-sm">Devis</p>
                        <p class="text-xs text-navy-400 font-medium">Creer et suivre</p>
                    </div>
                    <i data-lucide="chevron-right" class="ml-auto text-navy-300" style="width:16px;height:16px;"></i>
                </a>

                <a href="/rappel/public/pro/performance.php" class="flex items-center gap-4 p-4 rounded-xl hover:bg-navy-50 transition-colors group">
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-700 group-hover:bg-indigo-200 transition-colors">
                        <i data-lucide="pie-chart" style="width:20px;height:20px;"></i>
                    </div>
                    <div>
                        <p class="font-bold text-navy-900 text-sm">Performances</p>
                        <p class="text-xs text-navy-400 font-medium">Vos statistiques</p>
                    </div>
                    <i data-lucide="chevron-right" class="ml-auto text-navy-300" style="width:16px;height:16px;"></i>
                </a>

                <a href="/rappel/public/pro/settings.php" class="flex items-center gap-4 p-4 rounded-xl hover:bg-navy-50 transition-colors group">
                    <div class="w-10 h-10 bg-navy-100 rounded-xl flex items-center justify-center text-navy-700 group-hover:bg-navy-200 transition-colors">
                        <i data-lucide="settings" style="width:20px;height:20px;"></i>
                    </div>
                    <div>
                        <p class="font-bold text-navy-900 text-sm">Parametres</p>
                        <p class="text-xs text-navy-400 font-medium">Votre profil</p>
                    </div>
                    <i data-lucide="chevron-right" class="ml-auto text-navy-300" style="width:16px;height:16px;"></i>
                </a>
            </div>
        </div>

        <?php if (!hasSubscription()): ?>
        <div class="card p-6 bg-gradient-to-br from-brand-900 to-navy-950 border-brand-800 text-white">
            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="zap" style="width:20px;height:20px;fill:currentColor;" class="text-accent-400"></i>
            </div>
            <h3 class="font-bold text-lg mb-2">Passez Pro</h3>
            <p class="text-navy-300 text-sm font-medium mb-4">Accedez a des leads illimites et boostez votre activite.</p>
            <a href="/rappel/public/pro/pricing.php" class="btn btn-accent btn-sm w-full rounded-xl text-center">Voir les plans</a>
        </div>
        <?php else: ?>
        <div class="card p-6 bg-gradient-to-br from-accent-50 to-white border-accent-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-accent-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="shield-check" class="text-accent-600" style="width:20px;height:20px;"></i>
                </div>
                <div>
                    <p class="font-bold text-navy-900 text-sm">Abonnement Actif</p>
                    <p class="text-xs text-accent-600 font-bold">Pro</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$safeToken = addslashes($token ?? '');
$extraScript = <<<'JS'
<script>
const PHP_TOKEN = '__PHP_TOKEN__';

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

async function loadDashboard() {
    try {
        if (PHP_TOKEN) Auth.setToken(PHP_TOKEN);
        const [stats, leads] = await Promise.all([
            apiFetch('/stats'),
            apiFetch('/leads'),
        ]);

        renderStats(stats || {});
        renderRecentLeads(Array.isArray(leads) ? leads.slice(0, 5) : []);
    } catch (err) {
        console.error('Dashboard load error:', err);
        renderStats({});
        renderRecentLeads([]);
    }
}

function renderStats(s) {
    const grid = document.getElementById('stats-grid');
    if (!grid) return;

    const totalLeads = Number(s.totalLeads ?? 0);
    const pendingLeads = Number(s.pendingLeads ?? 0);
    const processedLeads = Math.max(0, totalLeads - pendingLeads);
    const conversionRate = Number(s.conversionRate ?? 0);

    const cards = [
        { label: 'Leads totaux', value: totalLeads, icon: 'users', color: 'bg-brand-100 text-brand-700' },
        { label: 'En attente', value: pendingLeads, icon: 'clock', color: 'bg-amber-100 text-amber-700' },
        { label: 'Traites', value: processedLeads, icon: 'check-circle', color: 'bg-accent-100 text-accent-700' },
        { label: 'Taux conversion', value: `${conversionRate}%`, icon: 'trending-up', color: 'bg-indigo-100 text-indigo-700' },
    ];

    grid.innerHTML = cards.map((c) => `
        <div class="card p-6 hover:shadow-premium transition-all duration-300 animate-scale-in">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl ${c.color} flex items-center justify-center">
                    <i data-lucide="${c.icon}" style="width:20px;height:20px;"></i>
                </div>
            </div>
            <p class="text-3xl font-display font-bold text-navy-950 mb-1">${c.value}</p>
            <p class="text-sm font-bold text-navy-400">${c.label}</p>
        </div>
    `).join('');

    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function renderRecentLeads(leads) {
    const el = document.getElementById('recent-leads');
    if (!el) return;

    if (!leads.length) {
        el.innerHTML = `<div class="text-center py-12">
            <div class="w-16 h-16 bg-navy-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i data-lucide="inbox" class="text-navy-300" style="width:28px;height:28px;"></i>
            </div>
            <p class="font-bold text-navy-400">Aucun lead pour le moment.</p>
            <p class="text-sm text-navy-300 mt-1">Vos nouveaux leads apparaitront ici.</p>
        </div>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();
        return;
    }

    el.innerHTML = leads.map((lead) => {
        const name = lead.name || [lead.first_name, lead.last_name].filter(Boolean).join(' ') || 'Inconnu';
        const phone = lead.phone || '';
        const sector = lead.service_type || lead.sector || '';
        const status = String(lead.status || '').toLowerCase();
        const isPending = status === 'pending' || status === 'attente_client';

        return `
        <div class="flex items-center gap-4 p-4 rounded-xl hover:bg-navy-50 transition-colors cursor-pointer" onclick="window.location='/rappel/public/pro/leads.php'">
            <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center text-brand-700 font-bold text-sm flex-shrink-0">
                ${escapeHtml(name.charAt(0).toUpperCase() || '?')}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-navy-900 text-sm truncate">${escapeHtml(name)}</p>
                <p class="text-xs text-navy-400 font-medium truncate">${escapeHtml(phone)}${phone && sector ? ' · ' : ''}${escapeHtml(sector)}</p>
            </div>
            <span class="badge text-xs flex-shrink-0 ${isPending ? 'bg-amber-50 text-amber-700' : 'bg-accent-50 text-accent-700'}">
                ${isPending ? 'En attente' : 'Traite'}
            </span>
        </div>`;
    }).join('');

    if (typeof lucide !== 'undefined') lucide.createIcons();
}

document.addEventListener('DOMContentLoaded', loadDashboard);
</script>
JS;
$extraScript = str_replace('__PHP_TOKEN__', $safeToken, $extraScript);
?>

<?php include __DIR__ . '/../includes/dashboard_layout_bottom.php'; ?>

