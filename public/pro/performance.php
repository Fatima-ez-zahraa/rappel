<?php
require_once __DIR__ . '/../includes/auth.php';
requireAuth(true);
$pageTitle = 'Performances';
$token = getToken();
?>
<?php include __DIR__ . '/../includes/dashboard_layout_top.php'; ?>

<div class="mb-8">
    <h1 class="text-3xl font-display font-bold text-navy-950">Performances</h1>
    <p class="text-navy-500 font-medium mt-1">Analysez vos résultats et optimisez votre activité</p>
</div>

<!-- Stats Overview -->
<div id="perf-stats" class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php for ($i = 0; $i < 4; $i++): ?>
    <div class="card p-6 animate-pulse">
        <div class="h-3 bg-navy-100 rounded w-2/3 mb-4"></div>
        <div class="h-8 bg-navy-100 rounded w-1/2 mb-2"></div>
        <div class="h-3 bg-navy-100 rounded w-3/4"></div>
    </div>
    <?php endfor; ?>
</div>

<!-- Charts Row -->
<div class="grid lg:grid-cols-2 gap-6 mb-6">
    <!-- Leads by Status -->
    <div class="card p-6">
        <h2 class="text-lg font-display font-bold text-navy-950 mb-6">Répartition des leads</h2>
        <div id="status-chart" class="space-y-4"></div>
    </div>

    <!-- Monthly Activity -->
    <div class="card p-6">
        <h2 class="text-lg font-display font-bold text-navy-950 mb-6">Activité mensuelle</h2>
        <div id="monthly-chart" class="space-y-3"></div>
    </div>
</div>

<!-- Sector Breakdown -->
<div class="card p-6">
    <h2 class="text-lg font-display font-bold text-navy-950 mb-6">Performance par secteur</h2>
    <div id="sector-breakdown" class="grid md:grid-cols-3 gap-4"></div>
</div>

<?php
$safeToken = addslashes($token ?? '');
$extraScript = <<<'JS'
<script>
const PHP_TOKEN = 'RAPPEL_PHP_TOKEN_PLACEHOLDER';

async function loadPerformance() {
    if (PHP_TOKEN) Auth.setToken(PHP_TOKEN);
    try {
        const [stats, leads] = await Promise.all([
            apiFetch('/stats'),
            apiFetch('/leads')
        ]);
        renderPerfStats(stats);
        renderStatusChart(stats);
        renderMonthlyChart(leads);
        renderSectorBreakdown(leads);
    } catch (err) {
        console.error(err);
    }
}

function renderPerfStats(s) {
    const cards = [
        { label:'Total Leads', value: s.totalLeads ?? 0, icon:'users', color:'bg-brand-100 text-brand-700' },
        { label:'Taux de conversion', value: (s.conversionRate ?? 0) + '%', icon:'trending-up', color:'bg-accent-100 text-accent-700' },
        { label:'Leads traités', value: ((s.totalLeads ?? 0) - (s.pendingLeads ?? 0)), icon:'check-circle', color:'bg-emerald-100 text-emerald-700' },
        { label:'En attente', value: s.pendingLeads ?? 0, icon:'clock', color:'bg-amber-100 text-amber-700' },
    ];
    document.getElementById('perf-stats').innerHTML = cards.map(c => `
        <div class="card p-6 animate-scale-in">
            <div class="w-10 h-10 rounded-xl ${c.color} flex items-center justify-center mb-4">
                <i data-lucide="${c.icon}" style="width:20px;height:20px;"></i>
            </div>
            <p class="text-3xl font-display font-bold text-navy-950 mb-1">${c.value}</p>
            <p class="text-sm font-bold text-navy-400">${c.label}</p>
        </div>
    `).join('');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function renderStatusChart(s) {
    const total = (s.totalLeads ?? 0) || 1;
    const items = [
        { label:'Traités', value: ((s.totalLeads ?? 0) - (s.pendingLeads ?? 0)), color:'bg-accent-500' },
        { label:'En attente', value: s.pendingLeads ?? 0, color:'bg-amber-400' },
    ];
    document.getElementById('status-chart').innerHTML = items.map(i => `
        <div>
            <div class="flex justify-between text-sm font-bold text-navy-700 mb-2">
                <span>${i.label}</span><span>${i.value}</span>
            </div>
            <div class="h-3 bg-navy-100 rounded-full overflow-hidden">
                <div class="h-full ${i.color} rounded-full transition-all duration-1000" style="width:${Math.round(i.value/total*100)}%"></div>
            </div>
        </div>
    `).join('');
}

function renderMonthlyChart(leads) {
    const months = {};
    (leads || []).forEach(l => {
        const d = new Date(l.created_at || Date.now());
        const key = d.toLocaleDateString('fr-FR', { month:'short', year:'numeric' });
        months[key] = (months[key] || 0) + 1;
    });
    const entries = Object.entries(months).slice(-6);
    const max = Math.max(...entries.map(e => e[1]), 1);
    document.getElementById('monthly-chart').innerHTML = entries.length ? entries.map(([m, v]) => `
        <div class="flex items-center gap-3">
            <span class="text-xs font-bold text-navy-400 w-20 text-right">${m}</span>
            <div class="flex-1 h-6 bg-navy-50 rounded-lg overflow-hidden">
                <div class="h-full bg-brand-500 rounded-lg transition-all duration-700" style="width:${Math.round(v/max*100)}%"></div>
            </div>
            <span class="text-xs font-bold text-navy-700 w-6">${v}</span>
        </div>
    `).join('') : '<p class="text-navy-400 font-medium text-sm text-center py-8">Aucune donnée disponible</p>';
}

function renderSectorBreakdown(leads) {
    const sectors = {};
    (leads || []).forEach(l => {
        const s = l.sector || l.service_type || 'Autre';
        if (!sectors[s]) sectors[s] = { total: 0, processed: 0 };
        sectors[s].total++;
        if (l.status === 'processed') sectors[s].processed++;
    });
    const el = document.getElementById('sector-breakdown');
    const entries = Object.entries(sectors);
    if (!entries.length) {
        el.innerHTML = '<p class="text-navy-400 font-medium text-sm col-span-3 text-center py-8">Aucune donnée disponible</p>';
        return;
    }
    el.innerHTML = entries.map(([s, d]) => `
        <div class="p-5 bg-navy-50 rounded-2xl border border-navy-100">
            <p class="font-bold text-navy-900 capitalize mb-3">${s}</p>
            <div class="flex justify-between text-sm mb-2">
                <span class="text-navy-500 font-medium">Total</span>
                <span class="font-bold text-navy-900">${d.total}</span>
            </div>
            <div class="flex justify-between text-sm mb-3">
                <span class="text-navy-500 font-medium">Traités</span>
                <span class="font-bold text-accent-600">${d.processed}</span>
            </div>
            <div class="h-2 bg-navy-200 rounded-full overflow-hidden">
                <div class="h-full bg-accent-500 rounded-full" style="width:${d.total ? Math.round(d.processed/d.total*100) : 0}%"></div>
            </div>
        </div>
    `).join('');
}

document.addEventListener('DOMContentLoaded', loadPerformance);
</script>
JS;
$extraScript = str_replace('RAPPEL_PHP_TOKEN_PLACEHOLDER', $safeToken, $extraScript);
?>

<?php include __DIR__ . '/../includes/dashboard_layout_bottom.php'; ?>
